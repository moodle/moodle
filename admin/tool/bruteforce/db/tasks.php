<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => '\\tool_bruteforce\\task\\purge_blocks',
        'blocking' => 0,
        'minute' => '*/5',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*',
    ],
];
