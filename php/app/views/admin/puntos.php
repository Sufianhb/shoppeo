<!-- Vista: admin/puntos.php -->

<h1 class="ad-page-title"><i class="bi bi-star-fill text-purple me-2"></i>Gestión de Puntos</h1>
<p class="ad-page-sub">Otorga puntos a usuarios y consulta el historial de transacciones</p>

<!-- ══ KPIs ══ -->
<div class="ad-kpi-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:28px;">

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-purple"><i class="bi bi-star-fill"></i></div>
            <div>
                <div class="ad-kpi-val"><?= number_format((int)$stats['puntos_otorgados'], 0, ',', '.') ?></div>
                <div class="ad-kpi-label">Puntos otorgados hoy</div>
            </div>
        </div>
    </div>

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#fef3c7;color:#d97706;"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="ad-kpi-val"><?= count($ranking) ?></div>
                <div class="ad-kpi-label">Usuarios con puntos</div>
            </div>
        </div>
    </div>

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <div class="ad-kpi-val"><?= $ranking ? number_format((int)$ranking[0]['total_puntos'], 0, ',', '.') : 0 ?></div>
                <div class="ad-kpi-label">Máximo en ranking</div>
            </div>
        </div>
    </div>

    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon" style="background:#fce7f3;color:#db2777;"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="ad-kpi-val"><?= (int)$stats['transacciones'] ?></div>
                <div class="ad-kpi-label">Transacciones hoy</div>
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

    <!-- ══ COLUMNA IZQUIERDA: Otorgar + Ranking ══ -->
    <div class="col-lg-4">

        <!-- Otorgar puntos -->
        <div class="ad-card mb-4">
            <div class="ad-card-header">
                <i class="bi bi-plus-circle-fill text-purple"></i>
                <span>Otorgar puntos</span>
            </div>
            <div class="p-3">
                <form method="POST" action="/admin/puntos/otorgar">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Usuario</label>
                        <select name="usuario_id" class="form-select form-select-sm" required>
                            <option value="">— Selecciona usuario —</option>
                            <?php foreach ($usuarios as $u): ?>
                            <option value="<?= (int)$u['id'] ?>">
                                <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['email']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Puntos</label>
                        <input type="number" name="puntos" class="form-control form-control-sm"
                               min="1" max="100000" placeholder="ej. 50" required>
                        <div class="form-text">1 punto = 1 € de compra</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Concepto</label>
                        <input type="text" name="concepto" class="form-control form-control-sm"
                               placeholder="ej. Compra en Tienda X" maxlength="200" required>
                    </div>
                    <button type="submit" class="btn btn-purple w-100 btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Otorgar puntos
                    </button>
                </form>
            </div>
        </div>

        <!-- Ranking usuarios -->
        <div class="ad-card">
            <div class="ad-card-header">
                <i class="bi bi-bar-chart-fill text-purple"></i>
                <span>Top usuarios</span>
            </div>
            <div class="p-0">
                <?php if (empty($ranking)): ?>
                    <p class="text-muted p-3 mb-0" style="font-size:13px;">Sin datos aún.</p>
                <?php else: ?>
                <ol class="list-group list-group-flush" style="counter-reset:ranking">
                    <?php foreach (array_slice($ranking, 0, 10) as $i => $r): ?>
                    <li class="list-group-item d-flex align-items-center gap-2 px-3 py-2" style="font-size:13px;">
                        <span class="fw-bold" style="width:20px;color:#7c3aed;"><?= $i + 1 ?>.</span>
                        <div class="flex-grow-1">
                            <div class="fw-semibold"><?= htmlspecialchars($r['nombre']) ?></div>
                            <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars($r['email']) ?></div>
                        </div>
                        <span class="badge" style="background:#ede9fe;color:#5b21b6;font-size:11px;">
                            <?= number_format((int)$r['total_puntos'], 0, ',', '.') ?> pts
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ══ COLUMNA DERECHA: Historial ══ -->
    <div class="col-lg-8">
        <div class="ad-card">
            <div class="ad-card-header">
                <i class="bi bi-clock-history text-purple"></i>
                <span>Historial de transacciones</span>
            </div>
            <div class="p-0">
                <?php if (empty($transacciones)): ?>
                    <p class="text-muted p-4 mb-0" style="font-size:13px;">No hay transacciones registradas.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size:13px;">
                        <thead class="thead-purple">
                            <tr>
                                <th>Usuario</th>
                                <th>Concepto</th>
                                <th class="text-end">Puntos</th>
                                <th>Otorgado por</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transacciones as $t):
                                $positivo = (int)$t['puntos'] > 0;
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($t['usuario_nombre']) ?></div>
                                    <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars($t['usuario_email']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($t['concepto']) ?></td>
                                <td class="text-end fw-bold <?= $positivo ? 'text-success' : 'text-danger' ?>">
                                    <?= $positivo ? '+' : '' ?><?= number_format((int)$t['puntos'], 0, ',', '.') ?>
                                </td>
                                <td><?= $t['admin_nombre'] ? htmlspecialchars($t['admin_nombre']) : '<span class="text-muted">Sistema</span>' ?></td>
                                <td class="text-muted"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
