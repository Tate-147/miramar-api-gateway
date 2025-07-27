<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// Ruta de prueba del Gateway
$router->get('/', function () {
    return 'API Gateway funcionando correctamente.';
});

// Rutas que apuntan a miramar-productos
$router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/servicios[/{any:.*}]', 'GatewayController@forwardToProductos');
$router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/paquetes[/{any:.*}]', 'GatewayController@forwardToProductos');

// Rutas que apuntan a miramar-ventas-clientes
$router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/clientes[/{any:.*}]', 'GatewayController@forwardToVentasClientes');
$router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/ventas[/{any:.*}]', 'GatewayController@forwardToVentasClientes');