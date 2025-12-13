<?php
    use CodeIgniter\Router\RouteCollection;

    /**
     * @var RouteCollection $routes
     */

    $routes->setAutoRoute(false);

    // --- PUBLIC ROUTES (tanpa auth) ---
    $routes->get('/', 'Home::index');
    $routes->get('auth/login', 'Auth\AuthController::login');
    $routes->post('auth/process-login', 'Auth\AuthController::processLogin');
    $routes->get('auth/logout', 'Auth\AuthController::logout');
    $routes->get('auth/check-session', 'Auth\AuthController::checkSession');

    // Debug routes
    $routes->get('debug/login', 'Auth\AuthController::debugLogin');
    $routes->post('auth/reset-admin', 'Auth\AuthController::resetAdminPassword');

    // --- PROTECTED ROUTES (perlu login) ---
    $routes->group('', ['filter' => 'auth'], function($routes) {
        // Menu utama
        $routes->get('menu', 'MenuController::index');
        $routes->get('grafik-data', 'MenuController::grafikData');
        
        // Rembesan Routes
        $routes->get('input-data', 'DataInputController::rembesan');
        $routes->get('rembesan/check-connection', 'Rembesan\CheckConnection::index');
        $routes->post('rembesan/input', 'Rembesan\InputRembesan::index');
        $routes->get('rembesan/get_pengukuran', 'Rembesan\GetPengukuran::index');
        $routes->get('rembesan/cek-data', 'Rembesan\CekDataController::index');
        
        // Data routes
        $routes->get('get-latest-data', 'DataInputController::getLatestData');
        $routes->get('data/create', 'DataInputController::create');
        $routes->post('data/store', 'DataInputController::store');
        $routes->get('data/edit/(:num)', 'DataInputController::edit/$1');
        $routes->post('data/update/(:num)', 'DataInputController::update/$1');
        $routes->delete('data/delete/(:num)', 'DataInputController::delete/$1');
        
        // Import routes
        $routes->post('import-sql', 'ImportController::importSQL');
        $routes->post('import-sql-advanced', 'ImportController::importSQLAdvanced');
        $routes->post('import-sql-file', 'ImportController::importSQLFile');
        
        // Grafik routes
        $routes->get('grafik', 'Grafik::index');
        $routes->get('grafik/panel/(:num)', 'Grafik::panel/$1');
        $routes->get('grafik/(:num)', 'Grafik::index/$1');
        
        // Excel viewer
        $routes->get('data/tabel_thomson', 'ExcelViewerController::tabelThomson');
        $routes->get('lihat/tabel_ambang', 'ExcelViewerController::tabelAmbangBatas');
        
        // Perhitungan
        $routes->get('perhitungan-sr/hitung/(:num)', 'PerhitunganSRController::hitung/$1');
        
        // Analisa Look Burt
        $routes->get('analisaLookBurt', 'AnalisaLookBurt::index');
        $routes->post('analisaLookBurt/save', 'AnalisaLookBurt::save');
        
        // Tambahkan route untuk export Excel rapih
        $routes->get('export/excel-rapih', 'ExportExcelController::exportExcelRapih');
        
        // --- HDM Routes ---
        $routes->get('horizontal-displacement', '\App\Controllers\HDM\HDMController::index');
        $routes->get('horizontal-displacement/data', '\App\Controllers\HDM\HDMController::getData');
        $routes->get('horizontal-displacement/detail/(:num)', '\App\Controllers\HDM\HDMController::detail');
        $routes->get('horizontal-displacement/export-excel', '\App\Controllers\HDM\HDMController::exportExcel');
        $routes->delete('horizontal-displacement/delete/(:num)', '\App\Controllers\HDM\HDMController::delete/$1');
        $routes->get('horizontal-displacement/data-lengkap', '\App\Controllers\HDM\HDMController::dataLengkap');
        $routes->get('horizontal-displacement/create', '\App\Controllers\HDM\HDMController::create');
        $routes->post('horizontal-displacement/store', '\App\Controllers\HDM\HDMController::store');
        $routes->post('horizontal-displacement/check-duplicate', '\App\Controllers\HDM\HDMController::checkDuplicate');
        $routes->get('horizontal-displacement/edit/(:num)', '\App\Controllers\HDM\HDMController::edit/$1');
        $routes->put('horizontal-displacement/update/(:num)', '\App\Controllers\HDM\HDMController::update/$1');
        $routes->post('horizontal-displacement/importSQL', '\App\Controllers\HDM\HDMController::importSQL');
        
        // HDM 625 Routes
        $routes->get('hdm625', 'HDM\Hdm625Controller::index');
        $routes->get('hdm625/detail/(:num)', 'HDM\Hdm625Controller::detail/$1');
        $routes->get('hdm625/export', 'HDM\Hdm625Controller::exportExcel');
        $routes->post('hdm625/recalculate', 'HDM\Hdm625Controller::recalculatePergerakan');
        $routes->get('hdm625/chart-data', 'HDM\Hdm625Controller::chartData');
        $routes->get('hdm625/api', 'HDM\Hdm625Controller::apiData');
        
        // HDM 600 Routes
        $routes->get('hdm600', 'HDM\Hdm600Controller::index');
        $routes->get('hdm600/detail/(:num)', 'HDM\Hdm600Controller::detail/$1');
        $routes->get('hdm600/export', 'HDM\Hdm600Controller::exportExcel');
        $routes->post('hdm600/recalculate', 'HDM\Hdm600Controller::recalculatePergerakan');
        $routes->get('hdm600/chart-data', 'HDM\Hdm600Controller::chartData');
        $routes->get('hdm600/api', 'HDM\Hdm600Controller::apiData');
        $routes->post('hdm600/update-ambang-batas/(:num)', 'HDM\Hdm600Controller::updateAmbangBatas/$1');
        $routes->post('hdm600/insert-default-ambang-batas/(:num)', 'HDM\Hdm600Controller::insertDefaultAmbangBatas/$1');
        
        // --- BTM Routes ---
        $routes->get('btm', 'BTM\BtmController::index');
        $routes->get('btm/create', 'BTM\BtmController::create');
        $routes->post('btm/store', 'BTM\BtmController::store');
        $routes->get('btm/edit/(:num)', 'BTM\BtmController::edit/$1');
        $routes->put('btm/update/(:num)', 'BTM\BtmController::update/$1');
        $routes->delete('btm/delete/(:num)', 'BTM\BtmController::delete/$1');
        $routes->post('btm/calculate-all', 'BTM\BtmController::calculateAll');
        $routes->get('btm/export-excel', 'BTM\BtmController::exportExcel');
        $routes->post('btm/check-duplicate', 'BTM\BtmController::checkDuplicate');
        
        // BTM Import
        $routes->post('btm/import-sql', 'BTM\ImportBtmController::importSQL');
        $routes->post('btm/import-sql-file', 'BTM\ImportBtmController::importSQLFile');
        $routes->post('btm/import-data', 'BTM\ImportBtmController::importData');
        
        // BTM Sub-routes
        $routes->get('btm/bt1', 'BTM\BtmController::bt1');
        $routes->get('btm/bt2', 'BTM\BtmController::bt2');
        $routes->get('btm/bt3', 'BTM\BtmController::bt3');
        $routes->get('btm/bt4', 'BTM\BtmController::bt4');
        $routes->get('btm/bt6', 'BTM\BtmController::bt6');
        $routes->get('btm/bt7', 'BTM\BtmController::bt7');
        $routes->get('btm/bt8', 'BTM\BtmController::bt8');
        
        // --- Extensometer Routes ---
        $routes->get('extenso', 'EXS\ExtensoController::index');
        $routes->get('extenso/ex1', 'EXS\ExtensoController::ex1');
        $routes->get('extenso/ex2', 'EXS\ExtensoController::ex2');
        $routes->get('extenso/ex3', 'EXS\ExtensoController::ex3');
        $routes->get('extenso/ex4', 'EXS\ExtensoController::ex4');
        $routes->get('extenso/create', 'EXS\ExtensoController::create');
        $routes->post('extenso/store', 'EXS\ExtensoController::store');
        $routes->get('extenso/edit/(:num)', 'EXS\ExtensoController::edit/$1');
        $routes->post('extenso/update/(:num)', 'EXS\ExtensoController::update/$1');
        $routes->delete('extenso/delete/(:num)', 'EXS\ExtensoController::delete/$1');
        $routes->get('extenso/export', 'EXS\ExtensoController::exportExcel');
        $routes->get('extenso/grafik-ambang', 'EXS\ExtensoController::grafikAmbang');
        
        // --- Left Piezometer Routes ---
        $routes->get('left-piez', '\App\Controllers\LeftPiez\PiezometerController::index');
        $routes->get('left-piez/create', '\App\Controllers\LeftPiez\PiezometerController::create');
        $routes->post('left-piez/store', '\App\Controllers\LeftPiez\PiezometerController::store');
        $routes->get('left-piez/edit/(:num)', '\App\Controllers\LeftPiez\PiezometerController::edit/$1');
        $routes->put('left-piez/update/(:num)', '\App\Controllers\LeftPiez\PiezometerController::update/$1');
        $routes->post('left-piez/update/(:num)', '\App\Controllers\LeftPiez\PiezometerController::update/$1');
        $routes->delete('left-piez/delete/(:num)', '\App\Controllers\LeftPiez\PiezometerController::delete/$1');
        $routes->post('left-piez/import-sql', '\App\Controllers\LeftPiez\PiezometerController::importSql');
        $routes->post('left-piez/check-duplicate', '\App\Controllers\LeftPiez\PiezometerController::checkDuplicate');
        
        // Left Piezometer Grafik History
        $routes->get('left-piez/grafik-history-l1-l3', 'Leftpiez\GrafikHistoryL1L3::index');
        $routes->get('left_piez/grafik-history-l1-l3', 'Leftpiez\GrafikHistoryL1L3::index');
        $routes->get('left-piez/grafik-history-l1-l3/api', 'Leftpiez\GrafikHistoryL1L3::apiData');
        $routes->get('left-piez/grafik-history-l1-l3/debug', 'Leftpiez\GrafikHistoryL1L3::debugStructure');
        $routes->get('left_piez/grafik-history-l4-l6', 'Leftpiez\GrafikHistoryL4L6::index');
        $routes->get('left_piez/grafik-history-l4-l6/api-data', 'Leftpiez\GrafikHistoryL4L6::apiData');
        $routes->get('left_piez/grafik-history-l4-l6/debug', 'Leftpiez\GrafikHistoryL4L6::debugStructure');
        $routes->get('left_piez/grafik-history-l7-l9', 'Leftpiez\GrafikHistoryL7L9::index');
        $routes->get('left_piez/grafik-history-l7-l9/api-data', 'Leftpiez\GrafikHistoryL7L9::apiData');
        $routes->get('left_piez/grafik-history-l7-l9/debug', 'Leftpiez\GrafikHistoryL7L9::debugStructure');
        $routes->get('left_piez/grafik-history-l10-spz02', 'Leftpiez\GrafikHistoryL10Spz02::index');
        $routes->get('left_piez/grafik-history-l10-spz02/api-data', 'Leftpiez\GrafikHistoryL10Spz02::apiData');
        $routes->get('left_piez/grafik-history-l10-spz02/debug', 'Leftpiez\GrafikHistoryL10Spz02::debugStructure');
        
        // --- Right Piezometer Routes ---
        $routes->get('right-piez', 'Rightpiezo\RightpiezController::index');
        $routes->get('right-piez/create', 'Rightpiezo\RightpiezController::create');
        $routes->post('right-piez/store', 'Rightpiezo\RightpiezController::store');
        $routes->get('right-piez/edit/(:num)', 'Rightpiezo\RightpiezController::edit/$1');
        $routes->post('right-piez/update/(:num)', 'Rightpiezo\RightpiezController::update/$1');
        $routes->delete('right-piez/delete/(:num)', 'Rightpiezo\RightpiezController::delete/$1');
        $routes->post('right-piez/calculate/(:num)', 'Rightpiezo\RightpiezController::calculate/$1');
        $routes->post('right-piez/import-sql', 'Rightpiezo\RightpiezController::importSql');
        $routes->post('right-piez/check-duplicate-edit', 'Rightpiezo\RightpiezController::checkDuplicateEdit');
        
        // --- INCLINO ROUTES (PROTECTED) ---
        $routes->group('inclino', function($routes) {
            // Main Inclino Controller Routes
            $routes->get('/', 'Inclino\InclinoController::index');
            $routes->get('view', 'Inclino\InclinoController::view');
            $routes->get('create', 'Inclino\InclinoController::create');
            $routes->get('edit/(:num)', 'Inclino\InclinoController::edit/$1');
            
            // Routes untuk filter data tabel
            $routes->get('getDataByFilter', 'Inclino\InclinoController::getDataByFilter');
            $routes->get('getMonthsByYear', 'Inclino\InclinoController::getMonthsByYear');
            $routes->get('getDaysByMonth', 'Inclino\InclinoController::getDaysByMonth');
            
            // Import Controller Routes
            $routes->get('import', 'Inclino\ImportController::index');
            $routes->post('import/uploadCSV', 'Inclino\ImportController::uploadCSV');
            $routes->get('import/boreholes', 'Inclino\ImportController::getBoreholeData');
            $routes->get('import/dates/(:any)', 'Inclino\ImportController::getReadingDates/$1');
            $routes->post('import/delete', 'Inclino\ImportController::deleteData');
            $routes->get('import/template', 'Inclino\ImportController::downloadTemplate');
            $routes->get('import/statistics', 'Inclino\ImportController::getStatistics');
            $routes->get('import/test', 'Inclino\ImportController::testConnection');
            
            // Routes untuk testing:
            $routes->get('import/testKoneksi', 'Inclino\ImportController::testKoneksi');
            $routes->get('import/testDbConnection', 'Inclino\ImportController::testDbConnection');
            $routes->get('import/testManualQuery', 'Inclino\ImportController::testManualQuery');
            
            $routes->get('test', function() {
                return "Inclino routes are working!";
            });
        });
        
        // Fallback untuk inclino test
        $routes->get('test-inclino', 'Inclino\ImportController::testKoneksi');
        $routes->get('test-inclino-db', 'Inclino\ImportController::testDbConnection');
    });

    // --- API ROUTES (tanpa auth atau dengan auth khusus) ---
    $routes->get('api/rembesan', 'Api\Rembesan::index');

    // Profil A Routes
$routes->get('inclino/profilea', 'Inclino\ProfileAController::index');
$routes->get('inclino/profilea/view', 'Inclino\ProfileAController::view');
$routes->get('inclino/profilea/getDataByYear', 'Inclino\ProfileAController::getDataByYear');
$routes->get('inclino/profilea/exportToExcel', 'Inclino\ProfileAController::exportToExcel');

// Untuk Profile B
$routes->get('inclino/profileb', 'Inclino\ProfileBController::view');
$routes->get('inclino/profileb/getDataByYear', 'Inclino\ProfileBController::getDataByYear');
$routes->get('inclino/profileb/exportToExcel', 'Inclino\ProfileBController::exportToExcel');