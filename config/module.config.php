<?php
return [
    'service_manager' => [
        'factories' => [
            AndyDune\Pipeline\Pipeline::class => AndyDune\Pipeline\ZendFactory::class,
        ],
    ]
];