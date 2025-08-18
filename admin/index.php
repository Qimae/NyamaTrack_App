<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nyamatrack - Admin Login | Premium Butchery Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="utils/registration.css">
    <link rel="manifest" href="manifest.json" />
    <meta name="theme-color" content="#007bff" />
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center relative overflow-hidden py-8">
    <!-- Floating Background Elements -->
    <div class="meat-pattern"></div>
    <!-- Floating Butchery Icons -->
    <div class="floating-icon text-gray-500 text-4xl">
        <i class="fas fa-store"></i>
    </div>
    <div class="floating-icon text-gray-400 text-3xl">
        <i class="fas fa-scale-balanced"></i>
    </div>
    <div class="floating-icon text-gray-600 text-5xl">
        <i class="fas fa-receipt"></i>
    </div>
    <div class="floating-icon text-gray-500 text-3xl">
        <i class="fas fa-chart-line"></i>
    </div>
    <div class="floating-icon text-gray-400 text-4xl">
        <i class="fas fa-users"></i>
    </div>
    <div class="floating-icon text-gray-600 text-3xl">
        <i class="fas fa-money-bill-wave"></i>
    </div>
    <!-- Main Login Container -->
    <div class="login-container w-full max-w-md mx-auto p-4 relative z-10">
        <!-- Logo and Brand -->
        <div class="text-center mb-8">
            <div class="relative inline-block">
                <div
                    class="pulse-ring absolute inset-0 rounded-full bg-gradient-to-r from-gray-600 to-gray-500 opacity-20">
                </div>
                <div
                    class="relative bg-gradient-to-r from-gray-600 to-gray-500 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cut text-white text-2xl"></i>
                </div>
            </div>
            <h1 class="brand-text text-4xl font-bold mb-2">Nyamatrack</h1>
            <p class="text-gray-400 text-sm">Admin Login - Premium Butchery Management System</p>
        </div>

        <!-- Login Form -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Welcome</h2>
                <p class="text-gray-400 text-sm">Sign in to your account</p>
            </div>

            <form id="loginForm" class="space-y-6">
                <!-- Email Input -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>Email Address
                    </label>
                    <input type="email" id="email" name="email"
                        class="input-glow w-full px-3 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                        placeholder="Enter your email" required autocomplete="email">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pt-7">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>Password:
                    </label>
                    <input type="password" id="password" name="password"
                        class="input-glow w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                        placeholder="Enter your password" autocomplete="off" required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pt-7">
                        <button type="button" onclick="togglePassword('password')" class="text-gray-400 hover:text-white">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox"
                            class="w-4 h-4 text-gray-500 bg-gray-800 border-gray-600 rounded focus:ring-gray-500">
                        <span class="ml-2 text-sm text-gray-400">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-gray-400 hover:text-gray-300 transition-colors">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit" id="loginButton"
                    class="glow-button w-full py-3 px-4 bg-gradient-to-r from-gray-600 to-gray-700 border border-gray-600 rounded-lg text-white font-semibold text-lg transition-all duration-300 hover:scale-[1.02] hover:from-gray-500 hover:to-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    <span class="button-text">Sign In</span>
                    <span class="loading-spinner hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Signing in...
                    </span>
                </button>
                <!-- Register Link -->
                <div class="text-center mt-4">
                    <p class="text-gray-400 text-sm">
                        Don't have an account?
                        <a href="#" id="showVerificationModal" class="text-gray-300 hover:text-white font-semibold transition-colors">
                            Create Account
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-400 text-xs">
                © 2025 Nyamatrack. Secure butchery management for Kenya.
            </p>
            <div class="flex justify-center space-x-4 mt-2">
                <a href="#" class="text-gray-400 hover:text-gray-300 text-xs">Privacy</a>
                <a href="#" class="text-gray-400 hover:text-gray-300 text-xs">Terms</a>
                <a href="#" class="text-gray-400 hover:text-gray-300 text-xs">Support</a>
            </div>
        </div>
    </div>
    <style>
        .loading-spinner {
            display: none;
        }
        .loading .button-text {
            display: none;
        }
        .loading .loading-spinner {
            display: inline-block;
        }
        #verificationModal {
            transition: opacity 0.3s ease-in-out;
        }
        .modal-enter {
            opacity: 0;
        }
        .modal-enter-active {
            opacity: 1;
        }
        .modal-exit {
            opacity: 1;
        }
        .modal-exit-active {
            opacity: 0;
        }
    </style>
    <script src="js/login_script.js"></script>

    <!-- Account Code Verification Modal -->
    <div id="verificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-white">Verify Account Code</h3>
                <button id="closeModal" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="verificationForm" class="space-y-4">
                <div>
                    <label for="accountCode" class="block text-sm font-medium text-gray-300 mb-1">
                        Enter your account code
                    </label>
                    <input type="text" id="accountCode" name="accountCode" 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gray-500"
                        placeholder="XXXX-XXXX" required>
                    <p class="mt-1 text-xs text-gray-400">Please enter the account code provided by your administrator.</p>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" id="cancelVerification" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                        Verify Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>