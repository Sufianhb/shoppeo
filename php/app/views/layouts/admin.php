<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin') ?> — shoppeo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body class="ad-body">

    <div class="ad-wrapper">

        <!-- Backdrop overlay for mobile sidebar -->
        <div class="ad-overlay" id="adOverlay"></div>

        <!--  SIDEBAR  -->
        <aside class="ad-sidebar">

            <!-- Logo -->
            <a href="/" class="ad-logo">
                <img src="/img/logo_completo.png" alt="shoppeo"
                    style="height:36px;width:auto;max-width:160px;filter:brightness(0) invert(1);"
                    onerror="this.onerror=null;this.style.display='none';">
            </a>

            <!-- Nav items -->
            <nav class="ad-nav">
                <a href="/admin" class="ad-nav-item <?= str_ends_with($_SERVER['REQUEST_URI'], '/admin') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="/admin/productos" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/productos') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i> Productos
                </a>
                <a href="/admin/precios" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/precios') ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i> Precios
                </a>
                <a href="/admin/tiendas" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/tiendas') ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt"></i> Tiendas
                </a>
                <a href="/admin/actividad" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/actividad') ? 'active' : '' ?>">
                    <i class="bi bi-activity"></i> Actividad
                </a>
                <a href="/admin/usuarios" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/usuarios') ? 'active' : '' ?>">
                    <i class="bi bi-person"></i> Usuarios
                </a>
                <a href="/admin/configuracion" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/configuracion') ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i> Configuración
                </a>
                <a href="/admin/logs" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/logs') ? 'active' : '' ?>">
                    <i class="bi bi-journal-text"></i> Logs
                </a>
                <div class="ad-nav-sep"></div>
                <a href="/admin/puntos" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/puntos') ? 'active' : '' ?>">
                    <i class="bi bi-star-fill"></i> Puntos
                </a>
                <a href="/admin/canjes" class="ad-nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/admin/canjes') ? 'active' : '' ?>">
                    <i class="bi bi-bag-heart"></i> Canjes
                </a>
            </nav>

            <!-- User card -->
            <div class="ad-sidebar-user">
                <div class="ad-user-row">
                    <div class="ad-user-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="ad-user-info">
                        <div class="ad-user-name"><?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Administrador') ?></div>
                        <div class="ad-user-email"><?= htmlspecialchars($_SESSION['user_email'] ?? 'admin@shoppeo.es') ?></div>
                    </div>
                </div>
                <a href="/logout" class="ad-logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </a>
            </div>

        </aside>

        <!--  MAIN  -->
        <div class="ad-main">

            <!-- Topbar -->
            <div class="ad-topbar">
                <div class="ad-topbar-left">
                    <button class="ad-hamburger" type="button" aria-label="Menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="ad-topbar-title">Panel de Administración &mdash; shoppeo</span>
                </div>
                <div class="ad-topbar-right">
                    <span class="ad-badge-rt" id="wsStatus">
                        <i class="bi bi-lightning-charge-fill"></i>
                        Tiempo real activo
                        <span class="ad-badge-rt-dot"></span>
                    </span>
                    <a href="/" target="_blank" class="ad-btn-verweb">
                        <i class="bi bi-box-arrow-up-right"></i> Ver web
                    </a>
                </div>
            </div>

            <!-- Flash messages -->
            <?php if (!empty($_SESSION['flash'])): ?>
                <?php $flash = $_SESSION['flash'];
                unset($_SESSION['flash']); ?>
                <div class="ad-flash">
                    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="ad-content-scroll">
                <div class="ad-content">
                    <?= $content ?>
                </div>

                <!-- Footer -->
                <div class="ad-footer">
                    <span>© 2024 Shoppeo. Todos los derechos reservados.</span>
                    <div class="ad-footer-links">
                        <a href="#">Documentación</a>
                        <a href="#"><i class="bi bi-headset"></i> Soporte</a>
                        <a href="#">Versión 1.0.0</a>
                    </div>
                </div>
            </div>

        </div><!-- /ad-main -->

    </div><!-- /ad-wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script async src="http://localhost:3000/socket.io/socket.io.js"></script>
    <script src="/js/websocket.js" defer></script>
    <script src="/js/admin.js"></script>
    <script>
        (function() {
            var sidebar = document.querySelector('.ad-sidebar');
            var hamburger = document.querySelector('.ad-hamburger');
            var overlay = document.getElementById('adOverlay');
            if (!sidebar || !hamburger) return;

            function isMobile() {
                return window.innerWidth < 992;
            }

            // Initialize without transition to avoid flash
            sidebar.style.transition = 'none';
            if (isMobile()) {
                sidebar.classList.add('collapsed');
            } else if (localStorage.getItem('adminSidebar') === 'collapsed') {
                sidebar.classList.add('collapsed');
            }
            requestAnimationFrame(function() {
                sidebar.style.transition = '';
            });

            function closeSidebar() {
                sidebar.classList.add('collapsed');
                if (overlay) overlay.classList.remove('visible');
            }

            hamburger.addEventListener('click', function() {
                var willCollapse = !sidebar.classList.contains('collapsed');
                sidebar.classList.toggle('collapsed');
                if (overlay) overlay.classList.toggle('visible', !sidebar.classList.contains('collapsed'));
                if (!isMobile()) {
                    localStorage.setItem('adminSidebar', willCollapse ? 'collapsed' : 'open');
                }
            });

            if (overlay) overlay.addEventListener('click', closeSidebar);

            // On resize to desktop, remove overlay
            window.addEventListener('resize', function() {
                if (!isMobile() && overlay) overlay.classList.remove('visible');
            });
        }());
    </script>
</body>

</html>