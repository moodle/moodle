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
 * Main management page for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');
require_once('classes/form/rule_form.php');

admin_externalpage_setup('local_coursematrix');

$action = optional_param('action', '', PARAM_ALPHA);
// We now identify rules by dept/job, but for editing an existing rule we might still use ID.
// However, to support "creating" a rule for a group that exists but has no rule, we pass dept/job.
$department = optional_param('department', '', PARAM_TEXT);
$jobtitle = optional_param('jobtitle', '', PARAM_TEXT);

$PAGE->set_url('/local/coursematrix/index.php');
$PAGE->set_title(get_string('pluginname', 'local_coursematrix'));
$PAGE->set_heading(get_string('pluginname', 'local_coursematrix'));

$form = new \local_coursematrix\form\rule_form(null, ['department' => $department, 'jobtitle' => $jobtitle]);

if ($form->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $form->get_data()) {
    // Save logic needs to handle finding the existing rule by dept/job if ID is missing.
    // But the form should handle loading the ID if it exists.

    // If we are saving, we might need to find the ID if it wasn't in the form (e.g. new rule for existing group).
    if (empty($data->id)) {
        $existing = $DB->get_record('local_coursematrix', ['department' => $data->department, 'jobtitle' => $data->jobtitle]);
        if ($existing) {
            $data->id = $existing->id;
        }
    }

    local_coursematrix_save_rule($data);
    redirect($PAGE->url, get_string('matrixupdated', 'local_coursematrix'));
}

echo $OUTPUT->header();

// EDIT ACTION.
if ($action == 'edit') {
    echo $OUTPUT->heading(get_string('editrule', 'local_coursematrix'));

    // Try to find existing rule.
    $rule = $DB->get_record('local_coursematrix', ['department' => $department, 'jobtitle' => $jobtitle]);

    $formdata = new stdClass();
    $formdata->department = $department;
    $formdata->jobtitle = $jobtitle;

    if ($rule) {
        $formdata->id = $rule->id;
        $formdata->courses = explode(',', $rule->courses);
    }

    $form->set_data($formdata);
    $form->display();

    echo $OUTPUT->footer();
    exit;
}

// DISPLAY TABLE.
echo $OUTPUT->heading(get_string('coursematrix', 'local_coursematrix'));

// 1. Get all user groups (Department + Job Title).
// Note: 'institution' column in {user} is often used for Job Title in Moodle if not using custom fields.
// The user script mapped job_title -> institution.
$concat = $DB->sql_concat('department', "'#'", 'institution');
$sql = "SELECT $concat AS uniqueid, department, institution, COUNT(id) as usercount
        FROM {user}
        WHERE deleted = 0 AND suspended = 0 AND (department <> '' OR institution <> '')
        GROUP BY department, institution
        ORDER BY department, institution";
$groups = $DB->get_records_sql($sql);

// 2. Get existing rules.
$rules = $DB->get_records('local_coursematrix');
$rulesmap = [];
foreach ($rules as $r) {
    // Create a unique key. Be careful with nulls/empty strings.
    $key = (string)$r->department . '|' . (string)$r->jobtitle;
    $rulesmap[$key] = $r;
}

// 3. Build Table.
$table = new html_table();
$table->head = [
    get_string('department', 'local_coursematrix'),
    get_string('jobtitle', 'local_coursematrix'),
    'User Count', // TODO: Add to lang strings.
    get_string('courses', 'local_coursematrix'),
    get_string('actions', 'local_coursematrix'),
];

// We want to show ALL groups from users, AND any rules that might exist for non-existent groups (edge case, but good to show)
// For now, let's drive by the groups found in users + any rules not covered.

$processedkeys = [];

foreach ($groups as $g) {
    $dept = (string)$g->department;
    $job = (string)$g->institution;
    $key = $dept . '|' . $job;
    $processedkeys[$key] = true;

    $rule = $rulesmap[$key] ?? null;

    $coursenames = [];
    if ($rule && !empty($rule->courses)) {
        $cids = explode(',', $rule->courses);
        if (!empty($cids)) {
            // Efficiently fetching names? For now, simple query is okay for admin page.
            // Optimization: Fetch all courses once or use get_in_or_equal.
            [$insql, $inparams] = $DB->get_in_or_equal($cids);
            $courses = $DB->get_records_select('course', "id $insql", $inparams, '', 'id, fullname');
            foreach ($courses as $c) {
                $coursenames[] = $c->fullname;
            }
        }
    }

    $editurl = new moodle_url($PAGE->url, ['action' => 'edit', 'department' => $dept, 'jobtitle' => $job]);
    $actions = html_writer::link($editurl, get_string('editrule', 'local_coursematrix'), ['class' => 'btn btn-secondary btn-sm']);

    $table->data[] = [
        s($dept),
        s($job),
        $g->usercount,
        implode(', ', $coursenames),
        $actions,
    ];
}

// Show rules that don't match current users (orphaned rules).
foreach ($rules as $r) {
    $key = (string)$r->department . '|' . (string)$r->jobtitle;
    if (isset($processedkeys[$key])) {
        continue;
    }

    $coursenames = [];
    if (!empty($r->courses)) {
        $cids = explode(',', $r->courses);
        if (!empty($cids)) {
            [$insql, $inparams] = $DB->get_in_or_equal($cids);
            $courses = $DB->get_records_select('course', "id $insql", $inparams, '', 'id, fullname');
            foreach ($courses as $c) {
                $coursenames[] = $c->fullname;
            }
        }
    }

    $editurl = new moodle_url($PAGE->url, ['action' => 'edit', 'department' => $r->department, 'jobtitle' => $r->jobtitle]);
    $actions = html_writer::link($editurl, get_string('editrule', 'local_coursematrix'), ['class' => 'btn btn-secondary btn-sm']);

    $table->data[] = [
        s($r->department),
        s($r->jobtitle),
        '0 (No matching users)',
        implode(', ', $coursenames),
        $actions,
    ];
}

echo html_writer::table($table);
echo $OUTPUT->footer();
