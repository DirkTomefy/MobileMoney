<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;
use App\Controllers\TarifController;

/**
 * @var RouteCollection $routes
 */




$routes->get('/', 'HomeController::index');

$routes->post('home/connect', 'HomeController::connect');

$routes->get('client/home', 'SoldeController::index');

$routes->get('backoffice/dashboard', 'DashboardController::index');


$routes->get('backoffice/tarif', 'TarifController::index');
$routes->get('backoffice/tarif/getTarifs', 'TarifController::getTarifs');
$routes->post('backoffice/tarif/update', 'TarifController::update');
$routes->post('home/connectOperateur', 'HomeController::connectOperateur');

$routes->group('client', function ($routes) {
    $routes->get('solde', 'SoldeController::index');
    $routes->get('solde/action/(:any)', 'SoldeController::action/$1');
});

$routes->get('backoffice/portefeuille', 'PortefeuilleController::index');