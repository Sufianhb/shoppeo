<?php
declare(strict_types=1);
namespace models;
use core\Model;

class CanjeModel extends Model
{
    public function crear(int $usuarioId, int $recompensaId, int $puntos, string $direccion): int
    {
        $stmt = $this->query(
            'INSERT INTO canjes (usuario_id, recompensa_id, puntos_usados, direccion_envio)
             VALUES (:uid, :rid, :pts, :dir) RETURNING id',
            [':uid' => $usuarioId, ':rid' => $recompensaId, ':pts' => $puntos, ':dir' => $direccion]
        );
        return (int)$stmt->fetchColumn();
    }

    public function obtenerPorUsuario(int $usuarioId): array
    {
        return $this->fetchAll(
            'SELECT c.*, r.nombre AS recompensa_nombre, r.emoji
             FROM canjes c
             JOIN recompensas r ON r.id = c.recompensa_id
             WHERE c.usuario_id = :uid
             ORDER BY c.created_at DESC',
            [':uid' => $usuarioId]
        );
    }

    public function obtenerTodos(): array
    {
        return $this->fetchAll(
            "SELECT c.*, r.nombre AS recompensa_nombre, r.emoji,
                    u.nombre AS usuario_nombre, u.email AS usuario_email,
                    a.nombre AS admin_nombre
             FROM canjes c
             JOIN recompensas r ON r.id = c.recompensa_id
             JOIN usuarios u ON u.id = c.usuario_id
             LEFT JOIN usuarios a ON a.id = c.admin_id
             ORDER BY
                CASE c.estado WHEN 'pendiente' THEN 0 WHEN 'enviado' THEN 1 ELSE 2 END,
                c.created_at DESC"
        );
    }

    public function obtenerPorId(int $id): array|false
    {
        return $this->fetchOne(
            'SELECT c.*, r.nombre AS recompensa_nombre, r.emoji,
                    u.nombre AS usuario_nombre, u.email AS usuario_email
             FROM canjes c
             JOIN recompensas r ON r.id = c.recompensa_id
             JOIN usuarios u ON u.id = c.usuario_id
             WHERE c.id = :id',
            [':id' => $id]
        );
    }

    public function actualizarEstado(int $id, string $estado, string $notas, int $adminId): void
    {
        $this->query(
            'UPDATE canjes SET estado = :estado, notas_admin = :notas,
             admin_id = :adm, actualizado_at = NOW() WHERE id = :id',
            [':estado' => $estado, ':notas' => $notas, ':adm' => $adminId, ':id' => $id]
        );
    }

    public function statsPorEstado(): array
    {
        $rows  = $this->fetchAll('SELECT estado, COUNT(*) AS total FROM canjes GROUP BY estado');
        $stats = ['pendiente' => 0, 'enviado' => 0, 'completado' => 0, 'cancelado' => 0];
        foreach ($rows as $r) {
            $stats[$r['estado']] = (int)$r['total'];
        }
        return $stats;
    }
}
