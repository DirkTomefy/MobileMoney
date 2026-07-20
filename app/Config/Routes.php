<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================
// Routes publiques (connexion)
// ==========================================
$routes->get('/', 'HomeController::index');
$routes->post('home/connect', 'HomeController::connect');
$routes->post('home/connectOperateur', 'HomeController::connectOperateur');

// ==========================================
// Routes frontoffice (client connecté)
// ==========================================
$routes->group('client', ['namespace' => 'App\Controllers'], function ($routes) {
    // Solde
    $routes->get('home', 'SoldeController::index');
    $routes->get('solde', 'SoldeController::index');
    $routes->get('solde/action/(:any)', 'SoldeController::action/$1');

    // Transaction
    $routes->get('transaction', 'TransactionController::index');
    $routes->post('deposer/save', 'TransactionController::saveDeposer');
    $routes->post('retirer/save', 'TransactionController::saveRetirer');
    $routes->post('transferer/save', 'TransactionController::saveTransferer');
});

// ==========================================
// Routes backoffice (opérateur connecté)
// ==========================================
$routes->group('backoffice', ['namespace' => 'App\Controllers'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Portefeuille
    $routes->get('portefeuille', 'PortefeuilleController::index');

    // Tarifs
    $routes->get('tarif', 'TarifController::index');
    $routes->get('tarif/getTarifs', 'TarifController::getTarifs');
    $routes->post('tarif/update', 'TarifController::update');

    // Préfixes (CRUD)
    $routes->get('prefix', 'PrefixController::index');
    $routes->get('prefix/create', 'PrefixController::create');
    $routes->post('prefix/store', 'PrefixController::store');
    $routes->get('prefix/edit/(:num)', 'PrefixController::edit/$1');
    $routes->post('prefix/update/(:num)', 'PrefixController::update/$1');
    $routes->get('prefix/delete/(:num)', 'PrefixController::delete/$1');
});