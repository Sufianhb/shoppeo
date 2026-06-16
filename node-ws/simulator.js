"use strict";

const bcrypt = require("bcryptjs");
const https = require("https");
const { Pool } = require("pg");

const PIXABAY_KEY = "55712676-40c4fe7304a19189798707f32";
const imgCache = {};

function buscarImagen(termino) {
  if (imgCache[termino]) return Promise.resolve(imgCache[termino]);
  return new Promise((resolve) => {
    const q = encodeURIComponent(termino);
    const url = `https://pixabay.com/api/?key=${PIXABAY_KEY}&q=${q}&image_type=photo&per_page=5&safesearch=true`;
    https
      .get(url, (res) => {
        let data = "";
        res.on("data", (chunk) => (data += chunk));
        res.on("end", () => {
          try {
            const hit = JSON.parse(data).hits?.[0];
            const img = hit?.largeImageURL || hit?.webformatURL || null;
            if (img) imgCache[termino] = img;
            resolve(img);
          } catch {
            resolve(null);
          }
        });
      })
      .on("error", () => resolve(null));
  });
}

//  Datos ficticios
const NOMBRES = [
  "Alejandro",
  "María",
  "Carlos",
  "Laura",
  "Javier",
  "Sofía",
  "David",
  "Elena",
  "Pablo",
  "Nuria",
  "Marcos",
  "Isabel",
  "Rubén",
  "Carmen",
  "Adrián",
  "Lucía",
  "Jorge",
  "Ana",
  "Sergio",
  "Marta",
  "Raúl",
  "Cristina",
  "Iván",
  "Patricia",
  "Óscar",
  "Beatriz",
  "Hugo",
  "Silvia",
  "Antonio",
  "Rosa",
];
const APELLIDOS = [
  "García",
  "Martínez",
  "López",
  "Sánchez",
  "González",
  "Pérez",
  "Rodríguez",
  "Fernández",
  "Jiménez",
  "Ruiz",
  "Hernández",
  "Díaz",
  "Torres",
  "Moreno",
  "Romero",
  "Navarro",
  "Domínguez",
  "Gil",
  "Blanco",
  "Serrano",
];

const TIENDAS_SIM = [
  {
    nombre: "Supermercado Día Express",
    direccion: "Calle Comuneros, 12",
    telefono: "978 100 001",
    lat: 40.343,
    lon: -1.104,
  },
  {
    nombre: "Ferretería San Miguel",
    direccion: "Av. Independencia, 44",
    telefono: "978 100 002",
    lat: 40.347,
    lon: -1.108,
  },
  {
    nombre: "Farmacia Central Teruel",
    direccion: "Plaza del Torico, 3",
    telefono: "978 100 003",
    lat: 40.345,
    lon: -1.106,
  },
  {
    nombre: "Panadería Artesana López",
    direccion: "Calle Nueva, 7",
    telefono: "978 100 004",
    lat: 40.342,
    lon: -1.109,
  },
  {
    nombre: "Librería El Ángel",
    direccion: "Calle Amantes, 18",
    telefono: "978 100 005",
    lat: 40.346,
    lon: -1.107,
  },
  {
    nombre: "Óptica Visión Plus",
    direccion: "Av. Sagunto, 55",
    telefono: "978 100 006",
    lat: 40.344,
    lon: -1.105,
  },
  {
    nombre: "Carnicería Hermanos Gómez",
    direccion: "Mercado Municipal, puesto 12",
    telefono: "978 100 007",
    lat: 40.341,
    lon: -1.103,
  },
  {
    nombre: "Tienda de Deportes Sprint",
    direccion: "Calle Yagüe de Salas, 9",
    telefono: "978 100 008",
    lat: 40.348,
    lon: -1.111,
  },
  {
    nombre: "Papelería y Copistería Ruiz",
    direccion: "Calle Abadía, 22",
    telefono: "978 100 009",
    lat: 40.34,
    lon: -1.102,
  },
  {
    nombre: "Bazar Todo a 1€",
    direccion: "Calle San Francisco, 5",
    telefono: "978 100 010",
    lat: 40.349,
    lon: -1.112,
  },
];

// Ciudades españolas para el seed geográfico masivo
const CIUDADES_ESPANA = [
  { ciudad: "Madrid", lat: 40.4168, lon: -3.7038 },
  { ciudad: "Barcelona", lat: 41.3851, lon: 2.1734 },
  { ciudad: "Valencia", lat: 39.4699, lon: -0.3763 },
  { ciudad: "Sevilla", lat: 37.3891, lon: -5.9845 },
  { ciudad: "Zaragoza", lat: 41.6488, lon: -0.8891 },
  { ciudad: "Málaga", lat: 36.7213, lon: -4.4214 },
  { ciudad: "Murcia", lat: 37.9922, lon: -1.1307 },
  { ciudad: "Palma", lat: 39.5696, lon: 2.6502 },
  { ciudad: "Bilbao", lat: 43.263, lon: -2.935 },
  { ciudad: "Alicante", lat: 38.3452, lon: -0.481 },
  { ciudad: "Córdoba", lat: 37.8882, lon: -4.7794 },
  { ciudad: "Valladolid", lat: 41.6523, lon: -4.7245 },
  { ciudad: "Vigo", lat: 42.2328, lon: -8.7226 },
  { ciudad: "Gijón", lat: 43.5322, lon: -5.6611 },
  { ciudad: "Granada", lat: 37.1773, lon: -3.5986 },
  { ciudad: "A Coruña", lat: 43.3623, lon: -8.4115 },
  { ciudad: "Vitoria", lat: 42.8467, lon: -2.6716 },
  { ciudad: "Pamplona", lat: 42.8169, lon: -1.6432 },
  { ciudad: "Santander", lat: 43.4623, lon: -3.8099 },
  { ciudad: "Almería", lat: 36.8381, lon: -2.4597 },
  { ciudad: "Burgos", lat: 42.3439, lon: -3.6969 },
  { ciudad: "Salamanca", lat: 40.9701, lon: -5.6635 },
  { ciudad: "Toledo", lat: 39.8628, lon: -4.0273 },
  { ciudad: "Cáceres", lat: 39.4753, lon: -6.3724 },
  { ciudad: "Logroño", lat: 42.4667, lon: -2.45 },
  { ciudad: "Huelva", lat: 37.2614, lon: -6.9447 },
  { ciudad: "Badajoz", lat: 38.8794, lon: -6.9706 },
  { ciudad: "San Sebastián", lat: 43.3183, lon: -1.9812 },
  { ciudad: "Albacete", lat: 38.9942, lon: -1.8564 },
  { ciudad: "Teruel", lat: 40.3433, lon: -1.106 },
  { ciudad: "Ceuta", lat: 35.8894, lon: -5.3198 },
  { ciudad: "Melilla", lat: 35.2923, lon: -2.9381 },
  { ciudad: "Lleida", lat: 41.6176, lon: 0.62 },
  { ciudad: "Tarragona", lat: 41.1189, lon: 1.2445 },
  { ciudad: "Girona", lat: 41.9794, lon: 2.8214 },
  { ciudad: "Castellón", lat: 39.9864, lon: -0.0513 },
  { ciudad: "Jaén", lat: 37.7796, lon: -3.7849 },
  { ciudad: "León", lat: 42.5987, lon: -5.5671 },
  { ciudad: "Pontevedra", lat: 42.4336, lon: -8.648 },
  { ciudad: "Lugo", lat: 43.0097, lon: -7.5567 },
  { ciudad: "Ourense", lat: 42.3359, lon: -7.8639 },
  { ciudad: "Zamora", lat: 41.5028, lon: -5.7446 },
  { ciudad: "Ávila", lat: 40.6565, lon: -4.6816 },
  { ciudad: "Segovia", lat: 40.9429, lon: -4.1088 },
  { ciudad: "Soria", lat: 41.7637, lon: -2.4653 },
  { ciudad: "Guadalajara", lat: 40.6326, lon: -3.166 },
  { ciudad: "Cuenca", lat: 40.0704, lon: -2.1374 },
  { ciudad: "Ciudad Real", lat: 38.9848, lon: -3.9274 },
];

const TIPOS_TIENDAS = [
  "Supermercado",
  "Ferretería",
  "Farmacia",
  "Panadería",
  "Electrónica",
  "Deportes",
  "Librería",
  "Bazar",
  "Óptica",
  "Perfumería",
  "Frutería",
  "Carnicería",
];

// Cada producto lleva query en inglés para Pixabay y tipos de tienda compatibles.
const PRODUCTOS_POOL = [
  //  Electrónica
  {
    nombre: 'Smart TV 43" 4K',
    categoria: 2,
    precio: [299, 499],
    query: "smart tv 4k television",
    tipos: ["Electrónica", "Bazar"],
  },
  {
    nombre: 'Smart TV 55" 4K OLED',
    categoria: 2,
    precio: [699, 1199],
    query: "oled television 4k",
    tipos: ["Electrónica", "Bazar"],
  },
  {
    nombre: "Auriculares Bluetooth",
    categoria: 2,
    precio: [29, 89],
    query: "wireless headphones bluetooth",
    tipos: ["Electrónica", "Bazar", "Deportes"],
  },
  {
    nombre: "Auriculares Noise Cancelling",
    categoria: 2,
    precio: [99, 299],
    query: "noise cancelling headphones",
    tipos: ["Electrónica", "Bazar"],
  },
  {
    nombre: 'Tablet 10" 64 GB',
    categoria: 2,
    precio: [149, 299],
    query: "tablet computer device",
    tipos: ["Electrónica", "Bazar", "Librería"],
  },
  {
    nombre: "Altavoz portátil Bluetooth",
    categoria: 2,
    precio: [25, 75],
    query: "portable bluetooth speaker",
    tipos: ["Electrónica", "Bazar", "Deportes"],
  },
  {
    nombre: "Smartphone 128 GB",
    categoria: 2,
    precio: [199, 599],
    query: "smartphone mobile phone",
    tipos: ["Electrónica", "Bazar"],
  },
  {
    nombre: "Smartwatch deportivo",
    categoria: 2,
    precio: [89, 249],
    query: "smartwatch sport fitness watch",
    tipos: ["Electrónica", "Deportes", "Bazar"],
  },
  {
    nombre: "Cámara de acción 4K",
    categoria: 2,
    precio: [79, 149],
    query: "action camera gopro sport",
    tipos: ["Electrónica", "Deportes", "Bazar"],
  },

  //  Electrodomésticos
  {
    nombre: "Microondas 20 L",
    categoria: 1,
    precio: [69, 139],
    query: "microwave oven kitchen",
    tipos: ["Supermercado", "Electrónica", "Bazar", "Ferretería"],
  },
  {
    nombre: "Batidora de vaso 1000W",
    categoria: 1,
    precio: [39, 99],
    query: "blender kitchen appliance",
    tipos: ["Supermercado", "Electrónica", "Bazar"],
  },
  {
    nombre: "Cafetera de goteo",
    categoria: 1,
    precio: [29, 79],
    query: "drip coffee maker machine",
    tipos: ["Supermercado", "Electrónica", "Bazar", "Panadería"],
  },
  {
    nombre: "Cafetera Nespresso",
    categoria: 1,
    precio: [79, 149],
    query: "nespresso capsule coffee machine",
    tipos: ["Supermercado", "Electrónica", "Bazar", "Panadería"],
  },
  {
    nombre: "Aspiradora sin cable",
    categoria: 1,
    precio: [89, 199],
    query: "cordless vacuum cleaner",
    tipos: ["Electrónica", "Bazar", "Ferretería"],
  },
  {
    nombre: "Freidora de aire 5 L",
    categoria: 1,
    precio: [59, 139],
    query: "air fryer kitchen appliance",
    tipos: ["Supermercado", "Electrónica", "Bazar"],
  },
  {
    nombre: "Robot de cocina 1500W",
    categoria: 1,
    precio: [149, 399],
    query: "food processor kitchen robot",
    tipos: ["Supermercado", "Electrónica", "Bazar"],
  },

  //  Panadería / pastelería
  {
    nombre: "Pan de masa madre",
    categoria: 3,
    precio: [2, 5],
    query: "sourdough bread artisan",
    tipos: ["Panadería", "Supermercado", "Frutería"],
  },
  {
    nombre: "Croissant artesano (ud)",
    categoria: 3,
    precio: [1, 3],
    query: "croissant pastry bakery",
    tipos: ["Panadería", "Supermercado"],
  },
  {
    nombre: "Tarta de chocolate entera",
    categoria: 3,
    precio: [12, 28],
    query: "chocolate cake whole dessert",
    tipos: ["Panadería"],
  },
  {
    nombre: "Baguette tradicional",
    categoria: 3,
    precio: [1, 2],
    query: "baguette french bread",
    tipos: ["Panadería", "Supermercado"],
  },
  {
    nombre: "Bizcocho de limón casero",
    categoria: 3,
    precio: [6, 14],
    query: "lemon cake homemade sponge",
    tipos: ["Panadería"],
  },

  //  Alimentación general
  {
    nombre: "Aceite de oliva virgen 1 L",
    categoria: 3,
    precio: [4, 12],
    query: "olive oil bottle extra virgin",
    tipos: ["Supermercado", "Frutería", "Bazar"],
  },
  {
    nombre: "Café molido 500 g",
    categoria: 3,
    precio: [5, 14],
    query: "ground coffee bag roasted",
    tipos: ["Supermercado", "Bazar", "Panadería"],
  },
  {
    nombre: "Zumo de naranja 2 L",
    categoria: 3,
    precio: [2, 5],
    query: "orange juice bottle fresh",
    tipos: ["Supermercado", "Frutería"],
  },
  {
    nombre: "Leche entera 6 ud",
    categoria: 3,
    precio: [5, 9],
    query: "milk carton dairy product",
    tipos: ["Supermercado", "Bazar"],
  },
  {
    nombre: "Arroz redondo 1 kg",
    categoria: 3,
    precio: [1, 3],
    query: "rice bag grain food",
    tipos: ["Supermercado", "Bazar"],
  },

  //  Frutería / carnicería
  {
    nombre: "Cesta de frutas variadas 3 kg",
    categoria: 3,
    precio: [8, 20],
    query: "fruit basket variety fresh",
    tipos: ["Frutería", "Supermercado"],
  },
  {
    nombre: "Filete de ternera 500 g",
    categoria: 3,
    precio: [8, 18],
    query: "beef steak raw meat",
    tipos: ["Carnicería", "Supermercado"],
  },
  {
    nombre: "Pollo entero 1.5 kg",
    categoria: 3,
    precio: [5, 10],
    query: "whole chicken raw poultry",
    tipos: ["Carnicería", "Supermercado"],
  },
  {
    nombre: "Chorizo ibérico 250 g",
    categoria: 3,
    precio: [4, 10],
    query: "chorizo sausage iberian meat",
    tipos: ["Carnicería", "Supermercado"],
  },

  //  Deporte
  {
    nombre: "Zapatillas running",
    categoria: 5,
    precio: [49, 129],
    query: "running shoes sport sneakers",
    tipos: ["Deportes", "Bazar"],
  },
  {
    nombre: "Mochila deportiva 30 L",
    categoria: 5,
    precio: [29, 69],
    query: "sport backpack hiking bag",
    tipos: ["Deportes", "Bazar", "Librería"],
  },
  {
    nombre: "Botella térmica 750 ml",
    categoria: 5,
    precio: [15, 35],
    query: "thermal water bottle sport",
    tipos: ["Deportes", "Bazar", "Frutería"],
  },
  {
    nombre: "Bicicleta estática plegable",
    categoria: 5,
    precio: [199, 499],
    query: "stationary exercise bike indoor",
    tipos: ["Deportes"],
  },
  {
    nombre: "Esterilla de yoga",
    categoria: 5,
    precio: [12, 35],
    query: "yoga mat exercise fitness",
    tipos: ["Deportes", "Bazar"],
  },

  //  Farmacia / Perfumería
  {
    nombre: "Crema hidratante facial 50ml",
    categoria: 4,
    precio: [8, 35],
    query: "face moisturizer cream cosmetic",
    tipos: ["Farmacia", "Perfumería", "Supermercado"],
  },
  {
    nombre: "Protector solar SPF50 200ml",
    categoria: 4,
    precio: [10, 28],
    query: "sunscreen sunblock SPF lotion",
    tipos: ["Farmacia", "Perfumería"],
  },
  {
    nombre: "Perfume floral 50ml",
    categoria: 4,
    precio: [25, 80],
    query: "perfume fragrance bottle floral",
    tipos: ["Perfumería", "Farmacia"],
  },
  {
    nombre: "Gel de ducha hidratante 500ml",
    categoria: 4,
    precio: [3, 10],
    query: "shower gel body wash bottle",
    tipos: ["Perfumería", "Farmacia", "Supermercado"],
  },
  {
    nombre: "Vitamina C 1000mg 60 cáps",
    categoria: 4,
    precio: [8, 22],
    query: "vitamin C supplement capsules",
    tipos: ["Farmacia"],
  },

  //  Óptica
  {
    nombre: "Gafas de sol polarizadas",
    categoria: 4,
    precio: [25, 120],
    query: "polarized sunglasses fashion",
    tipos: ["Óptica", "Bazar", "Deportes"],
  },
  {
    nombre: "Gafas de lectura +2.0",
    categoria: 4,
    precio: [8, 30],
    query: "reading glasses eyewear",
    tipos: ["Óptica", "Farmacia"],
  },

  //  Librería / papelería
  {
    nombre: "Novela bestseller 2024",
    categoria: 4,
    precio: [12, 22],
    query: "book novel reading bestseller",
    tipos: ["Librería"],
  },
  {
    nombre: "Agenda anual A5",
    categoria: 4,
    precio: [8, 18],
    query: "planner diary notebook agenda",
    tipos: ["Librería", "Bazar"],
  },
  {
    nombre: "Set bolígrafos colores 24 ud",
    categoria: 4,
    precio: [5, 15],
    query: "colored pens set stationery",
    tipos: ["Librería", "Bazar"],
  },

  //  Ferretería
  {
    nombre: "Taladro percutor 600W",
    categoria: 1,
    precio: [35, 120],
    query: "power drill electric tool",
    tipos: ["Ferretería", "Bazar"],
  },
  {
    nombre: "Kit destornilladores 18 pzas",
    categoria: 1,
    precio: [12, 35],
    query: "screwdriver set hand tools",
    tipos: ["Ferretería", "Bazar"],
  },
  {
    nombre: "Cinta métrica 5 m",
    categoria: 1,
    precio: [4, 12],
    query: "measuring tape ruler tool",
    tipos: ["Ferretería", "Bazar"],
  },

  //  Hogar deco 
  {
    nombre: "Lámpara de escritorio LED",
    categoria: 4,
    precio: [19, 59],
    query: "LED desk lamp office light",
    tipos: ["Bazar", "Ferretería", "Librería"],
  },
  {
    nombre: "Cojín decorativo 50x50",
    categoria: 4,
    precio: [8, 25],
    query: "decorative cushion pillow sofa",
    tipos: ["Bazar", "Supermercado"],
  },
  {
    nombre: 'Marco de fotos digital 10"',
    categoria: 4,
    precio: [35, 85],
    query: "digital photo frame display",
    tipos: ["Electrónica", "Bazar"],
  },
];

const LOG_MENSAJES = {
  compra: (u, pts, t) =>
    `Usuario ${u} acumuló ${pts} puntos por compra en ${t}`,
  qr: (u, pts) => `Escaneo de QR: usuario ${u} +${pts} puntos`,
  descuento: (u, pct) => `Descuento del ${pct}% aplicado por usuario ${u}`,
  precio: (p, viejo, nv) =>
    `Precio actualizado: ${p} → ${nv} € (antes ${viejo} €)`,
  stock: (p, s) => `Stock actualizado: ${p} → ${s} unidades`,
  usuario: (n, e) => `Nuevo usuario registrado: ${n} (${e})`,
  producto: (p) => `Nuevo producto añadido al catálogo: ${p}`,
  canje: (u, r) => `Canje solicitado: ${u} → ${r}`,
};

//  Pool PostgreSQL
module.exports = async function startSimulator(io) {
  const pool = new Pool({
    host: process.env.DB_HOST || "db",
    port: parseInt(process.env.DB_PORT || "5432"),
    database: process.env.DB_NAME || "shoppeo",
    user: process.env.DB_USER || "tmuser",
    password: process.env.DB_PASS || "tmpassword",
  });

  // Hash de '12345678' precalculado al arrancar (evitar en cada tick)
  const PASS_HASH = await bcrypt.hash("12345678", 10);

  //  Caché
  let users = []; // { id, nombre }
  let precios = []; // { id, producto_id, tienda_id, precio, stock, producto_nombre, tienda_nombre }
  let tiendas = []; // { id, nombre }
  let recompensas = []; // { id, nombre, puntos_necesarios }

  async function loadData() {
    try {
      const [uR, pR, tR, rR] = await Promise.all([
        pool.query(
          "SELECT id, nombre FROM usuarios WHERE activo = TRUE AND rol_id = 2 LIMIT 60",
        ),
        pool.query(`SELECT p.id, p.producto_id, p.tienda_id, p.precio, p.stock,
                           pr.nombre AS producto_nombre, t.nombre AS tienda_nombre
                    FROM precios p
                    JOIN productos pr ON pr.id = p.producto_id
                    JOIN tiendas  t  ON t.id  = p.tienda_id
                    LIMIT 80`),
        pool.query("SELECT id, nombre FROM tiendas LIMIT 20"),
        pool.query(
          "SELECT id, nombre, puntos_necesarios FROM recompensas WHERE activo = TRUE ORDER BY puntos_necesarios",
        ),
      ]);
      if (uR.rows.length) users = uR.rows;
      if (pR.rows.length) precios = pR.rows;
      if (tR.rows.length) tiendas = tR.rows;
      if (rR.rows.length) recompensas = rR.rows;
      console.log(
        `[SIM] Cache: ${users.length} usuarios · ${precios.length} precios · ${tiendas.length} tiendas`,
      );
    } catch (e) {
      console.error("[SIM] loadData:", e.message);
    }
  }

  //  Helpers
  const rand = (arr) => arr[Math.floor(Math.random() * arr.length)];
  const between = (a, b) => Math.floor(Math.random() * (b - a + 1)) + a;
  const now = () => new Date().toISOString();
  const randomPct = () => rand([5, 10, 15, 20, 25]);

  async function insertLog(nivel, mensaje) {
    try {
      await pool.query("INSERT INTO logs (nivel, mensaje) VALUES ($1,$2)", [
        nivel,
        mensaje,
      ]);
    } catch (e) {
      /* silencioso */
    }
  }

  async function insertActividad(
    tipo,
    producto_id,
    tienda_id,
    usuario_id,
    productoNombre,
  ) {
    try {
      await pool.query(
        "INSERT INTO actividad (producto_id, tienda_id, usuario_id, tipo) VALUES ($1,$2,$3,$4)",
        [producto_id || null, tienda_id || null, usuario_id || null, tipo],
      );
      io.emit("nueva_actividad", {
        producto: productoNombre || "Producto simulado",
        tipo,
        created_at: new Date().toISOString(),
      });
    } catch (e) {
      /* silencioso */
    }
  }

  //  Acción 1: fluctuación de precio
  async function simPrecio() {
    if (!precios.length) return;
    const p = rand(precios);
    const anterior = parseFloat(p.precio);
    const factor = 1 + (Math.random() * 0.18 - 0.09);
    const nuevo = Math.max(0.5, parseFloat((anterior * factor).toFixed(2)));

    try {
      await pool.query(
        "UPDATE precios SET precio = $1, actualizado_at = NOW() WHERE id = $2",
        [nuevo, p.id],
      );
      p.precio = nuevo;

      const payload = {
        producto_id: p.producto_id,
        tienda_id: p.tienda_id,
        producto: p.producto_nombre,
        tienda: p.tienda_nombre,
        precio: nuevo,
        precio_anterior: anterior,
        stock: p.stock,
        timestamp: now(),
      };
      io.to(`product_${p.producto_id}`).emit("price_updated", payload);
      io.emit("price_activity", payload);
      await insertLog(
        "info",
        LOG_MENSAJES.precio(p.producto_nombre, anterior, nuevo),
      );
      await insertActividad(
        "view",
        p.producto_id,
        p.tienda_id,
        null,
        p.producto_nombre,
      );
      console.log(`[SIM] Precio: ${p.producto_nombre} ${anterior}→${nuevo}€`);
    } catch (e) {
      console.error("[SIM] simPrecio:", e.message);
    }
  }

  //  Acción 2: cambio de stock
  async function simStock() {
    if (!precios.length) return;
    const p = rand(precios);
    const stock = between(0, 80);

    try {
      await pool.query(
        "UPDATE precios SET stock = $1, actualizado_at = NOW() WHERE id = $2",
        [stock, p.id],
      );
      p.stock = stock;

      io.emit("stock_updated", {
        producto_id: p.producto_id,
        producto: p.producto_nombre,
        tienda: p.tienda_nombre,
        stock,
        timestamp: now(),
      });
      if (stock < 5) {
        await insertLog(
          "warning",
          LOG_MENSAJES.stock(p.producto_nombre, stock),
        );
      } else {
        await insertLog("info", LOG_MENSAJES.stock(p.producto_nombre, stock));
      }
      console.log(`[SIM] Stock: ${p.producto_nombre} → ${stock} uds`);
    } catch (e) {
      console.error("[SIM] simStock:", e.message);
    }
  }

  //  Acción 3-5: puntos (compra / QR / descuento)
  async function simPuntos(tipo) {
    const user = users.length ? rand(users) : null;
    const uid = user?.id ?? null;
    const nombre = user?.nombre ?? rand(NOMBRES);
    const precio_ref = precios.length ? rand(precios) : null;
    const tienda = precio_ref?.tienda_nombre ?? "Tienda local";

    let puntos, concepto, icono, color, bg, detalle, logMsg;

    if (tipo === "compra") {
      const importe = between(5, 200);
      puntos = importe;
      concepto = `Compra en ${tienda}`;
      icono = "bi-bag-check-fill";
      color = "#16a34a";
      bg = "#dcfce7";
      detalle = `Compra de ${importe} € en ${tienda}`;
      logMsg = LOG_MENSAJES.compra(nombre, puntos, tienda);
    } else if (tipo === "qr") {
      puntos = between(5, 20);
      concepto = "Bonus por escaneo de QR shoppeo";
      icono = "bi-qr-code-scan";
      color = "#7c3aed";
      bg = "#ede9fe";
      detalle = "Escaneo de QR en tienda asociada";
      logMsg = LOG_MENSAJES.qr(nombre, puntos);
    } else {
      const pct = randomPct();
      puntos = between(10, 35);
      concepto = `Descuento shoppeo del ${pct}% aplicado`;
      icono = "bi-percent";
      color = "#d97706";
      bg = "#fef3c7";
      detalle = `Descuento del ${pct}% aplicado con shoppeo`;
      logMsg = LOG_MENSAJES.descuento(nombre, pct);
    }

    if (uid) {
      try {
        await pool.query(
          "INSERT INTO puntos_transacciones (usuario_id, puntos, concepto) VALUES ($1,$2,$3)",
          [uid, puntos, concepto],
        );
        // Emitir actualización al room del usuario (página Mis Puntos en tiempo real)
        io.to(`user_${uid}`).emit("puntos_update", {
          puntos_delta: puntos,
          concepto,
          timestamp: now(),
        });
        if (precio_ref) {
          await insertActividad(
            "scan",
            precio_ref.producto_id,
            precio_ref.tienda_id,
            uid,
            precio_ref.producto_nombre,
          );
        }
      } catch (e) {
        console.error(`[SIM] simPuntos(${tipo}):`, e.message);
      }
    }

    io.emit("sim_actividad", {
      tipo,
      icono,
      color,
      bg,
      usuario: nombre,
      detalle,
      puntos: `+${puntos} pts`,
      timestamp: now(),
    });
    await insertLog("info", logMsg);
    console.log(`[SIM] ${tipo}: ${nombre} +${puntos}pts`);
  }

  //  Acción 6: nuevo producto
  async function simNuevoProducto() {
    if (!tiendas.length) return;
    const tienda = rand(tiendas);

    // Determinar tipo de tienda por prefijo del nombre
    const tipo = TIPOS_TIENDAS.find((t) => tienda.nombre.startsWith(t)) || null;
    let compatibles = tipo
      ? PRODUCTOS_POOL.filter((p) => p.tipos.includes(tipo))
      : PRODUCTOS_POOL;
    if (!compatibles.length) compatibles = PRODUCTOS_POOL;

    const plantilla = rand(compatibles);
    const suffix = between(1, 999);
    const nombre = `${plantilla.nombre} #${suffix}`;
    const precio = parseFloat(
      (
        between(plantilla.precio[0] * 100, plantilla.precio[1] * 100) / 100
      ).toFixed(2),
    );
    const stock = between(5, 100);
    const imagen =
      (await buscarImagen(plantilla.query || plantilla.nombre)) || null;

    try {
      const pRes = await pool.query(
        `INSERT INTO productos (nombre, descripcion, categoria_id, imagen_url)
         VALUES ($1, $2, $3, $4) RETURNING id`,
        [nombre, `Producto simulado — ${nombre}`, plantilla.categoria, imagen],
      );
      const productoId = pRes.rows[0].id;

      await pool.query(
        `INSERT INTO precios (producto_id, tienda_id, precio, stock)
         VALUES ($1, $2, $3, $4)
         ON CONFLICT (producto_id, tienda_id) DO UPDATE SET precio = EXCLUDED.precio, stock = EXCLUDED.stock`,
        [productoId, tienda.id, precio, stock],
      );

      // Añadir a caché local
      precios.push({
        id: 0,
        producto_id: productoId,
        tienda_id: tienda.id,
        precio,
        stock,
        producto_nombre: nombre,
        tienda_nombre: tienda.nombre,
      });

      io.emit("sim_actividad", {
        tipo: "producto",
        icono: "bi-box-seam-fill",
        color: "#2563eb",
        bg: "#dbeafe",
        usuario: "Sistema",
        detalle: `Nuevo producto: ${nombre} en ${tienda.nombre}`,
        puntos: `${precio} €`,
        timestamp: now(),
      });
      await insertLog("info", LOG_MENSAJES.producto(nombre));
      console.log(
        `[SIM] Producto: "${nombre}" en ${tienda.nombre} → ${precio}€`,
      );
    } catch (e) {
      console.error("[SIM] simNuevoProducto:", e.message);
    }
  }

  //  Acción 7: nuevo canje
  async function simNuevoCanje() {
    if (!users.length || !recompensas.length) return;
    const recompensa = rand(recompensas);
    const user = rand(users);

    // Comprobar si el usuario tiene suficientes puntos
    try {
      const tRes = await pool.query(
        "SELECT COALESCE(SUM(puntos),0) AS total FROM puntos_transacciones WHERE usuario_id = $1",
        [user.id],
      );
      const total = parseInt(tRes.rows[0].total);
      if (total < recompensa.puntos_necesarios) return; // No tiene suficientes

      await pool.query(
        `INSERT INTO canjes (usuario_id, recompensa_id, puntos_usados, direccion_envio)
         VALUES ($1,$2,$3,$4)`,
        [
          user.id,
          recompensa.id,
          recompensa.puntos_necesarios,
          `Calle Simulada ${between(1, 99)}, ${between(10000, 99999)} España`,
        ],
      );
      await pool.query(
        "INSERT INTO puntos_transacciones (usuario_id, puntos, concepto) VALUES ($1,$2,$3)",
        [user.id, -recompensa.puntos_necesarios, `Canje: ${recompensa.nombre}`],
      );

      io.emit("sim_actividad", {
        tipo: "canje",
        icono: "bi-gift-fill",
        color: "#db2777",
        bg: "#fce7f3",
        usuario: user.nombre,
        detalle: `Canje solicitado: ${recompensa.nombre}`,
        puntos: `-${recompensa.puntos_necesarios} pts`,
        timestamp: now(),
      });
      await insertLog(
        "info",
        LOG_MENSAJES.canje(user.nombre, recompensa.nombre),
      );
      console.log(`[SIM] Canje: ${user.nombre} → ${recompensa.nombre}`);
    } catch (e) {
      console.error("[SIM] simNuevoCanje:", e.message);
    }
  }

  //  Acción extra: nueva tienda 
  async function simNuevaTienda() {
    const ciudad = rand(CIUDADES_ESPANA);
    const tipo = rand(TIPOS_TIENDAS);
    const suffix = between(1, 99);
    const nombre = `${tipo} ${ciudad.ciudad} #${suffix}`;
    const lat = parseFloat(
      (ciudad.lat + (Math.random() * 0.002 - 0.001)).toFixed(6),
    );
    const lon = parseFloat(
      (ciudad.lon + (Math.random() * 0.002 - 0.001)).toFixed(6),
    );
    const tel = `${between(600, 699)} ${between(100000, 999999)}`;
    const dir = `Calle ${rand(["Mayor", "Real", "Nueva", "Ancha", "Alta", "Baja"])} ${between(1, 120)}, ${ciudad.ciudad}`;

    try {
      await pool.query(
        `INSERT INTO tiendas (nombre, direccion, telefono, latitud, longitud)
         VALUES ($1,$2,$3,$4,$5)`,
        [nombre, dir, tel, lat, lon],
      );
      io.emit("nueva_tienda", {
        nombre,
        direccion: dir,
        telefono: tel,
        latitud: lat,
        longitud: lon,
      });
      io.emit("sim_actividad", {
        tipo: "tienda",
        icono: "bi-shop-window",
        color: "#059669",
        bg: "#d1fae5",
        usuario: "Sistema",
        detalle: `Nueva tienda: ${nombre}`,
        puntos: "Nueva",
        timestamp: now(),
      });
      await insertLog(
        "info",
        `Nueva tienda registrada: ${nombre} (${lat}, ${lon})`,
      );
      tiendas.push({ id: 0, nombre });
      console.log(`[SIM] Tienda: ${nombre}`);
    } catch (e) {
      console.error("[SIM] simNuevaTienda:", e.message);
    }
  }

  //  Cadencia separada: nuevo usuario 
  async function simNuevoUsuario() {
    const nombre = `${rand(NOMBRES)} ${rand(APELLIDOS)}`;
    const slug = nombre
      .toLowerCase()
      .replace(/ /g, ".")
      .replace(
        /[áéíóúñ]/g,
        (c) => ({ á: "a", é: "e", í: "i", ó: "o", ú: "u", ñ: "n" })[c] ?? c,
      );
    const email = `${slug}.${between(10, 999)}@shoppeo-sim.es`;

    try {
      await pool.query(
        `INSERT INTO usuarios (nombre, email, password, rol_id)
         VALUES ($1,$2,$3,2) ON CONFLICT (email) DO NOTHING`,
        [nombre, email, PASS_HASH],
      );

      io.emit("sim_actividad", {
        tipo: "registro",
        icono: "bi-person-plus-fill",
        color: "#0891b2",
        bg: "#cffafe",
        usuario: nombre,
        detalle: `Nuevo usuario registrado: ${email}`,
        puntos: "¡Bienvenido!",
        timestamp: now(),
      });
      io.emit("nuevo_usuario", { nombre, email, timestamp: now() });
      await insertLog("info", LOG_MENSAJES.usuario(nombre, email));
      console.log(`[SIM] Usuario: ${nombre} (${email})`);
      users.push({ id: 0, nombre });
    } catch (e) {
      console.error("[SIM] simNuevoUsuario:", e.message);
    }
  }

  //  Seed masivo España 
  async function seedEspana() {
    try {
      // Borrar tiendas del seed anterior 
      await pool.query(`
        DELETE FROM tiendas
        WHERE nombre ~ ' [1-9][0-8]?$'
          AND nombre NOT LIKE '%#%'
      `);
      console.log("[SIM] Tiendas seed anteriores eliminadas");

      console.log("[SIM] Iniciando seed masivo de España...");

      //  Paso 1: insertar los 30 productos comparables (idempotente)
      const productosBase = {}; 
      for (const p of PRODUCTOS_POOL) {
        try {
          // Evitar duplicados
          let existing = await pool.query(
            "SELECT id FROM productos WHERE nombre = $1 LIMIT 1",
            [p.nombre],
          );
          const imagen = (await buscarImagen(p.query || p.nombre)) || null;
          let prodId;
          if (existing.rows.length) {
            prodId = existing.rows[0].id;
            await pool.query(
              "UPDATE productos SET imagen_url = $1 WHERE id = $2",
              [imagen, prodId],
            );
          } else {
            const r = await pool.query(
              `INSERT INTO productos (nombre, descripcion, categoria_id, imagen_url)
               VALUES ($1,$2,$3,$4) RETURNING id`,
              [
                p.nombre,
                `${p.nombre} — disponible en múltiples tiendas`,
                p.categoria,
                imagen,
              ],
            );
            prodId = r.rows[0].id;
          }
          productosBase[p.nombre] = {
            id: prodId,
            rango: p.precio,
            tipos: p.tipos,
          };
        } catch (e) {
          /* skip */
        }
      }
      console.log(
        `[SIM] Seed: ${Object.keys(productosBase).length} productos comparables listos`,
      );

      //  Paso 2: crear entre 5 y 8 tiendas por ciudad
      const calles = [
        "Mayor",
        "Real",
        "Nueva",
        "Ancha",
        "Principal",
        "del Prado",
        "de la Paz",
        "del Carmen",
        "Cervantes",
        "Colón",
      ];
      const tiendasSeed = [];

      for (const ciudad of CIUDADES_ESPANA) {
        const numTiendas = between(5, 8);
        const tiposUsados = [...TIPOS_TIENDAS].sort(() => Math.random() - 0.5);

        for (let i = 0; i < numTiendas; i++) {
          const tipo = tiposUsados[i % tiposUsados.length];
          const nombre = `${tipo} ${ciudad.ciudad} ${i + 1}`;
          const lat = parseFloat(
            (ciudad.lat + (Math.random() * 0.002 - 0.001)).toFixed(6),
          );
          const lon = parseFloat(
            (ciudad.lon + (Math.random() * 0.002 - 0.001)).toFixed(6),
          );
          const tel = `${between(600, 699)} ${between(100000, 999999)}`;
          const dir = `Calle ${rand(calles)} ${between(1, 150)}, ${ciudad.ciudad}`;

          try {
            const res = await pool.query(
              `INSERT INTO tiendas (nombre, direccion, telefono, latitud, longitud)
               VALUES ($1,$2,$3,$4,$5)
               ON CONFLICT DO NOTHING
               RETURNING id, nombre`,
              [nombre, dir, tel, lat, lon],
            );
            if (res.rows.length > 0)
              tiendasSeed.push({
                id: res.rows[0].id,
                nombre: res.rows[0].nombre,
                tipo,
              });
          } catch (e) {
          }
        }
      }
      console.log(
        `[SIM] Seed: ${tiendasSeed.length} tiendas insertadas en ${CIUDADES_ESPANA.length} ciudades`,
      );

      let preciosInsertados = 0;
      const entries = Object.values(productosBase);

      for (const tienda of tiendasSeed) {
        // Filtrar productos compatibles con el tipo de tienda
        let compatibles = entries.filter(
          (p) => p.tipos && p.tipos.includes(tienda.tipo),
        );
        if (compatibles.length < 3) compatibles = entries; // fallback: todos

        for (const prod of compatibles) {
          const precio = parseFloat(
            (between(prod.rango[0] * 100, prod.rango[1] * 100) / 100).toFixed(
              2,
            ),
          );
          const stock = between(0, 80);
          try {
            await pool.query(
              `INSERT INTO precios (producto_id, tienda_id, precio, stock)
               VALUES ($1,$2,$3,$4)
               ON CONFLICT (producto_id, tienda_id) DO NOTHING`,
              [prod.id, tienda.id, precio, stock],
            );
            preciosInsertados++;
          } catch (e) {

          }
        }
      }

      console.log(
        `[SIM] Seed España completado: ${tiendasSeed.length} tiendas · ${preciosInsertados} precios`,
      );
      await insertLog(
        "info",
        `Seed España: ${tiendasSeed.length} tiendas · ${preciosInsertados} precios comparables`,
      );
    } catch (e) {
      console.error("[SIM] seedEspana error:", e.message);
    }
  }

  let tickCount = 0;
  let onlineCount = between(4, 10);

  async function tick() {
    tickCount++;
    if (tickCount % 37 === 0) await loadData(); 

    const r = Math.random();
    try {
      if (r < 0.3) await simPrecio();
      else if (r < 0.45) await simStock();
      else if (r < 0.63) await simPuntos("compra");
      else if (r < 0.75) await simPuntos("qr");
      else if (r < 0.85) await simPuntos("descuento");
      else if (r < 0.93) await simNuevoProducto();
      else await simNuevoCanje();
    } catch (e) {
      console.error("[SIM] tick error:", e.message);
    }

    onlineCount = Math.max(2, Math.min(25, onlineCount + between(-1, 2)));
    io.emit("users_online", { count: onlineCount });
  }

  //  Arranque
  setTimeout(async () => {
    console.log("[SIM] Iniciando simulador...");
    await loadData();
    await seedEspana(); 
    await loadData(); 
    console.log("[SIM] Activo — tick cada 8 s, usuarios cada 50 s");
    setTimeout(tick, 2000);
    setInterval(tick, 8000);
    setInterval(simNuevoUsuario, 50000); 
    setInterval(simNuevaTienda, 120000); 
  }, 8000);
};
