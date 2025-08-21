<?php
require_once __DIR__ . '/../../db/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$blockId = $input['id'] ?? null;

if (!$blockId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Block ID is required']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
        // 1. Get the blocked user details before deleting
    $stmt = $pdo->prepare("SELECT id, email, business_name FROM blocked_butcheries WHERE id = ?");
    $stmt->execute([$blockId]);
    $blockedUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$blockedUser) {
        error_log("Blocked user not found with ID: " . $blockId);
        throw new Exception('Blocked user not found');
    }
    
    // Hash the email and business_name to match the users table
    $emailHash = hash('sha256', $blockedUser['email']);
    $businessNameHash = hash('sha256', $blockedUser['business_name']);
    
    error_log("Processing unblock for user - Email: " . $blockedUser['email'] . ", Business: " . $blockedUser['business_name']);
    error_log("Hashed values - Email: $emailHash, Business: $businessNameHash");
    
    // 2. Update using the hashed values that match the users table's unique constraint
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET failed_attempts = 0, 
            last_failed_attempt = NULL 
        WHERE email_hash = ? AND business_name_hash = ?
    ");
    $updateStmt->execute([$emailHash, $businessNameHash]);
    $rowsAffected = $updateStmt->rowCount();
    
    error_log("Update attempt with hashed values - Rows affected: " . $rowsAffected);
    
    // 4. If still no rows affected, try to find the user in the users table
    if ($rowsAffected === 0) {
        $checkUser = $pdo->prepare("SELECT id FROM users WHERE email = ? OR business_name = ? LIMIT 1");
        $checkUser->execute([$blockedUser['email'], $blockedUser['business_name']]);
        $userExists = $checkUser->fetch(PDO::FETCH_ASSOC);
        
        if (!$userExists) {
            error_log("User not found in users table - Email: " . $blockedUser['email'] . ", Business: " . $blockedUser['business_name']);
            // Don't throw error, just log it since we still want to remove from blocked_butcheries
        } else {
            error_log("User exists but update failed - User ID: " . $userExists['id']);
        }
    }
    
    // 3. Remove from blocked_butcheries
    $deleteStmt = $pdo->prepare("DELETE FROM blocked_butcheries WHERE id = ?");
    $deleteStmt->execute([$blockId]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'User has been unblocked successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to unblock user',
        'debug' => $e->getMessage()
    ]);
}
