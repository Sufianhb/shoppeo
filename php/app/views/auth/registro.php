<!-- Vista: auth/registro.php — Pixel-perfect register -->
<style>
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
    <div class="lp-card rg-card">

        <!--  PANEL IZQUIERDO  -->
        <div class="lp-left rg-left">
            <div class="lp-circle lp-circle-1"></div>
            <div class="lp-circle lp-circle-2"></div>

            <!-- Logo -->
            <div class="lp-logo">
                <img src="/assets/logo-white.png" alt="shoppeo"
                    onerror="this.onerror=null;this.src='/img/logo_completo.png';this.style.filter='brightness(0) invert(1)';"
                    style="height:36px;filter:brightness(0) invert(1);">
            </div>

            <!-- Titular -->
            <div class="lp-headline">
                <h1 class="lp-title-line1">Tu tienda online,</h1>
                <h1 class="lp-title-line2">más fácil que nunca</h1>
                <p class="lp-subtitle">
                    Crea tu cuenta y comienza a gestionar<br>
                    tus productos, pedidos y clientes<br>desde un solo lugar.
                </p>
            </div>

            <!-- Imagen decorativa -->
            <div class="lp-img-wrap">
                <img src="/img/logo_copy.png" alt=""
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
        <div class="rg-right">
            <div class="rg-form-wrap">

                <!-- Icono superior -->
                <div class="lp-top-icon">
                    <i class="bi bi-shop-window"></i>
                </div>

                <h2 class="lp-form-title">¡Crea tu cuenta!</h2>
                <p class="lp-form-sub">Únete a Shoppeo y empieza hoy</p>

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
                <form method="POST" action="/registro" novalidate>

                    <!-- Fila 1: Nombre + Email en 2 columnas -->
                    <div class="rg-row-2col">
                        <div class="lp-field">
                            <label class="lp-label" for="rg-nombre">Nombre completo</label>
                            <div class="lp-input-wrap">
                                <i class="bi bi-person lp-input-icon"></i>
                                <input type="text" id="rg-nombre" name="nombre"
                                    class="lp-input rg-input"
                                    placeholder="Tu nombre"
                                    required minlength="2">
                            </div>
                        </div>
                        <div class="lp-field">
                            <label class="lp-label" for="rg-email">Email</label>
                            <div class="lp-input-wrap">
                                <i class="bi bi-envelope lp-input-icon"></i>
                                <input type="email" id="rg-email" name="email"
                                    class="lp-input rg-input"
                                    placeholder="tu@email.com"
                                    required autocomplete="email">
                            </div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="lp-field">
                        <label class="lp-label" for="rg-password">Contraseña</label>
                        <div class="lp-input-wrap">
                            <i class="bi bi-lock lp-input-icon"></i>
                            <input type="password" id="rg-password" name="password"
                                class="lp-input lp-input-pwd rg-input"
                                placeholder="Mín. 8 caracteres"
                                required minlength="8">
                            <button type="button" class="lp-eye-btn" data-target="rg-password" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar contraseña -->
                    <div class="lp-field">
                        <label class="lp-label" for="rg-confirm">Confirmar contraseña</label>
                        <div class="lp-input-wrap">
                            <i class="bi bi-lock lp-input-icon"></i>
                            <input type="password" id="rg-confirm" name="password_confirm"
                                class="lp-input lp-input-pwd rg-input"
                                placeholder="Repite tu contraseña"
                                required minlength="8">
                            <button type="button" class="lp-eye-btn" data-target="rg-confirm" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox términos -->
                    <div class="rg-terms">
                        <label class="rg-terms-label">
                            <input type="checkbox" class="lp-checkbox rg-checkbox" required>
                            <span>
                                Acepto los
                                <a href="#" class="rg-link">Términos y Condiciones</a>
                                y la
                                <a href="#" class="rg-link">Política de Privacidad</a>
                            </span>
                        </label>
                    </div>

                    <!-- Botón principal -->
                    <button type="submit" class="rg-btn-main">
                        <i class="bi bi-person-plus-fill"></i>
                        Crear cuenta
                    </button>

                </form>

                <!-- Divisor -->
                <div class="lp-divider">
                    <span class="lp-divider-line"></span>
                    <span class="lp-divider-text">o regístrate con</span>
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

                <!-- Enlace login -->
                <p class="lp-register-link">
                    ¿Ya tienes cuenta?
                    <a href="/login" class="lp-register-a">Inicia sesión</a>
                </p>

            </div><!-- /rg-form-wrap -->
        </div><!-- /rg-right -->

    </div><!-- /lp-card -->
</div><!-- /lp-wrap -->

<script>
    (function() {
        document.querySelectorAll('.lp-eye-btn[data-target]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = document.getElementById(this.dataset.target);
                var icon = this.querySelector('i');
                if (!target) return;
                if (target.type === 'password') {
                    target.type = 'text';
                    icon.className = 'bi bi-eye';
                } else {
                    target.type = 'password';
                    icon.className = 'bi bi-eye-slash';
                }
            });
        });
    }());
</script>