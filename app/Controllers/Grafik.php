<?php

namespace App\Controllers;

class Grafik extends BaseController
{
    // URL embed Grafana untuk masing-masing set grafik
    private $grafanaEmbeds = [
        // Set Grafik 1 (4 grafik berbeda)
        1 => [
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1704153600000&to=1735689600000&timezone=browser&panelId=1&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1704153600000&to=1735689600000&timezone=browser&panelId=2&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1704153600000&to=1735689600000&timezone=browser&panelId=3&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1704153600000&to=1735689600000&timezone=browser&panelId=4&__feature.dashboardSceneSolo=true'
        ],
        // Set Grafik 2 (4 grafik berbeda)
        2 => [
            'http://localhost:3000/d-solo/panel-id-2?orgId=1&panelId=5',
            'http://localhost:3000/d-solo/panel-id-2?orgId=1&panelId=6',
            'http://localhost:3000/d-solo/panel-id-2?orgId=1&panelId=7',
            'http://localhost:3000/d-solo/panel-id-2?orgId=1&panelId=8'
        ],
        // Set Grafik 3 (4 grafik berbeda)
        3 => [
            'http://localhost:3000/d-solo/panel-id-3?orgId=1&panelId=9',
            'http://localhost:3000/d-solo/panel-id-3?orgId=1&panelId=10',
            'http://localhost:3000/d-solo/panel-id-3?orgId=1&panelId=11',
            'http://localhost:3000/d-solo/panel-id-3?orgId=1&panelId=12'
        ],
        // Set Grafik 4 (4 grafik berbeda)
        4 => [
            'http://localhost:3000/d-solo/panel-id-4?orgId=1&panelId=13',
            'http://localhost:3000/d-solo/panel-id-4?orgId=1&panelId=14',
            'http://localhost:3000/d-solo/panel-id-4?orgId=1&panelId=15',
            'http://localhost:3000/d-solo/panel-id-4?orgId=1&panelId=16'
        ]
    ];

    // Nama grafik untuk judul
    private $grafanaTitles = [
        1 => 'Set Grafik 1 - Tahunan',
        2 => 'Set Grafik 2 -',
        3 => 'Set Grafik 3 - ',
        4 => 'Set Grafik 4 -'
    ];

    // Judul individual untuk masing-masing grafik dalam set
    private $panelTitles = [
        1 => ['Total Bocoran - Batas Maksimal - TMA ', 'A1 - TMA', 'B3 - TMA', 'SR - TMA'],
        2 => ['Rata-rata Nilai SR', 'Distribusi SR', 'SR Tertinggi', 'SR Terendah'],
        3 => ['Jenis Pengukuran', 'Periode Pengukuran', 'Distribusi Data', 'Status Pengukuran'],
        4 => ['Bocoran Talang 1', 'Bocoran Talang 2', 'Bocoran Pipa', 'Total Bocoran']
    ];

    public function index($graph_set = 1)
    {
        // Validasi graph_set
        if ($graph_set < 1 || $graph_set > 4) {
            $graph_set = 1;
        }
        
        $data = [
            'current_graph_set' => $graph_set,
            'grafana_urls' => $this->grafanaEmbeds[$graph_set],
            'grafana_title' => $this->grafanaTitles[$graph_set],
            'panel_titles' => $this->panelTitles[$graph_set],
            'title' => 'Grafik Rembesan Bendungan - PT Indonesia Power'
        ];
        
        return view('Grafik/Grafik', $data);
    }
}