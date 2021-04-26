<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Scheduler Forker',
    'description' => 'Forks a new process for each scheduler task',
    'category' => 'misc',
    'author' => 'Sascha Egerer',
    'author_email' => 'sascha.egerer@flowd.de',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-10.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
