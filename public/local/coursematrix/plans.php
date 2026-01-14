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
 * Learning Plans management page for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');
require_once('classes/form/plan_form.php');

admin_externalpage_setup('local_coursematrix_plans');

$action = optional_param('action', '', PARAM_ALPHA);
$planid = optional_param('id', 0, PARAM_INT);

$PAGE->set_url('/local/coursematrix/plans.php');
$PAGE->set_title(get_string('learningplans', 'local_coursematrix'));
$PAGE->set_heading(get_string('learningplans', 'local_coursematrix'));

// Handle delete action.
if ($action == 'delete' && $planid && confirm_sesskey()) {
    local_coursematrix_delete_plan($planid);
    redirect($PAGE->url, get_string('plandeleted', 'local_coursematrix'));
}

// Handle form submission.
$form = new \local_coursematrix\form\plan_form(null, ['id' => $planid]);

if ($form->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $form->get_data()) {
    // Process form data.
    $plandata = new stdClass();
    $plandata->id = $data->id ?? 0;
    $plandata->name = $data->name;
    $plandata->description = $data->description['text'] ?? '';

    // Process courses.
    $plandata->courses = [];
    if (!empty($data->courses)) {
        foreach ($data->courses as $i => $courseid) {
            $plandata->courses[] = [
                'courseid' => $courseid,
                'duedays' => $data->duedays[$i] ?? 14,
            ];
        }
    }

    // Process reminders.
    $plandata->reminders = [];
    if (!empty($data->reminders)) {
        foreach ($data->reminders as $daysbefore) {
            if (is_numeric($daysbefore) && $daysbefore > 0) {
                $plandata->reminders[] = [
                    'daysbefore' => (int)$daysbefore,
                    'enabled' => 1,
                ];
            }
        }
    }

    local_coursematrix_save_plan($plandata);
    redirect($PAGE->url, get_string('plansaved', 'local_coursematrix'));
}

echo $OUTPUT->header();

// EDIT/CREATE FORM.
if ($action == 'edit' || $action == 'create') {
    if ($action == 'edit' && $planid) {
        echo $OUTPUT->heading(get_string('editplan', 'local_coursematrix'));
        $plan = local_coursematrix_get_plan($planid);
        if ($plan) {
            $formdata = new stdClass();
            $formdata->id = $plan->id;
            $formdata->name = $plan->name;
            $formdata->description = ['text' => $plan->description, 'format' => FORMAT_HTML];

            // Load courses.
            $formdata->courses = [];
            $formdata->duedays = [];
            foreach ($plan->courses as $pc) {
                $formdata->courses[] = $pc->courseid;
                $formdata->duedays[] = $pc->duedays;
            }

            // Load reminders.
            $formdata->reminders = [];
            foreach ($plan->reminders as $r) {
                $formdata->reminders[] = $r->daysbefore;
            }

            $form->set_data($formdata);
        }
    } else {
        echo $OUTPUT->heading(get_string('createplan', 'local_coursematrix'));
    }

    $form->display();
    echo $OUTPUT->footer();
    exit;
}

// LIST VIEW.
echo $OUTPUT->heading(get_string('learningplans', 'local_coursematrix'));

// Create button.
$createurl = new moodle_url($PAGE->url, ['action' => 'create']);
echo html_writer::link($createurl, get_string('createplan', 'local_coursematrix'), [
    'class' => 'btn btn-primary mb-3',
]);

$plans = local_coursematrix_get_plans();

if (!empty($plans)) {
    $table = new html_table();
    $table->head = [
        get_string('planname', 'local_coursematrix'),
        get_string('plancourses', 'local_coursematrix'),
        get_string('actions', 'local_coursematrix'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($plans as $plan) {
        // Get course count.
        $coursecount = $DB->count_records('local_coursematrix_plan_courses', ['planid' => $plan->id]);

        $editurl = new moodle_url($PAGE->url, ['action' => 'edit', 'id' => $plan->id]);
        $deleteurl = new moodle_url($PAGE->url, ['action' => 'delete', 'id' => $plan->id, 'sesskey' => sesskey()]);

        $actions = html_writer::link($editurl, get_string('edit'), ['class' => 'btn btn-sm btn-secondary mr-1']);
        $actions .= html_writer::link($deleteurl, get_string('delete'), [
            'class' => 'btn btn-sm btn-danger',
            'onclick' => 'return confirm("' . get_string('areyousure') . '");',
        ]);

        $table->data[] = [
            s($plan->name),
            $coursecount . ' ' . get_string('courses', 'local_coursematrix'),
            $actions,
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noplans', 'local_coursematrix'), 'info');
}

echo $OUTPUT->footer();
