<?php // $Id$
      // This script fetches files from the dataroot directory
      // Syntax:      file.php/courseid/dir/dir/dir/filename.ext
      //              file.php/courseid/dir/dir/dir/filename.ext?forcedownload=1 (download instead of inline)
      //              file.php/courseid/dir (returns index.html from dir)
      // Workaround:  file.php?file=/courseid/dir/dir/dir/filename.ext
      // Test:        file.php/testslasharguments

    require_once('config.php');
    require_once('lib/filelib.php');

    if (empty($CFG->filelifetime)) {
        $lifetime = 86400;     // Seconds for files to remain in caches
    } else {
        $lifetime = $CFG->filelifetime;
    }
    

    $relativepath = get_file_argument('file.php');
    $forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);
    
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

    // security: teachers can view all assignments, students only their own
    if ((count($args) >= 3)
        and (strtolower($args[1]) == 'moddata')
        and (strtolower($args[2]) == 'assignment')) {

        $lifetime = 0;  // do not cache assignments, students may reupload them
        if ((!isteacher($course->id)) && (count($args) != 6 || $args[4] != $USER->id)) {
           error('Access not allowed');
        }
    }

    // security: force download of all attachments submitted by students
    if ((count($args) >= 3)
        and (strtolower($args[1]) == 'moddata')
        and ((strtolower($args[2]) == 'forum')
            or (strtolower($args[2]) == 'assignment')
            or (strtolower($args[2]) == 'glossary')
            or (strtolower($args[2]) == 'wiki')
            or (strtolower($args[2]) == 'exercise')
            or (strtolower($args[2]) == 'workshop')
            )) {
        $forcedownload  = 1; // force download of all attachments
    }

    // security: some protection of hidden resource files
    // warning: it may break backwards compatibility
    if ((!empty($CFG->preventaccesstohiddenfiles)) 
        and (count($args) >= 2)
        and (!isteacher($course->id))) {

        $reference = ltrim($relativepath, "/{$args[0]}/");

        $sql = "SELECT COUNT(r.id) " .
                 "FROM {$CFG->prefix}resource r, " .
                      "{$CFG->prefix}course_modules cm, " .
                      "{$CFG->prefix}modules m " .
                 "WHERE r.course    = '{$course->id}' " .
                   "AND m.name      = 'resource' " .
                   "AND cm.module   = m.id " .
                   "AND cm.instance = r.id " .
                   "AND cm.visible  = 0 " .
                   "AND r.type      = 'file' " .
                   "AND r.reference = '{$reference}'";
        if (count_records_sql($sql)) {
           error('Access not allowed');
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
    session_write_close(); // unlock session during fileserving
    $filename = $args[count($args)-1];
    send_file($pathname, $filename, $lifetime, !empty($CFG->filteruploadedfiles), false, $forcedownload);

    function not_found($courseid) {
        global $CFG;
        header('HTTP/1.0 404 not found');
        error(get_string('filenotfound', 'error'), $CFG->wwwroot.'/course/view.php?id='.$courseid); //this is not displayed on IIS??
    }
?>
