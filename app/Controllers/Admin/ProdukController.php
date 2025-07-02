<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\ProdukVarianModel;

class ProdukController extends BaseController
{
    protected $produkModel;
    protected $produkVarianModel;
    protected $helpers = ['form', 'url', 'text'];

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->produkVarianModel = new ProdukVarianModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $selectedKategori = $this->request->getGet('kategori_filter');
        $selectedStatus = $this->request->getGet('status_filter');

        $queryBuilder = $this->produkModel;

        if (!empty($keyword)) {
            $queryBuilder = $queryBuilder->groupStart()
                                ->like('nama_produk', $keyword)
                                ->orLike('deskripsi', $keyword)
                           ->groupEnd();
        }

        if (!empty($selectedKategori)) {
            $queryBuilder = $queryBuilder->where('kategori', $selectedKategori);
        }

        if ($selectedStatus !== null && $selectedStatus !== '') {
            $queryBuilder = $queryBuilder->where('produk.status', $selectedStatus);
        }

        $produk_list = $queryBuilder->orderBy('id', 'DESC')->paginate(10, 'produk_group');
        $produk_with_variants = [];

        if (!empty($produk_list)) {
            $produk_ids = array_column($produk_list, 'id');
            if (!empty($produk_ids)) {
                $all_variants = $this->produkVarianModel
                                     ->whereIn('produk_id', $produk_ids)
                                     ->orderBy('produk_id', 'ASC')
                                     ->orderBy('nama_varian', 'ASC')
                                     ->findAll();
                $variants_by_produk_id = [];
                foreach ($all_variants as $variant) {
                    $variants_by_produk_id[$variant['produk_id']][] = $variant;
                }
                foreach ($produk_list as $p) {
                    $p['variants'] = $variants_by_produk_id[$p['id']] ?? [];
                    $produk_with_variants[] = $p;
                }
            } else {
                foreach ($produk_list as $p) {
                    $p['variants'] = [];
                    $produk_with_variants[] = $p;
                }
            }
        }

        $data = [
            'title' => 'Daftar Produk Herbalife',
            'produk' => $produk_with_variants,
            'keyword' => $keyword,
            'selectedKategori' => $selectedKategori,
            'selectedStatus' => $selectedStatus,
            'pager' => $this->produkModel->pager,
        ];
        return view('admin/produk/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Produk Baru',
            'validation' => \Config\Services::validation(),
            'errors' => session()->getFlashdata('errors')
        ];
        return view('admin/produk/create', $data);
    }

    public function store()
    {
        $rulesProdukUtama = [
            'nama_produk' => 'required|min_length[3]|max_length[255]',
            'kategori'    => 'permit_empty|max_length[100]',
            'harga'       => 'required|numeric|greater_than[0]',
            'deskripsi'   => 'permit_empty',
            'status'      => 'required|in_list[0,1]',
            'gambar'      => [
                'rules' => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar (maks 2MB).',
                    'is_image' => 'File yang diupload bukan gambar.',
                    'mime_in'  => 'Format gambar harus JPG, JPEG, atau PNG.'
                ]
            ]
        ];
        $rulesVarian = [
            'nama_varian'  => 'required|max_length[150]',
            'harga_varian' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'stok_varian'  => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        $validation = \Config\Services::validation();
        if (!$validation->setRules($rulesProdukUtama)->run($this->request->getPost())) {
            return redirect()->to(route_to('admin_produk_create'))->withInput()->with('errors', $validation->getErrors());
        }

        $inputVariants = $this->request->getPost('variants') ?? [];
        $varianErrors = [];
        $isValid = true;
        if (!empty($inputVariants)) {
            foreach ($inputVariants as $index => $varianData) {
                if (!empty($varianData['nama_varian'])) {
                    $tempValidation = \Config\Services::validation();
                    if (!$tempValidation->setRules($rulesVarian)->run($varianData)) {
                        foreach ($tempValidation->getErrors() as $field => $error) {
                            $varianErrors["variants.{$index}.{$field}"] = $error;
                        }
                        $isValid = false;
                    }
                }
            }
        }
        if (!$isValid) {
            $allErrors = array_merge($validation->getErrors(), $varianErrors);
            return redirect()->to(route_to('admin_produk_create'))->withInput()->with('errors', $allErrors);
        }

        $fileGambar = $this->request->getFile('gambar');
        $namaGambar = null;
        if ($fileGambar && $fileGambar->isValid() && !$fileGambar->hasMoved()) {
            $namaGambar = $fileGambar->getRandomName();
            $fileGambar->move(FCPATH . 'uploads/produk', $namaGambar);
        }

        $dataProdukUtama = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'kategori'    => $this->request->getPost('kategori'),
            'harga'       => $this->request->getPost('harga'),
            'gambar'      => $namaGambar,
            'status'      => $this->request->getPost('status') ?? 1,
        ];

        $this->produkModel->db->transStart();
        $produkID = $this->produkModel->insert($dataProdukUtama);

        if (!$produkID) {
            if ($namaGambar && file_exists(FCPATH . 'uploads/produk/' . $namaGambar)) {
                unlink(FCPATH . 'uploads/produk/' . $namaGambar);
            }
            $this->produkModel->db->transRollback();
            session()->setFlashdata('error', 'Gagal menambahkan produk utama.');
            return redirect()->to(route_to('admin_produk_create'))->withInput();
        }

        if (!empty($inputVariants)) {
            foreach ($inputVariants as $varianData) {
                if (!empty($varianData['nama_varian'])) {
                    $dataToSaveVarian = [
                        'produk_id'    => $produkID,
                        'nama_varian'  => $varianData['nama_varian'],
                        'harga_varian' => !empty($varianData['harga_varian']) ? $varianData['harga_varian'] : null,
                        'stok_varian'  => $varianData['stok_varian'] ?? 0,
                    ];
                    $this->produkVarianModel->insert($dataToSaveVarian);
                }
            }
        }

        if ($this->produkModel->db->transStatus() === false) {
            $this->produkModel->db->transRollback();
            if ($namaGambar && file_exists(FCPATH . 'uploads/produk/' . $namaGambar)) {
                unlink(FCPATH . 'uploads/produk/' . $namaGambar);
            }
            session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan produk.');
            return redirect()->to(route_to('admin_produk_create'))->withInput();
        } else {
            $this->produkModel->db->transCommit();
            session()->setFlashdata('success', 'Produk berhasil ditambahkan.');
        }

        return redirect()->to(route_to('admin_produk_index'));
    }

    public function edit($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            session()->setFlashdata('error', 'Produk tidak ditemukan.');
            return redirect()->to(route_to('admin_produk_index'));
        }
        $produk['variants'] = $this->produkVarianModel->where('produk_id', $id)->orderBy('nama_varian', 'ASC')->findAll();
        $data = [
            'title' => 'Edit Produk: ' . esc($produk['nama_produk']),
            'produk' => $produk,
            'validation' => \Config\Services::validation(),
            'errors' => session()->getFlashdata('errors')
        ];
        return view('admin/produk/edit', $data);
    }

    public function update($id)
    {
        $produkLama = $this->produkModel->find($id);
        if (!$produkLama) {
            session()->setFlashdata('error', 'Produk tidak ditemukan.');
            return redirect()->to(route_to('admin_produk_index'));
        }

        $rulesProdukUtama = [
            'nama_produk' => 'required|min_length[3]|max_length[255]',
            'kategori'    => 'permit_empty|max_length[100]',
            'harga'       => 'required|numeric|greater_than[0]',
            'deskripsi'   => 'permit_empty',
            'status'      => 'required|in_list[0,1]',
            'gambar'      => [
                'rules' => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]',
                'errors' => [ /* ... */ ]
            ]
        ];
        $rulesVarian = [
            'nama_varian'  => 'required|max_length[150]',
            'harga_varian' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'stok_varian'  => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        $validation = \Config\Services::validation();
        if (!$validation->setRules($rulesProdukUtama)->run($this->request->getPost())) {
            return redirect()->to(route_to('admin_produk_edit', $id))->withInput()->with('errors', $validation->getErrors());
        }

        $inputVariants = $this->request->getPost('variants') ?? [];
        $varianErrors = [];
        $isValid = true;
        if (!empty($inputVariants)) {
            foreach ($inputVariants as $index => $varianData) {
                if (!empty($varianData['nama_varian'])) {
                     $tempValidation = \Config\Services::validation();
                    if (!$tempValidation->setRules($rulesVarian)->run($varianData)) {
                        foreach ($tempValidation->getErrors() as $field => $error) {
                            $varianErrors["variants.{$index}.{$field}"] = $error;
                        }
                        $isValid = false;
                    }
                }
            }
        }
        if (!$isValid) {
            $allErrors = array_merge($validation->getErrors(), $varianErrors);
            return redirect()->to(route_to('admin_produk_edit', $id))->withInput()->with('errors', $allErrors);
        }

        $fileGambar = $this->request->getFile('gambar');
        $namaGambar = $produkLama['gambar'];
        $hapusGambarCheckbox = $this->request->getPost('hapus_gambar_lama');
        if ($fileGambar && $fileGambar->isValid() && !$fileGambar->hasMoved()) {
            if ($produkLama['gambar'] && file_exists(FCPATH . 'uploads/produk/' . $produkLama['gambar'])) {
                unlink(FCPATH . 'uploads/produk/' . $produkLama['gambar']);
            }
            $namaGambar = $fileGambar->getRandomName();
            $fileGambar->move(FCPATH . 'uploads/produk', $namaGambar);
        } elseif (empty($fileGambar->getName()) && $hapusGambarCheckbox === '1' && $produkLama['gambar']) {
            if (file_exists(FCPATH . 'uploads/produk/' . $produkLama['gambar'])) {
                unlink(FCPATH . 'uploads/produk/' . $produkLama['gambar']);
            }
            $namaGambar = null;
        }

        $dataProdukUtama = [
            'id'          => $id,
            'nama_produk' => $this->request->getPost('nama_produk'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'kategori'    => $this->request->getPost('kategori'),
            'harga'       => $this->request->getPost('harga'),
            'gambar'      => $namaGambar,
            'status'      => $this->request->getPost('status'),
        ];

        $this->produkModel->db->transStart();
        if (!$this->produkModel->save($dataProdukUtama)) {
            $this->produkModel->db->transRollback();
            session()->setFlashdata('error', 'Gagal memperbarui produk utama.');
            return redirect()->to(route_to('admin_produk_edit', $id))->withInput();
        }

        $varianIDsFromForm = [];
        if (!empty($inputVariants)) {
            foreach ($inputVariants as $varianData) {
                if (empty($varianData['nama_varian'])) {
                    if (!empty($varianData['id'])) {
                         $this->produkVarianModel->delete($varianData['id']);
                    }
                    continue;
                }
                $dataToSave = [
                    'produk_id'    => $id,
                    'nama_varian'  => $varianData['nama_varian'],
                    'harga_varian' => !empty($varianData['harga_varian']) ? $varianData['harga_varian'] : null,
                    'stok_varian'  => $varianData['stok_varian'] ?? 0,
                ];
                if (!empty($varianData['id'])) {
                    $dataToSave['id'] = $varianData['id'];
                    $this->produkVarianModel->save($dataToSave);
                    $varianIDsFromForm[] = $varianData['id'];
                } else {
                    $newVarianId = $this->produkVarianModel->insert($dataToSave);
                    if ($newVarianId) {
                        $varianIDsFromForm[] = $newVarianId;
                    }
                }
            }
        }
        $currentVarianIDsInDB = array_column($this->produkVarianModel->where('produk_id', $id)->select('id')->findAll(), 'id');
        $varianIDsToDelete = array_diff($currentVarianIDsInDB, $varianIDsFromForm);
        if (!empty($varianIDsToDelete)) {
            $this->produkVarianModel->delete($varianIDsToDelete);
        }

        if ($this->produkModel->db->transStatus() === false) {
            $this->produkModel->db->transRollback();
            session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan perubahan produk dan varian.');
        } else {
            $this->produkModel->db->transCommit();
            session()->setFlashdata('success', 'Produk dan varian berhasil diperbarui.');
        }

        return redirect()->to(route_to('admin_produk_index'));
    }

    public function toggleStatus($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            session()->setFlashdata('error', 'Produk tidak ditemukan.');
            return redirect()->back();
        }

        $newStatus = ($produk['status'] == 1) ? 0 : 1;

        if ($this->produkModel->update($id, ['status' => $newStatus])) {
            $statusText = ($newStatus == 1) ? 'diaktifkan' : 'dinonaktifkan';
            session()->setFlashdata('success', "Produk '" . esc($produk['nama_produk']) . "' berhasil {$statusText}.");
        } else {
            session()->setFlashdata('error', "Gagal mengubah status produk '" . esc($produk['nama_produk']) . "'.");
        }
        return redirect()->back();
    }

    public function delete($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            session()->setFlashdata('error', 'Produk tidak ditemukan.');
            return redirect()->to(route_to('admin_produk_index'));
        }
        if ($produk['gambar'] && file_exists(FCPATH . 'uploads/produk/' . $produk['gambar'])) {
            unlink(FCPATH . 'uploads/produk/' . $produk['gambar']);
        }
        if ($this->produkModel->delete($id)) {
            session()->setFlashdata('success', 'Produk berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus produk.');
        }
        return redirect()->to(route_to('admin_produk_index'));
    }
}
