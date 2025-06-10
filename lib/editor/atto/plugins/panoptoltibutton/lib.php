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
    global $PAGE, $DB, $CFG;

    if (empty($CFG)) {
        require_once(dirname(__FILE__) . '/../../../../../config.php');
    }
    require_once($CFG->dirroot . '/mod/lti/lib.php');
    require_once($CFG->dirroot . '/mod/lti/locallib.php');

    $ltitooltypes = $DB->get_records('lti_types', null, 'name');

    $targetservername = $DB->get_field('block_panopto_foldermap', 'panopto_server', array('moodleid' => $PAGE->course->id));
    
    $tooltypes = [];
    foreach ($ltitooltypes as $type) {
        $type->config = lti_get_config(
            (object)[
                'typeid' => $type->id,
            ]
        );

        if (!empty($targetservername) && strpos($type->config['toolurl'], $targetservername) !== false) {
            $tooltypes[] = $type;
        }
    }


    return [
        'toolTypes' => $tooltypes,
        'course' => $PAGE->course,
        'resourcebase' => sha1(
            $PAGE->url->__toString() . '&' . $PAGE->course->sortorder
                . '&' . $PAGE->course->timecreated
        ),
    ];
}