<?php
// algorithm_report_handler.php
// Handles algorithm-based projections and reporting
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../db/config.php';

// Encryption/Decryption functions (must match beef_transactions_handler.php)
function decrypt_data($data, $key) {
    // If data is not encrypted (numeric), return as is
    if (is_numeric($data)) {
        return $data;
    }
    
    try {
        $cipher = 'AES-128-CTR';
        $data = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivlen);
        $encrypted = substr($data, $ivlen);
        return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
    } catch (Exception $e) {
        // If decryption fails, return 0 to prevent breaking calculations
        return 0;
    }
}

// Secret key - must match the one in beef_transactions_handler.php
$secret_key = 'Nyama@546_2025';

// Get request parameters
$date = $_GET['date'] ?? date('Y-m-d');
$outlet = $_GET['outlet'] ?? 'all';
$category = strtolower($_GET['category'] ?? 'beef'); // Default to beef if not specified

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define table name based on category
    $validCategories = ['beef', 'goat', 'matumbo', 'chicken'];
    $tableName = in_array($category, $validCategories) ? "{$category}_transactions" : 'beef_transactions';
    
    // Get historical data for the last 30 days
    $query = "SELECT 
                transaction_date,
                buy_price,
                sell_price,
                total_cash_sales,
                daily_expense,
                outlet
              FROM {$tableName} 
              WHERE transaction_date >= DATE_SUB(:date, INTERVAL 30 DAY)
              AND transaction_date <= :date";
    
    $params = [':date' => $date];
    
    if ($outlet !== 'all') {
        $query .= " AND outlet = :outlet";
        $params[':outlet'] = $outlet;
    }
    
    $query .= " GROUP BY transaction_date ORDER BY transaction_date";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $historicalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate projections using weighted moving average
    $projections = [];
    $salesData = [];
    $expenseData = [];
    $profitData = [];
    $labels = [];
    
    // Category-specific adjustments (if needed)
    $categoryMultipliers = [
        'beef' => 1.0,
        'goat' => 0.8,    // Example: Goat might have different patterns
        'matumbo' => 0.7, // Example: Matumbo might have different patterns
        'chicken' => 0.9  // Example: Chicken might have different patterns
    ];
    
    $multiplier = $categoryMultipliers[$category] ?? 1.0;
    
    // Group data by date and prepare for projection
    $groupedData = [];
    
    foreach ($historicalData as $row) {
        $date = $row['transaction_date'];
        
        // Decrypt the encrypted fields
        $buyPrice = (float)decrypt_data($row['buy_price'], $secret_key);
        $sellPrice = (float)decrypt_data($row['sell_price'], $secret_key);
        $totalCashSales = (float)decrypt_data($row['total_cash_sales'], $secret_key);
        $dailyExpense = (float)decrypt_data($row['daily_expense'], $secret_key);
        
        // Calculate derived fields to match the database view
        $totalCash = $totalCashSales + $dailyExpense; // Total Cash = Total Cash Sales + Daily Expense
        $totalKilos = $sellPrice > 0 ? $totalCash / $sellPrice : 0; // Total Kilos = Total Cash / Sell Price
        $profitPerKg = $sellPrice - $buyPrice; // Profit per KG = Sell Price - Buy Price
        
        // Profit = (Sell Price - Buy Price) * Total Kilos - Daily Expense
        $profit = $sellPrice > 0 ? (($sellPrice - $buyPrice) * $totalKilos) - $dailyExpense : 0;
        
        if (!isset($groupedData[$date])) {
            $groupedData[$date] = [
                'transaction_date' => $date,
                'total_sales' => 0,
                'total_expenses' => 0,
                'total_profit' => 0,
                'total_kilos' => 0,
                'total_cash' => 0
            ];
        }
        
        // Sum up values for the same date
        $groupedData[$date]['total_sales'] += $totalCashSales;
        $groupedData[$date]['total_expenses'] += $dailyExpense;
        $groupedData[$date]['total_profit'] += $profit;
        $groupedData[$date]['total_kilos'] += $totalKilos;
        $groupedData[$date]['total_cash'] += $totalCash;
    }
    
    // Prepare data for projection
    foreach ($groupedData as $date => $data) {
        $labels[] = $date;
        $salesData[] = (float)$data['total_sales'];
        $expenseData[] = (float)$data['total_expenses'];
        $profitData[] = (float)$data['total_profit'];
    }
    
    // Simple weighted moving average for projection (7-day window)
    $projectionDays = 7; // Project next 7 days
    $windowSize = min(7, count($salesData));
    
    for ($i = 0; $i < $projectionDays; $i++) {
        $nextDate = date('Y-m-d', strtotime($date . ' + ' . ($i + 1) . ' days'));
        $projectedSales = 0;
        $projectedExpenses = 0;
        $projectedProfit = 0;
        $weightSum = 0;
        
        // Calculate weighted average (more recent days have higher weight)
        for ($j = 0; $j < $windowSize; $j++) {
            $weight = $windowSize - $j; // Linear weights
            $idx = count($salesData) - 1 - $j;
            
            if ($idx >= 0) {
                $projectedSales += $salesData[$idx] * $weight;
                $projectedExpenses += $expenseData[$idx] * $weight;
                $projectedProfit += $profitData[$idx] * $weight;
                $weightSum += $weight;
            }
        }
        
        if ($weightSum > 0) {
            $projectedSales = $projectedSales / $weightSum;
            $projectedExpenses = $projectedExpenses / $weightSum;
            $projectedProfit = $projectedProfit / $weightSum;
            
            // Add some randomness to projections (5% variation) and apply category multiplier
            $variation = 0.05 * $multiplier;
            $projectedSales *= (1 + (rand(-100, 100) / 100 * $variation));
            $projectedExpenses *= (1 + (rand(-100, 100) / 100 * $variation));
            
            // Calculate projected profit using the same formula as historical data
            $projectedTotalCash = $projectedSales + $projectedExpenses;
            $projectedSellPrice = $projectedSales > 0 ? $projectedTotalCash / $projectedSales : 0;
            $projectedBuyPrice = $projectedSellPrice * 0.8; // Assuming 20% margin for projection
            $projectedKilos = $projectedSellPrice > 0 ? $projectedTotalCash / $projectedSellPrice : 0;
            $projectedProfit = (($projectedSellPrice - $projectedBuyPrice) * $projectedKilos - $projectedExpenses) * $multiplier;
        }
        
        $projections[] = [
            'date' => $nextDate,
            'projected_sales' => round($projectedSales, 2),
            'projected_expenses' => round($projectedExpenses, 2),
            'projected_profit' => round($projectedProfit, 2),
            'is_projection' => true
        ];
        
        // Add to historical data for next projection
        $salesData[] = $projectedSales;
        $expenseData[] = $projectedExpenses;
        $profitData[] = $projectedProfit;
        $labels[] = $nextDate;
    }
    
    // Format response
    $response = [
        'status' => 'success',
        'data' => [
            'historical' => $historicalData,
            'projections' => $projections,
            'chart_data' => [
                'labels' => $labels,
                'sales' => array_map('floatval', $salesData),
                'expenses' => array_map('floatval', $expenseData),
                'profit' => array_map('floatval', $profitData)
            ],
            'summary' => [
                'total_sales' => array_sum($salesData),
                'total_expenses' => array_sum($expenseData),
                'total_profit' => array_sum($profitData),
                'avg_daily_profit' => count($profitData) > 0 ? array_sum($profitData) / count($profitData) : 0,
                'projected_profit_next_7_days' => array_sum(array_column($projections, 'projected_profit'))
            ]
        ]
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
