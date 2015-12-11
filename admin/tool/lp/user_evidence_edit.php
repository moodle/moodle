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
 * User evidence (evidence of prior learning).
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

$userid = optional_param('userid', $USER->id, PARAM_INT);
$id = optional_param('id', null, PARAM_INT);
$returntype = optional_param('return', 'list', PARAM_ALPHA);

$url = new moodle_url('/admin/tool/lp/user_evidence_edit.php', array('id' => $id, 'userid' => $userid, 'return' => $returntype));

$userevidence = null;
if (empty($id)) {
    $pagetitle = get_string('addnewuserevidence', 'tool_lp');
    list($title, $subtitle, $returnurl) = \tool_lp\page_helper::setup_for_user_evidence($userid, $url, null,
        $pagetitle, $returntype);

} else {
    $userevidence = \tool_lp\api::read_user_evidence($id);

    // The userid parameter must be the same as the owner of the evidence.
    if ($userid != $userevidence->get_userid()) {
        throw new coding_exception('Inconsistency between the userid parameter and the userid of the plan.');
    }

    $pagetitle = get_string('edituserevidence', 'tool_lp');
    list($title, $subtitle, $returnurl) = \tool_lp\page_helper::setup_for_user_evidence($userid, $url, $userevidence,
        $pagetitle, $returntype);
}

// The context has been set to the user context in the page_helper.
$context = $PAGE->context;

$fileareaoptions = array('subdirs' => false);
$customdata = array(
    'fileareaoptions' => $fileareaoptions,
    'persistent' => $userevidence,
    'userid' => $userid,
);

// Check if user has permissions to manage user evidence.
if ($userevidence != null) {
    if (!$userevidence->can_manage()) {
        throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');
    }
    $customdata['evidence'] = $userevidence;

} else if (!\tool_lp\user_evidence::can_manage_user($userid)) {
    throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');
}

$form = new \tool_lp\form\user_evidence($url->out(false), $customdata);
if ($form->is_cancelled()) {
    redirect($returnurl);
}

// Load existing user evidence.
$itemid = null;
if ($userevidence) {
    $itemid = $userevidence->get_id();
}

// Massaging the file API.
$draftitemid = file_get_submitted_draft_itemid('files');
file_prepare_draft_area($draftitemid, $context->id, 'tool_lp', 'userevidence', $itemid, $fileareaoptions);
$form->set_data((object) array('files' => $draftitemid));

// We're getting there...
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($title);
if (!empty($subtitle)) {
    echo $output->heading($subtitle, 3);
}

// Hurray, the user has submitted the form! Everyone loves forms :)!
if ($data = $form->get_data()) {
    require_sesskey();
    $draftitemid = $data->files;
    unset($data->files);

    if (empty($userevidence)) {
        $userevidence = \tool_lp\api::create_user_evidence($data, $draftitemid);
        echo $output->notification(get_string('userevidencecreated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button($returnurl);
    } else {
        \tool_lp\api::update_user_evidence($data, $draftitemid);
        echo $output->notification(get_string('userevidenceupdated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button($returnurl);
    }

} else {
    $form->display();
}

echo $output->footer();
