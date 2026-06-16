<div style="max-width:680px;margin:0 auto;">
    <!-- Vista: user/configuracion.php -->
    <h1 class="ad-page-title">Mi configuración</h1>
    <p class="ad-page-sub">Ajustes de tu cuenta</p>

    <div class="wz-banner" style="margin-bottom:24px;">
        <i class="bi bi-info-circle-fill"></i>
        Los cambios se aplican inmediatamente en tu sesión.
    </div>

    <div class="wz-card">
        <form method="POST" action="/configuracion">

            <div class="wz-field">
                <label class="wz-label" for="cfg-nombre">
                    <i class="bi bi-person" style="color:#7c3aed;margin-right:6px;"></i>
                    Nombre
                </label>
                <input type="text"
                    id="cfg-nombre"
                    name="nombre"
                    class="wz-input"
                    value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                    required>
                <p class="wz-hint">Nombre que aparece en tu perfil y en el panel.</p>
            </div>

            <div class="wz-field">
                <label class="wz-label" for="cfg-password">
                    <i class="bi bi-lock" style="color:#7c3aed;margin-right:6px;"></i>
                    Nueva contraseña
                </label>
                <input type="password"
                    id="cfg-password"
                    name="password"
                    class="wz-input"
                    placeholder="Dejar en blanco para no cambiar">
                <p class="wz-hint">Mínimo 6 caracteres.</p>
            </div>

            <div class="wz-field">
                <label class="wz-label" for="cfg-password-confirm">
                    <i class="bi bi-lock-fill" style="color:#7c3aed;margin-right:6px;"></i>
                    Confirmar contraseña
                </label>
                <input type="password"
                    id="cfg-password-confirm"
                    name="password_confirm"
                    class="wz-input"
                    placeholder="Repite la nueva contraseña">
            </div>

            <div class="wz-actions">
                <button type="submit" class="wz-btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar cambios
                </button>
                <a href="/" class="wz-btn-ghost">Cancelar</a>
            </div>

        </form>
    </div>

</div>