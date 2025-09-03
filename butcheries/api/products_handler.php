<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Include database configuration
$configFile = __DIR__ . '/../db/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration not found']);
    exit;
}

require_once $configFile;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to generate a unique product ID
function generateProductId($pdo) {
    do {
        $id = 'NYMPRD-' . strtoupper(substr(uniqid(), -8));
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->execute([$id]);
    } while ($stmt->rowCount() > 0);
    
    return $id;
}

// Function to handle file upload
function handleFileUpload($file, $productId) {
    $uploadDir = __DIR__ . '/../uploads/products/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Get file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = $productId . '.' . $fileExt;
    $targetPath = $uploadDir . $fileName;
    
    // Check if file is an actual image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        throw new Exception('File is not an image.');
    }
    
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('File is too large. Maximum size is 2MB.');
    }
    
    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExt, $allowedTypes)) {
        throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
    }
    
    // Try to upload file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/products/' . $fileName;
    } else {
        throw new Exception('There was an error uploading your file.');
    }
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Authentication required', 401);
    }
    
    // Database connection is already established in config.php as $pdo
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception('Database connection failed');
    }
    
    // Get action from query string
    $action = $_GET['action'] ?? '';
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($action === 'get_products') {
                // Get business name from session or user data
                $businessName = $_SESSION['business_name'] ?? null;
                if (!$businessName && isset($_SESSION['user_id'])) {
                    $stmt = $pdo->prepare("SELECT business_name FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    $businessName = $user['business_name'] ?? null;
                }
                
                if ($businessName) {
                    // Get products for the specific business
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE business_name = ? ORDER BY created_at DESC");
                    $stmt->execute([$businessName]);
                } else {
                    // Fallback to all products (admin view)
                    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
                }
                
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $products
                ]);
            } elseif ($action === 'get_product' && !empty($_GET['id'])) {
                // Get single product
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    echo json_encode([
                        'success' => true,
                        'data' => $product
                    ]);
                } else {
                    throw new Exception('Product not found');
                }
            } else {
                throw new Exception('Invalid action');
            }
            break;
            
        case 'POST':
            // Handle file upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $productId = !empty($_POST['id']) ? $_POST['id'] : generateProductId($pdo);
                $imagePath = handleFileUpload($_FILES['image'], $productId);
            }
            
            // Get business name from session or user data
            $businessName = $_SESSION['business_name'] ?? null;
            if (!$businessName) {
                // If not in session, get from users table
                $stmt = $pdo->prepare("SELECT business_name FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $businessName = $user['business_name'] ?? null;
                
                if (!$businessName) {
                    throw new Exception('Business name not found');
                }
            }
            
            // Prepare product data
            $productData = [
                'id' => !empty($_POST['id']) ? $_POST['id'] : generateProductId($pdo),
                'business_name' => $businessName,
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'price' => floatval($_POST['price']),
                'created_by' => $_SESSION['user_id'] ?? null
            ];
            
            if ($imagePath) {
                $productData['image_path'] = $imagePath;
            }
            
            // Check if product exists (update) or new (insert)
            $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->execute([$productData['id']]);
            $productExists = $stmt->fetch();
            
            if ($productExists) {
                // Update existing product
                $updateFields = [];
                $updateValues = [];
                
                foreach (['name', 'description', 'price', 'image_path'] as $field) {
                    if (isset($productData[$field])) {
                        $updateFields[] = "$field = ?";
                        $updateValues[] = $productData[$field];
                    }
                }
                
                if (!empty($updateFields)) {
                    $updateFields[] = 'updated_at = CURRENT_TIMESTAMP';
                    $sql = "UPDATE products SET " . implode(', ', $updateFields) . " WHERE id = ?";
                    $updateValues[] = $productData['id'];
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($updateValues);
                }
                
                $message = 'Product updated successfully';
            } else {
                // Insert new product
                $fields = array_keys($productData);
                $placeholders = array_fill(0, count($fields), '?');
                
                $sql = "INSERT INTO products (" . implode(', ', $fields) . ") 
                        VALUES (" . implode(', ', $placeholders) . ")";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($productData));
                
                $message = 'Product added successfully';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'product_id' => $productData['id']
            ]);
            break;
            
        case 'DELETE':
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['id'])) {
                throw new Exception('Product ID is required');
            }
            
            // First, get the image path to delete the file
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
            $stmt->execute([$input['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product && !empty($product['image_path'])) {
                $imagePath = __DIR__ . '/..' . $product['image_path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // Delete the product
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$input['id']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product deleted successfully'
                ]);
            } else {
                throw new Exception('Product not found or already deleted');
            }
            break;
            
        default:
            throw new Exception('Method not allowed', 405);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 400;
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
