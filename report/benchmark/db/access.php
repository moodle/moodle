<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'report/benchmark:view' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'report/performance:view'
    )
);
