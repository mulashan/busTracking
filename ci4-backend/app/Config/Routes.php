<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AdminController::dashboard');
$routes->get('admin', 'AdminController::dashboard');
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/database', 'AdminController::dashboard/database');
$routes->get('admin/route', 'AdminController::dashboard/route');
$routes->get('admin/tracking', 'AdminController::dashboard/tracking');
$routes->get('admin/requests', 'AdminController::dashboard/requests');
$routes->get('admin/settings', 'AdminController::dashboard/settings');
$routes->post('admin/buses', 'AdminController::storeBus');
$routes->post('admin/drivers', 'AdminController::storeDriver');
$routes->post('admin/students', 'AdminController::storeStudent');
$routes->post('admin/routes', 'AdminController::storeRoute');
$routes->post('admin/stops', 'AdminController::storeStop');
$routes->post('admin/assignments', 'AdminController::assignStudent');
$routes->post('admin/trips', 'AdminController::storeTrip');
$routes->post('admin/trips/(:num)/complete', 'AdminController::completeTrip/$1');

$routes->post('api/devices/(:segment)/locations', 'Api\\GpsLocationController::store/$1');
$routes->get('api/buses/(:num)/location', 'Api\\BusLocationController::show/$1');
$routes->get('api/buses/(:num)/history', 'Api\\BusLocationController::history/$1');
