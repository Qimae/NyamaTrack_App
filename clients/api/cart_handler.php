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
$method = $_SERVER['REQUEST_METHOD'];

// Get active cart or create one if it doesn't exist
function getOrCreateCart($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cart) {
        $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
        $stmt->execute([$user_id]);
        $cart_id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT * FROM carts WHERE id = ?");
        $stmt->execute([$cart_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    return $cart;
}

// Get cart with items
function getCartWithItems($pdo, $cart_id) {
    $stmt = $pdo->prepare("
        SELECT ci.*, p.name, p.image_path 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        // Get cart contents
        $cart = getOrCreateCart($pdo, $user_id);
        $items = getCartWithItems($pdo, $cart['id']);
        
        // Calculate total
        $total = array_reduce($items, function($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
        
        echo json_encode([
            'success' => true,
            'cart' => [
                'id' => $cart['id'],
                'items' => $items,
                'total' => $total
            ]
        ]);
        break;
        
    case 'POST':
        // Add item to cart
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing product_id or quantity']);
            exit;
        }
        
        $product_id = $data['product_id'];
        $quantity = (int)$data['quantity'];
        
        // Get product price
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Product not found']);
            exit;
        }
        
        $cart = getOrCreateCart($pdo, $user_id);
        
        // Check if item already in cart
        $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
        $stmt->execute([$cart['id'], $product_id]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $success = $stmt->execute([$newQuantity, $existingItem['id']]);
        } else {
            // Add new item
            $stmt = $pdo->prepare("
                INSERT INTO cart_items (cart_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $success = $stmt->execute([$cart['id'], $product_id, $quantity, $product['price']]);
        }
        
        if ($success) {
            $items = getCartWithItems($pdo, $cart['id']);
            $total = array_reduce($items, function($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);
            
            echo json_encode([
                'success' => true,
                'cart' => [
                    'id' => $cart['id'],
                    'items' => $items,
                    'total' => $total,
                    'itemCount' => count($items)
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update cart']);
        }
        break;
        
    case 'DELETE':
        // Remove item from cart
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['item_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing item_id']);
            exit;
        }
        
        $cart = getOrCreateCart($pdo, $user_id);
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
        $success = $stmt->execute([$data['item_id'], $cart['id']]);
        
        if ($success) {
            $items = getCartWithItems($pdo, $cart['id']);
            $total = array_reduce($items, function($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);
            
            echo json_encode([
                'success' => true,
                'cart' => [
                    'id' => $cart['id'],
                    'items' => $items,
                    'total' => $total,
                    'itemCount' => count($items)
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to remove item']);
        }
        break;
        
    case 'PUT':
        // Update item quantity
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['item_id']) || !isset($data['quantity'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing item_id or quantity']);
            exit;
        }
        
        $cart = getOrCreateCart($pdo, $user_id);
        $quantity = (int)$data['quantity'];
        
        if ($quantity <= 0) {
            // If quantity is 0 or negative, remove the item
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
            $success = $stmt->execute([$data['item_id'], $cart['id']]);
        } else {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?");
            $success = $stmt->execute([$quantity, $data['item_id'], $cart['id']]);
        }
        
        if ($success) {
            $items = getCartWithItems($pdo, $cart['id']);
            $total = array_reduce($items, function($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);
            
            echo json_encode([
                'success' => true,
                'cart' => [
                    'id' => $cart['id'],
                    'items' => $items,
                    'total' => $total,
                    'itemCount' => count($items)
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update cart']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        break;
}
