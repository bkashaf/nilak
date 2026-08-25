<?php

return [
    'allow_preview_when_locked' => (bool) env('INSTALLER_ALLOW_PREVIEW_WHEN_LOCKED', false),

    'required_extensions' => [
        'openssl',
        'pdo',
        'pdo_mysql',
        'mbstring',
        'tokenizer',
        'xml',
        'ctype',
        'json',
        'fileinfo',
    ],

    'required_writable_paths' => [
        'storage',
        'bootstrap/cache',
    ],
];
