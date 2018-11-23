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
 * Display the request reject + resubmit confirmation page.
 *
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 */

require_once('../../../config.php');

$requestid = required_param('requestid', PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_INT);

$PAGE->set_url(new moodle_url('/admin/tool/dataprivacy/resubmitrequest.php', ['requestid' => $requestid]));

require_login();

$PAGE->set_context(\context_system::instance());
require_capability('tool/dataprivacy:managedatarequests', $PAGE->context);

$manageurl = new moodle_url('/admin/tool/dataprivacy/datarequests.php');

$originalrequest = \tool_dataprivacy\api::get_request($requestid);
$user = \core_user::get_user($originalrequest->get('userid'));
$stringparams = (object) [
        'username' => fullname($user),
        'type' => \tool_dataprivacy\local\helper::get_shortened_request_type_string($originalrequest->get('type')),
    ];

if (null !== $confirm && confirm_sesskey()) {
    $originalrequest->resubmit_request();
    redirect($manageurl, get_string('resubmittedrequest', 'tool_dataprivacy', $stringparams));
}

$heading = get_string('resubmitrequest', 'tool_dataprivacy', $stringparams);
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

echo $OUTPUT->header();

$confirmstring = get_string('confirmrequestresubmit', 'tool_dataprivacy', $stringparams);
$confirmurl = new \moodle_url($PAGE->url, ['confirm' => 1]);
echo $OUTPUT->confirm($confirmstring, $confirmurl, $manageurl);
echo $OUTPUT->footer();
