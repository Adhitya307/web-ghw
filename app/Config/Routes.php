<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

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