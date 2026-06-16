<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'shoppeo') ?></title>
    <link rel="icon" type="image/png" href="/img/logo_copy.png">


    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- CSS propio -->
    <link rel="stylesheet" href="/css/app.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-shoppeo shadow-sm sticky-top">
        <div class="container-xl">
            <a class="navbar-brand" href="/">
                <img src="/img/logo_completo.png" alt="shoppeo" style="height:40px;width:auto;">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"
                style="border-color:#ddd;">
                <span class="navbar-toggler-icon" style="filter:invert(1) sepia(1) saturate(3) hue-rotate(230deg);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <!-- Buscador central -->
                <form class="d-flex mx-auto" style="max-width:560px;width:100%;position:relative;" action="/buscar" method="GET">
                    <div class="input-group" style="border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                        <input class="form-control border-0 bg-light" type="search" name="q"
                            placeholder="Busca un producto..." id="navSearchInput"
                            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                            autocomplete="off"
                            style="font-size:14px;background:#f5f5f5!important;">
                        <button class="btn-nav-search" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <ul class="navbar-nav ms-3 align-items-lg-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-1" href="/mapa"
                            style="color:#444;font-weight:500;font-size:14px;">
                            <i class="bi bi-map"></i> Mapa
                        </a>
                    </li>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin" style="color:#444;font-size:14px;">
                                    <i class="bi bi-gear me-1"></i>Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                style="color:#444;font-weight:500;font-size:14px;">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['user_nombre']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="/descuentos">
                                        <i class="bi bi-percent me-1" style="color:#4f46e5;"></i>Mis Descuentos
                                    </a></li>
                                <li><a class="dropdown-item" href="/puntos">
                                        <i class="bi bi-star me-1" style="color:#7c3aed;"></i>Mis Puntos
                                    </a></li>
                                <li><a class="dropdown-item" href="/configuracion">
                                        <i class="bi bi-gear me-1"></i>Configuración
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="/logout">
                                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn-nav-entrar" href="/login">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn-nav-register" href="/registro">Registrarse</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <?php
                        $__favCount = 0;
                        if (!empty($_SESSION['user_id'])) {
                            $__fav = new \models\FavoritoModel();
                            $__favCount = $__fav->contar((int)$_SESSION['user_id']);
                        }
                        ?>
                        <a class="nav-wishlist ms-1" href="/favoritos" aria-label="Mi lista">
                            <i class="bi <?= $__favCount > 0 ? 'bi-heart-fill' : 'bi-heart' ?>" style="font-size:20px;"></i>
                            <?php if ($__favCount > 0): ?>
                                <span class="wish-count" id="wishCount"><?= $__favCount ?></span>
                            <?php else: ?>
                                <span class="wish-count" id="wishCount" style="display:none;">0</span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>

                <!-- Indicador de usuarios online (WebSocket) -->
                <span class="badge bg-success ms-2 d-none d-lg-inline" id="onlineBadge" title="Usuarios conectados">
                    <i class="bi bi-wifi"></i> <span id="onlineCount">–</span>
                </span>
            </div>
        </div>
    </nav>

    <!-- FLASH MESSAGES════ -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- CONTENIDO PRINCIPAL═ -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <main>
        <?= $content ?>
    </main>

    <!-- FOOTER -->
    <footer class="ft-footer">

        <div class="ft-top">
            <div class="container-xl">
                <div class="ft-grid">

                    <!-- ── Marca ── -->
                    <div class="ft-brand">
                        <a href="/" class="ft-logo">
                            <img src="/img/logo_completo.png" alt="shoppeo"
                                style="height:36px;width:auto;"
                                onerror="this.style.display='none'">
                        </a>
                        <p class="ft-brand-desc">
                            Compara precios en tiendas locales en tiempo real.
                            Encuentra la mejor oferta sin salir de tu ciudad.
                        </p>
                        <div class="ft-socials">
                            <a href="#" class="ft-social" aria-label="GitHub"><i class="bi bi-github"></i></a>
                            <a href="#" class="ft-social" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="ft-social" aria-label="Email"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>

                    <!-- ── Navegar ── -->
                    <div class="ft-col">
                        <h4 class="ft-col-title">Navegar</h4>
                        <ul class="ft-links">
                            <li><a href="/" class="ft-link"><i class="bi bi-house"></i> Inicio</a></li>
                            <li><a href="/buscar" class="ft-link"><i class="bi bi-search"></i> Buscar productos</a></li>
                            <li><a href="/mapa" class="ft-link"><i class="bi bi-map"></i> Mapa de tiendas</a></li>
                            <li><a href="/favoritos" class="ft-link"><i class="bi bi-heart"></i> Mis lista</a></li>
                            <li><a href="/descuentos" class="ft-link"><i class="bi bi-percent"></i> Descuentos</a></li>
                        </ul>
                    </div>

                    <!-- ── Información ── -->
                    <div class="ft-col">
                        <h4 class="ft-col-title">Información</h4>
                        <ul class="ft-links">
                            <li><a href="/ayuda" class="ft-link"><i class="bi bi-info-circle"></i> Sobre shoppeo</a></li>
                            <li><a href="#" class="ft-link"><i class="bi bi-file-text"></i> Términos de uso</a></li>
                            <li><a href="#" class="ft-link"><i class="bi bi-shield-check"></i> Privacidad</a></li>
                            <li><a href="/ayuda#contacto" class="ft-link"><i class="bi bi-envelope-at"></i> Contacto</a></li>
                        </ul>
                    </div>

                    <!-- ── Estado del sistema ── -->
                    <div class="ft-col">
                        <h4 class="ft-col-title">Sistema</h4>
                        <div class="ft-status">
                            <div class="ft-status-item">
                                <span class="ft-status-dot"></span> Servicios activos
                            </div>
                            <div class="ft-status-item">
                                <span class="ft-status-dot"></span> Precios en tiempo real
                            </div>
                            <div class="ft-status-item">
                                <span class="ft-status-dot"></span> Base de datos OK
                            </div>
                        </div>
                        <div class="ft-badges">
                            <span class="ft-badge">PHP 8.2</span>
                            <span class="ft-badge">PostgreSQL</span>
                            <span class="ft-badge">Node.js</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="ft-bottom">
            <div class="container-xl">
                <div class="ft-bottom-inner">
                    <span>&copy; <?= date('Y') ?> shoppeo &mdash; TFG DAW. Todos los derechos reservados.</span>
                    <span class="ft-bottom-right">
                        powered by <i class="bi bi-cup-hot-fill" style="color:green;font-size:12px;"></i>Sufian Hossain Badri
                    </span>
                </div>
            </div>
        </div>

    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Socket.io client desde el servidor Node.js -->
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
    <script src="/js/websocket.js"></script>
    <script src="/js/app.js"></script>
</body>

</html>