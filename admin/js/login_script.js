// Toggle password visibility
function togglePassword(inputId) {
  const passwordInput = document.getElementById(inputId);
  const toggleIcon = document.getElementById('toggleIcon');

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

document.getElementById("loginForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;

  // Client-side validation
  if (!email || !password) {
    showError('Please fill in all fields');
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    Swal.fire({
      title: 'Login Failed',
      text: 'Please enter a valid email address',
      icon: 'error',
      confirmButtonText: 'Try Again',
      confirmButtonColor: '#e53e3e',
      background: '#1a1a1a',
      color: '#fff',
      allowOutsideClick: false
    });
    return;
  }

  const submitBtn = this.querySelector('button[type="submit"]');
  const originalBtnText = submitBtn.innerHTML;

  // Show loading state
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';

  try {
    // Submit form via AJAX
    const response = await fetch('./api/login_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        email,
        password
      })
    });

    const responseText = await response.text();
    let data;
    
    try {
      data = JSON.parse(responseText);
    } catch (e) {
      console.error('Failed to parse JSON:', responseText);
      throw new Error(`Server returned invalid JSON. Status: ${response.status} ${response.statusText}. Response: ${responseText.substring(0, 200)}`);
    }

    if (data && data.success) {
      // Show success message and redirect
      Swal.fire({
        title: 'Login Successful!',
        text: 'Redirecting to dashboard...',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        background: '#1a1a1a',
        color: '#fff',
        didOpen: () => {
          Swal.showLoading();
        }
      }).then(() => {
        window.location.href = 'dashboard.php';
      });
    } else {
      // Show error message on the login form
      Swal.fire({
        title: 'Login Failed',
        text: data.error || 'Login failed. Please try again.',
        icon: 'error',
        confirmButtonText: 'Try Again',
        confirmButtonColor: '#e53e3e',
        background: '#1a1a1a',
        color: '#fff',
        allowOutsideClick: false
      });
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  } catch (error) {
    console.error('Login error:', error);
    let errorMessage = 'An error occurred. Please try again.';
    
    if (error instanceof SyntaxError) {
      errorMessage = 'Server returned invalid response. Please check console for details.';
    } else if (error.message) {
      errorMessage = error.message;
    }
    
    Swal.fire({
      title: 'Login Failed',
      text: errorMessage,
      icon: 'error',
      confirmButtonText: 'Try Again',
      confirmButtonColor: '#e53e3e',
      background: '#1a1a1a',
      color: '#fff',
      width: '80%',
      customClass: {
        content: 'text-left'
      }
    }).then(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    });
  }
});

// Helper function to show error messages
function showError(message) {
  Swal.fire({
    title: 'Login Failed',
    text: message,
    icon: 'error',
    confirmButtonText: 'Try Again',
    confirmButtonColor: '#e53e3e',
    background: '#1a1a1a',
    color: '#fff',
    allowOutsideClick: false
  });
}

// Toggle modal visibility
function toggleModal(show = true) {
  const modal = document.getElementById('verificationModal');
  if (show) {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  } else {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  }
}

// Account code validation
async function validateAccountCode(code) {
  try {
    const response = await fetch('api/verify_code.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `code=${encodeURIComponent(code)}`
    });
    
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Validation error:', error);
    return { success: false, message: 'An error occurred. Please try again.' };
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // Add floating animation to icons
  const icons = document.querySelectorAll('.floating-icon');
  icons.forEach((icon, index) => {
    icon.style.animationDelay = `${index * 0.5}s`;
  });

  // Verification modal elements
  const showModalBtn = document.getElementById('showVerificationModal');
  const closeModalBtn = document.getElementById('closeModal');
  const cancelBtn = document.getElementById('cancelVerification');
  const verificationForm = document.getElementById('verificationForm');
  const accountCodeInput = document.getElementById('accountCode');

  // Show modal when clicking Create Account
  showModalBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    toggleModal(true);
    accountCodeInput?.focus();
  });

  // Close modal when clicking X or Cancel
  [closeModalBtn, cancelBtn].forEach(btn => {
    btn?.addEventListener('click', () => toggleModal(false));
  });

  // Close modal when clicking outside the modal content
  document.getElementById('verificationModal')?.addEventListener('click', (e) => {
    if (e.target === document.getElementById('verificationModal')) {
      toggleModal(false);
    }
  });

  // Handle form submission
  verificationForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const code = accountCodeInput?.value.trim();
    
    if (!code) {
      Swal.fire({
        title: 'Error',
        text: 'Please enter an account code',
        icon: 'error',
        confirmButtonColor: '#e53e3e',
        background: '#1a1a1a',
        color: '#fff'
      });
      return;
    }

    const submitBtn = verificationForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';

    try {
      const result = await validateAccountCode(code);
      
      if (result.success) {
        // Store the verified code in sessionStorage temporarily
        sessionStorage.setItem('verifiedCode', code);
        // Redirect to registration page with the code
        window.location.href = `registration.php?code=${encodeURIComponent(code)}`;
      } else {
        Swal.fire({
          title: 'Invalid Code',
          text: result.message || 'The account code is invalid or has expired.',
          icon: 'error',
          confirmButtonColor: '#e53e3e',
          background: '#1a1a1a',
          color: '#fff'
        });
      }
    } catch (error) {
      console.error('Verification error:', error);
      Swal.fire({
        title: 'Error',
        text: 'An error occurred while verifying the code. Please try again.',
        icon: 'error',
        confirmButtonColor: '#e53e3e',
        background: '#1a1a1a',
        color: '#fff'
      });
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
});
