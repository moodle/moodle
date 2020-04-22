<?php
// This file is part of Moodle - https://moodle.org/
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
 * Web services / external tokens management UI.
 *
 * @package     core_webservice
 * @category    admin
 * @copyright   2009 Jerome Mouneyrac
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$tokenid = optional_param('tokenid', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

admin_externalpage_setup('webservicetokens');

if ($action === 'create') {
    $webservicemanager = new webservice();
    $mform = new \core_webservice\token_form(null, ['action' => 'create']);
    $data = $mform->get_data();

    if ($mform->is_cancelled()) {
        redirect($PAGE->url);

    } else if ($data) {
        ignore_user_abort(true);

        // Check the user is allowed for the service.
        $selectedservice = $webservicemanager->get_external_service_by_id($data->service);

        if ($selectedservice->restrictedusers) {
            $restricteduser = $webservicemanager->get_ws_authorised_user($data->service, $data->user);

            if (empty($restricteduser)) {
                $allowuserurl = new moodle_url('/admin/webservice/service_users.php', ['id' => $selectedservice->id]);
                $allowuserlink = html_writer::link($selectedservice->name, $allowuserurl);
                $errormsg = $OUTPUT->notification(get_string('usernotallowed', 'webservice', $allowuserlink));
            }
        }

        $user = \core_user::get_user($data->user, '*', MUST_EXIST);
        \core_user::require_active_user($user);

        // Generate the token.
        if (empty($errormsg)) {
            external_generate_token(EXTERNAL_TOKEN_PERMANENT, $data->service, $data->user, context_system::instance(),
                $data->validuntil, $data->iprestriction);
            redirect($PAGE->url);
        }
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('createtoken', 'webservice'));
    if (!empty($errormsg)) {
        echo $errormsg;
    }
    $mform->display();
    echo $OUTPUT->footer();
    die();
}

if ($action === 'delete') {
    $webservicemanager = new webservice();
    $token = $webservicemanager->get_token_by_id_with_details($tokenid);

    if ($token->creatorid != $USER->id) {
        require_capability('moodle/webservice:managealltokens', context_system::instance());
    }

    if ($confirm && confirm_sesskey()) {
        $webservicemanager->delete_user_ws_token($token->id);
        redirect($PAGE->url);
    }

    echo $OUTPUT->header();

    echo $OUTPUT->confirm(
        get_string('deletetokenconfirm', 'webservice', [
            'user' => $token->firstname . ' ' . $token->lastname,
            'service' => $token->name,
        ]),
        new single_button(new moodle_url('/admin/webservice/tokens.php', [
            'tokenid' => $token->id,
            'action' => 'delete',
            'confirm' => 1,
            'sesskey' => sesskey(),
        ]), get_string('delete')),
        $PAGE->url
    );

    echo $OUTPUT->footer();
    die();
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managetokens', 'core_webservice'));

$table = new \core_webservice\token_table('webservicetokens');
$table->define_baseurl($PAGE->url);
$table->attributes['class'] = 'admintable generaltable';
$table->data = [];
$table->out(30, false);

echo $OUTPUT->footer();

// TODO Add button
//$tokenpageurl = "$CFG->wwwroot/$CFG->admin/webservice/tokens.php?sesskey=" . sesskey();
//
//$return .= $OUTPUT->box_end();
//// add a token to the table
//$return .= "<a href=\"".$tokenpageurl."&amp;action=create\">";
//$return .= get_string('add')."</a>";
