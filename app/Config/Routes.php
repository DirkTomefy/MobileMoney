<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;
use App\Controllers\TarifController;


$routes->get('backoffice/dashboard', 'DashboardController::index');


$routes->get('backoffice/tarif', 'TarifController::index');
$routes->get('backoffice/tarif/getTarifs', 'TarifController::getTarifs');
$routes->post('backoffice/tarif/update', 'TarifController::update');
