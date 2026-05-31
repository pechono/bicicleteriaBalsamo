const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');

const app = express();
app.use(express.json());

const PUERTO = 3000;
let isReady = false;

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    }
});

client.on('qr', (qr) => {
    console.clear();
    console.log('=========================================');
    console.log('  ESCANEÁ ESTE QR CON TU WHATSAPP');
    console.log('  WhatsApp > Dispositivos vinculados > +');
    console.log('=========================================\n');
    qrcode.generate(qr, { small: true });
});

client.on('authenticated', () => {
    console.log('\n✅ Autenticado correctamente');
});

client.on('ready', () => {
    isReady = true;
    console.log('✅ WhatsApp listo para enviar mensajes');
    console.log(`🚀 Servidor corriendo en http://localhost:${PUERTO}\n`);
});

client.on('disconnected', (reason) => {
    isReady = false;
    console.log('❌ WhatsApp desconectado:', reason);
    console.log('Intentando reconectar...');
    client.initialize();
});

client.initialize();

// Endpoint para enviar mensajes (llamado por Laravel)
app.post('/send', async (req, res) => {
    const { to, message } = req.body;

    if (!isReady) {
        return res.status(503).json({ success: false, error: 'WhatsApp no está conectado aún' });
    }

    if (!to || !message) {
        return res.status(400).json({ success: false, error: 'Faltan parámetros: to, message' });
    }

    try {
        const chatId = to.includes('@c.us') ? to : `${to}@c.us`;
        await client.sendMessage(chatId, message);
        console.log(`📤 Mensaje enviado a ${to}`);
        res.json({ success: true });
    } catch (error) {
        console.error(`❌ Error enviando a ${to}:`, error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

// Endpoint para verificar estado (usado por Laravel para saber si la PC está on)
app.get('/status', (req, res) => {
    res.json({ ready: isReady, timestamp: new Date().toISOString() });
});

app.listen(PUERTO, () => {
    console.log('=========================================');
    console.log('  BICICLETERIA BALSAMO - WhatsApp Server');
    console.log('=========================================');
    console.log('Iniciando conexión con WhatsApp...\n');
});
