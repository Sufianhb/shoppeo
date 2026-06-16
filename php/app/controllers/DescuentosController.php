<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\DescuentoModel;

class DescuentosController extends Controller
{
    private DescuentoModel $descuentoModel;

    public function __construct()
    {
        $this->descuentoModel = new DescuentoModel();
    }

    // GET /descuentos
    public function index(array $params): void
    {
        $descuentos  = $this->descuentoModel->obtenerActivos();
        $categorias  = [];
        foreach ($descuentos as $d) {
            if ($d['categoria_nombre'] && !in_array($d['categoria_nombre'], $categorias)) {
                $categorias[] = $d['categoria_nombre'];
            }
        }

        $this->render('public/descuentos', [
            'title'      => 'Mis Descuentos — shoppeo',
            'descuentos' => $descuentos,
            'categorias' => $categorias,
        ]);
    }

    // POST /descuentos/usar  (registra uso del código)
    public function usar(array $params): void
    {
        $id = (int)($_POST['descuento_id'] ?? 0);
        if ($id) {
            $this->descuentoModel->incrementarUso($id);
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }
}
