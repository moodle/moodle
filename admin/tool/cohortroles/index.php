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
 * Assign roles for a user.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$removeid = optional_param('removecohortroleassignment', 0, PARAM_INT);

admin_externalpage_setup('toolcohortroles');
$context = context_system::instance();

$pageurl = new moodle_url('/admin/tool/cohortroles/index.php');

$output = $PAGE->get_renderer('tool_cohortroles');

echo $output->header();
$title = get_string('assignroletocohort', 'tool_cohortroles');
echo $output->heading($title);

$form = new tool_cohortroles\form\assign_role_cohort();

if ($removeid) {
    require_sesskey();

    $result = \tool_cohortroles\api::delete_cohort_role_assignment($removeid);
    if ($result) {
        $notification = get_string('cohortroleassignmentremoved', 'tool_cohortroles');
        echo $output->notify_success($notification);
    } else {
        $notification = get_string('cohortroleassignmentnotremoved', 'tool_cohortroles');
        echo $output->notify_problem($notification);
    }
    echo $output->continue_button(new moodle_url($pageurl));
} else if ($data = $form->get_data()) {
    require_sesskey();
    // We must create them all or none.
    $saved = 0;
    // Loop through userids and cohortids only if both of them are not empty.
    if (!empty($data->userids) && !empty($data->cohortids)) {
        foreach ($data->userids as $userid) {
            foreach ($data->cohortids as $cohortid) {
                $params = (object) array('userid' => $userid, 'cohortid' => $cohortid, 'roleid' => $data->roleid);
                $result = \tool_cohortroles\api::create_cohort_role_assignment($params);
                if ($result) {
                    $saved++;
                }
            }
        }
    }
    if ($saved == 0) {
        $notification = get_string('nocohortroleassignmentssaved', 'tool_cohortroles');
        echo $output->notify_problem($notification);
    } else if ($saved == 1) {
        $notification = get_string('onecohortroleassignmentsaved', 'tool_cohortroles');
        echo $output->notify_success($notification);
    } else {
        $notification = get_string('acohortroleassignmentssaved', 'tool_cohortroles', $saved);
        echo $output->notify_success($notification);
    }

    echo $output->continue_button(new moodle_url($pageurl));
} else {
    $form->display();

    $title = get_string('existingcohortroles', 'tool_cohortroles');
    echo $output->heading($title);
    $url = new moodle_url('/admin/tool/cohortroles/index.php');
    $table = new tool_cohortroles\output\cohort_role_assignments_table(uniqid(), $url);
    echo $table->out(50, true);

    echo $output->spacer();
    echo $output->notify_message(get_string('backgroundsync', 'tool_cohortroles'));
}

echo $output->footer();

