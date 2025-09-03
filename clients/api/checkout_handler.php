<?php
header('Content-Type: application/json');
require_once '../db/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Start transaction
$pdo->beginTransaction();

try {
    // Get active cart with items
    $stmt = $pdo->prepare("
        SELECT c.id, ci.product_id, ci.quantity, ci.price, p.name as product_name, p.business_name
        FROM carts c
        JOIN cart_items ci ON c.id = ci.cart_id
        JOIN products p ON ci.product_id = p.id
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        throw new Exception('Cart is empty');
    }
    
    $cart_id = $items[0]['id'];
    $total = array_reduce($items, function($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status)
        VALUES (?, ?, 'pending')
    ");
    $stmt->execute([$user_id, $total]);
    $order_id = $pdo->lastInsertId();
    
    // Add order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($items as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }
    
    // Mark cart as completed
    $stmt = $pdo->prepare("UPDATE carts SET status = 'completed' WHERE id = ?");
    $stmt->execute([$cart_id]);
    
    // Create a new active cart for the user
    $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order placed successfully!'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
