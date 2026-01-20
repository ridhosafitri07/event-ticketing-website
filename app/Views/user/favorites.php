<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container">
    <!-- Favorites Header -->
    <div class="favorites-header">
        <div class="favorites-header-content">
            <div class="header-icon-large">❤️</div>
            <div class="header-text">
                <h1>Event Favoritku</h1>
                <p>Koleksi event yang kamu sukai - <?= $totalFavorites ?> event</p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="favorites-actions">
            <a href="<?= base_url('user/dashboard') ?>" class="btn-secondary-outline">
                <span>🔍</span>
                <span>Cari Event Lain</span>
            </a>
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

    <?php if (!empty($favorites) && is_array($favorites)): ?>
        <!-- Favorites Grid -->
        <div class="favorites-grid">
            <?php foreach ($favorites as $index => $event): ?>
                <div class="favorite-card" style="--card-index: <?= $index ?>">
                    <!-- Card Image -->
                    <div class="favorite-card-image-wrapper">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?= base_url($event['image']) ?>" alt="<?= esc($event['title']) ?>" class="favorite-card-img">
                        <?php else: ?>
                            <div class="favorite-card-img-placeholder">
                                <span class="placeholder-icon-large"><?= esc($event['icon'] ?? '🎉') ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Gradient Overlay -->
                        <div class="favorite-card-overlay"></div>
                        
                        <!-- Remove Favorite Button -->
                        <button class="btn-remove-favorite" 
                                onclick="removeFavorite(<?= $event['id'] ?>, this.closest('.favorite-card'))"
                                title="Hapus dari favorit">
                            <span>❌</span>
                        </button>
                        
                        <!-- Category Badge -->
                        <div class="favorite-category-badge"><?= esc($event['category']) ?></div>
                        
                        <!-- Quick Info Overlay -->
                        <div class="favorite-quick-info">
                            <div class="favorite-info-item">
                                <span class="info-icon">🎫</span>
                                <span><?= number_format($event['available_tickets']) ?> tiket</span>
                            </div>
                            <div class="favorite-info-item">
                                <span class="info-icon">💰</span>
                                <span>Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Content -->
                    <div class="favorite-card-content">
                        <h3 class="favorite-event-title"><?= esc($event['title']) ?></h3>
                        
                        <div class="favorite-event-meta">
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span class="meta-text"><?= date('d M Y', strtotime($event['date'])) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">📍</span>
                                <span class="meta-text"><?= esc($event['location']) ?></span>
                            </div>
                        </div>
                        
                        <?php if (!empty($event['description'])): ?>
                        <p class="favorite-event-description">
                            <?= substr(strip_tags($event['description']), 0, 100) ?>...
                        </p>
                        <?php endif; ?>
                        
                        <!-- Card Actions -->
                        <div class="favorite-card-actions">
                            <div class="favorite-price">
                                <span class="price-label">Harga</span>
                                <span class="price-value">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                            </div>
                            <a href="<?= base_url('user/booking/' . $event['id']) ?>" class="btn-book-favorite">
                                <span>🎫</span>
                                <span>Book Now</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Favorites Summary -->
        <div class="favorites-summary">
            <div class="summary-card">
                <div class="summary-icon">📊</div>
                <div class="summary-content">
                    <div class="summary-label">Total Event Favorit</div>
                    <div class="summary-value"><?= $totalFavorites ?> event</div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon">💰</div>
                <div class="summary-content">
                    <div class="summary-label">Total Estimasi Harga</div>
                    <div class="summary-value">
                        Rp <?php 
                            $totalPrice = array_sum(array_column($favorites, 'price'));
                            echo number_format($totalPrice, 0, ',', '.');
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="summary-icon">🎯</div>
                <div class="summary-content">
                    <div class="summary-label">Kategori Terfavorit</div>
                    <div class="summary-value">
                        <?php 
                            $categories = array_count_values(array_column($favorites, 'category'));
                            arsort($categories);
                            echo esc(array_key_first($categories) ?? 'Belum ada');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Empty State -->
        <div class="favorites-empty-state">
            <div class="empty-animation-heart">
                <div class="empty-heart-icon">💔</div>
                <div class="empty-heart-particles">
                    <span class="particle">✨</span>
                    <span class="particle">✨</span>
                    <span class="particle">✨</span>
                    <span class="particle">✨</span>
                </div>
            </div>
            <h3 class="empty-title">Belum Ada Event Favorit</h3>
            <p class="empty-description">
                Yuk mulai tambahkan event-event favoritmu! Klik tombol ❤️ di event yang kamu suka 
                untuk menyimpannya di sini.
            </p>
            <a href="<?= base_url('user/dashboard') ?>" class="btn-primary-large">
                <span>🎉</span>
                <span>Jelajahi Event</span>
                <span>→</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Remove Favorite dengan Animation
function removeFavorite(eventId, cardElement) {
    // Confirm dialog
    if (!confirm('Yakin ingin menghapus event ini dari favorit?')) {
        return;
    }
    
    // Animate out
    cardElement.style.transform = 'scale(0.8)';
    cardElement.style.opacity = '0';
    
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
            // Remove card from DOM
            setTimeout(() => {
                cardElement.remove();
                
                // Check if no more favorites
                const remainingCards = document.querySelectorAll('.favorite-card');
                if (remainingCards.length === 0) {
                    location.reload(); // Reload to show empty state
                }
            }, 300);
            
            showToast('success', data.message);
        } else {
            // Restore card if failed
            cardElement.style.transform = 'scale(1)';
            cardElement.style.opacity = '1';
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        cardElement.style.transform = 'scale(1)';
        cardElement.style.opacity = '1';
        showToast('error', 'Terjadi kesalahan. Silakan coba lagi.');
    });
}

// Toast Notification
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
    const cards = document.querySelectorAll('.favorite-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<?= $this->include('templates/footer') ?>