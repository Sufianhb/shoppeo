<?php

declare(strict_types=1);

namespace models;

use core\Model;

class TiendaModel extends Model
{
    public function obtenerTodas(): array
    {
        return $this->fetchAll(
            'SELECT * FROM tiendas WHERE activa = TRUE ORDER BY nombre'
        );
    }

    public function obtenerPorId(int $id): array|false
    {
        return $this->fetchOne(
            'SELECT * FROM tiendas WHERE id = :id',
            [':id' => $id]
        );
    }

    public function crear(array $datos): string
    {
        $sql = "
            INSERT INTO tiendas (nombre, direccion, telefono, latitud, longitud, web)
            VALUES (:nombre, :direccion, :telefono, :latitud, :longitud, :web)
            RETURNING id
        ";
        $stmt = $this->query($sql, [
            ':nombre' => $datos['nombre'],
            ':direccion' => $datos['direccion'],
            ':telefono' => $datos['telefono'],
            ':latitud' => $datos['latitud'],
            ':longitud' => $datos['longitud'],
            ':web' => $datos['web'] ?: null,
        ]);
        return $stmt->fetchColumn();
    }

    // Datos para el mapa: todas las tiendas con sus productos
    public function obtenerParaMapa(): array
    {
        $sql = "
            SELECT
                t.id, t.nombre, t.direccion, t.telefono, t.latitud, t.longitud, t.web,
                COUNT(DISTINCT pr.producto_id) AS num_productos,
                MIN(pr.precio) AS precio_minimo
            FROM tiendas t
            LEFT JOIN precios pr ON pr.tienda_id = t.id
            WHERE t.activa = TRUE
            GROUP BY t.id
            ORDER BY t.nombre
        ";
        return $this->fetchAll($sql);
    }
}
