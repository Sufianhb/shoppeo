<!-- Vista: admin/usuarios.php -->
<?php
$total   = count($usuarios);
$admins  = count(array_filter($usuarios, fn($u) => $u['rol'] === 'admin'));
$activos = count(array_filter($usuarios, fn($u) => $u['activo']));
?>

<h1 class="ad-page-title">Usuarios</h1>
<p class="ad-page-sub">Cuentas registradas en el sistema</p>

<!-- Stats -->
<div class="ad-kpi-grid" style="grid-template-columns: repeat(3,1fr); margin-bottom: 24px;">
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-purple"><i class="bi bi-people"></i></div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-usuarios-total"><?= $total ?></div>
                <div class="ad-kpi-label">Total usuarios</div>
            </div>
        </div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-amber"><i class="bi bi-shield-check"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $admins ?></div>
                <div class="ad-kpi-label">Administradores</div>
            </div>
        </div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-green"><i class="bi bi-person-check"></i></div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-usuarios-activos"><?= $activos ?></div>
                <div class="ad-kpi-label">Activos</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla usuarios -->
<div class="ad-card">
    <div class="ad-card-header">
        <div class="ad-card-title">
            <i class="bi bi-people" style="color:#7c3aed;"></i>
            Listado de usuarios
        </div>
        <input type="text" id="filtroUsuarios" class="bk-search-input"
               placeholder="Filtrar..." style="width:220px;height:38px;">
    </div>

    <div class="ad-table-wrap">
        <table class="ad-table" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="th-right">Registro</th>
                    <th class="th-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="usuariosTableBody">
                <?php foreach ($usuarios as $u): ?>
                <?php $esSelf = ((int)$u['id'] === (int)($_SESSION['user_id'] ?? 0)); ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#9333ea);
                                        border-radius:50%;display:flex;align-items:center;justify-content:center;
                                        color:white;font-size:14px;font-weight:700;flex-shrink:0;">
                                <?= mb_strtoupper(mb_substr($u['nombre'], 0, 1)) ?>
                            </div>
                            <span style="font-weight:600;color:#111827;">
                                <?= htmlspecialchars($u['nombre']) ?>
                                <?php if ($esSelf): ?>
                                    <span style="font-size:11px;color:#7c3aed;font-weight:500;margin-left:4px;">(tú)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </td>
                    <td style="color:#6b7280;"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php if ($u['rol'] === 'admin'): ?>
                            <span class="ad-store-badge" style="background:#fef3c7;color:#b45309;">
                                <i class="bi bi-shield-fill me-1"></i>Admin
                            </span>
                        <?php else: ?>
                            <span class="ad-store-badge">
                                <i class="bi bi-person me-1"></i>Usuario
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($u['activo']): ?>
                            <span class="pd-stock-badge">Activo</span>
                        <?php else: ?>
                            <span class="pd-stock-badge pd-stock-out">Bloqueado</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-right td-date">
                        <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                    </td>
                    <td class="td-right">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:nowrap;">

                            <!-- Editar -->
                            <a href="/admin/usuarios/<?= (int)$u['id'] ?>/editar"
                               title="Editar"
                               style="display:inline-flex;align-items:center;justify-content:center;
                                      width:30px;height:30px;border-radius:8px;
                                      background:#ede9fe;color:#7c3aed;text-decoration:none;
                                      font-size:13px;transition:background .15s;">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <!-- Bloquear / Activar -->
                            <?php if (!$esSelf): ?>
                            <form method="POST" action="/admin/usuarios/<?= (int)$u['id'] ?>/toggle" style="margin:0;">
                                <button type="submit" title="<?= $u['activo'] ? 'Bloquear' : 'Activar' ?>"
                                        style="display:inline-flex;align-items:center;justify-content:center;
                                               width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;font-size:13px;transition:background .15s;
                                               background:<?= $u['activo'] ? '#fef3c7' : '#dcfce7' ?>;
                                               color:<?= $u['activo'] ? '#b45309' : '#16a34a' ?>;">
                                    <i class="bi <?= $u['activo'] ? 'bi-lock' : 'bi-unlock' ?>"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                            <!-- Reset contraseña -->
                            <form method="POST" action="/admin/usuarios/<?= (int)$u['id'] ?>/reset-password"
                                  style="margin:0;"
                                  onsubmit="return confirm('¿Generar nueva contraseña temporal para <?= htmlspecialchars(addslashes($u['nombre'])) ?>?')">
                                <button type="submit" title="Resetear contraseña"
                                        style="display:inline-flex;align-items:center;justify-content:center;
                                               width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;
                                               background:#dbeafe;color:#1d4ed8;font-size:13px;transition:background .15s;">
                                    <i class="bi bi-key"></i>
                                </button>
                            </form>

                            <!-- Eliminar (solo si no admin y no es uno mismo) -->
                            <?php if ($u['rol'] !== 'admin' && !$esSelf): ?>
                            <form method="POST" action="/admin/usuarios/<?= (int)$u['id'] ?>/eliminar"
                                  style="margin:0;"
                                  onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars(addslashes($u['nombre'])) ?>? Esta acción no se puede deshacer.')">
                                <button type="submit" title="Eliminar"
                                        style="display:inline-flex;align-items:center;justify-content:center;
                                               width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;
                                               background:#fee2e2;color:#dc2626;font-size:13px;transition:background .15s;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('filtroUsuarios').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#tablaUsuarios tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
