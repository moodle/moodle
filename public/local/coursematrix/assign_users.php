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
require_once('lib.php');

admin_externalpage_setup('local_coursematrix_assign');

$PAGE->set_url('/local/coursematrix/assign_users.php');
$PAGE->set_title(get_string('assignusers', 'local_coursematrix'));
$PAGE->set_heading(get_string('assignusers', 'local_coursematrix'));

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $planid = required_param('planid', PARAM_INT);
    $userids = required_param_array('users', PARAM_INT);

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

// Get all users for selection - include all fields needed by fullname().
$userfields = \core_user\fields::for_name()->with_userpic()->get_sql('u', false, '', '', false)->selects;
$sql = "SELECT u.id, u.email, u.department, u.institution, $userfields
        FROM {user} u
        WHERE u.deleted = 0 AND u.suspended = 0 AND u.id > 1
        ORDER BY u.lastname, u.firstname";
$users = $DB->get_records_sql($sql);

$userlist = [];
foreach ($users as $user) {
    $userlist[$user->id] = fullname($user) . ' (' . $user->email . ')';
}

// Build form.
echo '<form method="post">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

echo '<div class="form-group">';
echo '<label for="planid">' . get_string('selectplan', 'local_coursematrix') . '</label>';
echo '<select name="planid" id="planid" class="form-control" required>';
echo '<option value="">' . get_string('selectplan', 'local_coursematrix') . '</option>';
foreach ($plans as $plan) {
    echo '<option value="' . $plan->id . '">' . s($plan->name) . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div class="form-group">';
echo '<label>' . get_string('selectusers', 'local_coursematrix') . '</label>';

// Use Moodle's autocomplete for user selection.
$attributes = [
    'multiple' => 'multiple',
    'class' => 'form-control',
    'id' => 'users',
];
echo html_writer::select($userlist, 'users[]', [], null, $attributes);
echo '<small class="text-muted">Hold Ctrl/Cmd to select multiple users.</small>';
echo '</div>';

echo '<button type="submit" class="btn btn-primary">' . get_string('assigntoplan', 'local_coursematrix') . '</button>';
echo '</form>';

echo $OUTPUT->footer();

