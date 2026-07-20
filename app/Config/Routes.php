<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DashboardController;

$routes->get('backoffice/dashboard', 'DashboardController::index');