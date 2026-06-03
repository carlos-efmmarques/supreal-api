<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'printing' => [
        'smb' => [
            'domain'   => env('SMB_PRINT_DOMAIN', 'supreal'),
            'user'     => env('SMB_PRINT_USER'),
            'password' => env('SMB_PRINT_PASSWORD'),
            'timeout'  => (int) env('SMB_PRINT_TIMEOUT', 10),
            'retries'  => (int) env('SMB_PRINT_RETRIES', 2),
        ],
        'tcp' => [
            'port'            => (int) env('PRINT_TCP_PORT', 9100),
            'connect_timeout' => (int) env('PRINT_TCP_CONNECT_TIMEOUT', 5),
            'send_timeout'    => (int) env('PRINT_TCP_SEND_TIMEOUT', 10),
        ],
        // Mapeamento: IP do host Windows (extraído de DIRETEXPORTARQUIVO) => IP da impressora de etiquetas
        // Usado como fallback quando a empresa não tem catálogo em 'printers' abaixo.
        'host_map' => [
            '10.36.3.202' => '10.36.3.46', // Loja 1 (Matriz)  — Zebra GT800
            '10.36.7.14'  => '10.36.7.33', // Loja 2 (Maravista) — Elgin L42PRO
        ],
        // Catálogo de impressoras por empresa (nroempresa => lista).
        // O app lista esses apelidos pro usuário escolher; o envio TCP usa o 'ip'.
        // 'default' => true marca a impressora usada quando nenhuma é escolhida.
        // 'host' é só o IP do CPU/Windows (referência; o TCP usa o 'ip' da impressora).
        'printers' => [
            1 => [ // Matriz
                ['id' => 'matriz_etiquetas', 'alias' => 'Etiquetas', 'ip' => '10.36.3.46', 'host' => '10.36.3.202', 'model' => 'Zebra GT800', 'default' => true],
            ],
            2 => [ // Maravista
                ['id' => 'deposito', 'alias' => 'Conferência / Depósito', 'ip' => '10.36.7.33', 'host' => '10.36.7.14', 'model' => 'Elgin L42PRO', 'default' => true],
                ['id' => 'frente',   'alias' => 'Frente de Loja',          'ip' => '10.36.7.24', 'host' => '10.36.7.44', 'model' => 'Elgin L42PRO 01'],
            ],
        ],
    ],

];
