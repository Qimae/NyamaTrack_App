// Toggle Password Visibility
function togglePassword(inputId) {
  const passwordInput = document.getElementById(inputId);
  const toggleIcon = document.getElementById(inputId + 'Toggle');

  if (!passwordInput || !toggleIcon) return;

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
  const business_name = document.getElementById("business_name").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;

  // Simple client-side validation
  if (!business_name || !email || !password) {
    Swal.fire({
      title: 'Login Failed',
      text: 'Please fill in all fields',
      icon: 'error',
      confirmButtonText: 'Try Again',
      confirmButtonColor: '#e53e3e',
      background: '#1a1a1a',
      color: '#fff',
      allowOutsideClick: false
    });
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
    const response = await fetch('api/login_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        business_name,
        email,
        password
      })
    });

    const data = await response.json();

    if (data.success) {
      // Show success message
      Swal.fire({
        title: 'Login Successful!',
        text: data.message || 'Redirecting to dashboard...',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        background: '#1a1a1a',
        color: '#fff',
        didOpen: () => {
          Swal.showLoading();
        }
      });
      // Redirect to dashboard on success
      window.location.href = 'dashboard.php';
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
    Swal.fire({
      title: 'Login Failed',
      text: 'An error occurred. Please try again later.',
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
});
document.addEventListener('DOMContentLoaded', function () {

  // Add floating animation to icons
  const icons = document.querySelectorAll('.floating-icon');
  icons.forEach((icon, index) => {
    icon.style.animationDelay = `${index * 0.5}s`;
  });
});
