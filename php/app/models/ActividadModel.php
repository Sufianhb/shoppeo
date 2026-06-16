<?php

declare(strict_types=1);

namespace models;

use core\Model;

class ActividadModel extends Model
{
    public function registrarVista(int $productoId, ?int $usuarioId = null): void
    {
        $this->query(
            'INSERT INTO actividad (producto_id, usuario_id, tipo) VALUES (:pid, :uid, :tipo)',
            [':pid' => $productoId, ':uid' => $usuarioId, ':tipo' => 'view']
        );
    }

    public function obtenerRecientes(int $limite = 100): array
    {
        $limite = max(1, min(500, $limite));
        return $this->fetchAll("
            SELECT a.id, a.tipo, a.created_at,
                   p.nombre AS producto, p.id AS producto_id,
                   t.nombre AS tienda
            FROM actividad a
            LEFT JOIN productos p ON p.id = a.producto_id
            LEFT JOIN tiendas   t ON t.id = a.tienda_id
            ORDER BY a.created_at DESC
            LIMIT $limite
        ");
    }

    public function estadisticas(): array
    {
        $hoy = $this->fetchOne("
            SELECT COUNT(*) AS total
            FROM actividad
            WHERE DATE(created_at) = CURRENT_DATE AND tipo = 'view'
        ");

        $total = $this->fetchOne("
            SELECT COUNT(*) AS total FROM actividad WHERE tipo = 'view'
        ");

        $top = $this->fetchOne("
            SELECT p.nombre, COUNT(*) AS visitas
            FROM actividad a
            JOIN productos p ON p.id = a.producto_id
            WHERE a.tipo = 'view'
            GROUP BY p.nombre
            ORDER BY visitas DESC
            LIMIT 1
        ");

        return [
            'hoy' => (int)($hoy['total']  ?? 0),
            'total' => (int)($total['total'] ?? 0),
            'top_producto' => $top ?: null,
        ];
    }
}
