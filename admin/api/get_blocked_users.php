<?php
require_once __DIR__ . '/../../db/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $query = "SELECT 
                b.id, 
                b.email, 
                b.business_name, 
                b.reason, 
                DATE_FORMAT(b.created_at, '%Y-%m-%d %H:%i:%s') as blocked_date,
                u.id as user_id
              FROM blocked_butcheries b
              LEFT JOIN users u ON b.email = u.email AND b.business_name = u.business_name
              ORDER BY b.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $blockedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $blockedUsers
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch blocked users',
        'debug' => $e->getMessage()
    ]);
}
