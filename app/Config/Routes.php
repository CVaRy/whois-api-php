<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/whois','WhoisController::index');
$routes->get('/whois/(:segment)', 'WhoisController::query/$1');