<?= $this->extend('admin/layout/template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= esc($title ?? 'Daftar Produk') ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= site_url(route_to('admin_produk_create')) ?>" class="btn btn-sm btn-success">
            Tambah Produk Baru
        </a>
    </div>
</div>

<!-- Form Pencarian dan Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form action="<?= site_url(route_to('admin_produk_index')) ?>" method="get" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label for="keyword" class="visually-hidden">Kata Kunci</label>
                <input type="text" class="form-control" name="keyword" id="keyword" placeholder="Cari nama atau deskripsi produk..." value="<?= esc($keyword ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="kategori_filter" class="visually-hidden">Kategori</label>
                <select class="form-select" name="kategori_filter" id="kategori_filter">
                    <option value="">-- Semua Kategori --</option>
                    <?php
                    $kategori_options = [
                        'Menurunkan Berat Badan' => 'Menurunkan Berat Badan',
                        'Menambah Berat Badan' => 'Menambah Berat Badan',
                        'Nutrisi Umum' => 'Nutrisi Umum',
                        'Energi & Kebugaran' => 'Energi & Kebugaran'
                    ];
                    foreach ($kategori_options as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= (isset($selectedKategori) && $selectedKategori == $value) ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status_filter" class="visually-hidden">Status</label>
                <select class="form-select" name="status_filter" id="status_filter">
                    <option value="">-- Semua Status --</option>
                    <option value="1" <?= (isset($selectedStatus) && $selectedStatus === '1') ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= (isset($selectedStatus) && $selectedStatus === '0') ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-2">Cari/Filter</button>
                <a href="<?= site_url(route_to('admin_produk_index')) ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
<!-- Akhir Form Pencarian dan Filter -->


<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Gambar</th>
                <th scope="col">Nama Produk / Varian</th>
                <th scope="col">Kategori</th>
                <th scope="col">Harga Utama</th>
                <th scope="col">Status</th> <!-- Kolom Baru Ditambahkan -->
                <th scope="col" style="min-width: 180px;">Aksi</th> <!-- Min-width untuk tombol aksi -->
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($produk) && is_array($produk)): ?>
                <?php
                $nomor_awal = 1;
                if (isset($pager) && $pager->getPageCount('produk_group') > 0) {
                    $nomor_awal = ($pager->getCurrentPage('produk_group') - 1) * $pager->getPerPage('produk_group') + 1;
                }
                $no = $nomor_awal;
                ?>
                <?php foreach ($produk as $item): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <?php if (!empty($item['gambar'])): ?>
                            <img src="<?= base_url('uploads/produk/' . esc($item['gambar'])) ?>" alt="<?= esc($item['nama_produk']) ?>" class="img-thumbnail-custom" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                        <?php else: ?>
                            <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= esc($item['nama_produk']) ?></strong>
                        <?php if (!empty($item['variants']) && is_array($item['variants'])): ?>
                            <ul class="list-unstyled ms-3 mt-1 mb-0 variants-list" style="font-size: 0.85em; color: #444;">
                                <?php foreach ($item['variants'] as $varian): ?>
                                    <li>
                                        <small>
                                            ↳ <?= esc($varian['nama_varian']) ?>
                                            <?php if (isset($varian['harga_varian']) && $varian['harga_varian'] > 0): ?>
                                                - <span style="color: #c7254e; font-weight: bold;">Rp <?= number_format($varian['harga_varian'], 0, ',', '.') ?></span>
                                            <?php endif; ?>
                                            <?php if (isset($varian['stok_varian'])): ?>
                                                (Stok: <?= esc($varian['stok_varian']) ?>)
                                            <?php endif; ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($item['kategori'] ?? 'N/A') ?></td>
                    <td>Rp <?= number_format($item['harga'] ?? 0, 0, ',', '.') ?></td>
                    <td> <!-- Kolom Status -->
                        <?php if ($item['status'] == 1): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= site_url(route_to('admin_produk_toggle_status', $item['id'])) ?>"
                           class="btn btn-sm <?= $item['status'] == 1 ? 'btn-outline-warning' : 'btn-outline-info' ?> mb-1 me-1"
                           title="<?= $item['status'] == 1 ? 'Nonaktifkan Produk' : 'Aktifkan Produk' ?>"
                           onclick="return confirm('Anda yakin ingin mengubah status produk \'<?= esc($item['nama_produk']) ?>\'?')">
                           <i class="fas <?= $item['status'] == 1 ? 'fa-toggle-off' : 'fa-toggle-on' ?>"></i> <!-- Contoh ikon Font Awesome, ganti jika perlu -->
                           <!-- <?= $item['status'] == 1 ? 'Off' : 'On' ?> -->
                        </a>
                        <a href="<?= site_url(route_to('admin_produk_edit', $item['id'])) ?>" class="btn btn-sm btn-primary mb-1 me-1">Edit</a>
                        <form action="<?= site_url(route_to('admin_produk_delete', $item['id'])) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk \'<?= esc($item['nama_produk']) ?>\' beserta variannya?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger mb-1">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center"> <!-- Colspan ditambah 1 karena ada kolom status -->
                        <?php if (!empty($keyword) || !empty($selectedKategori) || (isset($selectedStatus) && $selectedStatus !== '')): ?>
                            Produk tidak ditemukan dengan kriteria pencarian/filter Anda.
                        <?php else: ?>
                            Belum ada produk yang ditambahkan.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Tampilkan Pagination -->
<?php if (isset($pager) && $pager->getPageCount('produk_group') > 1): ?>
    <div class="mt-4 d-flex justify-content-center">
        <?= $pager->links('produk_group', 'default_bootstrap') ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>