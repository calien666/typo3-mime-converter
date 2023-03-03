<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => '(web-vision) Mime Converter',
    'description' => 'File converter based on mime-type to file extension comparison. Serves image converter by default and automatic tagging for self written converter providers.',
    'category' => 'services',
    'author' => 'web-vision',
    'author_email' => 'hello@web-vision.de',
    'author_company' => 'web-vision GmbH',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'WebVision\\MimeConverter\\' => 'Classes'
        ]
    ],
    'autoload-dev' => [
        'psr-4' => [
            'WebVision\\MimeConverter\\Tests\\' => 'Tests'
        ]
    ],
];
