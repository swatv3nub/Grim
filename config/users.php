<?php
return [
    [
        'username' => 'admin',
        'password' => password_hash('admin', PASSWORD_DEFAULT),
        'role' => 'admin'
    ],
    [
        'username' => 'analyst',
        'password' => password_hash('analyst', PASSWORD_DEFAULT),
        'role' => 'analyst'
    ]
];
