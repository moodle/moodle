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
 * Assign users to learning plans page for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');

admin_externalpage_setup('local_coursematrix_assign');

$PAGE->set_url('/local/coursematrix/assign_users.php');
$PAGE->set_title(get_string('assignusers', 'local_coursematrix'));
$PAGE->set_heading(get_string('assignusers', 'local_coursematrix'));

$action = optional_param('action', '', PARAM_ALPHA);
$userplanid = optional_param('upid', 0, PARAM_INT);

// Handle unenroll action.
if ($action == 'unenroll' && $userplanid && confirm_sesskey()) {
    $DB->delete_records('local_coursematrix_user_plans', ['id' => $userplanid]);
    redirect($PAGE->url, get_string('userunenrolled', 'local_coursematrix'));
}

// Handle form submission for enrollment.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey() && !$action) {
    $planid = required_param('planid', PARAM_INT);
    $userids = optional_param_array('users', [], PARAM_INT);

    $assigned = 0;
    foreach ($userids as $userid) {
        if (local_coursematrix_assign_user_to_plan($userid, $planid)) {
            $assigned++;
        }
    }

    redirect($PAGE->url, get_string('usersassigned', 'local_coursematrix') . " ($assigned users)");
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assignusers', 'local_coursematrix'));

// Get all plans.
$plans = local_coursematrix_get_plans();

if (empty($plans)) {
    echo $OUTPUT->notification(get_string('noplans', 'local_coursematrix'), 'warning');
    echo $OUTPUT->footer();
    exit;
}

// Get all users for autocomplete.
$userfields = \core_user\fields::for_name()->with_userpic()->get_sql('u', false, '', '', false)->selects;
$sql = "SELECT u.id, u.email, u.department, u.institution, $userfields
        FROM {user} u
        WHERE u.deleted = 0 AND u.suspended = 0 AND u.id > 1
        ORDER BY u.lastname, u.firstname";
$users = $DB->get_records_sql($sql);

$useroptions = [];
foreach ($users as $user) {
    $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
}

// Build plan options.
$planoptions = [];
foreach ($plans as $plan) {
    $planoptions[$plan->id] = $plan->name;
}

// ENROLLMENT FORM.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('assigntoplan', 'local_coursematrix') . '</h5></div>';
echo '<div class="card-body">';

echo '<form method="post">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

echo '<div class="form-group">';
echo '<label for="planid">' . get_string('selectplan', 'local_coursematrix') . '</label>';
echo html_writer::select($planoptions, 'planid', '', ['' => get_string('selectplan', 'local_coursematrix')], ['class' => 'form-control', 'required' => 'required']);
echo '</div>';

echo '<div class="form-group">';
echo '<label>' . get_string('selectusers', 'local_coursematrix') . '</label>';
echo html_writer::select($useroptions, 'users[]', [], null, [
    'class' => 'form-control',
    'id' => 'users',
    'multiple' => 'multiple',
    'data-enhance' => 'autocomplete',
]);

// Add Moodle's autocomplete enhancement
$PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance', [
    '#users',
    false, // Tags
    '', // Ajax URL (none - we have all options)
    get_string('selectusers', 'local_coursematrix'),
    false, // Case sensitive
    true, // Show suggestions on focus
    '', // No results string
    true // Close on select
]);

echo '</div>';

echo '<button type="submit" class="btn btn-primary">' . get_string('assigntoplan', 'local_coursematrix') . '</button>';
echo '</form>';
echo '</div></div>';

// CURRENT ENROLLMENTS - show users with unenroll option.
echo '<div class="card">';
echo '<div class="card-header"><h5>' . get_string('currentenrollments', 'local_coursematrix') . '</h5></div>';
echo '<div class="card-body">';

// Get all user plan enrollments.
$enrollments = $DB->get_records_sql("
    SELECT up.id, up.userid, up.planid, up.status, up.startdate,
           u.firstname, u.lastname, u.email,
           p.name as planname
    FROM {local_coursematrix_user_plans} up
    JOIN {user} u ON u.id = up.userid
    JOIN {local_coursematrix_plans} p ON p.id = up.planid
    ORDER BY p.name, u.lastname, u.firstname
");

if (!empty($enrollments)) {
    $table = new html_table();
    $table->head = [
        get_string('user'),
        get_string('learningplan', 'local_coursematrix'),
        get_string('status', 'local_coursematrix'),
        get_string('startdate', 'local_coursematrix'),
        get_string('actions', 'local_coursematrix'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($enrollments as $enrollment) {
        $statusclass = '';
        $statustext = get_string('status_' . $enrollment->status, 'local_coursematrix');
        if ($enrollment->status == 'overdue') {
            $statusclass = 'badge badge-danger';
        } else if ($enrollment->status == 'completed') {
            $statusclass = 'badge badge-success';
        } else {
            $statusclass = 'badge badge-info';
        }

        $unenrollurl = new moodle_url($PAGE->url, ['action' => 'unenroll', 'upid' => $enrollment->id, 'sesskey' => sesskey()]);
        $actions = html_writer::link($unenrollurl, get_string('unenroll', 'local_coursematrix'), [
            'class' => 'btn btn-sm btn-danger',
            'onclick' => 'return confirm("' . get_string('confirmunenroll', 'local_coursematrix') . '");',
        ]);

        $table->data[] = [
            $enrollment->firstname . ' ' . $enrollment->lastname . '<br><small class="text-muted">' . $enrollment->email . '</small>',
            s($enrollment->planname),
            '<span class="' . $statusclass . '">' . $statustext . '</span>',
            $enrollment->startdate ? userdate($enrollment->startdate, get_string('strftimedateshort')) : '-',
            $actions,
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noenrollments', 'local_coursematrix'), 'info');
}

echo '</div></div>';

echo $OUTPUT->footer();


