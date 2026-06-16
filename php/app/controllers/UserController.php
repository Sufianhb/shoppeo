<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\UsuarioModel;

class UserController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // GET /configuracion
    public function configuracion(array $params): void
    {
        $this->requireAuth();
        $usuario = $this->usuarioModel->obtenerPorId((int)$_SESSION['user_id']);

        $this->render('user/configuracion', [
            'title'   => 'Mi configuración — shoppeo',
            'usuario' => $usuario,
        ]);
    }

    // POST /configuracion
    public function guardarConfiguracion(array $params): void
    {
        $this->requireAuth();
        $userId   = (int)$_SESSION['user_id'];
        $nombre   = $this->input('nombre');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['password_confirm'] ?? '');

        if (empty($nombre)) {
            $this->flash('error', 'El nombre no puede estar vacío.');
            $this->redirect('/configuracion');
            return;
        }

        if ($password !== '') {
            if (strlen($password) < 6) {
                $this->flash('error', 'La contraseña debe tener al menos 6 caracteres.');
                $this->redirect('/configuracion');
                return;
            }
            if ($password !== $confirm) {
                $this->flash('error', 'Las contraseñas no coinciden.');
                $this->redirect('/configuracion');
                return;
            }
        }

        $this->usuarioModel->actualizarPerfil(
            $userId,
            $nombre,
            $password !== '' ? $password : null
        );

        $_SESSION['user_nombre'] = $nombre;
        $this->flash('success', 'Perfil actualizado correctamente.');
        $this->redirect('/configuracion');
    }
}
