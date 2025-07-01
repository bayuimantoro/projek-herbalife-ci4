<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> - Herbalife CI4</title>
    <!-- Bootstrap CSS (Contoh, bisa ganti dengan framework CSS lain atau custom) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 5rem; }
        .main-content {
            padding: 20px;
            border-radius: 5px;
        }
        .img-thumbnail-custom {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-success fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('admin/produk') ?>">Herbalife Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == 'admin/produk' || strpos(uri_string(), 'admin/produk/create') !== false || strpos(uri_string(), 'admin/produk/edit') !== false) ? 'active' : '' ?>" aria-current="page" href="<?= site_url('admin/produk') ?>">Produk</a>
                    </li>
                    <!-- Tambahkan menu lain jika perlu -->
                </ul>

                <!-- MODIFIKASI DIMULAI DI SINI: Tombol Logout dan Nama Admin -->
                <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <!-- Menampilkan nama lengkap jika ada, jika tidak, username -->
                                Halo, <?= esc(session()->get('admin_nama') ?? session()->get('admin_username')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAdmin">
                                <!--
                                <li><a class="dropdown-item" href="#">Profil (Jika ada)</a></li>
                                <li><hr class="dropdown-divider"></li>
                                -->
                                <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <!-- MODIFIKASI SELESAI -->

            </div>
        </div>
    </nav>

    <main class="container">
        <div class="main-content bg-light">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>