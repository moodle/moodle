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
    if ($context->contextlevel != CONTEXT_USER) {
        print_error('invalidarguments');
    }

    $userid = $context->instanceid;
    if ($USER->id != $userid) {
        print_error('invaliduserid');
    }

    switch ($filearea) {
        case 'user_profile':
            if (!empty($CFG->forceloginforprofiles)) {
                require_login();
                if (isguestuser()) {
                    print_error('noguest');
                }
                $user = $DB->get_record("user", array("id"=>$userid));
                $usercontext   = get_context_instance(CONTEXT_USER, $user->id);
                if (!isteacherinanycourse()
                    and !isteacherinanycourse($user->id)
                    and !has_capability('moodle/user:viewdetails', $usercontext)) {
                    print_error('usernotavailable');
                }
                //TODO: find a way to get $coursecontext .. or equivalent check.
                //if (!has_capability('moodle/user:viewdetails', $coursecontext) &&
                //    !has_capability('moodle/user:viewdetails', $usercontext)) {
                //    print_error('cannotviewprofile');
                //}
                //if (!has_capability('moodle/course:view', $coursecontext, $user->id, false)) {
                //    print_error('notenrolledprofile');
                //}
                //if (groups_get_course_groupmode($course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $coursecontext)) {
                //    print_error('groupnotamember');
                //}
            }
            $itemid = 0;
            $forcedownload = true;
            break;
        case 'user_private':
            require_login();
            if (isguestuser()) {
                print_error('noguest');
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
