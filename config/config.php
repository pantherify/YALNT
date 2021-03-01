<?php

return
    [
        'models' => [
            'namespace' => 'App\\Models',
        ],
        'generation' => [
            'skipTimeStamps' => true,
            'skipIds' => true
        ],
        'nova' => [
            'path' => 'app/Nova/',
            'namespace' => 'App\\Nova'
        ]
    ];
