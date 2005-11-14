<?php // preliminary page to find a course to import data from & interface with the backup/restore functionality

require_once('../config.php');
require_once('lib.php');
require_once('../backup/restorelib.php');

require_variable($id, 0);   // course id to import TO
optional_variable($fromcourse,0);
optional_variable($fromcoursesearch,'');
optional_variable($page,0);
optional_variable($filename,0);

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

    $strimport = get_string("importdata");

    print_header("$course->fullname: $strimport", "$course->fullname: $strimport", "$course->shortname");

    print_heading(get_string("importdatafrom"));

    $tcourseids = '';

    if ($teachers = get_records_select('user_teachers', "userid = $USER->id AND editall = 1",'','id,course')) {
        foreach ($teachers as $teacher) {
            if ($teacher->course != $course->id && $teacher->course != $site->id){
                $tcourseids .= $teacher->course.',';
            }
        }
    }

    $taught_courses = array();
    if (!empty($tcourseids)) {
        $tcourseids = substr($tcourseids,0,-1);
        $taught_courses = get_records_list('course', 'id', $tcourseids);
    }

    if (!empty($creator)) {
        $cat_courses = get_courses($course->category);
    } else {
        $cat_courses = array();
    }

    $options = array();
    foreach ($taught_courses as $tcourse) {
        if ($tcourse->id != $course->id && $tcourse->id != $site->id){
            $options[$tcourse->id] = $tcourse->fullname;
        }
    }

    if (empty($options) && empty($creator)) {
        error(get_string('courseimportnotaught'),$CFG->wwwroot.'/course/view.php?id='.$course->id);
        print_footer();
        die();
    }


    $fm = '<form method="post" action="'.$CFG->wwwroot.'/course/import.php"><input type="hidden" name="id" value="'.$course->id.'" />';
    $submit = '<input type="submit" value="'.get_string('usethiscourse').'" /></form>';

    if (count($options) > 0) {
        $table->data[] = array($fm.'<b>'.get_string('coursestaught').'</b>',
                               choose_from_menu($options,"fromcourse","","choose","","0",true),
                               $submit);
    }

    unset($options);

    $options = array();
    foreach ($cat_courses as $ccourse) {
        if ($ccourse->id != $course->id && $ccourse->id != $site->id){
            $options[$ccourse->id] = $ccourse->fullname;
        }
    }
    $cat = get_record("course_categories","id",$course->category);

    if (count($options) > 0) {
        $table->data[] = array($fm.'<b>'.get_string('coursescategory').' ('.$cat->name .')</b>',
                               choose_from_menu($options,"fromcourse","","choose","","0",true),
                               $submit);
    }

    if (!empty($creator)) {
        $table->data[] = array($fm.'<b>'.get_string('searchcourses').'</b>',
                               '<input type="text" name="fromcoursesearch" />',
                               '<input type="submit" value="'.get_string('searchcourses').'" />');
    }

    if (!empty($fromcoursesearch) && !empty($creator)) {
        $totalcount = 0;
        $courses = get_courses_search(explode(" ",$fromcoursesearch),"fullname ASC",$page,50,$totalcount);
        if (is_array($courses) and count($courses) > 0) {
            $table->data[] = array('<b>'.get_string('searchresults').'</b>','','');
            foreach ($courses as $scourse) {
                if ($course->id != $scourse->id) {
                    $table->data[] = array('',$scourse->fullname,
                                           '<a href="'.$CFG->wwwroot.'/course/import.php?id='.$course->id.'&fromcourse='.$scourse->id.'">'
                                           .get_string('usethiscourse'));
                }
            }
        }
        else {
            $table->data[] = array('',get_string('noresults'),'');
        }
    }

    print_table($table);
    print_footer();
?>