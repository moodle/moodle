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
 * OAuth 2 Endpoint Configuration page.
 *
 * @package    tool_oauth2
 * @copyright  2017 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$PAGE->set_url('/admin/tool/oauth2/userfieldmappings.php', ['issuerid' => required_param('issuerid', PARAM_INT)]);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'tool_oauth2');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

require_admin();

$renderer = $PAGE->get_renderer('tool_oauth2');

$action = optional_param('action', '', PARAM_ALPHAEXT);
$issuerid = required_param('issuerid', PARAM_INT);
$userfieldmappingid = optional_param('userfieldmappingid', '', PARAM_INT);
$userfieldmapping = null;
$mform = null;

$issuer = \core\oauth2\api::get_issuer($issuerid);
if (!$issuer) {
    throw new \moodle_exception('invaliddata');
}
$PAGE->navbar->override_active_url(new moodle_url('/admin/tool/oauth2/issuers.php'), true);

if (!empty($userfieldmappingid)) {
    $userfieldmapping = \core\oauth2\api::get_user_field_mapping($userfieldmappingid);
}

if ($action == 'edit') {
    if ($userfieldmapping) {
        $PAGE->navbar->add(get_string('edituserfieldmapping', 'tool_oauth2', s($issuer->get('name'))));
    } else {
        $PAGE->navbar->add(get_string('createnewuserfieldmapping', 'tool_oauth2', s($issuer->get('name'))));
    }

    $mform = new \tool_oauth2\form\user_field_mapping(null, ['persistent' => $userfieldmapping, 'issuerid' => $issuerid]);
}

if ($mform && $mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/oauth2/userfieldmappings.php', ['issuerid' => $issuerid]));
} else if ($action == 'edit') {

    if ($data = $mform->get_data()) {

        try {
            if (!empty($data->id)) {
                core\oauth2\api::update_user_field_mapping($data);
            } else {
                core\oauth2\api::create_user_field_mapping($data);
            }
            redirect($PAGE->url, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
        } catch (Exception $e) {
            redirect($PAGE->url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        echo $OUTPUT->header();
        if ($issuer) {
            echo $OUTPUT->heading(get_string('edituserfieldmapping', 'tool_oauth2', s($issuer->get('name'))));
        } else {
            echo $OUTPUT->heading(get_string('createnewuserfieldmapping', 'tool_oauth2', s($issuer->get('name'))));
        }
        $mform->display();
        echo $OUTPUT->footer();
    }

} else if ($action == 'delete') {

    if (!optional_param('confirm', false, PARAM_BOOL)) {
        $continueparams = [
            'action' => 'delete',
            'issuerid' => $issuerid,
            'userfieldmappingid' => $userfieldmappingid,
            'sesskey' => sesskey(),
            'confirm' => true
        ];
        $continueurl = new moodle_url('/admin/tool/oauth2/userfieldmappings.php', $continueparams);
        $cancelurl = new moodle_url('/admin/tool/oauth2/userfieldmappings.php');
        echo $OUTPUT->header();
        $str = get_string('deleteuserfieldmappingconfirm', 'tool_oauth2', s($issuer->get('name')));
        echo $OUTPUT->confirm($str, $continueurl, $cancelurl);
        echo $OUTPUT->footer();
    } else {
        require_sesskey();
        core\oauth2\api::delete_user_field_mapping($userfieldmappingid);
        redirect($PAGE->url, get_string('userfieldmappingdeleted', 'tool_oauth2'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userfieldmappingsforissuer', 'tool_oauth2', s($issuer->get('name'))));
    $userfieldmappings = core\oauth2\api::get_user_field_mappings($issuer);
    echo $renderer->user_field_mappings_table($userfieldmappings, $issuerid);

    $addurl = new moodle_url('/admin/tool/oauth2/userfieldmappings.php', ['action' => 'edit', 'issuerid' => $issuerid]);
    echo $renderer->single_button($addurl, get_string('createnewuserfieldmapping', 'tool_oauth2', s($issuer->get('name'))));
    echo $OUTPUT->footer();
}
