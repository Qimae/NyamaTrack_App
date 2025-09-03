let currentStep = 1;
const totalSteps = 2; // Reduced from 3 to 2 steps

// Initialize form state when the page loads
document.addEventListener('DOMContentLoaded', function() {
    updateProgress(1);
    updateButtons(1);
});

// Toggle Password Visibility
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(inputId + 'Toggle');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Change Step
function changeStep(direction) {
    const newStep = currentStep + direction;
    if (newStep < 1 || newStep > totalSteps) return;

    // Only validate when moving forward
    if (direction > 0) {
        const currentStepEl = document.getElementById(`step${currentStep}`);
        const requiredInputs = currentStepEl.querySelectorAll('[required]');
        let isValid = true;

        // Reset all error states first
        currentStepEl.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });

        // Check required fields in current step
        for (const input of requiredInputs) {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
                // Add shake animation to the input
                input.classList.add('animate-shake');
                setTimeout(() => {
                    input.classList.remove('animate-shake');
                }, 500);
            }
        }

        if (!isValid) {
            // Scroll to first invalid input
            const firstInvalid = currentStepEl.querySelector('.border-red-500');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
    }

    // Proceed to next step if validation passes
    document.getElementById(`step${currentStep}`).classList.add('hidden');
    document.getElementById(`step${newStep}`).classList.remove('hidden');

    // Update progress and buttons
    updateProgress(newStep);
    updateButtons(newStep);
    currentStep = newStep;
}

// Update Progress
function updateProgress(step) {
    const steps = document.querySelectorAll('.progress-step');

    steps.forEach((stepEl, index) => {
        stepEl.classList.remove('active', 'completed');

        if (index < step - 1) {
            stepEl.classList.add('completed');
        } else if (index === step - 1) {
            stepEl.classList.add('active');
        }
    });
}

// Update Buttons
function updateButtons(step) {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Always hide both buttons first
    nextBtn.classList.add('hidden');
    submitBtn.classList.add('hidden');
    
    if (step === 1) {
        prevBtn.classList.add('hidden');
        nextBtn.classList.remove('hidden');
    } else if (step === 2) {
        prevBtn.classList.remove('hidden');
        submitBtn.classList.remove('hidden');
    }
}

// Password Strength Checker
function checkPasswordStrength(password) {
    let strength = 0;
    let text = 'Weak';
    let className = 'strength-weak';

    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    if (strength >= 3) {
        text = 'Medium';
        className = 'strength-medium';
    }
    if (strength >= 4) {
        text = 'Strong';
        className = 'strength-strong';
    }

    return { strength, text, className };
}

// Email Validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Form Submission
document.getElementById("registerForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  
  const submitBtn = document.getElementById('submitBtn');
  const originalBtnText = submitBtn.innerHTML;
  
  // Show loading state
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...';
  submitBtn.disabled = true;

  try {
    const values = {
      fullname: document.getElementById("fullname").value.trim(),
      phone: document.getElementById("phone").value.trim(),
      email: document.getElementById("email").value.trim(),
      password: document.getElementById("password").value,
      confirm: document.getElementById("confirm").value
    };

    const response = await fetch('api/registration_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams(values)
    });

    const data = await response.json();

    if (data.success) {
      await Swal.fire({
        title: 'Success!',
        text: data.message || 'Your account has been created successfully!',
        icon: 'success',
        confirmButtonText: 'Continue to Login',
        confirmButtonColor: '#4a5568',
        background: '#1a1a1a',
        color: '#fff',
        allowOutsideClick: false
      });
      window.location.href = "login.php";
    } else {
      let errorMessage = data.error || 'Registration failed. Please try again.';
      if (data.details) {
        errorMessage += `\n\n${data.details}`;
      }
      
      await Swal.fire({
        title: 'Registration Failed',
        text: errorMessage,
        icon: 'error',
        confirmButtonText: 'Try Again',
        confirmButtonColor: '#e53e3e',
        background: '#1a1a1a',
        color: '#fff'
      });
      
      // Re-enable the submit button
      submitBtn.innerHTML = originalBtnText;
      submitBtn.disabled = false;
    }
  } catch (error) {
    console.error('Registration error:', error);
    await Swal.fire({
      title: 'Error',
      text: 'An unexpected error occurred. Please try again later.',
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#e53e3e',
      background: '#1a1a1a',
      color: '#fff'
    });
    
    // Re-enable the submit button
    submitBtn.innerHTML = originalBtnText;
    submitBtn.disabled = false;
  }
});

// Real-time validation
// Initialize progress steps
document.addEventListener('DOMContentLoaded', function () {
    // Set first step as active on page load
    updateProgress(1);
    
    // Rest of the initialization code
    // Email validation
    const emailInput = document.getElementById('email');
    const emailCheck = document.getElementById('emailCheck');

    emailInput.addEventListener('input', function () {
        if (validateEmail(this.value)) {
            emailCheck.classList.remove('hidden');
        } else {
            emailCheck.classList.add('hidden');
        }
    });

    // Password strength
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');

    passwordInput.addEventListener('input', function () {
        const result = checkPasswordStrength(this.value);
        strengthBar.className = `password-strength w-full ${result.className}`;
        strengthText.textContent = `Password strength: ${result.text}`;
    });

    // Password confirmation
    const confirmPasswordInput = document.getElementById('confirm');
    const passwordMatch = document.getElementById('passwordMatch');
    const passwordMismatch = document.getElementById('passwordMismatch');

    confirmPasswordInput.addEventListener('input', function () {
        if (this.value === passwordInput.value && this.value !== '') {
            passwordMatch.classList.remove('hidden');
            passwordMismatch.classList.add('hidden');
        } else if (this.value !== '') {
            passwordMatch.classList.add('hidden');
            passwordMismatch.classList.remove('hidden');
        } else {
            passwordMatch.classList.add('hidden');
            passwordMismatch.classList.add('hidden');
        }
    });

    // Add floating animation to icons
    const icons = document.querySelectorAll('.floating-icon');
    icons.forEach((icon, index) => {
        icon.style.animationDelay = `${index * 0.5}s`;
    });
});