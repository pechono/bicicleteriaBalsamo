<?php

return [
    // Token secreto para el hook de deploy por HTTP. Definir DEPLOY_TOKEN en el .env del servidor.
    'token' => env('DEPLOY_TOKEN'),

    // Carpeta pública (docroot). Si no se setea, usa ../public_html al lado del proyecto.
    'public_path' => env('DEPLOY_PUBLIC_PATH'),
];
