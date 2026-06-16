<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\ProductoModel;
use models\TiendaModel;
use models\PrecioModel;

/**
 * Responde siempre en JSON
 */
class ApiController extends Controller
{
    private ProductoModel $productoModel;
    private TiendaModel $tiendaModel;
    private PrecioModel $precioModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->tiendaModel = new TiendaModel();
        $this->precioModel = new PrecioModel();
    }

    //  GET /api/sugerencias?q=termino  (autocomplete navbar)
    public function sugerencias(array $params): void
    {
        $termino = trim($_GET['q'] ?? '');
        if (strlen($termino) < 1) {
            $this->json(['ok' => true, 'data' => []]);
            return;
        }
        $datos = $this->productoModel->sugerencias($termino);
        $this->json(['ok' => true, 'data' => $datos]);
    }

    //  GET /api/chollos?lat=X&lng=Y&radius=25&limit=6
    public function chollos(array $params): void
    {
        $lat    = isset($_GET['lat'])    && $_GET['lat']    !== '' ? (float)$_GET['lat']    : null;
        $lng    = isset($_GET['lng'])    && $_GET['lng']    !== '' ? (float)$_GET['lng']    : null;
        $radius = isset($_GET['radius']) && $_GET['radius'] !== '' ? (float)$_GET['radius'] : 25.0;
        $limit  = isset($_GET['limit'])  && $_GET['limit']  !== '' ? (int)$_GET['limit']    : 6;

        if ($lat === null || $lng === null) {
            $this->json(['ok' => false, 'error' => 'lat/lng requeridos'], 400);
            return;
        }

        $data = $this->productoModel->obtenerCholloscercanos($lat, $lng, $radius, $limit);
        $this->json(['ok' => true, 'data' => $data]);
    }

    //  GET /api/tags-populares
    public function tagsPopulares(array $params): void
    {
        $rawTags = \core\RedisService::getTopTags(15);
        $tags    = $this->productoModel->filtrarTagsExistentes($rawTags, 4);
        $this->json(['ok' => true, 'data' => $tags]);
    }

    //  GET /api/productos?q=termino&lat=Y&lng=Z&radius_km=W&precio_min=A&precio_max=B
    public function productos(array $params): void
    {
        $termino  = trim($_GET['q'] ?? '');
        $categoria  = trim($_GET['categoria'] ?? '') ?: null;
        $lat = isset($_GET['lat']) && $_GET['lat']!== '' ? (float)$_GET['lat'] : null;
        $lng = isset($_GET['lng']) && $_GET['lng']!== '' ? (float)$_GET['lng'] : null;
        $radius_km  = isset($_GET['radius_km'])  && $_GET['radius_km']  !== '' ? (float)$_GET['radius_km']  : null;
        $precio_min = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? (float)$_GET['precio_min'] : null;
        $precio_max = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? (float)$_GET['precio_max'] : null;

        $productos = strlen($termino) >= 2
            ? $this->productoModel->buscar($termino, $categoria, $lat, $lng, $radius_km, $precio_min, $precio_max)
            : $this->productoModel->obtenerTodos();

        $this->json(['ok' => true, 'data' => $productos]);
    }

    //  GET /api/productos/{id} 
    public function producto(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $producto = $this->productoModel->obtenerConPrecios($id);

        if (!$producto) {
            $this->json(['ok' => false, 'error' => 'Producto no encontrado'], 404);
            return;
        }

        $this->json(['ok' => true, 'data' => $producto]);
    }

    //  GET /api/tiendas ─
    public function tiendas(array $params): void
    {
        $tiendas = $this->tiendaModel->obtenerTodas();
        $this->json(['ok' => true, 'data' => $tiendas]);
    }

    //  GET /api/mapa 
    public function mapa(array $params): void
    {
        $tiendas = $this->tiendaModel->obtenerParaMapa();
        $this->json(['ok' => true, 'data' => $tiendas]);
    }

    //  POST /api/ws-notify 
    // Endpoint interno que puede llamar Node.js si necesita confirmar algo
    public function wsNotify(array $params): void
    {
        // Solo accesible desde red interna Docker
        $secret = $_SERVER['HTTP_X_WS_SECRET'] ?? '';
        if ($secret !== WS_SECRET) {
            $this->json(['error' => 'Forbidden'], 403);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->json(['ok' => true, 'received' => $body]);
    }

    // endpoint de la geolocalizacion 
    public function location()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $lat = $data['lat'] ?? null;
        $lon = $data['lon'] ?? null;

        if (!$lat || !$lon) {
            echo json_encode(["error" => "coords missing"]);
            return;
        }

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon&accept-language=es";

        $opts = [
            "http" => [
                "header" => "User-Agent: Shoppeo/1.0\r\n"
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        $result = json_decode($response, true);

        $city = $result['address']['city']
            ?? $result['address']['town']
            ?? $result['address']['village']
            ?? "Desconocido";

        echo json_encode(["city" => $city]);
    }
}
