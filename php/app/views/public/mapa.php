<!-- Vista: public/mapa.php — Pixel-perfect redesign -->
<?php
/* Calcular stats para el overlay inferior */
$totalTiendas = count($tiendas);
$precioGlobal = null;
foreach ($tiendas as $t) {
    $p = isset($t['precio_minimo']) ? (float)$t['precio_minimo'] : null;
    if ($p !== null && ($precioGlobal === null || $p < $precioGlobal)) {
        $precioGlobal = $p;
    }
}
?>

<div class="mp-page">

    <!--  HEADER  -->
    <div class="mp-header">
        <div class="mp-header-left">
            <div class="mp-header-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <h1 class="mp-title">Mapa de Tiendas</h1>
                <p class="mp-subtitle">Tiendas cerca de ti que participan en Shoppeo.</p>
            </div>
        </div>
        <button id="btnMiUbicacion" class="mp-location-btn">
            <i class="bi bi-crosshair"></i> Mi ubicación
        </button>
    </div>

    <!--  LAYOUT PRINCIPAL  -->
    <div class="mp-layout">

        <!-- ── Panel izquierdo: lista ── -->
        <div class="mp-sidebar">
            <!-- Header de la lista -->
            <div class="mp-sidebar-head">
                <i class="bi bi-list-ul" style="color:#7c3aed;font-size:18px;"></i>
                <span><?= $totalTiendas ?> tiendas</span>
            </div>

            <!-- Lista de tiendas -->
            <div class="mp-store-list" id="listaTiendas">
                <?php foreach ($tiendas as $t): ?>
                    <?php
                    $n = (int)$t['num_productos'];
                    $badgeClass = $n >= 4 ? 'mp-badge-purple' : ($n >= 2 ? 'mp-badge-amber' : 'mp-badge-green');
                    ?>
                    <div class="mp-store-card tienda-item"
                        data-id="<?= (int)$t['id'] ?>"
                        data-lat="<?= (float)$t['latitud'] ?>"
                        data-lng="<?= (float)$t['longitud'] ?>"
                        role="button" tabindex="0">
                        <div class="mp-store-top">
                            <div class="mp-store-icon-wrap">
                                <i class="bi bi-shop"></i>
                            </div>
                            <div class="mp-store-info">
                                <div class="mp-store-name"><?= htmlspecialchars($t['nombre']) ?></div>
                                <div class="mp-store-addr">
                                    <i class="bi bi-geo-alt"></i>
                                    <?= htmlspecialchars($t['direccion'] ?? '') ?>
                                </div>
                                <?php if ($t['precio_minimo']): ?>
                                    <div class="mp-store-price">
                                        Desde <?= number_format((float)$t['precio_minimo'], 2, ',', '.') ?> €
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="mp-badge <?= $badgeClass ?>"><?= $n ?> prod.</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Botón pie de lista -->
            <div class="mp-sidebar-foot">
                <button class="mp-all-btn" onclick="location.href='/buscar'">
                    Ver todas las tiendas
                </button>
            </div>
        </div>

        <!-- ── Mapa ── -->
        <div class="mp-map-wrap">
            <div id="mainMap" class="mp-map"></div>

            <!-- Leyenda -->
            <div class="mp-legend">
                <span class="mp-legend-dot" style="background:#22c55e;"></span> 1 producto
                <span class="mp-legend-dot" style="background:#f59e0b;margin-left:10px;"></span> 2-3 productos
                <span class="mp-legend-dot" style="background:#7c3aed;margin-left:10px;"></span> 4+ productos
            </div>

            <!-- Stats overlay (abajo del mapa) -->
            <div class="mp-stats">
                <div class="mp-stat">
                    <div class="mp-stat-icon mp-stat-icon-purple">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <div>
                        <div class="mp-stat-val"><?= $totalTiendas ?></div>
                        <div class="mp-stat-label">Tiendas activas</div>
                    </div>
                </div>
                <div class="mp-stat-divider"></div>
                <div class="mp-stat">
                    <div class="mp-stat-icon mp-stat-icon-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="mp-stat-val">
                            <?= $precioGlobal !== null
                                ? 'Desde ' . number_format($precioGlobal, 2, ',', '.') . ' €'
                                : '—' ?>
                        </div>
                        <div class="mp-stat-label">Mejores precios</div>
                    </div>
                </div>
                <div class="mp-stat-divider"></div>
                <div class="mp-stat">
                    <div class="mp-stat-icon mp-stat-icon-blue">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <div class="mp-stat-val ciudad">Detectando ubicación…</div>
                        <div class="mp-stat-label">Zona seleccionada</div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /mp-layout -->
</div><!-- /mp-page -->

<script>
    (function() {
        /* ── Mostrar ciudad cacheada de inmediato ── */
        var ciudadCacheada = localStorage.getItem('userCiudad');
        if (ciudadCacheada) {
            document.querySelectorAll('.ciudad').forEach(function(el) {
                el.textContent = ciudadCacheada;
            });
        }

        /* ── Mapa Leaflet ── */
        var cachedLat = parseFloat(localStorage.getItem('userLat'));
        var cachedLon = parseFloat(localStorage.getItem('userLon'));
        var latInicio = window.userLat || (cachedLat || 40.3456);
        var lonInicio = window.userLon || (cachedLon || -1.1065);
        var usandoUbicacion = !!(window.userLat || cachedLat);

        var mapa = L.map('mainMap', {
            zoomControl: true
        }).setView([latInicio, lonInicio], usandoUbicacion ? 14 : 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(mapa);

        /* ── Color por número de productos ── */
        function colorTienda(n) {
            if (n >= 4) return '#7c3aed';
            if (n >= 2) return '#f59e0b';
            return '#22c55e';
        }

        /* ── Marcador custom (pin morado con icono tienda) ── */
        function iconoTienda(color) {
            return L.divIcon({
                className: 'mapa-marker-wrap',
                html: '<div class="mapa-pin" style="background:' + color + ';">' +
                    '<i class="bi bi-shop"></i>' +
                    '</div>',
                iconSize: [36, 44],
                iconAnchor: [18, 44],
                popupAnchor: [0, -48]
            });
        }

        /* ── Marcador usuario ── */
        var iconoUsuario = L.divIcon({
            className: '',
            html: '<div style="width:16px;height:16px;background:#7c3aed;border-radius:50%;border:3px solid white;box-shadow:0 0 0 4px rgba(124,58,237,0.25);"></div>',
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });

        var marcadorUsuario = null;

        function ponerMarcadorUsuario(lat, lon) {
            if (marcadorUsuario) {
                marcadorUsuario.setLatLng([lat, lon]);
            } else {
                marcadorUsuario = L.marker([lat, lon], {
                        icon: iconoUsuario,
                        zIndexOffset: 999
                    })
                    .addTo(mapa)
                    .bindPopup('<strong>Tu ubicación</strong>');
            }
        }

        if (window.userLat && window.userLon) {
            ponerMarcadorUsuario(window.userLat, window.userLon);
            ordenarListaPorCercania(window.userLat, window.userLon);
        } else if (cachedLat && cachedLon) {
            ponerMarcadorUsuario(cachedLat, cachedLon);
            ordenarListaPorCercania(cachedLat, cachedLon);
        }

        /* ── Haversine (km entre dos coords) ── */
        function haversine(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        /* ── Ordenar lista por proximidad + añadir badge de distancia ── */
        function ordenarListaPorCercania(userLat, userLon) {
            var lista = document.getElementById('listaTiendas');
            var cards = Array.from(lista.querySelectorAll('.tienda-item'));

            cards.sort(function(a, b) {
                var dA = haversine(userLat, userLon, parseFloat(a.dataset.lat), parseFloat(a.dataset.lng));
                var dB = haversine(userLat, userLon, parseFloat(b.dataset.lat), parseFloat(b.dataset.lng));
                return dA - dB;
            });

            cards.forEach(function(card) {
                var dist = haversine(userLat, userLon, parseFloat(card.dataset.lat), parseFloat(card.dataset.lng));
                var distStr = dist < 1 ?
                    Math.round(dist * 1000) + ' m' :
                    dist.toFixed(1) + ' km';

                /* Insertar o actualizar badge de distancia */
                var badge = card.querySelector('.mp-dist-badge');
                if (!badge) {
                    badge = document.createElement('div');
                    badge.className = 'mp-dist-badge';
                    card.querySelector('.mp-store-top').appendChild(badge);
                }
                badge.innerHTML = '<i class="bi bi-geo-alt-fill"></i> ' + distStr;

                lista.appendChild(card); /* reordenar en el DOM */
            });

            /* Actualizar contador con ciudad */
            var head = document.querySelector('.mp-sidebar-head span');
            if (head) {
                var ciudad = localStorage.getItem('userCiudad') || 'tu zona';
                head.textContent = cards.length + ' tiendas · más cercanas primero';
            }
        }

        /* ── Geolocalización propia del mapa (no depende de app.js) ── */
        function aplicarUbicacion(lat, lon) {
            window.userLat = lat;
            window.userLon = lon;
            localStorage.setItem('userLat', lat);
            localStorage.setItem('userLon', lon);
            ponerMarcadorUsuario(lat, lon);
            mapa.flyTo([lat, lon], 15, {
                duration: 1.2
            });
            ordenarListaPorCercania(lat, lon);

            fetch('/api/location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        lat: lat,
                        lon: lon
                    })
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    var ciudad = data.city || 'Tu ubicación';
                    localStorage.setItem('userCiudad', ciudad);
                    document.querySelectorAll('.ciudad').forEach(function(el) {
                        el.textContent = ciudad;
                    });
                })
                .catch(function() {
                    document.querySelectorAll('.ciudad').forEach(function(el) {
                        el.textContent = 'Tu ubicación';
                    });
                });
        }

        if (window.userLat && window.userLon) {
            aplicarUbicacion(window.userLat, window.userLon);
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    aplicarUbicacion(pos.coords.latitude, pos.coords.longitude);
                },
                function() {
                    /* permiso denegado: queda en ubicación cacheada o por defecto */ }
            );
        }

        window.addEventListener('ubicacionLista', function(e) {
            ponerMarcadorUsuario(e.detail.lat, e.detail.lon);
        });

        /* ── Botón Mi ubicación ── */
        document.getElementById('btnMiUbicacion').addEventListener('click', function() {
            if (window.userLat && window.userLon) {
                mapa.flyTo([window.userLat, window.userLon], 15, {
                    duration: 1
                });
                if (marcadorUsuario) marcadorUsuario.openPopup();
            } else {
                navigator.geolocation && navigator.geolocation.getCurrentPosition(function(pos) {
                    window.userLat = pos.coords.latitude;
                    window.userLon = pos.coords.longitude;
                    ponerMarcadorUsuario(window.userLat, window.userLon);
                    mapa.flyTo([window.userLat, window.userLon], 15, {
                        duration: 1
                    });
                });
            }
        });

        /* ── Marcadores de tiendas ── */
        var tiendas = <?= json_encode($tiendas, JSON_UNESCAPED_UNICODE) ?>;
        var marcadores = {};

        tiendas.forEach(function(t) {
            var n = parseInt(t.num_productos) || 0;
            var color = colorTienda(n);
            var marker = L.marker([parseFloat(t.latitud), parseFloat(t.longitud)], {
                icon: iconoTienda(color)
            });

            var popup =
                '<div style="min-width:190px;font-family:inherit;">' +
                '<div style="font-weight:700;font-size:14px;margin-bottom:4px;">' +
                '<i class="bi bi-shop me-1" style="color:#7c3aed;"></i>' + t.nombre + '</div>' +
                '<div style="font-size:12px;color:#6b7280;margin-bottom:6px;">' + (t.direccion || '') + '</div>' +
                (t.telefono ? '<div style="font-size:12px;color:#6b7280;"><i class="bi bi-telephone me-1"></i>' + t.telefono + '</div>' : '') +
                '<hr style="margin:8px 0;border-color:#f3f4f6;">' +
                '<span style="background:#ede9fe;color:#6d28d9;font-size:12px;font-weight:600;padding:3px 10px;border-radius:999px;">' + n + ' productos</span>' +
                (t.precio_minimo ? ' <span style="background:#dcfce7;color:#16a34a;font-size:12px;font-weight:600;padding:3px 10px;border-radius:999px;">Desde ' + parseFloat(t.precio_minimo).toFixed(2) + ' €</span>' : '') +
                (t.web ? '<div style="margin-top:8px;"><a href="' + t.web + '" target="_blank" style="font-size:12px;color:#7c3aed;"><i class="bi bi-box-arrow-up-right me-1"></i>Visitar web</a></div>' : '') +
                '</div>';

            marker.bindPopup(popup);
            marker.addTo(mapa);
            marcadores[t.id] = marker;
        });

        /* ── Click en lista → flyTo ── */
        document.querySelectorAll('.tienda-item').forEach(function(card) {
            card.addEventListener('click', function() {
                var lat = parseFloat(this.dataset.lat);
                var lng = parseFloat(this.dataset.lng);
                var id = parseInt(this.dataset.id);

                mapa.flyTo([lat, lng], 16, {
                    duration: 1
                });
                if (marcadores[id]) marcadores[id].openPopup();

                document.querySelectorAll('.tienda-item').forEach(function(c) {
                    c.classList.remove('mp-active');
                });
                this.classList.add('mp-active');
            });
        });

        /* Invalidar tamaño por si el contenedor tardó en renderizar */
        setTimeout(function() {
            mapa.invalidateSize();
        }, 200);
    }());
</script>