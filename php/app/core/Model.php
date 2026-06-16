<?php

declare(strict_types=1);

namespace core;

use PDO;

/**
 * Clase base de todos los modelos.
 * Encapsula el acceso a PDO y provee helpers de consulta.
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Ejecutar consulta preparada
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Obtener todos los resultados
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    // Obtener un único resultado
    protected function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    // Insertar y devolver el ID generado
    protected function insert(string $sql, array $params = []): string
    {
        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
