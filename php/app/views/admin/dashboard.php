<!-- Vista: admin/dashboard.php — Pixel-perfect redesign -->

<!-- Page header -->
<h1 class="ad-page-title">Dashboard</h1>
<p class="ad-page-sub">Resumen del sistema shoppeo</p>

<!-- ══ KPI CARDS ══ -->
<div class="ad-kpi-grid">

    <!-- Productos -->
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-purple">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-productos"><?= count($productos) ?></div>
                <div class="ad-kpi-label">Productos activos</div>
            </div>
        </div>
        <div class="ad-kpi-trend">
            <i class="bi bi-arrow-up-right"></i> +2 este mes
        </div>
    </div>

    <!-- Tiendas -->
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-green">
                <i class="bi bi-shop-window"></i>
            </div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-tiendas"><?= count($tiendas) ?></div>
                <div class="ad-kpi-label">Tiendas activas</div>
            </div>
        </div>
        <div class="ad-kpi-trend" style="color:#22c55e;">
            <i class="bi bi-arrow-up-right"></i> +1 este mes
        </div>
    </div>

    <!-- Precios -->
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-amber">
                <i class="bi bi-tags"></i>
            </div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-precios"><?= count($precios) ?></div>
                <div class="ad-kpi-label">Precios registrados</div>
            </div>
        </div>
        <div class="ad-kpi-trend" style="color:#f59e0b;">
            <i class="bi bi-arrow-up-right"></i> +8 hoy
        </div>
    </div>

    <!-- Usuarios -->
    <div class="ad-kpi-card">
        <div class="ad-kpi-top">
            <div class="ad-kpi-icon ad-kpi-icon-blue">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="ad-kpi-val" id="kpi-val-usuarios"><?= count($usuarios) ?></div>
                <div class="ad-kpi-label">Usuarios registrados</div>
            </div>
        </div>
        <div class="ad-kpi-trend" style="color:#3b82f6;">
            <i class="bi bi-arrow-up-right"></i> +0 este mes
        </div>
    </div>

</div>

<!-- ══ MID: TABLA + TIEMPO REAL ══ -->
<div class="ad-mid-grid">

    <!-- Tabla de últimos precios -->
    <div class="ad-card">
        <div class="ad-card-header">
            <div class="ad-card-title">
                <i class="bi bi-clock-history"></i>
                Últimos precios actualizados
            </div>
            <a href="/admin/precios" class="ad-btn-outline">Ver todos</a>
        </div>
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tienda</th>
                        <th class="th-right">Precio</th>
                        <th class="th-right">Actualizado</th>
                    </tr>
                </thead>
                <tbody id="dashPriceTableBody">
                    <?php foreach (array_slice($precios, 0, 10) as $p): ?>
                        <tr>
                            <td class="td-prod"><?= htmlspecialchars($p['producto']) ?></td>
                            <td><span class="ad-store-badge"><?= htmlspecialchars($p['tienda']) ?></span></td>
                            <td class="td-price"><?= number_format((float)$p['precio'], 2, ',', '.') ?> €</td>
                            <td class="td-date"><?= date('d/m H:i', strtotime($p['actualizado_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel tiempo real -->
    <div class="ad-card">
        <div class="ad-card-header">
            <div class="ad-card-title" style="color:#1a1a2e;">
                <i class="bi bi-activity" style="color:#7c3aed;"></i>
                Tiempo Real
            </div>
            <span class="ad-ws-badge" id="wsAdminStatus">
                <span class="ad-ws-dot"></span>
                WebSocket activo
            </span>
        </div>

        <div class="ad-rt-body">
            <!-- decorative visual -->
            <div class="ad-rt-visual">
                <div class="ad-rt-circle">
                    <i class="bi bi-activity"></i>
                </div>
                <span class="ad-rt-dot ad-rt-dot-1"></span>
                <span class="ad-rt-dot ad-rt-dot-2"></span>
                <span class="ad-rt-dot ad-rt-dot-3"></span>
                <span class="ad-rt-dot ad-rt-dot-4"></span>
            </div>
            <p class="ad-rt-title">Esperando eventos...</p>
            <p class="ad-rt-sub">Los eventos en tiempo real aparecerán aquí</p>
        </div>

        <!-- Feed populated by websocket.js -->
        <div id="adminActivityFeed" style="display:none;max-height:220px;overflow-y:auto;padding:0 20px 16px;"></div>
    </div>

</div>

<!-- ══ BOTONES DE ACCIÓN ══ -->
<div class="ad-actions-grid">

    <a href="/admin/productos/crear" class="ad-action-btn ad-action-purple">
        <div class="ad-action-icon">
            <i class="bi bi-plus-circle"></i>
        </div>
        <div class="ad-action-text">
            <div class="ad-action-title">Nuevo Producto</div>
            <div class="ad-action-sub">Añadir un nuevo producto al sistema</div>
        </div>
        <i class="bi bi-arrow-right ad-action-arrow"></i>
    </a>

    <a href="/admin/precios" class="ad-action-btn ad-action-amber">
        <div class="ad-action-icon">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div class="ad-action-text">
            <div class="ad-action-title">Actualizar Precios</div>
            <div class="ad-action-sub">Sincronizar y actualizar todos los precios</div>
        </div>
        <i class="bi bi-arrow-right ad-action-arrow"></i>
    </a>

    <a href="/admin/tiendas" class="ad-action-btn ad-action-green">
        <div class="ad-action-icon">
            <i class="bi bi-shop-window"></i>
        </div>
        <div class="ad-action-text">
            <div class="ad-action-title">Gestionar Tiendas</div>
            <div class="ad-action-sub">Administrar tiendas y configuraciones</div>
        </div>
        <i class="bi bi-arrow-right ad-action-arrow"></i>
    </a>

</div>
