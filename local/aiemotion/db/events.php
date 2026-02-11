<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_module_updated',
        'callback'  => '\local_aiemotion\observer::assign_updated',
        'priority'  => 9999,
    ],
];
