<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $code = $data['code'] ?? '';
    
    if (empty($code)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Account code is required']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM account_codes WHERE account_code = ?");
        $stmt->execute([$code]);
        $codeData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($codeData) {
            $_SESSION['verified_code'] = $code;
            echo json_encode([
                'success' => true,
                'message' => 'Access granted',
                'code' => $code
            ]);
        } else {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or expired registration code'
            ]);
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while verifying access'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
