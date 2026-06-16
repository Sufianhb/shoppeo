<?php

declare(strict_types=1);

namespace models;

use core\Model;

class PrecioModel extends Model
{
    // Obtener todos los precios con info de producto y tienda
    public function obtenerTodos(): array
    {
        $sql = "
            SELECT pr.id, pr.precio, pr.stock, pr.actualizado_at,
                   p.id AS producto_id, p.nombre AS producto,
                   t.id AS tienda_id,   t.nombre AS tienda
            FROM precios pr
            JOIN productos p ON p.id = pr.producto_id
            JOIN tiendas t ON t.id = pr.tienda_id
            WHERE p.activo = TRUE AND t.activa = TRUE
            ORDER BY p.nombre, t.nombre
        ";
        return $this->fetchAll($sql);
    }

    // Obtener precio anterior antes de actualizar
    public function obtenerPrecioActual(int $productoId, int $tiendaId): float|false
    {
        $row = $this->fetchOne(
            'SELECT precio FROM precios WHERE producto_id = :p AND tienda_id = :t',
            [':p' => $productoId, ':t' => $tiendaId]
        );
        return $row ? (float)$row['precio'] : false;
    }

    // Actualizar o insertar precio
    public function upsert(int $productoId, int $tiendaId, float $precio, int $stock): void
    {
        $sql = "
            INSERT INTO precios (producto_id, tienda_id, precio, stock, actualizado_at)
            VALUES (:producto_id, :tienda_id, :precio, :stock, NOW())
            ON CONFLICT (producto_id, tienda_id)
            DO UPDATE SET precio = EXCLUDED.precio,
                          stock  = EXCLUDED.stock,
                          actualizado_at = NOW()
        ";
        $this->query($sql, [
            ':producto_id' => $productoId,
            ':tienda_id' => $tiendaId,
            ':precio' => $precio,
            ':stock' => $stock,
        ]);
    }

    // Comparador: todos los precios de un producto ordenados
    public function comparar(int $productoId): array
    {
        $sql = "
            SELECT * FROM comparador_precios
            WHERE producto_id = :id
            ORDER BY precio ASC
        ";
        return $this->fetchAll($sql, [':id' => $productoId]);
    }
}
