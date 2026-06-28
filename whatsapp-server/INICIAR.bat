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
set WHATSAPP_TOKEN=f839725faa86392586940296fd44ac807ae46cee2cfbdb79
node server.js
pause
