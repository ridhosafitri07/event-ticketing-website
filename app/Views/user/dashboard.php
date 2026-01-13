<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<div class="container">
    <div class="dashboard-header">
        <h1>🎉 Temukan Event Favoritmu!</h1>
        <p>Jelajahi berbagai event menarik dan booking tiketmu sekarang</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="success-box"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="error-box"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterEvents(event, 'all')">Semua</button>
        <button class="filter-tab" onclick="filterEvents(event, 'Musik')">🎵 Musik</button>
        <button class="filter-tab" onclick="filterEvents(event, 'Olahraga')">⚽ Olahraga</button>
        <button class="filter-tab" onclick="filterEvents(event, 'Edukasi')">📚 Edukasi</button>
        <button class="filter-tab" onclick="filterEvents(event, 'Teknologi')">💻 Teknologi</button>
        <button class="filter-tab" onclick="filterEvents(event, 'Kuliner')">🍜 Kuliner</button>
    </div>

    <div class="events-grid">
        <?php if (!empty($events) && is_array($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card" data-category="<?= esc($event['category']) ?>">
                    <?php if (!empty($event['image'])): ?>
                        <img src="<?= base_url($event['image']) ?>" alt="<?= esc($event['title']) ?>" class="event-card-image">
                    <?php else: ?>
                        <div class="event-card-image-placeholder">
                            <span style="font-size: 64px;"><?= esc($event['icon'] ?? '🎉') ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="event-card-content">
                        <span class="category-badge"><?= esc($event['category']) ?></span>
                        <h3><?= esc($event['title']) ?></h3>
                        
                        <div class="event-info">
                            <div class="info-row">
                                <span class="info-icon">📅</span>
                                <span><?= date('d M Y', strtotime($event['date'])) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-icon">📍</span>
                                <span><?= esc($event['location']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-icon">🎫</span>
                                <span><?= number_format($event['available_tickets']) ?> tiket</span>
                            </div>
                        </div>
                        
                        <div class="event-card-footer">
                            <div class="price-info">
                                <span class="price-label">Harga</span>
                                <span class="price-amount">Rp <?= number_format($event['price'], 0, ',', '.') ?></span>
                            </div>
                            <a href="<?= base_url('user/booking/' . $event['id']) ?>" class="btn-book">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🎭</div>
                <h3>Belum ada event tersedia</h3>
                <p>Tunggu event menarik segera hadir!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterEvents(e, category) {
    const cards = document.querySelectorAll('.event-card');
    const buttons = document.querySelectorAll('.filter-tab');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');
    
    cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

<?= $this->include('templates/footer') ?>
