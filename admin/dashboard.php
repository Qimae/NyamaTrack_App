<?php
require_once '../db/config.php';
session_start();

// Check if user is admin and logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get total revenue from mpesa_transactions
$revenueQuery = $pdo->query("SELECT COALESCE(SUM(Amount), 0) as total_revenue FROM mpesa_transactions WHERE ResultCode = 0");
$totalRevenue = $revenueQuery->fetch(PDO::FETCH_ASSOC)['total_revenue'];

// Get active butcheries (with valid subscription)
$activeButcheriesQuery = $pdo->query("SELECT COUNT(DISTINCT business_name) as active_butcheries FROM mpesa_transactions WHERE end_date > NOW() AND ResultCode = 0");
$activeButcheries = $activeButcheriesQuery->fetch(PDO::FETCH_ASSOC)['active_butcheries'];

// Get total admin users
$adminUsersQuery = $pdo->query("SELECT COUNT(*) as admin_count FROM users");
$totalAdmins = $adminUsersQuery->fetch(PDO::FETCH_ASSOC)['admin_count'];

// Get recent M-Pesa transactions
$recentTransactionsQuery = $pdo->query("
    SELECT business_name, Amount, transaction_date, MpesaReceiptNumber, PhoneNumber 
    FROM mpesa_transactions 
    WHERE ResultCode = 0 
    ORDER BY transaction_date DESC, id DESC 
    LIMIT 5
");
$recentTransactions = $recentTransactionsQuery->fetchAll(PDO::FETCH_ASSOC);

// Get recent unblock requests
$unblockRequestsQuery = $pdo->query("
    SELECT id, business_name, email, reason, created_at 
    FROM blocked_butcheries 
    ORDER BY created_at DESC 
    LIMIT 5
");
$unblockRequests = $unblockRequestsQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamaTrack Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="utils/emmanuel.css">

</head>

<body class="bg-black text-white">
    <?php include 'includes/left-sidebar.php'; ?>
    <?php include 'includes/bottom-sidebar.php'; ?>

    <div class="main-content">
        <div class="p-6">
            <!-- Page Header -->
            <div class="relative mb-6 sm:mb-8 p-4 sm:p-6 bg-gradient-to-r from-gray-900 to-black rounded-xl border border-gray-800">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="p-2 sm:p-3 rounded-lg bg-gradient-to-br from-red-600 to-orange-600">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">NyamaTrack Admin</h1>
                        <p class="text-sm sm:text-base text-gray-400">Manage payments, butcheries, and track your business growth all in one place with our professional interface.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Revenue</h3>
                        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-400">KSh <?= number_format($totalRevenue, 2) ?></div>
                    <p class="text-xs text-gray-500">Total revenue from all subscriptions</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Active Butcheries</h3>
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-blue-400"><?= $activeButcheries ?></div>
                    <p class="text-xs text-gray-500">Active butcheries with valid subscription</p>
                </div>
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Employees</h3>
                        <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-purple-400"><?= $totalAdmins ?></div>
                    <p class="text-xs text-gray-500">Total admin users in the system</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl card-hover">
                    <div class="p-6 border-b border-gray-800">
                        <h3 class="text-white flex items-center gap-2 text-lg font-semibold">
                        <i class="fas fa-money-bill-wave text-green-500"></i>
                            Recent Butchery Payments
                        </h3>
                    </div>
                    <div class="p-6">                       
                        <div class="overflow-x-auto mb-4">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-800">
                                        <th class="text-left text-orange-400 font-semibold py-3">Business</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Amount</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Date</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800">
                                    <?php if (empty($recentTransactions)): ?>
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-gray-400">No recent transactions found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentTransactions as $transaction): ?>
                                            <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                                <td class="py-4 font-medium text-white"><?php echo htmlspecialchars($transaction['business_name']); ?></td>
                                                <td class="py-4 text-green-400">Ksh <?php echo number_format($transaction['Amount'], 2); ?></td>
                                                <td class="py-4 text-gray-300"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                                                <td class="py-4 text-blue-400 font-mono"><?php echo htmlspecialchars($transaction['MpesaReceiptNumber']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <a href="subscriptions.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors no-underline">
                                View All Payments
                            </a>                            
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl card-hover">
                    <div class="p-6 border-b border-gray-800">
                        <h3 class="text-white flex items-center gap-2 text-lg font-semibold">
                        <i class="fas fa-lock-open text-yellow-500"></i>
                            Recent Unblock requests
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto mb-4">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-800">
                                        <th class="text-left text-orange-400 font-semibold py-3">Business</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Email</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Reason</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Blocked On</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800">
                                    <?php if (empty($unblockRequests)): ?>
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-gray-400">No unblock requests found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($unblockRequests as $request): ?>
                                            <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                                <td class="py-4 font-medium text-white"><?php echo htmlspecialchars($request['business_name']); ?></td>
                                                <td class="py-4 text-blue-400"><?php echo htmlspecialchars($request['email']); ?></td>
                                                <td class="py-4 text-gray-300 max-w-xs truncate" title="<?php echo htmlspecialchars($request['reason']); ?>">
                                                    <?php echo htmlspecialchars($request['reason']); ?>
                                                </td>
                                                <td class="py-4 text-gray-300">
                                                    <?php echo date('M d, Y', strtotime($request['created_at'])); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex gap-3">
                            <a href="blockings.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors no-underline">
                                View All Requests
                            </a>                          
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>