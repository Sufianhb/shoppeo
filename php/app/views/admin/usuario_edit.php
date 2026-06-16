<!-- Vista: admin/usuario_edit.php -->

<h1 class="ad-page-title">Editar usuario</h1>
<p class="ad-page-sub">Modificar datos de la cuenta</p>

<div style="max-width:560px;">

    <div class="wz-card">

        <!-- Avatar + info estática -->
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:28px;
                    padding:16px 20px;background:#f8f7ff;border-radius:12px;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#7c3aed,#9333ea);
                        border-radius:50%;display:flex;align-items:center;justify-content:center;
                        color:white;font-size:20px;font-weight:700;flex-shrink:0;">
                <?= mb_strtoupper(mb_substr($usuario['nombre'], 0, 1)) ?>
            </div>
            <div>
                <div style="font-weight:700;color:#111827;font-size:15px;">
                    <?= htmlspecialchars($usuario['nombre']) ?>
                </div>
                <div style="font-size:13px;color:#6b7280;">
                    <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($usuario['email']) ?>
                    <span style="margin-left:8px;font-size:11px;color:#9ca3af;">(no editable)</span>
                </div>
            </div>
        </div>

        <form method="POST" action="/admin/usuarios/<?= (int)$usuario['id'] ?>/editar">

            <!-- Nombre -->
            <div class="wz-field">
                <label class="wz-label" for="nombre">
                    <i class="bi bi-person" style="color:#7c3aed;margin-right:6px;"></i>
                    Nombre
                </label>
                <input type="text" id="nombre" name="nombre" class="wz-input"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>"
                       required maxlength="100">
            </div>

            <!-- Rol -->
            <div class="wz-field">
                <label class="wz-label" for="rol_id">
                    <i class="bi bi-shield" style="color:#7c3aed;margin-right:6px;"></i>
                    Rol
                </label>
                <select id="rol_id" name="rol_id" class="wz-select wz-input">
                    <option value="2" <?= (int)$usuario['rol_id'] === 2 ? 'selected' : '' ?>>Usuario</option>
                    <option value="1" <?= (int)$usuario['rol_id'] === 1 ? 'selected' : '' ?>>Administrador</option>
                </select>
                <p class="wz-hint">Cambiar a Admin otorga acceso completo al panel.</p>
            </div>

            <div class="wz-actions">
                <button type="submit" class="wz-btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar cambios
                </button>
                <a href="/admin/usuarios" class="wz-btn-ghost">Cancelar</a>
            </div>

        </form>
    </div>

</div>
