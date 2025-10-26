<?php
require_once 'includes/header.php';

// Fetch orders with user info
$orders = $pdo->query("
    SELECT o.*, u.username 
FROM orders o 
LEFT JOIN users u ON o.user_id=u.id 
ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h2>Orders</h2>
    <div class="d-flex gap-2 flex-wrap">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by user or status...">
        <select id="statusFilter" class="form-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<div class="card glass-card shadow-lg p-3">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0" id="ordersTable">
            <thead>
                <tr style="background: linear-gradient(135deg,#6a11cb,#2575fc); color:#fff;">
                    <th>ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($orders) == 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td><?= $o['id'] ?></td>
                        <td class="order-user"><?= htmlspecialchars($o['username']) ?></td>
                        <td>$<?= number_format($o['total_amount'],2) ?></td>
                        <td><?= htmlspecialchars($o['payment_method']) ?></td>
                        <td class="order-status"><?= ucfirst($o['status']) ?></td>
                        <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-gradient-info" data-bs-toggle="modal" data-bs-target="#orderModal<?= $o['id'] ?>">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals outside table -->
<?php foreach($orders as $o): ?>
<div class="modal fade bg-light" id="orderModal<?= $o['id'] ?>" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content glass-card p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Order #<?= $o['id'] ?> Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>User:</strong> <?= htmlspecialchars($o['username']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($o['address']) ?></p>
        <p><strong>Payment:</strong> <?= htmlspecialchars($o['payment_method']) ?> <?= $o['card_name'] ? "(Card: ****".substr($o['card_number'],-4).")" : '' ?></p>
        <h5>Products:</h5>
        <ul class="list-unstyled">
          <?php
          $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=?");
          $stmt->execute([$o['id']]);
          $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach($items as $i): ?>
            <li class="d-flex align-items-center mb-2">
              <img src="<?= $i['image'] ?>" width="50" class="me-2 rounded shadow-sm">
              <span><?= htmlspecialchars($i['name']) ?> × <?= $i['quantity'] ?> ($<?= number_format($i['unit_price'],2) ?>)</span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="modal-footer border-0">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<style>
.glass-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.glass-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.2); }

.table-responsive { border-radius: 15px; overflow-x:auto; }
.table tbody tr { transition: background 0.3s, transform 0.3s; }
.table tbody tr:hover { background: rgba(255,255,255,0.1); transform: translateX(3px); }

.btn-gradient-info {
    background: linear-gradient(135deg,#11998e,#38ef7d);
    color:#fff; border:none; transition: transform 0.2s, box-shadow 0.2s;
}
.btn-gradient-info:hover {
    transform: translateY(-2px); box-shadow: 0 6px 20px rgba(56,239,125,0.4);
}

/* Responsive table for mobile */
@media(max-width:768px){
    .table thead { display:none; }
    .table tbody tr { display:block; margin-bottom:15px; background: rgba(255,255,255,0.05); padding:10px; border-radius:12px; }
    .table tbody td { display:flex; justify-content: space-between; padding:5px 10px; }
}
</style>

<script>
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const table = document.getElementById('ordersTable').getElementsByTagName('tbody')[0];

function filterOrders() {
    const searchText = searchInput.value.toLowerCase();
    const statusText = statusFilter.value.toLowerCase();

    Array.from(table.rows).forEach(row => {
        const user = row.querySelector('.order-user')?.innerText.toLowerCase() || '';
        const status = row.querySelector('.order-status')?.innerText.toLowerCase() || '';

        if(user.includes(searchText) && (statusText === '' || status.includes(statusText))) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', filterOrders);
statusFilter.addEventListener('change', filterOrders);
</script>

<?php include 'includes/footer.php'; ?>
