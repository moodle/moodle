<?php // $Id$
/**
 * Capability definitions for the lams module.
 * 
 * For naming conventions, see lib/db/access.php.
 */
$mod_lams_capabilities = array(

    'mod/lams:participate' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_ALLOW,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'admin' => CAP_PREVENT
        )
    ),

    'mod/lams:manage' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_PREVENT,
            'admin' => CAP_ALLOW
        )
    )
);