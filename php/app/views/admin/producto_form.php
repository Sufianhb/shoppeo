<!-- Vista: admin/producto_form.php (crear y editar) -->
<?php $isEdit = !empty($producto); ?>

<?php if (!$isEdit): ?>
<!-- ══ WIZARD HEADER ══ -->
<div class="wz-wrap">
    <div class="wz-steps">
        <div class="wz-step active">
            <div class="wz-circle">1</div>
            <div class="wz-step-text">
                <div class="wz-step-label">Crear producto</div>
                <div class="wz-step-sub">Datos básicos</div>
            </div>
        </div>
        <div class="wz-line"></div>
        <div class="wz-step">
            <div class="wz-circle">2</div>
            <div class="wz-step-text">
                <div class="wz-step-label">Precio y tienda</div>
                <div class="wz-step-sub">Añadir precio inicial</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ── Cabecera ── -->
<div class="wz-form-header">
    <a href="/admin/productos" class="wz-back-btn" aria-label="Volver">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="wz-form-title"><?= $isEdit ? 'Editar Producto' : 'Nuevo Producto' ?></h2>
        <?php if ($isEdit): ?>
            <p class="wz-form-sub">ID: <?= (int)$producto['id'] ?></p>
        <?php else: ?>
            <p class="wz-form-sub">Paso 1 de 2 — completa los datos del producto</p>
        <?php endif; ?>
    </div>
</div>

<!-- ── Formulario ── -->
<div class="wz-card">
    <form method="POST"
          action="<?= $isEdit ? "/admin/productos/{$producto['id']}/editar" : '/admin/productos/crear' ?>">

        <!-- Nombre -->
        <div class="wz-field">
            <label class="wz-label" for="nombre">
                Nombre del producto <span class="wz-required">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" class="wz-input"
                   value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                   placeholder="Ej: Air Fryer Cecotec 5L"
                   required maxlength="200">
        </div>

        <!-- Categoría -->
        <div class="wz-field">
            <label class="wz-label" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="wz-select">
                <option value="">— Sin categoría —</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>"
                        <?= ($producto['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Descripción -->
        <div class="wz-field">
            <label class="wz-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="wz-textarea"
                      rows="4" placeholder="Describe el producto…"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
        </div>

        <!-- URL Imagen -->
        <div class="wz-field">
            <label class="wz-label" for="imagen_url">URL de imagen</label>
            <input type="url" id="imagen_url" name="imagen_url" class="wz-input"
                   value="<?= htmlspecialchars($producto['imagen_url'] ?? '') ?>"
                   placeholder="https://…">
            <p class="wz-hint">Introduce la URL directa de la imagen del producto.</p>

            <div id="imgPreviewWrap" class="wz-img-preview <?= empty($producto['imagen_url']) ? 'wz-hidden' : '' ?>">
                <img id="imgPreview"
                     src="<?= htmlspecialchars($producto['imagen_url'] ?? '') ?>"
                     alt="Preview">
            </div>
        </div>

        <!-- Acciones -->
        <div class="wz-actions">
            <button type="submit" class="wz-btn-primary">
                <i class="bi bi-<?= $isEdit ? 'check-lg' : 'arrow-right' ?>"></i>
                <?= $isEdit ? 'Guardar cambios' : 'Crear y continuar' ?>
            </button>
            <a href="/admin/productos" class="wz-btn-ghost">Cancelar</a>
        </div>

    </form>
</div>

<script>
document.getElementById('imagen_url').addEventListener('input', function () {
    var wrap = document.getElementById('imgPreviewWrap');
    var img  = document.getElementById('imgPreview');
    if (this.value) {
        img.src = this.value;
        wrap.classList.remove('wz-hidden');
    } else {
        wrap.classList.add('wz-hidden');
    }
});
</script>
