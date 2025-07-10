<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login & Lupa Password</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
  />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap");
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Poppins", sans-serif;
    }

    body {
      background: linear-gradient(to right, #e2e2e2, #c9d6ff);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 1rem;
    }

    .container {
      background-color: #fff;
      border-radius: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
      position: relative;
      overflow: hidden;
      width: 768px;
      max-width: 100%;
      min-height: 480px;
    }

    form {
      background-color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 0 40px;
      height: 100%;
    }

    input {
      background-color: #eee;
      border: none;
      margin: 8px 0;
      padding: 10px 15px;
      font-size: 13px;
      border-radius: 8px;
      width: 100%;
      outline: none;
    }

    button {
      background-color: #512da8;
      color: #fff;
      font-size: 12px;
      padding: 10px 45px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      text-transform: uppercase;
      margin-top: 10px;
      cursor: pointer;
    }

    a {
      color: #333;
      font-size: 13px;
      text-decoration: none;
      margin: 15px 0 10px;
      cursor: pointer;
    }

    .form-container {
      position: absolute;
      top: 0;
      height: 100%;
      transition: all 0.6s ease-in-out;
    }

    .login-form {
      left: 0;
      width: 50%;
      z-index: 2;
    }

    .forgot-form {
      left: 100%;
      width: 50%;
      opacity: 0;
      z-index: 1;
    }

    .container.active .login-form {
      transform: translateX(-100%);
    }

    .container.active .forgot-form {
      transform: translateX(-100%);
      opacity: 1;
      z-index: 5;
      animation: move 0.6s;
    }

    @keyframes move {
      0%,
      49.99% {
        opacity: 0;
        z-index: 1;
      }
      50%,
      100% {
        opacity: 1;
        z-index: 5;
      }
    }

    .toggle-container {
      position: absolute;
      top: 0;
      left: 50%;
      width: 50%;
      height: 100%;
      overflow: hidden;
      transition: all 0.6s ease-in-out;
      border-radius: 150px 0 0 100px;
      z-index: 1000;
    }

    .container.active .toggle-container {
      transform: translateX(-100%);
      border-radius: 0 150px 100px 0;
    }

    .toggle {
      background: linear-gradient(to right, #5c6bc0, #512da8);
      color: #fff;
      position: relative;
      left: -100%;
      height: 100%;
      width: 200%;
      transform: translateX(0);
      transition: all 0.6s ease-in-out;
    }

    .container.active .toggle {
      transform: translateX(50%);
    }

    .toggle-panel {
      position: absolute;
      width: 50%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 0 30px;
      text-align: center;
      top: 0;
    }

    .toggle-left {
      transform: translateX(-200%);
    }

    .container.active .toggle-left {
      transform: translateX(0);
    }

    .toggle-right {
      right: 0;
      transform: translateX(0);
    }

    .container.active .toggle-right {
      transform: translateX(200%);
    }

    .text-danger {
      color: red;
      font-size: 12px;
      margin-top: 2px;
    }

    .text-success {
      color: green;
      font-size: 13px;
      margin-top: 8px;
    }

    .btn-primary {
      background-color: #6c63ff;
      color: white;
      padding: 8px 16px;
      text-decoration: none;
      border-radius: 6px;
      display: inline-block;
      margin-top: 20px;
      cursor: pointer;
    }

    /* ===== MODIFIKASI UNTUK MOBILE ===== */
    @media (max-width: 768px) {
      .container {
        width: 100%;
        min-height: auto;
        border-radius: 20px;
      }

      /* Sembunyikan toggle container di mobile */
      .toggle-container {
        display: none !important;
      }

      /* Nonaktifkan positioning absolute dan animasi geser */
      .form-container {
        position: relative !important;
        width: 100% !important;
        left: 0 !important;
        opacity: 1 !important;
        transition: none !important;
        height: auto !important;
        z-index: auto !important;
        display: none; /* sembunyikan default */
      }

      /* Tampilkan hanya form yang aktif */
      .form-container.active {
        display: block;
      }
    }
  </style>
</head>
<body>
  <div class="container" id="container">
    <!-- Login Form -->
    <div class="form-container login-form">
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width:100px; margin-bottom: 20px;" />
        <input
          type="email"
          name="email"
          placeholder="Email"
          value="{{ old('email') }}"
          required
        />
        @error("email")
        <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="password" name="password" placeholder="Password" required />
        @error("password")
        <span class="text-danger">{{ $message }}</span>
        @enderror
        @if (session("status"))
        <div class="text-success">{{ session("status") }}</div>
        @endif

        @if (session("new_password"))
        <div class="text-success">
          Password akun Anda telah direset menjadi:
          <strong>{{ session("new_password") }}</strong>
        </div>
        @endif

        @if (session("password_changed"))
        <div class="text-success">Password Anda telah berhasil diubah!</div>
        @endif

        <a href="#" id="showForgot">Lupa Password?</a>
        <button type="submit">Login</button>

        <a href="{{ url('/') }}" class="btn-primary">Kembali ke Beranda</a>
      </form>
    </div>

    <!-- Forgot Password Form -->
    <div class="form-container forgot-form">
      <form method="POST" action="{{ route('custom.password.reset') }}">
        @csrf
        <h1>Reset Password</h1>

        <input type="email" name="email" placeholder="Email" required />
        @error("email")
        <span class="text-danger">{{ $message }}</span>
        @enderror

        <input type="date" name="tanggal_lahir" placeholder="Tanggal Lahir" required />
        @error("tanggal_lahir")
        <span class="text-danger">{{ $message }}</span>
        @enderror

        @if (session("status"))
        <div class="text-success">{{ session("status") }}</div>
        @endif

        @if (session("new_password"))
        <div class="text-success">
          Password akun Anda telah direset menjadi:
          <strong>{{ session("new_password") }}</strong>
        </div>
        @endif

        @if (session("password_changed"))
        <div class="text-success">Password Anda telah berhasil diubah!</div>
        @endif

        <button type="submit">Reset Password</button>
        <a href="#" id="showLogin">Kembali ke Login</a>
      </form>
    </div>

    <!-- Toggle -->
    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-left">
          <h1>Reset Password</h1>
          <p>
            Masukkan email & tanggal lahir.
          </p>
        </div>
        <div class="toggle-panel toggle-right">
          <h1>Selamat Datang!</h1>
          <p>Silakan login dengan akun yang telah terdaftar</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const container = document.getElementById("container");
    const showForgot = document.getElementById("showForgot");
    const showLogin = document.getElementById("showLogin");

    const loginForm = document.querySelector(".login-form");
    const forgotForm = document.querySelector(".forgot-form");

    function isMobile() {
      return window.innerWidth <= 768;
    }

    if (showForgot) {
      showForgot.addEventListener("click", (e) => {
        e.preventDefault();
        if (isMobile()) {
          // Di mobile toggle display block/none
          loginForm.classList.remove("active");
          forgotForm.classList.add("active");
        } else {
          // Desktop pakai efek geser
          container.classList.add("active");
        }
      });
    }

    if (showLogin) {
      showLogin.addEventListener("click", (e) => {
        e.preventDefault();
        if (isMobile()) {
          forgotForm.classList.remove("active");
          loginForm.classList.add("active");
        } else {
          container.classList.remove("active");
        }
      });
    }

    // Saat load halaman, set default form untuk mobile
    window.addEventListener("DOMContentLoaded", () => {
      if (isMobile()) {
        loginForm.classList.add("active");
        forgotForm.classList.remove("active");
      }
    });

    // Agar perubahan ukuran window mereset UI mobile/desktop
    // Hindari auto-reset saat resize, karena keyboard HP bisa trigger resize
let lastForm = "login"; // default form yang aktif

if (showForgot) {
  showForgot.addEventListener("click", (e) => {
    e.preventDefault();
    lastForm = "forgot";
    if (isMobile()) {
      loginForm.classList.remove("active");
      forgotForm.classList.add("active");
    } else {
      container.classList.add("active");
    }
  });
}

if (showLogin) {
  showLogin.addEventListener("click", (e) => {
    e.preventDefault();
    lastForm = "login";
    if (isMobile()) {
      forgotForm.classList.remove("active");
      loginForm.classList.add("active");
    } else {
      container.classList.remove("active");
    }
  });
}

// Saat resize hanya ubah jika berpindah device, tapi tidak reset form
window.addEventListener("resize", () => {
  if (isMobile()) {
    container.classList.remove("active");
    loginForm.classList.remove("active");
    forgotForm.classList.remove("active");

    if (lastForm === "login") {
      loginForm.classList.add("active");
    } else {
      forgotForm.classList.add("active");
    }
  } else {
    loginForm.classList.remove("active");
    forgotForm.classList.remove("active");
    container.classList.remove("active");
  }
});

  </script>
</body>
</html>
