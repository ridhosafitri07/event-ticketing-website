<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container">
    <!-- Hero Header with Stats -->
    <div class="dashboard-hero">
        <div class="hero-content">
            <h1>🎉 Temukan Event Favoritmu!</h1>
            <p>Jelajahi berbagai event menarik dan booking tiketmu sekarang</p>
        </div>
        
        <!-- Mini Stats -->
        <div class="hero-stats">
            <div class="hero-stat-item">
                <div class="hero-stat-number"><?= count($events ?? []) ?></div>
                <div class="hero-stat-label">Event Tersedia</div>
            </div>
            <div class="hero-stat-item">
                <div class="hero-stat-number">
                    <?php 
                    $totalTickets = 0;
                    foreach ($events ?? [] as $event) {
                        $totalTickets += $event['available_tickets'];
                    }
                    echo number_format($totalTickets);
                    ?>
                </div>
                <div class="hero-stat-label">Tiket Available</div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="toast-notification success show">
            <div class="toast-icon">✓</div>
            <div class="toast-content">
                <div class="toast-title">Berhasil!</div>
                <div class="toast-message"><?= session()->getFlashdata('success') ?></div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="toast-notification error show">
            <div class="toast-icon">✗</div>
            <div class="toast-content">
                <div class="toast-title">Error!</div>
                <div class="toast-message"><?= session()->getFlashdata('error') ?></div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="search-section">
        <div class="search-box-enhanced">
            <span class="search-icon-enhanced">🔍</span>
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Cari event by nama, lokasi, atau kategori..."
                onkeyup="searchEvents()"
            >
            <button class="search-clear" id="clearSearch" onclick="clearSearch()" style="display:none">×</button>
        </div>
        <div class="search-results-info" id="searchInfo"></div>
    </div>

    <!-- Filter Tabs with Icons -->
    <div class="filter-tabs-dashboard">
        <button class="filter-tab-dash active" onclick="filterEvents(event, 'all')" data-category="all">
            <span class="tab-emoji">🔍</span>
            <span class="tab-text">Semua</span>
            <span class="tab-count"><?= count($events ?? []) ?></span>
        </button>
        <button class="filter-tab-dash" onclick="filterEvents(event, 'Musik')" data-category="Musik">
            <span class="tab-emoji">🎵</span>
            <span class="tab-text">Musik</span>
            <span class="tab-count">
                <?php 
                $musik = array_filter($events ?? [], fn($e) => strtolower($e['category']) === strtolower('Musik'));
                echo count($musik);
                ?>
            </span>
        </button>
        <button class="filter-tab-dash" onclick="filterEvents(event, 'Olahraga')" data-category="Olahraga">
            <span class="tab-emoji">⚽</span>
            <span class="tab-text">Olahraga</span>
            <span class="tab-count">
                <?php 
                $olahraga = array_filter($events ?? [], fn($e) => strtolower($e['category']) === strtolower('Olahraga'));
                echo count($olahraga);
                ?>
            </span>
        </button>
        <button class="filter-tab-dash" onclick="filterEvents(event, 'Edukasi')" data-category="Edukasi">
            <span class="tab-emoji">📚</span>
            <span class="tab-text">Edukasi</span>
            <span class="tab-count">
                <?php 
                $edukasi = array_filter($events ?? [], fn($e) => strtolower($e['category']) === strtolower('Edukasi'));
                echo count($edukasi);
                ?>
            </span>
        </button>
        <button class="filter-tab-dash" onclick="filterEvents(event, 'Teknologi')" data-category="Teknologi">
            <span class="tab-emoji">💻</span>
            <span class="tab-text">Teknologi</span>
            <span class="tab-count">
                <?php 
                $teknologi = array_filter($events ?? [], fn($e) => strtolower($e['category']) === strtolower('Teknologi'));
                echo count($teknologi);
                ?>
            </span>
        </button>
        <button class="filter-tab-dash" onclick="filterEvents(event, 'Kuliner')" data-category="Kuliner">
            <span class="tab-emoji">🍜</span>
            <span class="tab-text">Kuliner</span>
            <span class="tab-count">
                <?php 
                $kuliner = array_filter($events ?? [], fn($e) => strtolower($e['category']) === strtolower('Kuliner'));
                echo count($kuliner);
                ?>
            </span>
        </button>
    </div>

    <!-- Events Grid -->
    <div class="events-grid-premium">
        <?php if (!empty($events) && is_array($events)): ?>
            <?php foreach ($events as $index => $event): ?>
                <?php 
                // Cek apakah event ini sudah difavoritkan
                $isFavorited = in_array($event['id'], $favoriteIds ?? []);
                ?>
                <div class="event-card-premium" 
                     data-category="<?= esc($event['category']) ?>"
                     data-title="<?= strtolower(esc($event['title'])) ?>"
                     data-location="<?= strtolower(esc($event['location'])) ?>"
                     style="--card-index: <?= $index ?>">
                    
                    <!-- Card Image with Overlay -->
                    <div class="event-card-image-wrapper">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?= base_url($event['image']) ?>" alt="<?= esc($event['title']) ?>" class="event-card-img-premium">
                        <?php else: ?>
                            <div class="event-card-img-placeholder-premium">
                                <span class="placeholder-icon"><?= esc($event['icon'] ?? '🎉') ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Gradient Overlay -->
                        <div class="image-overlay"></div>
                        
                        <!-- FAVORITE BUTTON - NEW! -->
                        <button class="btn-favorite <?= $isFavorited ? 'favorited' : '' ?>" 
                                onclick="toggleFavorite(<?= $event['id'] ?>, this)"
                                data-event-id="<?= $event['id'] ?>"
                                title="<?= $isFavorited ? 'Hapus dari favorit' : 'Tambah ke favorit' ?>">
                            <span class="heart-icon"><?= $isFavorited ? '❤️' : '🤍' ?></span>
                        </button>
                        
                        <!-- Quick Info Overlay (on hover) -->
                        <div class="quick-info-overlay">
                            <div class="quick-info-item">
                                <span class="quick-icon">🎫</span>
                                <span><?= number_format($event['available_tickets']) ?> tersedia</span>
                            </div>
                            <div class="quick-info-item">
                                <span class="quick-icon">📅</span>
                                <span><?= date('d M Y', strtotime($event['date'])) ?></span>
                            </div>
                        </div>
                        
                        <!-- Category Badge on Image -->
                        <div class="category-badge-float"><?= esc($event['category']) ?></div>
                        
                        <!-- Tickets Alert (if low stock) -->
                        <?php if ($event['available_tickets'] <= 10): ?>
                        <div class="low-stock-badge">
                            ⚠️ Hampir Habis!
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Card Content -->
                    <div class="event-card-content-premium">
                        <h3 class="event-title-premium"><?= esc($event['title']) ?></h3>
                        
                        <div class="event-info-grid">
                            <div class="info-item-compact">
                                <span class="info-icon-small">📅</span>
                                <span class="info-text"><?= date('d M Y', strtotime($event['date'])) ?></span>
                            </div>
                            <div class="info-item-compact">
                                <span class="info-icon-small">📍</span>
                                <span class="info-text"><?= esc($event['location']) ?></span>
                            </div>
                            <div class="info-item-compact">
                                <span class="info-icon-small">🎫</span>
                                <span class="info-text"><?= number_format($event['available_tickets']) ?> tiket</span>
                            </div>
                        </div>
                        
                        <!-- Description Preview (Optional) -->
                        <?php if (!empty($event['description'])): ?>
                        <p class="event-description-preview">
                            <?= substr(strip_tags($event['description']), 0, 80) ?>...
                        </p>
                        <?php endif; ?>
                        
                        <!-- Footer with Price & Button -->
                        <div class="event-card-footer-premium">
                            <div class="price-section-premium">
                                <div class="price-label-small">Harga</div>
                                <div class="price-amount-premium">Rp <?= number_format($event['price'], 0, ',', '.') ?></div>
                            </div>
                            <a href="<?= base_url('user/booking/' . $event['id']) ?>" class="btn-book-premium">
                                <span class="btn-icon-premium">🎫</span>
                                <span class="btn-text-premium">Book Now</span>
                                <span class="btn-arrow">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state-dashboard">
                <div class="empty-animation">
                    <div class="empty-icon-big">🎭</div>
                </div>
                <h3>Belum Ada Event Tersedia</h3>
                <p>Tunggu event menarik segera hadir!</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- No Results Message (Hidden by default) -->
    <div class="no-results-message" id="noResults" style="display:none">
        <div class="no-results-icon">🔍</div>
        <h3>Tidak ada event yang cocok</h3>
        <p>Coba gunakan kata kunci lain atau ubah filter kategori</p>
        <button class="btn-secondary btn-large" onclick="clearSearch(); document.querySelector('.filter-tab-dash.active').click()">
            Reset Pencarian
        </button>
    </div>
</div>

<script>
// ===== FAVORITE FUNCTIONALITY =====
function toggleFavorite(eventId, buttonElement) {
    // Prevent event bubbling
    event.stopPropagation();
    
    // Send AJAX request
    fetch('<?= base_url('user/favorite/toggle') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `event_id=${eventId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            const heartIcon = buttonElement.querySelector('.heart-icon');
            
            if (data.favorited) {
                buttonElement.classList.add('favorited');
                heartIcon.textContent = '❤️';
                buttonElement.title = 'Hapus dari favorit';
                
                // Animation: scale up
                buttonElement.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    buttonElement.style.transform = 'scale(1)';
                }, 300);
            } else {
                buttonElement.classList.remove('favorited');
                heartIcon.textContent = '🤍';
                buttonElement.title = 'Tambah ke favorit';
            }
            
            // Show toast notification
            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan. Silakan coba lagi.');
    });
}

// Toast Notification Function
function showToast(type, message) {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
        <div class="toast-icon">${type === 'success' ? '✓' : '✗'}</div>
        <div class="toast-content">
            <div class="toast-title">${type === 'success' ? 'Berhasil' : 'Gagal'}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ===== EXISTING FUNCTIONS =====
// Search Events
function searchEvents() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const clearBtn = document.getElementById('clearSearch');
    const cards = document.querySelectorAll('.event-card-premium');
    const noResults = document.getElementById('noResults');
    const searchInfo = document.getElementById('searchInfo');
    let visibleCount = 0;
    
    clearBtn.style.display = searchTerm ? 'block' : 'none';
    
    cards.forEach(card => {
        const title = card.dataset.title;
        const location = card.dataset.location;
        const category = card.dataset.category.toLowerCase();
        
        if (title.includes(searchTerm) || location.includes(searchTerm) || category.includes(searchTerm)) {
            card.style.display = 'block';
            setTimeout(() => card.style.opacity = '1', 10);
            visibleCount++;
        } else {
            card.style.opacity = '0';
            setTimeout(() => card.style.display = 'none', 300);
        }
    });
    
    if (searchTerm && visibleCount === 0) {
        noResults.style.display = 'flex';
        searchInfo.textContent = '';
    } else if (searchTerm && visibleCount > 0) {
        noResults.style.display = 'none';
        searchInfo.textContent = `Menampilkan ${visibleCount} event untuk "${searchTerm}"`;
    } else {
        noResults.style.display = 'none';
        searchInfo.textContent = '';
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').style.display = 'none';
    document.getElementById('searchInfo').textContent = '';
    searchEvents();
}

function filterEvents(event, category) {
    document.querySelectorAll('.filter-tab-dash').forEach(tab => {
        tab.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    document.getElementById('searchInput').value = '';
    document.getElementById('clearSearch').style.display = 'none';
    document.getElementById('searchInfo').textContent = '';
    
    const cards = document.querySelectorAll('.event-card-premium');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    cards.forEach((card, index) => {
        card.style.setProperty('--card-index', index);
        
        if (category === 'all') {
            card.style.display = 'block';
            setTimeout(() => card.style.opacity = '1', 10);
            visibleCount++;
        } else {
            if (card.dataset.category.toLowerCase() === category.toLowerCase()) {
                card.style.display = 'block';
                setTimeout(() => card.style.opacity = '1', 10);
                visibleCount++;
            } else {
                card.style.opacity = '0';
                setTimeout(() => card.style.display = 'none', 300);
            }
        }
    });
    
    noResults.style.display = visibleCount === 0 ? 'flex' : 'none';
}

// Auto-hide toast notifications
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.toast-notification');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });
});

// Entrance animations
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.event-card-premium');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<?= $this->include('templates/footer') ?>