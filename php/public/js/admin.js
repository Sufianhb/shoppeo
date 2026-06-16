/**
 * Logica específica del panel de administracion
 */
'use strict';

// Confirmacion de eliminaciones
document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', function (e) {
        if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
});

// Tooltips de Bootstrap
document.querySelectorAll('[title]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover' });
});

// WebSocket — tiempo real completo
if (typeof io !== 'undefined') {
    const socket = window.shoppeoSocket || io('http://localhost:3000');

    //Helpers 

    function esc(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

    // Incrementa un contador KPI con flash morado
    function kpiAdd(id, delta) {
        const el = document.getElementById(id);
        if (!el) return;
        const v = (parseInt(el.textContent.replace(/\D/g, '')) || 0) + delta;
        el.textContent = v.toLocaleString('es-ES');
        el.style.transition = 'color 0.4s';
        el.style.color = '#7c3aed';
        setTimeout(() => { el.style.color = ''; }, 1200);
    }

    // Flash de fila nueva en una tabla
    function flashRow(tr, color) {
        color = color || '#f0fdf4';
        tr.style.transition = 'background 0.4s';
        tr.style.background = color;
        setTimeout(() => { tr.style.background = ''; }, 1600);
    }

    // Inyectar keyframes una vez
    if (!document.getElementById('adminRtStyles')) {
        const s = document.createElement('style');
        s.id = 'adminRtStyles';
        s.textContent = `
            @keyframes rtRowIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:none; } }
            @keyframes simToastIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:none; } }
        `;
        document.head.appendChild(s);
    }

    //1. price_updated — flash en tabla de precios (simulaciones de actividad)
    socket.on('price_updated', function (data) {
        document.querySelectorAll('#preciosTableBody tr, #dashPriceTableBody tr').forEach(row => {
            const c1 = row.querySelector('td:first-child');
            const c2 = row.querySelector('td:nth-child(2)');
            const cp = row.querySelector('.fw-bold');
            if (!c1 || !c2 || !cp) return;
            if (!c1.textContent.includes(data.producto)) return;
            if (!c2.textContent.includes(data.tienda)) return;
            flashRow(row, '#fff3cd');
            cp.textContent = parseFloat(data.precio).toFixed(2).replace('.', ',') + ' €';
        });
    });

    //2. price_activity — prepend en tabla del dashboard 
    socket.on('price_activity', function (data) {
        kpiAdd('kpi-val-precios', 0); // no suma registros, solo cambia valor

        const tbody = document.getElementById('dashPriceTableBody');
        if (!tbody) return;
        const dir   = data.precio < data.precio_anterior ? '▼' : '▲';
        const color = data.precio < data.precio_anterior ? '#16a34a' : '#dc2626';
        const tr = document.createElement('tr');
        tr.style.animation = 'rtRowIn 0.3s ease';
        tr.innerHTML = `
            <td class="td-prod">${esc(data.producto)}</td>
            <td><span class="ad-store-badge">${esc(data.tienda)}</span></td>
            <td class="td-price" style="color:${color};font-weight:700;">
                ${dir} ${parseFloat(data.precio).toFixed(2).replace('.', ',')} €
            </td>
            <td class="td-date">ahora</td>`;
        tbody.prepend(tr);
        flashRow(tr, '#f0fdf4');
        while (tbody.children.length > 12) tbody.removeChild(tbody.lastChild);
    });

    //3. stock_updated — flash fila en tabla de precios 
    socket.on('stock_updated', function (data) {
        document.querySelectorAll('#preciosTableBody tr').forEach(row => {
            const c1 = row.querySelector('td:first-child');
            if (!c1 || !c1.textContent.includes(data.producto)) return;
            flashRow(row, '#fef3c7');
            const stockCell = row.querySelector('.stock-val');
            if (stockCell) stockCell.textContent = data.stock;
        });
    });

    //4. sim_actividad — toast + dashboard RT feed + KPIs
    socket.on('sim_actividad', function (data) {
        showSimToast(data);
        updateDashFeed(data);

        if (data.tipo === 'producto') kpiAdd('kpi-val-productos', 1);
        if (data.tipo === 'registro') {
            kpiAdd('kpi-val-usuarios',        1);
            kpiAdd('kpi-val-usuarios-total',  1);
            kpiAdd('kpi-val-usuarios-activos', 1);
        }
    });

    //5. nueva_tienda — tabla tiendas + KPI 
    socket.on('nueva_tienda', function (data) {
        kpiAdd('kpi-val-tiendas', 1);
        const span = document.getElementById('tiendasCount');
        if (span) span.textContent = (parseInt(span.textContent) || 0) + 1;

        const tbody = document.getElementById('tiendasTableBody');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.style.animation = 'rtRowIn 0.3s ease';
        tr.innerHTML = `
            <td><strong>${esc(data.nombre)}</strong></td>
            <td><small class="text-muted">${esc(data.direccion || '—')}</small></td>
            <td><small>${esc(data.telefono || '—')}</small></td>
            <td class="text-center">
                <small class="text-muted font-monospace">${data.latitud}, ${data.longitud}</small>
            </td>
            <td><span class="text-muted">—</span></td>`;
        tbody.prepend(tr);
        flashRow(tr, '#f0fdf4');
    });

    //6. nuevo_usuario — tabla usuarios + KPIs 
    socket.on('nuevo_usuario', function (data) {
        kpiAdd('kpi-val-usuarios',         1);
        kpiAdd('kpi-val-usuarios-total',   1);
        kpiAdd('kpi-val-usuarios-activos', 1);

        const tbody = document.getElementById('usuariosTableBody');
        if (!tbody) return;
        const letra = (data.nombre || '?')[0].toUpperCase();
        const tr = document.createElement('tr');
        tr.style.animation = 'rtRowIn 0.3s ease';
        tr.innerHTML = `
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#9333ea);
                                border-radius:50%;display:flex;align-items:center;justify-content:center;
                                color:white;font-size:14px;font-weight:700;flex-shrink:0;">${letra}</div>
                    <span style="font-weight:600;color:#111827;">${esc(data.nombre)}</span>
                </div>
            </td>
            <td style="color:#6b7280;">${esc(data.email)}</td>
            <td><span class="ad-store-badge"><i class="bi bi-person me-1"></i>Usuario</span></td>
            <td><span class="pd-stock-badge">Activo</span></td>
            <td class="td-right td-date">ahora</td>
            <td class="td-right"><a href="#" style="font-size:12px;color:#7c3aed;">Editar</a></td>`;
        tbody.prepend(tr);
        flashRow(tr, '#f0fdf4');
    });

    //7. nueva_actividad — tabla actividad + KPIs hoy/total 
    socket.on('nueva_actividad', function (data) {
        if (data.tipo === 'view') kpiAdd('kpi-val-hoy', 1);
        kpiAdd('kpi-val-total', 1);

        const empty = document.getElementById('actividadEmpty');
        const wrap  = document.getElementById('actividadTableWrap');
        if (empty) empty.style.display = 'none';
        if (wrap)  wrap.style.display  = '';

        const tbody = document.getElementById('actividadTableBody');
        if (!tbody) return;
        const tipoHtml = data.tipo === 'scan'
            ? '<span class="ad-store-badge" style="background:#f5f3ff;color:#6d28d9;"><i class="bi bi-qr-code me-1"></i>Escaneo QR</span>'
            : '<span class="ad-store-badge"><i class="bi bi-eye me-1"></i>Vista</span>';
        const hora = new Date(data.created_at).toLocaleString('es-ES', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
        });
        const tr = document.createElement('tr');
        tr.style.animation = 'rtRowIn 0.3s ease';
        tr.innerHTML = `
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:32px;height:32px;background:#ede9fe;border-radius:8px;
                                display:flex;align-items:center;justify-content:center;
                                color:#7c3aed;font-size:14px;flex-shrink:0;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <span style="color:#111827;font-weight:600;font-size:13.5px;">${esc(data.producto || 'Producto simulado')}</span>
                </div>
            </td>
            <td>${tipoHtml}</td>
            <td class="td-right td-date">${hora}</td>`;
        tbody.prepend(tr);
        flashRow(tr, '#f5f3ff');
        while (tbody.children.length > 100) tbody.removeChild(tbody.lastChild);
    });

    //Dashboard RT feed 
    function updateDashFeed(data) {
        const feed   = document.getElementById('adminActivityFeed');
        const rtBody = document.querySelector('.ad-rt-body');
        if (!feed) return;
        if (rtBody) rtBody.style.display = 'none';
        feed.style.display = 'block';

        const hora = new Date(data.timestamp).toLocaleTimeString('es-ES');
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid #f3f4f6;font-size:13px;animation:rtRowIn 0.25s ease;';
        item.innerHTML = `
            <i class="bi ${esc(data.icono)}" style="color:${esc(data.color)};font-size:17px;flex-shrink:0;"></i>
            <div style="flex:1;min-width:0;overflow:hidden;">
                <span style="font-weight:700;">${esc(data.usuario)}</span>
                <span style="color:#6b7280;margin-left:6px;font-size:12px;">${esc(data.detalle)}</span>
            </div>
            <span style="background:${esc(data.bg)};color:${esc(data.color)};border-radius:99px;padding:2px 8px;font-size:11px;font-weight:700;flex-shrink:0;">${esc(data.puntos)}</span>
            <span style="color:#9ca3af;font-size:11px;flex-shrink:0;">${hora}</span>`;
        feed.prepend(item);
        while (feed.children.length > 10) feed.removeChild(feed.lastChild);
    }

    //Toast esquina inferior derecha (simulaciones de actividad)
    function showSimToast(data) {
        let container = document.getElementById('simToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'simToastContainer';
            container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column-reverse;gap:8px;max-width:320px;pointer-events:none;';
            document.body.appendChild(container);
        }
        const hora = new Date(data.timestamp).toLocaleTimeString('es-ES');
        const toast = document.createElement('div');
        toast.style.cssText = `background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.12);padding:12px 14px;display:flex;align-items:center;gap:10px;font-size:13px;pointer-events:all;animation:simToastIn 0.25s ease;border-left:4px solid ${data.color};`;
        toast.innerHTML = `
            <i class="bi ${esc(data.icono)}" style="font-size:20px;color:${esc(data.color)};flex-shrink:0;"></i>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(data.usuario)}</div>
                <div style="color:#6b7280;font-size:11.5px;">${esc(data.detalle)}</div>
            </div>
            <div style="flex-shrink:0;text-align:right;">
                <span style="background:${esc(data.bg)};color:${esc(data.color)};font-weight:700;border-radius:99px;padding:2px 8px;font-size:11px;">${esc(data.puntos)}</span>
                <div style="color:#9ca3af;font-size:10.5px;margin-top:2px;">${hora}</div>
            </div>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s,transform 0.3s';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
        while (container.children.length > 5) container.removeChild(container.firstChild);
    }

    //Estado WebSocket (simulaciones de actividad)
    socket.on('connect', () => {
        const b = document.getElementById('wsAdminStatus');
        if (b) { b.className = 'ad-badge-rt'; b.style.background = '#dcfce7'; b.style.color = '#16a34a'; b.innerHTML = '<span class="ad-badge-rt-dot"></span><i class="bi bi-lightning-charge-fill"></i> WebSocket activo'; }
    });
    socket.on('disconnect', () => {
        const b = document.getElementById('wsAdminStatus');
        if (b) { b.className = 'ad-badge-rt'; b.style.background = '#fef2f2'; b.style.color = '#dc2626'; b.innerHTML = '<span class="ad-badge-rt-dot" style="background:#dc2626"></span><i class="bi bi-wifi-off"></i> Desconectado'; }
    });
}
