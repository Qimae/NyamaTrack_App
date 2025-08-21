<?php
// Handles login form input from login.html

require_once __DIR__ . '/../db/config.php';

// Constants for login attempts
const MAX_LOGIN_ATTEMPTS = 3;
const LOCKOUT_MINUTES = 3; // 30 minutes lockout after max attempts

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
        // Get user with failed_attempts and last_failed_attempt
        $stmt = $pdo->prepare("SELECT id, fullname, business_name, password, failed_attempts, last_failed_attempt FROM users WHERE email_hash = ? AND business_name_hash = ? LIMIT 1");
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
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['business_name'] = $dec_business_name;
            $_SESSION['email'] = $email;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful.', 
                'fullname' => $dec_fullname, 
                'user_id' => $user['id']
            ]);
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
                        // Add to blocked_butcheries table
                        $blockStmt = $pdo->prepare("INSERT INTO blocked_butcheries (email, business_name, reason) VALUES (?, ?, ?)");
                        $blockStmt->execute([
                            $email,
                            $business_name,
                            'Account locked due to ' . MAX_LOGIN_ATTEMPTS . ' failed login attempts. Contact support@nyamatrack.co.ke'
                        ]);
                    } catch (PDOException $e) {
                        // Log error but don't expose to user
                        error_log("Error adding to blocked_butcheries: " . $e->getMessage());
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
        http_response_code(500);
        echo json_encode(['error' => 'Login failed.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
