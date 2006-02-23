<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID
    $action = optional_param('action', '');
    
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_course_login($course, false, $cm);

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }
    
    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");
    


/// Submit any new data if there is any

    if ($form = data_submitted()) {
        $timenow = time();

        if (isteacher($course->id, $user->id)) {
            if ($action == 'delete') { //some responses need to be deleted     
                $attemptids = isset($_POST['attemptid']) ? $_POST['attemptid'] : array(); //get array of repsonses to delete.
                choice_delete_responses($attemptids); //delete responses.
                redirect("view.php?id=$cm->id");
            }
        }    
        
        if (empty($form->answer)) {
            redirect("view.php?id=$cm->id", get_string('mustchooseone', 'choice'));
        } else {
            choice_user_submit_response($form->answer, $choice, $USER->id);
        }
        redirect("view.php?id=$cm->id");
        exit;
    }


/// Display the choice and possibly results

    add_to_log($course->id, "choice", "view", "view.php?id=$cm->id", $choice->id, $cm->id);

    print_header_simple(format_string($choice->name), "",
                 "<a href=\"index.php?id=$course->id\">$strchoices</a> -> ".format_string($choice->name), "", "", true,
                  update_module_button($cm->id, $course->id, $strchoice), navmenu($course, $cm));
                                                      

    if (isteacher($course->id)) {
        choice_show_reportlink($choice, $course->id, $cm->id);
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    if ($choice->text) {
        print_simple_box(format_text($choice->text, $choice->format), 'center', '70%', '', 5, 'generalbox', 'intro');
    }

    //if user has already made a selection, and they are not allowed to update it, show their selected answer.
    if (isset($USER->id) && ($current = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $USER->id)) && !$choice->allowupdate) {
        print_simple_box(get_string("yourselection", "choice", userdate($choice->timeopen)).": ".format_string(choice_get_option_text($choice, $current->optionid)), "center");
    }

/// Print the form

    if ($choice->timeopen > time() ) {
        print_simple_box(get_string("notopenyet", "choice", userdate($choice->timeopen)), "center");
        print_footer($course);
        exit;
    }

    if ( (!$current or $choice->allowupdate) and ($choice->timeclose >= time() or $choice->timeclose == 0) ) {
    // They haven't made their choice yet or updates allowed and choice is open

        echo "<form name=\"form\" method=\"post\" action=\"view.php\">";        

        choice_show_form($choice, $USER, $cm);
        
        echo "</form>";

    }



    // print the results at the bottom of the screen

    if (  $choice->showresults == CHOICE_SHOWRESULTS_ALWAYS or
        ( $choice->showresults == CHOICE_SHOWRESULTS_AFTER_ANSWER and $current ) or
        ( $choice->showresults == CHOICE_SHOWRESULTS_AFTER_CLOSE and $choice->timeclose <= time() ) )  {

		choice_show_results($choice, $course, $cm);
    }

    print_footer($course);


?>
