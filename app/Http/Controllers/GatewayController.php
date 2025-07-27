<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    /**
     * Reenvía peticiones al microservicio de Productos.
     */
    public function forwardToProductos(Request $request): Response
    {
        $baseUri = 'http://localhost:8001'; // URL del servicio de productos
        return $this->forward($request, $baseUri);
    }

    /**
     * Reenvía peticiones al microservicio de Ventas y Clientes.
     */
    public function forwardToVentasClientes(Request $request): Response
    {
        $baseUri = 'http://localhost:8002'; // URL del servicio de ventas y clientes
        return $this->forward($request, $baseUri);
    }

    /**
     * Lógica de reenvío genérica.
     */
    private function forward(Request $request, string $baseUri): Response
    {
        $path = $request->path();
        $fullUrl = $baseUri . '/' . $path;
        $method = strtolower($request->method());

        $response = Http::withHeaders($request->headers->all())
                        ->$method($fullUrl, $request->all());

        return response($response->body(), $response->status())
            ->withHeaders($response->headers());
    }
}