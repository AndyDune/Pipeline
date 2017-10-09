<?php
use AndyDune\Pipeline\Pipeline;
use AndyDune\Pipeline\ZendFactory;
return [
    'service_manager' => [
        'factories' => [
            Pipeline::class => ZendFactory::class,
        ],
        'aliases' => [
            'pipeline' => Pipeline::class,
        ],
        'shared' => [
            Pipeline::class => false,
            'pipeline' => false
        ]
    ],
];