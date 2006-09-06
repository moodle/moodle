<?php // $Id$
/**
 * Capability definitions for the workshop module.
 * 
 * For naming conventions, see lib/db/access.php.
 */
$mod_workshop_capabilities = array(

    'mod/workshop:view' => array(
    
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'mod/workshop:participate' => array(
    
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

    'mod/workshop:manage' => array(
    
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    )
);