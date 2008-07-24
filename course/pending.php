<?php  // $Id$
       // allow the administrators to look through a list of course requests and either approve them or reject them.

    require_once('../config.php');
    require_once($CFG->libdir.'/pagelib.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/eventslib.php');

    require_login();

    require_capability('moodle/site:approvecourse', get_context_instance(CONTEXT_SYSTEM));

    $approve      = optional_param('approve', 0, PARAM_INT);
    $reject       = optional_param('reject', 0, PARAM_INT);
    $rejectnotice = optional_param('rejectnotice', '', PARAM_CLEANHTML);

    if (!empty($approve) and confirm_sesskey()) {
        if ($course = $DB->get_record("course_request", array("id"=>$approve))) {

            $category = get_course_category($CFG->defaultrequestcategory);

            $course->category = $CFG->defaultrequestcategory;
            $course->sortorder = $category->sortorder; // place as the first in category 
            $course->requested = 1;
            unset($course->reason);
            unset($course->id);
            $teacherid = $course->requester;
            unset($course->requester);
            $course->teacher = get_string("defaultcourseteacher");
            $course->teachers = get_string("defaultcourseteachers");
            $course->student = get_string("defaultcoursestudent");
            $course->students = get_string("defaultcoursestudents");
            if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
                $course->restrictmodules = 1;
            }
            if ($courseid = $DB->insert_record("course",$course)) {
                $page = page_create_object(PAGE_COURSE_VIEW, $courseid);
                blocks_repopulate_page($page); // Return value not checked because you can always edit later
                $context = get_context_instance(CONTEXT_COURSE, $courseid);
                role_assign($CFG->creatornewroleid, $teacherid, 0, $context->id); // assing teacher role
                $course->id = $courseid;
                if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) { // if we're all or requested we're ok.
                    $allowedmods = explode(',',$CFG->defaultallowedmodules);
                    update_restricted_mods($course,$allowedmods);
                }
                $DB->delete_records('course_request', array('id'=>$approve));
                $success = 1;
                fix_course_sortorder();
            }
            if (!empty($success)) {
                $user = $DB->get_record('user', array('id'=>$teacherid));
                $a->name = $course->fullname;
                $a->url = $CFG->wwwroot.'/course/view.php?id='.$courseid;
                $a->teacher = $course->teacher;
                
                $eventdata = new object();
                $eventdata->modulename        = 'moodle';
                $eventdata->userfrom          = $USER;
                $eventdata->userto            = $user;
                $eventdata->subject           = get_string('courseapprovedsubject');
                $eventdata->fullmessage       = get_string('courseapprovedemail','moodle',$a);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';			    
                events_trigger('message_send', $eventdata);

                /*
                email_to_user($user,$USER,get_string('courseapprovedsubject'),get_string('courseapprovedemail','moodle',$a));
                */
                redirect($CFG->wwwroot.'/course/edit.php?id='.$courseid);
                exit;
            }
            else {
                print_error('courseapprovedfailed');
                exit;
            }
        }
    }

    $strtitle = get_string('coursespending');
    $strheading = get_string(((!empty($reject)) ? 'coursereject' : 'coursespending'));

    print_header($strtitle,$strheading,build_navigation(array(array('name'=>$strheading,'link'=>'','type'=>'misc'))));

    if (!empty($reject) and confirm_sesskey()) {
        if ($reject = $DB->get_record("course_request", array("id"=>$reject))) {
            if (empty($rejectnotice)) {
                //  display a form for writing a reason
                print_simple_box_start('center');
                print_string('courserejectreason');
                include('pending-reject.html');
                print_simple_box_end();
            }
            else {
                $user = $DB->get_record("user", array("id"=>$reject->requester));
                
                $eventdata = new object();
                $eventdata->modulename        = 'moodle';
                $eventdata->userfrom          = $USER;
                $eventdata->userto            = $user;
                $eventdata->subject           = get_string('courserejectsubject');
                $eventdata->fullmessage       = get_string('courserejectemail','moodle',$rejectnotice);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';			    
                events_trigger('message_send', $eventdata);
                /*
                email_to_user($user,$USER,get_string('courserejectsubject'),get_string('courserejectemail','moodle',$rejectnotice));
                */
                $DB->delete_records("course_request", array("id"=>$reject->id));
                notice(get_string('courserejected'),'pending.php');
            }
        }
    } else if ($pending = $DB->get_records("course_request")) {
        // loop through
        $table->cellpadding = 4;
        $table->cellspacing = 3;
        $table->align = array('center','center','center','center','center','center','center');
        $table->head = array('&nbsp;',get_string('shortnamecourse'),get_string('fullnamecourse'),get_string('requestedby'),get_string('summary'),
                               get_string('requestreason'),'');
        $strrequireskey = get_string('requireskey');
        foreach ($pending as $course) {
            $requester = $DB->get_record('user', array('id'=>$course->requester));
            // check here for shortname collisions and warn about them.
            if ($match = $DB->get_record("course", array("shortname"=>$course->shortname))) {
                $course->shortname .= ' [*]';
                $collision = 1;
            }
            //do not output raw html from request, quote html entities using s()!!
            $table->data[] = array(((!empty($course->password)) ?
                                    '<img hspace="1" alt="'.$strrequireskey.'" class="icon" src="'.$CFG->pixpath.'/i/key.gif" />' : ''),
                                   format_string($course->shortname),format_string($course->fullname),fullname($requester),
                                   format_string($course->summary),format_string($course->reason),
                                   '<a href="pending.php?approve='.$course->id.'&amp;sesskey='.sesskey().'">'.get_string('approve').'</a> | '
                                   .'<a href="pending.php?reject='.$course->id.'&amp;sesskey='.sesskey().'">'.get_string('reject').'</a>');
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
