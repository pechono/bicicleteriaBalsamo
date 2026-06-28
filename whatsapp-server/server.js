const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
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
        console.log('  ESCANEA ESTE QR CON TU WHATSAPP');
        console.log('  WhatsApp > Menu > Dispositivos vinculados');
        console.log('=========================================\n');
        qrcode.generate(qr, { small: true });
    });

    c.on('authenticated', () => {
        console.log('\n Autenticado correctamente. Cargando...');
    });

    c.on('auth_failure', (msg) => {
        console.log('Fallo la autenticacion:', msg);
    });

    c.on('ready', () => {
        isReady = true;
        console.log('=========================================');
        console.log('  WhatsApp listo para enviar mensajes!');
        console.log('  NO CIERRES ESTA VENTANA');
        console.log('=========================================\n');
    });

    c.on('disconnected', (reason) => {
        isReady = false;
        console.log('WhatsApp desconectado:', reason);

        // Si el celular deslogueo el dispositivo, la sesion guardada ya no sirve:
        // hay que borrarla para que aparezca un QR nuevo.
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
            console.log('Sesion anterior eliminada. Hay que escanear el QR de nuevo.');
        } catch (e) {
            console.log('No se pudo borrar la sesion:', e.message);
        }
    }

    // Espera para que el browser termine de cerrar antes de relanzar.
    setTimeout(() => {
        console.log('Reiniciando conexion con WhatsApp...');
        iniciar();
        reconectando = false;
    }, 3000);
}

function iniciar() {
    client = crearCliente();
    registrarEventos(client);
    client.initialize().catch((err) => {
        console.error('Error al inicializar WhatsApp:', err.message);
        reiniciar(false);
    });
}

app.post('/send', async (req, res) => {
    const { to, message } = req.body;

    if (!isReady || !client) {
        return res.status(503).json({ success: false, error: 'WhatsApp no conectado' });
    }

    if (!to || !message) {
        return res.status(400).json({ success: false, error: 'Faltan datos' });
    }

    try {
        const chatId = to.includes('@c.us') ? to : `${to}@c.us`;
        await client.sendMessage(chatId, message);
        console.log('Mensaje enviado a ' + to);
        res.json({ success: true });
    } catch (error) {
        console.error('Error al enviar a ' + to + ':', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

app.post('/send-media', async (req, res) => {
    const { to, base64, filename, caption } = req.body;

    if (!isReady || !client) {
        return res.status(503).json({ success: false, error: 'WhatsApp no conectado' });
    }

    if (!to || !base64) {
        return res.status(400).json({ success: false, error: 'Faltan datos' });
    }

    try {
        const chatId = to.includes('@c.us') ? to : `${to}@c.us`;
        const media = new MessageMedia('application/pdf', base64, filename || 'documento.pdf');
        await client.sendMessage(chatId, media, { caption: caption || '' });
        console.log('Archivo enviado a ' + to);
        res.json({ success: true });
    } catch (error) {
        console.error('Error al enviar archivo a ' + to + ':', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

app.get('/status', (req, res) => {
    res.json({ ready: isReady });
});

app.listen(PUERTO, () => {
    console.log('Servidor iniciado en puerto ' + PUERTO);
    console.log('Conectando con WhatsApp...\n');
    iniciar();
});
