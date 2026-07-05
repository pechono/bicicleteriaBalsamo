const { NodeSSH } = require('node-ssh');
const { execSync } = require('child_process');
require('dotenv').config({ path: '.env.deploy' });

const ssh = new NodeSSH();

async function correr(desc, comando) {
    process.stdout.write(`  ⚡ ${desc}... `);
    const r = await ssh.execCommand(comando);
    if (r.code !== 0 && r.stderr) {
        console.log(`❌\n     ${r.stderr.split('\n')[0]}`);
    } else {
        console.log('✓');
    }
    return r;
}

async function deploy() {
    console.log('\n========================================');
    console.log('  Deploy - Bicicleteria Balsamo');
    console.log('========================================\n');

    const APP = process.env.DEPLOY_APP_PATH;   // ej: /home/u123456789/bicicleteriaBalsamo
    const PUB = process.env.DEPLOY_PUBLIC_PATH; // ej: /home/u123456789/public_html

    // ── 1. Commitear assets compilados + push ─────────
    // Se commitea el public/build recién generado por `npm run build` con
    // [skip ci] (evita que el CI recompile) para que el reset --hard del
    // servidor traiga los assets frescos sin depender de la Action.
    console.log('📤 Subiendo a GitHub...');
    try {
        execSync('git add public/build', { stdio: 'inherit' });
        execSync('git commit -m "build: assets compilados [skip ci]"', { stdio: 'inherit' });
    } catch {
        console.log('  (build sin cambios)');
    }
    try {
        execSync('git push', { stdio: 'inherit' });
    } catch {
        console.log('  (nada nuevo para subir)\n');
    }

    // ── 2. Conectar a Hostinger ───────────────────────
    console.log('\n🔌 Conectando a Hostinger...');
    await ssh.connect({
        host:     process.env.DEPLOY_HOST,
        port:     parseInt(process.env.DEPLOY_PORT || '22'),
        username: process.env.DEPLOY_USER,
        password: process.env.DEPLOY_PASS,
    });
    console.log('✅ Conectado\n');

    // ── 3. Actualizar código en el servidor ───────────
    console.log('📦 Actualizando servidor...');
    await correr('actualizar código', `cd ${APP} && git fetch origin && git reset --hard origin/main`);
    await correr('copiar build/',     `rm -rf ${PUB}/build && cp -rf ${APP}/public/build ${PUB}/build`);
    await correr('copiar index.php',  `cp ${APP}/public/index.php ${PUB}/index.php`);
    await correr('copiar .htaccess',  `cp ${APP}/public/.htaccess ${PUB}/.htaccess`);

    // ── 4. Artisan ────────────────────────────────────
    console.log('\n⚙️  Comandos Laravel...');
    await correr('migraciones',    `cd ${APP} && php artisan migrate --force`);
    await correr('limpiar cache',  `cd ${APP} && php artisan optimize:clear`);
    await correr('cachear config', `cd ${APP} && php artisan config:cache`);
    await correr('cachear rutas',  `cd ${APP} && php artisan route:cache`);
    await correr('cachear vistas', `cd ${APP} && php artisan view:cache`);

    ssh.dispose();

    console.log('\n========================================');
    console.log('  ✅ Deploy completado!');
    console.log('========================================\n');
}

deploy().catch(err => {
    console.error('\n❌ Error:', err.message);
    ssh.dispose();
    process.exit(1);
});
