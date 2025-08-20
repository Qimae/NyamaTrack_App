<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamaTrack Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gray: {
                            950: '#030712',
                        }
                    }
                }
            },
            corePlugins: {
                preflight: false,
            },
        }
    </script>
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Revenue</h3>
                        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-400">KSh 195,000</div>
                    <p class="text-xs text-gray-500">+12% from last month</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Active Butcheries</h3>
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-blue-400">4</div>
                    <p class="text-xs text-gray-500">80% active rate</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Pending Payments</h3>
                        <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-yellow-400">2</div>
                    <p class="text-xs text-gray-500">KSh 40,000 pending</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Employees</h3>
                        <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-purple-400">33</div>
                    <p class="text-xs text-gray-500">Across all butcheries</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl card-hover">
                    <div class="p-6 border-b border-gray-800">
                        <h3 class="text-white flex items-center gap-2 text-lg font-semibold">
                            <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Recent Butchery Payments
                        </h3>
                    </div>
                    <div class="p-6">                       
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-800">
                                        <th class="text-left text-orange-400 font-semibold py-3">Butchery Name</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Plan</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Amount</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Status</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                        <td class="py-4 font-medium text-white">Sample Butchery 1</td>
                                        <td class="py-4 text-gray-300">Premium</td>
                                        <td class="py-4 text-gray-300">Ksh 5,000</td>
                                        <td class="py-4"><span class="px-2 py-1 text-xs rounded-full bg-green-900 text-green-300">Active</span></td>                                        
                                    </tr>
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                        <td class="py-4 font-medium text-white">Sample Butchery 2</td>
                                        <td class="py-4 text-gray-300">Standard</td>
                                        <td class="py-4 text-gray-300">Ksh 3,000</td>
                                        <td class="py-4"><span class="px-2 py-1 text-xs rounded-full bg-yellow-900 text-yellow-300">Pending</span></td>                                        
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <a href="processing.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors no-underline">
                                View All Payments
                            </a>                            
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl card-hover">
                    <div class="p-6 border-b border-gray-800">
                        <h3 class="text-white flex items-center gap-2 text-lg font-semibold">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Recent Unblock requests
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto mb-4">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-800">
                                        <th class="text-left text-orange-400 font-semibold py-3">Butchery Name</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Full Name</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Reason</th>
                                        <th class="text-left text-orange-400 font-semibold py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                        <td class="py-4 font-medium text-white">Nyama Fresh</td>
                                        <td class="py-4 text-gray-300">John Doe</td>
                                        <td class="py-4 text-gray-300">Payment issue resolved</td>
                                        <td class="py-4"><span class="px-2 py-1 text-xs rounded-full bg-blue-900 text-blue-300">New</span></td>
                                    </tr>
                                    <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                        <td class="py-4 font-medium text-white">Meat Masters</td>
                                        <td class="py-4 text-gray-300">Jane Smith</td>
                                        <td class="py-4 text-gray-300">Account verification completed</td>
                                        <td class="py-4"><span class="px-2 py-1 text-xs rounded-full bg-yellow-900 text-yellow-300">In Review</span></td>
                                    </tr>
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