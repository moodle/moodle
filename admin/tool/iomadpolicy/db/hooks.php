<?php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => tool_iomadpolicy\hook\output\before_standard_footer_html_generation_hook::class,
        'callback' => tool_iomadpolicy\hook\output\before_standard_footer_html_generation_hook::class . '::execute',
        'priority' => 0,
    ],

];
