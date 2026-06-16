<?php

declare(strict_types=1);

namespace models;

use core\Model;

class FavoritoModel extends Model
{
    public function toggle(int $usuario_id, int $producto_id): bool
    {
        if ($this->existe($usuario_id, $producto_id)) {
            $this->query(
                'DELETE FROM favoritos WHERE usuario_id = ? AND producto_id = ?',
                [$usuario_id, $producto_id]
            );
            return false;
        }

        $this->query(
            'INSERT INTO favoritos (usuario_id, producto_id) VALUES (?, ?)',
            [$usuario_id, $producto_id]
        );
        return true;
    }

    public function existe(int $usuario_id, int $producto_id): bool
    {
        return $this->fetchOne(
            'SELECT id FROM favoritos WHERE usuario_id = ? AND producto_id = ?',
            [$usuario_id, $producto_id]
        ) !== false;
    }

    public function obtenerPorUsuario(int $usuario_id): array
    {
        return $this->fetchAll(
            'SELECT f.id, p.id AS producto_id, p.nombre, p.imagen_url,
                    MIN(pr.precio) AS precio_minimo
             FROM favoritos f
             JOIN productos p ON p.id = f.producto_id
             LEFT JOIN precios pr ON pr.producto_id = p.id
             WHERE f.usuario_id = ?
             GROUP BY f.id, p.id, p.nombre, p.imagen_url
             ORDER BY f.created_at DESC',
            [$usuario_id]
        );
    }

    public function contar(int $usuario_id): int
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS total FROM favoritos WHERE usuario_id = ?',
            [$usuario_id]
        );
        return (int)($row['total'] ?? 0);
    }

    public function idsDeUsuario(int $usuario_id): array
    {
        $rows = $this->fetchAll(
            'SELECT producto_id FROM favoritos WHERE usuario_id = ?',
            [$usuario_id]
        );
        return array_column($rows, 'producto_id');
    }
}
