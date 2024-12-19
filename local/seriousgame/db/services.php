<?php
// This file defines external services for the plugin.
// It must be placed in the root folder of the plugin.

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_seriousgame_get_activities' => [
        'classname'   => 'local_seriousgame_external', 
        'methodname'  => 'get_activities',           
        'classpath'   => 'local/seriousgame/externallib.php', 
        'description' => 'Get a list of activities for a course.',
        'type'        => 'read', 
        'capabilities' => 'moodle/course:view', 
    ],
];

$services = [
    'local_seriousgame' => [
        'functions' => ['local_seriousgame_get_activities'], 
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'seriousgame',
    ],
];
