<?php
/**
 * Capability definitions for the quiz statistics report.
 *
 * For naming conventions, see lib/db/access.php.
 */
$quizreport_statistics_capabilities = array(
    'quizreport/statistics:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'mod/quiz:viewreports'
    )
);
?>

