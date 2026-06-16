<!-- Vista: admin/actividad.php -->

<h1 class="ad-page-title">Actividad</h1>
<p class="ad-page-sub">Visitas y eventos registrados en tiempo real</p>

<!-- Stats -->
<div class="ad-kpi-grid" style="grid-template-columns: repeat(3,1fr); margin-bottom: 24px;">
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-purple"><i class="bi bi-eye"></i></div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-hoy"><?= $stats['hoy'] ?></div>
                <div class="ad-kpi-label">Visitas hoy</div>
            </div>
        </div>
        <div class="ad-kpi-trend"><i class="bi bi-calendar-day"></i> Hoy</div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-blue"><i class="bi bi-graph-up"></i></div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-total"><?= $stats['total'] ?></div>
                <div class="ad-kpi-label">Total visitas</div>
            </div>
        </div>
        <div class="ad-kpi-trend" style="color:#3b82f6;"><i class="bi bi-arrow-up-right"></i> Acumulado</div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-amber"><i class="bi bi-trophy"></i></div>
            <div>
                <div class="ad-kpi-val" style="font-size:18px;letter-spacing:0;">
                    <?= $stats['top_producto'] ? htmlspecialchars(mb_substr($stats['top_producto']['nombre'], 0, 18)) . '…' : '—' ?>
                </div>
                <div class="ad-kpi-label">Producto más visto
                    <?php if ($stats['top_producto']): ?>
                        (<?= $stats['top_producto']['visitas'] ?> veces)
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feed de actividad -->
<div class="ad-card">
    <div class="ad-card-header">
        <div class="ad-card-title">
            <i class="bi bi-activity" style="color:#7c3aed;"></i>
            Últimos eventos
        </div>
        <span class="ad-ws-badge">
            <span class="ad-ws-dot"></span>
            <?= count($actividad) ?> registros
        </span>
    </div>

    <?php if (empty($actividad)): ?>
        <div id="actividadEmpty" style="text-align:center;padding:60px 24px;color:#9ca3af;">
            <i class="bi bi-activity" style="font-size:40px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:15px;font-weight:600;color:#374151;margin:0 0 6px;">Sin actividad todavía</p>
            <p style="font-size:13px;margin:0;">Las visitas y eventos aparecerán aquí en tiempo real.</p>
        </div>
    <?php endif; ?>
    <div class="ad-table-wrap" id="actividadTableWrap" <?= empty($actividad) ? 'style="display:none"' : '' ?>>
        <table class="ad-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="th-right">Fecha y hora</th>
                </tr>
            </thead>
            <tbody id="actividadTableBody">
                <?php foreach ($actividad as $a): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:32px;height:32px;background:#ede9fe;border-radius:8px;
                                        display:flex;align-items:center;justify-content:center;
                                        color:#7c3aed;font-size:14px;flex-shrink:0;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <?php if ($a['producto']): ?>
                                <a href="/producto/<?= (int)$a['producto_id'] ?>"
                                   style="color:#111827;font-weight:600;text-decoration:none;font-size:13.5px;"
                                   target="_blank">
                                    <?= htmlspecialchars($a['producto']) ?>
                                    <i class="bi bi-box-arrow-up-right" style="font-size:11px;color:#9ca3af;"></i>
                                </a>
                            <?php else: ?>
                                <span style="color:#9ca3af;font-size:13px;">Producto simulado</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($a['tipo'] === 'view'): ?>
                            <span class="ad-store-badge"><i class="bi bi-eye me-1"></i>Vista</span>
                        <?php elseif ($a['tipo'] === 'scan'): ?>
                            <span class="ad-store-badge" style="background:#f5f3ff;color:#6d28d9;">
                                <i class="bi bi-qr-code me-1"></i>Escaneo QR
                            </span>
                        <?php else: ?>
                            <span class="ad-store-badge" style="background:#f3f4f6;color:#6b7280;">
                                <?= htmlspecialchars($a['tipo']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="td-right td-date">
                        <?= date('d/m/Y H:i:s', strtotime($a['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
