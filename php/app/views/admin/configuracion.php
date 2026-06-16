<!-- Vista: admin/configuracion.php -->

<h1 class="ad-page-title">Configuración</h1>
<p class="ad-page-sub">Ajustes generales del sistema</p>

<!-- Flash (reuse Bootstrap alert since layout injects flash above, but showing inline form errors here if needed) -->

<div style="max-width:680px;margin:0 auto;">

    <div class="wz-card">
        <form method="POST" action="/admin/configuracion">

            <?php
            /* Etiquetas legibles para cada clave */
            $labels = [
                'site_name'       => ['Nombre del sitio',        'bi-globe',         'Nombre que aparece en el título de la web'],
                'contact_email'   => ['Email de contacto',       'bi-envelope',      'Dirección para notificaciones del sistema'],
                'ciudad_default'  => ['Ciudad por defecto',      'bi-geo-alt',       'Ciudad que se muestra en el mapa y footer'],
                'moneda'          => ['Moneda',                   'bi-currency-euro', 'Código ISO de la moneda (EUR, USD…)'],
                'items_por_pagina' => ['Resultados por página',    'bi-list-ol',       'Número de items en listados paginados'],
            ];
            ?>

            <?php foreach ($config as $row): ?>
                <?php
                $clave = $row['clave'];
                [$label, $icon, $hint] = $labels[$clave] ?? [$clave, 'bi-gear', ''];
                ?>
                <div class="wz-field">
                    <label class="wz-label" for="cfg-<?= htmlspecialchars($clave) ?>">
                        <i class="bi <?= $icon ?>" style="color:#7c3aed;margin-right:6px;"></i>
                        <?= htmlspecialchars($label) ?>
                    </label>
                    <input type="text"
                        id="cfg-<?= htmlspecialchars($clave) ?>"
                        name="config[<?= htmlspecialchars($clave) ?>]"
                        class="wz-input"
                        value="<?= htmlspecialchars($row['valor']) ?>">
                    <?php if ($hint): ?>
                        <p class="wz-hint"><?= htmlspecialchars($hint) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (empty($config)): ?>
                <div style="text-align:center;padding:40px 0;color:#9ca3af;">
                    <i class="bi bi-database-x" style="font-size:36px;display:block;margin-bottom:12px;"></i>
                    <p>No hay configuraciones en la base de datos.<br>
                        Ejecuta las migraciones SQL para crearlas.</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($config)): ?>
                <div class="wz-actions">
                    <button type="submit" class="wz-btn-primary">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                    <a href="/admin" class="wz-btn-ghost">Cancelar</a>
                </div>
            <?php endif; ?>

        </form>
    </div>

</div>