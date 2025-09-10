<?php

namespace App\Controllers;

use App\Models\Rembesan\MDataPengukuran;

class Grafik extends BaseController
{
    /**
     * URL embed Grafana untuk setiap set grafik
     * - Untuk setiap set, array berisi URL per panel (index 0..n-1)
     * - Jika sebuah set hanya butuh 1 grafik, cukup isi 1 elemen saja.
     */
    private $grafanaEmbeds = [
        '1' => [ // Default tahun (2023) → set 1: 4 panel
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=1&var-tahun=2023&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=2&var-tahun=2023&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=3&var-tahun=2023&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=4&var-tahun=2023&__feature.dashboardSceneSolo=true'
        ],
        '1A' => [ // Semua tahun (All) → 4 panel
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672617600000&to=1757490018111&timezone=browser&var-tahun=$__all&panelId=1&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672617600000&to=1757490018111&timezone=browser&var-tahun=$__all&panelId=2&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672617600000&to=1757490018111&timezone=browser&var-tahun=$__all&panelId=3&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672617600000&to=1757490018111&timezone=browser&var-tahun=$__all&panelId=4&__feature.dashboardSceneSolo=true'
        ],
        '2' => [ // Set grafik 2 → hanya 1 grafik (panelId=5)
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672617600000&to=1757493553375&timezone=browser&var-tahun=$__all&panelId=5&__feature.dashboardSceneSolo=true'
        ]
    ];

    /**
     * Judul tiap set grafik
     */
    private $grafanaTitles = [
        '1'  => 'Set Grafik 1 - All Series',
        '2'  => 'Set Grafik 2 - Analisis Look Burt'
    ];

    /**
     * Judul tiap panel dalam set grafik
     * - Untuk set 1 ada 4 judul
     * - Untuk set 2 hanya 1 judul (sesuai permintaan)
     */
    private $panelTitles = [
        '1' => ['Total Bocoran - Batas Maksimal', 'A1', 'B3 (B1+B2)', 'SR'],
        '2' => ['Grafik Tunggal Set 2']
    ];

    public function index($graph_set = '1')
    {
        // Validasi input (hanya '1' atau '2' yang diterima)
        if (!in_array($graph_set, ['1', '2'])) {
            $graph_set = '1';
        }

        // Ambil tahun unik dari database hanya untuk set 1
        $tahunAvailable = [];
        if ($graph_set === '1') {
            $model = new MDataPengukuran();
            $tahunAvailable = $model->select('DISTINCT YEAR(tanggal) as tahun')
                                    ->orderBy('tahun', 'ASC')
                                    ->findColumn('tahun');
        }

        $data = [
            'current_graph_set' => $graph_set,
            'grafanaUrlsTahun'  => $this->grafanaEmbeds['1'],   // versi tahun default (2023)
            'grafanaUrlsAll'    => $this->grafanaEmbeds['1A'],  // versi all tahun
            'grafana_urls'      => $this->grafanaEmbeds[$graph_set], // URL yang akan di-loop di view
            'grafana_title'     => $this->grafanaTitles[$graph_set],
            'panel_titles'      => $this->panelTitles[$graph_set],
            'tahunAvailable'    => $tahunAvailable,
            'title'             => 'Grafik Rembesan Bendungan - PT Indonesia Power'
        ];

        return view('Grafik/Grafik', $data);
    }
}
