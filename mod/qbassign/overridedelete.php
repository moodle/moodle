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
 * This page handles deleting assigment overrides
 *
 * @package    mod_qbassign
 * @copyright  2016 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/mod/qbassign/lib.php');
require_once($CFG->dirroot.'/mod/qbassign/locallib.php');
require_once($CFG->dirroot.'/mod/qbassign/override_form.php');

$overrideid = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

if (! $override = $DB->get_record('qbassign_overrides', array('id' => $overrideid))) {
    throw new \moodle_exception('invalidoverrideid', 'qbassign');
}

list($course, $cm) = get_course_and_cm_from_instance($override->qbassignid, 'qbassign');
$context = context_module::instance($cm->id);
$qbassign = new qbassign($context, null, null);

require_login($course, false, $cm);

// Check the user has the required capabilities to modify an override.
require_capability('mod/qbassign:manageoverrides', $context);

if ($override->groupid) {
    if (!groups_group_visible($override->groupid, $course, $cm)) {
        throw new \moodle_exception('invalidoverrideid', 'qbassign');
    }
} else {
    if (!groups_user_groups_visible($course, $override->userid, $cm)) {
        throw new \moodle_exception('invalidoverrideid', 'qbassign');
    }
}

$url = new moodle_url('/mod/qbassign/overridedelete.php', array('id' => $override->id));
$confirmurl = new moodle_url($url, array('id' => $override->id, 'confirm' => 1));
$cancelurl = new moodle_url('/mod/qbassign/overrides.php', array('cmid' => $cm->id));

if (!empty($override->userid)) {
    $cancelurl->param('mode', 'user');
}

// If confirm is set (PARAM_BOOL) then we have confirmation of intention to delete.
if ($confirm) {
    require_sesskey();

    $qbassign->delete_override($override->id);

    qbreorder_group_overrides($qbassign->get_instance()->id);

    redirect($cancelurl);
}

// Prepare the page to show the confirmation form.
$stroverride = get_string('override', 'qbassign');
$title = get_string('deletecheck', null, $stroverride);

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->set_attrs([
    "title" => format_string($qbassign->get_instance()->name, true, ['context' => $context]),
    "description" => "",
    "hidecompletion" => true
]);
$PAGE->set_secondary_active_tab('mod_qbassign_useroverrides');

echo $OUTPUT->header();

if ($override->groupid) {
    $group = $DB->get_record('groups', array('id' => $override->groupid), 'id, name');
    $confirmstr = get_string("overridedeletegroupsure", "qbassign", format_string($group->name, true, ['context' => $context]));
} else {
    $userfieldsapi = \core_user\fields::for_name();
    $namefields = $userfieldsapi->get_sql('', false, '', '', false)->selects;
    $user = $DB->get_record('user', array('id' => $override->userid),
            'id, ' . $namefields);
    $confirmstr = get_string("overridedeleteusersure", "qbassign", fullname($user));
}

echo $OUTPUT->confirm($confirmstr, $confirmurl, $cancelurl);

echo $OUTPUT->footer();
