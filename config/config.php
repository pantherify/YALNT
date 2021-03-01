<?php

return
    [
        'models' => [
            'namespace' => 'App\\Models',
        ],
        'generation' => [
            'skipTimeStamps' => true,
            'skipIds' => true,
            'skipUser' => true
        ],
        'nova' => [
            'path' => 'app/Nova/',
            'namespace' => 'App\\Nova'
        ]
    ];
