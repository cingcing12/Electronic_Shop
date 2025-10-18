<?php
require_once 'init.php';
if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'account.php';
    redirect('login.php');
}
$user = get_user($pdo, $_SESSION['user_id']);
$titleName = "Account";
include 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body {
    background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
    font-family: "Poppins", sans-serif;
    color: #333;
}
.account-container {
    max-width: 950px;
    margin: 70px auto;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    animation: fadeIn 0.8s ease-in-out;
}
@keyframes fadeIn { from {opacity:0; transform:translateY(30px);} to {opacity:1; transform:translateY(0);} }

.account-header {
    background: linear-gradient(90deg, #007bff, #6610f2);
    color: #fff;
    text-align: center;
    padding: 50px 30px;
}
.account-header h2 { font-weight:600; letter-spacing:0.5px; }
.account-header p { margin-top:8px; font-size:1.05rem; opacity:0.9; }

.account-details { padding: 40px; }
.account-details h4 { font-weight:600; color:#007bff; margin-bottom:20px; }

.info-box {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px 25px;
    margin-bottom: 25px;
    box-shadow: inset 0 0 0 1px #e9ecef;
}

.order-section h4 { font-weight:600; color:#007bff; margin-bottom:20px; }
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
.order-card.cancelled {
    background: #f5f5f5;
    border-left-color: #e57373;
    color: #888;
    cursor: not-allowed;
    box-shadow: none;
}
.order-info { font-size: 0.95rem; }
.order-info strong { font-weight:600; color:#222; }
.order-status {
    padding: 7px 14px;
    border-radius: 20px;
    font-weight:500;
    font-size:0.85rem;
}
.order-status.pending { background-color:#ffe082; color:#333; }
.order-status.completed { background-color:#81c784; color:#fff; }
.order-status.cancelled { background-color:#e57373; color:#fff; }

.order-icon { font-size:1.3rem; margin-right:8px; }

.swal2-html-container img {
    max-width: 60px;
    margin-right: 10px;
    border-radius: 8px;
}
.order-item-row { display:flex; align-items:center; margin-bottom:8px; }
.order-item-row .item-info { flex-grow:1; }

@media (max-width:768px) {
    .account-header h2 { font-size:1.8rem; }
    .account-details { padding:25px; }
    .order-card { flex-direction:column; align-items:flex-start; }
}
</style>

<div class="account-container shadow-lg">
    <div class="account-header">
        <h2>Your Account</h2>
        <p>Welcome back, <strong><?= htmlspecialchars($user['username']); ?></strong> 👋</p>
    </div>

    <div class="account-details">
        <h4>Profile Information</h4>
        <div class="info-box">
            <p><strong>👤 Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
            <p><strong>📧 Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="order-section">
            <h4>Your Orders</h4>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user['id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($orders):
                foreach ($orders as $o):
                    $status = strtolower($o['status']);
                    $badgeClass = ($status == 'completed') ? 'completed' : (($status == 'cancelled') ? 'cancelled' : 'pending');
                    $isClickable = ($status !== 'cancelled');
                    $cardClass = ($status === 'cancelled') ? 'cancelled' : '';
            ?>
            <div class="order-card <?= $cardClass; ?>" <?= $isClickable ? "onclick=\"showOrderDetails({$o['id']})\"" : "" ?> >
                <div class="order-info" style="cursor: <?= $isClickable ? 'pointer' : 'not-allowed' ?>;">
                    <span class="order-icon">🧾</span>
                    <strong>Order #<?= $o['id']; ?></strong><br>
                    💰 <strong>$<?= number_format($o['total_amount'],2); ?></strong>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="order-status <?= $badgeClass; ?>"><?= ucfirst($status); ?></span>
                    <?php if($status === 'pending'): ?>
                        <button class="btn btn-sm btn-danger" onclick="cancelOrder(<?= $o['id']; ?>, event)">Cancel</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">You haven’t placed any orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showOrderDetails(orderId){
    fetch('order_details.php?order_id=' + orderId)
    .then(res => res.json())
    .then(data => {
        if(data.success){
            let html = '';
            data.items.forEach(item=>{
                html += `<div class="order-item-row">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="item-info">
                        <strong>${item.name}</strong><br>
                        Qty: ${item.quantity} × $${parseFloat(item.unit_price).toFixed(2)} = $${(item.quantity*item.unit_price).toFixed(2)}
                    </div>
                </div>`;
            });
            html += `<hr>
                     <p><strong>Total Amount:</strong> $${parseFloat(data.total).toFixed(2)}</p>
                     <p><strong>Payment Method:</strong> ${data.payment_method}</p>
                     <p><strong>Delivery Address:</strong> ${data.address}</p>
                     <p><strong>Status:</strong> ${data.status}</p>`;

            Swal.fire({
                title: `Order #${orderId} Details`,
                html: html,
                width: '700px',
                confirmButtonText: 'Close'
            });
        } else {
            Swal.fire('Error','Could not fetch order details.','error');
        }
    });
}

function cancelOrder(orderId, event){
    event.stopPropagation(); // prevent opening modal
    Swal.fire({
        title: 'Cancel Order?',
        text: "Are you sure you want to cancel this order?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No'
    }).then((result)=>{
        if(result.isConfirmed){
            fetch('cancel_order.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({order_id:orderId})
            }).then(res=>res.json())
            .then(data=>{
                if(data.success){
                    Swal.fire('Cancelled!','Your order has been cancelled.','success').then(()=>{
                        location.reload();
                    });
                } else {
                    Swal.fire('Error','Could not cancel order.','error');
                }
            });
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
