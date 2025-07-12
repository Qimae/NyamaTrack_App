

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Account | NyamaTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&family=Noto+Serif:wght@400;700&display=swap" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<body class="bg-[#181111] font-['Noto_Sans'] text-white">
  <div class="flex flex-col min-h-screen justify-between overflow-x-hidden">
    <div>
      <!-- Header -->
      <div class="flex items-center p-4 pb-2 justify-between">
        <div class="text-white flex size-12 shrink-0 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
            <path d="M224,128a8,8,0,0,1-8,8H59.31l58.35,58.34a8,8,0,0,1-11.32,11.32l-72-72a8,8,0,0,1,0-11.32l72-72a8,8,0,0,1,11.32,11.32L59.31,120H216A8,8,0,0,1,224,128Z" />
          </svg>
        </div>
        <h2 class="text-lg font-bold text-center flex-1 pr-12">Nyamatrack.co.ke</h2>
      </div>

      <!-- Title -->
      <h2 class="text-[28px] font-bold px-4 pt-5 pb-3">Create an account</h2>

      <!-- Form -->
      <form id="registerForm" method="POST" class="max-w-[480px] mx-auto px-4">


        <input type="text" id="fullname" name="fullname" placeholder="Full Name" required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="text" id="business_name" name="business_name" placeholder="Business Name" required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="text" id="permit" name="permit" placeholder="Business Permit No." required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="text" id="location" name="location" placeholder="Business Location" required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="tel" id="phone" name="phone" placeholder="Phone Number" required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="email" id="email" name="email" placeholder="Email Address" required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="password" id="password" name="password" placeholder="Password" required
          class="mb-3 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <input type="password" id="confirm" name="confirm" placeholder="Confirm Password" required
          class="mb-4 w-full h-14 p-4 rounded-xl bg-[#382929] text-white placeholder-[#b89d9d]" autocomplete="off" />

        <button type="submit"
          class="w-full h-12 bg-[#e82626] rounded-full font-bold tracking-wide text-white hover:bg-red-600 transition-all duration-200">
          Register
        </button>
      </form>
    </div>

    <div class="text-sm text-[#b89d9d] text-center py-3 underline">
      Already have an account? Sign in
    </div>
  </div>

  <!-- 🔐 Frontend Security Script -->
  <script>
    document.getElementById("registerForm").addEventListener("submit", function (e) {
      e.preventDefault();

      const values = {
        fullname: document.getElementById("fullname").value.trim(),
        business_name: document.getElementById("business_name").value.trim(),
        permit: document.getElementById("permit").value.trim(),
        location: document.getElementById("location").value.trim(),
        phone: document.getElementById("phone").value.trim(),
        email: document.getElementById("email").value.trim(),
        password: document.getElementById("password").value,
        confirm: document.getElementById("confirm").value
      };

      // All checks passed, submit via AJAX
      fetch('api/registration_handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(values)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          window.location.href = "login.php";
        } else {
          // Show detailed error if available
          alert((data.error ? "Registration failed: " + data.error : "Registration failed.") +
                (data.details ? "\nDetails: " + data.details : ""));
        }
      })
      .catch(() => {
        alert("Registration failed. Please try again later.");
      });
    });
  </script>
</body>
</html>
