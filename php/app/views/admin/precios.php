<!-- Vista: admin/precios.php -->
<?php
/* ── Detectar modo wizard ── */
$wizardMode      = isset($_GET['nuevo'], $_GET['producto_id']) && (int)$_GET['producto_id'] > 0;
$wizardProductoId = $wizardMode ? (int)$_GET['producto_id'] : null;
$wizardProducto  = null;
if ($wizardMode) {
    foreach ($productos as $p) {
        if ((int)$p['id'] === $wizardProductoId) {
            $wizardProducto = $p;
            break;
        }
    }
    if (!$wizardProducto) $wizardMode = false;
}
?>

<?php if ($wizardMode): ?>
<!-- ══════════════════════════════════════════════
     MODO WIZARD — Paso 2: Añadir precio
     ══════════════════════════════════════════════ -->

<!-- Wizard header -->
<div class="wz-wrap">
    <div class="wz-steps">
        <div class="wz-step completed">
            <div class="wz-circle"><i class="bi bi-check-lg"></i></div>
            <div class="wz-step-text">
                <div class="wz-step-label">Crear producto</div>
                <div class="wz-step-sub">Completado</div>
            </div>
        </div>
        <div class="wz-line wz-line-done"></div>
        <div class="wz-step active">
            <div class="wz-circle">2</div>
            <div class="wz-step-text">
                <div class="wz-step-label">Precio y tienda</div>
                <div class="wz-step-sub">Añadir precio inicial</div>
            </div>
        </div>
    </div>
</div>

<!-- Cabecera wizard -->
<div class="wz-form-header">
    <a href="/admin/precios" class="wz-back-btn" aria-label="Volver a precios">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="wz-form-title">Añadir precio inicial</h2>
        <p class="wz-form-sub">Paso 2 de 2 — indica dónde se vende y a qué precio</p>
    </div>
</div>

<!-- Banner UX -->
<div class="wz-banner">
    <i class="bi bi-info-circle-fill"></i>
    Estás añadiendo el primer precio para este producto. Puedes añadir más tiendas después.
</div>

<!-- Card producto fijo (no editable) -->
<div class="wz-product-fixed">
    <div class="wz-product-fixed-icon">
        <i class="bi bi-box-seam"></i>
    </div>
    <div class="wz-product-fixed-info">
        <div class="wz-product-fixed-name"><?= htmlspecialchars($wizardProducto['nombre']) ?></div>
        <div class="wz-product-fixed-meta">
            ID <?= $wizardProductoId ?>
            <?php if (!empty($wizardProducto['categoria'])): ?>
                &nbsp;·&nbsp; <?= htmlspecialchars($wizardProducto['categoria']) ?>
            <?php endif; ?>
        </div>
    </div>
    <span class="wz-product-fixed-badge">
        <i class="bi bi-check-circle-fill"></i> Producto creado
    </span>
</div>

<!-- Formulario paso 2 -->
<div class="wz-card">
    <form method="POST" action="/admin/precios/actualizar" id="precioForm">

        <input type="hidden" name="producto_id" value="<?= $wizardProductoId ?>">

        <!-- Tienda -->
        <div class="wz-field">
            <label class="wz-label" for="wz-tienda">
                Tienda <span class="wz-required">*</span>
            </label>
            <select id="wz-tienda" name="tienda_id" class="wz-select" required>
                <option value="">Selecciona una tienda…</option>
                <?php foreach ($tiendas as $t): ?>
                    <option value="<?= (int)$t['id'] ?>">
                        <?= htmlspecialchars($t['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Precio -->
        <div class="wz-field">
            <label class="wz-label" for="wz-precio">
                Precio (€) <span class="wz-required">*</span>
            </label>
            <div class="wz-input-prefix-wrap">
                <span class="wz-input-prefix"><i class="bi bi-currency-euro"></i></span>
                <input type="number" id="wz-precio" name="precio" class="wz-input wz-input-with-prefix"
                       step="0.01" min="0" placeholder="0,00" required>
            </div>
        </div>

        <!-- Stock -->
        <div class="wz-field">
            <label class="wz-label" for="wz-stock">Stock disponible</label>
            <input type="number" id="wz-stock" name="stock" class="wz-input"
                   min="0" value="0" placeholder="0">
            <p class="wz-hint">Unidades disponibles en esta tienda.</p>
        </div>

        <!-- Acciones -->
        <div class="wz-actions">
            <button type="submit" class="wz-btn-primary">
                <i class="bi bi-check-lg"></i> Guardar precio
            </button>
            <a href="/admin/precios" class="wz-btn-ghost">Omitir por ahora</a>
        </div>

    </form>
</div>

<?php else: ?>
<!-- ══════════════════════════════════════════════
     MODO NORMAL — Gestión de precios
     ══════════════════════════════════════════════ -->

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;">
    <div>
        <h1 class="ad-page-title">Gestión de Precios</h1>
        <p class="ad-page-sub">Actualiza precios — se emiten en tiempo real vía WebSocket</p>
    </div>
    <span class="ad-badge-rt" id="wsAdminStatus" style="margin-top:6px;">
        <span class="ad-badge-rt-dot"></span>
        <i class="bi bi-lightning-charge-fill"></i> WebSocket listo
    </span>
</div>

<div class="row g-4">

    <!-- Formulario de actualización AJAX -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-pencil-square me-1 text-purple"></i>Actualizar precio
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/precios/actualizar" id="precioForm">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Producto</label>
                        <select class="form-select" name="producto_id" id="selProducto" required>
                            <option value="">Selecciona producto…</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tienda</label>
                        <select class="form-select" name="tienda_id" id="selTienda" required>
                            <option value="">Selecciona tienda…</option>
                            <?php foreach ($tiendas as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo precio (€)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-euro"></i></span>
                            <input type="number" class="form-control" name="precio" id="inputPrecio"
                                   step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <small class="text-muted" id="precioActualMsg"></small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Stock</label>
                        <input type="number" class="form-control" name="stock" min="0" value="0">
                    </div>

                    <button type="submit" class="btn btn-purple w-100 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i>Actualizar precio
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de precios actuales -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Precios actuales</span>
                    <input type="text" class="form-control form-control-sm w-auto"
                           id="filtroPrecios" placeholder="Filtrar…">
                </div>
            </div>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover small align-middle mb-0" id="tablaPreciosAdmin">
                    <thead class="thead-purple sticky-top">
                        <tr>
                            <th>Producto</th>
                            <th>Tienda</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Stock</th>
                            <th>Actualizado</th>
                            <th class="text-center">Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($precios as $p): ?>
                            <tr id="admin-precio-row-<?= $p['id'] ?>">
                                <td><?= htmlspecialchars($p['producto']) ?></td>
                                <td><span class="badge bg-secondary-subtle text-dark"><?= htmlspecialchars($p['tienda']) ?></span></td>
                                <td class="text-end fw-bold" id="ap-precio-<?= $p['id'] ?>">
                                    <?= number_format((float)$p['precio'], 2, ',', '.') ?> €
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $p['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $p['stock'] ?>
                                    </span>
                                </td>
                                <td class="text-muted" id="ap-updated-<?= $p['id'] ?>">
                                    <?= date('d/m/Y H:i', strtotime($p['actualizado_at'])) ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-purple btn-editar-precio"
                                            data-producto-id="<?= $p['producto_id'] ?>"
                                            data-tienda-id="<?= $p['tienda_id'] ?>"
                                            data-precio="<?= $p['precio'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /row -->
<?php endif; ?>

<script>
/* Rellenar formulario al hacer clic en "editar" de la tabla */
document.querySelectorAll('.btn-editar-precio').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var selProd = document.getElementById('selProducto');
        var selTien = document.getElementById('selTienda');
        var inp     = document.getElementById('inputPrecio');
        var msg     = document.getElementById('precioActualMsg');
        if (selProd) selProd.value = this.dataset.productoId;
        if (selTien) selTien.value = this.dataset.tiendaId;
        if (inp) inp.value = parseFloat(this.dataset.precio).toFixed(2);
        if (msg) msg.textContent = 'Precio actual: ' + parseFloat(this.dataset.precio).toFixed(2) + ' €';
        document.getElementById('precioForm').scrollIntoView({ behavior: 'smooth' });
    });
});

/* Filtro de texto en la tabla */
var filtroEl = document.getElementById('filtroPrecios');
if (filtroEl) {
    filtroEl.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#tablaPreciosAdmin tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

/* WebSocket: actividad en tiempo real */
if (typeof io !== 'undefined') {
    var socket = io('http://localhost:3000');
    socket.on('price_activity', function (data) {
        var feed = document.getElementById('adminActivityFeed');
        if (feed) {
            var msg = document.createElement('div');
            msg.className = 'alert alert-info py-1 px-2 mb-1 small';
            msg.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>' +
                '<strong>' + data.tienda + '</strong> actualizó ' +
                '<em>' + data.producto + '</em> → ' +
                '<strong>' + parseFloat(data.precio).toFixed(2) + ' €</strong>' +
                '<span class="text-muted float-end">' + new Date().toLocaleTimeString('es-ES') + '</span>';
            feed.prepend(msg);
            if (feed.children.length > 10) feed.lastChild.remove();
        }
    });
}
</script>
