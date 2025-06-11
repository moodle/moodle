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
//

/**
 * This file contains library functions for the Panopto LTI Button for Atto plugin.
 *
 * @package    atto_panoptoltibutton
 * @copyright  2020 - Panopto
 * @author     Panopto with contributions from David Shepard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Sets up strings for button display.
 */
function atto_panoptoltibutton_strings_for_js() {
    global $PAGE;
    $PAGE->requires->strings_for_js(['lti', 'erroroccurred'], 'atto_panoptoltibutton');
}

/**
 * Sets up LTI parameters.
 *
 * @param int $elementid atto required param
 * @param array $options atto required param
 * @param array $fpoptions atto required param
 * @return array of additional params to pass to javascript init function for this module.
 */
function atto_panoptoltibutton_params_for_js($elementid, $options, $fpoptions) {
    global $PAGE, $DB, $CFG, $COURSE;

    if (empty($CFG)) {
        require_once(dirname(__FILE__) . '/../../../../../config.php');
    }
    require_once($CFG->dirroot . '/mod/lti/lib.php');
    require_once($CFG->dirroot . '/mod/lti/locallib.php');

    $coursecontext = context_course::instance($COURSE->id);
    $targetservername = $DB->get_field('block_panopto_foldermap', 'panopto_server', ['moodleid' => $PAGE->course->id]);

    // If the course is not provisioned with the Panopto block, retrieve the default Panopto server FQDN.
    if (empty($targetservername)) {
        $targetservername = get_config('block_panopto', 'automatic_operation_target_server');
    }

    $ltitooltypes = !empty($targetservername)
        ? $DB->get_records('lti_types', ['tooldomain' => $targetservername], 'name')
        : atto_get_filtered_lti_tool_types();

    $tooltypes = [];
    foreach ($ltitooltypes as $type) {
        $type->config = lti_get_config(
            (object)[
                'typeid' => $type->id,
            ]
        );

        // Match the tool, make sure it is in configured state and course visible.
        if (!empty($targetservername) && strpos($type->config['toolurl'], $targetservername) !== false
                && $type->state == LTI_TOOL_STATE_CONFIGURED
                && $type->coursevisible != LTI_COURSEVISIBLE_NO) {

            $tooltypes[] = $type;
        }
    }

    // If they don't have permission don't show it.
    $disabled = false;
    if (!has_capability('atto/panoptoltibutton:visible', $coursecontext)) {
        $disabled = true;
    }

    $isresponsive = false;
    if (get_config('atto_panoptoltibutton', 'is_responsive')) {
        $isresponsive = true;
    }

    return [
        'toolTypes' => $tooltypes,
        'course' => $PAGE->course,
        'disabled' => $disabled,
        'resourcebase' => sha1(
            $PAGE->url->__toString() . '&' . $PAGE->course->sortorder
                . '&' . $PAGE->course->timecreated
        ),
        'isResponsive' => $isresponsive,
    ];
}

/**
 * Return filtered lti tool types
 * @return mixed
 */
function atto_get_filtered_lti_tool_types() {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/lti/locallib.php');

    $sql = "
        SELECT *
        FROM {lti_types} lt
        WHERE lt.state = :state
        AND (
            lt.baseurl LIKE :panopto_com_pattern
            OR lt.baseurl LIKE :panopto_eu_pattern
        )";

    // Since we don't have targeted server, let's use baseurl for filtering.
    $params = [
        'state' => LTI_TOOL_STATE_CONFIGURED,
        'panopto_com_pattern' => '%.panopto.com%',
        'panopto_eu_pattern' => '%.panopto.eu%',
    ];

    $tooltypes = $DB->get_records_sql($sql, $params);

    return $tooltypes;
};
