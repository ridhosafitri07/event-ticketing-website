<?php
$title = $title ?? 'Manage Users - EventKu';
$extraStyles = <<<CSS
    .admin-container { max-width: 1400px; margin: 0 auto; }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .page-header h1 { font-size: 22px; color: var(--gray-900); margin: 0; }

    .search-bar { display: flex; gap: 12px; margin-bottom: 18px; }
    .search-bar input {
        flex: 1;
        padding: 12px 14px;
        border: 1px solid var(--gray-300);
        border-radius: 12px;
        font-size: 14px;
        background: white;
    }
    .search-bar input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .users-table {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .table { width: 100%; border-collapse: collapse; }
    .table th {
        background: var(--gray-50);
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 900;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--gray-200);
    }
    .table td { padding: 16px; border-bottom: 1px solid var(--gray-100); font-size: 14px; }
    .table tr:hover { background: var(--gray-50); }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 16px;
    }
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-details h4 { margin: 0 0 4px 0; color: var(--gray-900); font-size: 14px; font-weight: 900; }
    .user-details p { margin: 0; color: var(--gray-500); font-size: 13px; }

    .stats-badge {
        display: inline-block;
        padding: 4px 10px;
        background: var(--gray-100);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        color: var(--gray-600);
    }

    .btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-view:hover { background: var(--primary-dark); }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--gray-500); }
    .empty-state-icon { font-size: 72px; margin-bottom: 18px; opacity: 0.5; }

    @media (max-width: 768px) {
        .table { font-size: 12px; }
        .table th, .table td { padding: 12px 10px; }
        .user-info { flex-direction: column; align-items: flex-start; }
    }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'users');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="admin-container">
    <div class="page-header">
        <h1>👥 Manage Users</h1>
    </div>

    <div class="search-bar">
        <input type="text" 
               id="searchInput" 
               placeholder="🔍 Cari user berdasarkan nama, email, atau nomor HP..." 
               onkeyup="searchUsers()">
    </div>

    <?php if (!empty($users)): ?>
        <div class="users-table">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Kontak</th>
                        <th>Terdaftar Sejak</th>
                        <th>Total Booking</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user):
                        $totalBookings = (int)($bookingCounts[$user['id']] ?? 0);
                    ?>
                        <tr data-name="<?= strtolower(esc($user['name'])) ?>" 
                            data-email="<?= strtolower(esc($user['email'])) ?>" 
                            data-phone="<?= esc($user['phone']) ?>">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                    <div class="user-details">
                                        <h4><?= esc($user['name']) ?></h4>
                                        <p>User ID: #<?= $user['id'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-size: 13px;">📧 <?= esc($user['email']) ?></span>
                                    <span style="font-size: 13px;">📱 <?= esc($user['phone']) ?></span>
                                </div>
                            </td>
                            <td>
                                <?= date('d M Y', strtotime($user['registered_at'])) ?>
                                <br>
                                <span style="font-size: 12px; color: #64748b;">
                                    <?= date('H:i', strtotime($user['registered_at'])) ?> WIB
                                </span>
                            </td>
                            <td>
                                <span class="stats-badge">
                                    🎫 <?= $totalBookings ?> booking
                                </span>
                            </td>
                            <td>
                                <a class="btn-view" href="<?= base_url('admin/users/' . $user['id']) ?>">👁️ Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div id="noResults" style="display: none; text-align: center; padding: 40px; color: #94a3b8;">
            <p style="font-size: 16px;">Tidak ada user yang cocok dengan pencarian</p>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>Belum ada user terdaftar</h3>
            <p>User akan muncul di sini setelah registrasi</p>
        </div>
    <?php endif; ?>
</div>

<script>
function searchUsers() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('usersTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const name = row.getAttribute('data-name');
        const email = row.getAttribute('data-email');
        const phone = row.getAttribute('data-phone');
        
        if (name.includes(searchValue) || email.includes(searchValue) || phone.includes(searchValue)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
    
    document.getElementById('noResults').style.display = (visibleCount === 0 && searchValue !== '') ? 'block' : 'none';
}
</script>

<?= $this->include('admin/_partials/layout_bottom') ?>