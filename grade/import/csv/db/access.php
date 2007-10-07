<?php  // $Id$

$gradeimport_csv_capabilities = array(

    'gradeimport/csv:view' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    )
);

?>
