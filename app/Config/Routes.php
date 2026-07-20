<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');

// Traitement du bouton "Se connecter"
$routes->post('home/connect', 'HomeController::connect');

// Page après connexion
$routes->get('client/home', 'ClientController::home');
$routes->get('backoffice/dashboard', 'DashboardController::index');
