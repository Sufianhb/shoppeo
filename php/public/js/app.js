"use strict";

// Buscador con autocompletado en la navbar
(function () {
    const input = document.getElementById("navSearchInput");
    if (!input) return;

    let timer = null;
    let panel = null;
    let activo = -1;

    //Construir panel de sugerencias y manejar eventos de cada item
    function mostrarPanel(items) {
        cerrarPanel();
        if (!items.length) return;

        panel = document.createElement("div");
        panel.className = "nav-suggestions";

        items.forEach(function (p, i) {
            const item = document.createElement("a");
            item.className = "nav-suggestion-item";
            item.href = "/producto/" + p.id;
            item.dataset.idx = i;

            const precio = p.precio_minimo
                ? '<span class="nsi-precio">desde ' +
                parseFloat(p.precio_minimo).toFixed(2) +
                " €</span>"
                : "";
            const cat = p.categoria
                ? '<span class="nsi-cat">' + limpiarHtml(p.categoria) + "</span>"
                : "";

            item.innerHTML =
                '<span class="nsi-icon"><i class="bi bi-box-seam"></i></span>' +
                '<span class="nsi-body">' +
                '<strong class="nsi-nombre">' +
                limpiarHtml(p.nombre) +
                "</strong>" +
                cat +
                "</span>" +
                precio;

            item.addEventListener("mouseenter", function () {
                setActivo(i);
            });
            panel.appendChild(item);
        });

        // Append to <form> (not .input-group which has overflow:hidden)
        const wrap = input.closest("form") || input.parentElement;
        wrap.appendChild(panel);
        activo = -1;
    }

    function cerrarPanel() {
        if (panel) {
            panel.remove();
            panel = null;
        }
        activo = -1;
    }

    function setActivo(idx) {
        if (!panel) return;
        panel.querySelectorAll(".nav-suggestion-item").forEach(function (el, i) {
            el.classList.toggle("is-active", i === idx);
        });
        activo = idx;
    }

    //Input con debounce
    input.addEventListener("input", function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 1) {
            cerrarPanel();
            return;
        }
        timer = setTimeout(function () {
            fetchSugerencias(q);
        }, 150);
    });

    //Navegación con teclado
    input.addEventListener("keydown", function (e) {
        if (!panel) return;
        const items = panel.querySelectorAll(".nav-suggestion-item");
        if (e.key === "ArrowDown") {
            e.preventDefault();
            setActivo(Math.min(activo + 1, items.length - 1));
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            setActivo(Math.max(activo - 1, 0));
        } else if (e.key === "Enter" && activo >= 0) {
            e.preventDefault();
            items[activo].click();
        } else if (e.key === "Escape") {
            cerrarPanel();
        }
    });

    //Cerrar al hacer click fuera
    document.addEventListener("click", function (e) {
        if (panel && !panel.contains(e.target) && e.target !== input) cerrarPanel();
    });

    //Fetch al endpoint de sugerencias
    async function fetchSugerencias(q) {
        try {
            const res = await fetch("/api/sugerencias?q=" + encodeURIComponent(q));
            const json = await res.json();
            if (json.ok && json.data && json.data.length > 0) {
                mostrarPanel(json.data);
            } else {
                cerrarPanel();
            }
        } catch (err) {
            console.error("Sugerencias error:", err);
        }
    }
})();

// Escape HTML
function limpiarHtml(str) {
    const d = document.createElement("div");
    d.appendChild(document.createTextNode(str || ""));
    return d.innerHTML;
}

// Cerrar alertas de exito solas tras 4 segundos
document.querySelectorAll(".alert.alert-success").forEach(function (alerta) {
    setTimeout(function () {
        const b = bootstrap.Alert.getOrCreateInstance(alerta);
        if (b) b.close();
    }, 4000);
});

// Geolocalizacion: detecta la ciudad del usuario y centra el mapa
function detectarUbicacion() {
    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
        async function (pos) {
            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;

            window.userLat = lat;
            window.userLon = lon;

            // Cachear para que el mapa cargue en la ubicacion correcta en la proxima visita
            localStorage.setItem("userLat", lat);
            localStorage.setItem("userLon", lon);

            window.dispatchEvent(
                new CustomEvent("ubicacionLista", {
                    detail: { lat: lat, lon: lon },
                }),
            );

            try {
                const res = await fetch("/api/location", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ lat: lat, lon: lon }),
                });
                const data = await res.json();
                const ciudad = data.city || "Tu ubicación";
                document.querySelectorAll(".ciudad").forEach(function (el) {
                    el.textContent = ciudad;
                });
                // Cachear ciudad para siguiente carga
                localStorage.setItem("userCiudad", ciudad);
            } catch (e) {
                console.error(e);
                document.querySelectorAll(".ciudad").forEach(function (el) {
                    el.textContent = "Tu ubicación";
                });
            }
        },
        function () { },
    );
}

document.addEventListener("DOMContentLoaded", detectarUbicacion);

// Inyectar lat/lng/radius_km en formularios de búsqueda que no los tengan ya
// (navbar, hero). buscar.php los preserva vía PHP desde la URL actual.
(function () {
    function inyectarUbicacion() {
        var lat = localStorage.getItem("userLat");
        var lon = localStorage.getItem("userLon");
        var radius = localStorage.getItem("userRadius"); // lo establece el slider en PASO 4

        if (!lat || !lon) return;

        document
            .querySelectorAll('form[action="/buscar"]')
            .forEach(function (form) {
                // Si PHP ya inyectó lat desde la URL, no sobreescribir
                if (form.querySelector('input[name="lat"]')) return;

                function addHidden(name, value) {
                    var inp = document.createElement("input");
                    inp.type = "hidden";
                    inp.name = name;
                    inp.value = value;
                    form.appendChild(inp);
                }

                addHidden("lat", lat);
                addHidden("lng", lon);
                if (radius) addHidden("radius_km", radius);
            });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", inyectarUbicacion);
    } else {
        inyectarUbicacion();
    }
})();

// Favoritos — toggle global para todos los botones .btn-fav
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btn-fav").forEach(function (btn) {
        btn.addEventListener("click", async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const productoId = btn.dataset.id;
            if (!productoId) return;

            try {
                const res = await fetch("/favoritos/toggle", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ producto_id: parseInt(productoId, 10) }),
                });

                if (res.status === 401) {
                    window.location.href = "/login";
                    return;
                }

                const data = await res.json();
                if (!data.success) return;

                const icon = btn.querySelector("i");
                const label = btn.querySelector(".pd-fav-label");

                /* Detectar si es el botón de la página de producto (tiene pd-fav-label) */
                const isProductPage = !!label;

                if (data.added) {
                    if (isProductPage) {
                        icon.className = "bi bi-bookmark-check-fill";
                        btn.classList.add("is-saved");
                        label.textContent = "¡Guardado en tu lista!";
                    } else {
                        icon.className = "bi bi-heart-fill text-danger";
                    }
                    btn.setAttribute("aria-label", "Quitar de favoritos");
                } else {
                    if (isProductPage) {
                        icon.className = "bi bi-bookmark-plus-fill";
                        btn.classList.remove("is-saved");
                        label.textContent = "Guardar en mi lista";
                    } else {
                        icon.className = "bi bi-heart";
                    }
                    btn.setAttribute("aria-label", "Guardar en mi lista");
                }

                const wishCount = document.getElementById("wishCount");
                const wishIcon = document.querySelector(".nav-wishlist i");
                if (wishCount !== null) {
                    wishCount.textContent = data.count;
                    wishCount.style.display = data.count > 0 ? "" : "none";
                }
                if (wishIcon) {
                    wishIcon.className =
                        data.count > 0 ? "bi bi-heart-fill" : "bi bi-heart";
                    wishIcon.style.fontSize = "20px";
                }
            } catch (err) {
                console.error("Favoritos toggle error:", err);
            }
        });
    });
});
