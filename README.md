# shoppeo — Comparador de Precios inteligente del Comercio Local

> Tecnologías: PHP 8, PostgreSQL, Node.js, Socket.io, Bootstrap 5, Leaflet.js y Docker

---

## Estructura del proyecto

```
shoppeo/
├── docker-compose.yml              # Orquestación de servicios
├── docker/
│   └── php/
│       └── Dockerfile              # Imagen PHP 8.2 + Apache + pdo_pgsql
├── database/
│   └── init.sql                    # Esquema BD + datos de prueba
├── node-ws/
│   ├── Dockerfile
│   ├── package.json
│   └── server.js                   # Servidor WebSocket (Socket.io)
└── php/
    ├── public/                     # Document root de Apache
    │   ├── index.php               # Front Controller (punto de entrada único)
    │   ├── .htaccess               # mod_rewrite → MVC routing
    │   ├── css/
    │   │   ├── app.css
    │   │   └── admin.css
    │   └── js/
    │       ├── app.js              # Lógica frontend general (AJAX/Fetch)
    │       ├── admin.js            # Lógica panel admin
    │       └── websocket.js        # Cliente Socket.io global
    └── app/
        ├── core/
        │   ├── Router.php          # Router MVC con segmentos dinámicos
        │   ├── Database.php        # Singleton PDO → PostgreSQL
        │   ├── Controller.php      # Controlador base
        │   └── Model.php           # Modelo base
        ├── controllers/
        │   ├── AuthController.php  # Login / Logout / Registro
        │   ├── PublicController.php# Páginas públicas
        │   ├── AdminController.php # Panel de administración
        │   └── ApiController.php   # Endpoints JSON para AJAX
        ├── models/
        │   ├── UsuarioModel.php
        │   ├── ProductoModel.php
        │   ├── PrecioModel.php
        │   ├── TiendaModel.php
        │   └── CategoriaModel.php
        └── views/
            ├── layouts/
            │   ├── main.php        # Layout público (navbar + footer)
            │   ├── admin.php       # Layout admin (sidebar)
            │   └── 404.php
            ├── auth/
            │   ├── login.php
            │   └── registro.php
            ├── public/
            │   ├── inicio.php      # Home con hero + buscador
            │   ├── buscar.php      # Grid de resultados
            │   ├── producto.php    # Comparador + mini-mapa
            │   └── mapa.php        # Mapa Leaflet interactivo
            └── admin/
                ├── dashboard.php
                ├── productos.php
                ├── producto_form.php
                ├── precios.php
                └── tiendas.php
```

---

## Puesta en marcha

### Prerrequisitos
- [Docker Desktop](https://www.docker.com/products/docker-desktop) instalado y en ejecución.
- Puertos libres: `8080` (PHP), `3000` (Node.js WS), `5432` (PostgreSQL).

### 1. Clonar/copiar el proyecto
```bash
git clone <repo> shoppeo
cd shoppeo
```

### 2. Levantar los servicios
```bash
docker compose up --build
```
La primera vez Docker:
1. Construye la imagen PHP con la extensión `pdo_pgsql`.
2. Lanza PostgreSQL y ejecuta `database/init.sql` automáticamente.
3. Construye el servidor Node.js e instala dependencias npm.

### 3. Acceder a la aplicación
| Servicio | URL |
|---|---|
| Aplicación web | http://localhost:8080 |
| WebSocket (health) | http://localhost:3000/health |
| PostgreSQL | localhost:5432 (tmuser / tmpassword) |

### 4. Credenciales de demo
| Rol | Email | Contraseña |
|---|---|---|
| Admin | admin@shoppeo.es | admin123 |
| Usuario | maria@example.com | user123 |

---

## Rutas de la aplicación

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/` | Página de inicio |
| GET | `/buscar?q=termino` | Búsqueda de productos |
| GET | `/producto/{id}` | Comparador de precios de un producto |
| GET | `/mapa` | Mapa interactivo con Leaflet.js |
| GET/POST | `/login` | Inicio de sesión |
| GET/POST | `/registro` | Registro de usuario |
| GET | `/logout` | Cerrar sesión |
| GET | `/admin` | Dashboard admin |
| GET | `/admin/productos` | CRUD de productos |
| GET | `/admin/precios` | Actualización de precios |
| GET | `/admin/tiendas` | Gestión de tiendas |
| GET | `/api/productos?q=` | API JSON — búsqueda AJAX |
| GET | `/api/productos/{id}` | API JSON — detalle producto |
| GET | `/api/mapa` | API JSON — datos mapa |

---

## Arquitectura MVC

```
Browser → HTTP Request
    ↓
public/index.php  (Front Controller)
    ↓
core/Router.php   (mapea URI → Controlador::Acción)
    ↓
controllers/XxxController.php
    ├── llama a models/XxxModel.php  (consultas PDO a PostgreSQL)
    └── renderiza views/xxx/yyy.php  (inyectado en layouts/)
    
Para peticiones AJAX:
Browser ──fetch()──→ /api/xxx → ApiController → JSON response

Para tiempo real:
AdminController ──HTTP POST──→ node-ws:3000/internal/notify
                                    ↓ Socket.io emit
                               Browser ← price_updated event
```

---

## Justificación técnica

### PHP 8 + Arquitectura MVC
PHP 8 introduce **JIT compilation**, **Named Arguments**, tipos de unión (`int|false`) y `match` expressions que mejoran la legibilidad y robustez. La arquitectura MVC separa claramente las responsabilidades: el Modelo gestiona los datos (PDO + PostgreSQL), el Controlador contiene la lógica de negocio, y la Vista se ocupa exclusivamente de la presentación. Esta separación facilita el mantenimiento, las pruebas y la escalabilidad.

### PDO + PostgreSQL
PDO (PHP Data Objects) proporciona una capa de abstracción sobre la base de datos con **consultas preparadas** (`prepare` + `execute`) que parametrizan los valores separándolos del SQL, eliminando completamente la posibilidad de **SQL Injection**. PostgreSQL fue elegido sobre MySQL por su soporte nativo de tipos JSON (`json_agg`, `json_build_object`), el operador `UPSERT` (`ON CONFLICT DO UPDATE`), las **Window Functions** (`RANK() OVER PARTITION BY`) usadas en la vista `comparador_precios`, y su mayor conformidad con el estándar SQL.

### Node.js + Socket.io (WebSockets)
PHP opera en un modelo **request-response sincrónico**: no puede "empujar" datos al cliente sin que éste lo solicite. Para implementar actualizaciones en **tiempo real**, se usa un servidor Node.js independiente con Socket.io, que mantiene conexiones WebSocket persistentes con los navegadores. Cuando un administrador actualiza un precio, PHP llama al endpoint interno del servidor Node.js, que a su vez emite el evento a todos los clientes suscritos en milisegundos, sin que el usuario tenga que recargar la página.

### Docker + Docker Compose
Docker garantiza que la aplicación funciona de forma **idéntica en cualquier entorno** (desarrollo, CI/CD, producción). Docker Compose define los tres servicios (`db`, `php`, `websocket`) con sus dependencias y red interna, permitiendo levantar todo el stack con un solo comando (`docker compose up`). Esto elimina el problema clásico de "en mi máquina funciona" y facilita la corrección y evaluación del TFG.

### Leaflet.js
Leaflet es la librería de mapas **open source** más popular del ecosistema web. A diferencia de Google Maps, no requiere API key ni facturación. Usa tiles de OpenStreetMap (datos geográficos libres), admite marcadores personalizados y popups ricos en HTML, y añade solo ~40KB al bundle.

### Bootstrap 5 + JavaScript vanilla + Fetch API
Bootstrap 5 proporciona un sistema de grid responsive, componentes accesibles y utilidades CSS que aceleran el desarrollo frontend sin sacrificar calidad. El uso de **JavaScript vanilla** con la **Fetch API** (nativa en todos los navegadores modernos) evita dependencias innecesarias como jQuery, reduciendo el peso de la página y demostrando dominio del lenguaje sin abstracciones.

---

## Seguridad implementada

| Vector | Medida |
|---|---|
| SQL Injection | PDO con consultas preparadas en todos los modelos |
| XSS | `htmlspecialchars()` en todas las vistas, `ENT_QUOTES` |
| CSRF | Validación de roles + sesiones con `session_regenerate_id()` |
| Session Fixation | `session_regenerate_id(true)` tras login exitoso |
| Clickjacking | Cabecera `X-Frame-Options: SAMEORIGIN` en `.htaccess` |
| Contraseñas | `password_hash()` con bcrypt (cost=12) + `password_verify()` |
| Acceso admin | Middleware `requireAdmin()` en cada acción protegida |
| API interna WS | Header secreto `X-Ws-Secret` entre PHP y Node.js |

---   

## Autor

Sufian Hossain Badri
