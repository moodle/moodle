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
    'local_masterbuilder_reset_course_progress' => [
        'classname'   => 'local_masterbuilder\external',
        'methodname'  => 'reset_course_progress',
        'description' => 'Resets grades, completion, and quiz attempts for a course',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_masterbuilder_get_build_state' => [
        'classname' => 'local_masterbuilder\external',
        'methodname' => 'get_build_state',
        'description' => 'Gets the build version for a course.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_masterbuilder_update_build_state' => [
        'classname' => 'local_masterbuilder\external',
        'methodname' => 'update_build_state',
        'description' => 'Updates the build version for a course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_masterbuilder_reset_build_state' => [
        'classname' => 'local_masterbuilder\external',
        'methodname' => 'reset_build_state',
        'description' => 'Resets the entire build state table.',
        'type' => 'write',
        'ajax' => true,
    ],
];

$services = [
    'MasterBuilder Service' => [
        'functions' => [
            'local_masterbuilder_create_question',
            'local_masterbuilder_reset_course_progress',
            'local_masterbuilder_get_build_state',
            'local_masterbuilder_update_build_state',
            'local_masterbuilder_reset_build_state'
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'masterbuilder',
    ],
];
