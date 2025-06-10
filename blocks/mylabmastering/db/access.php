<?php

defined('MOODLE_INTERNAL') || die;

$capabilities = array(

    'block/mylabmastering:addinstance' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
        	'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
	'block/mylabmastering:view' => array(
		'captype' => 'read',
		'contextlevel' => CONTEXT_MODULE,
		'archetypes' => array(
				'guest' => CAP_PROHIBIT,
				'student' => CAP_ALLOW,
				'teacher' => CAP_ALLOW,
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
		)
	),
	'block/mylabmastering:manage' => array(
		'riskbitmask' => RISK_XSS,

		'captype' => 'write',
		'contextlevel' => CONTEXT_COURSE,
		'archetypes' => array(
				'teacher' => CAP_ALLOW,
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
		),
		'clonepermissionsfrom' => 'moodle/site:manageblocks'
	)		
);
