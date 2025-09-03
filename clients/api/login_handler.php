<?php
// Set error reporting and display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Set JSON content type
header('Content-Type: application/json');

// Function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Function to log errors
function logError($message, $data = []) {
    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if (!empty($data)) {
        $logMessage .= ' ' . json_encode($data, JSON_PRETTY_PRINT);
    }
    error_log($logMessage);
}

try {
    // Load database configuration
    $configFile = __DIR__ . '/../db/config.php';
    if (!file_exists($configFile)) {
        throw new Exception('Database configuration file not found');
    }
    require_once $configFile;
    
    // Constants for login attempts
    define('MAX_LOGIN_ATTEMPTS', 5);
    define('LOCKOUT_MINUTES', 30); // 30 minutes lockout after max attempts

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $business_name = trim($_POST['business_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Encryption function (same as registration)
    function encrypt_data($data, $key) {
        $cipher = 'AES-128-CTR';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    // Decryption function
    function decrypt_data($data, $key) {
        $cipher = 'AES-128-CTR';
        $data = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivlen);
        $encrypted = substr($data, $ivlen);
        return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
    }

    $secret_key = 'Nyama@546_2025'; // Must match registration key

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format.']);
        exit;
    }

    // Hash business_name and email for lookup
    $hash_email = hash('sha256', strtolower($email));

    try {
        // Get user with all necessary fields including created_at
        $stmt = $pdo->prepare("SELECT id, fullname, password, failed_attempts, last_failed_attempt, created_at FROM client_users WHERE email_hash = ? LIMIT 1");
        $stmt->execute([$hash_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if account is locked
        if ($user && $user['failed_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Account locked due to too many failed attempts. Please use the "Forgot Password" option to reset your password.',
                'forgot_password' => true
            ]);
            exit;
        }

        if ($user && password_verify($password, $user['password'])) {
            // Reset failed attempts on successful login
            $resetStmt = $pdo->prepare("UPDATE client_users SET failed_attempts = 0, last_failed_attempt = NULL WHERE id = ?");
            $resetStmt->execute([$user['id']]);
            
            // Decrypt fullname and business_name for session and response
            $dec_fullname = decrypt_data($user['fullname'], $secret_key);
            $user_id = (int)$user['id'];
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            
            // Prepare response
            $response = [
                'success' => true, 
                'message' => 'Login successful.', 
                'fullname' => $dec_fullname, 
                'user_id' => $user_id,
            ];
            
            echo json_encode($response);
        } else {
            $attempts_remaining = 0;
            
            if ($user) {
                // Increment failed attempts
                $new_attempts = $user['failed_attempts'] + 1;
                $attempts_remaining = max(0, MAX_LOGIN_ATTEMPTS - $new_attempts);
                
                $updateStmt = $pdo->prepare("UPDATE client_users SET failed_attempts = ?, last_failed_attempt = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$new_attempts, $user['id']]);
                
                // If this was the 3rd failed attempt, block the account
                if ($new_attempts >= MAX_LOGIN_ATTEMPTS) {
                    http_response_code(403);
                    echo json_encode([
                        'error' => 'Too many failed login attempts. Please use the "Forgot Password" option to reset your password.',
                        'forgot_password' => true,
                        'time_remaining' => LOCKOUT_MINUTES
                    ]);
                    exit;
                }
            }
            
            http_response_code(401);
            echo json_encode([
                'error' => 'Invalid email or password. ' . 
                          ($attempts_remaining > 0 ? $attempts_remaining . ' attempts remaining. ' : '') . 
                          ($attempts_remaining <= 2 ? 'Use "Forgot Password" if you cannot remember your password.' : '')
            ]);
        }
    } catch (PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Database error occurred',
            'debug' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    } catch (Exception $e) {
        error_log('Login Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'An error occurred during login',
            'debug' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed.']);
    }
} catch (PDOException $e) {
    logError('Database Error: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    sendJsonResponse([
        'success' => false,
        'error' => 'Database connection failed',
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], 500);
    
} catch (Exception $e) {
    logError('Unexpected Error: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    sendJsonResponse([
        'success' => false,
        'error' => 'An unexpected error occurred',
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], 500);
}
