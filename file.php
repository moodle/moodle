<?php // $Id$
      // This script fetches files from the dataroot directory
      // Syntax:      file.php/courseid/dir/dir/dir/filename.ext
      //              file.php/courseid/dir (returns index.html from dir)
      // Workaround:  file.php?file=/courseid/dir/dir/dir/filename.ext
      // Test:        file.php/test

    require_once('config.php');
    require_once('files/mimetypes.php');

    if (empty($CFG->filelifetime)) {
        $lifetime = 86400;     // Seconds for files to remain in caches
    } else {
        $lifetime = $CFG->filelifetime;
    }
    

    $relativepath = get_file_argument('file.php');
    
    // relative path must start with '/', because of backup/restore!!!
    if (!$relativepath) {
        error('No valid arguments supplied or incorrect server configuration');
    } else if ($relativepath{0} != '/') {
        error('No valid arguments supplied, path does not start with slash!');
    }

    $pathname = $CFG->dataroot.$relativepath;

    // extract relative path components
    $args = explode('/', trim($relativepath, '/'));
    if (count($args) == 0) { // always at least courseid, may search for index.html in course root
        error('No valid arguments supplied');
    }

    // security: limit access to existing course subdirectories
    // note: course ID must be specified
    // note: the lang field is needed for the course language switching hack in weblib.php
    if (!$course = get_record_sql("SELECT id, lang FROM {$CFG->prefix}course WHERE id='".(int)$args[0]."'")) {
        error('Invalid course ID');
    }

    // security: prevent access to "000" or "1 something" directories
    if ($args[0] != $course->id) {
        error('Invalid course ID');
    }

    // security: login to course if necessary
    if ($course->id != SITEID) {
        require_login($course->id);
    } else if ($CFG->forcelogin) {
        require_login();
    }

    // security: only editing teachers can access backups
    if ((!isteacheredit($course->id))
        and (count($args) >= 2)
        and (strtolower($args[1]) == 'backupdata')) {

        error('Access not allowed');
    }

    // security: teachers can view all assignments, students only their own
    if ((count($args) >= 3)
        and (strtolower($args[1]) == 'moddata')
        and (strtolower($args[2]) == 'assignment')) {

        $lifetime = 0;  // do not cache assignments, students may reupload them
        if ((!isteacher($course->id)) && (count($args) != 6 || $args[4] != $USER->id)) {
           error('Access not allowed');
        }
    }

    if (is_dir($pathname)) {
        if (file_exists($pathname.'/index.html')) {
            $pathname = rtrim($pathname, '/').'/index.html';
            $args[] = 'index.html';
        } else if (file_exists($pathname.'/index.htm')) {
            $pathname = rtrim($pathname, '/').'/index.htm';
            $args[] = 'index.htm';
        } else if (file_exists($pathname.'/Default.htm')) {
            $pathname = rtrim($pathname, '/').'/Default.htm';
            $args[] = 'Default.htm';
        } else {
            // security: do not return directory node!
            not_found($course->id);
        }
    }

    // check that file exists
    if (!file_exists($pathname)) {
        not_found($course->id);
    }

    // extra security: keep symbolic links inside dataroot/courseid if required
    /*if (!empty($CFG->checksymlinks)) {
        $realpath = realpath($pathname);
        $realdataroot = realpath($CFG->dataroot.'/'.$course->id);
        if (strpos($realpath, $realdataroot) !== 0) {
            not_found($course->id);
        }
    }*/

    // ========================================
    // finally send the file
    // ========================================
    $filename = $args[count($args)-1];
    send_file($pathname, $filename, $lifetime, !empty($CFG->filteruploadedfiles));

    function not_found($courseid) {
        global $CFG;
        header('HTTP/1.0 404 not found');
        error(get_string('filenotfound', 'error'), $CFG->wwwroot.'/course/view.php?id='.$courseid); //this is not displayed on IIS??
    }
?>
