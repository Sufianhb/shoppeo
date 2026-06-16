<?php
declare(strict_types=1);
namespace controllers;

use core\Controller;
use models\PuntosModel;
use models\RecompensaModel;
use models\CanjeModel;

class PuntosController extends Controller
{
    private PuntosModel $puntosModel;
    private RecompensaModel $recompensaModel;
    private CanjeModel $canjeModel;

    public function __construct()
    {
        $this->puntosModel     = new PuntosModel();
        $this->recompensaModel = new RecompensaModel();
        $this->canjeModel      = new CanjeModel();
    }

    // GET /puntos
    public function index(array $params): void
    {
        $this->requireAuth();
        $userId = (int)$_SESSION['user_id'];

        $totalPuntos   = $this->puntosModel->totalPuntos($userId);
        $transacciones = $this->puntosModel->transacciones($userId, 15);
        $recompensas   = $this->recompensaModel->obtenerActivas();
        $canjes        = $this->canjeModel->obtenerPorUsuario($userId);

        // Primera recompensa que aún no puede alcanzar 
        $proximaRecompensa = null;
        foreach ($recompensas as $r) {
            if ((int)$r['puntos_necesarios'] > $totalPuntos) {
                $proximaRecompensa = $r;
                break;
            }
        }

        $this->render('public/puntos', [
            'title'             => 'Mis Puntos — shoppeo',
            'totalPuntos'       => $totalPuntos,
            'transacciones'     => $transacciones,
            'recompensas'       => $recompensas,
            'canjes'            => $canjes,
            'proximaRecompensa' => $proximaRecompensa,
        ]);
    }

    // POST /puntos/canjear
    public function canjear(array $params): void
    {
        $this->requireAuth();
        $userId       = (int)$_SESSION['user_id'];
        $recompensaId = (int)($_POST['recompensa_id'] ?? 0);
        $direccion    = trim($_POST['direccion_envio'] ?? '');

        if (!$recompensaId || !$direccion) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Debes indicar una dirección de envío.'];
            header('Location: /puntos');
            exit;
        }

        $recompensa = $this->recompensaModel->obtenerPorId($recompensaId);
        if (!$recompensa) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Recompensa no disponible.'];
            header('Location: /puntos');
            exit;
        }

        $totalPuntos = $this->puntosModel->totalPuntos($userId);
        if ($totalPuntos < (int)$recompensa['puntos_necesarios']) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'No tienes suficientes puntos para este canje.'];
            header('Location: /puntos');
            exit;
        }

        $this->canjeModel->crear($userId, $recompensaId, (int)$recompensa['puntos_necesarios'], $direccion);
        $this->puntosModel->descontar($userId, (int)$recompensa['puntos_necesarios'],
            'Canje: ' . $recompensa['nombre']);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => '¡Canje solicitado! Recibirás ' . $recompensa['emoji'] . ' <strong>'
                       . htmlspecialchars($recompensa['nombre'])
                       . '</strong> en la dirección indicada. El equipo shoppeo se pondrá en contacto contigo.',
        ];
        header('Location: /puntos');
        exit;
    }
}
