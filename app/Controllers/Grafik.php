<?php

namespace App\Controllers;

class Grafik extends BaseController
{
    // URL embed Grafana untuk masing-masing set grafik
    private $grafanaEmbeds = [
        // Set Grafik 1 → All tahun
        1 => [
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672506000000&to=1756966755511&timezone=browser&var-tahun=$__all&var-query0=&var-query0-2=&panelId=1&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672506000000&to=1756966755511&timezone=browser&var-tahun=$__all&var-query0=&var-query0-2=&panelId=2&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672506000000&to=1756966755511&timezone=browser&var-tahun=$__all&var-query0=&var-query0-2=&panelId=3&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&from=1672506000000&to=1756966755511&timezone=browser&var-tahun=$__all&var-query0=&var-query0-2=&panelId=4&__feature.dashboardSceneSolo=true'
        ],
        // Set Grafik 2 → Filter tahun (misal 2023)
        2 => [
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=1&var-tahun=2023&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=2&var-tahun=2023&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=3&var-tahun=2023&__feature.dashboardSceneSolo=true',
            'http://localhost:3000/d-solo/f50b2824-acb5-4b55-9cab-72af27e8e8c5/monitoring-rembesan?orgId=1&panelId=4&var-tahun=2023&__feature.dashboardSceneSolo=true'
        ],
        // Set Grafik 3 & 4 → isi sesuai kebutuhan
        3 => ['', '', '', ''],
        4 => ['', '', '', '']
    ];

    // Judul set grafik
    private $grafanaTitles = [
        1 => 'Set Grafik 1 - Semua Tahun',
        2 => 'Set Grafik 2 - Filter Tahun',
        3 => 'Set Grafik 3',
        4 => 'Set Grafik 4'
    ];

    // Judul tiap panel
    private $panelTitles = [
        1 => ['Total Bocoran - Batas Maksimal - TMA', 'A1 - TMA', 'B3 - TMA', 'SR - TMA'],
        2 => ['Total Bocoran - Batas Maksimal - TMA', 'A1 - TMA', 'B3 - TMA', 'SR - TMA'],
        3 => ['Panel 1','Panel 2','Panel 3','Panel 4'],
        4 => ['Panel 1','Panel 2','Panel 3','Panel 4']
    ];

    public function index($graph_set = 1)
    {
        if ($graph_set < 1 || $graph_set > 4) $graph_set = 1;

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
