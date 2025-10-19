<?php
require_once 'includes/header.php';

// Delete user
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header('Location: users.php');
    exit;
}

// Fetch users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Users</h2>

<div class="card glass-card shadow-lg p-3">
    <!-- Search -->
    <div class="mb-3 d-flex justify-content-end">
        <input type="text" id="userSearch" class="form-control w-50" placeholder="Search users...">
    </div>

    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0" id="usersTable">
            <thead>
                <tr style="background: linear-gradient(135deg,#6a11cb,#2575fc); color:#fff;">
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr class="align-middle">
                    <td><?= $u['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar me-2"><?= strtoupper($u['username'][0]) ?></div>
                            <span class="fw-bold"><?= htmlspecialchars($u['username']) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge text-dark bg-gradient-secondary">
                            <?= date('M d, Y', strtotime($u['created_at'])) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-sm btn-gradient" onclick="return confirm('Delete user?')">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($users) == 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* ================= Glass Card + Table ================= */
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

.avatar {
    width:35px; height:35px;
    background: linear-gradient(135deg,#ff416c,#ff4b2b);
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    border-radius:50%; font-weight:700;
    text-transform:uppercase;
}

.badge { font-size:0.85rem; padding:0.5em 0.75em; }

.btn-gradient {
    background: linear-gradient(135deg,#ff416c,#ff4b2b);
    border:none; color:#fff; transition: transform 0.2s, box-shadow 0.2s;
}
.btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,75,43,0.4); }

/* Search input */
#userSearch { border-radius: 12px; }

/* Responsive tweaks */
@media(max-width:768px){
    .table thead { display:none; }
    .table tbody tr { display:block; margin-bottom:15px; background: rgba(255,255,255,0.05); padding:10px; border-radius:12px; }
    .table tbody td { display:flex; justify-content: space-between; padding:5px 10px; }
    .table tbody td:before { display:none; }
}
</style>

<script>
// Search / filter users
const searchInput = document.getElementById('userSearch');
const tableRows = document.querySelectorAll('#usersTable tbody tr');

searchInput.addEventListener('keyup', function(){
    const val = this.value.toLowerCase();
    tableRows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
