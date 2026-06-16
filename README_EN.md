# shoppeo — Smart Local Commerce Price Comparison Platform

> Technologies: PHP 8, PostgreSQL, Node.js, Socket.io, Bootstrap 5, Leaflet.js, and Docker

---

## Project Structure

```text
shoppeo/
├── docker-compose.yml              # Service orchestration
├── docker/
│   └── php/
│       └── Dockerfile              # PHP 8.2 + Apache + pdo_pgsql image
├── database/
│   └── init.sql                    # Database schema and sample data
├── node-ws/
│   ├── Dockerfile
│   ├── package.json
│   └── server.js                   # Socket.io WebSocket server
└── php/
    ├── public/                     # Apache document root
    │   ├── index.php               # Front Controller
    │   ├── .htaccess               # MVC routing via mod_rewrite
    │   ├── css/
    │   └── js/
    └── app/
        ├── core/
        ├── controllers/
        ├── models/
        └── views/
```

---

## Overview

Shoppeo is a web platform designed to help users compare product prices across local stores. The application combines a traditional MVC architecture with real-time updates and geolocation features, allowing users to find the best available prices nearby.

The project was developed as a Final Degree Project for the Web Application Development program and focuses on software architecture, security, scalability, and modern web development practices.

---

## Main Features

* Product search with dynamic filtering
* Price comparison across multiple stores
* Interactive map displaying store locations
* User authentication and registration
* Administrative dashboard for product and price management
* Real-time price update notifications using WebSockets
* REST-style JSON API for asynchronous requests
* Responsive design optimized for desktop and mobile devices

---

## Getting Started

### Requirements

* Docker Desktop installed and running
* Available ports:

  * 8080 (PHP/Apache)
  * 3000 (WebSocket Server)
  * 5432 (PostgreSQL)

### Clone the Repository

```bash
git clone <repository-url> shoppeo
cd shoppeo
```

### Start the Application

```bash
docker compose up --build
```

Docker will automatically:

* Build the PHP environment with PostgreSQL support.
* Initialize the PostgreSQL database using the provided SQL script.
* Build and start the Node.js WebSocket server.
* Configure the internal network between services.

### Access the Application

| Service                | URL                          |
| ---------------------- | ---------------------------- |
| Web Application        | http://localhost:8080        |
| WebSocket Health Check | http://localhost:3000/health |
| PostgreSQL             | localhost:5432               |

### Demo Credentials

| Role          | Email                                         | Password |
| ------------- | --------------------------------------------- | -------- |
| Administrator | [admin@shoppeo.es](mailto:admin@shoppeo.es)   | admin123 |
| User          | [maria@example.com](mailto:maria@example.com) | user123  |

---

## Application Routes

| Method   | Route                | Description             |
| -------- | -------------------- | ----------------------- |
| GET      | `/`                  | Home page               |
| GET      | `/search?q=term`     | Product search          |
| GET      | `/product/{id}`      | Product comparison page |
| GET      | `/map`               | Interactive map         |
| GET/POST | `/login`             | User login              |
| GET/POST | `/register`          | User registration       |
| GET      | `/logout`            | Logout                  |
| GET      | `/admin`             | Admin dashboard         |
| GET      | `/admin/products`    | Product management      |
| GET      | `/admin/prices`      | Price management        |
| GET      | `/admin/stores`      | Store management        |
| GET      | `/api/products?q=`   | Product search API      |
| GET      | `/api/products/{id}` | Product details API     |
| GET      | `/api/map`           | Map data API            |

---

## Architecture

```text
Browser → HTTP Request
    ↓
public/index.php
    ↓
Router
    ↓
Controller
    ↓
Model (PDO + PostgreSQL)
    ↓
View

AJAX Requests:
Browser → Fetch API → API Controller → JSON Response

Real-Time Updates:
Admin Action
    ↓
PHP Backend
    ↓
Node.js WebSocket Server
    ↓
Socket.io Event
    ↓
Connected Clients
```

The project follows the MVC pattern, ensuring a clear separation between business logic, data access, and presentation layers.

---

## Technical Decisions

### PHP 8 and MVC Architecture

PHP 8 provides modern language features such as union types, match expressions, and performance improvements. The MVC architecture improves maintainability and scalability by separating responsibilities into models, views, and controllers.

### PostgreSQL and PDO

PostgreSQL was selected for its reliability, advanced SQL capabilities, JSON support, window functions, and standards compliance. PDO prepared statements are used throughout the application to prevent SQL injection attacks.

### Node.js and Socket.io

Since PHP follows a request-response model, real-time communication is handled by a dedicated Node.js server using Socket.io. This enables instant updates without requiring page refreshes.

### Docker

Docker ensures a consistent environment across development and deployment. Docker Compose orchestrates all services and allows the complete application stack to be started with a single command.

### Leaflet.js

Leaflet provides lightweight, open-source map functionality powered by OpenStreetMap data, avoiding the need for proprietary APIs or paid services.

### Bootstrap 5 and Vanilla JavaScript

Bootstrap accelerates frontend development with responsive layouts and accessible components, while modern JavaScript and the Fetch API eliminate unnecessary dependencies such as jQuery.

---

## Security Measures

| Threat                     | Protection                            |
| -------------------------- | ------------------------------------- |
| SQL Injection              | PDO prepared statements               |
| Cross-Site Scripting (XSS) | htmlspecialchars() output escaping    |
| Session Fixation           | session_regenerate_id(true)           |
| Password Security          | password_hash() and password_verify() |
| Clickjacking               | X-Frame-Options header                |
| Authorization              | Role-based access control             |
| Administrative Access      | Protected middleware                  |
| Internal WebSocket API     | Secret header validation              |

---

## Educational Objectives

This project demonstrates practical implementation of:

* MVC software architecture
* Database design and optimization
* Secure authentication systems
* RESTful API development
* Real-time web communication
* Containerized deployment with Docker
* Responsive frontend development
* Interactive geolocation services

---

## Author

SUFIAN HOSSAIN BADRI
