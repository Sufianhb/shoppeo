<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\ProductoModel;
use models\PrecioModel;
use models\TiendaModel;
use models\CategoriaModel;
use models\UsuarioModel;
use models\ActividadModel;
use models\ConfiguracionModel;
use models\LogModel;
use models\PuntosModel;
use models\RecompensaModel;
use models\CanjeModel;

class AdminController extends Controller
{
    private ProductoModel $productoModel;
    private PrecioModel $precioModel;
    private TiendaModel $tiendaModel;
    private CategoriaModel $categoriaModel;
    private UsuarioModel $usuarioModel;
    private ActividadModel $actividadModel;
    private ConfiguracionModel $configuracionModel;
    private LogModel $logModel;
    private PuntosModel $puntosModel;
    private RecompensaModel $recompensaModel;
    private CanjeModel $canjeModel;

    public function __construct()
    {
        $this->productoModel      = new ProductoModel();
        $this->precioModel        = new PrecioModel();
        $this->tiendaModel        = new TiendaModel();
        $this->categoriaModel     = new CategoriaModel();
        $this->usuarioModel       = new UsuarioModel();
        $this->actividadModel     = new ActividadModel();
        $this->configuracionModel = new ConfiguracionModel();
        $this->logModel           = new LogModel();
        $this->puntosModel        = new PuntosModel();
        $this->recompensaModel    = new RecompensaModel();
        $this->canjeModel         = new CanjeModel();
    }

    //  GET /admin 
    public function dashboard(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/dashboard', [
            'title' => 'Panel de Administración — shoppeo',
            'productos' => $this->productoModel->obtenerTodos(),
            'tiendas' => $this->tiendaModel->obtenerTodas(),
            'usuarios' => $this->usuarioModel->obtenerTodos(),
            'precios' => $this->precioModel->obtenerTodos(),
        ], 'admin');
    }

    //  GET /admin/productos 
    public function productos(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/productos', [
            'title' => 'Gestión de Productos',
            'productos' => $this->productoModel->obtenerTodos(),
        ], 'admin');
    }

    //  GET /admin/productos/crear 
    public function crearProductoForm(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/producto_form', [
            'title' => 'Crear Producto',
            'producto' => null,
            'categorias' => $this->categoriaModel->obtenerTodas(),
        ], 'admin');
    }

    //  POST /admin/productos/crear 
    public function crearProducto(array $params): void
    {
        $this->requireAdmin();

        $datos = [
            'nombre' => $this->input('nombre'),
            'descripcion' => $this->input('descripcion'),
            'categoria_id' => (int)$this->input('categoria_id', 0),
            'imagen_url' => $this->input('imagen_url'),
        ];

        if (empty($datos['nombre'])) {
            $this->flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect('/admin/productos/crear');
            return;
        }

        $productoId = $this->productoModel->crear($datos);
        $this->logModel->insertar('info', "Producto creado: \"{$datos['nombre']}\" (ID: {$productoId})");
        $this->flash('success', 'Producto creado correctamente.');
        $this->redirect('/admin/precios?producto_id=' . $productoId . '&nuevo=1');
    }

    //  GET /admin/productos/{id}/editar 
    public function editarProductoForm(array $params): void
    {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto) {
            $this->flash('error', 'Producto no encontrado.');
            $this->redirect('/admin/productos');
            return;
        }

        $this->render('admin/producto_form', [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $this->categoriaModel->obtenerTodas(),
        ], 'admin');
    }

    //  POST /admin/productos/{id}/editar 
    public function editarProducto(array $params): void
    {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);

        $datos = [
            'nombre' => $this->input('nombre'),
            'descripcion' => $this->input('descripcion'),
            'categoria_id' => (int)$this->input('categoria_id', 0),
            'imagen_url' => $this->input('imagen_url'),
        ];

        if (empty($datos['nombre'])) {
            $this->flash('error', 'El nombre del producto es obligatorio.');
            $this->redirect("/admin/productos/{$id}/editar");
            return;
        }

        $this->productoModel->actualizar($id, $datos);
        $this->flash('success', 'Producto actualizado correctamente.');
        $this->redirect('/admin/productos');
    }

    //  POST /admin/productos/{id}/eliminar 
    public function eliminarProducto(array $params): void
    {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $this->productoModel->eliminar($id);
        $this->flash('success', 'Producto eliminado correctamente.');
        $this->redirect('/admin/productos');
    }

    //  GET /admin/precios 
    public function precios(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/precios', [
            'title' => 'Gestión de Precios',
            'precios' => $this->precioModel->obtenerTodos(),
            'productos' => $this->productoModel->obtenerTodos(),
            'tiendas' => $this->tiendaModel->obtenerTodas(),
        ], 'admin');
    }

    //  POST /admin/precios/actualizar 
    public function actualizarPrecio(array $params): void
    {
        $this->requireAdmin();

        $productoId = (int)$this->input('producto_id');
        $tiendaId = (int)$this->input('tienda_id');
        $precio = (float)str_replace(',', '.', $this->input('precio'));
        $stock = (int)$this->input('stock', 0);

        if ($productoId <= 0 || $tiendaId <= 0 || $precio < 0) {
            $this->flash('error', 'Datos inválidos para actualizar el precio.');
            $this->redirect('/admin/precios');
            return;
        }

        // Precio anterior para la notificacion WebSocket
        $precioAnterior = $this->precioModel->obtenerPrecioActual($productoId, $tiendaId);

        // Actualizar en BBDD
        $this->precioModel->upsert($productoId, $tiendaId, $precio, $stock);
        $this->logModel->insertar('info', "Precio actualizado: producto #{$productoId} en tienda #{$tiendaId} → {$precio} € (stock: {$stock})");

        // Notificar al servidor WebSocket (Node.js)
        $producto = $this->productoModel->obtenerPorId($productoId);
        $tienda   = $this->tiendaModel->obtenerPorId($tiendaId);

        if ($producto && $tienda) {
            $this->notificarWebSocket([
                'producto_id' => $productoId,
                'producto' => $producto['nombre'],
                'tienda_id' => $tiendaId,
                'tienda' => $tienda['nombre'],
                'precio' => $precio,
                'precio_anterior' => $precioAnterior !== false ? $precioAnterior : $precio,
                'stock' => $stock,
            ]);
        }

        $this->flash('success', 'Precio actualizado correctamente.');
        $this->redirect('/admin/precios');
    }

    //  GET /admin/tiendas 
    public function tiendas(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/tiendas', [
            'title' => 'Gestión de Tiendas',
            'tiendas' => $this->tiendaModel->obtenerTodas(),
        ], 'admin');
    }

    //  POST /admin/tiendas/crear 
    public function crearTienda(array $params): void
    {
        $this->requireAdmin();

        $datos = [
            'nombre' => $this->input('nombre'),
            'direccion' => $this->input('direccion'),
            'telefono' => $this->input('telefono'),
            'latitud' => (float)str_replace(',', '.', $this->input('latitud')),
            'longitud' => (float)str_replace(',', '.', $this->input('longitud')),
            'web' => $this->input('web'),
        ];

        if (empty($datos['nombre']) || empty($datos['latitud']) || empty($datos['longitud'])) {
            $this->flash('error', 'Nombre, latitud y longitud son obligatorios.');
            $this->redirect('/admin/tiendas');
            return;
        }

        $this->tiendaModel->crear($datos);
        $this->flash('success', 'Tienda creada correctamente.');
        $this->redirect('/admin/tiendas');
    }

    //  GET /admin/usuarios
    public function usuarios(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/usuarios', [
            'title'    => 'Usuarios',
            'usuarios' => $this->usuarioModel->obtenerTodos(),
        ], 'admin');
    }

    //  GET /admin/usuarios/{id}/editar
    public function editarUsuarioForm(array $params): void
    {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $this->render('admin/usuario_edit', [
            'title'   => 'Editar usuario',
            'usuario' => $usuario,
        ], 'admin');
    }

    //  POST /admin/usuarios/{id}/editar
    public function editarUsuario(array $params): void
    {
        $this->requireAdmin();
        $id    = (int)($params['id'] ?? 0);
        $nombre = $this->input('nombre');
        $rolId  = (int)$this->input('rol_id', 2);

        if (empty($nombre)) {
            $this->flash('error', 'El nombre es obligatorio.');
            $this->redirect("/admin/usuarios/{$id}/editar");
            return;
        }

        // No permitir quitarle el rol admin al propio usuario logueado
        if ($id === (int)$_SESSION['user_id'] && $rolId !== 1) {
            $this->flash('error', 'No puedes cambiar tu propio rol.');
            $this->redirect("/admin/usuarios/{$id}/editar");
            return;
        }

        $this->usuarioModel->actualizar($id, $nombre, $rolId);
        $this->logModel->insertar('info', "Usuario #{$id} actualizado por admin");
        $this->flash('success', 'Usuario actualizado correctamente.');
        $this->redirect('/admin/usuarios');
    }

    //  POST /admin/usuarios/{id}/toggle
    public function toggleUsuario(array $params): void
    {
        $this->requireAdmin();
        $id = (int)($params['id'] ?? 0);

        if ($id === (int)$_SESSION['user_id']) {
            $this->flash('error', 'No puedes bloquearte a ti mismo.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $this->usuarioModel->toggleActivo($id);
        $this->logModel->insertar('info', "Estado del usuario #{$id} cambiado por admin");
        $this->flash('success', 'Estado del usuario actualizado.');
        $this->redirect('/admin/usuarios');
    }

    //  POST /admin/usuarios/{id}/eliminar
    public function eliminarUsuario(array $params): void
    {
        $this->requireAdmin();
        $id      = (int)($params['id'] ?? 0);
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/usuarios');
            return;
        }

        if ($usuario['rol'] === 'admin') {
            $this->flash('error', 'No se puede eliminar a un administrador.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $this->usuarioModel->eliminar($id);
        $this->logModel->insertar('info', "Usuario #{$id} ({$usuario['email']}) eliminado por admin");
        $this->flash('success', 'Usuario eliminado correctamente.');
        $this->redirect('/admin/usuarios');
    }

    //  POST /admin/usuarios/{id}/reset-password
    public function resetPasswordUsuario(array $params): void
    {
        $this->requireAdmin();
        $id      = (int)($params['id'] ?? 0);
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            $this->flash('error', 'Usuario no encontrado.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $passwordTemporal = bin2hex(random_bytes(4));
        $this->usuarioModel->resetPassword($id, $passwordTemporal);

        $asunto  = 'Restablecimiento de contraseña — shoppeo';
        $cuerpo  = "Hola {$usuario['nombre']},\n\n"
            . "Tu nueva contraseña temporal es: {$passwordTemporal}\n\n"
            . "Por seguridad, cámbiala en Configuración tras iniciar sesión.\n\n"
            . "Equipo shoppeo";
        @mail($usuario['email'], $asunto, $cuerpo);

        $this->logModel->insertar('info', "Contraseña reseteada para usuario #{$id}");
        $this->flash('success', "Contraseña reseteada: {$passwordTemporal}  (enviada por email a {$usuario['email']})");
        $this->redirect('/admin/usuarios');
    }

    //  GET /admin/actividad
    public function actividad(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/actividad', [
            'title'     => 'Actividad',
            'actividad' => $this->actividadModel->obtenerRecientes(100),
            'stats'     => $this->actividadModel->estadisticas(),
        ], 'admin');
    }

    //  GET /admin/configuracion
    public function configuracion(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/configuracion', [
            'title'  => 'Configuración',
            'config' => $this->configuracionModel->obtenerTodas(),
        ], 'admin');
    }

    //  POST /admin/configuracion
    public function guardarConfiguracion(array $params): void
    {
        $this->requireAdmin();
        $campos = $_POST['config'] ?? [];
        foreach ($campos as $clave => $valor) {
            $this->configuracionModel->actualizar($clave, trim((string)$valor));
        }
        $this->logModel->insertar('info', 'Configuración del sistema actualizada');
        $this->flash('success', 'Configuración guardada correctamente.');
        $this->redirect('/admin/configuracion');
    }

    //  GET /admin/logs
    public function logs(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/logs', [
            'title'  => 'Logs del sistema',
            'logs'   => $this->logModel->obtenerRecientes(200),
            'stats'  => $this->logModel->estadisticas(),
        ], 'admin');
    }

    //  Helper: llamar al servidor WebSocket via HTTP interno
    private function notificarWebSocket(array $payload): void
    {
        $url  = WS_URL . '/internal/notify';
        $json = json_encode($payload);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nX-Ws-Secret: " . WS_SECRET . "\r\n",
                'content' => $json,
                'timeout' => 2,
                'ignore_errors' => true,
            ],
        ];

        // Fire-and-forget (no bloqueamos si el WS está caído)
        @file_get_contents($url, false, stream_context_create($opts));
    }

    // GET /admin/puntos
    public function puntosAdmin(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/puntos', [
            'title'         => 'Gestión de Puntos — Admin',
            'transacciones' => $this->puntosModel->todasTransacciones(100),
            'stats'         => $this->puntosModel->statsDia(),
            'ranking'       => $this->puntosModel->rankingUsuarios(),
            'usuarios'      => $this->usuarioModel->obtenerTodos(),
            'recompensas'   => $this->recompensaModel->obtenerTodas(),
        ], 'admin');
    }

    // POST /admin/puntos/otorgar
    public function otorgarPuntos(array $params): void
    {
        $this->requireAdmin();
        $usuarioId = (int)($_POST['usuario_id'] ?? 0);
        $puntos    = (int)($_POST['puntos'] ?? 0);
        $concepto  = trim($_POST['concepto'] ?? '');

        if (!$usuarioId || $puntos <= 0 || !$concepto) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Datos incompletos.'];
            header('Location: /admin/puntos');
            exit;
        }

        $this->puntosModel->otorgar($usuarioId, $puntos, $concepto, (int)$_SESSION['user_id']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => "{$puntos} puntos otorgados correctamente."];
        header('Location: /admin/puntos');
        exit;
    }

    // GET /admin/canjes
    public function canjesAdmin(array $params): void
    {
        $this->requireAdmin();
        $this->render('admin/canjes', [
            'title'  => 'Canjes — Admin',
            'canjes' => $this->canjeModel->obtenerTodos(),
            'stats'  => $this->canjeModel->statsPorEstado(),
        ], 'admin');
    }

    // POST /admin/canjes/{id}/estado
    public function actualizarCanje(array $params): void
    {
        $this->requireAdmin();
        $id      = (int)($params['id'] ?? 0);
        $estado  = $_POST['estado'] ?? '';
        $notas   = trim($_POST['notas_admin'] ?? '');
        $adminId = (int)$_SESSION['user_id'];

        if (!in_array($estado, ['pendiente', 'enviado', 'completado', 'cancelado'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Estado inválido.'];
            header('Location: /admin/canjes');
            exit;
        }

        // Si se cancela devolvemos los puntos
        if ($estado === 'cancelado') {
            $canje = $this->canjeModel->obtenerPorId($id);
            if ($canje && $canje['estado'] !== 'cancelado') {
                $this->puntosModel->otorgar(
                    (int)$canje['usuario_id'],
                    (int)$canje['puntos_usados'],
                    'Devolución por cancelación de canje #' . $id,
                    $adminId
                );
            }
        }

        $this->canjeModel->actualizarEstado($id, $estado, $notas, $adminId);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Canje actualizado correctamente.'];
        header('Location: /admin/canjes');
        exit;
    }
}
