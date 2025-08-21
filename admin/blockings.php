<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}
?>

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
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="p-2 sm:p-3 rounded-lg bg-gradient-to-br from-red-600 to-orange-600">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Blocked Butcheries Management</h1>
                        <p class="text-sm sm:text-base text-gray-400">Manage all blocked butcheries.</p>
                    </div>
                </div>
            </div>
            <!-- Blocked Companies Section -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-8 shadow-xl">
                <h2 class="text-xl font-semibold text-white mb-6">Blocked Companies</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800">
                                <th class="text-left text-orange-400 font-semibold py-3">Name</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Blocked Date</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Reason</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="blockedUsersTableBody">
                            <!-- Blocked users will be loaded here via JavaScript -->
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">Loading blocked users...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    </div>
    <script src="js/blocking_script.js"></script>

</body>

</html>