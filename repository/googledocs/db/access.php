<?php

$capabilities = array(

    'repository/googledocs:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'user' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    )
);
