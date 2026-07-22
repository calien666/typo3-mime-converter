<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '(web-vision) Mime Converter',
    'description' => 'File converter based on mime-type to file extension comparison. Serves image converter by default and registry for self written converter providers.',
    'category' => 'services',
    'author' => 'web-vision',
    'author_email' => 'hello@web-vision.de',
    'author_company' => 'web-vision GmbH',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'reports' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'WebVision\\MimeConverter\\' => 'Classes',
        ],
    ],
    'autoload-dev' => [
        'psr-4' => [
            'WebVision\\MimeConverter\\Tests\\' => 'Tests',
        ],
    ],
];
