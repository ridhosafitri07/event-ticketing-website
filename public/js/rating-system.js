// ============================================
// EVENTKU - Rating System JavaScript
// ============================================

let selectedRating = 0;

// Open Rating Modal
function openRatingModal(eventId) {
    // Check if user can rate
    fetch(`${BASE_URL}/user/rating/can-rate/${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.can_rate) {
                showToastModern('error', data.reason);
                return;
            }
            
            document.getElementById('ratingEventId').value = eventId;
            document.getElementById('ratingModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            showToastModern('error', 'Gagal membuka form rating');
        });
}

// Close Rating Modal
function closeRatingModal() {
    document.getElementById('ratingModal').classList.remove('active');
    document.body.style.overflow = '';
    resetRatingForm();
}

// Select Rating
function selectRating(rating) {
    selectedRating = rating;
    document.getElementById('ratingValue').value = rating;
    
    // Update star buttons
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// Submit Rating
function submitRating(event) {
    event.preventDefault();

    if (selectedRating === 0) {
        showToastModern('error', 'Pilih rating terlebih dahulu');
        return;
    }

    const formData = new FormData(event.target);

    fetch(`${BASE_URL}/user/rating/submit`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToastModern('success', data.message);
            closeRatingModal();

            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToastModern('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToastModern('error', 'Gagal mengirim rating');
    });
}

// Reset Rating Form
function resetRatingForm() {
    selectedRating = 0;
    document.getElementById('ratingValue').value = '';
    document.querySelectorAll('.star-btn').forEach(star => {
        star.classList.remove('active');
    });
    document.querySelector('textarea[name="review"]').value = '';
    document.querySelector('input[name="is_anonymous"]').checked = false;
}

// Load Event Ratings (for detail page)
function loadEventRatings(eventId) {
    fetch(`${BASE_URL}/user/rating/event/${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRatings(data.ratings, data.stats);
            }
        })
        .catch(error => {
            console.error('Error loading ratings:', error);
        });
}

// Display Ratings
function displayRatings(ratings, stats) {
    // Update rating summary
    const summaryEl = document.getElementById('ratingSummary');
    if (summaryEl && stats) {
        summaryEl.innerHTML = `
            <div class="rating-summary">
                <div class="avg-rating">
                    <span class="avg-number">${stats.average.toFixed(1)}</span>
                    <div class="stars">${generateStars(stats.average)}</div>
                    <span class="total-ratings">${stats.total} ulasan</span>
                </div>
                <div class="rating-distribution">
                    ${Object.entries(stats.percentage).reverse().map(([star, percent]) => `
                        <div class="dist-row">
                            <span>${star}⭐</span>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${percent}%"></div>
                            </div>
                            <span>${stats.distribution[star]}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    // Display individual ratings
    const listEl = document.getElementById('ratingsList');
    if (listEl && ratings) {
        listEl.innerHTML = ratings.map(rating => `
            <div class="rating-item">
                <div class="rating-header">
                    <strong>${rating.is_anonymous ? 'Anonymous' : rating.user_name}</strong>
                    <div class="stars-small">${generateStars(rating.rating)}</div>
                </div>
                <p class="rating-review">${rating.review || '<em>Tidak ada review</em>'}</p>
                <span class="rating-date">${formatDate(rating.created_at)}</span>
            </div>
        `).join('');
    }
}

// Generate Stars HTML
function generateStars(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        html += i <= Math.round(rating) ? '⭐' : '☆';
    }
    return html;
}

// Format Date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

// Close modal when clicking outside
document.getElementById('ratingModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRatingModal();
    }
});

// Toast Animation Styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
`;
document.head.appendChild(style);