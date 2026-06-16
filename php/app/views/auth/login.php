<!-- Vista: auth/login.php — Pixel-perfect SaaS login -->
<style>
    /* Oculta navbar y footer solo en esta página */
    body>nav.navbar,
    body>footer {
        display: none !important;
    }

    body {
        background: #eeecf7;
    }

    main {
        padding: 0;
    }
</style>

<div class="lp-wrap">
    <div class="lp-card">

        <!--  PANEL IZQUIERDO  -->
        <div class="lp-left">
            <!-- Decoración de círculos de fondo -->
            <div class="lp-circle lp-circle-1"></div>
            <div class="lp-circle lp-circle-2"></div>

            <!-- Logo -->
            <div class="lp-logo">
                <img src="/assets/logo_copy.png" alt="shoppeo"
                    onerror="this.onerror=null;this.src='/img/logo_completo.png';"
                    style="height:36px;filter:brightness(0) invert(1);">
            </div>

            <!-- Titular -->
            <div class="lp-headline">
                <h1 class="lp-title-line1">Tu tienda online,</h1>
                <h1 class="lp-title-line2">más fácil que nunca</h1>
                <p class="lp-subtitle">
                    Gestiona tus productos, pedidos y clientes<br>desde un solo lugar.
                </p>
            </div>

            <!-- Imagen decorativa -->
            <div class="lp-img-wrap">
                <img src="/assets/login-cart.png" alt=""
                    class="lp-cart-img"
                    onerror="this.style.display='none'">
            </div>

            <!-- Tarjeta de seguridad -->
            <div class="lp-security">
                <div class="lp-security-icon">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <div class="lp-security-title">Seguro y confiable</div>
                    <div class="lp-security-sub">
                        Tus datos están protegidos con<br>cifrado de nivel empresarial.
                    </div>
                </div>
            </div>
        </div>

        <!--  PANEL DERECHO  -->
        <div class="lp-right">
            <div class="lp-form-wrap">

                <!-- Icono superior -->
                <div class="lp-top-icon">
                    <i class="bi bi-shop-window"></i>
                </div>

                <!-- Títulos -->
                <h2 class="lp-form-title">¡Bienvenido de vuelta!</h2>
                <p class="lp-form-sub">Inicia sesión para continuar</p>

                <!-- Flash messages -->
                <?php if (!empty($_SESSION['flash'])): ?>
                    <?php $flash = $_SESSION['flash'];
                    unset($_SESSION['flash']); ?>
                    <div class="lp-alert lp-alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
                        <i class="bi bi-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?> me-2"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form method="POST" action="/login" novalidate>

                    <!-- Email -->
                    <div class="lp-field">
                        <label class="lp-label" for="email">Email</label>
                        <div class="lp-input-wrap">
                            <i class="bi bi-envelope lp-input-icon"></i>
                            <input type="email" id="email" name="email"
                                class="lp-input"
                                placeholder="tu@email.com"
                                required autocomplete="email">
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="lp-field">
                        <label class="lp-label" for="password">Contraseña</label>
                        <div class="lp-input-wrap">
                            <i class="bi bi-lock lp-input-icon"></i>
                            <input type="password" id="password" name="password"
                                class="lp-input lp-input-pwd"
                                placeholder="Tu contraseña"
                                required autocomplete="current-password">
                            <button type="button" class="lp-eye-btn" id="eyeBtn"
                                aria-label="Mostrar contraseña">
                                <i class="bi bi-eye-slash" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Recordarme + Olvidaste -->
                    <div class="lp-row-check">
                        <label class="lp-check-label">
                            <input type="checkbox" name="remember" class="lp-checkbox">
                            <span>Recordarme</span>
                        </label>
                        <a href="#" class="lp-forgot">¿Olvidaste tu contraseña?</a>
                    </div>

                    <!-- Botón principal -->
                    <button type="submit" class="lp-btn-main">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Entrar
                    </button>

                </form>

                <!-- Divisor -->
                <div class="lp-divider">
                    <span class="lp-divider-line"></span>
                    <span class="lp-divider-text">o continúa con</span>
                    <span class="lp-divider-line"></span>
                </div>

                <!-- Botones sociales -->
                <div class="lp-socials">
                    <button type="button" class="lp-social-btn">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4" />
                            <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z" fill="#34A853" />
                            <path d="M3.964 10.71c-.18-.54-.282-1.117-.282-1.71s.102-1.17.282-1.71V4.958H.957C.347 6.173 0 7.548 0 9s.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05" />
                            <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z" fill="#EA4335" />
                        </svg>
                        Google
                    </button>
                    <button type="button" class="lp-social-btn">
                        <i class="bi bi-github" style="font-size:18px;"></i>
                        GitHub
                    </button>
                </div>

                <!-- Registro -->
                <p class="lp-register-link">
                    ¿No tienes cuenta?
                    <a href="/registro" class="lp-register-a">Regístrate aquí</a>
                </p>

                <!-- Demo credentials -->
                <div class="lp-demo">
                    <strong>Demo:</strong>
                    Admin: <code>admin@shoppeo.es</code> / <code>12345678</code>
                </div>

            </div><!-- /lp-form-wrap -->
        </div><!-- /lp-right -->

    </div><!-- /lp-card -->
</div><!-- /lp-wrap -->

<script>
    (function() {
        var btn = document.getElementById('eyeBtn');
        var pwd = document.getElementById('password');
        var icon = document.getElementById('eyeIcon');
        if (!btn) return;
        btn.addEventListener('click', function() {
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye-slash';
            }
        });
    }());
</script>