<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Bisa juga 'object'
    protected $useSoftDeletes   = false; // Kita tidak pakai soft delete untuk contoh ini

    // Kolom yang diizinkan untuk diisi melalui form (mass assignment)
    // TAMBAHKAN 'status' DI SINI
    protected $allowedFields    = ['nama_produk', 'deskripsi', 'kategori', 'harga', 'gambar', 'status'];

    // Menggunakan timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules (opsional, bisa juga di controller)
    // TAMBAHKAN VALIDASI UNTUK 'status' JIKA PERLU
    protected $validationRules      = [
        'nama_produk' => 'required|min_length[3]|max_length[255]',
        'kategori'    => 'permit_empty|max_length[100]',
        'harga'       => 'required|numeric|greater_than[0]',
        'deskripsi'   => 'permit_empty',
        'status'      => 'permit_empty|in_list[0,1]', // Memastikan status hanya 0 atau 1, permit_empty agar tidak wajib saat create (default di DB)
        // 'gambar' akan divalidasi di controller karena lebih kompleks
    ];
    protected $validationMessages   = [
        'nama_produk' => [
            'required' => 'Nama produk wajib diisi.',
            'min_length' => 'Nama produk minimal 3 karakter.',
            'max_length' => 'Nama produk maksimal 255 karakter.'
        ],
        'harga' => [
            'required' => 'Harga wajib diisi.',
            'numeric'  => 'Harga harus berupa angka.',
            'greater_than' => 'Harga harus lebih besar dari 0.'
        ],
        'status' => [ // Pesan validasi untuk status
            'in_list' => 'Nilai status tidak valid.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}