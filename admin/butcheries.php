<?php
require_once '../db/config.php';
require_once 'api/butchery_logic.php';
session_start();

// Check if user is admin and logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get butchery data
$butcheryData = getButcheriesData($pdo);

// Extract data for the view
$butcheries = $butcheryData['butcheries'];
$totalButcheries = $butcheryData['totalButcheries'];
$activeButcheries = $butcheryData['activeButcheries'];
$blockedButcheries = $butcheryData['blockedButcheries'];
$totalRevenue = $butcheryData['totalRevenue'];

?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Butcheries Management - NyamaTrack Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="utils/emmanuel.css">
</head>

<body class="bg-black text-white">
    <?php include 'includes/left-sidebar.php'; ?>
    <?php include 'includes/bottom-sidebar.php'; ?>

    <div class="main-content">
        <div class="p-4 sm:p-6">

            <!-- Page Header -->    
            <div class="relative mb-6 sm:mb-8 p-4 sm:p-6 bg-gradient-to-r from-gray-900 to-black rounded-xl border border-gray-800">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="p-2 sm:p-3 rounded-lg bg-gradient-to-br from-red-600 to-orange-600">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Butcheries Management</h1>
                        <p class="text-sm sm:text-base text-gray-400">Manage all registered butcheries and their details</p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Butcheries</h3>
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-blue-400"><?php echo $totalButcheries; ?></div>
                    <p class="text-xs text-gray-500">Total registered butcheries</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Active Butcheries</h3>
                        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-400"><?php echo $activeButcheries; ?></div>
                    <p class="text-xs text-gray-500">Currently active</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Blocked Butcheries</h3>
                        <svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-red-400"><?php echo $blockedButcheries; ?></div>
                    <p class="text-xs text-gray-500">Currently blocked</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Revenue</h3>
                        <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-purple-400">KSh <?php echo number_format($totalRevenue, 2); ?></div>
                    <p class="text-xs text-gray-500">Total revenue</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between mb-6">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search butcheries, owners, or locations..." class="pl-10 bg-black border border-gray-700 text-white rounded-lg px-3 py-2 w-full focus:border-red-500 focus:outline-none">
                </div>
            </div>

            <!-- Butcheries Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                <?php if (empty($butcheries)): ?>
                    <div class="col-span-full text-center py-8 text-gray-400">No butcheries found</div>
                <?php else: ?>
                    <?php foreach ($butcheries as $butchery): 
                        $statusClass = [
                            'Active' => 'bg-green-500/10 text-green-400',
                            'Blocked' => 'bg-red-500/10 text-red-400',
                            'Inactive' => 'bg-yellow-500/10 text-yellow-400'
                        ][$butchery['status']] ?? 'bg-gray-500/10 text-gray-400';
                    ?>
                        <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl card-hover overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-gray-800">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold border-2 border-red-500/20">
                                            <?php echo strtoupper(substr($butchery['business_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h3 class="text-lg text-white font-semibold"><?php echo htmlspecialchars($butchery['business_name']); ?></h3>
                                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($butchery['fullname']); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $statusClass; ?>">
                                            <?php echo $butchery['status']; ?>
                                        </span>
                                        <button class="text-gray-400 bg-inherit border-none hover:text-white hover:bg-gray-800 p-2 rounded transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 sm:p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Business Permit: <?php echo htmlspecialchars($butchery['permit'] ?? 'Standard'); ?>
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?php 
                                        echo $butchery['status'] === 'Active' ? 'bg-green-100 text-green-800' : 
                                            ($butchery['status'] === 'Blocked' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); 
                                    ?>">
                                        <?php echo $butchery['status']; ?>
                                    </span>
                                </div>

                                <?php if (!empty($butchery['location'])): ?>
                                <div class="flex items-center text-sm text-gray-400">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($butchery['location']); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($butchery['email'])): ?>
                                <div class="flex items-center text-sm text-gray-400">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($butchery['email']); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($butchery['phone'])): ?>
                                <div class="flex items-center text-sm text-gray-400">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($butchery['phone']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>                            
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <script>
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.card-hover');
                
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? 'block' : 'none';
                });
            });           
            
            </script>
        </div>
    </div>
    
</body>

</html>