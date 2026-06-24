<?php

// Configuracion de la pasarela de pagos Libelula (https://libelula.bo).
// Los valores se leen desde el .env y pueden sobrescribirse por entorno.
return [
    'appkey' => env('LIBELULA_APPKEY', ''),                        // Clave unica proporcionada por Libelula
    'api_url' => env('LIBELULA_API_URL', 'https://api.libelula.bo'), // URL base de la API REST
    'callback_url' => env('LIBELULA_CALLBACK_URL', ''),            // URL donde Libelula notificara pagos exitosos
    'moneda' => env('LIBELULA_MONEDA', 'BOB'),                     // Moneda de las transacciones (BOB, USD)
    'emite_factura' => env('LIBELULA_EMITE_FACTURA', 'false'),     // Si Libelula debe emitir factura electronica
];
