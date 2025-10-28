    <?php
    use CodeIgniter\Router\RouteCollection;

    /**
     * @var RouteCollection $routes
     */

    $routes->setAutoRoute(false);


    // --- Rembesan (non API) ---
    $routes->get('rembesan/check-connection', 'Rembesan\CheckConnection::index');
    $routes->post('rembesan/input', 'Rembesan\InputRembesan::index');
    $routes->get('rembesan/get_pengukuran', 'Rembesan\GetPengukuran::index');
    $routes->get('rembesan/cek-data', 'Rembesan\CekDataController::index');

    // web routes
    $routes->get('/menu', 'MenuController::index');
    $routes->get('/input-data', 'DataInputController::rembesan');
    $routes->get('/grafik-data', 'MenuController::grafikData');
    $routes->get('/data/tabel_thomson', 'ExcelViewerController::tabelThomson');
    $routes->get('lihat/tabel_ambang', 'ExcelViewerController::tabelAmbangBatas');
    $routes->get('perhitungan-sr/hitung/(:num)', 'PerhitunganSRController::hitung/$1');
    $routes->get('api/rembesan', 'Api\Rembesan::index');

    $routes->get('get-latest-data', 'DataInputController::getLatestData');

    // Data Input Routes
    $routes->get('data/create', 'DataInputController::create');
    $routes->post('data/store', 'DataInputController::store');
    $routes->get('data/edit/(:num)', 'DataInputController::edit/$1');
    $routes->post('data/update/(:num)', 'DataInputController::update/$1');
    $routes->delete('data/delete/(:num)', 'DataInputController::delete/$1');
    $routes->get('get-latest-data', 'DataInputController::getLatestData');

    $routes->post('import-sql', 'ImportController::importSQL');
    $routes->post('import-sql-advanced', 'ImportController::importSQLAdvanced');
    $routes->post('import-sql-file', 'ImportController::importSQLFile');

    $routes->get('grafik', 'Grafik::index');
    $routes->get('grafik/panel/(:num)', 'Grafik::panel/$1');

    
    // Pastikan route untuk grafik ada di atas route default
    $routes->get('grafik', 'Grafik::index');
    $routes->get('grafik/(:num)', 'Grafik::index/$1');

    $routes->get('/', 'Home::index');

    // Analisa Look Burt
    $routes->get('/analisaLookBurt', 'AnalisaLookBurt::index');
    $routes->post('/analisaLookBurt/save', 'AnalisaLookBurt::save');

    // HDM Routes
$routes->get('horizontal-displacement', '\App\Controllers\HDM\HDMController::index');
$routes->get('horizontal-displacement/data', '\App\Controllers\HDM\HDMController::getData');
$routes->get('horizontal-displacement/detail/(:num)', '\App\Controllers\HDM\HDMController::detail');
$routes->get('horizontal-displacement/export-excel', '\App\Controllers\HDM\HDMController::exportExcel');
$routes->delete('horizontal-displacement/delete/(:num)', '\App\Controllers\HDM\HDMController::delete/$1');
$routes->get('horizontal-displacement/data-lengkap', '\App\Controllers\HDM\HDMController::dataLengkap');

// Add Data Routes
$routes->get('horizontal-displacement/create', '\App\Controllers\HDM\HDMController::create');
$routes->post('horizontal-displacement/store', '\App\Controllers\HDM\HDMController::store');
$routes->post('horizontal-displacement/check-duplicate', '\App\Controllers\HDM\HDMController::checkDuplicate');

$routes->get('horizontal-displacement/edit/(:num)', '\App\Controllers\HDM\HDMController::edit/$1');
$routes->put('horizontal-displacement/update/(:num)', '\App\Controllers\HDM\HDMController::update/$1');

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

// IMPORT SQL ROUTE - INI YANG PERLU DIPASTIKAN
// Di file app/Config/Routes.php
$routes->post('horizontal-displacement/importSQL', 'HDM\HDMController::importSQL');
$routes->get('horizontal-displacement/export-excel', 'HDM\HDMController::exportExcel');