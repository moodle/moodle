<?php 

$capabilities = array(

 'block/evaluation_kit_sso:view' => array('captype' => 'read',
  'contextlevel' => CONTEXT_COURSE,
  'legacy' => array('guest' => CAP_PREVENT,
  'user' => CAP_ALLOW,
  'student' => CAP_ALLOW,
  'teacher' => CAP_ALLOW,
  'editingteacher' => CAP_ALLOW,
  'coursecreator' => CAP_ALLOW,
 'manager' => CAP_ALLOW)),

 'block/evaluation_kit_sso:myaddinstance' => array(
         'captype' => 'write',
         'contextlevel' => CONTEXT_SYSTEM,
         'archetypes' => array(
             'user' => CAP_ALLOW
         ),
 
         'clonepermissionsfrom' => 'moodle/my:manageblocks'
     ),

      'block/evaluation_kit_sso:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

); 
?>