<?php
return [
    'splunk' => [
        'url' => 'https://splunk.example.com:8088/services/collector',
        'token' => 'YOUR_SPLUNK_HEC_TOKEN',
        'verify_ssl' => true
    ],
    'elk' => [
        'url' => 'https://elk.example.com:9200/grim-results/_doc/',
        'username' => 'elastic',
        'password' => 'changeme',
        'verify_ssl' => true
    ],
    'graylog' => [
        'url' => 'https://graylog.example.com:12201/gelf',
        'verify_ssl' => true
    ]
];
