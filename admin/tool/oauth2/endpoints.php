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
 * OAuth 2 Endpoing Configuration page.
 *
 * @package    tool_oauth2
 * @copyright  2017 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$PAGE->set_url('/admin/tool/oauth2/endpoints.php', ['issuerid' => required_param('issuerid', PARAM_INT)]);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'tool_oauth2');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

require_admin();

$renderer = $PAGE->get_renderer('tool_oauth2');

$action = optional_param('action', '', PARAM_ALPHAEXT);
$issuerid = required_param('issuerid', PARAM_INT);
$endpointid = optional_param('endpointid', '', PARAM_INT);
$endpoint = null;
$mform = null;

$issuer = \core\oauth2\api::get_issuer($issuerid);
if (!$issuer) {
    print_error('invaliddata');
}
$PAGE->navbar->override_active_url(new moodle_url('/admin/tool/oauth2/issuers.php'), true);

if (!empty($endpointid)) {
    $endpoint = \core\oauth2\api::get_endpoint($endpointid);
}

if ($action == 'edit') {
    if ($endpoint) {
        $strparams = [ 'issuer' => s($issuer->get('name')), 'endpoint' => s($endpoint->get('name')) ];
        $PAGE->navbar->add(get_string('editendpoint', 'tool_oauth2', $strparams));
    } else {
        $PAGE->navbar->add(get_string('createnewendpoint', 'tool_oauth2', s($issuer->get('name'))));
    }

    $mform = new \tool_oauth2\form\endpoint(null, ['persistent' => $endpoint, 'issuerid' => $issuerid]);
}

if ($mform && $mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/oauth2/endpoints.php', ['issuerid' => $issuerid]));
} else if ($action == 'edit') {

    if ($data = $mform->get_data()) {

        try {
            if (!empty($data->id)) {
                core\oauth2\api::update_endpoint($data);
            } else {
                core\oauth2\api::create_endpoint($data);
            }
            redirect($PAGE->url, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
        } catch (Exception $e) {
            redirect($PAGE->url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        echo $OUTPUT->header();
        if ($endpoint) {
            $strparams = [ 'issuer' => s($issuer->get('name')), 'endpoint' => s($endpoint->get('name')) ];
            echo $OUTPUT->heading(get_string('editendpoint', 'tool_oauth2', $strparams));
        } else {
            echo $OUTPUT->heading(get_string('createnewendpoint', 'tool_oauth2', s($issuer->get('name'))));
        }
        $mform->display();
        echo $OUTPUT->footer();
    }

} else if ($action == 'delete') {

    if (!optional_param('confirm', false, PARAM_BOOL)) {
        $continueparams = [
            'action' => 'delete',
            'issuerid' => $issuerid,
            'endpointid' => $endpointid,
            'sesskey' => sesskey(),
            'confirm' => true
        ];
        $continueurl = new moodle_url('/admin/tool/oauth2/endpoints.php', $continueparams);
        $cancelurl = new moodle_url('/admin/tool/oauth2/endpoints.php');
        echo $OUTPUT->header();
        $strparams = [ 'issuer' => s($issuer->get('name')), 'endpoint' => s($endpoint->get('name')) ];
        echo $OUTPUT->confirm(get_string('deleteendpointconfirm', 'tool_oauth2', $strparams), $continueurl, $cancelurl);
        echo $OUTPUT->footer();
    } else {
        require_sesskey();
        core\oauth2\api::delete_endpoint($endpointid);
        redirect($PAGE->url, get_string('endpointdeleted', 'tool_oauth2'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('endpointsforissuer', 'tool_oauth2', s($issuer->get('name'))));
    $endpoints = core\oauth2\api::get_endpoints($issuer);
    echo $renderer->endpoints_table($endpoints, $issuerid);

    $addurl = new moodle_url('/admin/tool/oauth2/endpoints.php', ['action' => 'edit', 'issuerid' => $issuerid]);
    echo $renderer->single_button($addurl, get_string('createnewendpoint', 'tool_oauth2', s($issuer->get('name'))));
    echo $OUTPUT->footer();
}
