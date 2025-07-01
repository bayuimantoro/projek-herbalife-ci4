<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Rute Utama Aplikasi (Root)
// --------------------------------------------------------------------
// Rute ini akan dicek pertama kali.
// HomeController::index akan menentukan apakah pengguna diarahkan ke login atau ke admin area.
$routes->get('/', 'Home::index', ['as' => 'homepage']);

// --------------------------------------------------------------------
// Rute Autentikasi
// --------------------------------------------------------------------
// Rute ini tidak memerlukan filter 'auth' karena ini adalah proses untuk mendapatkan autentikasi.
// Grup 'guest' bisa digunakan jika Anda punya filter yang hanya berlaku untuk pengguna yang BELUM login.
// Untuk saat ini, kita biarkan di luar grup.
$routes->get('login', 'AuthController::login', ['as' => 'login_form']);
$routes->post('auth/attemptLogin', 'AuthController::attemptLogin', ['as' => 'attempt_login']);
$routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

// --------------------------------------------------------------------
// Rute Panel Admin (Dilindungi oleh Filter 'auth')
// --------------------------------------------------------------------
// Semua rute di dalam grup 'admin' ini akan otomatis dicek oleh 'AuthFilter'.
// Jika pengguna belum login, AuthFilter akan mengarahkan mereka ke 'login_form'.
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {

    // Halaman default untuk admin setelah login.
    // Jika Anda ingin /admin/ mengarah langsung ke daftar produk:
    $routes->get('/', 'Admin\ProdukController::index', ['as' => 'admin_dashboard']); // Atau bisa juga dinamai 'admin_home'

    // Rute untuk CRUD Produk
    // Memberi nama rute ('as') membantu dalam pembuatan URL dan redirect.
    $routes->get('produk', 'Admin\ProdukController::index', ['as' => 'admin_produk_index']);
    $routes->get('produk/create', 'Admin\ProdukController::create', ['as' => 'admin_produk_create']);
    $routes->post('produk/store', 'Admin\ProdukController::store', ['as' => 'admin_produk_store']);
    $routes->get('produk/edit/(:num)', 'Admin\ProdukController::edit/$1', ['as' => 'admin_produk_edit']);
    $routes->put('produk/update/(:num)', 'Admin\ProdukController::update/$1', ['as' => 'admin_produk_update']);
    $routes->delete('produk/delete/(:num)', 'Admin\ProdukController::delete/$1', ['as' => 'admin_produk_delete']);

    // Jika Anda punya modul admin lain (misal, manajemen pengguna, kategori, dll.),
    // tambahkan rutenya di dalam grup 'admin' ini.
    // Contoh:
    // $routes->get('users', 'Admin\UserController::index', ['as' => 'admin_users_index']);
    // $routes->resource('categories', ['controller' => 'Admin\CategoryController', 'as' => 'admin_categories']);
});

$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    // ... rute produk lainnya ...
    $routes->get('produk/toggle-status/(:num)', 'Admin\ProdukController::toggleStatus/$1', ['as' => 'admin_produk_toggle_status']);
});

/*
 * --------------------------------------------------------------------
 * Additional Routing (Biarkan seperti bawaan)
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}