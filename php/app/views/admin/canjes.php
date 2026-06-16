<!-- Vista: admin/canjes.php -->

<h1 class="ad-page-title"><i class="bi bi-bag-heart-fill text-purple me-2"></i>Gestión de Canjes</h1>
<p class="ad-page-sub">Gestiona las solicitudes de recompensa y coordina el envío a los usuarios</p>

<!-- ══ KPIs ══ -->
<div class="ad-kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));margin-bottom:28px;">

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#fef9c3;color:#ca8a04;"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['pendiente'] ?></div>
                <div class="ad-kpi-label">Pendientes</div>
            </div>
        </div>
    </div>

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-truck"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['enviado'] ?></div>
                <div class="ad-kpi-label">En camino</div>
            </div>
        </div>
    </div>

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['completado'] ?></div>
                <div class="ad-kpi-label">Entregados</div>
            </div>
        </div>
    </div>

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-x-circle"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['cancelado'] ?></div>
                <div class="ad-kpi-label">Cancelados</div>
            </div>
        </div>
    </div>

</div>

<!-- ══ TABLA CANJES ══ -->
<div class="ad-card">
    <div class="ad-card-header">
        <i class="bi bi-list-check text-purple"></i>
        <span>Solicitudes de canje</span>
        <span class="ms-auto badge" style="background:#ede9fe;color:#5b21b6;">
            <?= count($canjes) ?> total
        </span>
    </div>
    <div class="p-0">
        <?php if (empty($canjes)): ?>
            <p class="text-muted p-4 mb-0" style="font-size:13px;">No hay canjes registrados aún.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead class="thead-purple">
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Recompensa</th>
                        <th>Dirección de envío</th>
                        <th class="text-end">Puntos</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($canjes as $c):
                        $badgeStyle = match($c['estado']) {
                            'pendiente'  => 'background:#fef9c3;color:#92400e;',
                            'enviado'    => 'background:#dbeafe;color:#1e40af;',
                            'completado' => 'background:#dcfce7;color:#166534;',
                            default      => 'background:#fee2e2;color:#991b1b;',
                        };
                        $badgeLabel = match($c['estado']) {
                            'pendiente'  => 'Pendiente',
                            'enviado'    => 'En camino',
                            'completado' => 'Entregado',
                            default      => 'Cancelado',
                        };
                    ?>
                    <tr>
                        <td class="text-muted">#<?= (int)$c['id'] ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($c['usuario_nombre']) ?></div>
                            <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars($c['usuario_email']) ?></div>
                        </td>
                        <td>
                            <span style="font-size:18px;"><?= $c['emoji'] ?></span>
                            <span class="fw-semibold ms-1"><?= htmlspecialchars($c['recompensa_nombre']) ?></span>
                        </td>
                        <td style="max-width:180px;">
                            <span class="text-truncate d-block" title="<?= htmlspecialchars($c['direccion_envio'] ?? '') ?>">
                                <?= htmlspecialchars($c['direccion_envio'] ?? '—') ?>
                            </span>
                        </td>
                        <td class="text-end fw-bold" style="color:#7c3aed;">
                            <?= number_format((int)$c['puntos_usados'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <span class="badge" style="<?= $badgeStyle ?>font-size:11px;">
                                <?= $badgeLabel ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                        <td>
                            <?php if ($c['estado'] !== 'completado' && $c['estado'] !== 'cancelado'): ?>
                            <button type="button" class="btn btn-outline-purple btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalCanje<?= (int)$c['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php else: ?>
                            <span class="text-muted" style="font-size:11px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ══ MODALES UPDATE ESTADO ══ -->
<?php foreach ($canjes as $c):
    if ($c['estado'] === 'completado' || $c['estado'] === 'cancelado') continue;
?>
<div class="modal fade" id="modalCanje<?= (int)$c['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;">
                <h5 class="modal-title fw-bold" style="font-size:15px;">
                    <?= $c['emoji'] ?> Actualizar canje #<?= (int)$c['id'] ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/canjes/<?= (int)$c['id'] ?>/estado">
                <div class="modal-body">
                    <div class="mb-3 p-3" style="background:#f9fafb;border-radius:10px;font-size:13px;">
                        <strong><?= htmlspecialchars($c['usuario_nombre']) ?></strong> · <?= htmlspecialchars($c['recompensa_nombre']) ?><br>
                        <span class="text-muted"><?= htmlspecialchars($c['direccion_envio'] ?? '—') ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nuevo estado</label>
                        <select name="estado" class="form-select form-select-sm" required>
                            <option value="pendiente" <?= $c['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="enviado"   <?= $c['estado'] === 'enviado'   ? 'selected' : '' ?>>En camino</option>
                            <option value="completado">Entregado</option>
                            <option value="cancelado">Cancelado (devuelve puntos)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:13px;">Notas internas</label>
                        <textarea name="notas_admin" class="form-control form-control-sm" rows="2"
                                  placeholder="Número de seguimiento, incidencia..."><?= htmlspecialchars($c['notas_admin'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f3f4f6;gap:8px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-purple">
                        <i class="bi bi-check-lg me-1"></i> Guardar cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
