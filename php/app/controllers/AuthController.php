<?php

declare(strict_types=1);

namespace controllers;

use core\Controller;
use models\UsuarioModel;

class AuthController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // GET /login
    public function loginForm(array $params): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->render('auth/login', ['title' => 'Iniciar sesion']);
    }

    // POST /login
    public function login(array $params): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW) ?? '';

        // Validacion basica
        if (empty($email) || empty($password)) {
            $this->flash('error', 'Por favor, completa todos los campos.');
            $this->redirect('/login');
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $this->flash('error', 'Credenciales incorrectas. Inténtalo de nuevo.');
            $this->redirect('/login');
            return;
        }

        // Regenerar ID de sesion para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_nombre'] = $usuario['nombre'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['user_rol'] = $usuario['rol'];

        $destino = ($usuario['rol'] === 'admin') ? '/admin' : '/';
        $this->redirect($destino);
    }

    // GET /logout
    public function logout(array $params): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }

    // GET /registro
    public function registroForm(array $params): void
    {
        $this->render('auth/registro', ['title' => 'Crear cuenta']);
    }

    // POST /registro
    public function registro(array $params): void
    {
        $nombre = $this->input('nombre');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // Validaciones
        if (strlen($nombre) < 2) {
            $this->flash('error', 'El nombre debe tener al menos 2 caracteres.');
            $this->redirect('/registro');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'El email no es valido.');
            $this->redirect('/registro');
            return;
        }

        if (strlen($password) < 8) {
            $this->flash('error', 'La contraseña debe tener al menos 8 caracteres.');
            $this->redirect('/registro');
            return;
        }

        if ($password !== $confirm) {
            $this->flash('error', 'Las contraseñas no coinciden.');
            $this->redirect('/registro');
            return;
        }

        if ($this->usuarioModel->emailExiste($email)) {
            $this->flash('error', 'Este email ya esta registrado.');
            $this->redirect('/registro');
            return;
        }

        $this->usuarioModel->crear($nombre, $email, $password);
        $this->flash('success', '¡Cuenta creada! Ya puedes iniciar sesion.');
        $this->redirect('/login');
    }
}
