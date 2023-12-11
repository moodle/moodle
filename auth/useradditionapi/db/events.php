<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => '\core\event\user_created',
        'callback' => 'auth_useradditionapi_observer::user_added',
    ),
);
