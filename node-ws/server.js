
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
const server = http.createServer(app);


const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

app.use(cors());
app.use(express.json());

 
let connectedClients = 0;

//  Eventos Socket.io 
io.on('connection', (socket) => {
  connectedClients++;
  console.log(`[WS] Cliente conectado: ${socket.id} | Total: ${connectedClients}`);

  // Informar al cliente recién conectado cuántos usuarios hay online
  io.emit('users_online', { count: connectedClients });

  // El cliente puede suscribirse a un producto concreto
  socket.on('subscribe_product', (data) => {
    const room = `product_${data.producto_id}`;
    socket.join(room);
    console.log(`[WS] ${socket.id} suscrito a ${room}`);
  });

  // El cliente se suscribe a sus propias actualizaciones de puntos
  socket.on('subscribe_user', (data) => {
    if (data && data.user_id) {
      const room = `user_${data.user_id}`;
      socket.join(room);
    }
  });

  socket.on('disconnect', () => {
    connectedClients--;
    console.log(`[WS] Cliente desconectado: ${socket.id} | Total: ${connectedClients}`);
    io.emit('users_online', { count: connectedClients });
  });
});


app.post('/internal/notify', (req, res) => {
  const secret = req.headers['x-ws-secret'];
  if (secret !== (process.env.WS_SECRET || 'ws_secret_tfg_2024')) {
    return res.status(403).json({ error: 'Forbidden' });
  }

  const payload = req.body;
  console.log('[WS] Notificación de precio recibida desde PHP:', payload);

  // Emitir al room del producto específico y a todos los clientes
  const room = `product_${payload.producto_id}`;
  io.to(room).emit('price_updated', payload);

  // También emitir globalmente para el panel de actividad reciente
  io.emit('price_activity', {
    producto_id: payload.producto_id,
    producto: payload.producto,
    tienda: payload.tienda,
    precio: payload.precio,
    precio_anterior: payload.precio_anterior,
    timestamp: new Date().toISOString()
  });

  return res.json({ ok: true, emitted_to: room });
});

//  Endpoint de salud 
app.get('/health', (_req, res) => {
  res.json({
    status: 'ok',
    clients: connectedClients,
    uptime: process.uptime()
  });
});

//  Inicio del servidor
const PORT = process.env.WS_PORT || 3000;
server.listen(PORT, () => {
  console.log(`[WS] Servidor Socket.io escuchando en puerto ${PORT}`);

  // Arrancar simulador si está activado por variable de entorno
  if (process.env.SIMULATE === 'true') {
    console.log('[WS] SIMULATE=true → arrancando simulador de actividad');
    require('./simulator')(io);
  }
});
