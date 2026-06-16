<?php
declare(strict_types=1);
namespace models;
use core\Model;

class PuntosModel extends Model
{
    public function totalPuntos(int $usuarioId): int
    {
        $row = $this->fetchOne(
            'SELECT COALESCE(SUM(puntos),0) AS total FROM puntos_transacciones WHERE usuario_id = :uid',
            [':uid' => $usuarioId]
        );
        return (int)($row['total'] ?? 0);
    }

    public function transacciones(int $usuarioId, int $limit = 15): array
    {
        return $this->fetchAll(
            'SELECT * FROM puntos_transacciones WHERE usuario_id = :uid ORDER BY created_at DESC LIMIT :lim',
            [':uid' => $usuarioId, ':lim' => $limit]
        );
    }

    public function otorgar(int $usuarioId, int $puntos, string $concepto, ?int $adminId = null): void
    {
        $this->query(
            'INSERT INTO puntos_transacciones (usuario_id, puntos, concepto, admin_id)
             VALUES (:uid, :pts, :con, :adm)',
            [':uid' => $usuarioId, ':pts' => $puntos, ':con' => $concepto, ':adm' => $adminId]
        );
    }

    public function descontar(int $usuarioId, int $puntos, string $concepto): void
    {
        $this->query(
            'INSERT INTO puntos_transacciones (usuario_id, puntos, concepto)
             VALUES (:uid, :pts, :con)',
            [':uid' => $usuarioId, ':pts' => -abs($puntos), ':con' => $concepto]
        );
    }

    public function todasTransacciones(int $limit = 100): array
    {
        return $this->fetchAll(
            'SELECT pt.*, u.nombre AS usuario_nombre, u.email AS usuario_email,
                    a.nombre AS admin_nombre
             FROM puntos_transacciones pt
             JOIN usuarios u ON u.id = pt.usuario_id
             LEFT JOIN usuarios a ON a.id = pt.admin_id
             ORDER BY pt.created_at DESC LIMIT :lim',
            [':lim' => $limit]
        );
    }

    public function rankingUsuarios(): array
    {
        return $this->fetchAll(
            'SELECT u.id, u.nombre, u.email,
                    COALESCE(SUM(pt.puntos),0) AS total_puntos
             FROM usuarios u
             LEFT JOIN puntos_transacciones pt ON pt.usuario_id = u.id
             WHERE u.activo = TRUE
             GROUP BY u.id, u.nombre, u.email
             ORDER BY total_puntos DESC
             LIMIT 10'
        );
    }

    public function statsDia(): array
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS transacciones,
                    COALESCE(SUM(CASE WHEN puntos > 0 THEN puntos ELSE 0 END),0) AS puntos_otorgados
             FROM puntos_transacciones
             WHERE created_at >= NOW() - INTERVAL '24 hours'"
        );
        return $row ?: ['transacciones' => 0, 'puntos_otorgados' => 0];
    }
}
