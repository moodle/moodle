<?php
// Event observers for tool_bruteforce.

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\\core\\event\\user_login_failed',
        'callback' => '\\tool_bruteforce\\observer::user_login_failed',
    ],
    [
        'eventname' => '\\core\\event\\user_loggedin',
        'callback' => '\\tool_bruteforce\\observer::user_loggedin',
    ],
];
