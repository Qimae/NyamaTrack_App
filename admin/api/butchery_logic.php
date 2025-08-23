<?php
// Function to decrypt data (matching login handler)
function decrypt_data($data, $key) {
    if (empty($data)) return '';
    $cipher = 'AES-128-CTR';
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $encrypted = substr($data, $ivlen);
    return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
}

// Function to get all butcheries with their status and total revenue
function getButcheriesData($pdo) {
    try {
        // Get the total revenue from all M-Pesa transactions (not encrypted)
        $revenueQuery = "
            SELECT COALESCE(SUM(CAST(Amount AS DECIMAL(10,2))), 0) as total_revenue
            FROM mpesa_transactions 
            WHERE ResultCode = 0
        ";
        
        $revenueStmt = $pdo->prepare($revenueQuery);
        $revenueStmt->execute();
        $revenueData = $revenueStmt->fetch(PDO::FETCH_ASSOC);
        $totalRevenue = (float)($revenueData['total_revenue'] ?? 0);
        
        // Get all butcheries with their status
        $query = "
            SELECT 
                u.*,
                CASE 
                    WHEN bb.id IS NOT NULL THEN 'Blocked'
                    WHEN EXISTS (
                        SELECT 1 FROM mpesa_transactions 
                        WHERE business_name = u.business_name 
                        AND end_date > NOW() 
                        AND ResultCode = 0
                    ) THEN 'Active'
                    ELSE 'Inactive'
                END as status
            FROM users u
            LEFT JOIN blocked_butcheries bb ON u.business_name = bb.business_name
            ORDER BY u.created_at DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $butcheries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate counts for dashboard and decrypt user data
        $totalButcheries = count($butcheries);
        $activeButcheries = 0;
        $blockedButcheries = 0;
        
        foreach ($butcheries as &$butchery) {
            // Decrypt user data
            $butchery['fullname'] = decrypt_data($butchery['fullname'], 'Nyama@546_2025');
            $butchery['business_name'] = decrypt_data($butchery['business_name'], 'Nyama@546_2025');
            $butchery['permit'] = decrypt_data($butchery['permit'], 'Nyama@546_2025');
            $butchery['location'] = !empty($butchery['location']) ? decrypt_data($butchery['location'], 'Nyama@546_2025') : '';
            $butchery['phone'] = !empty($butchery['phone']) ? decrypt_data($butchery['phone'], 'Nyama@546_2025') : '';
            $butchery['email'] = !empty($butchery['email']) ? decrypt_data($butchery['email'], 'Nyama@546_2025') : '';
            
            // Count statuses
            if ($butchery['status'] === 'Active') {
                $activeButcheries++;
            } elseif ($butchery['status'] === 'Blocked') {
                $blockedButcheries++;
            }
        }
        
        return [
            'butcheries' => $butcheries,
            'totalRevenue' => $totalRevenue,
            'totalButcheries' => $totalButcheries,
            'activeButcheries' => $activeButcheries,
            'blockedButcheries' => $blockedButcheries
        ];
        
    } catch (PDOException $e) {
        error_log("Database error in butchery_logic.php: " . $e->getMessage());
        return [
            'butcheries' => [],
            'totalRevenue' => 0,
            'totalButcheries' => 0,
            'activeButcheries' => 0,
            'blockedButcheries' => 0
        ];
    }
}
?>
