<!-- Vista: public/descuentos.php -->
<?php
// Colores por categoría
$catColors = [
    'Electrónica'      => ['from' => '#1d4ed8', 'to' => '#2563eb', 'light' => '#dbeafe', 'text' => '#1e40af'],
    'Electrodomésticos'=> ['from' => '#4338ca', 'to' => '#4f46e5', 'light' => '#e0e7ff', 'text' => '#3730a3'],
    'Alimentación'     => ['from' => '#15803d', 'to' => '#16a34a', 'light' => '#dcfce7', 'text' => '#166534'],
    'Hogar'            => ['from' => '#6d28d9', 'to' => '#7c3aed', 'light' => '#ede9fe', 'text' => '#5b21b6'],
    'Deportes'         => ['from' => '#b45309', 'to' => '#d97706', 'light' => '#fef3c7', 'text' => '#92400e'],
];
$defaultColor = ['from' => '#374151', 'to' => '#6b7280', 'light' => '#f3f4f6', 'text' => '#1f2937'];

function catColor(array $catColors, ?string $cat): array {
    return $catColors[$cat ?? ''] ?? $catColors['default'] ?? ['from'=>'#374151','to'=>'#6b7280','light'=>'#f3f4f6','text'=>'#1f2937'];
}
?>

<!-- ══ HERO ══ -->
<div class="dc-hero">
    <div class="container-xl">
        <div class="dc-hero-inner">
            <div>
                <div class="dc-hero-label">
                    <i class="bi bi-percent"></i> Descuentos exclusivos
                </div>
                <h1 class="dc-hero-title">Mis Descuentos</h1>
                <p class="dc-hero-sub">
                    Copia el código y preséntalo en la tienda asociada para aplicar el descuento.
                    Válido solo en tiendas adheridas a shoppeo.
                </p>
                <div class="dc-hero-stats">
                    <div class="dc-stat">
                        <span class="dc-stat-num"><?= count($descuentos) ?></span>
                        <span class="dc-stat-label">disponibles</span>
                    </div>
                    <div class="dc-stat-sep"></div>
                    <div class="dc-stat">
                        <span class="dc-stat-num"><?= count(array_filter($descuentos, fn($d) => $d['tipo'] === 'porcentaje')) ?></span>
                        <span class="dc-stat-label">en porcentaje</span>
                    </div>
                    <div class="dc-stat-sep"></div>
                    <div class="dc-stat">
                        <span class="dc-stat-num"><?= count(array_filter($descuentos, fn($d) => $d['tipo'] === 'fijo')) ?></span>
                        <span class="dc-stat-label">importe fijo</span>
                    </div>
                </div>
            </div>
            <div class="dc-hero-icon-wrap">
                <div class="dc-hero-icon"><i class="bi bi-tags-fill"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- ══ FILTROS ══ -->
<div class="dc-filters-bar">
    <div class="container-xl">
        <div class="dc-filters">
            <button class="dc-filter active" data-cat="todos">Todos</button>
            <?php foreach ($categorias as $cat): ?>
            <button class="dc-filter" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
            <?php endforeach; ?>
            <button class="dc-filter" data-cat="general">General</button>
        </div>
    </div>
</div>

<!-- ══ GRID DESCUENTOS ══ -->
<div class="container-xl dc-body">

    <?php if (empty($descuentos)): ?>
    <div class="dc-empty">
        <i class="bi bi-tags"></i>
        <p>No hay descuentos disponibles en este momento.</p>
        <small>Vuelve pronto, actualizamos el catálogo regularmente.</small>
    </div>
    <?php else: ?>

    <div class="dc-grid" id="dcGrid">
        <?php foreach ($descuentos as $d):
            $cat   = $d['categoria_nombre'] ?? null;
            $c     = $catColors[$cat] ?? $defaultColor;
            $esFijo = $d['tipo'] === 'fijo';
            $valorDisplay = $esFijo
                ? number_format((float)$d['valor'], 0, ',', '.') . ' €'
                : (int)$d['valor'] . '%';
            $limitado = (int)$d['max_usos'] > 0;
            $usosLeft = $limitado ? max(0, (int)$d['max_usos'] - (int)$d['usos_actuales']) : null;
            $pctUsado = $limitado ? min(100, round((int)$d['usos_actuales'] / (int)$d['max_usos'] * 100)) : 0;

            // Días restantes
            $diasRestantes = null;
            $urgente = false;
            if ($d['fecha_fin']) {
                $diasRestantes = (int)ceil((strtotime($d['fecha_fin']) - time()) / 86400);
                $urgente = $diasRestantes <= 7;
            }
        ?>
        <div class="dc-card" data-cat="<?= htmlspecialchars($cat ?? 'general') ?>">

            <!-- Cabecera con degradado de categoría -->
            <div class="dc-card-top" style="background: linear-gradient(135deg, <?= $c['from'] ?> 0%, <?= $c['to'] ?> 100%);">
                <?php if ($urgente): ?>
                <div class="dc-urgente">⏳ ¡Expira pronto!</div>
                <?php elseif ($limitado && $usosLeft <= 5): ?>
                <div class="dc-urgente" style="background:rgba(220,38,38,0.9);">🔥 ¡Últimos usos!</div>
                <?php endif; ?>

                <div class="dc-emoji"><?= $d['emoji'] ?></div>
                <div class="dc-val">−<?= $valorDisplay ?></div>
                <?php if ($cat): ?>
                <div class="dc-cat-badge" style="background:rgba(255,255,255,0.2);"><?= htmlspecialchars($cat) ?></div>
                <?php else: ?>
                <div class="dc-cat-badge" style="background:rgba(255,255,255,0.2);">General</div>
                <?php endif; ?>
            </div>

            <!-- Cuerpo -->
            <div class="dc-card-body">
                <div class="dc-nombre"><?= htmlspecialchars($d['nombre']) ?></div>
                <div class="dc-desc"><?= htmlspecialchars($d['descripcion'] ?? '') ?></div>

                <!-- Código con botón copiar -->
                <div class="dc-code-wrap">
                    <div class="dc-code" id="code-<?= (int)$d['id'] ?>"><?= htmlspecialchars($d['codigo']) ?></div>
                    <button class="dc-btn-copy"
                            data-code="<?= htmlspecialchars($d['codigo'], ENT_QUOTES) ?>"
                            data-id="<?= (int)$d['id'] ?>"
                            title="Copiar código">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>

                <!-- Condiciones -->
                <div class="dc-conditions">
                    <?php if ((float)$d['min_compra'] > 0): ?>
                    <div class="dc-cond-item">
                        <i class="bi bi-cart-check" style="color:<?= $c['from'] ?>;"></i>
                        Compra mínima: <strong><?= number_format((float)$d['min_compra'], 0, ',', '.') ?> €</strong>
                    </div>
                    <?php endif; ?>
                    <?php if ($d['fecha_fin']): ?>
                    <div class="dc-cond-item <?= $urgente ? 'dc-cond-urgente' : '' ?>">
                        <i class="bi bi-calendar-check" style="color:<?= $urgente ? '#dc2626' : $c['from'] ?>;"></i>
                        <?php if ($urgente): ?>
                            <strong style="color:#dc2626;">¡Caduca en <?= $diasRestantes ?> <?= $diasRestantes === 1 ? 'día' : 'días' ?>!</strong>
                        <?php else: ?>
                            Válido hasta <?= date('d/m/Y', strtotime($d['fecha_fin'])) ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Barra de usos si es limitado -->
                <?php if ($limitado): ?>
                <div class="dc-usos-wrap">
                    <div class="dc-usos-label">
                        <span><?= (int)$d['usos_actuales'] ?> usados</span>
                        <span><?= $usosLeft ?> restantes</span>
                    </div>
                    <div class="dc-usos-bar">
                        <div class="dc-usos-fill" style="width:<?= $pctUsado ?>%;background:<?= $c['from'] ?>;"></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<!-- ══ CÓMO FUNCIONA ══ -->
<div class="dc-how">
    <div class="container-xl">
        <h2 class="dc-how-title">¿Cómo usar un descuento?</h2>
        <div class="dc-how-grid">
            <div class="dc-how-step">
                <div class="dc-how-num">1</div>
                <div class="dc-how-icon"><i class="bi bi-clipboard-check"></i></div>
                <div class="dc-how-text">Copia el código del descuento que quieras usar</div>
            </div>
            <div class="dc-how-arrow"><i class="bi bi-arrow-right"></i></div>
            <div class="dc-how-step">
                <div class="dc-how-num">2</div>
                <div class="dc-how-icon"><i class="bi bi-shop"></i></div>
                <div class="dc-how-text">Acude a una tienda asociada a shoppeo</div>
            </div>
            <div class="dc-how-arrow"><i class="bi bi-arrow-right"></i></div>
            <div class="dc-how-step">
                <div class="dc-how-num">3</div>
                <div class="dc-how-icon"><i class="bi bi-phone"></i></div>
                <div class="dc-how-text">Muestra el código en caja antes de pagar</div>
            </div>
            <div class="dc-how-arrow"><i class="bi bi-arrow-right"></i></div>
            <div class="dc-how-step">
                <div class="dc-how-num">4</div>
                <div class="dc-how-icon"><i class="bi bi-star-fill"></i></div>
                <div class="dc-how-text">¡Ahorra y acumula puntos shoppeo!</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ── Filtros de categoría ──────────────────────────────────────────────
    document.querySelectorAll('.dc-filter').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.dc-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.cat;
            document.querySelectorAll('.dc-card').forEach(function (card) {
                if (cat === 'todos') {
                    card.style.display = '';
                } else if (cat === 'general') {
                    card.style.display = (card.dataset.cat === 'null' || !card.dataset.cat || card.dataset.cat === '') ? '' : 'none';
                } else {
                    card.style.display = card.dataset.cat === cat ? '' : 'none';
                }
            });
        });
    });

    // ── Copiar código ─────────────────────────────────────────────────────
    document.querySelectorAll('.dc-btn-copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const code = this.dataset.code;
            const id   = this.dataset.id;

            navigator.clipboard.writeText(code).then(function () {
                btn.classList.add('dc-btn-copied');
                btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
                setTimeout(function () {
                    btn.classList.remove('dc-btn-copied');
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);

                // Registrar uso (fire & forget)
                fetch('/descuentos/usar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'descuento_id=' + id,
                });
            }).catch(function () {
                // Fallback para navegadores sin Clipboard API
                const el = document.getElementById('code-' + id);
                if (el) {
                    const range = document.createRange();
                    range.selectNode(el);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                    document.execCommand('copy');
                    window.getSelection().removeAllRanges();
                }
            });
        });
    });
}());
</script>
