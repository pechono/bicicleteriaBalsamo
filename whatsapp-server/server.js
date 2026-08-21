const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(express.json({ limit: '25mb' }));

const PUERTO = 3000;
const AUTH_DIR = path.join(__dirname, '.wwebjs_auth');
const TOKEN = process.env.WHATSAPP_TOKEN || '';

// Si hay token configurado, exige que coincida el header x-token.
function tokenInvalido(req, res) {
    if (TOKEN && req.headers['x-token'] !== TOKEN) {
        res.status(401).json({ success: false, error: 'Token invalido' });
        return true;
    }
    return false;
}

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

// Resuelve el chatId real en WhatsApp. En Argentina el "9" del movil suele dar
// problemas: getNumberId devuelve el ID como esta registrado aunque el 9 sobre o
// falte. Se prueba tal cual, sin el 9 y con el 9. null = el numero no tiene WhatsApp.
async function resolverChatId(to) {
    const limpio = String(to).replace(/\D/g, '');
    if (limpio.includes('@')) return to; // ya viene como chatId

    const candidatos = [limpio];
    if (limpio.startsWith('549')) {
        candidatos.push('54' + limpio.slice(3)); // sin el 9
    } else if (limpio.startsWith('54')) {
        candidatos.push('549' + limpio.slice(2)); // con el 9
    }

    for (const num of candidatos) {
        try {
            const numberId = await client.getNumberId(num);
            if (numberId) return numberId._serialized;
        } catch (_) { /* sigue probando */ }
    }
    return null;
}

app.post('/send', async (req, res) => {
    if (tokenInvalido(req, res)) return;
    const { to, message } = req.body;

    if (!isReady || !client) {
        return res.status(503).json({ success: false, error: 'WhatsApp no conectado' });
    }

    if (!to || !message) {
        return res.status(400).json({ success: false, error: 'Faltan datos' });
    }

    try {
        // Resolver con getNumberId (arregla el 9 de Argentina). Si no resuelve,
        // NO bloqueamos con 422: intentamos con el numero crudo (como antes).
        const chatId = (await resolverChatId(to)) || (String(to).replace(/\D/g, '') + '@c.us');
        await client.sendMessage(chatId, message);
        console.log('Mensaje enviado a ' + to);
        res.json({ success: true });
    } catch (error) {
        console.error('Error al enviar a ' + to + ':', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

app.post('/send-media', async (req, res) => {
    if (tokenInvalido(req, res)) return;
    const { to, base64, filename, caption } = req.body;

    if (!isReady || !client) {
        return res.status(503).json({ success: false, error: 'WhatsApp no conectado' });
    }

    if (!to || !base64) {
        return res.status(400).json({ success: false, error: 'Faltan datos' });
    }

    try {
        // Resolver con getNumberId (arregla el 9 de Argentina). Si no resuelve,
        // NO bloqueamos con 422: intentamos con el numero crudo (como antes).
        const chatId = (await resolverChatId(to)) || (String(to).replace(/\D/g, '') + '@c.us');
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
