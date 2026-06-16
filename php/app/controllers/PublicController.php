<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\ProductoModel;
use models\TiendaModel;
use models\FavoritoModel;

class PublicController extends Controller
{
    private ProductoModel $productoModel;
    private TiendaModel $tiendaModel;
    private FavoritoModel $favoritoModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->tiendaModel   = new TiendaModel();
        $this->favoritoModel = new FavoritoModel();
    }

    //GET /
    public function index(array $params): void
    {
        $rawTags       = \core\RedisService::getTopTags(15);
        $tags_populares = $this->productoModel->filtrarTagsExistentes($rawTags, 4);

        $this->render('public/inicio', [
            'title'          => 'shoppeo — Comparador de Precios',
            'chollos'        => $this->productoModel->obtenerChollos(6),
            'tags_populares' => $tags_populares,
        ]);
    }

    // GET /buscar?q=termino&categoria=X&lat=Y&lng=Z&radius_km=W&precio_min=A&precio_max=B
    public function buscar(array $params): void
    {
        $termino    = trim($_GET['q'] ?? '');
        $categoria  = trim($_GET['categoria'] ?? '');
        $lat        = isset($_GET['lat'])        && $_GET['lat']        !== '' ? (float)$_GET['lat']        : null;
        $lng        = isset($_GET['lng'])        && $_GET['lng']        !== '' ? (float)$_GET['lng']        : null;
        $radius_km  = isset($_GET['radius_km'])  && $_GET['radius_km']  !== '' ? (float)$_GET['radius_km']  : null;
        $precio_min = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? (float)$_GET['precio_min'] : null;
        $precio_max = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? (float)$_GET['precio_max'] : null;

        // Buscar siempre; sin filtros devuelve todos los productos activos
        if (strlen($termino) === 1) {
            $productos = []; // término demasiado corto, esperar más caracteres
        } else {
            $productos = $this->productoModel->buscar(
                $termino, $categoria ?: null,
                $lat, $lng, $radius_km,
                $precio_min, $precio_max
            );
            // Registrar término en Redis para tags populares (solo búsquedas reales)
            if (strlen($termino) >= 2) {
                \core\RedisService::incrementTag($termino);
            }
        }

        $userId       = (int)($_SESSION['user_id'] ?? 0);
        $favoritosIds = $userId ? $this->favoritoModel->idsDeUsuario($userId) : [];

        if ($termino && $categoria) {
            $title = "\"$termino\" en $categoria — shoppeo";
        } elseif ($categoria) {
            $title = "$categoria — shoppeo";
        } elseif ($termino) {
            $title = "Resultados para \"$termino\" — shoppeo";
        } else {
            $title = 'Productos — shoppeo';
        }

        $this->render('public/buscar', [
            'title'         => $title,
            'termino'       => $termino,
            'categoria'     => $categoria,
            'lat'           => $lat,
            'lng'           => $lng,
            'radius_km'     => $radius_km,
            'precio_min'    => $precio_min,
            'precio_max'    => $precio_max,
            'productos'     => $productos,
            'favoritos_ids' => $favoritosIds,
        ]);
    }

    //  GET /producto/{id}
    public function producto(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $producto = $this->productoModel->obtenerConPrecios($id);

        if (!$producto) {
            http_response_code(404);
            $this->render('layouts/404', ['title' => 'Producto no encontrado']);
            return;
        }

        // Registrar visita real en BD
        (new \models\ActividadModel())->registrarVista($id, $_SESSION['user_id'] ?? null);

        $userId     = (int)($_SESSION['user_id'] ?? 0);
        $esFavorito = $userId ? $this->favoritoModel->existe($userId, $id) : false;

        $this->render('public/producto', [
            'title'      => $producto['nombre'] . ' — shoppeo',
            'producto'   => $producto,
            'esFavorito' => $esFavorito,
        ]);
    }

    // GET|POST /ayuda
    public function ayuda(array $params): void
    {
        $enviado = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sólo registramos el intento; en producción aquí iría un mailer
            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => '¡Mensaje recibido! Te responderemos en menos de 24 h.',
            ];
            header('Location: /ayuda');
            exit;
        }
        $this->render('public/ayuda', [
            'title' => 'Ayuda y Contacto — shoppeo',
        ]);
    }

    //  GET /mapa
    public function mapa(array $params): void
    {
        $tiendas = $this->tiendaModel->obtenerParaMapa();
        $this->render('public/mapa', [
            'title' => 'Mapa de Tiendas — shoppeo',
            'tiendas' => $tiendas,
        ]);
    }
}
