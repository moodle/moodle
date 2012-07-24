<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// Edit course completion settings

require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_self.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_date.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_unenrol.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_activity.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_duration.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_grade.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_role.php');
require_once($CFG->dirroot.'/completion/criteria/completion_criteria_course.php');
require_once $CFG->libdir.'/gradelib.php';
require_once('completion_form.php');

$id = required_param('id', PARAM_INT);       // course id

/// basic access control checks
if ($id) { // editing course

    if($id == SITEID){
        // don't allow editing of  'site course' using this from
        print_error('cannoteditsiteform');
    }

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error('invalidcourseid');
    }
    require_login($course);
    require_capability('moodle/course:update', context_course::instance($course->id));

} else {
    require_login();
    print_error('needcourseid');
}

/// Set up the page
$streditcompletionsettings = get_string("editcoursecompletionsettings", 'completion');
$PAGE->set_course($course);
$PAGE->set_url('/course/completion.php', array('id' => $course->id));
//$PAGE->navbar->add($streditcompletionsettings);
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');

/// first create the form
$form = new course_completion_form('completion.php?id='.$id, compact('course'));

// now override defaults if course already exists
if ($form->is_cancelled()){
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);

} else if ($data = $form->get_data()) {

    $completion = new completion_info($course);

/// process criteria unlocking if requested
    if (!empty($data->settingsunlock)) {

        $completion->delete_course_completion_data();

        // Return to form (now unlocked)
        redirect($CFG->wwwroot."/course/completion.php?id=$course->id");
    }

/// process data if submitted
    // Delete old criteria
    $completion->clear_criteria();

    // Loop through each criteria type and run update_config
    global $COMPLETION_CRITERIA_TYPES;
    foreach ($COMPLETION_CRITERIA_TYPES as $type) {
        $class = 'completion_criteria_'.$type;
        $criterion = new $class();
        $criterion->update_config($data);
    }

    // Handle aggregation methods
    // Overall aggregation
    $aggdata = array(
        'course'        => $data->id,
        'criteriatype'  => null
    );
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->overall_aggregation);
    $aggregation->save();

    // Activity aggregation
    if (empty($data->activity_aggregation)) {
        $data->activity_aggregation = 0;
    }

    $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_ACTIVITY;
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->activity_aggregation);
    $aggregation->save();

    // Course aggregation
    if (empty($data->course_aggregation)) {
        $data->course_aggregation = 0;
    }

    $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_COURSE;
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->course_aggregation);
    $aggregation->save();

    // Role aggregation
    if (empty($data->role_aggregation)) {
        $data->role_aggregation = 0;
    }

    $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_ROLE;
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->role_aggregation);
    $aggregation->save();

    add_to_log($course->id, 'course', 'completion updated', 'completion.php?id='.$course->id);

    $url = new moodle_url('/course/view.php', array('id' => $course->id));
    redirect($url);
}


/// Print the form


echo $OUTPUT->header();
echo $OUTPUT->heading($streditcompletionsettings);

$form->display();

echo $OUTPUT->footer();
