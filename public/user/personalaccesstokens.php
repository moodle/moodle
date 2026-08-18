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
 * Lets a user manage their own personal access tokens for the REST API.
 *
 * @package    core_user
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

use core\api\form\create_token;
use core\api\output\created_token;
use core\api\repository\api_token_repository;
use core\api\token_manager;
use core\exception\moodle_exception;
use core\output\html_writer;
use core\reportbuilder\local\systemreports\api_tokens;
use core\url;
use core_reportbuilder\system_report_factory;

require_login();

// A guest has no identity to act on behalf of, so a token would be meaningless.
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

$action = optional_param('action', '', PARAM_ALPHA);

$context = context_user::instance($USER->id);
require_capability('moodle/api:createtoken', context_system::instance());

$pageurl = new url('/user/personalaccesstokens.php');

$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('personalaccesstokens'));
$PAGE->set_heading(fullname($USER));

/** @var token_manager $manager */
$manager = \core\di::get(token_manager::class);
/** @var api_token_repository $repository */
$repository = \core\di::get(api_token_repository::class);

if ($action === 'delete') {
    // Removing a token only ever happens on a posted request carrying a sesskey, so no link,
    // prefetch or crawl can reach it. The confirmation is the modal on the list.
    if (!data_submitted()) {
        redirect($pageurl);
    }

    require_sesskey();

    $id = required_param('id', PARAM_INT);
    $token = $repository->get_by_id($id);

    // A token id is guessable, so ownership decides this, not the id alone.
    if ($token->get_userid() !== (int) $USER->id) {
        throw new moodle_exception('nopermissions', 'error', $pageurl, get_string('pat_delete'));
    }

    // Revoking and deleting remove the row alike, but only one of them cuts off working access,
    // so the wording follows the token's state.
    $expired = $token->has_expired();
    $repository->delete_token($id);

    redirect(
        $pageurl,
        get_string($expired ? 'pat_deleted' : 'pat_revoked'),
        null,
        \core\output\notification::NOTIFY_SUCCESS,
    );
}

if ($action === 'create') {
    $createurl = new url($pageurl, ['action' => 'create']);
    $form = new create_token($createurl->out(false), ['manager' => $manager]);

    if ($form->is_cancelled()) {
        redirect($pageurl);
    }

    if ($data = $form->get_data()) {
        $scopes = $form->get_submitted_scopes((array) $data);
        $expirytime = $form->get_expiry_time($data);

        $token = $manager->create_token(
            $data->name,
            (int) $USER->id,
            $scopes,
            $data->description ?: null,
            $expirytime,
        );

        $SESSION->core_api_created_token = [
            'token' => $token,
            'name' => $data->name,
        ];

        redirect($pageurl);
    }

    $PAGE->set_title(get_string('pat_createheading'));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pat_createheading'));
    echo html_writer::tag('p', get_string('pat_createintro'));
    $form->display();
    echo $OUTPUT->footer();
    exit;
}

// The secret is held for exactly one render: the redirect that brought us here consumed the
// POST, and unsetting it as it is read means a refresh or a return visit cannot show it again.
$revealed = $SESSION->core_api_created_token ?? null;
unset($SESSION->core_api_created_token);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('personalaccesstokens'));

echo html_writer::tag('p', get_string('personalaccesstokensintro'));

// Between the description and the list: the value cannot be recovered once this render is
// over, and the row it belongs to is immediately below it.
if ($revealed !== null) {
    echo $OUTPUT->render(new created_token($revealed['token'], $revealed['name']));
}

echo system_report_factory::create(api_tokens::class, $context)->output();
echo $OUTPUT->footer();
