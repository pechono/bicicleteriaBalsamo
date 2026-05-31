@echo off
title WhatsApp Bicicleteria Balsamo
color 0A

echo =========================================
echo   BICICLETERIA BALSAMO - WhatsApp Server
echo =========================================
echo.

cd /d "%~dp0"

if not exist "node_modules" (
    echo Instalando dependencias por primera vez...
    echo Esto puede tardar unos minutos.
    echo.
    npm install
    echo.
)

echo Iniciando servidor WhatsApp...
echo No cierres esta ventana mientras uses el sistema.
echo.

node server.js

pause
