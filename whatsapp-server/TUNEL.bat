@echo off
title Tunel WhatsApp - Bicicleteria Balsamo
color 0B
echo =========================================
echo  TUNEL NGROK - Bicicleteria Balsamo
echo =========================================
echo.
echo Iniciando tunel permanente...
echo URL fija: https://calamari-judgingly-shakiness.ngrok-free.dev
echo NO CIERRES ESTA VENTANA.
echo.
ngrok http 3000 --domain=calamari-judgingly-shakiness.ngrok-free.dev
pause
