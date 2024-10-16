<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->post('/login', '\App\Controllers\Api\v1\Login::index'); // For login
$routes->post('/register','\App\Controllers\Api\v1\Register::signup'); // for register
$routes->get('/admin','\App\Controllers\Api\v1\Admin::index'); // for access of admin panel
$routes->get('/user','\App\Controllers\Api\v1\Login::checkuser'); // for access of user panel
$routes->post('/admin/update/(:num)','\App\Controllers\Api\v1\Admin::adminupdate/$1'); // for updating user through admin panel
$routes->post('/admin/delete/(:num)','\App\Controllers\Api\v1\Admin::admindelete/$1'); // for deleting user though admin panel
$routes->post('/admin/view','\App\Controllers\Api\v1\Admin::viewadmin'); // for viewing all users through admin panel
$routes->post('/admin/add','\App\Controllers\Api\v1\Admin::adminadd'); // for adding users thourhg admin panel