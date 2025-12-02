<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_coursematrix_get_rules' => [
        'classname'   => 'local_coursematrix\external',
        'methodname'  => 'get_rules',
        'description' => 'Get all course matrix rules',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'local_coursematrix_update_rule' => [
        'classname'   => 'local_coursematrix\external',
        'methodname'  => 'update_rule',
        'description' => 'Update or create course matrix rules',
        'type'        => 'write',
        'ajax'        => true,
    ],
];

$services = [
    'Course Matrix Service' => [
        'functions' => [
            'local_coursematrix_get_rules',
            'local_coursematrix_update_rule',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
