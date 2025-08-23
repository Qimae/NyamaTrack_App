<?php
header('Content-Type: application/json');
require_once '../db/config.php';

// Function to get all transactions
function getTransactions($pdo) {
    try {
        $sql = "SELECT * FROM mpesa_transactions ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => 'Error fetching transactions: ' . $e->getMessage()];
    }
}

// Function to get transaction statistics
function getTransactionStats($pdo) {
    try {
        $stats = [
            'total_revenue' => 0,
            'paid_count' => 0,
            'total_count' => 0
        ];

        // Get all transactions
        $transactions = getTransactions($pdo);
        if (isset($transactions['error'])) {
            return $transactions; // Return error if any
        }

        $stats['total_count'] = count($transactions);
        
        // Calculate stats
        foreach ($transactions as $transaction) {
            if ($transaction['ResultCode'] == 0) {
                $stats['total_revenue'] += $transaction['Amount'];
                $stats['paid_count']++;
            }
        }

        // Calculate success rate
        $stats['success_rate'] = $stats['total_count'] > 0 
            ? round(($stats['paid_count'] / $stats['total_count']) * 100) 
            : 0;

        return $stats;
    } catch (Exception $e) {
        return ['error' => 'Error calculating stats: ' . $e->getMessage()];
    }
}

// Main request handler
$response = [];
try {
    // Check if it's an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'get_transactions':
                $response['transactions'] = getTransactions($pdo);
                break;
                
            case 'get_stats':
                $response = array_merge($response, getTransactionStats($pdo));
                break;
                
            default:
                $response = array_merge(
                    getTransactionStats($pdo),
                    ['transactions' => getTransactions($pdo)]
                );
        }
        
        $response['success'] = !isset($response['error']);
    } else {
        $response = [
            'success' => false,
            'error' => 'Invalid request method'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ];
}

echo json_encode($response);
