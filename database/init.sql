CREATE EXTENSION IF NOT EXISTS pg_trgm;

CREATE TABLE
    IF NOT EXISTS roles (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL UNIQUE
    );

CREATE TABLE
    IF NOT EXISTS usuarios (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol_id INTEGER NOT NULL DEFAULT 2,
        activo BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT NOW (),
        CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES roles (id)
    );

CREATE TABLE
    IF NOT EXISTS categorias (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL UNIQUE
    );

CREATE TABLE
    IF NOT EXISTS productos (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(200) NOT NULL,
        descripcion TEXT,
        categoria_id INTEGER,
        imagen_url VARCHAR(300),
        activo BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT NOW (),
        CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id)
    );

CREATE INDEX IF NOT EXISTS idx_productos_nombre ON productos USING gin (to_tsvector ('spanish', nombre));

CREATE TABLE
    IF NOT EXISTS tiendas (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        direccion VARCHAR(300),
        telefono VARCHAR(20),
        latitud NUMERIC(10, 7) NOT NULL,
        longitud NUMERIC(10, 7) NOT NULL,
        web VARCHAR(300),
        activa BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE TABLE
    IF NOT EXISTS precios (
        id SERIAL PRIMARY KEY,
        producto_id INTEGER NOT NULL,
        tienda_id INTEGER NOT NULL,
        precio NUMERIC(10, 2) NOT NULL CHECK (precio >= 0),
        stock INTEGER NOT NULL DEFAULT 0,
        actualizado_at TIMESTAMP NOT NULL DEFAULT NOW (),
        CONSTRAINT fk_precio_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE,
        CONSTRAINT fk_precio_tienda FOREIGN KEY (tienda_id) REFERENCES tiendas (id) ON DELETE CASCADE,
        CONSTRAINT uq_precio_producto_tienda UNIQUE (producto_id, tienda_id)
    );

CREATE
OR REPLACE VIEW comparador_precios AS
SELECT
    pr.id AS precio_id,
    p.id AS producto_id,
    p.nombre AS producto,
    p.descripcion,
    p.imagen_url,
    c.nombre AS categoria,
    t.id AS tienda_id,
    t.nombre AS tienda,
    t.direccion,
    t.telefono,
    t.latitud,
    t.longitud,
    t.web,
    pr.precio,
    pr.stock,
    pr.actualizado_at,
    MIN(pr.precio) OVER (
        PARTITION BY
            p.id
    ) AS precio_minimo,
    MAX(pr.precio) OVER (
        PARTITION BY
            p.id
    ) AS precio_maximo,
    RANK() OVER (
        PARTITION BY
            p.id
        ORDER BY
            pr.precio ASC
    ) AS rank_precio
FROM
    precios pr
    JOIN productos p ON p.id = pr.producto_id
    JOIN tiendas t ON t.id = pr.tienda_id
    LEFT JOIN categorias c ON c.id = p.categoria_id
WHERE
    p.activo = TRUE
    AND t.activa = TRUE;

INSERT INTO
    roles (nombre)
VALUES
    ('admin'),
    ('usuario') ON CONFLICT DO NOTHING;

INSERT INTO
    usuarios (nombre, email, password, rol_id)
VALUES
    (
        'Administrador',
        'admin@shoppeo.es',
        '$2y$12$yhy.lXNzbpGEmlia7vpwPek/8BnXB7qnTZfzXnd..uCv6IgawkAYG',
        1
    ),
    (
        'María García',
        'maria@example.com',
        '$2y$12$yhy.lXNzbpGEmlia7vpwPek/8BnXB7qnTZfzXnd..uCv6IgawkAYG',
        2
    ) ON CONFLICT DO NOTHING;

INSERT INTO
    categorias (nombre)
VALUES
    ('Electrodomésticos'),
    ('Electrónica'),
    ('Alimentación'),
    ('Hogar'),
    ('Deportes') ON CONFLICT DO NOTHING;

INSERT INTO
    tiendas (
        nombre,
        direccion,
        telefono,
        latitud,
        longitud,
        web
    )
VALUES
    (
        'ElectroTeruel',
        'Calle del Salvador, 14, Teruel',
        '978 123 456',
        40.3456000,
        -1.1065000,
        'https://i.postimg.cc/yxz99C4r/proyectoshoppeosufian.png'
    ),
    (
        'MediaMarket Teruel',
        'Av. Sagunto, 88, Teruel',
        '978 234 567',
        40.3412000,
        -1.1098000,
        NULL
    ),
    (
        'Carrefour Teruel',
        'Ronda de Ambeles, 32, Teruel',
        '978 345 678',
        40.3389000,
        -1.1150000,
        'https://i.postimg.cc/yxz99C4r/proyectoshoppeosufian.png'
    ),
    (
        'El Corte Turolense',
        'Plaza del Torico, 1, Teruel',
        '978 456 789',
        40.3441000,
        -1.1062000,
        NULL
    ),
    (
        'TechShop Teruel',
        'Calle Yagüe, 5, Teruel',
        '978 567 890',
        40.3478000,
        -1.1030000,
        'https://i.postimg.cc/yxz99C4r/proyectoshoppeosufian.png'
    ) ON CONFLICT DO NOTHING;

INSERT INTO
    productos (nombre, descripcion, categoria_id, imagen_url)
VALUES
    (
        'Air Fryer Cecotec Cecofry 6000',
        'Freidora de aire sin aceite 6 litros, 1700W, con pantalla digital y 12 programas preconfigurados.',
        1,
        'https://thumb.pccomponentes.com/w-530-530/articles/1067/10674305/1899-cecotec-cecofry-bombastik-6000-full-freidora-sin-aceite-6l-1700w-32731783-a375-4964-9309-90811b6f4c1f.jpg'
    ),
    (
        'Smart TV Samsung 55" 4K UHD',
        'Televisor QLED 55 pulgadas, resolución 4K, sistema operativo Tizen, HDR10+.',
        2,
        'https://img.pccomponentes.com/articles/1084/10849160/1658-samsung-ue55du7172u-55-led-ultrahd-4k-hdr10.jpg'
    ),
    (
        'Robot aspirador Roomba i5',
        'Robot aspirador y fregasuelos con mapeo inteligente, compatible con Alexa y Google Home.',
        1,
        'https://www.irobot.es/on/demandware.static/-/Sites-master-catalog-irobot/default/dwc0e65e6c/images/large/roomba/I565240_2.jpg'
    ),
    (
        'PlayStation 5 Slim Digital',
        'Consola PS5 edición Slim, versión digital sin lector de discos, 1TB SSD.',
        2,
        'https://cdn.idealo.com/folder/Product/203554/8/203554857/s11_produktbild_gross/sony-playstation-5-slim-ps5-slim-digital-edition.jpg'
    ),
    (
        'Cafetera Nespresso Vertuo Pop',
        'Cafetera de cápsulas con centrifusión, 5 tamaños de taza, 1500ml depósito.',
        1,
        'https://www.nespresso.com/coffee-blog/sites/default/files/2022-11/01_img_1_1142x644_cabecera_4.jpg'
    ),
    (
        'Monitor LG 27" IPS 144Hz',
        'Monitor gaming Full HD 1ms, FreeSync Premium, HDMI 2.0.',
        2,
        'https://www.lg.com/content/dam/channel/wcms/es/images/monitores/27gn650-b_aeu_eees_es_c/gallery/27gn650-2.jpg'
    ),
    (
        'Silla Ergonómica Markus',
        'Silla de oficina con soporte lumbar, altura ajustable, respaldo de malla.',
        4,
        'https://i.blogs.es/fb5f5c/sillas-ikea/1200_630.jpg'
    ),
    (
        'iPhone 15 128GB',
        'Smartphone Apple iPhone 15, pantalla Super Retina XDR 6.1", chip A16 Bionic.',
        2,
        'https://images.milanuncios.com/api/v1/ma-ad-media-pro/images/d5294d3c-bd15-463f-a6bf-6ecfad1b2c75?rule=hw396_70'
    ) ON CONFLICT DO NOTHING;

INSERT INTO
    precios (producto_id, tienda_id, precio, stock)
VALUES
    (1, 1, 59.99, 8),
    (1, 2, 64.90, 15),
    (1, 3, 57.50, 3),
    (1, 4, 62.00, 0),
    (2, 1, 699.00, 4),
    (2, 2, 679.00, 7),
    (2, 3, 689.99, 2),
    (2, 5, 659.00, 10),
    (3, 1, 349.00, 5),
    (3, 2, 339.00, 9),
    (3, 4, 359.99, 1),
    (4, 1, 449.99, 6),
    (4, 2, 449.00, 12),
    (4, 5, 445.00, 4),
    (5, 1, 59.00, 20),
    (5, 3, 54.90, 8),
    (5, 4, 62.99, 3),
    (6, 2, 229.00, 5),
    (6, 5, 219.99, 8),
    (7, 3, 149.00, 10),
    (7, 4, 159.90, 4),
    (8, 1, 979.00, 6),
    (8, 2, 959.00, 9),
    (8, 5, 949.00, 3) ON CONFLICT DO NOTHING;

INSERT INTO
    tiendas (
        nombre,
        direccion,
        telefono,
        latitud,
        longitud,
        web
    )
VALUES
    (
        'MediaMarkt Madrid Centro',
        'Calle Preciados, 1, Madrid',
        '915 123 111',
        40.4180000,
        -3.7058000,
        'https://mediamarkt.es'
    ),
    (
        'El Corte Inglés Castellana',
        'Paseo de la Castellana, 85, Madrid',
        '915 567 222',
        40.4450000,
        -3.6919000,
        'https://elcorteingles.es'
    ),
    (
        'Fnac Callao',
        'Plaza del Callao, 4, Madrid',
        '915 789 333',
        40.4203000,
        -3.7050000,
        'https://fnac.es'
    ),
    (
        'Carrefour Madrid Sur',
        'Av. de Andalucía, 10, Madrid',
        '914 222 444',
        40.3702000,
        -3.6955000,
        'https://carrefour.es'
    ),
    (
        'Worten Madrid Río',
        'Paseo de Extremadura, 120, Madrid',
        '913 333 555',
        40.4078000,
        -3.7392000,
        'https://worten.es'
    ) ON CONFLICT DO NOTHING;

CREATE TABLE
    IF NOT EXISTS actividad (
        id SERIAL PRIMARY KEY,
        producto_id INT REFERENCES productos (id) ON DELETE CASCADE,
        tienda_id INT REFERENCES tiendas (id) ON DELETE SET NULL,
        usuario_id INT NULL,
        tipo VARCHAR(20) NOT NULL DEFAULT 'view',
        created_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE INDEX IF NOT EXISTS idx_actividad_producto ON actividad (producto_id);

CREATE INDEX IF NOT EXISTS idx_actividad_created ON actividad (created_at);

CREATE TABLE
    IF NOT EXISTS configuracion (
        id SERIAL PRIMARY KEY,
        clave VARCHAR(100) NOT NULL UNIQUE,
        valor TEXT NOT NULL DEFAULT ''
    );

INSERT INTO
    configuracion (clave, valor)
VALUES
    ('site_name', 'Shoppeo'),
    ('contact_email', 'admin@shoppeo.es'),
    ('ciudad_default', 'Teruel'),
    ('moneda', 'EUR'),
    ('items_por_pagina', '20') ON CONFLICT (clave) DO NOTHING;

CREATE TABLE
    IF NOT EXISTS logs (
        id SERIAL PRIMARY KEY,
        nivel VARCHAR(20) NOT NULL DEFAULT 'info',
        mensaje TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE INDEX IF NOT EXISTS idx_logs_created ON logs (created_at);

INSERT INTO
    logs (nivel, mensaje)
VALUES
    ('info', 'Sistema inicializado correctamente'),
    (
        'info',
        'Base de datos poblada con datos de prueba'
    ) ON CONFLICT DO NOTHING;

CREATE TABLE
    IF NOT EXISTS favoritos (
        id SERIAL PRIMARY KEY,
        usuario_id INT NOT NULL,
        producto_id INT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT NOW (),
        CONSTRAINT fk_favorito_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
        CONSTRAINT fk_favorito_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE,
        CONSTRAINT uq_favorito UNIQUE (usuario_id, producto_id)
    );

CREATE INDEX IF NOT EXISTS idx_favoritos_usuario ON favoritos (usuario_id);

CREATE TABLE
    IF NOT EXISTS recompensas (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(120) NOT NULL,
        descripcion TEXT,
        emoji VARCHAR(10) NOT NULL DEFAULT '🎁',
        puntos_necesarios INT NOT NULL,
        stock INT DEFAULT -1,
        activo BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE TABLE
    IF NOT EXISTS puntos_transacciones (
        id SERIAL PRIMARY KEY,
        usuario_id INT NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE,
        puntos INT NOT NULL,
        concepto VARCHAR(200) NOT NULL,
        admin_id INT REFERENCES usuarios (id) ON DELETE SET NULL,
        created_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE INDEX IF NOT EXISTS idx_puntos_usuario ON puntos_transacciones (usuario_id);

CREATE TABLE
    IF NOT EXISTS canjes (
        id SERIAL PRIMARY KEY,
        usuario_id INT NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE,
        recompensa_id INT NOT NULL REFERENCES recompensas (id),
        puntos_usados INT NOT NULL,
        estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
        direccion_envio TEXT,
        notas_admin TEXT,
        admin_id INT REFERENCES usuarios (id) ON DELETE SET NULL,
        created_at TIMESTAMP NOT NULL DEFAULT NOW (),
        actualizado_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE INDEX IF NOT EXISTS idx_canjes_usuario ON canjes (usuario_id);

CREATE INDEX IF NOT EXISTS idx_canjes_estado ON canjes (estado);

INSERT INTO
    recompensas (
        nombre,
        descripcion,
        emoji,
        puntos_necesarios,
        stock
    )
VALUES
    (
        'Bolsa reutilizable shoppeo',
        'Bolsa de tela ecológica con el logo de shoppeo',
        '🛍️',
        150,
        -1
    ),
    (
        'Llavero con linterna LED',
        'Llavero multifunción con pequeña linterna LED',
        '🔑',
        200,
        -1
    ),
    (
        'Guantes táctiles',
        'Guantes compatibles con pantallas táctiles, talla única',
        '🧤',
        300,
        -1
    ),
    (
        'Kit de viaje',
        'Neceser con champú, gel y crema solar en formato miniatura',
        '🧴',
        450,
        -1
    ),
    (
        'Paraguas plegable',
        'Paraguas compacto y resistente con funda incluida',
        '☂️',
        600,
        -1
    ),
    (
        'Vale €5 en tiendas locales',
        'Descuento canjeable en cualquier tienda asociada a shoppeo',
        '🎟️',
        1000,
        -1
    ),
    (
        'Cesta de productos artesanales',
        'Selección de productos locales de Teruel',
        '🧺',
        1500,
        50
    ),
    (
        'Mando retro gaming',
        'Mando inalámbrico estilo retro compatible con PC y móvil',
        '🎮',
        2000,
        -1
    ),
    (
        'Power bank 10000mAh',
        'Batería portátil de alta capacidad con 2 puertos USB',
        '🔋',
        2500,
        -1
    ),
    (
        'Auriculares Bluetooth',
        'Auriculares inalámbricos con micrófono y 20h de batería',
        '🎧',
        4000,
        -1
    ),
    (
        'Cafetera de cápsulas',
        'Cafetera compacta compatible Nespresso + 20 cápsulas incluidas',
        '☕',
        6000,
        -1
    ),
    (
        'Smartwatch básico',
        'Reloj inteligente con monitor de actividad y notificaciones',
        '⌚',
        10000,
        20
    ),
    (
        'Smartphone Android',
        'Smartphone 3GB RAM / 64GB almacenamiento, desbloqueado',
        '📱',
        18000,
        10
    ) ON CONFLICT DO NOTHING;

CREATE TABLE
    IF NOT EXISTS descuentos (
        id SERIAL PRIMARY KEY,
        codigo VARCHAR(20) NOT NULL UNIQUE,
        nombre VARCHAR(120) NOT NULL,
        descripcion TEXT,
        emoji VARCHAR(10) NOT NULL DEFAULT '🏷️',
        tipo VARCHAR(12) NOT NULL DEFAULT 'porcentaje',
        valor NUMERIC(6, 2) NOT NULL,
        categoria_id INT REFERENCES categorias (id) ON DELETE SET NULL,
        min_compra NUMERIC(8, 2) NOT NULL DEFAULT 0,
        max_usos INT NOT NULL DEFAULT -1,
        usos_actuales INT NOT NULL DEFAULT 0,
        activo BOOLEAN NOT NULL DEFAULT TRUE,
        fecha_fin DATE,
        created_at TIMESTAMP NOT NULL DEFAULT NOW ()
    );

CREATE INDEX IF NOT EXISTS idx_descuentos_activo ON descuentos (activo);

INSERT INTO
    descuentos (
        codigo,
        nombre,
        descripcion,
        emoji,
        tipo,
        valor,
        categoria_id,
        min_compra,
        max_usos,
        fecha_fin
    )
VALUES
    (
        'ELEC10',
        'Electrónica −10%',
        '10 % de descuento en todos los artículos de electrónica.',
        '💻',
        'porcentaje',
        10,
        2,
        0,
        -1,
        '2026-09-30'
    ),
    (
        'FOOD15',
        'Alimentación −15%',
        '15 % de descuento en productos de alimentación. Compra mínima 15 €.',
        '🛒',
        'porcentaje',
        15,
        3,
        15,
        -1,
        '2026-06-30'
    ),
    (
        'SPORT20',
        'Deportes −20%',
        '20 % de descuento en artículos deportivos.',
        '🏃',
        'porcentaje',
        20,
        5,
        25,
        200,
        '2026-08-15'
    ),
    (
        'HOGAR15',
        'Hogar −15%',
        '15 % de descuento en artículos para el hogar.',
        '🏠',
        'porcentaje',
        15,
        4,
        0,
        -1,
        '2026-10-31'
    ),
    (
        'SAVE5',
        '5 € de descuento',
        '5 € de descuento en cualquier compra superior a 50 €.',
        '💶',
        'fijo',
        5,
        NULL,
        50,
        -1,
        '2026-07-15'
    ),
    (
        'TECH25',
        'Oferta flash electrónica',
        '25 % en electrónica — ¡Oferta limitada a 50 usos!',
        '⚡',
        'porcentaje',
        25,
        2,
        30,
        50,
        '2026-05-31'
    ),
    (
        'ELDOM10',
        'Electrodomésticos −10%',
        '10 % en electrodomésticos del hogar.',
        '🔌',
        'porcentaje',
        10,
        1,
        0,
        -1,
        '2026-12-31'
    ),
    (
        'VERANO30',
        'Verano deportivo −30%',
        '30 % en deporte y ocio. ¡Prepara el verano con shoppeo!',
        '☀️',
        'porcentaje',
        30,
        5,
        20,
        100,
        '2026-08-31'
    ),
    (
        'NUEVO10',
        'Bienvenida −10%',
        '10 % de descuento de bienvenida para nuevos usuarios shoppeo.',
        '🎉',
        'porcentaje',
        10,
        NULL,
        0,
        -1,
        '2026-12-31'
    ),
    (
        'FLASH50',
        '¡Flash Sale −50%!',
        '50 % en electrónica — Solo 10 usos disponibles. ¡Corre!',
        '🔥',
        'porcentaje',
        50,
        2,
        50,
        10,
        '2026-05-15'
    ) ON CONFLICT DO NOTHING;