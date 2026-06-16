<?php

declare(strict_types=1);

namespace models;

use core\Model;

class LogModel extends Model
{
    public function insertar(string $nivel, string $mensaje): void
    {
        $this->query(
            'INSERT INTO logs (nivel, mensaje) VALUES (:nivel, :mensaje)',
            [':nivel' => $nivel, ':mensaje' => $mensaje]
        );
    }

    public function obtenerRecientes(int $limite = 200): array
    {
        $limite = max(1, min(1000, $limite));
        return $this->fetchAll(
            "SELECT id, nivel, mensaje, created_at
             FROM logs
             ORDER BY created_at DESC
             LIMIT $limite"
        );
    }

    public function estadisticas(): array
    {
        $rows = $this->fetchAll("
            SELECT nivel, COUNT(*) AS total
            FROM logs
            GROUP BY nivel
        ");
        $stats = ['info' => 0, 'warning' => 0, 'error' => 0, 'total' => 0];
        foreach ($rows as $r) {
            $stats[$r['nivel']] = (int)$r['total'];
            $stats['total'] += (int)$r['total'];
        }
        return $stats;
    }
}
