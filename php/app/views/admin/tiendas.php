<!-- Vista: admin/tiendas.php -->
<h1 class="ad-page-title">Gestión de Tiendas</h1>
<p class="ad-page-sub"><span id="tiendasCount"><?= count($tiendas) ?></span> tiendas registradas</p>

<div class="row g-4">
    <!-- Formulario nueva tienda -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-plus-circle me-1 text-purple"></i>Nueva tienda
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/tiendas/crear">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Direccion</label>
                        <input type="text" class="form-control" name="direccion"
                            placeholder="Calle, número, Teruel">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Latitud <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="latitud"
                                placeholder="40.3456" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Longitud <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="longitud"
                                placeholder="-1.1065" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" placeholder="978 000 000">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Web</label>
                        <input type="url" class="form-control" name="web" placeholder="https://…">
                    </div>
                    <button type="submit" class="btn btn-purple w-100 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i>Crear tienda
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body p-2">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Puedes obtener coordenadas desde
                    <a href="https://www.openstreetmap.org" target="_blank">OpenStreetMap</a>:
                    haz clic derecho sobre el mapa → "Mostrar direccion".
                </small>
            </div>
        </div>
    </div>

    <!-- Tabla de tiendas -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-purple">
                            <tr>
                                <th>Nombre</th>
                                <th>Direccion</th>
                                <th>Teléfono</th>
                                <th class="text-center">Coords</th>
                                <th>Web</th>
                            </tr>
                        </thead>
                        <tbody id="tiendasTableBody">
                            <?php foreach ($tiendas as $t): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($t['nombre']) ?></strong></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($t['direccion'] ?? '—') ?></small></td>
                                    <td><small><?= htmlspecialchars($t['telefono'] ?? '—') ?></small></td>
                                    <td class="text-center">
                                        <small class="text-muted font-monospace">
                                            <?= $t['latitud'] ?>, <?= $t['longitud'] ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($t['web']): ?>
                                            <a href="<?= htmlspecialchars($t['web']) ?>" target="_blank" class="small">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>