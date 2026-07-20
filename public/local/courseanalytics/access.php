<?php
defined('MOODLE_INTERNAL') || die();
$capabilities = [
    'local/courseanalytics:viewreport' => [
        'captype' => 'view',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
        ]
    ]
];
