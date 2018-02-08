<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Content migration',
    'description' => 'Allows developers to build migrations from one table or database to another.',
    'category' => 'misc',
    'version' => '0.0.1',
    'state' => 'alpha',
    'author' => 'Codappix',
    'author_email' => 'info@codappix.net',
    'author_company' => 'Codappix',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.23-8.7.999',
            'php' => '7.0.0-7.1.999',
        ],
    ],
];
