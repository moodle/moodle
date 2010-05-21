<?php
/**
 * Capability definitions for the quiz statistics report.
 *
 * For naming conventions, see lib/db/access.php.
 */
$capabilities = array(
    'quizreport/statistics:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'mod/quiz:viewreports'
    )
);
