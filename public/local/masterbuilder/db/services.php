<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_masterbuilder_create_question' => [
        'classname'   => 'local_masterbuilder\external',
        'methodname'  => 'create_question',
        'description' => 'Creates a True/False question and adds it to a quiz',
        'type'        => 'write',
        'ajax'        => true,
    ],
];

$services = [
    'MasterBuilder Service' => [
        'functions' => ['local_masterbuilder_create_question'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'masterbuilder',
    ],
];
