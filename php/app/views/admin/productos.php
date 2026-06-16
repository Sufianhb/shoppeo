<!-- Vista: admin/productos.php -->
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;">
    <div>
        <h1 class="ad-page-title">Gestión de Productos</h1>
        <p class="ad-page-sub"><?= count($productos) ?> productos activos</p>
    </div>
    <a href="/admin/productos/crear" class="btn btn-purple" style="margin-top:4px;">
        <i class="bi bi-plus-lg me-1"></i>Nuevo producto
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-purple">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td class="text-muted small"><?= $p['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($p['imagen_url']): ?>
                                    <img src="<?= htmlspecialchars($p['imagen_url']) ?>"
                                         width="40" height="40" class="rounded object-fit-cover border">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px">
                                        <i class="bi bi-box-seam text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <?php if ($p['categoria']): ?>
                                <span class="badge bg-secondary-subtle text-dark">
                                    <?= htmlspecialchars($p['categoria']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= htmlspecialchars(mb_substr($p['descripcion'] ?? '', 0, 60)) ?>
                                <?= strlen($p['descripcion'] ?? '') > 60 ? '…' : '' ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="/producto/<?= $p['id'] ?>" target="_blank"
                                   class="btn btn-outline-secondary" title="Ver en web">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/admin/productos/<?= $p['id'] ?>/editar"
                                   class="btn btn-outline-purple" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="/admin/productos/<?= $p['id'] ?>/eliminar"
                                      onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>»? Esta acción no se puede deshacer.')">
                                    <button class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
