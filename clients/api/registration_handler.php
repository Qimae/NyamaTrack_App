<?php
// registration_handler.php
// Handles registration form input from registration.html
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1);

require_once __DIR__ . '/../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Encryption function
    function encrypt_data($data, $key) {
        $cipher = 'AES-128-CTR';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    $secret_key = 'Nyama@546_2025'; // Change to a secure key and store safely

    // Basic validation
    if (!$fullname || !$phone || !$email || !$password || !$confirm) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format.']);
        exit;
    }
    if (!preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid phone number.']);
        exit;
    }
    if ($password !== $confirm) {
        http_response_code(400);
        echo json_encode(['error' => 'Passwords do not match.']);
        exit;
    }
    // Password policy: min 8 chars, upper, lower, number, symbol
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 8 characters, include uppercase, lowercase, number, and symbol.']);
        exit;
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);


    // Encrypt all fields except password hash
    $enc_fullname = encrypt_data($fullname, $secret_key);
    $enc_phone = encrypt_data($phone, $secret_key);
    $enc_email = encrypt_data($email, $secret_key);

    // Hash email for lookup
    $hash_email = hash('sha256', strtolower($email));

    // Check if user already exists (by email)
    $checkStmt = $pdo->prepare("SELECT id FROM client_users WHERE email = ? LIMIT 1");
    $checkStmt->execute([$enc_email]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'User with this email already exists.']);
        exit;
    }

    // Insert into DB using PDO
    try {
        $stmt = $pdo->prepare("INSERT INTO client_users (fullname, phone, email, password, email_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $enc_fullname,
            $enc_phone,
            $enc_email,
            $passwordHash,
            $hash_email,
        ]);
        echo json_encode(['success' => true, 'message' => 'Registration successful.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed.', 'details' => $e->getMessage()]);
    }
}
?>