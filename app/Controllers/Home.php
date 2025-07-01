<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            // Arahkan ke dashboard admin atau halaman utama admin
            return redirect()->to(route_to('admin_dashboard')); // Menggunakan nama rute yang baru
        } else {
            return redirect()->to(route_to('login_form'));
        }
    }
}