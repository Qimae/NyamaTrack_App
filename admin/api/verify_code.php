<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = [];
parse_str($input, $data);
$code = $data['code'] ?? '';

// Validate input
if (empty($code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Account code is required']);
    exit;
}

try {
    // Check if the code exists
    $stmt = $pdo->prepare("SELECT * FROM account_codes WHERE account_code = ?");
    $stmt->execute([$code]);
    $codeData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($codeData) {
        // Store the verified code in session
        $_SESSION['verified_code'] = $code;
        
        echo json_encode([
            'success' => true,
            'message' => 'Code verified successfully',
            'code' => $code
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired account code'
        ]);
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while verifying the code'
    ]);
}
