<?php
require_once __DIR__ . '/init.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if (!$username || !$email || !$password || !$password2) {
        $errors[] = "All fields required.";
    }
    if ($password !== $password2) {
        $errors[] = "Passwords do not match.";
    }
    // Check if username/email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $errors[] = "Username or email already taken.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $ins->execute([$username, $email, $hash]);
        // auto-login
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;
        redirect('index.php');
    }
}

// include 'includes/header.php';
?>

 <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<div class="register-wrapper">
  <canvas id="bg-canvas"></canvas>
  <div class="register-card p-4 p-md-5 rounded-4 shadow-lg">
    <h2 class="text-center fw-bold mb-4 text-gradient">Create Account</h2>

    <?php foreach ($errors as $e): ?>
      <div class="alert alert-danger rounded-pill text-center"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form method="post" novalidate>
      <div class="form-floating mb-3">
        <input type="text" name="username" class="form-control rounded-pill" id="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <label for="username">Username</label>
      </div>

      <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control rounded-pill" id="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <label for="email">Email</label>
      </div>

      <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control rounded-pill" id="password" placeholder="Password" required>
        <label for="password">Password</label>
      </div>

      <div class="form-floating mb-4">
        <input type="password" name="password2" class="form-control rounded-pill" id="password2" placeholder="Confirm Password" required>
        <label for="password2">Confirm Password</label>
      </div>

      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-glass btn-lg rounded-pill shadow-sm">Register</button>
      </div>

      <div class="text-center mb-3">
        <small class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none fw-semibold text-primary">Login here</a></small>
      </div>

      <div class="social-login text-center">
        <p class="text-muted mb-2">Or register with</p>
        <button type="button" class="btn btn-google">Google</button>
        <button type="button" class="btn btn-facebook">Facebook</button>
      </div>
    </form>
  </div>
</div>

<style>
/* Full-screen gradient + particle canvas */
.register-wrapper {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #6a11cb, #2575fc, #ff6a00, #f0c27b);
  background-size: 400% 400%;
  animation: gradientBG 20s ease infinite;
}
#bg-canvas {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  z-index: 0;
}

/* Gradient animation */
@keyframes gradientBG {
  0% {background-position:0% 50%;}
  50% {background-position:100% 50%;}
  100% {background-position:0% 50%;}
}

/* Glassmorphism card with neon glow */
.register-card {
  position: relative;
  z-index: 1;
  backdrop-filter: blur(16px);
  background-color: rgba(255, 255, 255, 0.15);
  max-width: 480px;
  width: 100%;
  border: 1px solid rgba(255,255,255,0.25);
  box-shadow: 0 15px 35px rgba(0,0,0,0.4);
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.register-card:hover {
  transform: perspective(1000px) rotateY(2deg) rotateX(2deg);
  box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}

/* Gradient title */
.text-gradient {
  background: linear-gradient(90deg, #ff6a00, #2575fc, #6a11cb);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: titleGradient 5s ease infinite;
}
@keyframes titleGradient {
  0% {background-position:0% 50%;}
  50% {background-position:100% 50%;}
  100% {background-position:0% 50%;}
}

/* Floating labels focus effect */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
  color: #2575fc;
  font-weight: 600;
  transform: translateY(-0.5rem) scale(0.9);
}

/* Glass buttons */
.btn-glass {
  background: rgba(255,255,255,0.25);
  border: 1px solid rgba(255,255,255,0.35);
  color: #fff;
  backdrop-filter: blur(6px);
  transition: all 0.3s ease;
}
.btn-glass:hover {
  background: rgba(255,255,255,0.45);
  transform: translateY(-2px) scale(1.05);
  color: #2575fc;
  box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

/* Social buttons */
.btn-google { background:#db4437; color:#fff; margin:0 5px; }
.btn-google:hover { background:#c1351d; }
.btn-facebook { background:#3b5998; color:#fff; margin:0 5px; }
.btn-facebook:hover { background:#2d4373; }

/* Input focus glow */
.form-control:focus {
  border-color: #2575fc;
  box-shadow: 0 0 10px rgba(37,117,252,0.5);
  background-color: rgba(255,255,255,0.7);
}

/* Responsive */
@media (max-width: 576px) {
  .register-card { padding: 2rem 1.5rem; }
}
</style>

<script>
// Particle background
const canvas = document.getElementById('bg-canvas');
const ctx = canvas.getContext('2d');
let particlesArray = [];

function initCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  particlesArray = [];
  const number = Math.floor(canvas.width / 10);
  for(let i=0;i<number;i++){
    particlesArray.push({
      x: Math.random()*canvas.width,
      y: Math.random()*canvas.height,
      size: Math.random()*3 + 1,
      speedX: (Math.random()-0.5)*1.5,
      speedY: (Math.random()-0.5)*1.5
    });
  }
}
function animateParticles(){
  ctx.clearRect(0,0,canvas.width,canvas.height);
  particlesArray.forEach(p=>{
    ctx.beginPath();
    ctx.arc(p.x,p.y,p.size,0,Math.PI*2);
    ctx.fillStyle='rgba(255,255,255,0.7)';
    ctx.fill();
    p.x += p.speedX;
    p.y += p.speedY;
    if(p.x<0) p.x = canvas.width;
    if(p.x>canvas.width) p.x = 0;
    if(p.y<0) p.y = canvas.height;
    if(p.y>canvas.height) p.y = 0;
  });
  requestAnimationFrame(animateParticles);
}
window.addEventListener('resize', initCanvas);
initCanvas();
animateParticles();
</script>


