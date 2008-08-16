<?php  // $Id$

    require_once('config.php');
    require_once('lib/filelib.php');

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('file.php');
    $forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);

    // relative path must start with '/'
    if (!$relativepath) {
        print_error('invalidargorconf');
    } else if ($relativepath{0} != '/') {
        print_error('pathdoesnotstartslash');
    }

    // extract relative path components
    $args = explode('/', ltrim($relativepath, '/'));

    if (count($args) == 0) { // always at least user id
        print_error('invalidarguments');
    }

    $contextid = (int)array_shift($args);
    $filearea = array_shift($args);

    $context = get_context_instance_by_id($contextid);
    $fs = get_file_storage();


    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'blog') {

            if (empty($CFG->bloglevel)) {
                print_error('siteblogdisable', 'blog');
            }
            if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
                require_login();
                if (isguestuser()) {
                    print_error('noguest');
                }
                if ($CFG->bloglevel == BLOG_USER_LEVEL) {
                    if ($USER->id != $entry->userid) {
                        send_file_not_found();
                    }
                }
            }
            $entryid = (int)array_shift($args);
            if (!$entry = $DB->get_record('post', array('module'=>'blog', 'id'=>$entryid))) {
                send_file_not_found();
            }
            if ('publishstate' === 'public') {
                if ($CFG->forcelogin) {
                    require_login();
                }

            } else if ('publishstate' === 'site') {
                require_login();
                //ok
            } else if ('publishstate' === 'draft') {
                require_login();
                if ($USER->id != $entry->userid) {
                    send_file_not_found();
                }
            }

            //TODO: implement shared course and shared group access

            $relativepath = '/'.implode('/', $args);
            $fullpath = $context->id.'blog'.$entryid.$relativepath;

            if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
                send_file_not_found();
            }

            send_stored_file($file, 10*60, 0, true); // download MUST be forced - security!

        } else {
            send_file_not_found();
        }


    } else if ($context->contextlevel == CONTEXT_USER) {
        send_file_not_found();


    } else if ($context->contextlevel == CONTEXT_COURSECAT) {
        if ($filearea !== 'intro') {
            send_file_not_found();
        }

        if ($CFG->forcelogin) {
            // no login necessary - unless login forced everywhere
            require_login();
        }

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'intro0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->get_filename() == '.') {
            send_file_not_found();
        }

        session_write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);


    } else if ($context->contextlevel == CONTEXT_COURSE) {
        if ($filearea !== 'intro' and $filearea !== 'backup') {
            send_file_not_found();
        }

        if (!$course = $DB->get_record('course', array('id'=>$context->instanceid))) {
            print_error('invalidcourseid');
        }

        if ($filearea === 'backup') {
            require_login($course);
            require_capability('moodle/site:backupdownload', $context);
        } else {
            if ($CFG->forcelogin) {
                require_login();
            }
        }

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'intro0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);


    } else if ($context->contextlevel == CONTEXT_MODULE) {
        
        if (!$coursecontext = get_context_instance_by_id(get_parent_contextid($context))) {
            send_file_not_found();
        }

        if (!$course = $DB->get_record('course', array('id'=>$coursecontext->instanceid))) {
            send_file_not_found();
        }
        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->cms[$context->instanceid])) {
            send_file_not_found();
        }

        $cminfo = $modinfo->cms[$context->instanceid];
        $modname = $cminfo->modname;
        $libfile = "$CFG->dirroot/mod/$modname/lib.php";
        if (file_exists($libfile)) {
            require_once($libfile);
            $filefunction = $modname.'_pluginfile';
            if (function_exists($filefunction)) {
                if ($filefunction($course, $cminfo, $context, $filearea, $args) !== false) {
                    die;
                }
            }
        }
        send_file_not_found();

    } else if ($context->contextlevel == CONTEXT_BLOCK) {
        //not supported yet
        send_file_not_found();


    } else {
        send_file_not_found();
    }
