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

// Encryption function (matching registration handler)
function encrypt_data($data, $key) {
    $cipher = 'AES-128-CTR';
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}


try {
    // Secret key for encryption (must match registration handler)
    $secret_key = 'Nyama@546_2025';
    
    // Load database configuration
    $configFile = __DIR__ . '/../db/config.php';
    if (!file_exists($configFile)) {
        throw new Exception('Database configuration file not found');
    }
    require_once $configFile;
    
    // Start session
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Basic validation
        if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
            sendJsonResponse(['error' => 'All fields are required.'], 400);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendJsonResponse(['error' => 'Invalid email format.'], 400);
        }
        
        if ($newPassword !== $confirmPassword) {
            sendJsonResponse(['error' => 'Passwords do not match.'], 400);
        }
        
        // Password policy: min 8 chars, upper, lower, number, symbol
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
            sendJsonResponse([
                'error' => 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.'
            ], 400);
        }
        
        // Hash email for lookup
        $hash_email = hash('sha256', strtolower($email));
        $enc_email = encrypt_data($email, $secret_key);
        
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Check if user exists and get current data
            $stmt = $pdo->prepare("SELECT id, email FROM client_users WHERE email_hash = ? OR email = ? LIMIT 1 FOR UPDATE");
            $stmt->execute([$hash_email, $enc_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If user not found by hash, try by encrypted email (backward compatibility)
            if (!$user) {
                $stmt = $pdo->prepare("SELECT id, email FROM client_users WHERE email = ? LIMIT 1 FOR UPDATE");
                $stmt->execute([$enc_email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if (!$user) {
                $pdo->rollBack();
                sendJsonResponse(['error' => 'No account found with that email address.'], 404);
            }
            
            // Hash the new password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password and reset failed attempts
            $updateStmt = $pdo->prepare("
                UPDATE client_users 
                SET password = ?, 
                    failed_attempts = 0, 
                    last_failed_attempt = NULL,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $updateStmt->execute([$passwordHash, $user['id']]);
            
            // Commit transaction
            $pdo->commit();
            
            // Clear any existing session
            session_destroy();
            
            sendJsonResponse([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now log in with your new password.'
            ]);
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            logError('Database error during password reset', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            sendJsonResponse(['error' => 'Failed to reset password. Please try again.'], 500);
        }
    } else {
        sendJsonResponse(['error' => 'Method not allowed. Use POST.'], 405);
    }
    
} catch (PDOException $e) {
    logError('Database connection error', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
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
    logError('Unexpected error', [
        'message' => $e->getMessage(),
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
