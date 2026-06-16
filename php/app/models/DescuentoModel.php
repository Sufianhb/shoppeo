<?php
declare(strict_types=1);
namespace models;
use core\Model;

class DescuentoModel extends Model
{
    public function obtenerActivos(): array
    {
        return $this->fetchAll(
            "SELECT d.*, c.nombre AS categoria_nombre
             FROM descuentos d
             LEFT JOIN categorias c ON c.id = d.categoria_id
             WHERE d.activo = TRUE
               AND (d.fecha_fin IS NULL OR d.fecha_fin >= CURRENT_DATE)
               AND (d.max_usos = -1 OR d.usos_actuales < d.max_usos)
             ORDER BY d.valor DESC, d.id ASC"
        );
    }

    public function obtenerTodos(): array
    {
        return $this->fetchAll(
            "SELECT d.*, c.nombre AS categoria_nombre
             FROM descuentos d
             LEFT JOIN categorias c ON c.id = d.categoria_id
             ORDER BY d.activo DESC, d.valor DESC"
        );
    }

    public function incrementarUso(int $id): void
    {
        $this->query(
            'UPDATE descuentos SET usos_actuales = usos_actuales + 1 WHERE id = :id',
            [':id' => $id]
        );
    }
}
