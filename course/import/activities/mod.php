<?php  // $Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/backup/restorelib.php');

    $syscontext = get_context_instance(CONTEXT_SYSTEM);

    // if we're not a course creator , we can only import from our own courses.
    if (has_capability('moodle/course:create', $syscontext)) {
        $creator = true;
    }

    $strimport = get_string("importdata");

    $tcourseids = '';

    if ($teachers = get_user_capability_course('moodle/course:update')) {
        foreach ($teachers as $teacher) {
            if ($teacher->id != $course->id && $teacher->id != SITEID){
                $tcourseids .= $teacher->id.',';
            }
        }
    }

    $taught_courses = array();
    if (!empty($tcourseids)) {
        $tcourseids = substr($tcourseids,0,-1);
        $taught_courses = get_records_list('course', 'id', $tcourseids, 'sortorder', 'id, fullname');
    }

    if (!empty($creator)) {
        $cat_courses = get_courses($course->category, $sort="c.sortorder ASC", $fields="c.id, c.fullname");
    } else {
        $cat_courses = array();
    }

    print_heading(get_string("importactivities"));

    $options = array();
    foreach ($taught_courses as $tcourse) {
        if ($tcourse->id != $course->id && $tcourse->id != SITEID){
            $options[$tcourse->id] = format_string($tcourse->fullname);
        }
    }

    if (empty($options) && empty($creator)) {
        notify(get_string('courseimportnotaught'));
        return; // yay , this will pass control back to the file that included or required us.
    }

    // quick forms
    include_once('import_form.php');

    $mform_post = new course_import_activities_form_1($CFG->wwwroot.'/course/import/activities/index.php', array('options'=>$options, 'courseid' => $course->id, 'text'=> get_string('coursestaught')));
    $mform_post ->display();

    unset($options);
    $options = array();

    foreach ($cat_courses as $ccourse) {
        if ($ccourse->id != $course->id && $ccourse->id != SITEID) {
            $options[$ccourse->id] = format_string($ccourse->fullname);
        }
    }
    $cat = get_record("course_categories","id",$course->category);

    if (count($options) > 0) {
        $mform_post = new course_import_activities_form_1($CFG->wwwroot.'/course/import/activities/index.php', array('options'=>$options, 'courseid' => $course->id, 'text' => get_string('coursescategory')));
        $mform_post ->display();
    }

    if (!empty($creator)) {
        $mform_post = new course_import_activities_form_2($CFG->wwwroot.'/course/import/activities/index.php', array('courseid' => $course->id));
        $mform_post ->display();
    }

    if (!empty($fromcoursesearch) && !empty($creator)) {
        $totalcount = 0;
        $courses = get_courses_search(explode(" ",$fromcoursesearch),"fullname ASC",$page,50,$totalcount);
        if (is_array($courses) and count($courses) > 0) {
            $table->data[] = array('<b>'.get_string('searchresults').'</b>','','');
            foreach ($courses as $scourse) {
                if ($course->id != $scourse->id) {
                    $table->data[] = array('',format_string($scourse->fullname),
                                           '<a href="'.$CFG->wwwroot.'/course/import/activities/index.php?id='.$course->id.'&amp;fromcourse='.$scourse->id.'">'.get_string('usethiscourse').'</a>');
                }
            }
        }
        else {
            $table->data[] = array('',get_string('noresults'),'');
        }
    }
    if (!empty($table)) {
        print_table($table);
    }
?>
