<!-- Vista: public/buscar.php — Pixel-perfect redesign -->
<?php
$total     = count($productos);
$categoria = $categoria ?? '';
?>

<div class="bk-page">
    <div class="bk-container">

        <!--  Header  -->
        <div class="bk-header">

            <div class="bk-header-left">
                <h1 class="bk-title">
                    <?php if ($termino && $categoria): ?>
                        Resultados para <span class="bk-query">"<?= htmlspecialchars($termino) ?>"</span>
                        <span style="color:#9ca3af;font-weight:400;font-size:0.7em;">en</span>
                        <span class="bk-query"><?= htmlspecialchars($categoria) ?></span>
                    <?php elseif ($categoria): ?>
                        <span class="bk-query"><?= htmlspecialchars($categoria) ?></span>
                    <?php elseif ($termino): ?>
                        Resultados para <span class="bk-query">"<?= htmlspecialchars($termino) ?>"</span>
                    <?php else: ?>
                        Todos los productos
                    <?php endif; ?>
                </h1>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <p class="bk-count" style="margin:0;">
                        <?= $total ?> producto<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?>
                    </p>
                    <?php if ($categoria): ?>
                        <a href="<?= $termino ? '/buscar?q=' . urlencode($termino) : '/buscar' ?>"
                            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;
                              color:#7c3aed;background:#f5f3ff;padding:3px 10px;border-radius:20px;
                              text-decoration:none;border:1px solid #ede9fe;">
                            <i class="bi bi-x-lg" style="font-size:10px;"></i>
                            Quitar filtro
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <form action="/buscar" method="GET" class="bk-search-form">
                <?php if ($categoria): ?>
                    <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
                <?php endif; ?>
                <?php if ($lat !== null && $lng !== null): ?>
                    <input type="hidden" name="lat" value="<?= htmlspecialchars((string)$lat) ?>">
                    <input type="hidden" name="lng" value="<?= htmlspecialchars((string)$lng) ?>">
                <?php endif; ?>
                <?php if ($radius_km !== null): ?>
                    <input type="hidden" name="radius_km" value="<?= htmlspecialchars((string)$radius_km) ?>">
                <?php endif; ?>
                <?php if ($precio_min !== null): ?>
                    <input type="hidden" name="precio_min" value="<?= htmlspecialchars((string)$precio_min) ?>">
                <?php endif; ?>
                <?php if ($precio_max !== null): ?>
                    <input type="hidden" name="precio_max" value="<?= htmlspecialchars((string)$precio_max) ?>">
                <?php endif; ?>
                <div class="bk-search-wrap">
                    <input type="search" name="q" class="bk-search-input"
                        value="<?= htmlspecialchars($termino) ?>"
                        placeholder="Busca un producto..."
                        autocomplete="off">
                    <button type="submit" class="bk-search-btn" aria-label="Buscar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

        </div><!-- /bk-header -->

        <!--  Panel de filtros  -->
        <form action="/buscar" method="GET" class="bk-filters">
            <input type="hidden" name="q" value="<?= htmlspecialchars($termino) ?>">
            <?php if ($categoria): ?>
                <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
            <?php endif; ?>
            <?php if ($lat !== null && $lng !== null): ?>
                <input type="hidden" name="lat" value="<?= htmlspecialchars((string)$lat) ?>">
                <input type="hidden" name="lng" value="<?= htmlspecialchars((string)$lng) ?>">
            <?php endif; ?>

            <!-- Slider distancia: siempre en el DOM, JS lo muestra si hay ubicación -->
            <div class="bk-filter-group" id="filterDistancia"
                style="<?= ($lat !== null && $lng !== null) ? '' : 'display:none' ?>">
                <span class="bk-filter-label">
                    <i class="bi bi-geo-alt-fill" style="color:#7c3aed;"></i> Distancia
                </span>
                <div class="bk-slider-wrap">
                    <input type="range" class="bk-slider" id="sliderRadius"
                        name="radius_km" min="1" max="50" step="1"
                        value="<?= (int)($radius_km ?? 10) ?>">
                    <span class="bk-slider-val" id="sliderLabel"><?= (int)($radius_km ?? 10) ?> km</span>
                </div>
            </div>
            <span class="bk-loc-hint" id="filterLocHint"
                style="<?= ($lat !== null && $lng !== null) ? 'display:none' : '' ?>">
                <i class="bi bi-geo-alt"></i> Activa tu ubicación para filtrar por distancia
            </span>
            <div class="bk-filter-sep-v"></div>

            <!-- Rango de precio -->
            <div class="bk-filter-group">
                <span class="bk-filter-label">
                    <i class="bi bi-tag" style="color:#7c3aed;"></i> Precio
                </span>
                <div class="bk-filter-price-wrap">
                    <input type="number" class="bk-filter-price-input" name="precio_min"
                        min="0" step="1" placeholder="Mín €"
                        value="<?= htmlspecialchars((string)($precio_min ?? '')) ?>">
                    <span class="bk-filter-sep">–</span>
                    <input type="number" class="bk-filter-price-input" name="precio_max"
                        min="0" step="1" placeholder="Máx €"
                        value="<?= htmlspecialchars((string)($precio_max ?? '')) ?>">
                </div>
            </div>

            <!-- Acciones -->
            <div style="display:flex;align-items:center;gap:12px;margin-left:auto;flex-wrap:wrap;">
                <?php
                $hayFiltrosActivos = $radius_km !== null || $precio_min !== null || $precio_max !== null;
                $urlLimpiar = '/buscar?q=' . urlencode($termino)
                    . ($categoria ? '&categoria=' . urlencode($categoria) : '')
                    . ($lat !== null ? '&lat=' . $lat . '&lng=' . $lng : '');
                ?>
                <?php if ($hayFiltrosActivos): ?>
                    <a href="<?= htmlspecialchars($urlLimpiar) ?>" class="bk-filter-clear">
                        <i class="bi bi-x-lg" style="font-size:10px;"></i> Limpiar
                    </a>
                <?php endif; ?>
                <button type="submit" class="bk-filter-apply">
                    <i class="bi bi-funnel-fill"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <script>
            (function() {
                var slider = document.getElementById('sliderRadius');
                var label = document.getElementById('sliderLabel');
                var section = document.getElementById('filterDistancia');
                var hint = document.getElementById('filterLocHint');
                var form = section ? section.closest('form') : null;

                var lat = localStorage.getItem('userLat');
                var lon = localStorage.getItem('userLon');

                // Mostrar slider si hay ubicación en localStorage (aunque no venga en la URL)
                if (lat && lon && section) {
                    section.style.display = '';
                    if (hint) hint.style.display = 'none';

                    // Si el form del filtro aún no tiene lat/lng (PHP no los inyectó), hacerlo ahora
                    if (form && !form.querySelector('input[name="lat"]')) {
                        function addHiddenToFilterForm(name, value) {
                            var inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = name;
                            inp.value = value;
                            form.appendChild(inp);
                        }
                        addHiddenToFilterForm('lat', lat);
                        addHiddenToFilterForm('lng', lon);
                    }
                }

                if (!slider) return;

                // Inicializar valor: URL tiene prioridad, luego localStorage, luego default (10)
                var urlRadius = <?= json_encode($radius_km) ?>;
                if (urlRadius === null) {
                    var stored = localStorage.getItem('userRadius');
                    if (stored) {
                        slider.value = stored;
                        label.textContent = stored + ' km';
                    }
                } else {
                    label.textContent = urlRadius + ' km';
                }

                slider.addEventListener('input', function() {
                    label.textContent = this.value + ' km';
                    localStorage.setItem('userRadius', this.value);
                });
            }());
        </script>

        <?php if (empty($productos)): ?>

            <!--  Estado vacío  -->
            <div class="bk-empty">
                <div class="bk-empty-icon">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="bk-empty-title">Sin resultados</h3>
                <p class="bk-empty-sub">
                    No encontramos productos para
                    <?php if ($termino): ?>
                        <strong>"<?= htmlspecialchars($termino) ?>"</strong>.
                    <?php else: ?>
                        tu búsqueda.
                    <?php endif; ?>
                    <br>Intenta con otro término o consulta las búsquedas populares.
                </p>
                <a href="/" class="bk-empty-btn">
                    <i class="bi bi-arrow-left"></i> Volver al inicio
                </a>
            </div>

        <?php else: ?>

            <!--  Grid de resultados  -->
            <div class="bk-grid" id="resultsGrid">
                <?php foreach ($productos as $p): ?>

                    <div class="bk-card">

                        <!-- Imagen + overlays -->
                        <div class="bk-card-img-wrap">
                            <?php if (!empty($p['imagen_url'])): ?>
                                <img src="<?= htmlspecialchars($p['imagen_url']) ?>"
                                    alt="<?= htmlspecialchars($p['nombre']) ?>"
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

                            <!-- Badge categoría -->
                            <?php if (!empty($p['categoria'])): ?>
                                <span class="bk-cat-badge">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                    <?= htmlspecialchars($p['categoria']) ?>
                                </span>
                            <?php endif; ?>

                            <!-- Badge proximidad (solo si hay distancia calculada < 5 km) -->
                            <?php
                            $distKm = isset($p['distancia_km_minima']) && $p['distancia_km_minima'] !== null
                                ? (float)$p['distancia_km_minima']
                                : null;
                            ?>
                            <?php if ($distKm !== null && $distKm < 5): ?>
                                <span class="bk-cerca-badge">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <?= $distKm < 1 ? 'Muy cerca' : round($distKm, 1) . ' km' ?>
                                </span>
                            <?php endif; ?>

                            <!-- Botón favorito -->
                            <?php $esFav = in_array((int)$p['id'], $favoritos_ids ?? []); ?>
                            <button type="button" class="bk-fav-btn btn-fav"
                                data-id="<?= (int)$p['id'] ?>"
                                aria-label="<?= $esFav ? 'Quitar de favoritos' : 'Añadir a favoritos' ?>">
                                <i class="bi <?= $esFav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                            </button>
                        </div><!-- /bk-card-img-wrap -->

                        <!-- Contenido -->
                        <div class="bk-card-body">
                            <h3 class="bk-card-name"><?= htmlspecialchars($p['nombre']) ?></h3>

                            <!-- Precios -->
                            <?php if ($p['precio_minimo'] !== null): ?>
                                <div class="bk-price-row">
                                    <span class="bk-price-desde">
                                        desde <?= number_format((float)$p['precio_minimo'], 2, ',', '.') ?> €
                                    </span>
                                    <?php if ((float)$p['precio_maximo'] > (float)$p['precio_minimo']): ?>
                                        <span class="bk-price-hasta">
                                            hasta <?= number_format((float)$p['precio_maximo'], 2, ',', '.') ?> €
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="bk-availability">
                                    <i class="bi bi-shop"></i>
                                    Disponible en <?= (int)$p['num_tiendas'] ?> tienda<?= (int)$p['num_tiendas'] !== 1 ? 's' : '' ?>
                                </div>
                            <?php else: ?>
                                <div class="bk-no-price">Sin precio registrado</div>
                            <?php endif; ?>

                            <!-- CTA -->
                            <a href="/producto/<?= (int)$p['id'] ?>" class="btn-compare">
                                <i class="bi bi-bar-chart"></i> Ver comparativa
                            </a>

                        </div><!-- /bk-card-body -->

                    </div><!-- /bk-card -->

                <?php endforeach; ?>
            </div><!-- /bk-grid -->

        <?php endif; ?>

        <!--  Footer features  -->
        <div class="pd-footer-features">
            <div class="pd-ff-item">
                <div class="pd-ff-icon"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="pd-ff-title">Mejores precios</div>
                    <div class="pd-ff-sub">Comparamos en tiempo real</div>
                </div>
            </div>
            <div class="pd-ff-item">
                <div class="pd-ff-icon"><i class="bi bi-shop-window"></i></div>
                <div>
                    <div class="pd-ff-title">Tiendas verificadas</div>
                    <div class="pd-ff-sub">Comercios de confianza</div>
                </div>
            </div>
            <div class="pd-ff-item">
                <div class="pd-ff-icon"><i class="bi bi-lightning-charge"></i></div>
                <div>
                    <div class="pd-ff-title">Información actualizada</div>
                    <div class="pd-ff-sub">Precios y stock al instante</div>
                </div>
            </div>
            <div class="pd-ff-item">
                <div class="pd-ff-icon"><i class="bi bi-lock"></i></div>
                <div>
                    <div class="pd-ff-title">Compra segura</div>
                    <div class="pd-ff-sub">Tus datos siempre protegidos</div>
                </div>
            </div>
        </div>

    </div><!-- /bk-container -->
</div><!-- /bk-page -->