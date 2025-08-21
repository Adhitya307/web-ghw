<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('rembesan/check-connection', 'Rembesan\CheckConnection::index');

$routes->post('rembesan/input', 'Rembesan\InputRembesan::index');

$routes->get('rembesan/get_pengukuran', 'Rembesan\GetPengukuran::index');

$routes->get('rembesan/cek-data', 'Rembesan\CekDataController::index');