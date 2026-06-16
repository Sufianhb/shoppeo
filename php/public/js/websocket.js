/**
 * Se carga en todos los layouts (main y admin).
 * Gestiona: usuarios online, feed de actividad en pagina de inicio.
 */

(function () {
  // Si Socket.io no esta disponible (WS server caído), no bloqueamos nada
  if (typeof io === "undefined") {
    console.warn("[WS] Socket.io no disponible.");
    return;
  }

  const socket = io("http://localhost:3000", {
    reconnection: true,
    reconnectionDelay: 2000,
    timeout: 5000,
  });

  // Eventos de conexion
  socket.on("connect", function () {
    console.log("[WS] Conectado al servidor Socket.io — ID:", socket.id);
    updateWsStatus(true);
  });

  socket.on("disconnect", function () {
    console.warn("[WS] Desconectado del servidor.");
    updateWsStatus(false);
  });

  // Usuarios online
  socket.on("users_online", function (data) {
    // Navbar badge (layout publico)
    const badge = document.getElementById("onlineBadge");
    const count = document.getElementById("onlineCount");
    const wsOnlineBadge = document.getElementById("wsOnlineBadge");

    if (badge) badge.classList.remove("d-none");
    if (count) count.textContent = data.count;
    if (wsOnlineBadge) {
      wsOnlineBadge.innerHTML = `<i class="bi bi-wifi"></i> ${data.count} online`;
    }
  });

  // Actividad de precios (pagina de inicio)
  socket.on("price_activity", function (data) {
    const feed = document.getElementById("activityFeed");
    if (!feed) return;

    // Ocultar mensaje de "esperando"
    const noMsg = document.getElementById("noActivityMsg");
    if (noMsg) noMsg.style.display = "none";

    const direction = data.precio < data.precio_anterior ? "bajó" : "subió";
    const iconClass =
      data.precio < data.precio_anterior
        ? "bi-arrow-down-circle-fill text-success"
        : "bi-arrow-up-circle-fill text-danger";
    const badgeClass =
      data.precio < data.precio_anterior ? "bg-success" : "bg-danger";
    const diff = Math.abs(data.precio - data.precio_anterior).toFixed(2);

    const item = document.createElement("div");
    item.className =
      "list-group-item list-group-item-action d-flex justify-content-between align-items-center";
    item.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <i class="bi ${iconClass} fs-4"></i>
                <div>
                    <strong>${escapeHtml(data.producto)}</strong>
                    <small class="text-muted d-block">en ${escapeHtml(data.tienda)}</small>
                </div>
            </div>
            <div class="text-end">
                <span class="badge ${badgeClass} fs-6">${parseFloat(data.precio).toFixed(2)} €</span>
                <small class="text-muted d-block">${direction} ${diff} €</small>
                <small class="text-muted d-block">${new Date(data.timestamp).toLocaleTimeString("es-ES")}</small>
            </div>
        `;

    // Insertar al principio (más reciente arriba)
    feed.prepend(item);

    // Limitar a los 8 últimos
    while (feed.children.length > 8) {
      feed.removeChild(feed.lastChild);
    }
  });

  // Helper: actualizar indicadores de estado WS
  function updateWsStatus(connected) {
    const indicators = document.querySelectorAll(
      "#wsStatus, #wsAdminStatus, #rtBadge",
    );
    indicators.forEach((el) => {
      el.classList.toggle("bg-success", connected);
      el.classList.toggle("bg-danger", !connected);
      if (connected) {
        el.innerHTML =
          '<i class="bi bi-lightning-charge"></i> Tiempo real activo';
      } else {
        el.innerHTML = '<i class="bi bi-wifi-off"></i> Desconectado';
      }
    });
  }

  // Actividad del simulador (compras, QR, descuentos)
  socket.on("sim_actividad", function (data) {
    const feed = document.getElementById("activityFeed");
    if (!feed) return;

    const noMsg = document.getElementById("noActivityMsg");
    if (noMsg) noMsg.style.display = "none";

    const hora = new Date(data.timestamp).toLocaleTimeString("es-ES");
    const item = document.createElement("div");
    item.className =
      "list-group-item list-group-item-action d-flex justify-content-between align-items-center";
    item.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <i class="bi ${escapeHtml(data.icono)}" style="font-size:1.6rem;color:${escapeHtml(data.color)};"></i>
                <div>
                    <strong>${escapeHtml(data.usuario)}</strong>
                    <small class="text-muted d-block">${escapeHtml(data.detalle)}</small>
                </div>
            </div>
            <div class="text-end">
                <span class="badge" style="background:${escapeHtml(data.bg)};color:${escapeHtml(data.color)};font-size:13px;">${escapeHtml(data.puntos)}</span>
                <small class="text-muted d-block">${hora}</small>
            </div>`;
    feed.prepend(item);
    while (feed.children.length > 8) feed.removeChild(feed.lastChild);
  });

  // Exponer socket globalmente para uso en otras vistas
  window.shoppeoSocket = socket;

  // Helper HTML escape
  function escapeHtml(str) {
    const div = document.createElement("div");
    div.appendChild(document.createTextNode(str || ""));
    return div.innerHTML;
  }
})();
