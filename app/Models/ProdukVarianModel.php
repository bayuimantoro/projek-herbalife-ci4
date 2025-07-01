<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukVarianModel extends Model
{
    protected $table            = 'produk_varian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['produk_id', 'nama_varian', 'harga_varian', 'stok_varian'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Anda bisa menambahkan relasi di ProdukModel jika diperlukan nanti
    // public function produk()
    // {
    //     return $this->belongsTo(ProdukModel::class, 'produk_id');
    // }
}