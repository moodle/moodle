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
        print_error('invalidargorconf');
    } else if ($relativepath{0} != '/') {
        print_error('pathdoesnotstartslash');
    }

    // extract relative path components
    $args = explode('/', ltrim($relativepath, '/'));

    if (count($args) == 0) { // always at least courseid, may search for index.html in course root
        print_error('invalidarguments');
    }

    $courseid = (int)array_shift($args);
    $relativepath = '/'.implode('/', $args);

    // security: limit access to existing course subdirectories
    if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
        print_error('invalidcourseid');
    }

    if ($course->id != SITEID) {
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

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $fs = get_file_storage();

    $fullpath = $context->id.'course_content0'.$relativepath;

    if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
        if (strrpos($fullpath, '/') !== strlen($fullpath) -1 ) {
            $fullpath .= '/';
        }
        if (!$file = $fs->get_file_by_hash(sha1($fullpath.'/.'))) {
            send_file_not_found();
        }
    }
    // do not serve dirs
    if ($file->get_filename() == '.') {
        if (!$file = $fs->get_file_by_hash(sha1($fullpath.'index.html'))) {
            if (!$file = $fs->get_file_by_hash(sha1($fullpath.'index.htm'))) {
                if (!$file = $fs->get_file_by_hash(sha1($fullpath.'Default.htm'))) {
                    send_file_not_found();
                }
            }
        }
    }

    // ========================================
    // finally send the file
    // ========================================
    session_write_close(); // unlock session during fileserving
    send_stored_file($file, $lifetime, $CFG->filteruploadedfiles, $forcedownload);


