<?php  // $Id$
      // This script fetches files from the dataroot/questionattempt directory
      // It is based on the top-level file.php
      //
      // On a module-by-module basis (currently only implemented for quiz), it checks
      // whether the user has permission to view the file.
      //
      // Syntax:      question/file.php/attemptid/questionid/filename.ext
      // Workaround:  question/file.php?file=/attemptid/questionid/filename.ext

    require_once('../config.php');
    require_once('../lib/filelib.php');

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('file.php');
    // force download for any student-submitted files to prevent XSS attacks.
    $forcedownload = 1;

    // relative path must start with '/', because of backup/restore!!!
    if (!$relativepath) {
        error('No valid arguments supplied or incorrect server configuration');
    } else if ($relativepath{0} != '/') {
        error('No valid arguments supplied, path does not start with slash!');
    }

    $pathname = $CFG->dataroot.'/questionattempt'.$relativepath;

    // extract relative path components
    $args = explode('/', trim($relativepath, '/'));

    // check for the right number of directories in the path
    if (count($args) != 3) {
        error('Invalid arguments supplied');
    }

    // security: require login
    require_login();

    // security: do not return directory node!
    if (is_dir($pathname)) {
        question_attempt_not_found();
    }

    $lifetime = 0;  // do not cache because students may reupload files

    // security: check that the user has permission to access this file
    $haspermission = false;
    if ($attempt = get_record("question_attempts", "id", $args[0])) {
        $modfile = $CFG->dirroot .'/mod/'. $attempt->modulename .'/lib.php';
        $modcheckfileaccess = $attempt->modulename .'_check_file_access';
        if (file_exists($modfile)) {
            @require_once($modfile);
            if (function_exists($modcheckfileaccess)) {
                $haspermission = $modcheckfileaccess($args[0], $args[1]);
            }
        }
    } else if ($args[0][0] == 0) {
        global $USER;
        $list = explode('_', $args[0]);
        if ($list[1] == $USER->id) {
            $haspermission = true;
        }
    }

    if ($haspermission) {
        // check that file exists
        if (!file_exists($pathname)) {
            question_attempt_not_found();
        }

        // send the file
        session_write_close(); // unlock session during fileserving
        $filename = $args[count($args)-1];
        send_file($pathname, $filename, $lifetime, $CFG->filteruploadedfiles, false, $forcedownload);
    } else {
        question_attempt_not_found();
    }

    function question_attempt_not_found() {
        global $CFG;
        header('HTTP/1.0 404 not found');
        print_error('filenotfound', 'error', $CFG->wwwroot); //this is not displayed on IIS??
    }
?>
