<?php

declare(strict_types=1);

namespace models;

use core\Model;

class ConfiguracionModel extends Model
{
    public function obtenerTodas(): array
    {
        return $this->fetchAll('SELECT id, clave, valor FROM configuracion ORDER BY clave');
    }

    public function obtener(string $clave): string
    {
        $row = $this->fetchOne(
            'SELECT valor FROM configuracion WHERE clave = :clave',
            [':clave' => $clave]
        );
        return $row ? (string)$row['valor'] : '';
    }

    public function actualizar(string $clave, string $valor): void
    {
        $this->query(
            'UPDATE configuracion SET valor = :valor WHERE clave = :clave',
            [':valor' => $valor, ':clave' => $clave]
        );
    }
}
