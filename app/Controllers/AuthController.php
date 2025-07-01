<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    protected $adminModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        // Pastikan session sudah di-load. Biasanya otomatis via BaseController CI4.
        // Jika tidak, $this->session = \Config\Services::session();
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard admin
        if (session()->get('isLoggedIn')) {
            return redirect()->to('admin/produk');
        }

        $data = [
            'title' => 'Login Admin',
            'validation' => \Config\Services::validation()
        ];
        return view('auth/login', $data);
    }

    public function attemptLogin()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/login')->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $admin = $this->adminModel->getAdminByUsername($username);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Login berhasil, set session
            $sessionData = [
                'admin_id'       => $admin['id'],
                'admin_username' => $admin['username'],
                'admin_nama'     => $admin['nama_lengkap'],
                'isLoggedIn'     => true,
            ];
            session()->set($sessionData);
            session()->setFlashdata('success', 'Login berhasil! Selamat datang, ' . $admin['nama_lengkap'] . '.');
            return redirect()->to('admin/produk'); // Redirect ke halaman utama admin
        } else {
            // Login gagal
            session()->setFlashdata('error', 'Username atau password salah.');
            return redirect()->to('/login')->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'Anda telah berhasil logout.');
        return redirect()->to('/login');
    }
}