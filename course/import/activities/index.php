<?php // preliminary page to find a course to import data from & interface with the backup/restore functionality

    require_once('../../../config.php');
    require_once('../../lib.php');
    require_once($CFG->dirroot.'/backup/restorelib.php');
    
    $strimportothercourses = get_string('importfromothercourses');
    $id = required_param('id', PARAM_INT);   // course id to import TO
    $fromcourse = optional_param('fromcourse',0,PARAM_INT);
    $fromcoursesearch = optional_param('fromcoursesearch','',PARAM_CLEAN);
    $page = optional_param('page',0,PARAM_INT);
    $filename = optional_param('filename',0,PARAM_PATH);

    if (! ($course = get_record("course", "id", $id)) ) {
        error("That's an invalid course id");
    }

    if (!$site = get_site()){ 
        error("Couldn't get site course");
    }

    require_login($course->id);

    if (!isteacheredit($course->id)) {
        error("You need to be a teacher or an admin to use this page");
    }

    // if we're not a course creator , we can only import from our own courses.
    if (iscreator()) {
        $creator = true;
    }

    if ($from = get_record("course","id",$fromcourse)) {
        if (!isteacheredit($fromcourse)) {
            error("You need to be a course creator, or a teacher in the course you are importing data from, as well");
        }
        if (!empty($filename) && file_exists($CFG->dataroot.'/'.$filename) && !empty($SESSION->import_preferences)) {
            $restore = backup_to_restore_array($SESSION->import_preferences);
            $restore->restoreto = 1;
            $restore->course_id=$id; 
            $restore->importing=1; // magic variable so we know that we're importing rather than just restoring.
            
            $SESSION->restore = $restore;
            redirect($CFG->wwwroot.'/backup/restore.php?file='.$filename.'&id='.$fromcourse.'&to='.$id);
        }
        else {
            redirect($CFG->wwwroot.'/backup/backup.php?id='.$from->id.'&to='.$course->id);
        }
    }
    
    print_header("$course->shortname: $strimportothercourses", "$course->fullname", 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/course/import.php?id=$course->id\">".get_string('import')."</a> ".
                 "-> $strimportothercourses");
    require_once('mod.html');    

    print_footer();
?>
