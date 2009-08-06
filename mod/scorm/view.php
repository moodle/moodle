<?php  // $Id$

    require_once("../../config.php");
    require_once('locallib.php');
    
    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $organization = optional_param('organization', '', PARAM_INT); // organization ID

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $scorm = $DB->get_record("scorm", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else if (!empty($a)) {
        if (! $scorm = $DB->get_record("scorm", array("id"=>$a))) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$scorm->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {
        print_error('missingparameter');
    }

    require_login($course->id, false, $cm);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (isset($SESSION->scorm_scoid)) {
        unset($SESSION->scorm_scoid);
    }

    $strscorms = get_string("modulenameplural", "scorm");
    $strscorm  = get_string("modulename", "scorm");

    $pagetitle = strip_tags($course->shortname.': '.format_string($scorm->name));

    add_to_log($course->id, 'scorm', 'pre-view', 'view.php?id='.$cm->id, "$scorm->id", $cm->id);

    if ((has_capability('mod/scorm:skipview', get_context_instance(CONTEXT_MODULE,$cm->id))) && scorm_simple_play($scorm,$USER)) {
        exit;
    }

    //
    // Print the page header
    //
    $navlinks = array();
    $navigation = build_navigation($navlinks, $cm);
    
    print_header($pagetitle, $course->fullname, $navigation,
                 '', '', true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));

    if (has_capability('mod/scorm:viewreport', $context)) {
        
        $trackedusers = scorm_get_count_users($scorm->id, $cm->groupingid);
        if ($trackedusers > 0) {
            echo "<div class=\"reportlink\"><a $CFG->frametarget href=\"report.php?id=$cm->id\"> ".get_string('viewalluserreports','scorm',$trackedusers).'</a></div>';
        } else {
            echo '<div class="reportlink">'.get_string('noreports','scorm').'</div>';
        }
    }

    // Print the main part of the page
    echo $OUTPUT->heading(format_string($scorm->name));
    $attemptstatus = '';
    if ($scorm->displayattemptstatus == 1) {
        $attemptstatus = scorm_get_attempt_status($USER,$scorm);
    }
    print_simple_box(format_module_intro('scorm', $scorm, $cm->id).$attemptstatus, 'center', '70%', '', 5, 'generalbox', 'intro');
    
    $scormopen = true;
    $timenow = time();
    if ($scorm->timeclose !=0) {
        if ($scorm->timeopen > $timenow) {
            print_simple_box(get_string("notopenyet", "scorm", userdate($scorm->timeopen)), "center");
            $scormopen = false;
        } else if ($timenow > $scorm->timeclose) {
            print_simple_box(get_string("expired", "scorm", userdate($scorm->timeclose)), "center");
            $scormopen = false;
        }
    }
    if ($scormopen) {
        scorm_view_display($USER, $scorm, 'view.php?id='.$cm->id, $cm);
    }
    print_footer($course);
?>
