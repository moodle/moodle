<?php

    // allow the administrators to look through a list of course requests and either approve them or reject them.
    require_once(dirname(dirname(__FILE__)).'/config.php');
    require_once(dirname(dirname(__FILE__)).'/lib/pagelib.php');
    require_once(dirname(dirname(__FILE__)).'/lib/blocklib.php'); 
    require_once(dirname(__FILE__).'/lib.php');

    require_login();

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    $approve = optional_param('approve',NULL,PARAM_INT);
    $reject = optional_param('reject',NULL,PARAM_INT);

    if (!empty($approve)) {
        if ($course = get_record("course_request","id",$approve)) {
            foreach (array_keys((array)$course) as $key) {
                $course->$key = addslashes($course->$key);
            }

            // place at beginning of category
            fix_course_sortorder();
            if (empty($CFG->defaultrequestedcategory)) {
                $CFG->defaultrequestedcategory = 1; //yuk, but default to miscellaneous.
            }
            $course->category = $CFG->defaultrequestedcategory;
            $course->sortorder = get_field_sql("SELECT min(sortorder)-1 FROM {$CFG->prefix}course WHERE category=$course->category");
            if (empty($course->sortorder)) {
                $course->sortorder = 1000;
            }
            $course->requested = 1;
            unset($course->reason);
            unset($course->id);
            $teacherid = $course->requester;
            unset($course->requester);
            $course->teacher = get_string("defaultcourseteacher");
            if (!empty($CFG->requestedteachername)) {
                $course->teacher = $CFG->requestedteachername;
            }
            $course->teachers = get_string("defaultcourseteachers");
            if (!empty($CFG->requestedteachersname)) {
                $course->teachers = $CFG->requestedteachersname;
            }
            $course->student = get_string("defaultcoursestudent");
            if (!empty($CFG->requestedstudentname)) {
                $course->student = $CFG->requestedstudentname;
            }
            $course->students = get_string("defaultcoursestudents");
            if (!empty($CFG->requestedstudentsname)) {
                $course->students = $CFG->requestedstudentsname;
            }
            if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
                $course->restrictmodules = 1;
            }
            if ($courseid = insert_record("course",$course)) {
                $page = page_create_object(PAGE_COURSE_VIEW, $courseid);
                blocks_repopulate_page($page); // Return value not checked because you can always edit later
                add_teacher($teacherid,$courseid);
                $course->id = $courseid;
                if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) { // if we're all or requested we're ok.
                    $allowedmods = explode(',',$CFG->defaultallowedmodules);
                    update_restricted_mods($course,$allowedmods);
                }
                delete_records('course_request','id',$approve);
                $success = 1;
            }
            if (!empty($success)) {
                $user = get_record('user','id',$teacherid);
                $a->name = $course->fullname;
                $a->url = $CFG->wwwroot.'/course/view.php?id='.$courseid;
                $a->teacher = $course->teacher;
                email_to_user($user,$USER,get_string('courseapprovedsubject'),get_string('courseapprovedemail','moodle',$a));
                redirect($CFG->wwwroot.'/course/edit.php?id='.$courseid);
                exit;
            }
            else {
                error(get_string('courseapprovedfailed'));
                exit;
            }
        }
    }
 
    $strtitle = get_string('coursespending');
    $strheading = get_string(((!empty($reject)) ? 'coursereject' : 'coursespending'));

    print_header($strtitle,$strheading,$strheading);
 
    if (!empty($reject)) {
        if ($reject = get_record("course_request","id",$reject)) {
            $rejectnotice = stripslashes(optional_param('rejectnotice',NULL,PARAM_CLEAN));
            if (empty($rejectnotice)) {
                //  display a form for writing a reason
                print_simple_box_start('center');
                print_string('courserejectreason');
                include('pending-reject.html');
                print_simple_box_end();
            }
            else {
                $user = get_record("user","id",$reject->requester);
                email_to_user($user,$USER,get_string('courserejectsubject'),get_string('courserejectemail','moodle',$rejectnotice));
                delete_records("course_request","id",$reject->id);
                notice(get_string('courserejected'),'pending.php');
            }
        }
    } else if ($pending = get_records("course_request")) {
        // loop through
        $table->cellpadding = 4;
        $table->cellspacing = 3;
        $table->align = array('center','center','center','center','center','center','center');
        $table->head = array('&nbsp',get_string('shortname'),get_string('fullname'),get_string('requestedby'),get_string('summary'),
                               get_string('requestreason'),'');
        $strrequireskey = get_string('requireskey');
        foreach ($pending as $course) {
            $requester = get_record('user','id',$course->requester);
            // check here for shortname collisions and warn about them.
            if ($match = get_record("course","shortname",$course->shortname)) {
                $course->shortname .= ' [*]';
                $collision = 1;
            }
            $table->data[] = array(((!empty($course->password)) ? 
                                    '<img hspace="1" alt="'.$strrequireskey.'" height="16" width="16" border="0" src="'.$CFG->pixpath.'/i/key.gif" />' : ''),
                                   $course->shortname,$course->fullname,fullname($requester),
                                   $course->summary,$course->reason,
                                   '<a href="pending.php?approve='.$course->id.'">'.get_string('approve').'</a> | '
                                   .'<a href="pending.php?reject='.$course->id.'">'.get_string('reject').'</a>');
        }
        print_table($table);
        if (!empty($collision)) {
            print_string('shortnamecollisionwarning');
        }
    } else {
        notice(get_string('nopendingcourses'));
        // no pending messages.
    }

print_footer();


?>