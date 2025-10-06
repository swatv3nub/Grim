<?php
return [
    // Email notification settings
    'email' => [
        'enabled' => false,
        'to' => 'admin@example.com',
        'from' => 'grim@localhost',
        'smtp' => [
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => '',
            'password' => ''
        ]
    ],
    // Slack notification settings
    'slack' => [
        'enabled' => false,
        'webhook_url' => ''
    ]
];
