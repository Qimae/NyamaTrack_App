<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Butcheries Management - NyamaTrack Admin</title>
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
                    <div class="text-2xl font-bold text-blue-400">5</div>
                    <p class="text-xs text-gray-500">+2 new this month</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Active Butcheries</h3>
                        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-400">4</div>
                    <p class="text-xs text-gray-500">80% active rate</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Revenue</h3>
                        <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-purple-400">KSh 195,000</div>
                    <p class="text-xs text-gray-500">Monthly recurring revenue</p>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 sm:p-6 shadow-xl card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-400">Total Employees</h3>
                        <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-yellow-400">33</div>
                    <p class="text-xs text-gray-500">Across all butcheries</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between mb-6">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search butcheries, owners, or locations..." class="pl-10 bg-black border border-gray-700 text-white rounded-lg px-3 py-2 w-full focus:border-red-500 focus:outline-none">
                </div>               
            </div>

            <!-- Butcheries Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-gray-900 border border-gray-800 rounded-lg shadow-xl card-hover overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold border-2 border-red-500/20">
                                    MN
                                </div>
                                <div>
                                    <h3 class="text-lg text-white font-semibold">Mama Nguo Butchery</h3>
                                    <p class="text-sm text-gray-400">Mary Wanjiku</p>
                                </div>
                            </div>
                            <button class="text-gray-400 bg-inherit border-none hover:text-white hover:bg-gray-800 p-2 rounded transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="plan-badge plan-premium">Premium</span>
                            <span class="plan-badge status-active">Active</span>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Nairobi, Eastlands
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                +254 712 345 678
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                mary@mamanguo.co.ke
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                                </svg>
                                Joined June 15, 2023
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-800">
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="text-lg font-semibold text-green-400">KSh 45,000</div>
                                    <div class="text-xs text-gray-500">Monthly Revenue</div>
                                </div>
                                <div>
                                    <div class="text-lg font-semibold text-blue-400">8</div>
                                    <div class="text-xs text-gray-500">Employees</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>              
            </div>
        </div>
    </div>
</body>

</html>