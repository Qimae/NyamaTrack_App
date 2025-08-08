<?php
// beef_transactions_handler.php
// Handles CRUD operations for beef transactions with encryption
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../db/config.php';

// Encryption/Decryption functions
function encrypt_data($data, $key) {
    $cipher = 'AES-128-CTR';
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_data($data, $key) {
    $cipher = 'AES-128-CTR';
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $encrypted = substr($data, $ivlen);
    return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
}

// Secret key - should be stored securely in production
$secret_key = 'Nyama@546_2025'; // Same key as registration handler

// Function to calculate derived fields
function calculateDerivedFields($transaction, $secret_key) {
    // Decrypt the values if they are encrypted
    $buy_price = is_numeric($transaction['buy_price']) ? $transaction['buy_price'] : floatval(decrypt_data($transaction['buy_price'], $secret_key));
    $sell_price = is_numeric($transaction['sell_price']) ? $transaction['sell_price'] : floatval(decrypt_data($transaction['sell_price'], $secret_key));
    $total_cash_sales = is_numeric($transaction['total_cash_sales']) ? $transaction['total_cash_sales'] : floatval(decrypt_data($transaction['total_cash_sales'], $secret_key));
    $daily_expense = is_numeric($transaction['daily_expense']) ? $transaction['daily_expense'] : floatval(decrypt_data($transaction['daily_expense'], $secret_key));
    
    // Calculate derived fields
    $total_cash = $total_cash_sales + $daily_expense;
    $total_kilos = $sell_price > 0 ? $total_cash / $sell_price : 0;
    $profit_per_kg = $sell_price - $buy_price;
    $profit = ($profit_per_kg * $total_kilos) - $daily_expense;
    
    return [
        'total_cash' => round($total_cash, 2),
        'total_kilos' => round($total_kilos, 2),
        'profit_per_kg' => round($profit_per_kg, 2),
        'profit' => round($profit, 2)
    ];
}

// Function to decrypt transaction data for display
function decryptTransactionData($transaction, $secret_key) {
    $decrypted = [];
    foreach ($transaction as $key => $value) {
        if (in_array($key, ['buy_price', 'sell_price', 'total_cash_sales', 'daily_expense'])) {
            $decrypted[$key] = is_numeric($value) ? $value : floatval(decrypt_data($value, $secret_key));
        } else {
            $decrypted[$key] = $value;
        }
    }
    return $decrypted;
}

// Function to validate transaction data
function validateTransactionData($input) {
    $errors = [];
    $required_fields = ['user_id', 'transaction_date', 'buy_price', 'sell_price', 'total_cash_sales', 'daily_expense'];
    
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            $errors[] = "Missing required field: $field";
        }
    }
    
    // Validate numeric fields
    $numeric_fields = ['buy_price', 'sell_price', 'total_cash_sales', 'daily_expense'];
    foreach ($numeric_fields as $field) {
        if (isset($input[$field]) && !is_numeric($input[$field])) {
            $errors[] = "$field must be a number";
        }
    }
    
    // Validate date format
    if (isset($input['transaction_date']) && !strtotime($input['transaction_date'])) {
        $errors[] = 'Invalid date format. Use YYYY-MM-DD';
    }
    
    return $errors;
}

// Main request handler
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Handle GET request - Fetch transactions
        try {
            global $secret_key, $pdo;
            
            // Start session and get business name
            session_start();
            if (!isset($_SESSION['business_name'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Not authenticated']);
                exit;
            }
            $business_name = $_SESSION['business_name'];
            
            // Get query parameters
            $transaction_id = $_GET['id'] ?? null;
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            
            if ($transaction_id) {
                // Get single transaction with calculated fields
                $stmt = $pdo->prepare("
                    SELECT * FROM beef_transactions 
                    WHERE id = ? AND business_name = ?
                ");
                $stmt->execute([$transaction_id, $business_name]);
                $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($transaction) {
                    // Decrypt the transaction data
                    $decrypted = decryptTransactionData($transaction, $secret_key);
                    
                    // Calculate derived fields
                    $derived = calculateDerivedFields($transaction, $secret_key);
                    
                    // Merge decrypted data with derived fields
                    $response = array_merge($decrypted, $derived);
                    $response['id'] = $transaction['id'];
                    $response['business_name'] = $transaction['business_name'];
                    $response['transaction_date'] = $transaction['transaction_date'];
                    
                    echo json_encode(['success' => true, 'data' => $response]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Transaction not found']);
                }
            } else {
                // Get all transactions for the business with optional date filtering
                $query = "
                    SELECT *
                    FROM beef_transactions 
                    WHERE business_name = :business_name
                ";
                $params = [':business_name' => $business_name];
                
                // Add date range filter if provided
                if ($start_date && $end_date) {
                    $query .= " AND transaction_date BETWEEN :start_date AND :end_date";
                    $params[':start_date'] = $start_date;
                    $params[':end_date'] = $end_date;
                }
                
                $query .= " ORDER BY transaction_date DESC";
                
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Initialize summary
                $summary = [
                    'total_profit' => 0,
                    'total_kilos' => 0,
                    'total_transactions' => count($transactions)
                ];
                
                // Process each transaction
                $result = [];
                foreach ($transactions as $transaction) {
                    // Get decrypted values
                    $buy_price = is_numeric($transaction['buy_price']) ? 
                        floatval($transaction['buy_price']) : 
                        floatval(decrypt_data($transaction['buy_price'], $secret_key));
                        
                    $sell_price = is_numeric($transaction['sell_price']) ? 
                        floatval($transaction['sell_price']) : 
                        floatval(decrypt_data($transaction['sell_price'], $secret_key));
                        
                    $total_cash_sales = is_numeric($transaction['total_cash_sales']) ? 
                        floatval($transaction['total_cash_sales']) : 
                        floatval(decrypt_data($transaction['total_cash_sales'], $secret_key));
                        
                    $daily_expense = is_numeric($transaction['daily_expense']) ? 
                        floatval($transaction['daily_expense']) : 
                        floatval(decrypt_data($transaction['daily_expense'], $secret_key));
                    
                    // Calculate derived fields
                    $total_cash = $total_cash_sales + $daily_expense;
                    $total_kilos = $sell_price > 0 ? $total_cash / $sell_price : 0;
                    $profit_per_kg = $sell_price - $buy_price;
                    $profit = ($profit_per_kg * $total_kilos) - $daily_expense;
                    
                    // Prepare derived fields
                    $derived = [
                        'total_cash' => round($total_cash, 2),
                        'total_kilos' => round($total_kilos, 2),
                        'profit_per_kg' => round($profit_per_kg, 2),
                        'profit' => round($profit, 2)
                    ];
                    
                    // Add to summary
                    $summary['total_profit'] += $derived['profit'];
                    $summary['total_kilos'] += $derived['total_kilos'];
                    
                    // Prepare the response item
                    $result[] = [
                        'id' => $transaction['id'],
                        'business_name' => $transaction['business_name'],
                        'transaction_date' => $transaction['transaction_date'],
                        'buy_price' => $buy_price,
                        'sell_price' => $sell_price,
                        'total_cash_sales' => $total_cash_sales,
                        'daily_expense' => $daily_expense,
                        'total_cash' => $derived['total_cash'],
                        'total_kilos' => $derived['total_kilos'],
                        'profit_per_kg' => $derived['profit_per_kg'],
                        'profit' => $derived['profit']
                    ];
                }
                
                // Calculate average profit per kg
                $summary['avg_profit_per_kg'] = $summary['total_kilos'] > 0 ? 
                    $summary['total_profit'] / $summary['total_kilos'] : 0;
                
                echo json_encode([
                    'success' => true, 
                    'data' => $result,
                    'summary' => $summary
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    case 'POST':
        // Handle POST request - Create new transaction
        try {
            global $secret_key, $pdo;
            
            // Start session and get business name
            session_start();
            if (!isset($_SESSION['business_name'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Not authenticated']);
                exit;
            }
            $business_name = $_SESSION['business_name'];
            
            // Get and validate input
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Add business_name to input for validation
            $input['business_name'] = $business_name;
            
            // Validate required fields
            $errors = validateTransactionData($input);
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['error' => 'Validation failed', 'details' => $errors]);
                exit;
            }
            
            // Encrypt sensitive data
            $encrypted_buy_price = encrypt_data($input['buy_price'], $secret_key);
            $encrypted_sell_price = encrypt_data($input['sell_price'], $secret_key);
            $encrypted_total_cash_sales = encrypt_data($input['total_cash_sales'], $secret_key);
            $encrypted_daily_expense = encrypt_data($input['daily_expense'], $secret_key);
            
            // Check for duplicate date for this business
            $checkStmt = $pdo->prepare("
                SELECT id FROM beef_transactions 
                WHERE business_name = ? AND transaction_date = ?
            ");
            $checkStmt->execute([$business_name, $input['transaction_date']]);
            
            if ($checkStmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'A transaction already exists for this date']);
                exit;
            }
            
            // Insert new transaction
            $stmt = $pdo->prepare("
                INSERT INTO beef_transactions 
                (business_name, transaction_date, buy_price, sell_price, total_cash_sales, daily_expense)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([
                $business_name,
                $input['transaction_date'],
                $encrypted_buy_price,
                $encrypted_sell_price,
                $encrypted_total_cash_sales,
                $encrypted_daily_expense
            ]);
            
            if ($success) {
                // Get the inserted transaction
                $transaction_id = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT * FROM beef_transactions WHERE id = ?");
                $stmt->execute([$transaction_id]);
                $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($transaction) {
                    // Decrypt the transaction data
                    $decrypted = decryptTransactionData($transaction, $secret_key);
                    
                    // Calculate derived fields
                    $derived = calculateDerivedFields($transaction, $secret_key);
                    
                    // Merge decrypted data with derived fields
                    $response = array_merge($decrypted, $derived);
                    $response['id'] = $transaction['id'];
                    $response['business_name'] = $transaction['business_name'];
                    $response['transaction_date'] = $transaction['transaction_date'];
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Transaction created successfully', 
                        'data' => $response
                    ]);
                } else {
                    throw new Exception('Failed to retrieve created transaction');
                }
            } else {
                throw new Exception('Failed to create transaction');
            }
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    case 'PUT':
        // Handle PUT request - Update existing transaction
        try {
            global $secret_key, $pdo;
            
            // Get and validate input
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $validation_errors = validateTransactionData($input);
            if (!empty($validation_errors)) {
                http_response_code(400);
                echo json_encode(['error' => 'Validation failed', 'details' => $validation_errors]);
                exit;
            }
            
            // Get business name from session
            session_start();
            if (!isset($_SESSION['business_name'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Not authenticated']);
                exit;
            }
            $business_name = $_SESSION['business_name'];
            
            // Check if transaction exists for this business
            $checkStmt = $pdo->prepare("
                SELECT id FROM beef_transactions 
                WHERE id = ? AND business_name = ?
            ");
            $checkStmt->execute([$input['id'], $business_name]);
            if (!$checkStmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Transaction not found']);
                exit;
            }
            
            // Check if another transaction already exists for the new date (for the same business)
            $checkDateStmt = $pdo->prepare("
                SELECT id FROM beef_transactions 
                WHERE business_name = ? AND transaction_date = ? AND id != ?
            ");
            $checkDateStmt->execute([
                $business_name, 
                $input['transaction_date'],
                $input['id']
            ]);
            if ($checkDateStmt->fetch()) {
                http_response_code(409);
                echo json_encode(['error' => 'Another transaction already exists for this date']);
                exit;
            }
            
            // Encrypt sensitive data before storage
            $encrypted_buy_price = encrypt_data($input['buy_price'], $secret_key);
            $encrypted_sell_price = encrypt_data($input['sell_price'], $secret_key);
            $encrypted_total_cash_sales = encrypt_data($input['total_cash_sales'], $secret_key);
            $encrypted_daily_expense = encrypt_data($input['daily_expense'], $secret_key);
            
            // Update transaction with encrypted data
            $stmt = $pdo->prepare("
                UPDATE beef_transactions 
                SET transaction_date = ?, 
                    buy_price = ?, 
                    sell_price = ?, 
                    total_cash_sales = ?, 
                    daily_expense = ?,
                    updated_at = NOW()
                WHERE id = ? AND business_name = ?
            ");
            
            $success = $stmt->execute([
                $input['transaction_date'],
                $encrypted_buy_price,
                $encrypted_sell_price,
                $encrypted_total_cash_sales,
                $encrypted_daily_expense,
                $input['id'],
                $business_name  // Using business_name from session instead of user_id
            ]);
            
            if ($success) {
                // Get the updated transaction
                $stmt = $pdo->prepare("SELECT * FROM beef_transactions WHERE id = ?");
                $stmt->execute([$input['id']]);
                $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($transaction) {
                    // Decrypt the transaction data for the response
                    $decrypted = decryptTransactionData($transaction, $secret_key);
                    
                    // Calculate derived fields
                    $derived = calculateDerivedFields($transaction, $secret_key);
                    
                    // Merge decrypted data with derived fields
                    $response = array_merge($decrypted, $derived);
                    $response['id'] = $transaction['id'];
                    $response['business_name'] = $transaction['business_name'];
                    $response['transaction_date'] = $transaction['transaction_date'];
                    
                    // Remove user_id from response since we're using business_name
                    unset($response['user_id']);
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Transaction updated successfully', 
                        'data' => $response
                    ]);
                } else {
                    throw new Exception('Failed to retrieve updated transaction');
                }
            } else {
                throw new Exception('Failed to update transaction');
            }
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    case 'DELETE':
        // Handle DELETE request - Delete transaction
        try {
            global $pdo;
            
            // Start session and get business name
            session_start();
            if (!isset($_SESSION['business_name'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Not authenticated']);
                exit;
            }
            $business_name = $_SESSION['business_name'];
            
            // Get transaction ID from URL or request body
            $transaction_id = $_GET['id'] ?? null;
            if (!$transaction_id) {
                $input = json_decode(file_get_contents('php://input'), true);
                $transaction_id = $input['id'] ?? null;
            }
            
            if (!$transaction_id) {
                http_response_code(400);
                echo json_encode(['error' => 'Transaction ID is required']);
                exit;
            }
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // First, verify the transaction exists and belongs to the business
                $checkStmt = $pdo->prepare("
                    SELECT id FROM beef_transactions 
                    WHERE id = ? AND business_name = ?
                    FOR UPDATE
                ");
                $checkStmt->execute([$transaction_id, $business_name]);
                
                if (!$checkStmt->fetch()) {
                    $pdo->rollBack();
                    http_response_code(404);
                    echo json_encode(['error' => 'Transaction not found']);
                    exit;
                }
                
                // Delete the transaction
                $deleteStmt = $pdo->prepare("DELETE FROM beef_transactions WHERE id = ?");
                $deleteStmt->execute([$transaction_id]);
                
                // Commit transaction
                $pdo->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Transaction deleted successfully',
                    'deleted_id' => $transaction_id
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
