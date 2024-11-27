<?php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_http_headers::class,
        'callback' => \auth_iomadsaml2\hook\output\before_http_headers_hook::class . '::execute',
        'priority' => 0,
    ],
    [
        'hook' => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => auth_iomadsaml2\hook\output\before_standard_head_html_generation_hook::class . '::execute',
        'priority' => 0,
    ],
    [
        'hook' => \core\hook\output\after_http_headers::class,
        'callback' => \auth_iomadsaml2\hook\after_http_headers_hook::class . '::execute',
        'priority' => 0,
    ],

];
