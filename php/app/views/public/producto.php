<!-- Vista: public/producto.php — Pixel-perfect redesign -->
<?php
$precios    = $producto['precios'] ?? [];
$minPrecio  = $precios[0]['precio'] ?? null;

?>

<div class="pd-page">
    <div class="pd-container">

        <!--  Breadcrumb  -->
        <nav class="pd-breadcrumb" aria-label="breadcrumb">
            <a href="/">Inicio</a>
            <span class="pd-bc-sep">/</span>
            <a href="/buscar">Productos</a>
            <span class="pd-bc-sep">/</span>
            <span class="pd-bc-current"><?= htmlspecialchars($producto['nombre']) ?></span>
        </nav>

        <!--  Main grid  -->
        <div class="pd-grid">

            <!-- ════ COLUMNA IZQUIERDA — Producto ════ -->
            <div class="product-card">

                <!-- Badge categoría -->
                <?php if (!empty($producto['categoria'])): ?>
                    <div class="pd-cat-badge">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <?= htmlspecialchars($producto['categoria']) ?>
                    </div>
                <?php endif; ?>

                <!-- Imagen -->
                <div class="pd-img-wrap">
                    <?php if (!empty($producto['imagen_url'])): ?>
                        <img src="<?= htmlspecialchars($producto['imagen_url']) ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                            class="pd-img"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="pd-img-placeholder" style="display:none;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    <?php else: ?>
                        <div class="pd-img-placeholder">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Título y descripción -->
                <h1 class="pd-title"><?= htmlspecialchars($producto['nombre']) ?></h1>

                <?php if (!empty($producto['descripcion'])): ?>
                    <p class="pd-desc"><?= htmlspecialchars($producto['descripcion']) ?></p>
                <?php endif; ?>


                <!-- Botón guardar en lista -->
                <button type="button"
                    class="pd-fav-btn btn-fav <?= ($esFavorito ?? false) ? 'is-saved' : '' ?>"
                    data-id="<?= (int)$producto['id'] ?>">
                    <i class="bi <?= ($esFavorito ?? false) ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus-fill' ?>"></i>
                    <span class="pd-fav-label"><?= ($esFavorito ?? false) ? '¡Guardado en tu lista!' : 'Guardar en mi lista' ?></span>
                </button>

            </div><!-- /product-card -->

            <!-- ════ COLUMNA DERECHA ════ -->
            <div class="pd-right">

                <!--  Card comparativa de precios  -->
                <div class="price-card">
                    <div class="pd-card-header">
                        <div class="pd-card-title">
                            <i class="bi bi-bar-chart-line"></i>
                            Comparativa de precios
                        </div>
                        <span class="pd-rt-badge" id="rtBadge">
                            <i class="bi bi-lightning-charge-fill"></i>
                            Tiempo real activo
                            <span class="pd-rt-dot"></span>
                        </span>
                    </div>

                    <div class="pd-table-wrap">
                        <table class="pd-price-table" id="preciosTable">
                            <thead>
                                <tr>
                                    <th>Tienda</th>
                                    <th>Dirección</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Actualizado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($precios as $precio): ?>
                                    <tr id="precio-row-<?= (int)$precio['tienda_id'] ?>"
                                        class="<?= $precio['es_minimo'] ? 'pd-row-best' : '' ?>"
                                        data-tienda-id="<?= (int)$precio['tienda_id'] ?>"
                                        data-lat="<?= htmlspecialchars((string)($precio['latitud'] ?? '')) ?>"
                                        data-lng="<?= htmlspecialchars((string)($precio['longitud'] ?? '')) ?>"
                                        data-tienda="<?= htmlspecialchars($precio['tienda']) ?>">

                                        <!-- Tienda -->
                                        <td>
                                            <div class="pd-store-name">
                                                <?php if ($precio['es_minimo']): ?>
                                                    <i class="bi bi-trophy-fill pd-trophy"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($precio['tienda']) ?>
                                                <?php if (!empty($precio['web'])): ?>
                                                    <a href="<?= htmlspecialchars($precio['web']) ?>"
                                                        target="_blank" title="Visitar web">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Dirección -->
                                        <td>
                                            <div class="pd-address">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= htmlspecialchars($precio['direccion'] ?? '') ?>
                                            </div>
                                        </td>

                                        <!-- Stock -->
                                        <td>
                                            <?php if ((int)$precio['stock'] > 0): ?>
                                                <span class="pd-stock-badge"><?= (int)$precio['stock'] ?> uds.</span>
                                            <?php else: ?>
                                                <span class="pd-stock-badge pd-stock-out">Agotado</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Precio -->
                                        <td>
                                            <span class="pd-price-val" id="precio-<?= (int)$precio['tienda_id'] ?>">
                                                <?= number_format((float)$precio['precio'], 2, ',', '.') ?> €
                                            </span>
                                        </td>

                                        <!-- Actualizado -->
                                        <td>
                                            <span class="pd-updated" id="updated-<?= (int)$precio['tienda_id'] ?>">
                                                <?= date('d/m/Y H:i', strtotime($precio['actualizado_at'])) ?>
                                            </span>
                                        </td>

                                        <!-- Arrow -->
                                        <td>
                                            <i class="bi bi-chevron-right pd-row-arrow"></i>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pd-table-footer">
                    </div>
                </div><!-- /price-card -->

                <!--  Card mapa  -->
                <div class="map-card">
                    <div class="pd-card-header">
                        <div class="pd-card-title">
                            <i class="bi bi-map"></i>
                            Tiendas en el mapa
                        </div>
                        <button type="button" class="pd-location-btn" id="btnProductoUbicacion">
                            <i class="bi bi-crosshair"></i> Mi ubicación
                        </button>
                    </div>
                    <div id="productoMap" class="pd-map"></div>
                </div><!-- /map-card -->

            </div><!-- /pd-right -->

        </div><!-- /pd-grid -->

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

    </div><!-- /pd-container -->
</div><!-- /pd-page -->

<script>
    (function() {
        /*  Mapa de tiendas del producto  */
        var tiendas = <?= json_encode(array_map(fn($p) => [
                            'tienda_id' => $p['tienda_id'],
                            'tienda'    => $p['tienda'],
                            'latitud'   => $p['latitud'],
                            'longitud'  => $p['longitud'],
                            'precio'    => $p['precio'],
                            'direccion' => $p['direccion'] ?? '',
                            'es_minimo' => $p['es_minimo'],
                        ], $precios), JSON_UNESCAPED_UNICODE) ?>;

        /* Centro inicial: primera tienda con coords, o España */
        var centro = [40.3456, -1.1065];
        var zoomInicial = 6;
        for (var i = 0; i < tiendas.length; i++) {
            if (tiendas[i].latitud && tiendas[i].longitud) {
                centro = [tiendas[i].latitud, tiendas[i].longitud];
                zoomInicial = 13;
                break;
            }
        }

        var map = L.map('productoMap').setView(centro, zoomInicial);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        /* Índice de marcadores por tienda_id para poder volar a ellos */
        var markersById = {};

        tiendas.forEach(function(t) {
            if (!t.latitud || !t.longitud) return;
            var color = t.es_minimo ? '#7c3aed' : '#3b82f6';
            var radius = t.es_minimo ? 12 : 9;
            var marker = L.circleMarker([t.latitud, t.longitud], {
                radius: radius,
                fillColor: color,
                color: '#fff',
                weight: 2.5,
                fillOpacity: 0.92
            });
            marker.bindPopup(
                '<div style="min-width:160px;font-family:inherit;line-height:1.5">' +
                '<strong style="font-size:13px;">' + (t.es_minimo ? '🏆 ' : '') + t.tienda + '</strong><br>' +
                '<span style="font-size:12px;color:#6b7280;">' + (t.direccion || '') + '</span><br>' +
                '<span style="color:#7c3aed;font-weight:700;font-size:15px;">' +
                parseFloat(t.precio).toFixed(2).replace('.', ',') + ' €</span></div>'
            );
            marker.addTo(map);
            markersById[t.tienda_id] = marker;
        });

        /* Clic en fila → volar al marcador + abrir popup + resaltar fila */
        var selectedRow = null;
        document.querySelectorAll('#preciosTable tbody tr[data-tienda-id]').forEach(function(row) {
            row.addEventListener('click', function() {
                var tid = parseInt(this.dataset.tiendaId);
                var lat = parseFloat(this.dataset.lat);
                var lng = parseFloat(this.dataset.lng);

                /* Resaltar fila */
                if (selectedRow) selectedRow.classList.remove('pd-row-selected');
                this.classList.add('pd-row-selected');
                selectedRow = this;

                /* Volar al marcador */
                if (!isNaN(lat) && !isNaN(lng)) {
                    map.flyTo([lat, lng], 16, {
                        duration: 0.8
                    });
                    if (markersById[tid]) {
                        setTimeout(function() {
                            markersById[tid].openPopup();
                        }, 850);
                    }
                }
            });
        });

        /* Mi ubicación */
        document.getElementById('btnProductoUbicacion').addEventListener('click', function() {
            navigator.geolocation && navigator.geolocation.getCurrentPosition(function(pos) {
                map.flyTo([pos.coords.latitude, pos.coords.longitude], 14, {
                    duration: 1.2
                });
            });
        });

        setTimeout(function() {
            map.invalidateSize();
        }, 200);
    }());

    /*  Suscripción WebSocket al producto  */
    document.addEventListener('DOMContentLoaded', function() {
        var productoId = <?= (int)$producto['id'] ?>;

        if (typeof io !== 'undefined') {
            var socket = io('http://localhost:3000');

            socket.on('connect', function() {
                socket.emit('subscribe_product', {
                    producto_id: productoId
                });
            });

            socket.on('price_updated', function(data) {
                if (data.producto_id !== productoId) return;

                var precioEl = document.getElementById('precio-' + data.tienda_id);
                var updatedEl = document.getElementById('updated-' + data.tienda_id);
                var row = document.getElementById('precio-row-' + data.tienda_id);

                if (precioEl) {
                    row.classList.add('price-flash');
                    setTimeout(function() {
                        row.classList.remove('price-flash');
                    }, 1500);

                    precioEl.textContent = parseFloat(data.precio).toFixed(2).replace('.', ',') + ' €';
                    if (updatedEl) updatedEl.textContent = new Date().toLocaleString('es-ES');

                    showPriceToast(data);
                }
            });
        }
    });

    function showPriceToast(data) {
        var dir = data.precio < data.precio_anterior ? '⬇️ bajó' : '⬆️ subió';
        var diff = Math.abs(data.precio - data.precio_anterior).toFixed(2);
        var msg = data.tienda + ': precio ' + dir + ' ' + diff + ' € → ' + parseFloat(data.precio).toFixed(2) + ' €';

        var toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 m-3 alert alert-info shadow fade show';
        toast.style.zIndex = 9999;
        toast.innerHTML = '<i class="bi bi-bell-fill me-2"></i>' + msg +
            '<button class="btn-close ms-2" onclick="this.parentElement.remove()"></button>';
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.remove();
        }, 5000);
    }
</script>