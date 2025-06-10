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
 * Atto text editor integration lib file.
 *
 * @package    atto_teamsmeeting
 * @copyright  2020 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/url/lib.php');

/**
 * Set params for this button.
 *
 * @param string $elementid
 * @param stdClass $options - the options for the editor, including the context.
 * @param stdClass $fpoptions - unused.
 */
function atto_teamsmeeting_params_for_js($elementid, $options, $fpoptions) {
    global $CFG, $SESSION, $USER;
    $params = [
        'clientdomain' => encode_url($CFG->wwwroot),
        'appurl' => get_config('atto_teamsmeeting', 'meetingapplink'),
        'locale' => (empty($SESSION->lang) ? $USER->lang : $SESSION->lang),
        'msession' => sesskey(),
        'editor' => 'atto',
    ];

    return $params;
}

/**
 * Initialise this plugin
 */
function atto_teamsmeeting_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js([
        'addlink',
        'createteamsmeeting',
        'meetingurl',
        'openinnewwindow'],
        'atto_teamsmeeting');
}

