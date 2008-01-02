<?php // $Id$

    if(!empty($_SERVER['GATEWAY_INTERFACE'])){
        error_log("should not be called from apache!");
        exit;
    }
    error_reporting(E_ALL);
    
    require_once(dirname(dirname(dirname(__FILE__))).'/config.php'); // global moodle config file.

    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/lib/blocklib.php');
    require_once($CFG->dirroot . "/enrol/database/enrol.php");

    // ensure errors are well explained
    $CFG->debug=E_ALL;

    if (!is_enabled_enrol('database')) {
         error_log("Database enrol plugin not enabled!");
         die;
    }

    // update enrolments -- these handlers should autocreate courses if required
    $enrol = new enrolment_plugin_database();

    // If we have settings to handle roles individually, through each type of
    // role and update it.  Otherwise, just got through once (with no role
    // specified).
    $roles = !empty($CFG->enrol_db_remoterolefield) && !empty($CFG->enrol_db_localrolefield)
        ? get_records('role')
        : array(null);
        
    foreach ($roles as $role) {
        $enrol->sync_enrolments($role);
    }
    
    // sync metacourses
    if (function_exists('sync_metacourses')) {
        sync_metacourses();
    }
    
?>
