<?php

declare(strict_types=1);

namespace models;

use core\Model;

class UsuarioModel extends Model
{
    // Buscar usuario por email
    public function buscarPorEmail(string $email): array|false
    {
        $sql = "
            SELECT u.*, r.nombre AS rol
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            WHERE u.email = :email AND u.activo = TRUE
        ";
        return $this->fetchOne($sql, [':email' => $email]);
    }

    // Registrar nuevo usuario
    public function crear(string $nombre, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $sql  = "
            INSERT INTO usuarios (nombre, email, password, rol_id)
            VALUES (:nombre, :email, :password, 2)
            RETURNING id
        ";
        $stmt = $this->query($sql, [
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $hash,
        ]);
        return $stmt->fetchColumn();
    }

    // Verificar si el email ya existe
    public function emailExiste(string $email): bool
    {
        $row = $this->fetchOne(
            'SELECT id FROM usuarios WHERE email = :email',
            [':email' => $email]
        );
        return (bool)$row;
    }

    public function obtenerTodos(): array
    {
        return $this->fetchAll(
            'SELECT u.id, u.nombre, u.email, r.nombre AS rol, u.rol_id, u.activo, u.created_at
             FROM usuarios u JOIN roles r ON r.id = u.rol_id ORDER BY u.created_at DESC'
        );
    }

    public function obtenerPorId(int $id): array|false
    {
        return $this->fetchOne(
            'SELECT u.*, r.nombre AS rol
             FROM usuarios u JOIN roles r ON r.id = u.rol_id
             WHERE u.id = :id',
            [':id' => $id]
        );
    }

    // Admin: editar nombre y rol
    public function actualizar(int $id, string $nombre, int $rol_id): void
    {
        $this->query(
            'UPDATE usuarios SET nombre = :nombre, rol_id = :rol_id WHERE id = :id',
            [':nombre' => $nombre, ':rol_id' => $rol_id, ':id' => $id]
        );
    }

    public function toggleActivo(int $id): void
    {
        $this->query(
            'UPDATE usuarios SET activo = NOT activo WHERE id = :id',
            [':id' => $id]
        );
    }

    public function eliminar(int $id): void
    {
        $this->query('DELETE FROM usuarios WHERE id = :id', [':id' => $id]);
    }

    public function resetPassword(int $id, string $passwordPlain): void
    {
        $hash = password_hash($passwordPlain, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->query(
            'UPDATE usuarios SET password = :password WHERE id = :id',
            [':password' => $hash, ':id' => $id]
        );
    }

    // Usuario: actualizar su propio perfil
    public function actualizarPerfil(int $id, string $nombre, ?string $passwordPlain): void
    {
        if ($passwordPlain !== null) {
            $hash = password_hash($passwordPlain, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->query(
                'UPDATE usuarios SET nombre = :nombre, password = :password WHERE id = :id',
                [':nombre' => $nombre, ':password' => $hash, ':id' => $id]
            );
        } else {
            $this->query(
                'UPDATE usuarios SET nombre = :nombre WHERE id = :id',
                [':nombre' => $nombre, ':id' => $id]
            );
        }
    }
}
