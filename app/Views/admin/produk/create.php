<?= $this->extend('admin/layout/template') ?>

<?= $this->section('content') ?>
<h1 class="h2 pb-2 mb-3 border-bottom"><?= esc($title) ?></h1>

<?php if ($validation->getErrors()): ?>
    <div class="alert alert-danger">
        <p><strong>Mohon periksa inputan Anda:</strong></p>
        <ul>
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('admin/produk/store') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="nama_produk" class="form-label">Nama Produk</label>
        <input type="text" class="form-control <?= ($validation->hasError('nama_produk')) ? 'is-invalid' : '' ?>" id="nama_produk" name="nama_produk" value="<?= old('nama_produk') ?>" required>
        <?php if ($validation->hasError('nama_produk')): ?>
            <div class="invalid-feedback">
                <?= $validation->getError('nama_produk') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="kategori" class="form-label">Kategori</label>
        <select class="form-select <?= ($validation->hasError('kategori')) ? 'is-invalid' : '' ?>" id="kategori" name="kategori">
            <option value="">-- Pilih Kategori --</option>
            <option value="Menurunkan Berat Badan" <?= (old('kategori') == 'Menurunkan Berat Badan') ? 'selected' : '' ?>>Menurunkan Berat Badan</option>
            <option value="Menambah Berat Badan" <?= (old('kategori') == 'Menambah Berat Badan') ? 'selected' : '' ?>>Menambah Berat Badan</option>
            <option value="Nutrisi Umum" <?= (old('kategori') == 'Nutrisi Umum') ? 'selected' : '' ?>>Nutrisi Umum</option>
            <option value="Energi & Kebugaran" <?= (old('kategori') == 'Energi & Kebugaran') ? 'selected' : '' ?>>Energi & Kebugaran</option>
            <!-- Tambahkan kategori lain jika perlu -->
        </select>
         <?php if ($validation->hasError('kategori')): ?>
            <div class="invalid-feedback">
                <?= $validation->getError('kategori') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="harga" class="form-label">Harga (Rp)</label>
        <input type="number" class="form-control <?= ($validation->hasError('harga')) ? 'is-invalid' : '' ?>" id="harga" name="harga" value="<?= old('harga') ?>" required step="1000">
        <?php if ($validation->hasError('harga')): ?>
            <div class="invalid-feedback">
                <?= $validation->getError('harga') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <textarea class="form-control <?= ($validation->hasError('deskripsi')) ? 'is-invalid' : '' ?>" id="deskripsi" name="deskripsi" rows="3"><?= old('deskripsi') ?></textarea>
        <?php if ($validation->hasError('deskripsi')): ?>
            <div class="invalid-feedback">
                <?= $validation->getError('deskripsi') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="gambar" class="form-label">Gambar Produk</label>
        <input class="form-control <?= ($validation->hasError('gambar')) ? 'is-invalid' : '' ?>" type="file" id="gambar" name="gambar">
        <div class="form-text">Maksimal 2MB, format JPG, JPEG, PNG.</div>
        <?php if ($validation->hasError('gambar')): ?>
            <div class="invalid-feedback d-block"> <!-- d-block karena input file tidak otomatis menampilkan invalid-feedback -->
                <?= $validation->getError('gambar') ?>
            </div>
        <?php endif; ?>
    </div>

        <!-- ... field lain ... -->
    <div class="mb-3">
        <label for="status" class="form-label">Status Produk</label>
        <select class="form-select <?= (isset($errors['status'])) ? 'is-invalid' : '' ?>" name="status" id="status">
            <option value="1" <?= (old('status', '1') == '1') ? 'selected' : '' ?>>Tersedia</option>
            <option value="0" <?= (old('status') == '0') ? 'selected' : '' ?>>Habis</option>
        </select>
        <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= esc($errors['status']) ?></div><?php endif; ?>
    </div>
    <!-- ... field varian ... -->
    
    <img id="preview_gambar" src="#" alt="Preview Gambar" class="mb-2" style="max-width: 200px; max-height: 200px; display: none;" />


    <button type="submit" class="btn btn-primary">Simpan Produk</button>
    <a href="<?= site_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
</form>

<script>
    // Preview gambar sebelum upload
    const inputGambar = document.getElementById('gambar');
    const previewGambar = document.getElementById('preview_gambar');

    inputGambar.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewGambar.src = e.target.result;
                previewGambar.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewGambar.src = '#';
            previewGambar.style.display = 'none';
        }
    });
</script>

<?= $this->endSection() ?>