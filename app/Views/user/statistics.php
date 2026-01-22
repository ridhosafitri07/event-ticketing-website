<?php
$this->setVar('showNavbar', true);
$this->setVar('bodyClass', 'dashboard-body');
?>
<?= $this->include('templates/header') ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="container">
    <!-- Statistics Header -->
    <div class="statistics-header">
        <div class="statistics-header-content">
            <div class="header-icon-stats">📊</div>
            <div class="header-text-stats">
                <h1>Dashboard Statistik</h1>
                <p>Analisis lengkap aktivitas booking & spending kamu</p>
            </div>
        </div>
        <div class="header-period">
            <span class="period-icon">📅</span>
            <span class="period-text">Last 6 Months</span>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="stats-cards-grid">
        <div class="stat-card-modern">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                <span>🎫</span>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Booking</div>
                <div class="stat-card-value"><?= number_format($stats['total_bookings']) ?></div>
                <div class="stat-card-change positive">
                    <span>↗</span> <?= $stats['confirmed_bookings'] ?> lunas
                </div>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                <span>💰</span>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Spending</div>
                <div class="stat-card-value">Rp <?= number_format($stats['total_spending'], 0, ',', '.') ?></div>
                <div class="stat-card-change">
                    Dari <?= $stats['confirmed_bookings'] ?> booking
                </div>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                <span>🎟️</span>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Total Tiket</div>
                <div class="stat-card-value"><?= number_format($stats['total_tickets']) ?></div>
                <div class="stat-card-change">
                    Tiket terbeli
                </div>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                <span>⏳</span>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Status Booking</div>
                <div class="stat-card-value"><?= $stats['pending_bookings'] ?></div>
                <div class="stat-card-change">
                    Menunggu konfirmasi
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Booking Timeline Chart -->
        <div class="chart-card large">
            <div class="chart-card-header">
                <h3>📈 Booking Timeline</h3>
                <p>Riwayat booking 6 bulan terakhir</p>
            </div>
            <div class="chart-container">
                <canvas id="bookingTimelineChart"></canvas>
            </div>
        </div>

        <!-- Monthly Spending Chart -->
        <div class="chart-card large">
            <div class="chart-card-header">
                <h3>💸 Monthly Spending</h3>
                <p>Total pengeluaran per bulan</p>
            </div>
            <div class="chart-container">
                <canvas id="spendingChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="chart-card medium">
            <div class="chart-card-header">
                <h3>🎯 Category Distribution</h3>
                <p>Event berdasarkan kategori</p>
            </div>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Payment Method Distribution -->
        <div class="chart-card medium">
            <div class="chart-card-header">
                <h3>💳 Payment Methods</h3>
                <p>Metode pembayaran favorit</p>
            </div>
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Events Table -->
    <?php if (!empty($topEvents)): ?>
    <div class="top-events-section">
        <div class="section-header">
            <h3>🏆 Top Events</h3>
            <p>Event yang paling sering kamu booking</p>
        </div>
        <div class="top-events-table">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Event Name</th>
                        <th>Booking Count</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topEvents as $index => $event): ?>
                    <tr>
                        <td>
                            <div class="rank-badge rank-<?= $index + 1 ?>">
                                <?= $index + 1 ?>
                            </div>
                        </td>
                        <td class="event-name"><?= esc($event['event_title']) ?></td>
                        <td>
                            <span class="badge-count"><?= $event['booking_count'] ?>x</span>
                        </td>
                        <td class="amount">Rp <?= number_format($event['total_spent'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Insights Section -->
    <div class="insights-grid">
        <div class="insight-card">
            <div class="insight-icon">🎨</div>
            <div class="insight-content">
                <h4>Kategori Favorit</h4>
                <p class="insight-value">
                    <?php 
                    $topCategory = array_key_first($categoryDistribution ?? []);
                    echo $topCategory ? esc($topCategory) : 'Belum ada';
                    ?>
                </p>
            </div>
        </div>

        <div class="insight-card">
            <div class="insight-icon">💰</div>
            <div class="insight-content">
                <h4>Rata-rata Spending</h4>
                <p class="insight-value">
                    Rp <?php 
                    $avgSpending = $stats['confirmed_bookings'] > 0 
                        ? $stats['total_spending'] / $stats['confirmed_bookings'] 
                        : 0;
                    echo number_format($avgSpending, 0, ',', '.');
                    ?>
                </p>
            </div>
        </div>

        <div class="insight-card">
            <div class="insight-icon">📊</div>
            <div class="insight-content">
                <h4>Success Rate</h4>
                <p class="insight-value">
                    <?php 
                    $successRate = $stats['total_bookings'] > 0 
                        ? round(($stats['confirmed_bookings'] / $stats['total_bookings']) * 100) 
                        : 0;
                    echo $successRate;
                    ?>%
                </p>
            </div>
        </div>

        <div class="insight-card">
            <div class="insight-icon">🎯</div>
            <div class="insight-content">
                <h4>Most Active Month</h4>
                <p class="insight-value">
                    <?php 
                    $mostActiveMonth = array_key_first($bookingTimeline ?? []);
                    echo $mostActiveMonth ?? 'Belum ada';
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js Global Config
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(124, 58, 237, 0.2)';
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";

// Booking Timeline Chart
const timelineCtx = document.getElementById('bookingTimelineChart');
const timelineData = <?= json_encode($bookingTimeline) ?>;
const timelineLabels = Object.keys(timelineData);
const timelineConfirmed = timelineLabels.map(m => timelineData[m].confirmed);
const timelinePending = timelineLabels.map(m => timelineData[m].pending);
const timelineCancelled = timelineLabels.map(m => timelineData[m].cancelled);

new Chart(timelineCtx, {
    type: 'line',
    data: {
        labels: timelineLabels,
        datasets: [
            {
                label: 'Lunas',
                data: timelineConfirmed,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Pending',
                data: timelinePending,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Dibatalkan',
                data: timelineCancelled,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Monthly Spending Chart
const spendingCtx = document.getElementById('spendingChart');
const spendingData = <?= json_encode($monthlySpending) ?>;
const spendingLabels = Object.keys(spendingData);
const spendingValues = Object.values(spendingData);

new Chart(spendingCtx, {
    type: 'bar',
    data: {
        labels: spendingLabels,
        datasets: [{
            label: 'Total Spending (Rp)',
            data: spendingValues,
            backgroundColor: 'rgba(124, 58, 237, 0.8)',
            borderColor: '#7c3aed',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart');
const categoryData = <?= json_encode($categoryDistribution) ?>;
const categoryLabels = Object.keys(categoryData);
const categoryValues = Object.values(categoryData);

new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryValues,
            backgroundColor: [
                '#6366f1',
                '#ec4899',
                '#10b981',
                '#f59e0b',
                '#8b5cf6',
                '#06b6d4'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Payment Method Chart
const paymentCtx = document.getElementById('paymentChart');
const paymentData = <?= json_encode($paymentMethods) ?>;

new Chart(paymentCtx, {
    type: 'pie',
    data: {
        labels: ['Manual Transfer', 'Midtrans'],
        datasets: [{
            data: [paymentData.manual_transfer, paymentData.midtrans],
            backgroundColor: ['#8b5cf6', '#ec4899'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>

<?= $this->include('templates/footer') ?>