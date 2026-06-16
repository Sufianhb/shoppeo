<!-- Vista: public/puntos.php -->
<?php
$userId   = (int)($_SESSION['user_id'] ?? 0);
$userCode = 'SHP-' . str_pad((string)$userId, 5, '0', STR_PAD_LEFT);
$qrUrl    = 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($userCode)
    . '&size=160x160&bgcolor=ffffff&color=5b21b6&qzone=1';

// Barra de progreso hacia la próxima recompensa
$progreso = 0;
$puntosParaProxima = 0;
if ($proximaRecompensa) {
    $puntosParaProxima = (int)$proximaRecompensa['puntos_necesarios'];
    // calcular el peldaño anterior (última recompensa ya alcanzada)
    $anterior = 0;
    foreach ($recompensas as $r) {
        if ((int)$r['puntos_necesarios'] <= $totalPuntos) {
            $anterior = (int)$r['puntos_necesarios'];
        }
    }
    $rango    = $puntosParaProxima - $anterior;
    $avance   = $totalPuntos - $anterior;
    $progreso = $rango > 0 ? min(100, (int)round($avance / $rango * 100)) : 100;
}
?>

<!--  HERO  -->
<div class="pt-hero">
    <div class="container-xl">
        <div class="pt-hero-grid">

            <!-- Info puntos -->
            <div class="pt-hero-info">
                <div class="pt-hero-label">
                    <i class="bi bi-star-fill"></i> Programa de fidelidad
                </div>
                <h1 class="pt-hero-title">Mis Puntos</h1>
                <div class="pt-hero-balance">
                    <span class="pt-balance-num"><?= number_format($totalPuntos, 0, ',', '.') ?></span>
                    <span class="pt-balance-label">puntos disponibles</span>
                </div>

                <?php if ($proximaRecompensa): ?>
                    <div class="pt-progress-wrap">
                        <div class="pt-progress-labels">
                            <span>Próxima recompensa: <strong><?= $proximaRecompensa['emoji'] ?> <?= htmlspecialchars($proximaRecompensa['nombre']) ?></strong></span>
                            <span><?= number_format($totalPuntos, 0, ',', '.') ?> / <?= number_format($puntosParaProxima, 0, ',', '.') ?> pts</span>
                        </div>
                        <div class="pt-progress-bar">
                            <div class="pt-progress-fill" style="width:<?= $progreso ?>%"></div>
                        </div>
                        <div class="pt-progress-sub">
                            Te faltan <strong><?= number_format($puntosParaProxima - $totalPuntos, 0, ',', '.') ?> puntos</strong> — equivale a <?= number_format($puntosParaProxima - $totalPuntos, 0, ',', '.') ?> € en compras
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pt-hero-all">
                        <i class="bi bi-trophy-fill"></i> ¡Puedes canjear cualquier recompensa del catálogo!
                    </div>
                <?php endif; ?>
            </div>

            <!-- QR + código -->
            <div class="pt-hero-qr">
                <div class="pt-qr-card">
                    <div class="pt-qr-title">Tu código de cliente</div>
                    <img src="<?= $qrUrl ?>" alt="QR <?= $userCode ?>" class="pt-qr-img">
                    <div class="pt-qr-code"><?= $userCode ?></div>
                    <p class="pt-qr-hint">Muestra este código en tiendas asociadas o al usar tus descuentos shoppeo para acumular puntos.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="container-xl pt-body">

    <!--  CATÁLOGO DE RECOMPENSAS  -->
    <section class="pt-section">
        <div class="pt-section-head">
            <div class="pt-section-icon"><i class="bi bi-gift"></i></div>
            <div>
                <h2 class="pt-section-title">Catálogo de recompensas</h2>
                <p class="pt-section-sub">Canjea tus puntos por estos premios — te los enviamos a casa</p>
            </div>
        </div>

        <div class="pt-rewards-grid">
            <?php foreach ($recompensas as $r):
                $puedeC  = $totalPuntos >= (int)$r['puntos_necesarios'];
                $agotado = (int)$r['stock'] === 0;
            ?>
                <div class="pt-reward-card <?= !$puedeC ? 'pt-reward-locked' : '' ?> <?= $agotado ? 'pt-reward-agotado' : '' ?>">
                    <div class="pt-reward-emoji"><?= $r['emoji'] ?></div>
                    <div class="pt-reward-name"><?= htmlspecialchars($r['nombre']) ?></div>
                    <div class="pt-reward-desc"><?= htmlspecialchars($r['descripcion'] ?? '') ?></div>
                    <div class="pt-reward-pts">
                        <i class="bi bi-star-fill"></i>
                        <?= number_format((int)$r['puntos_necesarios'], 0, ',', '.') ?> pts
                    </div>

                    <?php if ($agotado): ?>
                        <div class="pt-reward-badge-agotado">Agotado</div>

                    <?php elseif ($puedeC): ?>
                        <button type="button" class="pt-btn-canjear"
                            data-id="<?= (int)$r['id'] ?>"
                            data-nombre="<?= htmlspecialchars($r['nombre'], ENT_QUOTES) ?>"
                            data-emoji="<?= htmlspecialchars($r['emoji'], ENT_QUOTES) ?>"
                            data-pts="<?= (int)$r['puntos_necesarios'] ?>">
                            <i class="bi bi-bag-check"></i> Canjear
                        </button>
                    <?php else: ?>
                        <div class="pt-reward-falta">
                            Faltan <?= number_format((int)$r['puntos_necesarios'] - $totalPuntos, 0, ',', '.') ?> pts
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!--  HISTORIAL  -->
    <div class="pt-bottom-grid">

        <!-- Transacciones -->
        <section class="pt-section">
            <div class="pt-section-head">
                <div class="pt-section-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h2 class="pt-section-title">Historial de puntos</h2>
                    <p class="pt-section-sub">Últimos movimientos en tu cuenta</p>
                </div>
            </div>

            <?php if (empty($transacciones)): ?>
                <div class="pt-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Aún no tienes movimientos. ¡Haz tu primera compra en una tienda asociada!</p>
                </div>
            <?php else: ?>
                <div class="pt-history">
                    <?php foreach ($transacciones as $t):
                        $positivo = (int)$t['puntos'] > 0;
                    ?>
                        <div class="pt-hist-item">
                            <div class="pt-hist-icon <?= $positivo ? 'pt-hist-icon-plus' : 'pt-hist-icon-minus' ?>">
                                <i class="bi <?= $positivo ? 'bi-plus-lg' : 'bi-dash-lg' ?>"></i>
                            </div>
                            <div class="pt-hist-info">
                                <div class="pt-hist-concepto"><?= htmlspecialchars($t['concepto']) ?></div>
                                <div class="pt-hist-date"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></div>
                            </div>
                            <div class="pt-hist-puntos <?= $positivo ? 'pt-hist-plus' : 'pt-hist-minus' ?>">
                                <?= $positivo ? '+' : '' ?><?= number_format((int)$t['puntos'], 0, ',', '.') ?> pts
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Mis canjes -->
        <section class="pt-section">
            <div class="pt-section-head">
                <div class="pt-section-icon"><i class="bi bi-bag-heart"></i></div>
                <div>
                    <h2 class="pt-section-title">Mis canjes</h2>
                    <p class="pt-section-sub">Estado de tus solicitudes de recompensa</p>
                </div>
            </div>

            <?php if (empty($canjes)): ?>
                <div class="pt-empty">
                    <i class="bi bi-gift"></i>
                    <p>Todavía no has canjeado ninguna recompensa.</p>
                </div>
            <?php else: ?>
                <div class="pt-canjes">
                    <?php foreach ($canjes as $c):
                        $estadoClass = match ($c['estado']) {
                            'pendiente'  => 'pt-estado-pendiente',
                            'enviado'    => 'pt-estado-enviado',
                            'completado' => 'pt-estado-completado',
                            default      => 'pt-estado-cancelado',
                        };
                        $estadoLabel = match ($c['estado']) {
                            'pendiente'  => 'Pendiente',
                            'enviado'    => 'En camino',
                            'completado' => 'Entregado',
                            default      => 'Cancelado',
                        };
                    ?>
                        <div class="pt-canje-item">
                            <div class="pt-canje-emoji"><?= $c['emoji'] ?></div>
                            <div class="pt-canje-info">
                                <div class="pt-canje-nombre"><?= htmlspecialchars($c['recompensa_nombre']) ?></div>
                                <div class="pt-canje-date"><?= date('d/m/Y', strtotime($c['created_at'])) ?> · <?= number_format((int)$c['puntos_usados'], 0, ',', '.') ?> pts</div>
                            </div>
                            <span class="pt-estado <?= $estadoClass ?>"><?= $estadoLabel ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div><!-- /pt-bottom-grid -->

</div><!-- /container -->

<!--  MODAL CANJEAR  -->
<div class="pt-modal-overlay" id="ptModal" style="display:none;">
    <div class="pt-modal">
        <button class="pt-modal-close" id="ptModalClose" aria-label="Cerrar">&times;</button>
        <div class="pt-modal-emoji" id="ptModalEmoji"></div>
        <h3 class="pt-modal-title">Canjear recompensa</h3>
        <p class="pt-modal-desc">Vas a canjear <strong id="ptModalNombre"></strong> por <strong id="ptModalPts"></strong> puntos.</p>

        <form method="POST" action="/puntos/canjear">
            <input type="hidden" name="recompensa_id" id="ptModalId">
            <div class="pt-modal-field">
                <label class="pt-modal-label">Dirección de envío <span style="color:#dc2626">*</span></label>
                <textarea name="direccion_envio" class="pt-modal-textarea"
                    placeholder="Calle, número, piso, ciudad, código postal…"
                    required rows="3"></textarea>
            </div>
            <div class="pt-modal-actions">
                <button type="submit" class="pt-modal-btn-confirm">
                    <i class="bi bi-bag-check"></i> Confirmar canje
                </button>
                <button type="button" class="pt-modal-btn-cancel" id="ptModalCancel">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        // ── Tiempo real: suscribirse al room del usuario ──────────────────────
        if (typeof io !== 'undefined') {
            var socket = window.shoppeoSocket || io('http://localhost:3000');
            socket.emit('subscribe_user', {
                user_id: <?= $userId ?>
            });

            var totalPuntos = <?= $totalPuntos ?>;

            socket.on('puntos_update', function(data) {
                // Actualizar balance numérico
                totalPuntos += data.puntos_delta;
                var numEl = document.querySelector('.pt-balance-num');
                if (numEl) {
                    numEl.textContent = totalPuntos.toLocaleString('es-ES');
                    numEl.style.transition = 'transform 0.2s ease';
                    numEl.style.transform = 'scale(1.08)';
                    setTimeout(function() {
                        numEl.style.transform = '';
                    }, 200);
                }

                // Actualizar barra de progreso
                var fill = document.querySelector('.pt-progress-fill');
                var sub = document.querySelector('.pt-progress-sub');
                if (fill) {
                    var ptsLabel = document.querySelector('.pt-progress-labels span:last-child');
                    var match = ptsLabel ? ptsLabel.textContent.match(/(\d[\d.]*)\s*pts/) : null;
                    var meta = match ? parseInt(match[1].replace(/\./g, '')) : 0;
                    if (meta > 0) {
                        var pct = Math.min(100, Math.round(totalPuntos / meta * 100));
                        fill.style.width = pct + '%';
                        if (ptsLabel) ptsLabel.textContent = totalPuntos.toLocaleString('es-ES') + ' / ' + meta.toLocaleString('es-ES') + ' pts';
                    }
                    if (sub) {
                        var faltan = Math.max(0, meta - totalPuntos);
                        sub.innerHTML = 'Te faltan <strong>' + faltan.toLocaleString('es-ES') + ' puntos</strong> — equivale a ' + faltan.toLocaleString('es-ES') + ' € en compras';
                    }
                }

                // Añadir entrada al historial
                var history = document.querySelector('.pt-history');
                if (!history) {
                    // Puede que esté mostrando el empty state, reemplazar
                    var emptyEl = document.querySelector('.pt-section .pt-empty');
                    if (emptyEl) {
                        var section = emptyEl.parentElement;
                        emptyEl.remove();
                        history = document.createElement('div');
                        history.className = 'pt-history';
                        section.appendChild(history);
                    }
                }
                if (history) {
                    var hora = new Date(data.timestamp).toLocaleString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    var item = document.createElement('div');
                    item.className = 'pt-hist-item';
                    item.style.animation = 'ptHistIn 0.3s ease';
                    item.innerHTML =
                        '<div class="pt-hist-icon pt-hist-icon-plus"><i class="bi bi-plus-lg"></i></div>' +
                        '<div class="pt-hist-info">' +
                        '<div class="pt-hist-concepto">' + data.concepto + '</div>' +
                        '<div class="pt-hist-date">' + hora + '</div>' +
                        '</div>' +
                        '<div class="pt-hist-puntos pt-hist-plus">+' + data.puntos_delta.toLocaleString('es-ES') + ' pts</div>';
                    history.prepend(item);
                    // Limitar a 15 entradas
                    while (history.children.length > 15) history.removeChild(history.lastChild);
                }
            });
        }

        // Animación entrada historial
        if (!document.getElementById('ptRtStyle')) {
            var s = document.createElement('style');
            s.id = 'ptRtStyle';
            s.textContent = '@keyframes ptHistIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}';
            document.head.appendChild(s);
        }

        // ── Modal canje ───────────────────────────────────────────────────────
        var modal = document.getElementById('ptModal');
        var btnClose = document.getElementById('ptModalClose');
        var btnCancel = document.getElementById('ptModalCancel');

        document.querySelectorAll('.pt-btn-canjear').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('ptModalEmoji').textContent = this.dataset.emoji;
                document.getElementById('ptModalNombre').textContent = this.dataset.nombre;
                document.getElementById('ptModalPts').textContent = parseInt(this.dataset.pts).toLocaleString('es-ES') + ' pts';
                document.getElementById('ptModalId').value = this.dataset.id;
                modal.style.display = 'flex';
            });
        });

        function closeModal() {
            modal.style.display = 'none';
        }
        btnClose.addEventListener('click', closeModal);
        btnCancel.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }());
</script>