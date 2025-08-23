<?php
require_once __DIR__ . '/../db/config.php';

// Function to encrypt data
function encrypt_data($data, $key) {
    if (empty($data)) return '';
    $cipher = 'AES-128-CTR';
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Function to decrypt data
function decrypt_data($data, $key) {
    if (empty($data)) return '';
    $cipher = 'AES-128-CTR';
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $encrypted = substr($data, $ivlen);
    return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
}

// Get all admin employees
function getAdminEmployees($pdo) {
    $debug = [];
    try {
        // Debug output
        $debug[] = "Database connection successful";
        
        // Check if the table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'admin_users'");
        if ($tableCheck->rowCount() === 0) {
            $debug[] = "Table 'admin_users' does not exist";
            return ['debug' => $debug, 'employees' => []];
        }
        
        // Get column names
        $columns = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(PDO::FETCH_COLUMN);
        $debug[] = "Columns in admin_users: " . implode(', ', $columns);
        
        // Get row count
        $count = $pdo->query("SELECT COUNT(*) as count FROM admin_users")->fetch(PDO::FETCH_ASSOC);
        $debug[] = "Number of admin users: " . $count['count'];
        
        // Get all records
        $query = "SELECT * FROM admin_users ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $debug[] = "Found " . count($employees) . " employees";
        
        // Decrypt sensitive data
        $decryptedCount = 0;
        foreach ($employees as &$employee) {
            if (!empty($employee['fullname'])) {
                $employee['fullname'] = decrypt_data($employee['fullname'], 'Nyama@546_2025');
                $decryptedCount++;
            }
            if (!empty($employee['phone'])) {
                $employee['phone'] = decrypt_data($employee['phone'], 'Nyama@546_2025');
                $decryptedCount++;
            }
            if (!empty($employee['email'])) {
                $employee['email'] = decrypt_data($employee['email'], 'Nyama@546_2025');
                $decryptedCount++;
            }
        }
        $debug[] = "Decrypted $decryptedCount fields";
        
        return ['debug' => $debug, 'employees' => $employees];
    } catch (PDOException $e) {
        $errorInfo = [
            'message' => $e->getMessage(),
            'sql_state' => $e->errorInfo[0],
            'error_code' => $e->errorInfo[1],
            'error_message' => $e->errorInfo[2]
        ];
        $debug[] = "Database error: " . print_r($errorInfo, true);
        return ['debug' => $debug, 'employees' => [], 'error' => $errorInfo];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
                    $password = $_POST['password'] ?? '';
                    
                    if ($fullname && $email && $password) {
                        // Check if email already exists
                        $checkStmt = $pdo->prepare("SELECT id FROM admin_users WHERE email_hash = ?");
                        $checkStmt->execute([hash('sha256', $email)]);
                        if ($checkStmt->fetch()) {
                            $response['message'] = 'An admin with this email already exists';
                            break;
                        }
                        
                        $query = "INSERT INTO admin_users (fullname, phone, email, password, email_hash) 
                                 VALUES (:fullname, :phone, :email, :password, :email_hash)";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute([
                            ':fullname' => encrypt_data($fullname, 'Nyama@546_2025'),
                            ':phone' => encrypt_data($phone, 'Nyama@546_2025'),
                            ':email' => encrypt_data($email, 'Nyama@546_2025'),
                            ':password' => password_hash($password, PASSWORD_DEFAULT),
                            ':email_hash' => hash('sha256', $email)
                        ]);
                        $response = ['success' => true, 'message' => 'Employee added successfully'];
                    } else {
                        $response['message'] = 'Missing required fields';
                    }
                    break;
                    
                case 'update':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
                    
                    if ($id && $fullname && $email) {
                        // Check if email is being changed and if the new one already exists
                        $checkStmt = $pdo->prepare("SELECT email FROM admin_users WHERE id = ?");
                        $checkStmt->execute([$id]);
                        $current = $checkStmt->fetch();
                        $currentEmail = $current ? decrypt_data($current['email'], 'Nyama@546_2025') : '';
                        
                        if (strtolower($email) !== strtolower($currentEmail)) {
                            $checkEmailStmt = $pdo->prepare("SELECT id FROM admin_users WHERE email_hash = ? AND id != ?");
                            $checkEmailStmt->execute([hash('sha256', $email), $id]);
                            if ($checkEmailStmt->fetch()) {
                                $response['message'] = 'An admin with this email already exists';
                                break;
                            }
                        }
                        
                        $query = "UPDATE admin_users SET 
                                 fullname = :fullname,
                                 email = :email,
                                 email_hash = :email_hash,
                                 phone = :phone
                                 WHERE id = :id";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute([
                            ':fullname' => encrypt_data($fullname, 'Nyama@546_2025'),
                            ':email' => encrypt_data($email, 'Nyama@546_2025'),
                            ':email_hash' => hash('sha256', $email),
                            ':phone' => encrypt_data($phone, 'Nyama@546_2025'),
                            ':id' => $id
                        ]);
                        $response = ['success' => true, 'message' => 'Employee updated successfully'];
                    } else {
                        $response['message'] = 'Missing required fields';
                    }
                    break;
                    
                case 'delete':
                    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                    if ($id) {
                        // Prevent deleting the last admin
                        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
                        $count = $countStmt->fetch()['count'];
                        
                        if ($count <= 1) {
                            $response['message'] = 'Cannot delete the last admin user';
                        } else {
                            $query = "DELETE FROM admin_users WHERE id = ?";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute([$id]);
                            $response = ['success' => true, 'message' => 'Employee deleted successfully'];
                        }
                    }
                    break;
            }
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}
