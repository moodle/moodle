<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/esupervision:supervisor' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
        ),
    ),

    'local/esupervision:student' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'student' => CAP_ALLOW,
        ),
    ),
);

// No permissions for the supervisor role.
$supervisor = get_role_definition('supervisor');
$supervisor->capabilities = array();
define_role($supervisor->shortname, $supervisor->name, $supervisor->description, $supervisor->sortorder, $supervisor->archetypes, $supervisor->legacyfiles);

// No permissions for the student role.
$student = get_role_definition('student');
$student->capabilities = array();
define_role($student->shortname, $student->name, $student->description, $student->sortorder, $student->archetypes, $student->legacyfiles);
