<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;
use App\Controllers\TarifController;

/**
 * @var RouteCollection $routes
 */




$routes->get('/', 'HomeController::index');

// Traitement du bouton "Se connecter"
$routes->post('home/connect', 'HomeController::connect');

// Page après connexion
$routes->get('client/home', 'ClientController::home');

$routes->get('backoffice/dashboard', 'DashboardController::index');


$routes->get('backoffice/tarif', 'TarifController::index');
$routes->get('backoffice/tarif/getTarifs', 'TarifController::getTarifs');
$routes->post('backoffice/tarif/update', 'TarifController::update');
$routes->post('home/connectOperateur', 'HomeController::connectOperateur');