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
 * Atto text editor integration version file.
 *
 * @package   atto_vidgridbutton
 * @copyright Panopto 2009 - 2016
 * @copyright ilos 2017
 * @copyright VidGrid 2018 - 2020
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Initialize this plugin
 */
function atto_vidgridbutton_strings_for_js() {

    global $PAGE;

    $PAGE->requires->strings_for_js(array('dialogtitle'), 'atto_vidgridbutton');
}

/**
 * Return the js params required for this module.
 *
 * @param int $elementid
 * @param array $options
 * @param array $fpoptions
 * @return array of additional params to pass to javascript init function for this module.
 */
function atto_vidgridbutton_params_for_js($elementid, $options, $fpoptions) {

    global $USER, $COURSE, $DB, $CFG;

    $coursecontext = context_course::instance($COURSE->id);

    $disabled = false;

    $params = array();

    // If they don't have permission don't show it.
    if (!has_capability('atto/vidgridbutton:visible', $coursecontext) ) {
        $disabled = true;
    }

    $params['courseId'] = $COURSE->id;

    $params['orgApiKey'] = get_config('atto_vidgridbutton', 'orgApiKey');

    if($params['orgApiKey'] == '')
    {
        $disabled = true;
    }

    $params['disabled'] = $disabled;

    $params["webRoot"] = $CFG->wwwroot;

    $params["sessKey"] = $USER->sesskey;


    return $params;
}
