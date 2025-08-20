<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - NyamaTrack Admin</title>
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
        <div class="p-4 sm:p-6">
            <!-- Page Header -->
            <div class="relative mb-6 sm:mb-8 p-4 sm:p-6 bg-gradient-to-r from-gray-900 to-black rounded-xl border border-gray-800">
                <div class="absolute top-4 right-4 opacity-10">
                    <svg width="120" height="120" viewBox="0 0 120 120" class="text-red-500">
                        <path d="M20 100 L40 20 L45 20 L25 100 Z" fill="currentColor"/>
                        <path d="M35 15 L50 15 L50 25 L35 25 Z" fill="currentColor"/>
                        <circle cx="42.5" cy="20" r="2" fill="white"/>
                    </svg>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="p-2 sm:p-3 rounded-lg bg-gradient-to-br from-red-600 to-orange-600">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Payment Processing</h1>
                        <p class="text-sm sm:text-base text-gray-400">🥩 Manage payments, confirmations and processing status</p>
                    </div>
                </div>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Revenue</h3>
                        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-400">KSh 195,000</div>
                    <p class="text-xs text-gray-500">+12% from last month</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Paid Amount</h3>
                        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-400">KSh 135,000</div>
                    <p class="text-xs text-gray-500">3 transactions</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Pending Amount</h3>
                        <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-yellow-400">KSh 40,000</div>
                    <p class="text-xs text-gray-500">2 transactions</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Success Rate</h3>
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-blue-400">60%</div>
                    <p class="text-xs text-gray-500">Payment success rate</p>
                </div>
            </div>
            <!-- Search and Filter Section -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 mb-6 shadow-xl">
                <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between w-full">
                    <div class="w-full sm:flex-1 sm:mr-4">
                        <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
                            <div class="relative flex-1 min-w-0">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" placeholder="Search butcheries or references..." class="pl-10 pr-3 py-2 bg-black border border-gray-700 text-white rounded-lg focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none">
                            </div>
                            <div class="w-full sm:w-48">
                                <select class="w-full bg-black border border-gray-700 text-white rounded-lg px-3 py-2 focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none">
                                    <option value="all">All Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="w-full sm:w-auto">
                        <button class="w-full sm:w-auto border-none bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-800">
                    <h3 class="text-white flex items-center gap-2 text-lg font-semibold">
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        🔪 Payment Transactions
                    </h3>
                </div>
                <div class="p-0 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-800">
                                    <th class="text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Payment ID</th>
                                    <th class="hidden sm:table-cell text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Butchery</th>
                                    <th class="text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Amount</th>
                                    <th class="text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Status</th>
                                    <th class="hidden md:table-cell text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Date</th>
                                    <th class="hidden lg:table-cell text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Method</th>
                                    <th class="hidden xl:table-cell text-left text-orange-400 font-semibold py-3 px-4 sm:px-6">Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                    <td class="py-4 px-4 sm:px-6 font-medium text-white">PAY-001</td>
                                    <td class="hidden sm:table-cell py-4 px-4 sm:px-6 text-gray-300">Mama Nguo Butchery</td>
                                    <td class="py-4 px-4 sm:px-6 text-green-400 font-semibold">KSh 45,000</td>
                                    <td class="py-4 px-4 sm:px-6">
                                        <span class="status-badge status-paid">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Paid
                                        </span>
                                    </td>
                                    <td class="hidden md:table-cell py-4 px-4 sm:px-6 text-gray-300">2024-01-15</td>
                                    <td class="hidden lg:table-cell py-4 px-4 sm:px-6 text-gray-300">M-Pesa</td>
                                    <td class="hidden xl:table-cell py-4 px-4 sm:px-6 text-gray-400 font-mono text-sm">MPX123456789</td>
                                </tr>
                                <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                    <td class="py-4 px-4 sm:px-6 font-medium text-white">PAY-002</td>
                                    <td class="hidden sm:table-cell py-4 px-4 sm:px-6 text-gray-300">Kariokor Fresh Meats</td>
                                    <td class="py-4 px-4 sm:px-6 text-green-400 font-semibold">KSh 25,000</td>
                                    <td class="py-4 px-4 sm:px-6">
                                        <span class="status-badge status-pending">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending
                                        </span>
                                    </td>
                                    <td class="hidden md:table-cell py-4 px-4 sm:px-6 text-gray-300">2024-01-14</td>
                                    <td class="hidden lg:table-cell py-4 px-4 sm:px-6 text-gray-300">Bank Transfer</td>
                                    <td class="hidden xl:table-cell py-4 px-4 sm:px-6 text-gray-400 font-mono text-sm">BT987654321</td>
                                </tr>                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>