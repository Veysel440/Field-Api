<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('FRONTEND_URL','http://localhost:5173')],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type','X-Requested-With','X-CSRF-Token','Authorization','Idempotency-Key','X-Request-Id'],
    'exposed_headers' => ['ETag','Retry-After','X-Request-Id'],
    'max_age' => 3600,
    'supports_credentials' => true,
];
