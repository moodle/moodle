<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id         = required_param('id', PARAM_INT);                 // Course Module ID
    $action     = optional_param('action', '', PARAM_ALPHA);
    $attemptids = optional_param('attemptid', array(), PARAM_INT); // array of attempt ids for delete action
    
    if (! $cm = get_coursemodule_from_id('choice', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_course_login($course, false, $cm);

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }
    
    $strchoice = get_string('modulename', 'choice');
    $strchoices = get_string('modulenameplural', 'choice');

    if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
    }

if ($action == 'delchoice') {
    if ($answer = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $USER->id)) {
        //print_object($answer);
        delete_records('choice_answers', 'id', $answer->id);
    }
}

/// Submit any new data if there is any

    if ($form = data_submitted() && has_capability('mod/choice:choose', $context)) {
        $timenow = time();
        if (has_capability('mod/choice:deleteresponses', $context)) {
            if ($action == 'delete') { //some responses need to be deleted     
                choice_delete_responses($attemptids); //delete responses.
                redirect("view.php?id=$cm->id");
            }
        }
        $answer = optional_param('answer', '', PARAM_INT);

        if (empty($answer)) {
            redirect("view.php?id=$cm->id", get_string('mustchooseone', 'choice'));
        } else {
            choice_user_submit_response($answer, $choice, $USER->id, $course->id, $cm);
        }
        redirect("view.php?id=$cm->id");
        exit;
    }


/// Display the choice and possibly results
    $navlinks = array();
    $navlinks[] = array('name' => $strchoices, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($choice->name), 'link' => '', 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);

    print_header_simple(format_string($choice->name), "", $navigation, "", "", true,
                  update_module_button($cm->id, $course->id, $strchoice), navmenu($course, $cm));

    add_to_log($course->id, "choice", "view", "view.php?id=$cm->id", $choice->id, $cm->id);

    /// Check to see if groups are being used in this choice
    $groupmode = groups_get_activity_groupmode($cm);
    groups_get_activity_group($cm, true);
    groups_print_activity_menu($cm, 'view.php?id='.$id);
                                   
    if (has_capability('mod/choice:readresponses', $context)) {
        choice_show_reportlink($choice, $course->id, $cm->id, $groupmode);
    }

    echo '<div class="clearer"></div>';

    if ($choice->text) {
        print_box(format_text($choice->text, $choice->format), 'generalbox', 'intro');
    }

    //if user has already made a selection, and they are not allowed to update it, show their selected answer.
    if (!empty($USER->id) && ($current = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $USER->id)) &&
        empty($choice->allowupdate) ) {
        print_simple_box(get_string("yourselection", "choice", userdate($choice->timeopen)).": ".format_string(choice_get_option_text($choice, $current->optionid)), "center");
    }

/// Print the form

    if ($choice->timeopen > time() ) {
        print_simple_box(get_string("notopenyet", "choice", userdate($choice->timeopen)), "center");
        print_footer($course);
        exit;
    }

    if ( (!$current or $choice->allowupdate) and 
         ($choice->timeclose >= time() or $choice->timeclose == 0) and 
          has_capability('mod/choice:choose', $context) ) {
    // They haven't made their choice yet or updates allowed and choice is open

        echo '<form id="form" method="post" action="view.php">';        

        choice_show_form($choice, $USER, $cm);
        
        echo '</form>';

        $choiceformshown = true;
    } else {
        $choiceformshown = false;
    }



    if (!$choiceformshown) {

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        if (has_capability('moodle/legacy:guest', $sitecontext, NULL, false)) {      // Guest on whole site
            $wwwroot = $CFG->wwwroot.'/login/index.php';
            if (!empty($CFG->loginhttps)) {
                $wwwroot = str_replace('http:','https:', $wwwroot);
            }
            notice_yesno(get_string('noguestchoose', 'choice').'<br /><br />'.get_string('liketologin'),
                         $wwwroot, $_SERVER['HTTP_REFERER']);

        } else if (has_capability('moodle/legacy:guest', $context, NULL, false)) {   // Guest in this course only
            $SESSION->wantsurl = $FULLME;
            $SESSION->enrolcancel = $_SERVER['HTTP_REFERER'];

            print_simple_box_start('center', '60%', '', 5, 'generalbox', 'notice');
            echo '<p align="center">'. get_string('noguestchoose', 'choice') .'</p>';
            echo '<div class="continuebutton">';
            print_single_button($CFG->wwwroot.'/course/enrol.php?id='.$course->id, NULL, 
                                get_string('enrolme', '', format_string($course->shortname)), 'post', $CFG->framename);
            echo '</div>'."\n";
            print_simple_box_end();

        }
    }

    // print the results at the bottom of the screen

    if ( $choice->showresults == CHOICE_SHOWRESULTS_ALWAYS or
        ($choice->showresults == CHOICE_SHOWRESULTS_AFTER_ANSWER and $current ) or
        ($choice->showresults == CHOICE_SHOWRESULTS_AFTER_CLOSE and $choice->timeclose <= time() ) )  {

        choice_show_results($choice, $course, $cm);

    } else if (!$choiceformshown) {
        print_simple_box(get_string('noresultsviewable', 'choice'), 'center');
    } 


    print_footer($course);


?>
