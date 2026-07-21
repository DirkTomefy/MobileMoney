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
$routes->get('home/disconnect', 'HomeController::disconnect');


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

$routes->get('backoffice/portefeuille', 'PortefeuilleController::index');

$routes->get(
    'client/info-numero',
    'TransactionController::getInfoNumero'
);
$routes->get(
    'client/get-commission',
    'TransactionController::getCommission'
);
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
    $routes->post('prefix/store', 'PrefixController::store'); // historique
    $routes->get('prefix/edit/(:num)', 'PrefixController::edit/$1');
    $routes->post('prefix/update/(:num)', 'PrefixController::update/$1');
    $routes->get('prefix/delete/(:num)', 'PrefixController::delete/$1');


    //commission
    $routes->get('commission', 'CommissionController::index');
    $routes->get('commission/create', 'CommissionController::create');
    $routes->post('commission/store', 'CommissionController::store');  // historique
    $routes->get('commission/edit/(:num)', 'CommissionController::edit/$1');
    $routes->post('commission/update/(:num)', 'CommissionController::update/$1');
    $routes->get('commission/delete/(:num)', 'CommissionController::delete/$1');
});

$routes->get('client/info-numero', 'TransactionController::getInfoNumero');
$routes->get('client/get-commission', 'TransactionController::getCommission');
$routes->get('client/get-frais-transfert', 'TransactionController::getFraisTransfert');
