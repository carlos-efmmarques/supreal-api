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
            '10.36.6.65'  => '10.36.6.24', // Loja 3 (Noronha)   — Zebra GT800
            '10.36.9.69'  => '10.36.9.86', // Loja 109 (São Francisco) — Elgin L42PRO Full
            '10.36.4.202' => '10.36.4.53', // Loja 104 (Itacoatiara)   — Elgin L42PRO Full
            '10.36.5.65'  => '10.36.5.49', // Loja 105 (Pendotiba)     — Elgin L42Pro (PEN-PRE-01)
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
            3 => [ // Noronha
                ['id' => 'noronha_etiquetas', 'alias' => 'Etiquetas', 'ip' => '10.36.6.24', 'host' => '10.36.6.65', 'model' => 'Zebra GT800', 'default' => true],
            ],
            104 => [ // Itacoatiara
                ['id' => 'itacoatiara_etiquetas', 'alias' => 'Etiquetas', 'ip' => '10.36.4.53', 'host' => '10.36.4.202', 'model' => 'Elgin L42PRO Full', 'default' => true],
            ],
            105 => [ // Pendotiba
                ['id' => 'pendotiba_etiquetas', 'alias' => 'Etiquetas', 'ip' => '10.36.5.49', 'host' => '10.36.5.65', 'model' => 'Elgin L42Pro', 'default' => true],
            ],
            109 => [ // São Francisco
                ['id' => 'sao_francisco_etiquetas', 'alias' => 'Etiquetas', 'ip' => '10.36.9.86', 'host' => '10.36.9.69', 'model' => 'Elgin L42PRO Full', 'default' => true],
            ],
        ],
    ],

];
