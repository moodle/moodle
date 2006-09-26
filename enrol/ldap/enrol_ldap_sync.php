<?php

    if(!empty($_SERVER['GATEWAY_INTERFACE'])){
        error_log("should not be called from apache!");
        exit;
    }
    error_reporting(E_ALL);
    
    require_once(dirname(dirname(dirname(__FILE__))).'/config.php'); // global moodle config file.

    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/lib/moodlelib.php');
    require_once($CFG->dirroot . '/lib/datalib.php');
    require_once($CFG->dirroot . "/enrol/ldap/enrol.php");

    // ensure errors are well explained
    $CFG->debug = DEBUG_NORMAL;

    // update enrolments -- these handlers should autocreate courses if required
    $enrol = new enrolment_plugin_ldap();
    $enrol->enrol_ldap_connect();

    $enrol->check_legacy_config();

    $roles = get_records('role');
    foreach ($roles as $role) {
        $enrol->sync_enrolments($role->shortname, true);
    }
    
    // sync metacourses
    if (function_exists('sync_metacourses')) {
        sync_metacourses();
    }
    
?>
