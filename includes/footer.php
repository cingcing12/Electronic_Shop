<footer class="footer container-fluid">
  <div class="footer-glow"></div>

  <div class="container py-5 position-relative">
    <div class="row gy-5">
      <!-- 🏢 Brand Info -->
      <div class="col-lg-4 col-md-6">
        <h3 class="fw-bold text-gradient mb-3">
          <i class="bi bi-lightning-charge-fill me-2"></i>Electronic-Shop
        </h3>
        <p class="text-light opacity-75">
          Discover the latest and most powerful electronics at unbeatable prices.
          Innovation, quality, and speed — all in one place.
        </p>
        <div class="social-icons mt-3">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-twitter-x"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
          <a href="#"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>

      <!-- ⚡ Quick Links -->
      <div class="col-lg-2 col-md-6 col-6">
        <h6 class="footer-title">Shop</h6>
        <ul class="footer-links">
          <li><a href="#">Home</a></li>
          <li><a href="#">Products</a></li>
          <li><a href="#">Wishlist</a></li>
          <li><a href="#">Cart</a></li>
        </ul>
      </div>

      <!-- 🧭 Company -->
      <div class="col-lg-2 col-md-6 col-6">
        <h6 class="footer-title">Company</h6>
        <ul class="footer-links">
          <li><a href="#">About Us</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms</a></li>
        </ul>
      </div>

      <!-- 💌 Newsletter -->
      <div class="col-lg-4 col-md-6">
        <h6 class="footer-title">Stay Updated</h6>
        <p class="text-light opacity-75">Subscribe to get exclusive offers and product updates.</p>
        <form class="newsletter mt-3" style="background: none;">
          <div class="input-group">
            <input type="email" class="form-control" placeholder="Enter your email">
            <button class="btn btn-subscribe" type="submit">Subscribe</button>
          </div>
        </form>
      </div>
    </div>

    <hr class="footer-line my-4">

    <div class="text-center text-light-50 small">
      © 2025 <span class="fw-bold text-gradient">Electronic-Shop</span>. All rights reserved.<br>
      Designed with ❤️ for innovation.
    </div>
  </div>
</footer>

<!-- 🌈 CSS -->
<style>
.footer {
  position: relative;
  background: radial-gradient(circle at 20% 20%, #0a0a1f, #050515, #000);
  color: #fff;
  overflow: hidden;
  font-family: "Poppins", sans-serif;
}

/* Glowing animated layer */
.footer-glow {
  position: absolute;
  top: -100px;
  left: -150px;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(0,212,255,0.25), transparent 70%);
  animation: moveGlow 12s infinite alternate;
  filter: blur(100px);
  z-index: 0;
}
@keyframes moveGlow {
  from { transform: translate(0,0) scale(1); }
  to { transform: translate(200px,100px) scale(1.2); }
}

/* Gradient Text */
.text-gradient {
  background: linear-gradient(90deg, #00d4ff, #7f00ff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Footer Titles */
.footer-title {
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #c9d1ff;
}

/* Links */
.footer-links {
  list-style: none;
  padding: 0;
}
.footer-links li {
  margin-bottom: 8px;
}
.footer-links a {
  color: #aab3ff;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}
.footer-links a:hover {
  color: #00d4ff;
  text-shadow: 0 0 10px #00d4ff;
  transform: translateX(5px);
}

/* Social Icons */
.social-icons a {
  color: #fff;
  font-size: 1.3rem;
  margin-right: 12px;
  transition: 0.3s;
}
.social-icons a:hover {
  color: #00d4ff;
  text-shadow: 0 0 12px #00d4ff;
  transform: translateY(-4px);
}

/* Newsletter */
.newsletter .form-control {
  border: 1px solid rgba(255,255,255,0.2);
  color: #fff;
}
.newsletter .form-control::placeholder {
  color: rgba(255,255,255,0.6);
}

.btn-subscribe {
  background: linear-gradient(90deg, #00d4ff, #7f00ff);
  border: none;
  color: #fff;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-subscribe:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px #00d4ff;
}

/* Line */
.footer-line {
  border-color: rgba(255,255,255,0.1);
}

/* Responsive */
@media (max-width: 768px) {
  .footer { text-align: center; }
  .social-icons a { margin: 0 8px; }
}
</style>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
