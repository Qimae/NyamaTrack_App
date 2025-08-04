<?php
// Handles login form input from login.html

require_once __DIR__ . '/../db/config.php';

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
        $stmt = $pdo->prepare("SELECT id, fullname, business_name, password FROM users WHERE email_hash = ? AND business_name_hash = ? LIMIT 1");
        $stmt->execute([$hash_email, $hash_business_name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            // Decrypt fullname and business_name for session and response
            $dec_fullname = decrypt_data($user['fullname'], $secret_key);
            $dec_business_name = decrypt_data($user['business_name'], $secret_key);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['business_name'] = $dec_business_name;
            $_SESSION['email'] = $email;
            echo json_encode(['success' => true, 'message' => 'Login successful.', 'fullname' => $dec_fullname, 'user_id' => $user['id']]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid business name, email or password.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Login failed.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
