<?php
declare(strict_types=1);
namespace models;
use core\Model;

class RecompensaModel extends Model
{
    public function obtenerActivas(): array
    {
        return $this->fetchAll(
            'SELECT * FROM recompensas WHERE activo = TRUE ORDER BY puntos_necesarios ASC'
        );
    }

    public function obtenerPorId(int $id): array|false
    {
        return $this->fetchOne(
            'SELECT * FROM recompensas WHERE id = :id AND activo = TRUE',
            [':id' => $id]
        );
    }

    public function obtenerTodas(): array
    {
        return $this->fetchAll(
            'SELECT * FROM recompensas ORDER BY puntos_necesarios ASC'
        );
    }
}
