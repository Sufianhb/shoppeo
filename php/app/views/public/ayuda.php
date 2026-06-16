<!-- Vista: public/ayuda.php -->

<!--  HERO  -->
<div class="ay-hero">
    <div class="ay-hero-inner container-xl">
        <div class="ay-hero-badge">
            <i class="bi bi-headset"></i> Centro de Ayuda
        </div>
        <h1 class="ay-hero-title">¿En qué podemos<br><span class="ay-hero-accent">ayudarte hoy?</span></h1>
        <p class="ay-hero-sub">
            Encuentra respuesta a tus dudas o contacta con nuestro equipo.<br>
            Respondemos en menos de 24 horas.
        </p>
        <div class="ay-hero-chips">
            <a href="#faq" class="ay-chip"><i class="bi bi-question-circle"></i> Preguntas frecuentes</a>
            <a href="#contacto" class="ay-chip"><i class="bi bi-envelope"></i> Contactar</a>
            <a href="#servicios" class="ay-chip"><i class="bi bi-shop"></i> Contratar servicios</a>
        </div>
    </div>
</div>

<div class="container-xl ay-body">

    <!--  FAQ  -->
    <section id="faq" class="ay-section">
        <div class="ay-section-head">
            <div class="ay-section-icon"><i class="bi bi-question-lg"></i></div>
            <div>
                <h2 class="ay-section-title">Preguntas frecuentes</h2>
                <p class="ay-section-sub">Las dudas más comunes resueltas al instante</p>
            </div>
        </div>

        <div class="ay-faq-grid">

            <div class="ay-faq-col">
                <details class="ay-faq">
                    <summary class="ay-faq-q">
                        <i class="bi bi-chevron-right ay-faq-arrow"></i>
                        ¿Cómo funciona shoppeo?
                    </summary>
                    <div class="ay-faq-a">
                        shoppeo compara en tiempo real los precios de productos en tiendas locales de tu ciudad.
                        Puedes buscar cualquier producto, ver en qué tienda está más barato y consultar su ubicación en el mapa.
                    </div>
                </details>

                <details class="ay-faq">
                    <summary class="ay-faq-q">
                        <i class="bi bi-chevron-right ay-faq-arrow"></i>
                        ¿Los precios están actualizados?
                    </summary>
                    <div class="ay-faq-a">
                        Sí. Cada tienda actualiza sus precios desde su panel de administración y los cambios
                        se reflejan al instante en la plataforma gracias a nuestra tecnología WebSocket en tiempo real.
                    </div>
                </details>

                <details class="ay-faq">
                    <summary class="ay-faq-q">
                        <i class="bi bi-chevron-right ay-faq-arrow"></i>
                        ¿Cómo añado productos a favoritos?
                    </summary>
                    <div class="ay-faq-a">
                        Necesitas una cuenta. Una vez registrado, pulsa el icono <i class="bi bi-heart" style="color:#7c3aed;"></i>
                        en cualquier tarjeta de producto para guardarlo. Accede a todos tus productos guardados desde el icono del corazón en la barra de navegación.
                    </div>
                </details>
            </div>

            <div class="ay-faq-col">
                <details class="ay-faq">
                    <summary class="ay-faq-q">
                        <i class="bi bi-chevron-right ay-faq-arrow"></i>
                        ¿Las tiendas están verificadas?
                    </summary>
                    <div class="ay-faq-a">
                        Todas las tiendas de la plataforma son revisadas y aprobadas por nuestro equipo antes de aparecer
                        en el listado. Garantizamos que la información de contacto, ubicación y precios es real.
                    </div>
                </details>

                <details class="ay-faq">
                    <summary class="ay-faq-q">
                        <i class="bi bi-chevron-right ay-faq-arrow"></i>
                        ¿Puedo usar shoppeo sin registrarme?
                    </summary>
                    <div class="ay-faq-a">
                        Sí, la búsqueda y comparación de precios es totalmente libre.
                        El registro sólo es necesario para guardar productos y recibir alertas de precio.
                    </div>
                </details>

                <details class="ay-faq">
                    <summary class="ay-faq-q">
                        <i class="bi bi-chevron-right ay-faq-arrow"></i>
                        He encontrado un error, ¿qué hago?
                    </summary>
                    <div class="ay-faq-a">
                        Usa el formulario de contacto de abajo indicando el asunto "Problema técnico".
                        Incluye una descripción del error y, si puedes, una captura de pantalla.
                        Lo resolveremos lo antes posible.
                    </div>
                </details>
            </div>

        </div>
    </section>

    <!--  CONTACTO + SERVICIOS  -->
    <section id="contacto" class="ay-section">
        <div class="ay-contact-grid">

            <!-- Columna izquierda: datos + formulario -->
            <div class="ay-contact-left">
                <div class="ay-section-head">
                    <div class="ay-section-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <h2 class="ay-section-title">Contáctanos</h2>
                        <p class="ay-section-sub">Escríbenos y te respondemos en menos de 24 h</p>
                    </div>
                </div>

                <!-- Tarjetas de contacto -->
                <div class="ay-contact-cards">
                    <div class="ay-ccard">
                        <div class="ay-ccard-icon"><i class="bi bi-envelope-at"></i></div>
                        <div>
                            <div class="ay-ccard-label">Email</div>
                            <div class="ay-ccard-val">info@shoppeo.es</div>
                        </div>
                    </div>
                    <div class="ay-ccard">
                        <div class="ay-ccard-icon"><i class="bi bi-clock"></i></div>
                        <div>
                            <div class="ay-ccard-label">Horario de atención</div>
                            <div class="ay-ccard-val">Lun–Vie · 9:00–18:00</div>
                        </div>
                    </div>
                    <div class="ay-ccard">
                        <div class="ay-ccard-icon"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="ay-ccard-label">Ubicación</div>
                            <div class="ay-ccard-val">Teruel, España</div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="ay-form-card">
                    <form method="POST" action="/ayuda" novalidate>

                        <div class="ay-form-row">
                            <div class="ay-form-field">
                                <label class="ay-label" for="ay-nombre">Nombre</label>
                                <div class="ay-input-wrap">
                                    <i class="bi bi-person ay-input-icon"></i>
                                    <input type="text" id="ay-nombre" name="nombre"
                                        class="ay-input" placeholder="Tu nombre" required>
                                </div>
                            </div>
                            <div class="ay-form-field">
                                <label class="ay-label" for="ay-email">Email</label>
                                <div class="ay-input-wrap">
                                    <i class="bi bi-envelope ay-input-icon"></i>
                                    <input type="email" id="ay-email" name="email"
                                        class="ay-input" placeholder="tu@email.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="ay-form-field">
                            <label class="ay-label" for="ay-asunto">Asunto</label>
                            <div class="ay-input-wrap">
                                <i class="bi bi-tag ay-input-icon"></i>
                                <select id="ay-asunto" name="asunto" class="ay-input ay-select" required>
                                    <option value="">Selecciona un asunto…</option>
                                    <option value="consulta">Consulta general</option>
                                    <option value="problema">Problema técnico</option>
                                    <option value="contratar">Contratar servicios</option>
                                    <option value="tienda">Añadir mi tienda</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="ay-form-field">
                            <label class="ay-label" for="ay-mensaje">Mensaje</label>
                            <textarea id="ay-mensaje" name="mensaje" class="ay-input ay-textarea"
                                placeholder="Cuéntanos en qué podemos ayudarte…"
                                rows="5" required></textarea>
                        </div>

                        <button type="submit" class="ay-btn-send">
                            <i class="bi bi-send"></i> Enviar mensaje
                        </button>

                    </form>
                </div>
            </div>

            <!-- Columna derecha: contratar servicios -->
            <div class="ay-contact-right" id="servicios">
                <div class="ay-services-card">
                    <div class="ay-services-icon"><i class="bi bi-shop-window"></i></div>
                    <h3 class="ay-services-title">¿Tienes una tienda?</h3>
                    <p class="ay-services-desc">
                        Únete a shoppeo y lleva más clientes a tu negocio.
                        Publicamos tus productos y precios en tiempo real para que los usuarios
                        de tu ciudad te encuentren fácilmente.
                    </p>
                    <ul class="ay-services-list">
                        <li><i class="bi bi-check-circle-fill"></i> Alta gratuita de tu tienda</li>
                        <li><i class="bi bi-check-circle-fill"></i> Panel de administración propio</li>
                        <li><i class="bi bi-check-circle-fill"></i> Actualización de precios en tiempo real</li>
                        <li><i class="bi bi-check-circle-fill"></i> Visibilidad en el mapa interactivo</li>
                        <li><i class="bi bi-check-circle-fill"></i> Estadísticas de visitas</li>
                        <li><i class="bi bi-check-circle-fill"></i> Soporte técnico incluido</li>
                    </ul>
                    <a href="#contacto" class="ay-services-btn"
                        onclick="document.getElementById('ay-asunto').value='contratar';document.getElementById('ay-nombre').focus();return false;">
                        <i class="bi bi-rocket-takeoff"></i> Quiero unirme
                    </a>
                </div>

                <!-- Garantías -->
                <div class="ay-guarantees">
                    <div class="ay-guarantee">
                        <div class="ay-guarantee-icon"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <div class="ay-guarantee-title">Datos protegidos</div>
                            <div class="ay-guarantee-sub">Cumplimos con el RGPD</div>
                        </div>
                    </div>
                    <div class="ay-guarantee">
                        <div class="ay-guarantee-icon"><i class="bi bi-lightning-charge"></i></div>
                        <div>
                            <div class="ay-guarantee-title">Respuesta rápida</div>
                            <div class="ay-guarantee-sub">Menos de 24 horas</div>
                        </div>
                    </div>
                    <div class="ay-guarantee">
                        <div class="ay-guarantee-icon"><i class="bi bi-people"></i></div>
                        <div>
                            <div class="ay-guarantee-title">Soporte humano</div>
                            <div class="ay-guarantee-sub">Sin bots, personas reales</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div><!-- /container -->