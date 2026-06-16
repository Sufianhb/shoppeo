# Shoppeo

Shoppeo is a web application developed as a final degree project for the Higher Vocational Training Programme in Web Application Development (DAW).

The platform allows users to search products available in local stores, compare prices across nearby businesses and locate shops through geolocation services. Price changes are delivered in real time using WebSocket communication.

## Features

- Product search with autocomplete.
- Local price comparison.
- Filtering by category, distance and price range.
- Store geolocation.
- Real-time price updates.
- User authentication and authorization.
- Administration dashboard.
- Rewards and discount system.

## Technology Stack

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

### Real-Time Communication

- Node.js
- Socket.io

### Infrastructure

- Docker
- Docker Compose

## Architecture

The application follows a custom MVC architecture without external frameworks.

The platform is composed of multiple Docker services:

- PHP Application
- PostgreSQL Database
- Redis Cache
- Node.js WebSocket Server

## Installation

```bash
git clone https://github.com/Sufianhb/shoppeo.git

cd shoppeo

docker-compose up -d
```

## Screenshots

Coming soon.

## Author

Sufian Hossain Badri
