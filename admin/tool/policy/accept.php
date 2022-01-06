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
 * Accept or revoke policies on behalf of users (non-JS version)
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->dirroot.'/user/editlib.php');

$userids = optional_param_array('userids', null, PARAM_INT);
$versionids = optional_param_array('versionids', null, PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);
$action = optional_param('action', null, PARAM_ALPHA);

require_login();
if (isguestuser()) {
    print_error('noguest');
}
$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/policy/accept.php'));

if (!in_array($action, ['accept', 'decline', 'revoke'])) {
    throw new moodle_exception('invalidaccessparameter');
}

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else if (count($userids) == 1) {
    $userid = reset($userids);
    $returnurl = new moodle_url('/admin/tool/policy/user.php', ['userid' => $userid]);
} else {
    $returnurl = new moodle_url('/admin/tool/policy/acceptances.php');
}
// Initialise the form, this will also validate users, versions and check permission to accept policies.
$form = new \tool_policy\form\accept_policy(null,
    ['versionids' => $versionids, 'userids' => $userids, 'showbuttons' => true, 'action' => $action]);
$form->set_data(['returnurl' => $returnurl]);

if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($form->get_data()) {
    $form->process();
    redirect($returnurl);
}

$output = $PAGE->get_renderer('tool_policy');
echo $output->header();
echo $output->heading(get_string('statusformtitle'.$action, 'tool_policy'));
$form->display();
echo $output->footer();
