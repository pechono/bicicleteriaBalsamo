const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(express.json());

const PUERTO = 3000;
const AUTH_DIR = path.join(__dirname, '.wwebjs_auth');

let isReady = false;
let client = null;
let reconectando = false;

function crearCliente() {
    return new Client({
        authStrategy: new LocalAuth(),
        puppeteer: {
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
        }
    });
}

function registrarEventos(c) {
    c.on('qr', (qr) => {
        console.clear();
        console.log('=========================================');
        console.log('  ESCANEÁ ESTE QR CON TU WHATSAPP');
        console.log('  WhatsApp > Dispositivos vinculados > +');
        console.log('=========================================\n');
        qrcode.generate(qr, { small: true });
    });

    c.on('authenticated', () => {
        console.log('\n✅ Autenticado correctamente');
    });

    c.on('auth_failure', (msg) => {
        console.log('❌ Falló la autenticación:', msg);
    });

    c.on('ready', () => {
        isReady = true;
        console.log('✅ WhatsApp listo para enviar mensajes');
        console.log(`🚀 Servidor corriendo en http://localhost:${PUERTO}\n`);
    });

    c.on('disconnected', (reason) => {
        isReady = false;
        console.log('❌ WhatsApp desconectado:', reason);

        // Si el dispositivo fue deslogueado, la sesión guardada ya no sirve:
        // hay que borrarla para que se genere un QR nuevo.
        const sesionMuerta = String(reason).toUpperCase() === 'LOGOUT';
        reiniciar(sesionMuerta);
    });
}

// Reinicia el cliente de forma segura: destruye el browser anterior antes de
// volver a inicializar (evita el error "browser is already running for userDataDir").
async function reiniciar(borrarSesion = false) {
    if (reconectando) return;
    reconectando = true;

    try {
        if (client) {
            try { await client.destroy(); } catch (_) {}
        }
    } finally {
        client = null;
    }

    if (borrarSesion) {
        try {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
            console.log('🧹 Sesión anterior eliminada. Vas a tener que escanear el QR de nuevo.');
        } catch (e) {
            console.log('⚠️  No se pudo borrar la sesión:', e.message);
        }
    }

    // Pequeña espera para que el browser termine de cerrar antes de relanzar.
    setTimeout(() => {
        console.log('🔄 Reiniciando conexión con WhatsApp...');
        iniciar();
        reconectando = false;
    }, 3000);
}

function iniciar() {
    client = crearCliente();
    registrarEventos(client);
    client.initialize().catch((err) => {
        console.error('❌ Error al inicializar WhatsApp:', err.message);
        // Reintenta una vez más; si el browser quedó trabado, borrar sesión ayuda.
        reiniciar(false);
    });
}

// Endpoint para enviar mensajes (llamado por Laravel)
app.post('/send', async (req, res) => {
    const { to, message } = req.body;

    if (!isReady || !client) {
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
    iniciar();
});
