# Shoppeo

Shoppeo es una aplicación web desarrollada como Proyecto Final del Ciclo Formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW).

La plataforma permite consultar productos disponibles en comercios físicos, comparar precios entre establecimientos cercanos y localizar tiendas mediante geolocalización. El sistema incorpora actualizaciones en tiempo real para reflejar cambios de precio sin necesidad de recargar la página.

## Funcionalidades

- Búsqueda de productos con autocompletado.
- Comparación de precios entre diferentes establecimientos.
- Filtrado por categoría, distancia y rango de precios.
- Geolocalización de comercios cercanos.
- Actualización de precios en tiempo real mediante WebSockets.
- Sistema de autenticación y gestión de usuarios.
- Panel de administración para la gestión de productos, categorías y tiendas.
- Sistema de puntos y descuentos.

## Tecnologías utilizadas

### Backend

- PHP 8.2
- Apache 2.4
- PostgreSQL 16
- Redis

### Frontend

- HTML5
- CSS3
- Bootstrap 5
- JavaScript ES6+

### Tiempo real

- Node.js
- Socket.io

### Infraestructura

- Docker
- Docker Compose

## Arquitectura

La aplicación sigue una arquitectura MVC desarrollada sin frameworks externos.

Los distintos servicios se ejecutan mediante contenedores Docker independientes:

- Aplicación PHP
- PostgreSQL
- Redis
- Servidor WebSocket Node.js

## Instalación

```bash
git clone https://github.com/Sufianhb/shoppeo.git

cd shoppeo

docker-compose up -d
```

## Capturas

Próximamente.

## Autor

Sufian Hossain Badri
