<!-- Vista: public/inicio.php -->

<!--  HERO  -->
<section class="hero-new py-5">
    <div class="container">
        <div class="row align-items-center g-4">

            <!-- Columna izquierda: texto + buscador -->
            <div class="col-lg-5">
                <h1 class="hero-title mb-0">Compara precios.</h1>
                <h1 class="hero-title hero-gradient mb-3">Ahorra más.</h1>
                <p class="hero-desc mb-4">
                    Encuentra el mejor precio en productos de electrónica,
                    supermercado, belleza, hogar y más. Todo en un solo lugar.
                </p>

                <!-- Buscador -->
                <form action="/buscar" method="GET" class="hero-search-form mb-3" autocomplete="off">
                    <div class="hero-search-wrap">
                        <input type="search" class="hero-search-input" name="q" id="heroSearch"
                            placeholder="¿Qué buscas? Ej: iPhone, café, laptop, crema hidratante...">
                        <button type="submit" class="hero-search-btn">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                    </div>
                    <div id="heroSuggestions" class="hero-suggestions d-none"></div>
                </form>

                <!-- Tags populares (dinámicos desde Redis, fallback estático) -->
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="text-muted small">Búsquedas populares:</span>
                    <?php
                    $tagClases = ['tag-dark', 'tag-amber', 'tag-blue', 'tag-green'];
                    foreach (($tags_populares ?? []) as $i => $tag):
                        $clase = $tagClases[$i % count($tagClases)];
                    ?>
                        <a href="/buscar?q=<?= urlencode($tag) ?>"
                            class="popular-tag <?= $clase ?>">
                            <?= htmlspecialchars($tag) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- CTA ofertas -->
                <div class="cta-ofertas-wrapper">
                    <a href="#chollos" class="cta-ofertas-big">
                        🔥 Accede a las mejores ofertas cerca de ti
                    </a>
                </div>
            </div>

            <!-- Columna derecha: grid de categorías -->
            <div class="col-lg-7">
                <!-- Fila 1: 2 cards grandes -->
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <a href="/buscar?categoria=Electr%C3%B3nica" class="cat-hero-card cat-bg-purple" style="height:210px;">
                            <img src="/assets/electronica.png" alt="Electrónica" class="cat-card-img"
                                onerror="this.style.display='none'">
                            <div class="cat-hero-badge">
                                <i class="bi bi-display"></i>
                                <span>Electrónica</span>
                                <i class="bi bi-chevron-right cat-chevron"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/buscar?categoria=Alimentaci%C3%B3n" class="cat-hero-card cat-bg-green" style="height:210px;">
                            <img src="/assets/supermercado.png" alt="Alimentación" class="cat-card-img"
                                onerror="this.style.display='none'">
                            <div class="cat-hero-badge">
                                <i class="bi bi-basket3"></i>
                                <span>Supermercado</span>
                                <i class="bi bi-chevron-right cat-chevron"></i>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Fila 2: 3 cards pequeñas -->
                <div class="row g-2">
                    <div class="col-4">
                        <a href="/buscar?categoria=Electrodom%C3%A9sticos" class="cat-hero-card cat-bg-amber" style="height:172px;">
                            <img src="/assets/belleza.png" alt="Electrodomésticos" class="cat-card-img"
                                onerror="this.style.display='none'">
                            <div class="cat-hero-badge">
                                <i class="bi bi-droplet-half"></i>
                                <span>Belleza y Cuidado</span>
                                <i class="bi bi-chevron-right cat-chevron"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="/buscar?categoria=Hogar" class="cat-hero-card cat-bg-teal" style="height:172px;">
                            <img src="/assets/hogar.png" alt="Hogar" class="cat-card-img"
                                onerror="this.style.display='none'">
                            <div class="cat-hero-badge">
                                <i class="bi bi-house-heart"></i>
                                <span>Hogar y Cocina</span>
                                <i class="bi bi-chevron-right cat-chevron"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="/buscar?categoria=Deportes" class="cat-hero-card cat-bg-rose" style="height:172px;">
                            <img src="/assets/salud.png" alt="Deportes" class="cat-card-img"
                                onerror="this.style.display='none'">
                            <div class="cat-hero-badge">
                                <i class="bi bi-clipboard2-pulse"></i>
                                <span>Salud y Bienestar</span>
                                <i class="bi bi-chevron-right cat-chevron"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div><!-- /row hero -->
    </div><!-- /container -->
</section>
<!--  4 FEATURES  -->
<section class="py-4 border-top border-bottom">
    <div class="container">
        <div class="row g-3 align-items-center text-center text-md-start">

            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div class="feature-icon-wrap feature-icon-purple flex-shrink-0">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:14px;color:#111827;">Compara en segundos</div>
                        <div class="text-muted" style="font-size:12px;">Miles de productos, todos los precios.</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div class="feature-icon-wrap feature-icon-blue flex-shrink-0">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:14px;color:#111827;">Apoya al comercio local</div>
                        <div class="text-muted" style="font-size:12px;">Encuentra y apoya negocios cercanos.</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div class="feature-icon-wrap feature-icon-yellow flex-shrink-0">
                        <i class="bi bi-lightning-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:14px;color:#111827;">Precios en tiempo real</div>
                        <div class="text-muted" style="font-size:12px;">Información actualizada al instante.</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                    <div class="feature-icon-wrap feature-icon-green flex-shrink-0">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:14px;color:#111827;">Seguro y confiable</div>
                        <div class="text-muted" style="font-size:12px;">Tus datos siempre protegidos.</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!--  CHOLLOS  -->
<?php if (!empty($chollos)): ?>
    <section id="chollos" class="chollos-section container-xl my-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="font-size:22px;">
                    🔥 Chollos cerca de ti
                </h3>
                <small class="text-muted">Los mayores ahorros detectados ahora mismo</small>
            </div>
            <a href="/buscar" class="text-decoration-none fw-semibold" style="color:#7c3aed;font-size:14px;">
                Ver todos <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($chollos as $c):
                $actual   = (float)$c['precio_actual'];
                $maximo   = (float)$c['precio_maximo'];
                $ahorro   = $maximo - $actual;
                $pct      = $maximo > 0 ? round($ahorro / $maximo * 100) : 0;
            ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="chollo-card"
                        data-lat="<?= htmlspecialchars((string)($c['latitud'] ?? '')) ?>"
                        data-lng="<?= htmlspecialchars((string)($c['longitud'] ?? '')) ?>">

                        <!-- Imagen -->
                        <div class="chollo-img">
                            <?php if (!empty($c['imagen_url'])): ?>
                                <img src="<?= htmlspecialchars($c['imagen_url']) ?>"
                                    alt="<?= htmlspecialchars($c['nombre']) ?>"
                                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div class="chollo-img-ph" style="display:none;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            <?php else: ?>
                                <div class="chollo-img-ph">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            <?php endif; ?>

                            <?php if ($pct > 0): ?>
                                <span class="badge-descuento">-<?= $pct ?>%</span>
                            <?php endif; ?>

                            <?php if (!empty($c['categoria'])): ?>
                                <span class="chollo-cat-badge">
                                    <?= htmlspecialchars($c['categoria']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Cuerpo -->
                        <div class="chollo-body">
                            <h6 class="chollo-nombre"><?= htmlspecialchars($c['nombre']) ?></h6>
                            <small class="chollo-tienda">
                                <i class="bi bi-shop me-1"></i><?= htmlspecialchars($c['tienda']) ?>
                            </small>

                            <div class="chollo-precio mt-2">
                                <span class="chollo-actual"><?= number_format($actual, 2, ',', '.') ?> €</span>
                                <?php if ($maximo > $actual): ?>
                                    <span class="chollo-old"><?= number_format($maximo, 2, ',', '.') ?> €</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($ahorro > 0): ?>
                                <div class="chollo-ahorro">
                                    <i class="bi bi-piggy-bank me-1"></i>
                                    Ahorras <?= number_format($ahorro, 2, ',', '.') ?> €
                                </div>
                            <?php endif; ?>

                            <a href="/producto/<?= (int)$c['producto_id'] ?>" class="btn-chollo mt-3">
                                <i class="bi bi-lightning-fill me-1"></i>Ver oferta
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </section>
<?php endif; ?>



<!--  BANNERS  -->
<section class="pb-5">
    <div class="container">
        <div class="row g-3">

            <!-- Banner morado (más ancho) -->
            <div class="col-12 col-md-6">
                <div class="banner banner-purple h-100">
                    <div class="banner-icon-wrap">
                        <i class="bi bi-lightning-fill"></i>
                    </div>
                    <div class="banner-text">
                        <strong>Ofertas exclusivas cada día</strong>
                        <span>Descubre descuentos increíbles</span>
                    </div>
                    <div class="banner-countdown ms-auto">
                        <span class="countdown-label">Termina en</span>
                        <div class="countdown-timer">
                            <div class="countdown-unit"><span id="cd-hrs">03</span><small>HRS</small></div>
                            <span class="countdown-sep">:</span>
                            <div class="countdown-unit"><span id="cd-min">45</span><small>MIN</small></div>
                            <span class="countdown-sep">:</span>
                            <div class="countdown-unit"><span id="cd-sec">20</span><small>SEG</small></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner rosa -->
            <div class="col-6 col-md-3">
                <div class="banner banner-pink h-100">
                    <div class="banner-icon-wrap">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                    <div class="banner-text">
                        <strong>Nuevas ofertas</strong>
                        <span>Actualizadas cada hora</span>
                    </div>
                </div>
            </div>

            <!-- Banner naranja -->
            <div class="col-6 col-md-3">
                <div class="banner banner-orange h-100" style="position:relative;overflow:hidden;">
                    <div class="banner-icon-wrap">
                        <i class="bi bi-gift-fill"></i>
                    </div>
                    <div class="banner-text">
                        <strong>No te las pierdas</strong>
                        <span>Ahorra más, todos los días</span>
                    </div>
                    <span style="position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:28px;opacity:0.5;">🎊</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!--  ACTIVIDAD WEBSOCKET  -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="font-size:22px;">
                    <i class="bi bi-activity text-danger me-2"></i>Actividad reciente
                </h2>
                <p class="text-muted mb-0" style="font-size:14px;">Últimas actualizaciones de precios en tiempo real.</p>
            </div>
            <span class="badge bg-success fs-6" id="wsOnlineBadge">
                <i class="bi bi-wifi"></i> Conectando…
            </span>
        </div>
        <div id="activityFeed" class="list-group">
            <div class="list-group-item text-muted text-center py-4" id="noActivityMsg">
                <i class="bi bi-clock-history fs-3 d-block mb-2 opacity-50"></i>
                Esperando actualizaciones de precios…
            </div>
        </div>
    </div>
</section>

<script>
    /* Hero autocomplete — 1 carácter, debounce 150ms */
    (function() {
        var input = document.getElementById('heroSearch');
        var box = document.getElementById('heroSuggestions');
        if (!input) return;
        var timer;

        input.addEventListener('input', function() {
            clearTimeout(timer);
            var q = this.value.trim();
            if (q.length < 1) {
                box.classList.add('d-none');
                box.innerHTML = '';
                return;
            }
            timer = setTimeout(function() {
                fetchHero(q);
            }, 150);
        });

        async function fetchHero(q) {
            try {
                var res = await fetch('/api/productos?q=' + encodeURIComponent(q));
                var json = await res.json();
                box.innerHTML = '';
                if (json.data && json.data.length) {
                    json.data.slice(0, 6).forEach(function(p) {
                        var a = document.createElement('a');
                        a.href = '/producto/' + p.id;
                        a.className = 'hero-sug-item';
                        a.innerHTML =
                            '<span><i class="bi bi-box-seam me-2" style="color:#7c3aed"></i>' +
                            '<strong>' + esc(p.nombre) + '</strong>' +
                            (p.categoria ? '<small class="text-muted ms-2">' + esc(p.categoria) + '</small>' : '') +
                            '</span>' +
                            (p.precio_minimo ? '<span class="sug-price">desde ' + parseFloat(p.precio_minimo).toFixed(2) + ' €</span>' : '');
                        box.appendChild(a);
                    });
                    box.classList.remove('d-none');
                } else {
                    box.classList.add('d-none');
                }
            } catch (e) {
                console.error(e);
            }
        }

        document.addEventListener('click', function(e) {
            if (!box.contains(e.target) && e.target !== input) box.classList.add('d-none');
        });

        function esc(s) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(s || ''));
            return d.innerHTML;
        }
    }());

    /* Countdown hasta medianoche */
    (function() {
        var hEl = document.getElementById('cd-hrs');
        var mEl = document.getElementById('cd-min');
        var sEl = document.getElementById('cd-sec');
        if (!hEl) return;

        function tick() {
            var now = new Date();
            var end = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
            var diff = Math.max(0, Math.floor((end - now) / 1000));
            hEl.textContent = String(Math.floor(diff / 3600)).padStart(2, '0');
            mEl.textContent = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            sEl.textContent = String(diff % 60).padStart(2, '0');
        }
        tick();
        setInterval(tick, 1000);
    }());

    /* Chollos locales: carga solo los de la localidad del usuario vía API */
    (function() {
        function esc(s) {
            return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function fmt(n) {
            return parseFloat(n).toFixed(2).replace('.', ',');
        }

        function buildCard(c) {
            var actual = parseFloat(c.precio_actual);
            var maximo = parseFloat(c.precio_maximo);
            var ahorro = maximo - actual;
            var pct = maximo > 0 ? Math.round(ahorro / maximo * 100) : 0;
            var imgHtml = c.imagen_url ?
                '<img src="' + esc(c.imagen_url) + '" alt="' + esc(c.nombre) + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">' +
                '<div class="chollo-img-ph" style="display:none;"><i class="bi bi-box-seam"></i></div>' :
                '<div class="chollo-img-ph"><i class="bi bi-box-seam"></i></div>';

            return '<div class="col-12 col-sm-6 col-lg-4">' +
                '<div class="chollo-card" data-lat="' + esc(c.latitud) + '" data-lng="' + esc(c.longitud) + '">' +
                '<div class="chollo-img">' +
                imgHtml +
                (pct > 0 ? '<span class="badge-descuento">-' + pct + '%</span>' : '') +
                (c.categoria ? '<span class="chollo-cat-badge">' + esc(c.categoria) + '</span>' : '') +
                '</div>' +
                '<div class="chollo-body">' +
                '<h6 class="chollo-nombre">' + esc(c.nombre) + '</h6>' +
                '<small class="chollo-tienda"><i class="bi bi-shop me-1"></i>' + esc(c.tienda) + '</small>' +
                '<div class="chollo-precio mt-2">' +
                '<span class="chollo-actual">' + fmt(actual) + ' €</span>' +
                (maximo > actual ? '<span class="chollo-old">' + fmt(maximo) + ' €</span>' : '') +
                '</div>' +
                (ahorro > 0 ? '<div class="chollo-ahorro"><i class="bi bi-piggy-bank me-1"></i>Ahorras ' + fmt(ahorro) + ' €</div>' : '') +
                '<a href="/producto/' + parseInt(c.producto_id) + '" class="btn-chollo mt-3">' +
                '<i class="bi bi-lightning-fill me-1"></i>Ver oferta</a>' +
                '</div></div></div>';
        }

        function cargarCholloscercanos(lat, lon) {
            var grid = document.querySelector('#chollos .row');
            var titulo = document.querySelector('#chollos h3');
            var subtitulo = document.querySelector('#chollos .text-muted');
            var ciudad = localStorage.getItem('userCiudad') || 'tu zona';
            if (!grid) return;

            fetch('/api/chollos?lat=' + lat + '&lng=' + lon + '&radius=25&limit=6')
                .then(function(r) {
                    return r.json();
                })
                .then(function(json) {
                    if (!json.ok || !json.data || !json.data.length) {
                        /* Sin chollos cerca: mostrar aviso pero dejar los globales */
                        if (subtitulo) subtitulo.textContent = 'No encontramos chollos en ' + ciudad + ' — mostrando los mejores de España';
                        return;
                    }
                    /* Reemplazar grid con chollos locales */
                    grid.innerHTML = json.data.map(buildCard).join('');
                    if (titulo) titulo.innerHTML = '🔥 Chollos cerca de ' + esc(ciudad);
                    if (subtitulo) subtitulo.textContent = 'Las mejores ofertas ahora mismo en ' + ciudad + ' y alrededores';
                })
                .catch(function() {
                    /* red caída: dejamos los globales */ });
        }

        var lat = parseFloat(localStorage.getItem('userLat'));
        var lon = parseFloat(localStorage.getItem('userLon'));
        if (lat && lon) cargarCholloscercanos(lat, lon);

        window.addEventListener('ubicacionLista', function(e) {
            cargarCholloscercanos(e.detail.lat, e.detail.lon);
        });
    }());
</script>