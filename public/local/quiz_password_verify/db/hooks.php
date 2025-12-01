<?php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_footer::class,
        'callback' => \local_quiz_password_verify\hook\before_footer::class . '::callback',
    ],
];
