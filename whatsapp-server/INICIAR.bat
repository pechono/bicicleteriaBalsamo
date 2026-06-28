@echo off
title WhatsApp - Bicicleteria Balsamo
color 0A
cd /d "C:\WhatsAppBalsamo"

if not exist "node_modules" (
    echo Instalando por primera vez, espera unos minutos...
    npm install
    echo.
)

echo Iniciando WhatsApp...
echo NO CIERRES ESTA VENTANA mientras uses el sistema.
echo.
node server.js
pause
