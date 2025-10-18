<?php
require_once 'includes/header.php';

// Fetch stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='completed'")->fetchColumn();
?>

<!-- Dashboard Header -->
<div class="dashboard-header mb-5">
    <h1>Admin Dashboard</h1>
    <p class="welcome-text">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> 👋</p>
</div>

<!-- Stats Cards -->
<div class="row g-4">
    <?php 
    $cards = [
        ['icon'=>'users','title'=>'Total Users','count'=>$totalUsers,'gradient'=>'linear-gradient(135deg,#6a11cb,#2575fc)'],
        ['icon'=>'boxes','title'=>'Total Products','count'=>$totalProducts,'gradient'=>'linear-gradient(135deg,#11998e,#38ef7d)'],
        ['icon'=>'shopping-cart','title'=>'Total Orders','count'=>$totalOrders,'gradient'=>'linear-gradient(135deg,#f7971e,#ffd200)'],
        ['icon'=>'dollar-sign','title'=>'Total Revenue','count'=>number_format($totalRevenue,2),'gradient'=>'linear-gradient(135deg,#ff416c,#ff4b2b)']
    ];

    foreach($cards as $c): ?>
    <div class="col-md-3">
        <div class="card glass-card p-4 position-relative">
            <div class="icon-circle" style="background: <?= $c['gradient'] ?>">
                <i class="fas fa-<?= $c['icon'] ?> fa-2x text-white"></i>
            </div>
            <h6 class="mb-2 text-light"><?= $c['title'] ?></h6>
            <h2 class="counter text-white" data-count="<?= $c['count'] ?>">0</h2>
            <canvas class="sparkline mt-2" height="40"></canvas>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Charts Section -->
<div class="row g-4 mt-5">
    <div class="col-lg-6">
        <div class="card glass-card p-4">
            <h5 class="mb-3 text-light">Orders Over Time</h5>
            <canvas id="ordersChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card glass-card p-4">
            <h5 class="mb-3 text-light">Revenue Over Time</h5>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Include Chart.js and tsParticles -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.min.js"></script>

<script>
/* ================= Animated Counters ================= */
document.querySelectorAll('.counter').forEach(el=>{
    const countTo = parseFloat(el.dataset.count.replace(/,/g,''));
    let startTime = null;
    const duration = 1800;
    function animateCounter(timestamp){
        if(!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        const val = Math.min(progress / duration * countTo, countTo);
        el.innerText = countTo % 1 ? val.toFixed(2) : Math.floor(val);
        if(progress < duration) requestAnimationFrame(animateCounter);
        else el.innerText = countTo % 1 ? countTo.toFixed(2) : Math.floor(countTo);
    }
    requestAnimationFrame(animateCounter);
});

/* ================= Sparkline Mini-Charts ================= */
document.querySelectorAll('.sparkline').forEach(canvas=>{
    const ctx = canvas.getContext('2d');
    new Chart(ctx,{
        type:'line',
        data:{
            labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets:[{
                data:Array.from({length:7},()=>Math.floor(Math.random()*100)),
                borderColor:'rgba(255,255,255,0.8)',
                backgroundColor:'rgba(255,255,255,0.2)',
                tension:0.3,
                fill:true,
                pointRadius:0
            }]
        },
        options:{responsive:true, plugins:{legend:{display:false}}, scales:{x:{display:false},y:{display:false}}}
    });
});

/* ================= Charts ================= */
const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const ordersData = [5,12,8,20,15,10,25,30,28,22,18,35];
const revenueData = [500,1200,800,2000,1500,1000,2500,3000,2800,2200,1800,3500];

function gradientFill(chartId, colorStart, colorEnd){
    const ctx = document.getElementById(chartId).getContext('2d');
    const gradient = ctx.createLinearGradient(0,0,0,400);
    gradient.addColorStop(0,colorStart+'55');
    gradient.addColorStop(1,colorEnd+'00');
    return gradient;
}

const chartOptions = {
    responsive:true,
    plugins:{ legend:{ display:false }, tooltip:{ mode:'index', intersect:false } },
    interaction:{ mode:'nearest', axis:'x', intersect:false },
    scales:{
        y:{ beginAtZero:true, grid:{ color:'rgba(255,255,255,0.1)' }, ticks:{ color:'#fff' } },
        x:{ grid:{ color:'rgba(255,255,255,0.1)' }, ticks:{ color:'#fff' } }
    }
};

new Chart(document.getElementById('ordersChart').getContext('2d'),{
    type:'line',
    data:{ labels, datasets:[{ data:ordersData, borderColor:'#6a11cb', backgroundColor:gradientFill('ordersChart','#6a11cb','#2575fc'), tension:0.4, fill:true, pointRadius:6, pointHoverRadius:8 }] },
    options:chartOptions
});

new Chart(document.getElementById('revenueChart').getContext('2d'),{
    type:'line',
    data:{ labels, datasets:[{ data:revenueData, borderColor:'#ff416c', backgroundColor:gradientFill('revenueChart','#ff416c','#ff4b2b'), tension:0.4, fill:true, pointRadius:6, pointHoverRadius:8 }] },
    options:chartOptions
});

/* ================= Floating Particles ================= */
tsParticles.load("tsparticles", {
    background: { color: "transparent" },
    fpsLimit: 60,
    particles: {
        number: { value: 60, density: { enable: true, area: 800 } },
        color: { value: ["#ffffff","#6a11cb","#2575fc","#ff416c","#ff4b2b"] },
        shape: { type: "circle" },
        opacity: { value: 0.2, random: true },
        size: { value: 3, random: true },
        move: { enable: true, speed: 1.5, direction: "none", outModes: "out" }
    },
    interactivity: { events: { onHover: { enable: true, mode: "repulse" } }, modes: { repulse: { distance: 100 } } }
});
</script>

<div id="tsparticles" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:-1;"></div>

<style>
/* ================= Full Futuristic Dashboard ================= */
body {
    min-height:100vh;
    margin:0;
    font-family:'Poppins',sans-serif;
    background: linear-gradient(-45deg,#1e3c72,#2a5298,#ff416c,#ff4b2b);
    background-size: 400% 400%;
    animation: gradientBG 20s ease infinite;
    color:#fff;
}

@keyframes gradientBG{
  0%{background-position:0% 50%}
  50%{background-position:100% 50%}
  100%{background-position:0% 50%}
}

/* Dashboard Header */
.dashboard-header h1 { font-size:2.5rem; font-weight:700; }
.dashboard-header .welcome-text { font-size:1rem; color:#ccc; margin-top:0.2rem; }

/* Glass + Neon Glow Cards */
.glass-card {
    border-radius:20px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    box-shadow: 0 0 30px rgba(0,0,0,0.5);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    position:relative;
    overflow:hidden;
}
.glass-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 0 50px rgba(255,255,255,0.5);
}

/* Neon Icon Glow */
.icon-circle {
    width:70px;
    height:70px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    position:absolute;
    top:-25px;
    right:-25px;
    box-shadow:0 0 20px rgba(255,255,255,0.2);
}
.icon-circle i { color:#fff; text-shadow: 0 0 10px rgba(255,255,255,0.5); }

.counter { font-size:2.5rem; font-weight:700; }
h5,h6 { margin:0; }
canvas { background:transparent; }
</style>
