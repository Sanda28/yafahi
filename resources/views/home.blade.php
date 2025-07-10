<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <title>SMP-SMK YAFAHI</title>
  <link rel="shortcut icon" href="/assets/icon/favicon.ico" type="image/x-icon" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css"/>
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: "Poppins", sans-serif;
      scroll-behavior: smooth;
    }
    html, body {
      height: 100%;
      background-color: #f7f7f7;
      padding-top: 100px;
    }
    .container {
      width: 85%; margin: 0 auto;
    }
    @media (max-width: 992px) {
      .container { width: 90%; }
    }

    .navbar {
      position: fixed; top: 0; left: 0; width: 100%;
      padding: 20px 0; background-color: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      z-index: 1000;
    }
    .navbar-box {
      display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;
    }
    .navbar-box .logo h1 {
      color: #000; font-weight: bold;
    }
    .menu {
      display: flex; gap: 30px; list-style: none;
    }
    .menu li a {
      color: black; text-decoration: none; font-weight: 600;
      transition: color 0.3s;
    }
    .menu li a:hover {
      color: #4b0082;
    }
    .hamburger {
      display: none; flex-direction: column; gap: 5px;
      cursor: pointer;
    }
    .hamburger span {
      width: 25px; height: 3px; background-color: #333; border-radius: 5px;
    }
    @media (max-width: 768px) {
      .menu {
        display: none; flex-direction: column;
        width: 100%; background-color: #fff;
        padding: 10px 0;
      }
      .menu.active { display: flex; }
      .hamburger { display: flex; }
    }

    .hero-box {
      background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/assets/images/Background1.jpg');
      background-size: cover; background-position: center;
      padding: 120px 30px; border-radius: 25px;
      color: rgb(255, 217, 0); text-align: center;
    }
    .hero-box h1 {
      font-size: 3rem; margin-bottom: 20px;
    }
    .hero-box p {
      font-size: 1.2rem; max-width: 600px;
      margin: 0 auto 30px; opacity: 0.9;
    }
    .hero-box a {
      background-color: rgb(41, 5, 172); color: #fff;
      padding: 12px 30px; border-radius: 50px;
      font-weight: 600; text-decoration: none;
      transition: background-color 0.3s;
    }
    .hero-box a:hover {
      background-color: #6200ff;
    }

    .tentang-box {
      display: flex; flex-wrap: wrap; gap: 30px;
      align-items: center; justify-content: center;
      padding: 60px 0;
    }
    .tentang-box .box {
      flex: 1 1 100%;
      text-align: center;
    }

    .info {
      display: flex; justify-content: space-around;
      flex-wrap: wrap; gap: 30px; margin-top: 40px;
    }
    .info .box h2 {
      font-size: 1.5rem; color: rgb(25, 3, 223);
    }
    .info .box h2 span {
      font-size: 2.5rem; display: block;
    }

    .footer-box {
      display: flex; flex-wrap: wrap; gap: 30px;
      align-items: flex-start; justify-content: center;
      padding: 60px 0;
    }
    .footer-box .box {
      flex: 1 1 45%; min-width: 300px;
    }
    .footer-box .box h2 {
      margin-bottom: 10px;
    }
    .footer-box .box p, .footer-box .box a {
      color: #333; display: block; margin-bottom: 8px; text-decoration: none;
    }
    .social a {
      margin-right: 10px; font-size: 20px; color: rgb(5, 8, 199);
    }

    .copyright {
      text-align: center; padding: 20px 0;
      background-color: #eee; font-size: 14px;
    }

    .scroll-up {
      position: fixed; bottom: 30px; right: 30px;
      background-color: rgb(47, 2, 206); color: white;
      padding: 10px 12px; border-radius: 50%;
      text-decoration: none; font-size: 18px;
      display: none; z-index: 1000;
    }

    @media (max-width: 576px) {
      .hero-box h1 { font-size: 2rem; }
      .hero-box p { font-size: 1rem; }
      .tentang-box .box, .footer-box .box {
        flex: 1 1 100%;
        text-align: center;
      }
      .scroll-up { padding: 8px 10px; font-size: 16px; }
    }
  </style>
</head>
<body id="beranda">

  <!-- Navbar -->
  <div class="navbar">
    <div class="container">
      <div class="navbar-box">
        <div class="logo" style="display: flex; align-items: center; gap: 10px;">
          <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Yafahi" style="height: 40px;">
          <h1>Yafahi</h1>
        </div>
        <div class="hamburger" id="hamburger">
          <span></span><span></span><span></span>
        </div>
        <ul class="menu" id="menu">
          <li><a href="#beranda">Beranda</a></li>
          <li><a href="#tentang">Tentang Kami</a></li>
          <li><a href="#kontak">Kontak</a></li>
          <li><a href="/login">Login</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero-box">
        <h1 data-aos="fade-down">SELAMAT DATANG DI <br/> SMP-SMK YAFAHI DRAMAGA</h1>
        <p data-aos="fade-right">MEMBENTUK PESERTA DIDIK SMP-SMK YAFAHI YANG BERBUDI PEKERTI LUHUR DAN BERPRESTASI</p>
        <a href="#tentang" data-aos="fade-up">Lihat Selengkapnya Tentang Kami</a>
      </div>
    </div>
  </section>

  <!-- Tentang Kami -->
  <section id="tentang">
    <div class="container">
      <div class="tentang-box">
        <div class="box">
          <h1 data-aos="fade-up">Pencapaian</h1>
        </div>
      </div>
      <div class="info">
        <div class="box">
          <h2>
            <i class="fa-solid fa-user-graduate" style="color:#4b0082; font-size: 30px;"></i>
            <span>250+</span>Siswa Per Tahun
          </h2>
        </div>
        <div class="box">
          <h2>
            <i class="fa-solid fa-chalkboard-teacher" style="color:#4b0082; font-size: 30px;"></i>
            <span>40+</span>Jumlah Guru
          </h2>
        </div>

      </div>
    </div>
  </section>

  <!-- Lokasi -->
  <section class="lokasi">
    <div class="container">
      <div class="map-box">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.5506966358953!2d106.72309607418775!3d-6.578244664304049!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c4d495555555%3A0xf73538256c38c1b0!2sSMP-SMK%20YAFAHI!5e0!3m2!1sid!2sid!4v1747382113757!5m2!1sid!2sid"
          width="100%" height="450" style="border:0;" allowfullscreen=""
          loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="kontak">
    <div class="container">
      <div class="footer-box">
        <div class="box">
          <h2>Social Media</h2>
          <p>Media sosial kami digunakan untuk menampilkan kegiatan dan informasi sekolah.</p>
          <div class="social">
            <a href="#"><i class="fa-brands fa-instagram"></i> yafahi_officialaccount</a>
          </div>
        </div>
        <div class="box">
          <h2>Kontak</h2>
          <p>Email: info@yafahi.sch.id</p>
          <p>Telepon: (0251) 123-4567</p>
          <p>Alamat: Jl. Raya Dramaga No.123, Bogor</p>
        </div>
      </div>
      <div class="copyright">
        &copy; 2025 SMP-SMK YAFAHI. All rights reserved.
      </div>
    </div>
  </footer>

  <!-- Scroll Up -->
  <a href="#beranda" class="scroll-up" id="scrollUp"><i class="fa fa-arrow-up"></i></a>

  <!-- AOS -->
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();

    const hamburger = document.getElementById("hamburger");
    const menu = document.getElementById("menu");
    hamburger.addEventListener("click", () => {
      menu.classList.toggle("active");
    });

    const scrollUp = document.getElementById("scrollUp");
    window.addEventListener("scroll", function () {
      if (window.scrollY > 300) {
        scrollUp.style.display = "block";
      } else {
        scrollUp.style.display = "none";
      }
    });
  </script>
</body>
</html>
