<?php

declare(strict_types=1);

namespace core;

/**
 *  Clase base de todos los controladores.
 * Proporciona helpers para renderizar vistas y responder JSON.
 */
abstract class Controller
{
    // Renderizar vista con layout 
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extraer variables para que la vista las use directamente
        extract($data, EXTR_SKIP);

        $viewFile   = APP_PATH . '/views/' . $view . '.php';
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: $view");
        }

        // Capturar contenido de la vista en un buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Renderizar el layout con el contenido inyectado
        require $layoutFile;
    }

    // Respuesta JSON
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // Redireccion
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    // Verificar autenticacion
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    // Verificar rol admin
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_rol'] ?? '') !== 'admin') {
            http_response_code(403);
            die('Acceso denegado');
        }
    }

    // Obtener y sanear input POST 
    protected function input(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) : $value;
    }

    // Mensaje flash (una sola vez en sesion)
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
