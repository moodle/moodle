<?php  // $Id$

    require_once('config.php');
    require_once('lib/filelib.php');

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('userfile.php');
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
    if ($context->contextlevel != CONTEXT_USER) {
        print_error('invalidarguments');
    }

    $userid = $context->instanceid;

    switch ($filearea) {
        case 'user_profile':
            require_login();
            if (isguestuser()) {
                print_error('noguest');
            }

            // access controll here must match user edit forms
            if ($userid == $USER->id) {
                 if (!has_capability('moodle/user:editownprofile', get_context_instance(CONTEXT_SYSTEM))) {
                    send_file_not_found();
                 }
            } else { 
                if (!has_capability('moodle/user:editprofile', $context) and !has_capability('moodle/user:update', $context)) {
                    send_file_not_found();
                }
            }
            $itemid = 0;
            $forcedownload = true;
            break;

        case 'user_private':
            require_login();
            if (isguestuser()) {
                send_file_not_found();
            }
            if ($USER->id != $userid) {
                send_file_not_found();
            }
            $itemid = 0;
            $forcedownload = true;
            break;

        default:
            send_file_not_found();
    }
    
    $relativepath = '/'.implode('/', $args);

    $fs = get_file_storage();

    $fullpath = $context->id.$filearea.$itemid.$relativepath;

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->get_filename() == '.') {
        send_file_not_found();
    }

    // ========================================
    // finally send the file
    // ========================================
    session_write_close(); // unlock session during fileserving
    send_stored_file($file, 0, false, $forcedownload);
