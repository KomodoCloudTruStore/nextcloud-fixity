<?php

return [
    'routes' => [
        ['name' => 'fixity#show', 'url' => '/hashes/{id}', 'verb' => 'GET'],
        ['name' => 'fixity#validate', 'url' => '/hashes/{id}/validate', 'verb' => 'GET'],
        ['name' => 'fixity#create', 'url' => '/hashes', 'verb' => 'POST']
    ]
];
