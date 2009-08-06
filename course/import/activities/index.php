<?php // preliminary page to find a course to import data from & interface with the backup/restore functionality

    require_once('../../../config.php');
    require_once('../../lib.php');
    require_once($CFG->dirroot.'/backup/lib.php');
    require_once($CFG->dirroot.'/backup/restorelib.php');

    $id               = required_param('id', PARAM_INT);   // course id to import TO
    $fromcourse       = optional_param('fromcourse', 0, PARAM_INT);
    $fromcoursesearch = optional_param('fromcoursesearch', '', PARAM_RAW);
    $page             = optional_param('page', 0, PARAM_INT);
    $filename         = optional_param('filename', 0, PARAM_PATH);

    $strimportactivities = get_string('importactivities');

    if (! ($course = $DB->get_record("course", array("id"=>$id)))) {
        print_error("invalidcourseid");
    }

    if (!$site = get_site()){
        print_error("siteisnotdefined", 'debug');
    }

    require_login($course->id);
    $tocontext = get_context_instance(CONTEXT_COURSE, $id);
    if ($fromcourse) {
        $fromcontext = get_context_instance(CONTEXT_COURSE, $fromcourse);
    }
    $syscontext = get_context_instance(CONTEXT_SYSTEM);

    if (!has_capability('moodle/course:manageactivities', $tocontext)) {
        print_error('nopermissiontoimportact');
    }

    // if we're not a course creator , we can only import from our own courses.
    if (has_capability('moodle/course:create', $syscontext)) {
        $creator = true;
    }

    if ($from = $DB->get_record('course', array('id'=>$fromcourse))) {
        if (!has_capability('moodle/course:manageactivities', $fromcontext)) {
            print_error('nopermissiontoimportact');
        }
        if (!empty($filename) && file_exists($CFG->dataroot.'/'.$filename) && !empty($SESSION->import_preferences)) {
            $restore = backup_to_restore_array($SESSION->import_preferences);
            $restore->restoreto = RESTORETO_CURRENT_ADDING;
            $restore->course_id = $id;
            $restore->importing = 1; // magic variable so we know that we're importing rather than just restoring.

            $SESSION->restore = $restore;
            redirect($CFG->wwwroot.'/backup/restore.php?file='.$filename.'&id='.$fromcourse.'&to='.$id);
        }
        else {
            redirect($CFG->wwwroot.'/backup/backup.php?id='.$from->id.'&to='.$course->id);
        }
    }

    $navlinks = array();
    $navlinks[] = array('name' => $course->shortname,
                        'link' => "$CFG->wwwroot/course/view.php?id=$course->id",
                        'type' => 'misc');
    $navlinks[] = array('name' => get_string('import'),
                        'link' => "$CFG->wwwroot/course/import.php?id=$course->id",
                        'type' => 'misc');
    $navlinks[] = array('name' => $strimportactivities, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$course->shortname: $strimportactivities", $course->fullname, $navigation);
    require_once('mod.php');

    echo $OUTPUT->footer();
?>
