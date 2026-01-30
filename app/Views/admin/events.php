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
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 18px;
        max-width: 100%;
    }
    .event-card {
        background: white;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        transition: all 0.2s ease;
        max-width: 450px;
        width: 100%;
        justify-self: center;
    }
    .event-card:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10); }
    .event-image {
        width: 100%;
        height: 220px;
        max-height: 220px;
        background: var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .event-image img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover;
        object-position: center;
    }
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
    
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal.active { display: flex; }
    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 0;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    .modal-header {
        padding: 24px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { font-size: 18px; font-weight: 700; margin: 0; }
    .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--gray-500);
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
    .close-btn:hover { background: var(--gray-100); }
    .modal-body { padding: 24px; }
    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
    }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 14px;
    }
    .form-group textarea { resize: vertical; }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    
    .search-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 18px;
        position: relative;
    }
    .search-bar svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--gray-400);
        pointer-events: none;
    }
    .search-bar input {
        flex: 1;
        padding: 12px 14px 12px 40px;
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
    .advanced-filters {
        background: white;
        padding: 20px;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        margin-bottom: 20px;
    }
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }
    .filter-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 6px;
    }
    .filter-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 14px;
    }
    .filter-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
    
    @media (max-width: 1200px) {
        .events-grid { 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .events-grid { 
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .event-card {
            max-width: 100%;
        }
        .event-image {
            height: 200px;
            max-height: 200px;
        }
        .page-header { 
            flex-direction: column; 
            align-items: flex-start; 
        }
        .filter-tabs { 
            flex-direction: column; 
        }
        .filter-tab { 
            text-align: center; 
        }
        .filter-row { 
            grid-template-columns: 1fr; 
        }
        .filter-group .btn { 
            width: 100%; 
        }
        .event-actions {
            justify-content: center;
            width: 100%;
        }
        .event-actions .btn {
            flex: 1;
        }
    }
    
    @media (max-width: 480px) {
        .events-grid {
            grid-template-columns: 1fr;
        }
        .event-image {
            height: 180px;
            max-height: 180px;
        }
        .event-title {
            font-size: 15px;
        }
        .info-item {
            font-size: 13px;
        }
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

    <!-- Search Bar -->
    <div class="search-bar">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" id="searchInput" placeholder="Cari event berdasarkan nama, kategori, atau lokasi..." onkeyup="searchEvents()">
    </div>

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
        <a href="<?= base_url('admin/events?filter=inactive') ?>" 
           class="filter-tab <?= ($filter ?? '') === 'inactive' ? 'active' : '' ?>">
            ✗ Event Nonaktif
            <span class="badge"><?= $stats['inactive'] ?? 0 ?></span>
        </a>
    </div>

    <!-- Advanced Filters -->
    <div class="advanced-filters">
        <form method="GET" action="<?= base_url('admin/events') ?>">
            <input type="hidden" name="filter" value="<?= esc($filter ?? 'all') ?>">
            
            <div class="filter-row">
                <div class="filter-group">
                    <label>📅 Dari Tanggal</label>
                    <input type="date" name="date_from" value="<?= esc($date_from ?? '') ?>" class="filter-input">
                </div>
                
                <div class="filter-group">
                    <label>📅 Sampai Tanggal</label>
                    <input type="date" name="date_to" value="<?= esc($date_to ?? '') ?>" class="filter-input">
                </div>
                
                <div class="filter-group">
                    <label>🎯 Kategori</label>
                    <select name="category" class="filter-input">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories ?? [] as $cat): ?>
                            <option value="<?= esc($cat) ?>" <?= ($category ?? '') === $cat ? 'selected' : '' ?>>
                                <?= esc(ucfirst($cat)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <a href="<?= base_url('admin/events?filter=' . ($filter ?? 'all')) ?>" class="btn btn-secondary">↻ Reset</a>
                </div>
            </div>
        </form>
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
                                <?php 
                                    $isPastEvent = strtotime($event['date']) < strtotime(date('Y-m-d'));
                                    // Jika event sudah selesai (tanggal lewat), jangan tampilkan tombol aktifkan/nonaktifkan
                                    if (!$isPastEvent): 
                                        if ($event['is_active']): 
                                ?>
                                    <button class="btn btn-danger" 
                                            onclick="showDeactivateModal(<?= $event['id'] ?>, '<?= esc($event['title']) ?>')" 
                                            title="Nonaktifkan Event">
                                        ✗ Nonaktifkan
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary" 
                                            onclick="showActivateModal(<?= $event['id'] ?>, '<?= esc($event['title']) ?>')" 
                                            title="Aktifkan Event">
                                        ✓ Aktifkan
                                    </button>
                                <?php 
                                        endif;
                                    endif; 
                                ?>
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

    <!-- ACTIVATE MODAL -->
    <div class="modal" id="activateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✓ Aktifkan Event</h3>
                <button class="close-btn" onclick="closeActivateModal()">&times;</button>
            </div>
            <form id="activateForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mengaktifkan event ini?</p>
                    <div class="form-group">
                        <label>Event</label>
                        <input type="text" id="activateEventName" readonly>
                    </div>
                    <p style="color: var(--gray-600); font-size: 13px; margin-top: 12px;">
                        ✓ Event akan muncul di daftar event user<br>
                        ✓ User dapat membeli tiket event ini
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeActivateModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">✓ Aktifkan Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DEACTIVATE MODAL -->
    <div class="modal" id="deactivateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✗ Nonaktifkan Event</h3>
                <button class="close-btn" onclick="closeDeactivateModal()">&times;</button>
            </div>
            <form id="deactivateForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="POST">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menonaktifkan event ini?</p>
                    <div class="form-group">
                        <label>Event</label>
                        <input type="text" id="deactivateEventName" readonly>
                    </div>
                    <p style="color: var(--warning); font-size: 13px; margin-top: 12px;">
                        ⚠️ Event akan disembunyikan dari user<br>
                        ⚠️ User tidak dapat membeli tiket event ini
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeDeactivateModal()">Batal</button>
                    <button type="submit" class="btn btn-danger">✗ Nonaktifkan Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showActivateModal(eventId, eventName) {
            document.getElementById('activateForm').action = '<?= base_url('admin/event/activate/') ?>' + eventId;
            document.getElementById('activateEventName').value = eventName;
            document.getElementById('activateModal').classList.add('active');
        }

        function closeActivateModal() {
            document.getElementById('activateModal').classList.remove('active');
        }

        function showDeactivateModal(eventId, eventName) {
            document.getElementById('deactivateForm').action = '<?= base_url('admin/event/deactivate/') ?>' + eventId;
            document.getElementById('deactivateEventName').value = eventName;
            document.getElementById('deactivateModal').classList.add('active');
        }

        function closeDeactivateModal() {
            document.getElementById('deactivateModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('activateModal').addEventListener('click', function(e) {
            if (e.target === this) closeActivateModal();
        });

        document.getElementById('deactivateModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeactivateModal();
        });

        function searchEvents() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const eventsGrid = document.querySelector('.events-grid');
            const eventCards = eventsGrid ? eventsGrid.querySelectorAll('.event-card') : [];
            let visibleCount = 0;

            eventCards.forEach(card => {
                const title = card.querySelector('.event-title').textContent.toLowerCase();
                const category = card.querySelector('.event-category').textContent.toLowerCase();
                const location = card.querySelector('.info-item:nth-child(2)').textContent.toLowerCase();

                if (title.includes(searchValue) || category.includes(searchValue) || location.includes(searchValue)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update empty state if exists
            const emptyState = document.querySelector('.empty-state');
            if (emptyState && eventsGrid) {
                if (visibleCount === 0 && searchValue !== '') {
                    eventsGrid.style.display = 'none';
                    if (!document.getElementById('searchEmptyState')) {
                        const searchEmpty = document.createElement('div');
                        searchEmpty.id = 'searchEmptyState';
                        searchEmpty.className = 'empty-state';
                        searchEmpty.innerHTML = '<div class="empty-state-icon">🔍</div><h3>Tidak ditemukan</h3><p>Event yang Anda cari tidak ada</p>';
                        eventsGrid.parentNode.insertBefore(searchEmpty, eventsGrid.nextSibling);
                    }
                } else {
                    eventsGrid.style.display = '';
                    const searchEmpty = document.getElementById('searchEmptyState');
                    if (searchEmpty) searchEmpty.remove();
                }
            }
        }
    </script>
<?= $this->include('admin/_partials/layout_bottom') ?>