<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- CSRF Token Meta Tag Placeholder -->
    <meta name="csrf-token" content="YOUR_CSRF_TOKEN_HERE" />

    <title>NyamaTrack Login | Butchery Accounting</title>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Serif:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900"
    />
    <link rel="icon" type="image/x-icon" href="favicon.ico" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  </head>

  <body class="bg-[#171212] text-white" style='font-family: "Noto Serif", "Noto Sans", sans-serif;'>
    <div class="relative flex min-h-screen flex-col justify-between overflow-x-hidden">
      <!-- Top Image -->
      <div class="@container">
        <div class="@[480px]:px-4 @[480px]:py-3">
          <div
            class="w-full bg-center bg-no-repeat bg-cover flex flex-col justify-end overflow-hidden bg-[#171212] @[480px]:rounded-xl min-h-80"
            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCbj00R9cVZIR98oAGFJeB-53b772Lor1TZYfNf_qdqCGN6OM9gqg2rlkFIQUSU7SJsUltBP6HTT7t6m2ITq3qsgDxTVodJxk3au5CpUt1tmT46IppYDAcvs7YEITludIudOci0FT2GfiJJZyAhxqrnbg1Dhuj13sjsFL4LjnBvUzHn2Xcd0MUoFQUzeCKMGD8De7lRjREL18Gu7egPzoqU_Kex47UHKg0XcolegTzYl1L79_pM8jC0zFETc_b0AsbbmEmnZPigKA8");'
          ></div>
        </div>
      </div>

      <!-- Form Heading -->
      <h2 class="text-white text-center text-[28px] font-bold leading-tight px-4 pb-3 pt-5">
        Welcome Back
      </h2>

      <!-- Login Form -->
      <form
        id="loginForm"
        autocomplete="off"
        class="max-w-[480px] w-full mx-auto px-4 space-y-4"
      >
        <!-- Business Name Field -->
        <label class="flex flex-col" for="business_name">
          <input
            id="business_name"
            name="business_name"
            type="text"
            required
            aria-label="Business Name"
            placeholder="Business Name"
            autocomplete="off"
            class="form-input w-full rounded-xl bg-[#362b2b] text-white h-14 p-4 placeholder:text-[#b4a2a2] text-base"
          />
        </label>

        <!-- Email Field -->
        <label class="flex flex-col" for="email">
          <input
            id="email"
            name="email"
            type="email"
            required
            aria-label="Email address"
            placeholder="Email"
            autocomplete="off"
            class="form-input w-full rounded-xl bg-[#362b2b] text-white h-14 p-4 placeholder:text-[#b4a2a2] text-base"
          />
        </label>

        <!-- Password Field -->
        <label class="flex flex-col" for="password">
          <input
            id="password"
            name="password"
            type="password"
            required
            minlength="8"
            aria-label="Password"
            placeholder="Password"
            autocomplete="off"
            class="form-input w-full rounded-xl bg-[#362b2b] text-white h-14 p-4 placeholder:text-[#b4a2a2] text-base"
          />
        </label>

        <!-- Forgot Password -->
        <p class="text-[#b4a2a2] text-sm text-center underline cursor-pointer hover:text-white transition">
          Forgot Password?
        </p>

        <!-- Login Button -->
        <button
          type="submit"
          class="w-full h-12 rounded-full bg-[#e8b4b4] text-[#171212] text-base font-bold tracking-[0.015em] transition hover:bg-[#f5c6c6]"
        >
          Login
        </button>

        <!-- Sign Up -->
        <p class="text-[#b4a2a2] text-sm text-center underline">
          Don't have an account? <a href="registration.php" class="hover:text-white transition">Sign Up</a>
        </p>
      </form>

        <!-- Ajax Script -->
      
      <script>
        document.getElementById("loginForm").addEventListener("submit", function(e) {
          e.preventDefault();
          const business_name = document.getElementById("business_name").value.trim();
          const email = document.getElementById("email").value.trim();
          const password = document.getElementById("password").value;
          if (!business_name || !email || !password) {
            alert("Business name, email and password are required.");
            return;
          }
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            return;
          }
          fetch('api/login_handler.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ business_name, email, password })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.message);
              window.location.href = "dashboard.php";
            } else {
              alert(data.error || "Login failed.");
            }
          })
          .catch(() => {
            alert("Login failed. Please try again later.");
          });
        });
      </script>
