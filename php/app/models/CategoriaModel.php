<?php

declare(strict_types=1);

namespace models;

use core\Model;

class CategoriaModel extends Model
{
    public function obtenerTodas(): array
    {
        return $this->fetchAll('SELECT * FROM categorias ORDER BY nombre');
    }
}
