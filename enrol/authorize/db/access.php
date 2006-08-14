<?php // $Id$

$enrol_authorize_capabilities = array(

    'enrol/authorize:managepayments' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'admin' => CAP_ALLOW
        )
    )

);

?>
