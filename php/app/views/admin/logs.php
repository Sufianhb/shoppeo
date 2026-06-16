<!-- Vista: admin/logs.php -->

<h1 class="ad-page-title">Logs del sistema</h1>
<p class="ad-page-sub">Registro de eventos y errores</p>

<!-- Stats -->
<div class="ad-kpi-grid" style="grid-template-columns: repeat(4,1fr); margin-bottom: 24px;">
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-purple"><i class="bi bi-journal-text"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['total'] ?></div>
                <div class="ad-kpi-label">Total entradas</div>
            </div>
        </div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-blue"><i class="bi bi-info-circle"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['info'] ?></div>
                <div class="ad-kpi-label">Info</div>
            </div>
        </div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-amber"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $stats['warning'] ?></div>
                <div class="ad-kpi-label">Advertencias</div>
            </div>
        </div>
    </div>
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#fee2e2;color:#dc2626;width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:23px;">
                <i class="bi bi-x-circle"></i>
            </div>
            <div>
                <div class="ad-kpi-val"><?= $stats['error'] ?></div>
                <div class="ad-kpi-label">Errores</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de logs -->
<div class="ad-card">
    <div class="ad-card-header">
        <div class="ad-card-title">
            <i class="bi bi-journal-code" style="color:#7c3aed;"></i>
            Entradas del log
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <!-- Filtro por nivel -->
            <select id="filtroNivel" class="wz-select" style="width:140px;height:38px;padding:6px 12px;font-size:13px;">
                <option value="">Todos</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="error">Error</option>
            </select>
            <input type="text" id="filtroLogs" class="bk-search-input"
                   placeholder="Buscar…" style="width:220px;height:38px;">
        </div>
    </div>

    <?php if (empty($logs)): ?>
        <div style="text-align:center;padding:60px 24px;color:#9ca3af;">
            <i class="bi bi-journal-text" style="font-size:40px;display:block;margin-bottom:12px;"></i>
            <p style="font-size:15px;font-weight:600;color:#374151;margin:0 0 6px;">Sin entradas en el log</p>
            <p style="font-size:13px;margin:0;">Los eventos se registrarán automáticamente.</p>
        </div>
    <?php else: ?>
        <div class="ad-table-wrap">
            <table class="ad-table" id="tablaLogs">
                <thead>
                    <tr>
                        <th>Nivel</th>
                        <th>Mensaje</th>
                        <th class="th-right">Fecha y hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr data-nivel="<?= htmlspecialchars($log['nivel']) ?>">
                        <td style="width:110px;">
                            <?php
                            $nivelStyles = [
                                'info'    => 'background:#dbeafe;color:#1d4ed8;',
                                'warning' => 'background:#fef3c7;color:#b45309;',
                                'error'   => 'background:#fee2e2;color:#dc2626;',
                            ];
                            $nivelIcons = [
                                'info'    => 'bi-info-circle',
                                'warning' => 'bi-exclamation-triangle',
                                'error'   => 'bi-x-circle',
                            ];
                            $style = $nivelStyles[$log['nivel']] ?? 'background:#f3f4f6;color:#6b7280;';
                            $icon  = $nivelIcons[$log['nivel']] ?? 'bi-circle';
                            ?>
                            <span class="ad-store-badge" style="<?= $style ?>">
                                <i class="bi <?= $icon ?> me-1"></i>
                                <?= htmlspecialchars(ucfirst($log['nivel'])) ?>
                            </span>
                        </td>
                        <td style="font-size:13.5px;color:#374151;max-width:500px;word-break:break-word;">
                            <?= htmlspecialchars($log['mensaje']) ?>
                        </td>
                        <td class="td-right td-date" style="white-space:nowrap;">
                            <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
(function () {
    var filtroNivel = document.getElementById('filtroNivel');
    var filtroTexto = document.getElementById('filtroLogs');

    function aplicarFiltros() {
        var nivel = filtroNivel ? filtroNivel.value.toLowerCase() : '';
        var texto = filtroTexto ? filtroTexto.value.toLowerCase() : '';
        document.querySelectorAll('#tablaLogs tbody tr').forEach(function (row) {
            var matchNivel = !nivel || (row.dataset.nivel || '').toLowerCase() === nivel;
            var matchTexto = !texto || row.textContent.toLowerCase().includes(texto);
            row.style.display = (matchNivel && matchTexto) ? '' : 'none';
        });
    }

    if (filtroNivel) filtroNivel.addEventListener('change', aplicarFiltros);
    if (filtroTexto) filtroTexto.addEventListener('input', aplicarFiltros);
}());
</script>
