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
    define('MAX_LOGIN_ATTEMPTS', 3);
    define('LOCKOUT_MINUTES', 3); // 30 minutes lockout after max attempts

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

    if (!$business_name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Business name, email and password are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format.']);
        exit;
    }

    // Hash business_name and email for lookup
    $hash_business_name = hash('sha256', strtolower($business_name));
    $hash_email = hash('sha256', strtolower($email));

    try {
        // Get user with all necessary fields including created_at
        $stmt = $pdo->prepare("SELECT id, fullname, business_name, password, failed_attempts, last_failed_attempt, created_at FROM users WHERE email_hash = ? AND business_name_hash = ? LIMIT 1");
        $stmt->execute([$hash_email, $hash_business_name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if account is locked
        if ($user && $user['failed_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $last_attempt = strtotime($user['last_failed_attempt']);
            $time_since_last_attempt = (time() - $last_attempt) / 60; // in minutes
            
            if ($time_since_last_attempt < LOCKOUT_MINUTES) {
                $time_remaining = ceil(LOCKOUT_MINUTES - $time_since_last_attempt);
                http_response_code(403);
                echo json_encode([
                    'error' => 'Account locked. Please try again in ' . $time_remaining . ' minutes. or contact support@nyamatrack.co.ke',
                    'locked' => true,
                    'time_remaining' => $time_remaining
                ]);
                exit;
            } else {
                // Reset failed attempts if lockout period has passed
                $resetStmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE id = ?");
                $resetStmt->execute([$user['id']]);
                $user['failed_attempts'] = 0;
            }
        }

        if ($user && password_verify($password, $user['password'])) {
            // Reset failed attempts on successful login
            $resetStmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE id = ?");
            $resetStmt->execute([$user['id']]);
            
            // Decrypt fullname and business_name for session and response
            $dec_fullname = decrypt_data($user['fullname'], $secret_key);
            $dec_business_name = decrypt_data($user['business_name'], $secret_key);
            $user_id = (int)$user['id'];
            
            // Check subscription status
            $subscriptionStmt = $pdo->prepare("
                SELECT s.*, u.created_at as user_created_at 
                FROM subscribers s 
                RIGHT JOIN users u ON s.business_name = u.business_name 
                WHERE u.business_name = ?
            ");
            $subscriptionStmt->execute([$dec_business_name]);
            $subscription = $subscriptionStmt->fetch(PDO::FETCH_ASSOC);
            
            $now = new DateTime();
            $trialEndDate = new DateTime($subscription ? $subscription['end_date'] : $user['created_at']);
            $trialEndDate->modify('+7 days');
            $daysRemaining = $now->diff($trialEndDate)->days;
            $isTrialExpired = $now > $trialEndDate;
            $isSubscribed = $subscription && $subscription['status'] === 'active' && $subscription['subscription_type'] === 'paid';
            
            // If no subscription exists and user is new, create a trial
            if (!$subscription) {
                $trialEndDate = new DateTime();
                $trialEndDate->modify('+7 days');
                
                // First check if business is blocked
                $checkBlocked = $pdo->prepare("SELECT * FROM blocked_butcheries WHERE business_name = ?");
                $checkBlocked->execute([$dec_business_name]);
                
                if ($checkBlocked->rowCount() > 0) {
                    throw new Exception('This business has been blocked. Please contact support for assistance.');
                }
                
                $insertStmt = $pdo->prepare("
                    INSERT INTO subscribers (user_id, business_name, subscription_type, status, end_date)
                    VALUES (?, ?, 'trial', 'active', ?)
                ");
                $insertStmt->execute([$user_id, $dec_business_name, $trialEndDate->format('Y-m-d H:i:s')]);
                
                $daysRemaining = 7;
                $isTrialExpired = false;
            } elseif ($subscription['status'] === 'expired' && $subscription['subscription_type'] === 'trial') {
                $isTrialExpired = true;
                $daysRemaining = 0;
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['business_name'] = $dec_business_name;
            $_SESSION['email'] = $email;
            $_SESSION['is_trial'] = !$isSubscribed;
            $_SESSION['trial_days_remaining'] = $daysRemaining;
            $_SESSION['trial_expired'] = $isTrialExpired && !$isSubscribed;
            
            // Prepare response
            $response = [
                'success' => true, 
                'message' => 'Login successful.', 
                'fullname' => $dec_fullname, 
                'user_id' => $user_id,
                'subscription' => [
                    'is_trial' => !$isSubscribed,
                    'trial_days_remaining' => $daysRemaining,
                    'trial_expired' => $isTrialExpired && !$isSubscribed
                ]
            ];
            
            // Redirect to payment if trial expired and not subscribed
            if ($isTrialExpired && !$isSubscribed) {
                $response['redirect'] = 'payment.php';
            }
            
            echo json_encode($response);
        } else {
            $attempts_remaining = 0;
            
            if ($user) {
                // Increment failed attempts
                $new_attempts = $user['failed_attempts'] + 1;
                $attempts_remaining = max(0, MAX_LOGIN_ATTEMPTS - $new_attempts);
                
                $updateStmt = $pdo->prepare("UPDATE users SET failed_attempts = ?, last_failed_attempt = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$new_attempts, $user['id']]);
                
                // If this was the 3rd failed attempt, block the account
                if ($new_attempts >= MAX_LOGIN_ATTEMPTS) {
                    try {
                        // Check if business is already blocked
                        $checkStmt = $pdo->prepare("SELECT id FROM blocked_butcheries WHERE business_name = ? LIMIT 1");
                        $checkStmt->execute([$business_name]);
                        $alreadyBlocked = $checkStmt->fetch();
                        
                        if (!$alreadyBlocked) {
                            // Only insert if not already blocked
                            $blockStmt = $pdo->prepare("INSERT INTO blocked_butcheries (email, business_name, reason) VALUES (?, ?, ?)");
                            $blockStmt->execute([
                                $email,
                                $business_name,
                                'Account locked due to ' . MAX_LOGIN_ATTEMPTS . ' failed login attempts. Contact support@nyamatrack.co.ke'
                            ]);
                        }
                    } catch (PDOException $e) {
                        // Log error but don't expose to user
                        error_log("Error in blocked_butcheries operation: " . $e->getMessage());
                    }
                    
                    http_response_code(403);
                    echo json_encode([
                        'error' => 'Account locked. Too many failed login attempts. Please try again later or contact support@nyamatrack.co.ke',
                        'locked' => true,
                        'time_remaining' => LOCKOUT_MINUTES
                    ]);
                    exit;
                }
            }
            
            http_response_code(401);
            echo json_encode([
                'error' => 'Invalid business name, email or password. ' . 
                          ($attempts_remaining > 0 ? $attempts_remaining . ' attempts remaining.' : '')
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
