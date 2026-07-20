<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;
use App\Controllers\TarifController;


$routes->get('backoffice/dashboard', 'DashboardController::index');
$routes->get('backoffice/tarif', [TarifController::class, 'index']);
$routes->get('backoffice/tarif/getTarifs', [TarifController::class, 'getTarifs']);
$routes->post('backoffice/tarif/update', [TarifController::class, 'update']);
