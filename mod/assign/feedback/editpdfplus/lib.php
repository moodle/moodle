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
 * This file contains the version information for the comments feedback plugin
 *
 * @package assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/lib.php by Davo Smith.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Serves assignment feedback and other files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options - List of options affecting file serving.
 * @return bool false if file not found, does not return if found - just send the file
 */
function assignfeedback_editpdfplus_pluginfile($course, $cm, context $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/mod/assign/locallib.php');

    if ($context->contextlevel == CONTEXT_MODULE) {

        require_login($course, false, $cm);
        $itemid = (int) array_shift($args);

        $assign = new assign($context, $cm, $course);

        $record = $DB->get_record('assign_grades', array('id' => $itemid), 'userid,assignment', MUST_EXIST);
        $userid = $record->userid;
        if ($assign->get_instance()->id != $record->assignment) {
            return false;
        }

        // Rely on mod_assign checking permissions.
        if (!$assign->can_view_submission($userid)) {
            return false;
        }

        $relativepath = implode('/', $args);

        $fullpath = "/{$context->id}/assignfeedback_editpdfplus/$filearea/$itemid/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        // Download MUST be forced - security!
        send_stored_file($file, 0, 0, true, $options); // Check if we want to retrieve the stamps.
    }
}

/**
 * Display menu inside course'admin view
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function assignfeedback_editpdfplus_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    if (has_capability('assignfeedback/editpdfplus:managetools', $context)) {
        $url = new moodle_url('/mod/assign/feedback/editpdfplus/view_admin.php', array('id' => $context->id));
        $feedbackadminnode = navigation_node::create(get_string('feedback_configuration', "assignfeedback_editpdfplus"), $url, navigation_node::TYPE_CUSTOM, 'Bars d\'outils', 'editpdfplusadmin', new pix_icon('i/grades', ""));
        $navigation->add_node($feedbackadminnode);
    }
}

/**
 * Get axis form (add)
 * @param type $args
 */
function assignfeedback_editpdfplus_output_fragment_axisadd($args) {
    $context = $args['context'];

    require_once('locallib_admin.php');

    require_capability('assignfeedback/editpdfplus:managetools', $context, null, true, get_string('admin_access_error', 'assignfeedback_editpdfplus'));

    $editpdfplus = new assign_feedback_editpdfplus_admin($context);
    return $editpdfplus->getAxisForm();
}

/**
 * Get axis form (edit)
 * @param type $args
 */
function assignfeedback_editpdfplus_output_fragment_axisedit($args) {
    $context = $args['context'];
    $axisid = $args['axeid'];

    require_once('locallib_admin.php');

    require_capability('assignfeedback/editpdfplus:managetools', $context, null, true, get_string('admin_access_error', 'assignfeedback_editpdfplus'));

    $editpdfplus = new assign_feedback_editpdfplus_admin($context);
    return $editpdfplus->getAxisForm($axisid);
}

/**
 * Get axis form (export)
 * @param type $args
 */
function assignfeedback_editpdfplus_output_fragment_axisexport($args) {
    $context = $args['context'];
    $axisid = $args['axeid'];

    require_once('locallib_admin.php');

    require_capability('assignfeedback/editpdfplus:managetools', $context, null, true, get_string('admin_access_error', 'assignfeedback_editpdfplus'));

    $editpdfplus = new assign_feedback_editpdfplus_admin($context);
    return $editpdfplus->getAxisExportForm($axisid);
}

/**
 * Get tool form (edit)
 * @param type $args
 */
function assignfeedback_editpdfplus_output_fragment_tooledit($args) {
    $context = $args['context'];
    $toolid = $args['toolid'];

    require_once('locallib_admin.php');

    require_capability('assignfeedback/editpdfplus:managetools', $context, null, true, get_string('admin_access_error', 'assignfeedback_editpdfplus'));

    $editpdfplus = new assign_feedback_editpdfplus_admin($context);
    return $editpdfplus->getToolForm($toolid);
}

/**
 * Get tool form (add)
 * @param type $args
 */
function assignfeedback_editpdfplus_output_fragment_tooladd($args) {
    $context = $args['context'];
    $axisid = $args['axisid'];

    require_once('locallib_admin.php');

    require_capability('assignfeedback/editpdfplus:managetools', $context, null, true, get_string('admin_access_error', 'assignfeedback_editpdfplus'));

    $editpdfplus = new assign_feedback_editpdfplus_admin($context);
    return $editpdfplus->getToolForm(null, $axisid);
}
