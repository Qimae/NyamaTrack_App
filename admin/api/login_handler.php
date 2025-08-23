<?php
// Handles login form input from login page
require_once __DIR__ . '/../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

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

    // Hash email for lookup
    $hash_email = hash('sha256', strtolower($email));

    try {
        // Check if admin user exists with the given email
        $stmt = $pdo->prepare("SELECT id, email, fullname, password FROM admin_users WHERE email_hash = ? LIMIT 1");
        $stmt->execute([$hash_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password.']);
            exit;
        }

        // Decrypt and verify the email
        $decrypted_email = decrypt_data($user['email'], $secret_key);

        if ($email !== $decrypted_email) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password.']);
            exit;
        }

        // Decrypt fullname for session and response
        $dec_fullname = decrypt_data($user['fullname'], $secret_key);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int)$user['id'];
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
