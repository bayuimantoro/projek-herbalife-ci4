<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services; // Diperlukan untuk Services::session() dan route_to()

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will stop and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session(); // Mengambil instance session

        // Cek apakah session 'isLoggedIn' ada dan bernilai true
        if (!$session->get('isLoggedIn')) {
            // Jika belum login, set flashdata untuk pesan error
            $session->setFlashdata('error', 'Anda harus login untuk mengakses halaman ini.');

            // Redirect ke halaman login.
            // Lebih baik menggunakan named route jika sudah didefinisikan.
            // Contoh: $routes->get('/login', 'AuthController::login', ['as' => 'login_form']);
            try {
                // Mencoba redirect menggunakan named route 'login_form'
                return redirect()->to(route_to('login_form'));
            } catch (\CodeIgniter\Router\Exceptions\RouterException $e) {
                // Fallback jika named route 'login_form' tidak ditemukan,
                // atau jika Anda belum menggunakan named routes.
                log_message('error', '[AuthFilter] Named route "login_form" not found. Falling back to /login. Error: ' . $e->getMessage());
                return redirect()->to('/login'); // Pastikan rute '/login' ini ada
            }
        }

        // Jika sudah login, tidak ada yang dikembalikan (atau bisa return $request),
        // sehingga request akan dilanjutkan ke controller.
        // Tidak perlu return null secara eksplisit.
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi yang perlu dilakukan setelah request untuk filter ini
    }
}