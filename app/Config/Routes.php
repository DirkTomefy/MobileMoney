<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');

// Traitement du bouton "Se connecter"
$routes->post('home/connect', 'HomeController::connect');

// Page après connexion
$routes->get('client/home', 'ClientController::home');