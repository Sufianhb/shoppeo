<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\FavoritoModel;

class FavoritosController extends Controller
{
    private FavoritoModel $favoritoModel;

    public function __construct()
    {
        $this->favoritoModel = new FavoritoModel();
    }

    // GET /favoritos
    public function index(array $params): void
    {
        $this->requireAuth();
        $userId   = (int)$_SESSION['user_id'];
        $favoritos = $this->favoritoModel->obtenerPorUsuario($userId);

        $this->render('public/favoritos', [
            'title'=> 'Mi lista — shoppeo',
            'favoritos'=> $favoritos,
            'favoritos_ids' => array_column($favoritos, 'producto_id'),
        ]);
    }

    // POST /favoritos/toggle
    public function toggle(array $params): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['success' => false, 'redirect' => '/login'], 401);
            return;
        }

        $userId    = (int)$_SESSION['user_id'];
        $body      = json_decode(file_get_contents('php://input'), true) ?? [];
        $productoId = (int)($body['producto_id'] ?? 0);

        if (!$productoId) {
            $this->json(['success' => false, 'error' => 'producto_id requerido'], 400);
            return;
        }

        $added = $this->favoritoModel->toggle($userId, $productoId);
        $count = $this->favoritoModel->contar($userId);

        $this->json(['success' => true, 'added' => $added, 'count' => $count]);
    }
}
