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
 * @package    atto_collapse
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Initialise the js strings required for this module.
 */
function atto_collapse_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('showmore', 'showfewer'), 'atto_collapse');
}

/**
 * Set params for this plugin
 * @param string $elementid
 */
function atto_collapse_params_for_js($elementid, $options, $fpoptions) {
    // Pass the number of visible groups as a param.
    $params = array('showgroups' => get_config('atto_collapse', 'showgroups'));
    return $params;
}
