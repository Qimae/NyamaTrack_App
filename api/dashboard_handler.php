<?php
// dashboard_handler.php
// Handles dashboard data retrieval

// Ensure no output before headers
if (ob_get_level()) ob_clean();

// Set headers for JSON response
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/beef_transactions_handler.php'; // For encryption functions

// Function to get dashboard summary data
function getDashboardSummary($business_name) {
    global $pdo, $secret_key;
    
    try {
        // Get current date range (last 30 days)
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime('-30 days'));
        
        // Query to get beef transactions for the period
        $stmt = $pdo->prepare("
            SELECT *, 'beef' as transaction_type FROM beef_transactions 
            WHERE business_name = :business_name 
            AND transaction_date BETWEEN :start_date AND :end_date
            
            UNION ALL
            
            SELECT *, 'goat' as transaction_type FROM goat_transactions 
            WHERE business_name = :business_name 
            AND transaction_date BETWEEN :start_date AND :end_date
            
            ORDER BY transaction_date DESC
        ");
        
        $stmt->execute([
            ':business_name' => $business_name,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
        
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Initialize summary data
        $summary = [
            'total_sales' => 0,
            'total_expenses' => 0,
            'total_profit' => 0,
            'total_kilos' => 0,
            'transactions_count' => count($transactions),
            'daily_averages' => [
                'sales' => 0,
                'expenses' => 0,
                'profit' => 0,
                'kilos' => 0
            ],
            'chart_data' => [
                'labels' => [],
                'sales' => [],
                'expenses' => [],
                'profit' => []
            ]
        ];
        
        // Process transactions
        $days_data = [];
        
        foreach ($transactions as $tx) {
            // Decrypt transaction data
            $buy_price = is_numeric($tx['buy_price']) ? 
                floatval($tx['buy_price']) : 
                floatval(decrypt_data($tx['buy_price'], $secret_key));
                
            $sell_price = is_numeric($tx['sell_price']) ? 
                floatval($tx['sell_price']) : 
                floatval(decrypt_data($tx['sell_price'], $secret_key));
                
            $total_cash_sales = is_numeric($tx['total_cash_sales']) ? 
                floatval($tx['total_cash_sales']) : 
                floatval(decrypt_data($tx['total_cash_sales'], $secret_key));
                
            $daily_expense = is_numeric($tx['daily_expense']) ? 
                floatval($tx['daily_expense']) : 
                floatval(decrypt_data($tx['daily_expense'], $secret_key));
            
            // Calculate derived fields
            $total_cash = $total_cash_sales + $daily_expense;
            $total_kilos = $sell_price > 0 ? $total_cash / $sell_price : 0;
            $profit_per_kg = $sell_price - $buy_price;
            $profit = ($profit_per_kg * $total_kilos) - $daily_expense;
            
            // Add to summary
            $summary['total_sales'] += $total_cash_sales;
            $summary['total_expenses'] += $daily_expense;
            $summary['total_profit'] += $profit;
            $summary['total_kilos'] += $total_kilos;
            
            // Group by date for chart data
            $date = $tx['transaction_date'];
            if (!isset($days_data[$date])) {
                $days_data[$date] = [
                    'sales' => 0,
                    'expenses' => 0,
                    'profit' => 0,
                    'kilos' => 0
                ];
            }
            
            $days_data[$date]['sales'] += $total_cash_sales;
            $days_data[$date]['expenses'] += $daily_expense;
            $days_data[$date]['profit'] += $profit;
            $days_data[$date]['kilos'] += $total_kilos;
        }
        
        // Calculate daily averages
        $days_with_data = count($days_data);
        if ($days_with_data > 0) {
            $summary['daily_averages']['sales'] = $summary['total_sales'] / $days_with_data;
            $summary['daily_averages']['expenses'] = $summary['total_expenses'] / $days_with_data;
            $summary['daily_averages']['profit'] = $summary['total_profit'] / $days_with_data;
            $summary['daily_averages']['kilos'] = $summary['total_kilos'] / $days_with_data;
        }
        
        // Prepare chart data (last 7 days)
        $chart_days = 7;
        $chart_labels = [];
        $chart_sales = [];
        $chart_expenses = [];
        $chart_profit = [];
        
        for ($i = $chart_days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chart_labels[] = date('M j', strtotime($date));
            
            if (isset($days_data[$date])) {
                $chart_sales[] = round($days_data[$date]['sales'], 2);
                $chart_expenses[] = round($days_data[$date]['expenses'], 2);
                $chart_profit[] = round($days_data[$date]['profit'], 2);
            } else {
                $chart_sales[] = 0;
                $chart_expenses[] = 0;
                $chart_profit[] = 0;
            }
        }
        
        $summary['chart_data'] = [
            'labels' => $chart_labels,
            'sales' => $chart_sales,
            'expenses' => $chart_expenses,
            'profit' => $chart_profit
        ];
        
        // Round values
        $summary['total_sales'] = round($summary['total_sales'], 2);
        $summary['total_expenses'] = round($summary['total_expenses'], 2);
        $summary['total_profit'] = round($summary['total_profit'], 2);
        $summary['total_kilos'] = round($summary['total_kilos'], 2);
        
        return $summary;
        
    } catch (PDOException $e) {
        error_log("Database error in getDashboardSummary: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error in getDashboardSummary: " . $e->getMessage());
        return [];
    }
}

// Function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    if ($json === false) {
        // JSON encoding failed
        $error = json_last_error_msg();
        $response = [
            'success' => false,
            'error' => 'JSON encoding error: ' . $error
        ];
        http_response_code(500);
        $json = json_encode($response);
    }
    
    // Ensure no output before this
    if (ob_get_level()) ob_clean();
    
    // Output the JSON
    echo $json;
    exit;
}

// Main request handler
try {
    // Start session and get business name
    session_start();
    
    if (!isset($_SESSION['business_name'])) {
        sendJsonResponse([
            'success' => false,
            'error' => 'Not authenticated'
        ], 401);
    }
    
    $business_name = $_SESSION['business_name'];
    
    // Get dashboard data
    $dashboard_data = getDashboardSummary($business_name);
    
    if (!empty($dashboard_data)) {
        sendJsonResponse([
            'success' => true,
            'data' => $dashboard_data
        ]);
    } else {
        sendJsonResponse([
            'success' => false,
            'error' => 'No data available'
        ], 404);
    }
    
} catch (Exception $e) {
    error_log('Dashboard handler error: ' . $e->getMessage());
    sendJsonResponse([
        'success' => false,
        'error' => 'An error occurred while processing your request.'
    ], 500);
}
