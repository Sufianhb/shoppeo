<?php

declare(strict_types=1);

namespace models;

use core\Model;

class ProductoModel extends Model
{
    // Chollos: precio mínimo por producto (rank=1), ordenados por mayor ahorro.
    // Devuelve latitud/longitud de la tienda para que el frontend ordene por proximidad.
    public function obtenerChollos(int $limit = 6): array
    {
        return $this->fetchAll("
            SELECT
                producto_id,
                producto        AS nombre,
                imagen_url,
                categoria,
                tienda,
                latitud,
                longitud,
                precio          AS precio_actual,
                precio_minimo,
                precio_maximo,
                stock,
                actualizado_at
            FROM comparador_precios
            WHERE rank_precio = 1
              AND precio_maximo > precio_minimo
            ORDER BY (precio_maximo - precio_minimo) DESC
            LIMIT :limit
        ", [':limit' => $limit]);
    }

    // Chollos cercanos: solo tiendas dentro de un radio, ordenadas por mayor ahorro.
    public function obtenerCholloscercanos(float $lat, float $lng, float $radiusKm = 25.0, int $limit = 6): array
    {
        $latRange = $radiusKm / 111.32;
        $lngRange = $radiusKm / (111.32 * cos(deg2rad($lat)));

        return $this->fetchAll("
            SELECT
                producto_id,
                producto        AS nombre,
                imagen_url,
                categoria,
                tienda,
                latitud,
                longitud,
                precio          AS precio_actual,
                precio_minimo,
                precio_maximo,
                stock,
                actualizado_at
            FROM comparador_precios
            WHERE rank_precio = 1
              AND precio_maximo > precio_minimo
              AND latitud  IS NOT NULL
              AND longitud IS NOT NULL
              AND latitud  BETWEEN :bbox_lat_min AND :bbox_lat_max
              AND longitud BETWEEN :bbox_lng_min AND :bbox_lng_max
              AND (6371 * acos(LEAST(1.0,
                    cos(radians(:h_lat)) * cos(radians(latitud))
                    * cos(radians(longitud) - radians(:h_lng))
                    + sin(radians(:h_lat)) * sin(radians(latitud))
                  ))) <= :radius
            ORDER BY (precio_maximo - precio_minimo) DESC
            LIMIT :limit
        ", [
            ':bbox_lat_min' => $lat - $latRange,
            ':bbox_lat_max' => $lat + $latRange,
            ':bbox_lng_min' => $lng - $lngRange,
            ':bbox_lng_max' => $lng + $lngRange,
            ':h_lat'        => $lat,
            ':h_lng'        => $lng,
            ':radius'       => $radiusKm,
            ':limit'        => $limit,
        ]);
    }

    // Filtra una lista de tags de Redis devolviendo solo los que tienen productos en la BD.
    public function filtrarTagsExistentes(array $tags, int $limit = 4): array
    {
        $result = [];
        foreach ($tags as $tag) {
            if (count($result) >= $limit) break;
            $found = $this->fetchOne(
                "SELECT 1 FROM productos WHERE activo = TRUE AND nombre ILIKE :q LIMIT 1",
                [':q' => '%' . $tag . '%']
            );
            if ($found) $result[] = $tag;
        }
        return $result;
    }

    // Sugerencias para autocomplete: fuzzy + ILIKE, máx 6
    public function sugerencias(string $termino): array
    {
        // Intentar con pg_trgm (fuzzy); si no está disponible, ILIKE puro
        try {
            $sql = "
                SELECT
                    p.id,
                    p.nombre,
                    c.nombre AS categoria,
                    MIN(pr.precio) AS precio_minimo
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN precios pr ON pr.producto_id = p.id
                WHERE p.activo = TRUE
                  AND (
                      p.nombre ILIKE :like
                   OR similarity(p.nombre, :q) > 0.15
                  )
                GROUP BY p.id, p.nombre, c.nombre
                ORDER BY
                    CASE WHEN p.nombre ILIKE :like THEN 0 ELSE 1 END,
                    similarity(p.nombre, :q) DESC,
                    p.nombre
                LIMIT 6
            ";
            return $this->fetchAll($sql, [
                ':like' => "%{$termino}%",
                ':q'    => $termino,
            ]);
        } catch (\PDOException $e) {
            // pg_trgm no disponible — fallback a ILIKE simple
            $sql = "
                SELECT
                    p.id,
                    p.nombre,
                    c.nombre AS categoria,
                    MIN(pr.precio) AS precio_minimo
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN precios pr ON pr.producto_id = p.id
                WHERE p.activo = TRUE
                  AND p.nombre ILIKE :like
                GROUP BY p.id, p.nombre, c.nombre
                ORDER BY p.nombre
                LIMIT 6
            ";
            return $this->fetchAll($sql, [':like' => "%{$termino}%"]);
        }
    }

    // Buscar productos por texto, categoría, precio y/o proximidad geográfica
    public function buscar(
        string $termino,
        ?string $categoria  = null,
        ?float  $lat        = null,
        ?float  $lng        = null,
        ?float  $radius_km  = null,
        ?float  $precio_min = null,
        ?float  $precio_max = null
    ): array {
        $conditions = ['p.activo = TRUE'];
        $params     = [];

        if ($termino !== '') {
            $conditions[] = '(p.nombre ILIKE :termino OR p.descripcion ILIKE :termino)';
            $params[':termino'] = "%{$termino}%";
        }

        if ($categoria !== null && $categoria !== '') {
            $conditions[] = 'c.nombre = :categoria';
            $params[':categoria'] = $categoria;
        }

        // Filtro de proximidad: bounding box (índice) + Haversine (precisión)
        if ($lat !== null && $lng !== null && $radius_km !== null && $radius_km > 0) {
            // Bounding box aproximado: 1 grado lat ≈ 111.32 km
            $latRange = $radius_km / 111.32;
            $lngRange = $radius_km / (111.32 * cos(deg2rad($lat)));

            $conditions[] = "EXISTS (
                SELECT 1 FROM tiendas t_prox
                JOIN precios pr_prox
                    ON pr_prox.tienda_id    = t_prox.id
                   AND pr_prox.producto_id  = p.id
                WHERE t_prox.activa = TRUE
                  AND t_prox.latitud  BETWEEN :bbox_lat_min AND :bbox_lat_max
                  AND t_prox.longitud BETWEEN :bbox_lng_min AND :bbox_lng_max
                  AND (6371 * acos(LEAST(1.0,
                          cos(radians(:h_lat)) * cos(radians(t_prox.latitud))
                          * cos(radians(t_prox.longitud) - radians(:h_lng))
                          + sin(radians(:h_lat)) * sin(radians(t_prox.latitud))
                       ))) <= :h_radius
            )";

            $params[':bbox_lat_min'] = $lat - $latRange;
            $params[':bbox_lat_max'] = $lat + $latRange;
            $params[':bbox_lng_min'] = $lng - $lngRange;
            $params[':bbox_lng_max'] = $lng + $lngRange;
            $params[':h_lat']        = $lat;
            $params[':h_lng']        = $lng;
            $params[':h_radius']     = $radius_km;
        }

        $where = implode(' AND ', $conditions);

        // Filtro de precio sobre el precio mínimo disponible por producto
        $having = [];
        if ($precio_min !== null) {
            $having[] = 'MIN(pr.precio) >= :precio_min';
            $params[':precio_min'] = $precio_min;
        }
        if ($precio_max !== null) {
            $having[] = 'MIN(pr.precio) <= :precio_max';
            $params[':precio_max'] = $precio_max;
        }
        $havingClause = $having ? 'HAVING ' . implode(' AND ', $having) : '';

        // Ordenación y distancia: activa cuando hay lat/lng (con o sin radio)
        // Usa params :d_lat/:d_lng — distintos de :h_lat/:h_lng del WHERE EXISTS (PDO
        // no permite reutilizar el mismo named param dentro de la misma query).
        $selectDistancia = '';
        $orderBy         = 'p.nombre';

        if ($lat !== null && $lng !== null) {
            $selectDistancia = ",
                (
                    SELECT MIN(6371 * acos(LEAST(1.0,
                        cos(radians(:d_lat)) * cos(radians(t_d.latitud))
                        * cos(radians(t_d.longitud) - radians(:d_lng))
                        + sin(radians(:d_lat)) * sin(radians(t_d.latitud))
                    )))
                    FROM tiendas t_d
                    JOIN precios pr_d
                        ON pr_d.tienda_id   = t_d.id
                       AND pr_d.producto_id = p.id
                    WHERE t_d.activa = TRUE
                ) AS distancia_km_minima";

            $params[':d_lat'] = $lat;
            $params[':d_lng'] = $lng;
            $orderBy = 'distancia_km_minima ASC NULLS LAST, p.nombre';
        }

        $sql = "
            SELECT DISTINCT
                p.id,
                p.nombre,
                p.descripcion,
                p.imagen_url,
                c.nombre AS categoria,
                MIN(pr.precio) AS precio_minimo,
                MAX(pr.precio) AS precio_maximo,
                COUNT(pr.id) AS num_tiendas
                {$selectDistancia}
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN precios pr ON pr.producto_id = p.id
            WHERE {$where}
            GROUP BY p.id, p.nombre, p.descripcion, p.imagen_url, c.nombre
            {$havingClause}
            ORDER BY {$orderBy}
        ";
        return $this->fetchAll($sql, $params);
    }

    // Obtener todos los productos
    public function obtenerTodos(): array
    {
        $sql = "
            SELECT p.*, c.nombre AS categoria
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.activo = TRUE
            ORDER BY p.nombre
        ";
        return $this->fetchAll($sql);
    }

    // Obtener un producto por ID con sus precios por tienda
    public function obtenerConPrecios(int $id): array|false
    {
        $sql = "
            SELECT
                p.id, p.nombre, p.descripcion, p.imagen_url,
                c.nombre AS categoria,
                json_agg(
                    json_build_object(
                        'precio_id', cp.precio_id,
                        'tienda_id', cp.tienda_id,
                        'tienda', cp.tienda,
                        'direccion', cp.direccion,
                        'telefono', cp.telefono,
                        'latitud', cp.latitud,
                        'longitud', cp.longitud,
                        'web', cp.web,
                        'precio', cp.precio,
                        'stock', cp.stock,
                        'es_minimo', (cp.rank_precio = 1),
                        'actualizado_at', cp.actualizado_at
                    ) ORDER BY cp.precio ASC
                ) AS precios
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN comparador_precios cp ON cp.producto_id = p.id
            WHERE p.id = :id AND p.activo = TRUE
            GROUP BY p.id, p.nombre, p.descripcion, p.imagen_url, c.nombre
        ";
        $result = $this->fetchOne($sql, [':id' => $id]);
        if ($result && isset($result['precios'])) {
            $result['precios'] = json_decode($result['precios'], true);
        }
        return $result;
    }

    // Obtener producto por ID 
    public function obtenerPorId(int $id): array|false
    {
        return $this->fetchOne(
            'SELECT * FROM productos WHERE id = :id',
            [':id' => $id]
        );
    }

    // Crear producto
    public function crear(array $datos): int
    {
        $sql = "
            INSERT INTO productos (nombre, descripcion, categoria_id, imagen_url)
            VALUES (:nombre, :descripcion, :categoria_id, :imagen_url)
            RETURNING id
        ";
        $stmt = $this->query($sql, [
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':categoria_id' => $datos['categoria_id'] ?: null,
            ':imagen_url' => $datos['imagen_url'] ?: null,
        ]);
        return $stmt->fetchColumn();
    }

    // Actualizar producto
    public function actualizar(int $id, array $datos): void
    {
        $sql = "
            UPDATE productos
            SET nombre = :nombre, descripcion = :descripcion,
                categoria_id = :categoria_id, imagen_url = :imagen_url
            WHERE id = :id
        ";
        $this->query($sql, [
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':categoria_id' => $datos['categoria_id'] ?: null,
            ':imagen_url' => $datos['imagen_url'] ?: null,
            ':id' => $id,
        ]);
    }

    // Eliminar 
    public function eliminar(int $id): void
    {
        $this->query('UPDATE productos SET activo = FALSE WHERE id = :id', [':id' => $id]);
    }
}
