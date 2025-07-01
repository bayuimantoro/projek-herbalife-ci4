<?= $this->extend('admin/layout/template') ?>

<?= $this->section('content') ?>
<h1 class="h2 pb-2 mb-3 border-bottom"><?= esc($title) ?></h1>

<?php
// Menampilkan error validasi gabungan
if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <p><strong>Mohon periksa inputan Anda:</strong></p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php elseif (isset($validation) && $validation->getErrors()): // Fallback untuk error produk utama saja jika $errors tidak ada ?>
     <div class="alert alert-danger">
        <p><strong>Mohon periksa inputan Anda:</strong></p>
        <ul>
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url(route_to('admin_produk_update', $produk['id'])) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <!-- Field Produk Utama -->
    <div class="mb-3">
        <label for="nama_produk" class="form-label">Nama Produk</label>
        <input type="text" class="form-control <?= (isset($errors['nama_produk'])) ? 'is-invalid' : '' ?>" id="nama_produk" name="nama_produk" value="<?= old('nama_produk', $produk['nama_produk']) ?>" required>
        <?php if (isset($errors['nama_produk'])): ?><div class="invalid-feedback"><?= esc($errors['nama_produk']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="kategori" class="form-label">Kategori</label>
        <select class="form-select <?= (isset($errors['kategori'])) ? 'is-invalid' : '' ?>" id="kategori" name="kategori">
            <option value="">-- Pilih Kategori --</option>
            <option value="Menurunkan Berat Badan" <?= (old('kategori', $produk['kategori']) == 'Menurunkan Berat Badan') ? 'selected' : '' ?>>Menurunkan Berat Badan</option>
            <option value="Menambah Berat Badan" <?= (old('kategori', $produk['kategori']) == 'Menambah Berat Badan') ? 'selected' : '' ?>>Menambah Berat Badan</option>
            <option value="Nutrisi Umum" <?= (old('kategori', $produk['kategori']) == 'Nutrisi Umum') ? 'selected' : '' ?>>Nutrisi Umum</option>
            <option value="Energi & Kebugaran" <?= (old('kategori', $produk['kategori']) == 'Energi & Kebugaran') ? 'selected' : '' ?>>Energi & Kebugaran</option>
        </select>
        <?php if (isset($errors['kategori'])): ?><div class="invalid-feedback"><?= esc($errors['kategori']) ?></div><?php endif; ?>
    </div>

     <div class="mb-3">
        <label for="harga" class="form-label">Harga Produk Utama (Rp)</label>
        <input type="number" class="form-control <?= (isset($errors['harga'])) ? 'is-invalid' : '' ?>" id="harga" name="harga" value="<?= old('harga', $produk['harga']) ?>" required step="1000">
        <small class="form-text text-muted">Harga dasar produk. Harga per varian bisa diatur di bawah.</small>
        <?php if (isset($errors['harga'])): ?><div class="invalid-feedback"><?= esc($errors['harga']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <textarea class="form-control <?= (isset($errors['deskripsi'])) ? 'is-invalid' : '' ?>" id="deskripsi" name="deskripsi" rows="3"><?= old('deskripsi', $produk['deskripsi']) ?></textarea>
        <?php if (isset($errors['deskripsi'])): ?><div class="invalid-feedback"><?= esc($errors['deskripsi']) ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="gambar" class="form-label">Ganti Gambar Produk (Opsional)</label>
        <input class="form-control <?= (isset($errors['gambar'])) ? 'is-invalid' : '' ?>" type="file" id="gambar" name="gambar">
        <div class="form-text">Biarkan kosong jika tidak ingin mengganti gambar. Maksimal 2MB, format JPG, JPEG, PNG.</div>
        <?php if (isset($errors['gambar'])): ?><div class="invalid-feedback d-block"><?= esc($errors['gambar']) ?></div><?php endif; ?>
    </div>

    <?php if (!empty($produk['gambar'])): ?>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" name="hapus_gambar_lama" value="1" id="hapus_gambar_lama" <?= old('hapus_gambar_lama') ? 'checked' : '' ?>>
        <label class="form-check-label" for="hapus_gambar_lama">Hapus gambar saat ini</label>
    </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Gambar Saat Ini:</label><br>
        <?php if (!empty($produk['gambar'])): ?>
            <img id="preview_gambar_lama" src="<?= base_url('uploads/produk/' . esc($produk['gambar'])) ?>" alt="<?= esc($produk['nama_produk']) ?>" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
        <?php else: ?>
            <p>Tidak ada gambar.</p>
        <?php endif; ?>
         <img id="preview_gambar_baru" src="#" alt="Preview Gambar Baru" class="mb-2 ms-2" style="max-width: 200px; max-height: 200px; display: none;" />
    </div>

        <!-- ... field lain ... -->
    <div class="mb-3">
        <label for="status" class="form-label">Status Produk</label>
        <select class="form-select <?= (isset($errors['status'])) ? 'is-invalid' : '' ?>" name="status" id="status">
            <option value="1" <?= (old('status', $produk['status']) == '1') ? 'selected' : '' ?>>Tersedia</option>
            <option value="0" <?= (old('status', $produk['status']) == '0') ? 'selected' : '' ?>>Habis</option>
        </select>
        <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= esc($errors['status']) ?></div><?php endif; ?>
    </div>
    <!-- ... field varian ... -->

    <hr>
    <h4>Varian Produk</h4>
    <div id="variants-container">
        <?php
        // Menampilkan varian yang sudah ada atau dari old data
        $variants_data = old('variants', $produk['variants'] ?? []);
        if (!empty($variants_data)):
            foreach ($variants_data as $index => $varian): ?>
                <div class="row g-3 mb-2 align-items-center variant-row">
                    <input type="hidden" name="variants[<?= $index ?>][id]" value="<?= esc($varian['id'] ?? '') ?>">
                    <div class="col-md-4">
                        <label for="variants_<?= $index ?>_nama_varian" class="form-label visually-hidden">Nama Varian</label>
                        <input type="text" class="form-control <?= (isset($errors["variants.{$index}.nama_varian"])) ? 'is-invalid' : '' ?>" name="variants[<?= $index ?>][nama_varian]" id="variants_<?= $index ?>_nama_varian" placeholder="Nama Varian (cth: Vanila, XL)" value="<?= esc($varian['nama_varian'] ?? '') ?>">
                        <?php if (isset($errors["variants.{$index}.nama_varian"])): ?><div class="invalid-feedback"><?= esc($errors["variants.{$index}.nama_varian"]) ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label for="variants_<?= $index ?>_harga_varian" class="form-label visually-hidden">Harga Varian</label>
                        <input type="number" class="form-control <?= (isset($errors["variants.{$index}.harga_varian"])) ? 'is-invalid' : '' ?>" name="variants[<?= $index ?>][harga_varian]" id="variants_<?= $index ?>_harga_varian" placeholder="Harga Varian (Rp)" value="<?= esc($varian['harga_varian'] ?? '') ?>" step="1000">
                        <?php if (isset($errors["variants.{$index}.harga_varian"])): ?><div class="invalid-feedback"><?= esc($errors["variants.{$index}.harga_varian"]) ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label for="variants_<?= $index ?>_stok_varian" class="form-label visually-hidden">Stok Varian</label>
                        <input type="number" class="form-control <?= (isset($errors["variants.{$index}.stok_varian"])) ? 'is-invalid' : '' ?>" name="variants[<?= $index ?>][stok_varian]" id="variants_<?= $index ?>_stok_varian" placeholder="Stok" value="<?= esc($varian['stok_varian'] ?? '0') ?>" step="1">
                        <?php if (isset($errors["variants.{$index}.stok_varian"])): ?><div class="invalid-feedback"><?= esc($errors["variants.{$index}.stok_varian"]) ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger remove-variant-row">Hapus</button>
                    </div>
                </div>
            <?php endforeach;
        else: ?>
             <p class="text-muted" id="no-variants-message">Belum ada varian untuk produk ini. Klik "Tambah Varian" untuk memulai.</p>
        <?php endif; ?>
    </div>
    <button type="button" id="add-variant-button" class="btn btn-sm btn-outline-success mt-2 mb-3">Tambah Varian</button>
    <hr>


    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= site_url(route_to('admin_produk_index')) ?>" class="btn btn-secondary">Batal</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const variantsContainer = document.getElementById('variants-container');
        const addVariantButton = document.getElementById('add-variant-button');
        let variantIndex = <?= count($variants_data) ?>;
        const noVariantsMessage = document.getElementById('no-variants-message');

        function createVariantRow(index) {
            const row = document.createElement('div');
            row.className = 'row g-3 mb-2 align-items-center variant-row';
            row.innerHTML = `
                <input type="hidden" name="variants[${index}][id]" value="">
                <div class="col-md-4">
                    <label for="variants_${index}_nama_varian" class="form-label visually-hidden">Nama Varian</label>
                    <input type="text" class="form-control" name="variants[${index}][nama_varian]" id="variants_${index}_nama_varian" placeholder="Nama Varian (cth: Vanila, XL)" required>
                </div>
                <div class="col-md-3">
                    <label for="variants_${index}_harga_varian" class="form-label visually-hidden">Harga Varian</label>
                    <input type="number" class="form-control" name="variants[${index}][harga_varian]" id="variants_${index}_harga_varian" placeholder="Harga Varian (Rp)" step="1000">
                </div>
                <div class="col-md-3">
                    <label for="variants_${index}_stok_varian" class="form-label visually-hidden">Stok Varian</label>
                    <input type="number" class="form-control" name="variants[${index}][stok_varian]" id="variants_${index}_stok_varian" placeholder="Stok" value="0" step="1">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-variant-row">Hapus</button>
                </div>
            `;
            return row;
        }

        addVariantButton.addEventListener('click', function () {
            if(noVariantsMessage) {
                noVariantsMessage.style.display = 'none';
            }
            const newRow = createVariantRow(variantIndex);
            variantsContainer.appendChild(newRow);
            variantIndex++;
        });

        variantsContainer.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-variant-row')) {
                e.target.closest('.variant-row').remove();
                 if (variantsContainer.querySelectorAll('.variant-row').length === 0 && noVariantsMessage) {
                    noVariantsMessage.style.display = 'block';
                }
            }
        });

        const inputGambar = document.getElementById('gambar');
        const previewGambarBaru = document.getElementById('preview_gambar_baru');
        const previewGambarLama = document.getElementById('preview_gambar_lama');
        const checkboxHapusGambar = document.getElementById('hapus_gambar_lama');

        if (inputGambar) {
            inputGambar.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if(previewGambarBaru) {
                            previewGambarBaru.src = e.target.result;
                            previewGambarBaru.style.display = 'inline-block';
                        }
                        if (checkboxHapusGambar) checkboxHapusGambar.checked = false;
                    }
                    reader.readAsDataURL(file);
                } else {
                    if(previewGambarBaru) {
                        previewGambarBaru.src = '#';
                        previewGambarBaru.style.display = 'none';
                    }
                }
            });
        }
        if (checkboxHapusGambar) {
            checkboxHapusGambar.addEventListener('change', function() {
                if (this.checked && inputGambar && inputGambar.files.length > 0) {
                    inputGambar.value = '';
                    if(previewGambarBaru) {
                        previewGambarBaru.src = '#';
                        previewGambarBaru.style.display = 'none';
                    }
                    // alert('Pilihan gambar baru telah dibatalkan karena Anda memilih untuk menghapus gambar.');
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>