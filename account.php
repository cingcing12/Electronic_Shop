<?php
require_once 'init.php';
if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'account.php';
    redirect('login.php');
}
$user = get_user($pdo, $_SESSION['user_id']);
include 'includes/header.php';
?>

<!-- Add Bootstrap CSS if not included yet -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom Styles -->
<style>
body {
    background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
    font-family: "Poppins", sans-serif;
    color: #333;
}

/* ===== Account Card ===== */
.account-container {
    max-width: 950px;
    margin: 70px auto;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    animation: fadeIn 0.8s ease-in-out;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(30px);}
    to {opacity: 1; transform: translateY(0);}
}

/* ===== Header ===== */
.account-header {
    background: linear-gradient(90deg, #007bff, #6610f2);
    color: #fff;
    text-align: center;
    padding: 50px 30px;
}

.account-header h2 {
    font-weight: 600;
    letter-spacing: 0.5px;
}

.account-header p {
    margin-top: 8px;
    font-size: 1.05rem;
    opacity: 0.9;
}

/* ===== Profile Info ===== */
.account-details {
    padding: 40px;
}

.account-details h4 {
    font-weight: 600;
    color: #007bff;
    margin-bottom: 20px;
}

.account-details .info-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px 25px;
    margin-bottom: 25px;
    box-shadow: inset 0 0 0 1px #e9ecef;
}

.account-details p {
    margin-bottom: 5px;
    font-size: 0.95rem;
}

.account-details strong {
    color: #222;
}

/* ===== Orders Section ===== */
.order-section h4 {
    font-weight: 600;
    color: #007bff;
    margin-bottom: 20px;
}

.order-card {
    border-radius: 15px;
    background: #f8f9fa;
    padding: 18px 22px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
    border-left: 5px solid transparent;
}

.order-card:hover {
    transform: translateY(-3px);
    background: #eef2ff;
    border-left: 5px solid #007bff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
}

.order-info {
    font-size: 0.95rem;
}

.order-info strong {
    font-weight: 600;
    color: #222;
}

.order-status {
    padding: 7px 14px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
}

.order-status.pending { background-color: #ffe082; color: #333; }
.order-status.completed { background-color: #81c784; color: #fff; }
.order-status.cancelled { background-color: #e57373; color: #fff; }

.order-icon {
    font-size: 1.3rem;
    margin-right: 8px;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .account-header h2 { font-size: 1.8rem; }
    .account-details { padding: 25px; }
    .order-card { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="account-container shadow-lg">
    <div class="account-header">
        <h2>Your Account</h2>
        <p>Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong> 👋</p>
    </div>

    <div class="account-details">
        <h4>Profile Information</h4>
        <div class="info-box">
            <p><strong>👤 Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="order-section">
            <h4>Your Orders</h4>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user['id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($orders): ?>
                <?php foreach ($orders as $o): 
                    $status = strtolower($o['status']);
                    $badgeClass = ($status == 'completed') ? 'completed' : (($status == 'cancelled') ? 'cancelled' : 'pending');
                ?>
                    <div class="order-card">
                        <div class="order-info">
                            <span class="order-icon">🧾</span>
                            <strong>Order #<?php echo $o['id']; ?></strong><br>
                            💰 <strong>$<?php echo number_format($o['total_amount'], 2); ?></strong>
                        </div>
                        <span class="order-status <?php echo $badgeClass; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">You haven’t placed any orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>
