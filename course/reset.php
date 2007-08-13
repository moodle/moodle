<?php  // $Id$
/*
resetcourse.php  - Mark Flach and moodle.com
The purpose of this feature is to quickly remove all user related data from a course 
in order to make it available for a new semester.  This feature can handle the removal 
of general course data like students, teachers, logs, events and groups as well as module 
specific data.  Each module must be modified to take advantage of this new feature.  
The feature will also reset the start date of the course if necessary.
*/
    require('../config.php');

    $id = required_param('id', PARAM_INT);

    if (! $course = get_record('course', 'id', $id)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    require_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id));

    $strreset = get_string('reset');
    $strresetcourse = get_string('resetcourse');
    $strremove = get_string('remove');

    print_header($course->fullname.': '.$strresetcourse, $course->fullname.': '.$strresetcourse, 
                 '<a href="view.php?id='.$course->id.'">'.$course->shortname.'</a> -> '.$strresetcourse);
    
    print_simple_box_start();

    print_heading($strresetcourse);

/// If we have data, then process it.
    if ($data = data_submitted() and confirm_sesskey()) {

        $data->courseid = $course->id;

        reset_course_userdata($data, true);

        if (!empty($data->reset_start_date)) {
            if (set_field('course', 'startdate', 
                             make_timestamp($data->startyear, $data->startmonth, $data->startday), 
                             'id', $course->id)) {
                notify(get_string('datechanged'), 'notifysuccess');
            }
        }
        print_continue('view.php?id='.$course->id);  // Back to course page
        print_simple_box_end();
        print_footer($course);
        exit;
    }



/// Print forms so the user can make choices about what to delete

    print_simple_box(get_string('resetinfo'), 'center', '60%');

    echo '<form id="reset" action="reset.php" method="POST">';

    print_heading(get_string('course'), 'left', 3);

    echo '<div class="courseinfo">';
    echo $strremove.':<br />';
    print_checkbox('reset_students', 1, true, get_string('students'), '', '');  echo '<br />';
    print_checkbox('reset_teachers', 1, true, get_string('teachers'), '', '');  echo '<br />';
    print_checkbox('reset_events', 1, true, get_string('courseevents', 'calendar'), '', '');  echo '<br />';
    print_checkbox('reset_logs', 1, true, get_string('logs'), '', '');  echo '<br />';
    print_checkbox('reset_groups', 1, true, get_string('groups'), '', '');  echo '<br />';
    print_checkbox('reset_start_date', 1, true, get_string('startdate'), '', ''); 
    print_date_selector('startday', 'startmonth', 'startyear');
    helpbutton('coursestartdate', get_string('startdate'));
    echo '</div>';

    // Check each module and see if there is specific data to be removed

    if ($allmods = get_records('modules') ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = $CFG->dirroot .'/mod/'. $modname .'/lib.php';
            $mod_reset_course_form = $modname .'_reset_course_form'; 
            if (file_exists($modfile)) {
                @include_once($modfile);
                if (function_exists($mod_reset_course_form)) {
                    print_heading(get_string('modulenameplural', $modname), 'left', 3);
                    echo '<div class="'.$modname.'info">';
                    $mod_reset_course_form($course);
                    echo '</div>';
                }
            }
        }
    } else {
        error('No modules are installed!');
    }
    
    echo '<input name="id" value="'.$course->id.'" type="hidden" />';
    echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
    echo '<p align="center"><input name="submit" value="'.$strresetcourse.'" type="submit" /></p>';
    echo '</form>';
    
    print_simple_box_end();
    print_footer($course);

?>
