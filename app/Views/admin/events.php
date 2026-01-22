<?php
$title = $title ?? 'Manage Events - EventKu';
$extraStyles = <<<CSS
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }
    .page-header h1 { font-size: 22px; color: var(--gray-900); margin: 0; }

    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 18px;
    }
    .event-card {
        background: white;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        transition: all 0.2s ease;
    }
    .event-card:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10); }
    .event-image {
        width: 100%;
        height: 200px;
        background: var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .event-image img { width: 100%; height: 100%; object-fit: cover; }
    .event-icon-large { font-size: 64px; }
    .event-content { padding: 18px; }
    .event-category {
        display: inline-block;
        padding: 6px 12px;
        background: var(--primary);
        color: white;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 10px;
        text-transform: capitalize;
    }
    .event-title { font-size: 16px; color: var(--gray-900); margin-bottom: 10px; font-weight: 800; }
    .event-info { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
    .info-item { display: flex; align-items: center; gap: 8px; color: var(--gray-600); font-size: 14px; }
    .event-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px solid var(--gray-100);
    }
    .event-price { font-size: 18px; font-weight: 900; color: var(--primary); }
    .event-actions { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
    .btn-sm { padding: 8px 12px; border-radius: 10px; font-size: 13px; font-weight: 800; }
    .btn-warning { background: var(--warning); color: white; }
    .btn-warning:hover { filter: brightness(0.92); }
    .status-active { color: var(--success); font-weight: 800; font-size: 13px; }
    .status-inactive { color: var(--danger); font-weight: 800; font-size: 13px; }
    .status-past { color: var(--gray-500); font-weight: 800; font-size: 13px; }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
        background: white;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
    }
    .empty-state-icon { font-size: 72px; margin-bottom: 18px; opacity: 0.5; }
    
    .filter-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: white;
        padding: 16px;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 800;
        background: var(--gray-100);
        color: var(--gray-600);
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .filter-tab:hover { background: var(--gray-200); }
    .filter-tab.active { background: var(--primary); color: white; }
    .filter-tab .badge {
        background: rgba(0,0,0,0.15);
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 12px;
        margin-left: 8px;
    }
    .archive-section {
        background: #FEF3C7;
        border: 2px solid #FCD34D;
        padding: 16px;
        border-radius: 14px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .archive-section .info {
        flex: 1;
    }
    .archive-section h4 {
        margin: 0 0 6px 0;
        color: #92400E;
        font-size: 16px;
    }
    .archive-section p {
        margin: 0;
        color: #78350F;
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .events-grid { grid-template-columns: 1fr; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .filter-tabs { flex-direction: column; }
        .filter-tab { text-align: center; }
    }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'events');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="page-header">
    <h1>🎉 Manage Events</h1>
    <a href="<?= base_url('admin/event/create') ?>" class="btn btn-primary">➕ Tambah Event Baru</a>
</div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">✓ <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">✗ <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Archive Section -->
    <div class="archive-section">
        <div class="info">
            <h4>🗄️ Auto-Archive Event Yang Sudah Lewat</h4>
            <p>Event yang sudah lewat tanggalnya akan otomatis disembunyikan dari user, tapi tetap bisa dilihat di sini. Klik tombol ini untuk mengarsipkan secara manual.</p>
        </div>
        <a href="<?= base_url('admin/events/archive-past') ?>" 
           class="btn btn-warning"
           onclick="return confirm('Arsipkan semua event yang sudah lewat tanggalnya?')">
            📦 Arsipkan Event Lama
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="<?= base_url('admin/events?filter=all') ?>" 
           class="filter-tab <?= ($filter ?? 'all') === 'all' ? 'active' : '' ?>">
            📋 Semua Event
            <span class="badge"><?= $stats['total'] ?? 0 ?></span>
        </a>
        <a href="<?= base_url('admin/events?filter=upcoming') ?>" 
           class="filter-tab <?= ($filter ?? '') === 'upcoming' ? 'active' : '' ?>">
            🚀 Event Mendatang
            <span class="badge"><?= $stats['upcoming'] ?? 0 ?></span>
        </a>
        <a href="<?= base_url('admin/events?filter=past') ?>" 
           class="filter-tab <?= ($filter ?? '') === 'past' ? 'active' : '' ?>">
            📅 Event Selesai
            <span class="badge"><?= $stats['past'] ?? 0 ?></span>
        </a>
    </div>

    <?php if (!empty($events)): ?>
        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <div class="event-image">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?= base_url($event['image']) ?>" alt="<?= esc($event['title']) ?>">
                        <?php else: ?>
                            <div class="event-icon-large"><?= esc($event['icon'] ?? '🎉') ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="event-content">
                        <span class="event-category"><?= esc($event['category']) ?></span>
                        <h3 class="event-title"><?= esc($event['title']) ?></h3>
                        
                        <div class="event-info">
                            <div class="info-item">
                                <span>📅</span>
                                <span><?= esc($event['date']) ?></span>
                            </div>
                            <div class="info-item">
                                <span>📍</span>
                                <span><?= esc($event['location']) ?></span>
                            </div>
                            <div class="info-item">
                                <span>🎫</span>
                                <span><?= number_format($event['available_tickets']) ?> tiket tersedia</span>
                            </div>
                            <div class="info-item">
                                <span>📊</span>
                                <?php 
                                    $isPast = strtotime($event['date']) < strtotime(date('Y-m-d'));
                                    if ($isPast) {
                                        echo '<span class="status-past">📅 Event Selesai</span>';
                                    } else {
                                        echo '<span class="' . ($event['is_active'] ? 'status-active' : 'status-inactive') . '">';
                                        echo $event['is_active'] ? '✓ Aktif' : '✗ Tidak Aktif';
                                        echo '</span>';
                                    }
                                ?>
                            </div>
                        </div>
                        
                        <div class="event-footer">
                            <div class="event-price">
                                Rp <?= number_format($event['price'], 0, ',', '.') ?>
                            </div>
                            <div class="event-actions">
                                <a href="<?= base_url('admin/event/edit/' . $event['id']) ?>" 
                                   class="btn btn-warning" 
                                   title="Edit Event">
                                    ✏️ Edit
                                </a>
                                <a href="<?= base_url('admin/event/delete/' . $event['id']) ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('⚠️ Yakin ingin menghapus event ini?\n\nEvent: <?= esc($event['title']) ?>\n\nPerhatian: Event yang sudah memiliki booking tidak bisa dihapus.')"
                                   title="Hapus Event">
                                    🗑️ Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🎉</div>
            <h3>Belum ada event</h3>
            <p>Mulai tambahkan event pertama Anda</p>
            <a href="<?= base_url('admin/event/create') ?>" class="btn btn-primary" style="margin-top: 20px;">
                ➕ Tambah Event Baru
            </a>
        </div>
    <?php endif; ?>
<?= $this->include('admin/_partials/layout_bottom') ?>