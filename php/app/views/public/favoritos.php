<!-- Vista: public/favoritos.php -->
<?php $total = count($favoritos); ?>

<div class="bk-page">
    <div class="bk-container">

        <!-- ── Header ── -->
        <div class="bk-header">
            <div class="bk-header-left">
                <h1 class="bk-title">
                    <i class="bi bi-heart-fill" style="color:#dc2626;margin-right:10px;"></i>
                    Mi lista
                </h1>
                <p class="bk-count">
                    <?= $total ?> producto<?= $total !== 1 ? 's' : '' ?> guardado<?= $total !== 1 ? 's' : '' ?>
                </p>
            </div>
            <a href="/buscar" class="bk-empty-btn" style="text-decoration:none;">
                <i class="bi bi-search"></i> Explorar productos
            </a>
        </div>

        <?php if (empty($favoritos)): ?>

            <!-- ── Estado vacío ── -->
            <div class="bk-empty">
                <div class="bk-empty-icon">
                    <i class="bi bi-heart" style="color:#dc2626;"></i>
                </div>
                <h3 class="bk-empty-title">No tienes productos guardados aún</h3>
                <p class="bk-empty-sub">
                    Pulsa el <i class="bi bi-heart" style="color:#dc2626;"></i> en cualquier producto
                    para guardarlo aquí y comparar precios fácilmente.
                </p>
                <a href="/buscar" class="bk-empty-btn">
                    <i class="bi bi-search"></i> Explorar productos
                </a>
            </div>

        <?php else: ?>

            <!-- ── Grid ── -->
            <div class="bk-grid" id="favGrid">
                <?php foreach ($favoritos as $fav): ?>

                    <div class="bk-card" id="fav-card-<?= (int)$fav['producto_id'] ?>">

                        <!-- Imagen + overlays -->
                        <div class="bk-card-img-wrap">
                            <?php if (!empty($fav['imagen_url'])): ?>
                                <img src="<?= htmlspecialchars($fav['imagen_url']) ?>"
                                    alt="<?= htmlspecialchars($fav['nombre']) ?>"
                                    class="bk-card-img"
                                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div class="bk-card-img-ph" style="display:none;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            <?php else: ?>
                                <div class="bk-card-img-ph">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Botón quitar favorito -->
                            <button type="button" class="bk-fav-btn btn-fav"
                                data-id="<?= (int)$fav['producto_id'] ?>"
                                aria-label="Quitar de favoritos">
                                <i class="bi bi-heart-fill text-danger"></i>
                            </button>
                        </div>

                        <!-- Contenido -->
                        <div class="bk-card-body">
                            <h3 class="bk-card-name"><?= htmlspecialchars($fav['nombre']) ?></h3>

                            <?php if ($fav['precio_minimo'] !== null): ?>
                                <div class="bk-price-row">
                                    <span class="bk-price-desde">
                                        desde <?= number_format((float)$fav['precio_minimo'], 2, ',', '.') ?> €
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="bk-no-price">Sin precio registrado</div>
                            <?php endif; ?>

                            <a href="/producto/<?= (int)$fav['producto_id'] ?>" class="btn-compare">
                                <i class="bi bi-bar-chart"></i> Ver comparativa
                            </a>
                        </div>

                    </div><!-- /bk-card -->

                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div><!-- /bk-container -->
</div><!-- /bk-page -->

<script>
    // En la página de favoritos, quitar la card al hacer toggle (desfavoritar)
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('#favGrid .btn-fav').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productoId = btn.dataset.id;
                    // Esperar al fetch global, luego si ya no es favorito → eliminar card
                    setTimeout(function() {
                        const icon = btn.querySelector('i');
                        if (icon && !icon.classList.contains('bi-heart-fill')) {
                            const card = document.getElementById('fav-card-' + productoId);
                            if (card) {
                                card.style.transition = 'opacity .3s';
                                card.style.opacity = '0';
                                setTimeout(function() {
                                    card.remove();
                                    updateCount();
                                }, 300);
                            }
                        }
                    }, 400);
                });
            });

            function updateCount() {
                const remaining = document.querySelectorAll('#favGrid .bk-card').length;
                const countEl = document.querySelector('.bk-count');
                if (countEl) {
                    countEl.textContent = remaining + ' producto' + (remaining !== 1 ? 's' : '') + ' guardado' + (remaining !== 1 ? 's' : '');
                }
                if (remaining === 0) {
                    const grid = document.getElementById('favGrid');
                    if (grid) {
                        grid.innerHTML = '';
                        const empty = document.querySelector('.bk-container');
                        if (empty) {
                            const emptyDiv = document.createElement('div');
                            emptyDiv.className = 'bk-empty';
                            emptyDiv.innerHTML = '<div class="bk-empty-icon"><i class="bi bi-heart" style="color:#dc2626;"></i></div>' +
                                '<h3 class="bk-empty-title">No tienes favoritos aún</h3>' +
                                '<p class="bk-empty-sub">Pulsa el ❤ en cualquier producto para guardarlo aquí.</p>' +
                                '<a href="/buscar" class="bk-empty-btn"><i class="bi bi-search"></i> Explorar productos</a>';
                            grid.replaceWith(emptyDiv);
                        }
                    }
                }
            }
        });
    }());
</script>