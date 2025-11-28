<?php

namespace App\Controllers\EXS;

use App\Controllers\BaseController;
use App\Models\Exstenso\PengukuranEksModel;
use App\Models\Exstenso\PembacaanEx1Model;
use App\Models\Exstenso\PembacaanEx2Model;
use App\Models\Exstenso\PembacaanEx3Model;
use App\Models\Exstenso\PembacaanEx4Model;
use App\Models\Exstenso\DeformasiEx1Model;
use App\Models\Exstenso\DeformasiEx2Model;
use App\Models\Exstenso\DeformasiEx3Model;
use App\Models\Exstenso\DeformasiEx4Model;
use App\Models\Exstenso\ReadingsEx1Model;
use App\Models\Exstenso\ReadingsEx2Model;
use App\Models\Exstenso\ReadingsEx3Model;
use App\Models\Exstenso\ReadingsEx4Model;

class ExtensoController extends BaseController
{
    protected $pengukuranModel;
    protected $pembacaanEx1Model;
    protected $pembacaanEx2Model;
    protected $pembacaanEx3Model;
    protected $pembacaanEx4Model;
    protected $deformasiEx1Model;
    protected $deformasiEx2Model;
    protected $deformasiEx3Model;
    protected $deformasiEx4Model;
    protected $readingsEx1Model;
    protected $readingsEx2Model;
    protected $readingsEx3Model;
    protected $readingsEx4Model;

    public function __construct()
    {
        $this->pengukuranModel = new PengukuranEksModel();
        $this->pembacaanEx1Model = new PembacaanEx1Model();
        $this->pembacaanEx2Model = new PembacaanEx2Model();
        $this->pembacaanEx3Model = new PembacaanEx3Model();
        $this->pembacaanEx4Model = new PembacaanEx4Model();
        $this->deformasiEx1Model = new DeformasiEx1Model();
        $this->deformasiEx2Model = new DeformasiEx2Model();
        $this->deformasiEx3Model = new DeformasiEx3Model();
        $this->deformasiEx4Model = new DeformasiEx4Model();
        $this->readingsEx1Model = new ReadingsEx1Model();
        $this->readingsEx2Model = new ReadingsEx2Model();
        $this->readingsEx3Model = new ReadingsEx3Model();
        $this->readingsEx4Model = new ReadingsEx4Model();
    }

    public function index()
    {
        // Ambil semua data pengukuran
        $pengukuranData = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('periode', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();
        
        $data = [];
        
        foreach ($pengukuranData as $pengukuran) {
            $idPengukuran = $pengukuran['id_pengukuran'];
            
            // Ambil data pembacaan untuk setiap extensometer
            $pembacaanEx1 = $this->pembacaanEx1Model->where('id_pengukuran', $idPengukuran)->first();
            $pembacaanEx2 = $this->pembacaanEx2Model->where('id_pengukuran', $idPengukuran)->first();
            $pembacaanEx3 = $this->pembacaanEx3Model->where('id_pengukuran', $idPengukuran)->first();
            $pembacaanEx4 = $this->pembacaanEx4Model->where('id_pengukuran', $idPengukuran)->first();
            
            // Ambil data deformasi untuk setiap extensometer
            $deformasiEx1 = $this->deformasiEx1Model->where('id_pengukuran', $idPengukuran)->first();
            $deformasiEx2 = $this->deformasiEx2Model->where('id_pengukuran', $idPengukuran)->first();
            $deformasiEx3 = $this->deformasiEx3Model->where('id_pengukuran', $idPengukuran)->first();
            $deformasiEx4 = $this->deformasiEx4Model->where('id_pengukuran', $idPengukuran)->first();
            
            // Ambil data readings untuk setiap extensometer
            $readingsEx1 = $this->readingsEx1Model->where('id_pengukuran', $idPengukuran)->first();
            $readingsEx2 = $this->readingsEx2Model->where('id_pengukuran', $idPengukuran)->first();
            $readingsEx3 = $this->readingsEx3Model->where('id_pengukuran', $idPengukuran)->first();
            $readingsEx4 = $this->readingsEx4Model->where('id_pengukuran', $idPengukuran)->first();
            
            $data[] = [
                'pengukuran' => $pengukuran,
                'pembacaan' => [
                    'ex1' => $pembacaanEx1,
                    'ex2' => $pembacaanEx2,
                    'ex3' => $pembacaanEx3,
                    'ex4' => $pembacaanEx4
                ],
                'deformasi' => [
                    'ex1' => $deformasiEx1,
                    'ex2' => $deformasiEx2,
                    'ex3' => $deformasiEx3,
                    'ex4' => $deformasiEx4
                ],
                'readings' => [
                    'ex1' => $readingsEx1,
                    'ex2' => $readingsEx2,
                    'ex3' => $readingsEx3,
                    'ex4' => $readingsEx4
                ]
            ];
        }
        
        return view('Exs/index', [
            'title' => 'Extensometer Monitoring System',
            'pengukuran' => $data,
            'activeMenu' => 'extenso'
        ]);
    }

    public function ex1()
    {
        return $this->showExtensoDetail('EX-1');
    }

    public function ex2()
    {
        return $this->showExtensoDetail('EX-2');
    }

    public function ex3()
    {
        return $this->showExtensoDetail('EX-3');
    }

    public function ex4()
    {
        return $this->showExtensoDetail('EX-4');
    }

    private function showExtensoDetail($extensoName)
    {
        // Ambil semua data pengukuran
        $pengukuranData = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('periode', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();
        
        $data = [];
        
        foreach ($pengukuranData as $pengukuran) {
            $idPengukuran = $pengukuran['id_pengukuran'];
            
            // Ambil data berdasarkan extensometer yang dipilih
            switch ($extensoName) {
                case 'EX-1':
                    $pembacaan = $this->pembacaanEx1Model->where('id_pengukuran', $idPengukuran)->first();
                    $deformasi = $this->deformasiEx1Model->where('id_pengukuran', $idPengukuran)->first();
                    $readings = $this->readingsEx1Model->where('id_pengukuran', $idPengukuran)->first();
                    break;
                case 'EX-2':
                    $pembacaan = $this->pembacaanEx2Model->where('id_pengukuran', $idPengukuran)->first();
                    $deformasi = $this->deformasiEx2Model->where('id_pengukuran', $idPengukuran)->first();
                    $readings = $this->readingsEx2Model->where('id_pengukuran', $idPengukuran)->first();
                    break;
                case 'EX-3':
                    $pembacaan = $this->pembacaanEx3Model->where('id_pengukuran', $idPengukuran)->first();
                    $deformasi = $this->deformasiEx3Model->where('id_pengukuran', $idPengukuran)->first();
                    $readings = $this->readingsEx3Model->where('id_pengukuran', $idPengukuran)->first();
                    break;
                case 'EX-4':
                    $pembacaan = $this->pembacaanEx4Model->where('id_pengukuran', $idPengukuran)->first();
                    $deformasi = $this->deformasiEx4Model->where('id_pengukuran', $idPengukuran)->first();
                    $readings = $this->readingsEx4Model->where('id_pengukuran', $idPengukuran)->first();
                    break;
                default:
                    $pembacaan = null;
                    $deformasi = null;
                    $readings = null;
            }
            
            $data[] = [
                'pengukuran' => $pengukuran,
                'pembacaan' => $pembacaan,
                'deformasi' => $deformasi,
                'readings' => $readings
            ];
        }
        
        // PERBAIKAN: Ganti 'extenso/detail' dengan 'Exs/index' atau redirect ke grafik ambang
        return redirect()->to('/extenso');
        
        // Atau jika ingin tetap menampilkan view, gunakan:
        // return view('Exs/index', [
        //     'title' => $extensoName . ' - Extensometer Monitoring',
        //     'pengukuran' => $data,
        //     'extensoName' => $extensoName,
        //     'activeMenu' => 'extenso'
        // ]);
    }

    public function create()
    {
        return view('Exs/create', [
            'title' => 'Tambah Data Extensometer',
            'activeMenu' => 'extenso'
        ]);
    }

    public function store()
    {
        // Validasi input
        $rules = [
            'tahun' => 'required|numeric',
            'periode' => 'required',
            'tanggal' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Simpan data pengukuran
            $pengukuranData = [
                'tahun' => $this->request->getPost('tahun'),
                'periode' => $this->request->getPost('periode'),
                'tanggal' => $this->request->getPost('tanggal'),
                'dma' => $this->request->getPost('dma')
            ];

            $pengukuranId = $this->pengukuranModel->insert($pengukuranData);

            // Simpan data untuk setiap extensometer
            $this->saveExtensoData($pengukuranId, 'ex1');
            $this->saveExtensoData($pengukuranId, 'ex2');
            $this->saveExtensoData($pengukuranId, 'ex3');
            $this->saveExtensoData($pengukuranId, 'ex4');

            return redirect()->to('/extenso')->with('success', 'Data extensometer berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function saveExtensoData($pengukuranId, $extensoType)
    {
        // Nilai default initial readings untuk setiap extensometer
        $defaultInitials = [
            'ex1' => ['10' => 35.00, '20' => 40.95, '30' => 29.80],
            'ex2' => ['10' => 22.60, '20' => 23.70, '30' => 30.75],
            'ex3' => ['10' => 37.75, '20' => 39.15, '30' => 41.40],
            'ex4' => ['10' => 33.80, '20' => 29.30, '30' => 48.95]
        ];

        // Simpan data pembacaan
        $pembacaanData = [
            'id_pengukuran' => $pengukuranId,
            'pembacaan_10' => $this->request->getPost($extensoType . '_pembacaan_10'),
            'pembacaan_20' => $this->request->getPost($extensoType . '_pembacaan_20'),
            'pembacaan_30' => $this->request->getPost($extensoType . '_pembacaan_30')
        ];

        // Simpan data readings dengan nilai DEFAULT
        $readingsData = [
            'id_pengukuran' => $pengukuranId,
            'reading_10' => $defaultInitials[$extensoType]['10'],
            'reading_20' => $defaultInitials[$extensoType]['20'],
            'reading_30' => $defaultInitials[$extensoType]['30']
        ];

        // Pilih model yang sesuai
        switch ($extensoType) {
            case 'ex1':
                $this->pembacaanEx1Model->insert($pembacaanData);
                $this->readingsEx1Model->insert($readingsData);
                
                // Hitung deformasi otomatis
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx1Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx1Model
                    );
                }
                break;
                
            case 'ex2':
                $this->pembacaanEx2Model->insert($pembacaanData);
                $this->readingsEx2Model->insert($readingsData);
                
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx2Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx2Model
                    );
                }
                break;
                
            case 'ex3':
                $this->pembacaanEx3Model->insert($pembacaanData);
                $this->readingsEx3Model->insert($readingsData);
                
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx3Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx3Model
                    );
                }
                break;
                
            case 'ex4':
                $this->pembacaanEx4Model->insert($pembacaanData);
                $this->readingsEx4Model->insert($readingsData);
                
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx4Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx4Model
                    );
                }
                break;
        }
    }

    public function edit($id)
    {
        // Ambil data berdasarkan ID
        $pengukuran = $this->pengukuranModel->find($id);
        
        if (!$pengukuran) {
            return redirect()->to('/extenso')->with('error', 'Data tidak ditemukan.');
        }

        // Ambil data terkait
        $pembacaanEx1 = $this->pembacaanEx1Model->where('id_pengukuran', $id)->first();
        $pembacaanEx2 = $this->pembacaanEx2Model->where('id_pengukuran', $id)->first();
        $pembacaanEx3 = $this->pembacaanEx3Model->where('id_pengukuran', $id)->first();
        $pembacaanEx4 = $this->pembacaanEx4Model->where('id_pengukuran', $id)->first();

        $deformasiEx1 = $this->deformasiEx1Model->where('id_pengukuran', $id)->first();
        $deformasiEx2 = $this->deformasiEx2Model->where('id_pengukuran', $id)->first();
        $deformasiEx3 = $this->deformasiEx3Model->where('id_pengukuran', $id)->first();
        $deformasiEx4 = $this->deformasiEx4Model->where('id_pengukuran', $id)->first();

        $readingsEx1 = $this->readingsEx1Model->where('id_pengukuran', $id)->first();
        $readingsEx2 = $this->readingsEx2Model->where('id_pengukuran', $id)->first();
        $readingsEx3 = $this->readingsEx3Model->where('id_pengukuran', $id)->first();
        $readingsEx4 = $this->readingsEx4Model->where('id_pengukuran', $id)->first();

        // Gabungkan semua data menjadi satu array $extenso
        $extenso = [
            'id' => $pengukuran['id_pengukuran'],
            'tahun' => $pengukuran['tahun'],
            'periode' => $pengukuran['periode'],
            'tanggal' => $pengukuran['tanggal'],
            'dma' => $pengukuran['dma'],
            // Data EX1
            'ex1_pembacaan_10' => $pembacaanEx1 ? $pembacaanEx1['pembacaan_10'] : '',
            'ex1_pembacaan_20' => $pembacaanEx1 ? $pembacaanEx1['pembacaan_20'] : '',
            'ex1_pembacaan_30' => $pembacaanEx1 ? $pembacaanEx1['pembacaan_30'] : '',
            'ex1_deformasi_10' => $deformasiEx1 ? $deformasiEx1['deformasi_10'] : 0,
            'ex1_deformasi_20' => $deformasiEx1 ? $deformasiEx1['deformasi_20'] : 0,
            'ex1_deformasi_30' => $deformasiEx1 ? $deformasiEx1['deformasi_30'] : 0,
            // Data EX2
            'ex2_pembacaan_10' => $pembacaanEx2 ? $pembacaanEx2['pembacaan_10'] : '',
            'ex2_pembacaan_20' => $pembacaanEx2 ? $pembacaanEx2['pembacaan_20'] : '',
            'ex2_pembacaan_30' => $pembacaanEx2 ? $pembacaanEx2['pembacaan_30'] : '',
            'ex2_deformasi_10' => $deformasiEx2 ? $deformasiEx2['deformasi_10'] : 0,
            'ex2_deformasi_20' => $deformasiEx2 ? $deformasiEx2['deformasi_20'] : 0,
            'ex2_deformasi_30' => $deformasiEx2 ? $deformasiEx2['deformasi_30'] : 0,
            // Data EX3
            'ex3_pembacaan_10' => $pembacaanEx3 ? $pembacaanEx3['pembacaan_10'] : '',
            'ex3_pembacaan_20' => $pembacaanEx3 ? $pembacaanEx3['pembacaan_20'] : '',
            'ex3_pembacaan_30' => $pembacaanEx3 ? $pembacaanEx3['pembacaan_30'] : '',
            'ex3_deformasi_10' => $deformasiEx3 ? $deformasiEx3['deformasi_10'] : 0,
            'ex3_deformasi_20' => $deformasiEx3 ? $deformasiEx3['deformasi_20'] : 0,
            'ex3_deformasi_30' => $deformasiEx3 ? $deformasiEx3['deformasi_30'] : 0,
            // Data EX4
            'ex4_pembacaan_10' => $pembacaanEx4 ? $pembacaanEx4['pembacaan_10'] : '',
            'ex4_pembacaan_20' => $pembacaanEx4 ? $pembacaanEx4['pembacaan_20'] : '',
            'ex4_pembacaan_30' => $pembacaanEx4 ? $pembacaanEx4['pembacaan_30'] : '',
            'ex4_deformasi_10' => $deformasiEx4 ? $deformasiEx4['deformasi_10'] : 0,
            'ex4_deformasi_20' => $deformasiEx4 ? $deformasiEx4['deformasi_20'] : 0,
            'ex4_deformasi_30' => $deformasiEx4 ? $deformasiEx4['deformasi_30'] : 0,
        ];

        return view('Exs/edit', [
            'title' => 'Edit Data Extensometer',
            'extenso' => $extenso,
            'activeMenu' => 'extenso'
        ]);
    }

    public function update($id)
    {
        // Validasi input
        $rules = [
            'tahun' => 'required|numeric',
            'periode' => 'required',
            'tanggal' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Update data pengukuran
            $pengukuranData = [
                'tahun' => $this->request->getPost('tahun'),
                'periode' => $this->request->getPost('periode'),
                'tanggal' => $this->request->getPost('tanggal'),
                'dma' => $this->request->getPost('dma')
            ];

            $this->pengukuranModel->update($id, $pengukuranData);

            // Update data untuk setiap extensometer
            $this->updateExtensoData($id, 'ex1');
            $this->updateExtensoData($id, 'ex2');
            $this->updateExtensoData($id, 'ex3');
            $this->updateExtensoData($id, 'ex4');

            return redirect()->to('/extenso')->with('success', 'Data extensometer berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function updateExtensoData($pengukuranId, $extensoType)
    {
        // Nilai default initial readings untuk setiap extensometer
        $defaultInitials = [
            'ex1' => ['10' => 35.00, '20' => 40.95, '30' => 29.80],
            'ex2' => ['10' => 22.60, '20' => 23.70, '30' => 30.75],
            'ex3' => ['10' => 37.75, '20' => 39.15, '30' => 41.40],
            'ex4' => ['10' => 33.80, '20' => 29.30, '30' => 48.95]
        ];

        // Update data pembacaan
        $pembacaanData = [
            'pembacaan_10' => $this->request->getPost($extensoType . '_pembacaan_10'),
            'pembacaan_20' => $this->request->getPost($extensoType . '_pembacaan_20'),
            'pembacaan_30' => $this->request->getPost($extensoType . '_pembacaan_30')
        ];

        // Update data readings dengan nilai DEFAULT
        $readingsData = [
            'reading_10' => $defaultInitials[$extensoType]['10'],
            'reading_20' => $defaultInitials[$extensoType]['20'],
            'reading_30' => $defaultInitials[$extensoType]['30']
        ];

        // Pilih model yang sesuai dan update
        switch ($extensoType) {
            case 'ex1':
                $pembacaanExisting = $this->pembacaanEx1Model->where('id_pengukuran', $pengukuranId)->first();
                $readingsExisting = $this->readingsEx1Model->where('id_pengukuran', $pengukuranId)->first();
                
                if ($pembacaanExisting) {
                    $this->pembacaanEx1Model->update($pembacaanExisting['id_pembacaan_ex1'], $pembacaanData);
                }
                if ($readingsExisting) {
                    $this->readingsEx1Model->update($readingsExisting['id_reading_ex1'], $readingsData);
                }
                
                // Hitung ulang deformasi
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx1Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx1Model
                    );
                }
                break;
                
            case 'ex2':
                $pembacaanExisting = $this->pembacaanEx2Model->where('id_pengukuran', $pengukuranId)->first();
                $readingsExisting = $this->readingsEx2Model->where('id_pengukuran', $pengukuranId)->first();
                
                if ($pembacaanExisting) {
                    $this->pembacaanEx2Model->update($pembacaanExisting['id_pembacaan_ex2'], $pembacaanData);
                }
                if ($readingsExisting) {
                    $this->readingsEx2Model->update($readingsExisting['id_reading_ex2'], $readingsData);
                }
                
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx2Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx2Model
                    );
                }
                break;
                
            case 'ex3':
                $pembacaanExisting = $this->pembacaanEx3Model->where('id_pengukuran', $pengukuranId)->first();
                $readingsExisting = $this->readingsEx3Model->where('id_pengukuran', $pengukuranId)->first();
                
                if ($pembacaanExisting) {
                    $this->pembacaanEx3Model->update($pembacaanExisting['id_pembacaan_ex3'], $pembacaanData);
                }
                if ($readingsExisting) {
                    $this->readingsEx3Model->update($readingsExisting['id_reading_ex3'], $readingsData);
                }
                
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx3Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx3Model
                    );
                }
                break;
                
            case 'ex4':
                $pembacaanExisting = $this->pembacaanEx4Model->where('id_pengukuran', $pengukuranId)->first();
                $readingsExisting = $this->readingsEx4Model->where('id_pengukuran', $pengukuranId)->first();
                
                if ($pembacaanExisting) {
                    $this->pembacaanEx4Model->update($pembacaanExisting['id_pembacaan_ex4'], $pembacaanData);
                }
                if ($readingsExisting) {
                    $this->readingsEx4Model->update($readingsExisting['id_reading_ex4'], $readingsData);
                }
                
                if ($pembacaanData['pembacaan_10'] && $pembacaanData['pembacaan_20'] && $pembacaanData['pembacaan_30']) {
                    $this->deformasiEx4Model->hitungDeformasiFromPembacaan(
                        $pengukuranId, 
                        $this->pembacaanEx4Model
                    );
                }
                break;
        }
    }

    public function delete($id)
    {
        try {
            // Hapus data terkait dari semua tabel
            $this->pembacaanEx1Model->where('id_pengukuran', $id)->delete();
            $this->pembacaanEx2Model->where('id_pengukuran', $id)->delete();
            $this->pembacaanEx3Model->where('id_pengukuran', $id)->delete();
            $this->pembacaanEx4Model->where('id_pengukuran', $id)->delete();
            
            $this->deformasiEx1Model->where('id_pengukuran', $id)->delete();
            $this->deformasiEx2Model->where('id_pengukuran', $id)->delete();
            $this->deformasiEx3Model->where('id_pengukuran', $id)->delete();
            $this->deformasiEx4Model->where('id_pengukuran', $id)->delete();
            
            $this->readingsEx1Model->where('id_pengukuran', $id)->delete();
            $this->readingsEx2Model->where('id_pengukuran', $id)->delete();
            $this->readingsEx3Model->where('id_pengukuran', $id)->delete();
            $this->readingsEx4Model->where('id_pengukuran', $id)->delete();
            
            // Hapus data pengukuran
            $this->pengukuranModel->delete($id);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Data berhasil dihapus'
                ]);
            }

            return redirect()->to('/extenso')->with('success', 'Data berhasil dihapus.');

        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }

            return redirect()->to('/extenso')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        // Ambil data untuk export
        $pengukuranData = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('periode', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();
        
        // Logic untuk export Excel akan diimplementasikan di sini
        // Menggunakan library seperti PhpSpreadsheet
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Export functionality will be implemented here'
        ]);
    }

    public function grafikAmbang()
    {
        // Ambil semua data pengukuran
        $pengukuranData = $this->pengukuranModel->orderBy('tahun', 'DESC')
                                               ->orderBy('periode', 'DESC')
                                               ->orderBy('tanggal', 'DESC')
                                               ->findAll();
        
        $data = [];
        
        foreach ($pengukuranData as $pengukuran) {
            $idPengukuran = $pengukuran['id_pengukuran'];
            
            // Ambil data untuk SEMUA extensometer
            $pembacaanEx1 = $this->pembacaanEx1Model->where('id_pengukuran', $idPengukuran)->first();
            $pembacaanEx2 = $this->pembacaanEx2Model->where('id_pengukuran', $idPengukuran)->first();
            $pembacaanEx3 = $this->pembacaanEx3Model->where('id_pengukuran', $idPengukuran)->first();
            $pembacaanEx4 = $this->pembacaanEx4Model->where('id_pengukuran', $idPengukuran)->first();
            
            $readingsEx1 = $this->readingsEx1Model->where('id_pengukuran', $idPengukuran)->first();
            $readingsEx2 = $this->readingsEx2Model->where('id_pengukuran', $idPengukuran)->first();
            $readingsEx3 = $this->readingsEx3Model->where('id_pengukuran', $idPengukuran)->first();
            $readingsEx4 = $this->readingsEx4Model->where('id_pengukuran', $idPengukuran)->first();
            
            $data[] = [
                'pengukuran' => $pengukuran,
                'ex1' => [
                    'pembacaan' => $pembacaanEx1,
                    'readings' => $readingsEx1
                ],
                'ex2' => [
                    'pembacaan' => $pembacaanEx2,
                    'readings' => $readingsEx2
                ],
                'ex3' => [
                    'pembacaan' => $pembacaanEx3,
                    'readings' => $readingsEx3
                ],
                'ex4' => [
                    'pembacaan' => $pembacaanEx4,
                    'readings' => $readingsEx4
                ]
            ];
        }
        
        return view('Exs/grafik_ambang', [
            'title' => 'Grafik & Ambang Batas - Extensometer',
            'pengukuran' => $data,
            'activeMenu' => 'extenso'
        ]);
    }
}