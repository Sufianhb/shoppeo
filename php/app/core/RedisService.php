<?php

declare(strict_types=1);

namespace core;

/**
 * Si Redis no está disponible todos los métodos retornan sin error 
 */
class RedisService
{
    private const KEY      = 'shoppeo:tags_populares';
    private const MAX_TAGS = 100; // cota del sorted set en memoria

    // Fallback con términos que SÍ existen en el catálogo del simulador
    private const FALLBACK = [
        'Smart TV',
        'Auriculares',
        'Zapatillas',
        'Cafetera',
        'Tablet',
        'Aspiradora',
        'Freidora',
        'Smartphone',
    ];

    private static ?\Redis $conn      = null;
    private static bool    $available = true;

    private static function connect(): ?\Redis
    {
        if (!self::$available) return null;
        if (self::$conn !== null) return self::$conn;

        if (!class_exists(\Redis::class)) {
            self::$available = false;
            return null;
        }

        try {
            $r    = new \Redis();
            $host = defined('REDIS_HOST') ? REDIS_HOST : 'redis';
            $port = defined('REDIS_PORT') ? (int)REDIS_PORT : 6379;
            $r->connect($host, $port, 1.0); // timeout 1 s
            self::$conn = $r;
            return $r;
        } catch (\Throwable) {
            self::$available = false;
            return null;
        }
    }

    /**
     * Incrementa el contador del término buscado.
     */
    public static function incrementTag(string $termino): void
    {
        $termino = mb_strtolower(trim($termino));
        if (mb_strlen($termino) < 2) return;

        $redis = self::connect();
        if ($redis === null) return;

        try {
            $redis->zIncrBy(self::KEY, 1, $termino);
            // Eliminar las entradas con menor score si se supera el límite
            $redis->zRemRangeByRank(self::KEY, 0, - (self::MAX_TAGS + 1));
        } catch (\Throwable) {
            self::$available = false;
        }
    }

    /**
     * Devuelve los N tags con más búsquedas.
     * Fallback a lista estática si Redis no está disponible o el set está vacío.
     *
     * @return string[]
     */
    public static function getTopTags(int $n = 5): array
    {
        $redis = self::connect();

        if ($redis !== null) {
            try {
                $tags = $redis->zRevRange(self::KEY, 0, $n - 1);
                if (!empty($tags)) {
                    return array_values($tags);
                }
            } catch (\Throwable) {
                self::$available = false;
            }
        }

        return array_slice(self::FALLBACK, 0, $n);
    }
}
