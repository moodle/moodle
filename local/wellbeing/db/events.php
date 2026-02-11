<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_assign\event\submission_created',
        'callback'    => '\local_wellbeing\observer::submission_created',
        'priority'    => 9999,
        'internal'    => false,
    ],
    [
        'eventname' => '\assignsubmission_onlinetext\event\submission_updated',
        'callback'  => '\local_wellbeing\observer::onlinetext_updated',
        'priority'  => 9999,
    ],
];
