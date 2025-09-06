<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Database configuration
require_once __DIR__ . '/../db/config.php';

// Secret key - should match the one used in other handlers
$secret_key = 'Nyama@546_2025';

// Function to decrypt data
function decrypt_data($data, $key) {
    $cipher = 'AES-128-CTR';
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $encrypted = substr($data, $ivlen);
    return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
}

// Function to decrypt transaction data
function decryptTransactionData($transaction, $secret_key) {
    $decrypted = [];
    foreach ($transaction as $key => $value) {
        if (in_array($key, ['buy_price', 'sell_price', 'total_cash_sales', 'daily_expense'])) {
            $decrypted[$key] = is_numeric($value) ? floatval($value) : floatval(decrypt_data($value, $secret_key));
        } else {
            $decrypted[$key] = $value;
        }
    }
    return $decrypted;
}

// Get date range from query parameters
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // Default to start of current month
$endDate = $_GET['end_date'] ?? date('Y-m-t');     // Default to end of current month

try {
    // Fetch beef transactions
    $beefStmt = $pdo->prepare("
        SELECT 'beef' as type, id, transaction_date, buy_price, sell_price, 
               total_cash_sales, daily_expense, created_at, updated_at
        FROM beef_transactions 
        WHERE transaction_date BETWEEN ? AND ?
        ORDER BY transaction_date
    ");
    $beefStmt->execute([$startDate, $endDate]);
    $beefTransactions = array_map(function($tx) use ($secret_key) {
        return decryptTransactionData($tx, $secret_key);
    }, $beefStmt->fetchAll(PDO::FETCH_ASSOC));

    // Fetch goat transactions
    $goatStmt = $pdo->prepare("
        SELECT 'goat' as type, id, transaction_date, buy_price, sell_price, 
               total_cash_sales, daily_expense, created_at, updated_at
        FROM goat_transactions 
        WHERE transaction_date BETWEEN ? AND ?
        ORDER BY transaction_date
    ");
    $goatStmt->execute([$startDate, $endDate]);
    $goatTransactions = array_map(function($tx) use ($secret_key) {
        return decryptTransactionData($tx, $secret_key);
    }, $goatStmt->fetchAll(PDO::FETCH_ASSOC));

    // Initialize summary
    $summary = [
        'total_profit' => 0,
        'beef_profit' => 0,
        'goat_profit' => 0,
        'total_kilos' => 0,
        'total_sales' => 0,
        'total_expenses' => 0,
        'total_transactions' => count($beefTransactions) + count($goatTransactions)
    ];

    // Calculate beef totals
    $beefSales = array_sum(array_column($beefTransactions, 'total_cash_sales'));
    $beefExpenses = array_sum(array_column($beefTransactions, 'daily_expense'));
    $beefTotalCash = $beefSales + $beefExpenses;
    $beefSellPrice = array_sum(array_column($beefTransactions, 'sell_price'));
    $beefBuyPrice = array_sum(array_column($beefTransactions, 'buy_price'));
    
    // Calculate goat totals
    $goatSales = array_sum(array_column($goatTransactions, 'total_cash_sales'));
    $goatExpenses = array_sum(array_column($goatTransactions, 'daily_expense'));
    $goatTotalCash = $goatSales + $goatExpenses;
    $goatSellPrice = array_sum(array_column($goatTransactions, 'sell_price'));
    $goatBuyPrice = array_sum(array_column($goatTransactions, 'buy_price'));
    
    // Calculate kilos and profits
    $beefKilos = $beefSellPrice > 0 ? $beefTotalCash / $beefSellPrice : 0;
    $goatKilos = $goatSellPrice > 0 ? $goatTotalCash / $goatSellPrice : 0;
    
    $beefProfitPerKg = $beefSellPrice - $beefBuyPrice;
    $goatProfitPerKg = $goatSellPrice - $goatBuyPrice;
    
    $beefProfit = ($beefProfitPerKg * $beefKilos) - $beefExpenses;
    $goatProfit = ($goatProfitPerKg * $goatKilos) - $goatExpenses;
    
    // Update summary
    $summary['beef_profit'] = $beefProfit;
    $summary['goat_profit'] = $goatProfit;
    $summary['total_sales'] = $beefSales + $goatSales;
    $summary['total_expenses'] = $beefExpenses + $goatExpenses;
    $summary['total_kilos'] = $beefKilos + $goatKilos;
    $summary['total_profit'] = $beefProfit + $goatProfit;
    $summary['avg_profit_per_kg'] = $summary['total_kilos'] > 0 
        ? $summary['total_profit'] / $summary['total_kilos'] 
        : 0;

    // Round all values
    $summary = array_map(function($value) {
        return is_float($value) ? round($value, 2) : $value;
    }, $summary);

    // Combine and return results
    $response = [
        'success' => true,
        'data' => [
            'beef' => $beefTransactions,
            'goat' => $goatTransactions,
            'summary' => $summary
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
