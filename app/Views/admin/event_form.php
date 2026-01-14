<?php
$isEdit = isset($event);
$title = $title ?? (($isEdit ? 'Edit Event' : 'Tambah Event') . ' - EventKu');
$extraStyles = <<<CSS
    .admin-container { max-width: 980px; margin: 0 auto; }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 800;
        margin-bottom: 18px;
        transition: all 0.2s;
    }
    .back-link:hover { color: var(--primary-dark); }
    .form-card {
        background: white;
        border-radius: 14px;
        padding: 28px;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .form-card h1 { font-size: 20px; color: var(--gray-900); margin-bottom: 18px; }
    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 800;
        color: var(--gray-700);
        font-size: 13px;
    }
    .form-group label .required { color: var(--danger); }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--gray-300);
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }
    .form-group textarea { resize: vertical; min-height: 110px; font-family: inherit; }
    .form-group small { display: block; margin-top: 6px; color: var(--gray-500); font-size: 13px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .image-preview {
        margin-top: 12px;
        padding: 14px;
        background: var(--gray-50);
        border: 1px dashed var(--gray-300);
        border-radius: 12px;
        text-align: center;
    }
    .image-preview img { max-width: 100%; max-height: 220px; border-radius: 10px; }
    .checkbox-group { display: flex; align-items: center; gap: 10px; }
    .checkbox-group input[type="checkbox"] { width: auto; margin: 0; }
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid var(--gray-100);
    }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }
CSS;

$this->setVar('title', $title);
$this->setVar('activePage', 'events');
$this->setVar('extraStyles', $extraStyles);
?>

<?= $this->include('admin/_partials/layout_top') ?>

<div class="admin-container">
    <a href="<?= base_url('admin/events') ?>" class="back-link">← Kembali ke Events</a>

    <div class="form-card">
        <h1><?= $isEdit ? '✏️ Edit Event' : '➕ Tambah Event Baru' ?></h1>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <strong>Ada kesalahan:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/event/save') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <?php if ($isEdit): ?>
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Judul Event <span class="required">*</span></label>
                <input type="text" 
                       name="title" 
                       placeholder="Contoh: Java Jazz Festival 2025" 
                       value="<?= old('title', $event['title'] ?? '') ?>"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Event <span class="required">*</span></label>
                    <input type="text" 
                           name="date" 
                           placeholder="Contoh: 15-17 Maret 2025" 
                           value="<?= old('date', $event['date'] ?? '') ?>"
                           required>
                    <small>Format bebas, contoh: "20 April 2025" atau "15-17 Maret 2025"</small>
                </div>

                <div class="form-group">
                    <label>Kategori <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="">Pilih Kategori</option>
                        <option value="musik" <?= (old('category', $event['category'] ?? '') == 'musik') ? 'selected' : '' ?>>🎵 Musik</option>
                        <option value="olahraga" <?= (old('category', $event['category'] ?? '') == 'olahraga') ? 'selected' : '' ?>>⚽ Olahraga</option>
                        <option value="edukasi" <?= (old('category', $event['category'] ?? '') == 'edukasi') ? 'selected' : '' ?>>📚 Edukasi</option>
                        <option value="teknologi" <?= (old('category', $event['category'] ?? '') == 'teknologi') ? 'selected' : '' ?>>💻 Teknologi</option>
                        <option value="kuliner" <?= (old('category', $event['category'] ?? '') == 'kuliner') ? 'selected' : '' ?>>🍜 Kuliner</option>
                        <option value="teater" <?= (old('category', $event['category'] ?? '') == 'teater') ? 'selected' : '' ?>>🎭 Teater</option>
                        <option value="konser" <?= (old('category', $event['category'] ?? '') == 'konser') ? 'selected' : '' ?>>🎸 Konser</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Lokasi <span class="required">*</span></label>
                <input type="text" 
                       name="location" 
                       placeholder="Contoh: JIExpo Kemayoran, Jakarta" 
                       value="<?= old('location', $event['location'] ?? '') ?>"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Harga Tiket (Rp) <span class="required">*</span></label>
                    <input type="number" 
                           name="price" 
                           placeholder="250000" 
                           min="0"
                           value="<?= old('price', $event['price'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Tiket Tersedia <span class="required">*</span></label>
                    <input type="number" 
                           name="available_tickets" 
                           placeholder="100" 
                           min="0"
                           value="<?= old('available_tickets', $event['available_tickets'] ?? '') ?>"
                           required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Icon Emoji</label>
                    <input type="text" 
                           name="icon" 
                           placeholder="🎉" 
                           maxlength="10"
                           value="<?= old('icon', $event['icon'] ?? '🎉') ?>">
                    <small>Emoji yang akan ditampilkan (opsional)</small>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               id="is_active"
                               <?= old('is_active', $event['is_active'] ?? 1) == 1 ? 'checked' : '' ?>>
                        <label for="is_active" style="margin: 0;">Event Aktif (tampil di halaman user)</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Gambar Event</label>
                <input type="file" 
                       name="image" 
                       accept="image/jpeg,image/jpg,image/png"
                       onchange="previewImage(this)">
                <small>Format: JPG, PNG. Maksimal 2MB. <?= $isEdit ? 'Kosongkan jika tidak ingin mengubah gambar.' : '' ?></small>
                
                <?php if ($isEdit && !empty($event['image'])): ?>
                    <div class="image-preview">
                        <p style="margin-bottom: 12px; color: #64748b; font-size: 13px;">Gambar saat ini:</p>
                        <img src="<?= base_url($event['image']) ?>" alt="Current Image" id="currentImage">
                    </div>
                <?php endif; ?>
                
                <div class="image-preview" id="imagePreview" style="display: none;">
                    <p style="margin-bottom: 12px; color: #64748b; font-size: 13px;">Preview gambar baru:</p>
                    <img id="previewImg" src="" alt="Preview">
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Event</label>
                <textarea name="description" 
                          placeholder="Deskripsi singkat tentang event..."><?= old('description', $event['description'] ?? '') ?></textarea>
                <small>Opsional. Deskripsi detail event yang akan ditampilkan di halaman booking.</small>
            </div>

            <div class="form-actions">
                <a href="<?= base_url('admin/events') ?>" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? '💾 Update Event' : '➕ Tambah Event' ?>
                </button>
            </div>
        </form>
    </div>
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const currentImage = document.getElementById('currentImage');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            
            if (currentImage) {
                currentImage.parentElement.style.display = 'none';
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->include('admin/_partials/layout_bottom') ?>
