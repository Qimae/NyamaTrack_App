<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nyamatrack - Register | Premium Butchery Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="utils/registration.css">
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

    <!-- Main Register Container -->
    <div class="register-container w-full max-w-lg mx-auto p-4 relative z-10">
        <!-- Logo and Brand -->
        <div class="text-center mb-6">
            <div class="relative inline-block">
                <div
                    class="pulse-ring absolute inset-0 rounded-full bg-gradient-to-r from-gray-600 to-gray-500 opacity-20">
                </div>
                <div
                    class="relative bg-gradient-to-r from-gray-600 to-gray-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-cut text-white text-xl"></i>
                </div>
            </div>
            <h1 class="brand-text text-3xl font-bold mb-1">Nyamatrack</h1>
            <p class="text-gray-400 text-sm">Join the Premium Butchery Network</p>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps flex justify-center mb-6">
            <div class="flex items-center space-x-2">
                <div
                    class="progress-step active w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-gray-700 text-gray-300">
                    1</div>
                <div class="w-8 h-1 bg-gray-700 rounded"></div>
                <div
                    class="progress-step w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-gray-700 text-gray-300">
                    2</div>
            </div>
        </div>

        <!-- Register Form -->
        <div class="glass-card rounded-2xl p-6 shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-white mb-1">Create Your Account</h2>
                <p class="text-gray-400 text-sm">Start managing your butchery today</p>
            </div>

            <form id="registerForm" method="POST" class="flex-1 flex flex-col">
                <!-- Step 1: Personal Information -->
                <div id="step1" class="step-content flex-1 overflow-y-auto pr-2 space-y-4">
                    <!-- Full Name -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            <i class="fas fa-user mr-2 text-gray-400"></i>Full Name:
                        </label>
                        <input type="text" id="fullname" name="fullname"
                            class="input-glow w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                            placeholder="John Doe" required>
                    </div>

                    <!-- Email -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>Email Address:
                        </label>
                        <input type="email" id="email" name="email"
                            class="input-glow w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                            placeholder="johndoe@example.com" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pt-6">
                            <i class="fas fa-check text-green-500 hidden" id="emailCheck"></i>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>Phone Number:
                        </label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-600 bg-gray-700 text-gray-300 text-sm">+254</span>
                            <input type="tel" id="phone" name="phone"
                                class="input-glow flex-1 px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-r-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                                placeholder="712345678" required>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Security -->
                <div id="step2" class="step-content hidden flex-1 overflow-y-auto pr-2 space-y-4">
                    <!-- Password -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            <i class="fas fa-lock mr-2 text-gray-400"></i>Password:
                        </label>
                        <input type="password" id="password" name="password"
                            class="input-glow w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                            placeholder="Create a strong password" autocomplete="off" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pt-6">
                            <button type="button" onclick="togglePassword('password')"
                                class="text-gray-400 hover:text-white">
                                <i class="fas fa-eye" id="passwordToggle"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Password Strength -->
                    <div class="mt-2">
                        <div class="password-strength w-full bg-gray-700" id="passwordStrength"></div>
                        <p class="text-xs text-gray-400 mt-1" id="passwordStrengthText">Password strength: Weak</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            <i class="fas fa-lock mr-2 text-gray-400"></i>Confirm Password:
                        </label>
                        <input type="password" id="confirm" name="confirm"
                            class="input-glow w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-300"
                            placeholder="Confirm your password" autocomplete="off" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pt-6">
                            <i class="fas fa-check text-green-500 hidden" id="passwordMatch"></i>
                            <i class="fas fa-times text-red-500 hidden" id="passwordMismatch"></i>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox"
                                class="w-4 h-4 text-gray-500 bg-gray-800 border-gray-600 rounded focus:ring-gray-500 mt-1"
                                required>
                            <span class="ml-2 text-sm text-gray-300">
                                I agree to the <a href="#" class="text-gray-400 hover:text-white underline">Terms of
                                    Service</a> and <a href="#" class="text-gray-400 hover:text-white underline">Privacy
                                    Policy</a>
                            </span>
                        </label>

                        <label class="flex items-start">
                            <input type="checkbox"
                                class="w-4 h-4 text-gray-500 bg-gray-800 border-gray-600 rounded focus:ring-gray-500 mt-1">
                            <span class="ml-2 text-sm text-gray-300">
                                I want to receive marketing emails and product updates
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between pt-4 mt-auto">
                    <button type="button" id="prevBtn"
                        class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 hidden"
                        onclick="changeStep(-1)">
                        <i class="fas fa-arrow-left mr-2"></i>Previous
                    </button>
                    <div class="ml-auto space-x-2">
                        <button type="button" id="nextBtn"
                            class="px-6 py-2 bg-gradient-to-r from-gray-600 to-gray-500 text-white rounded-lg hover:from-gray-500 hover:to-gray-400 transition-all duration-300"
                            onclick="changeStep(1)">
                            Next<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit" id="submitBtn"
                            class="hidden px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg hover:from-blue-500 hover:to-blue-400 transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Create Account
                        </button>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-gray-400 text-sm">
                        Already have an account?
                        <a href="login.php" class="text-gray-300 hover:text-white font-semibold transition-colors">
                            Sign In
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
    <script src="js/registration_script.js"></script>
</body>

</html>