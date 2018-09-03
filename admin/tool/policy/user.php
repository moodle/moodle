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
 * View user acceptances to the policies
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->dirroot.'/user/editlib.php');

$userid = optional_param('userid', null, PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);

require_login();
$userid = $userid ?: $USER->id;
if (isguestuser() || isguestuser($userid)) {
    print_error('noguest');
}
$context = context_user::instance($userid);
if ($userid != $USER->id) {
    // Check capability to view acceptances. No capability is needed to view your own acceptances.
    if (!has_capability('tool/policy:acceptbehalf', $context)) {
        require_capability('tool/policy:viewacceptances', $context);
    }
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/admin/tool/policy/user.php', ['userid' => $userid]));

$output = $PAGE->get_renderer('tool_policy');
echo $output->header();
echo $output->heading(get_string('policiesagreements', 'tool_policy'));
$acceptances = new \tool_policy\output\acceptances($userid, $returnurl);
echo $output->render($acceptances);
$PAGE->requires->js_call_amd('tool_policy/acceptmodal', 'getInstance', [context_system::instance()->id]);
echo $output->footer();
