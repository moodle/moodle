<?php // $Id$
      // This script fetches files from the dataroot directory
      //
      // You should use the get_file_url() function, available in lib/filelib.php, to link to file.php.
      // This ensures proper formatting and offers useful options.
      //
      // Syntax:      file.php/courseid/dir/dir/dir/filename.ext
      //              file.php/courseid/dir/dir/dir/filename.ext?forcedownload=1 (download instead of inline)
      //              file.php/courseid/dir (returns index.html from dir)
      // Workaround:  file.php?file=/courseid/dir/dir/dir/filename.ext
      // Test:        file.php/testslasharguments


      //TODO: Blog attachments do not have access control implemented - anybody can read them!
      //      It might be better to move the code to separate file because the access
      //      control is quite complex - see bolg/index.php 

    require_once('config.php');
    require_once('lib/filelib.php');

    if (!isset($CFG->filelifetime)) {
        $lifetime = 86400;     // Seconds for files to remain in caches
    } else {
        $lifetime = $CFG->filelifetime;
    }

    // disable moodle specific debug messages
    disable_debugging();

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
    if (($args[0]!='blog') and (!$course = get_record_sql("SELECT * FROM {$CFG->prefix}course WHERE id='".(int)$args[0]."'"))) {
        error('Invalid course ID');
    }

    // security: prevent access to "000" or "1 something" directories
    // hack for blogs, needs proper security check too
    if (($args[0] != 'blog') and ($args[0] != $course->id)) {
        error('Invalid course ID');
    }

    // security: login to course if necessary
    // Note: file.php always calls require_login() with $setwantsurltome=false
    //       in order to avoid messing redirects. MDL-14495
    if ($args[0] == 'blog') {
        if (empty($CFG->bloglevel)) {
            error('Blogging is disabled!');
        } else if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
            require_login(0, true, null, false);
        } else if ($CFG->forcelogin) {
            require_login(0, true, null, false);
        }
    } else if ($course->id != SITEID) {
        require_login($course->id, true, null, false);
    } else if ($CFG->forcelogin) {
        if (!empty($CFG->sitepolicy)
            and ($CFG->sitepolicy == $CFG->wwwroot.'/file.php'.$relativepath
                 or $CFG->sitepolicy == $CFG->wwwroot.'/file.php?file='.$relativepath)) {
            //do not require login for policy file
        } else {
            require_login(0, true, null, false);
        }
    }

    // security: only editing teachers can access backups
    if ((count($args) >= 2) and (strtolower($args[1]) == 'backupdata')) {
        if (!has_capability('moodle/site:backup', get_context_instance(CONTEXT_COURSE, $course->id))) {
            error('Access not allowed');
        } else {
            $lifetime = 0; //disable browser caching for backups 
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

    // security: teachers can view all assignments, students only their own
    if ((count($args) >= 3)
        and (strtolower($args[1]) == 'moddata')
        and (strtolower($args[2]) == 'assignment')) {

        $lifetime = 0;  // do not cache assignments, students may reupload them
        if ($args[4] == $USER->id) {
            //can view own assignemnt submissions
        } else {
            $instance = (int)$args[3];
            if (!$cm = get_coursemodule_from_instance('assignment', $instance, $course->id)) {
                not_found($course->id);
            }
            if (!has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
                error('Access not allowed');
            }
        } 
    }

    // security: force download of all attachments submitted by students
    if (count($args) >= 3 and strtolower($args[1]) === 'moddata') {
        $mod = clean_param($args[2], PARAM_SAFEDIR);
        if (file_exists("$CFG->dirroot/mod/$mod/lib.php")) {
            if (!$forcedownload) {
                require_once("$CFG->dirroot/mod/$mod/lib.php");
                $trustedfunction = $mod.'_is_moddata_trusted';
                if (function_exists($trustedfunction)) {
                    // force download of all attachments that are not trusted
                    $forcedownload = !$trustedfunction();
                } else {
                    $forcedownload = 1;
                }
            }
        } else {
            // module is not installed - better not serve file at all
            not_found($course->id);
        }
    }
    if ($args[0] == 'blog') {
        $forcedownload  = 1; // force download of all attachments
    }    

    // security: some protection of hidden resource files
    // warning: it may break backwards compatibility
    if ((!empty($CFG->preventaccesstohiddenfiles)) 
        and (count($args) >= 2)
        and (!(strtolower($args[1]) == 'moddata' and strtolower($args[2]) != 'resource')) // do not block files from other modules!
        and (!has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE, $course->id)))) {

        $rargs = $args;
        array_shift($rargs);
        $reference = implode('/', $rargs);

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

    // ========================================
    // finally send the file
    // ========================================
    session_write_close(); // unlock session during fileserving
    $filename = $args[count($args)-1];
    send_file($pathname, $filename, $lifetime, $CFG->filteruploadedfiles, false, $forcedownload);

    function not_found($courseid) {
        global $CFG;
        header('HTTP/1.0 404 not found');
        print_error('filenotfound', 'error', $CFG->wwwroot.'/course/view.php?id='.$courseid); //this is not displayed on IIS??
    }
?>
