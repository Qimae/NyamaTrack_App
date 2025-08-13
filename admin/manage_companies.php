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
            }
        }
    </script>
    <style>
        body {
            background-color: #000000;
            color: white;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #000000;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: #000000;
            border-right: 1px solid #374151;
            z-index: 50;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: #000000;
        }
        
        .nav-item {
            transition: all 0.2s;
        }
        
        .nav-item:hover {
            background: #1f2937;
            color: white;
        }
        
        .nav-item.active {
            background: rgba(220, 38, 38, 0.2);
            color: #f87171;
            border-left: 4px solid #dc2626;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid;
        }
        
        .status-paid {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .status-pending {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
            border-color: rgba(234, 179, 8, 0.3);
        }
        
        .status-failed {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .status-processing {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-black text-white">
     Sidebar 
    <div class="sidebar">
         Header 
        <div class="border-b border-gray-800 p-4 bg-gray-950">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-red-600 to-orange-600 shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">NyamaTrack</h2>
                    <p class="text-xs text-orange-400 font-medium">Admin Dashboard</p>
                </div>
            </div>
        </div>
        
         Navigation 
        <div class="bg-black p-4">
            <div class="text-orange-400 text-xs uppercase tracking-wider font-semibold px-4 py-2 mb-4">
                🔪 Management Tools
            </div>
            <nav class="space-y-2">
                <a href="index.html" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Dashboard</div>
                        <div class="text-xs text-gray-500">Overview & stats</div>
                    </div>
                </a>
                
                <a href="payments.html" class="nav-item active flex items-center gap-3 p-3 rounded-lg text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Payment Processing</div>
                        <div class="text-xs text-gray-500">Manage payments & confirmations</div>
                    </div>
                </a>
                
                <a href="butcheries.html" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Butcheries</div>
                        <div class="text-xs text-gray-500">Manage registered butcheries</div>
                    </div>
                </a>
            </nav>
        </div>
        
         Footer 
        <div class="absolute bottom-0 left-0 right-0 border-t border-gray-800 p-4 bg-gray-950">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-red-600 flex items-center justify-center text-white font-bold">
                    AD
                </div>
                <div>
                    <div class="text-sm font-medium text-white">Admin User</div>
                    <div class="text-xs text-orange-400">admin@nyamatrack.co.ke</div>
                </div>
            </div>
        </div>
    </div>
      Main Content 
    <div class="main-content">
        <div class="p-6">
         <!-- Blocked Companies Section -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-8 shadow-xl">
                <h2 class="text-xl font-semibold text-white mb-6">Blocked Companies</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800">
                                <th class="text-left text-orange-400 font-semibold py-3">Company ID</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Name</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Blocked Date</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Reason</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample Blocked Company Row -->
                            <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                                <td class="py-4 font-medium text-white">BUT123</td>
                                <td class="py-4 text-gray-300">Sample Butchery Ltd</td>
                                <td class="py-4 text-gray-300">2024-01-15</td>
                                <td class="py-4 text-red-400">Payment Default</td>
                                <td class="py-4">
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Unblock
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>   
            <!-- Unblocking Request Section -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 shadow-xl">
                <h2 class="text-xl font-semibold text-white mb-6">Unblocking Requests</h2>
                <div class="grid gap-6">
                    <!-- Sample Unblock Request Card -->
                    <div class="border border-gray-800 rounded-lg p-4 hover:bg-gray-800/30">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-white mb-2">Butchery Name Ltd</h3>
                                <p class="text-sm text-gray-400">Request Date: 2024-01-16</p>
                                <p class="text-sm text-gray-400 mt-2">Reason for unblock request:</p>
                                <p class="text-sm text-gray-300 mt-1">Payment issues resolved. All outstanding amounts cleared.</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">
                                    Approve
                                </button>
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    </body>
</html>