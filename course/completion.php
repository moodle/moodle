<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit course completion settings
 *
 * @package     core_completion
 * @category    completion
 * @copyright   2009 Catalyst IT Ltd
 * @author      Aaron Barnes <aaronb@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../config.php');
require_once($CFG->dirroot.'/course/lib.php');
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
require_once($CFG->dirroot.'/course/completion_form.php');

$id = required_param('id', PARAM_INT);       // course id

// Perform some basic access control checks.
if ($id) {

    if($id == SITEID){
        // Don't allow editing of 'site course' using this form.
        print_error('cannoteditsiteform');
    }

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error('invalidcourseid');
    }
    require_login($course);
    $context = context_course::instance($course->id);
    if (!has_capability('moodle/course:update', $context)) {
        // User is not allowed to modify course completion.
        // Check if they can see default completion or edit bulk completion and redirect there.
        if ($tabs = core_completion\manager::get_available_completion_tabs($course)) {
            redirect($tabs[0]->link);
        } else {
            require_capability('moodle/course:update', $context);
        }
    }

} else {
    require_login();
    print_error('needcourseid');
}

// Set up the page.
$PAGE->set_course($course);
$PAGE->set_url('/course/completion.php', array('id' => $course->id));
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('admin');

// Create the settings form instance.
$form = new course_completion_form('completion.php?id='.$id, array('course' => $course));

if ($form->is_cancelled()){
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);

} else if ($data = $form->get_data()) {
    $completion = new completion_info($course);

    // Process criteria unlocking if requested.
    if (!empty($data->settingsunlock)) {
        $completion->delete_course_completion_data();

        // Return to form (now unlocked).
        redirect($PAGE->url);
    }

    // Delete old criteria.
    $completion->clear_criteria();

    // Loop through each criteria type and run its update_config() method.
    global $COMPLETION_CRITERIA_TYPES;
    foreach ($COMPLETION_CRITERIA_TYPES as $type) {
        $class = 'completion_criteria_'.$type;
        $criterion = new $class();
        $criterion->update_config($data);
    }

    // Handle overall aggregation.
    $aggdata = array(
        'course'        => $data->id,
        'criteriatype'  => null
    );
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->overall_aggregation);
    $aggregation->save();

    // Handle activity aggregation.
    if (empty($data->activity_aggregation)) {
        $data->activity_aggregation = 0;
    }

    $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_ACTIVITY;
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->activity_aggregation);
    $aggregation->save();

    // Handle course aggregation.
    if (empty($data->course_aggregation)) {
        $data->course_aggregation = 0;
    }

    $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_COURSE;
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->course_aggregation);
    $aggregation->save();

    // Handle role aggregation.
    if (empty($data->role_aggregation)) {
        $data->role_aggregation = 0;
    }

    $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_ROLE;
    $aggregation = new completion_aggregation($aggdata);
    $aggregation->setMethod($data->role_aggregation);
    $aggregation->save();

    // Trigger an event for course module completion changed.
    $event = \core\event\course_completion_updated::create(
            array(
                'courseid' => $course->id,
                'context' => context_course::instance($course->id)
                )
            );
    $event->trigger();

    // Redirect to the course main page.
    $url = new moodle_url('/course/view.php', array('id' => $course->id));
    redirect($url);
}

$renderer = $PAGE->get_renderer('core_course', 'bulk_activity_completion');

// Print the form.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('editcoursecompletionsettings', 'core_completion'));

echo $renderer->navigation($course, 'completion');

$form->display();

echo $OUTPUT->footer();
