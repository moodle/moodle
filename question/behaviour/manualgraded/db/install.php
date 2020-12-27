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
 * Post-install script for manual graded question behaviour.
 * @package   qbehaviour_manualgraded
 * @copyright 2013 The Open Universtiy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Post-install script
 */
function xmldb_qbehaviour_manualgraded_install() {

    // Hide the manualgraded behaviour from the list of behaviours that users
    // can select in the user-interface. If a user accidentally chooses manual
    // graded behaviour for a quiz, there is no way to get the questions automatically
    // graded after the student has answered them. If teachers really want to do
    // this they can ask their admin to enable it on the manage behaviours
    // screen in the UI.
    $disabledbehaviours = get_config('question', 'disabledbehaviours');
    if (!empty($disabledbehaviours)) {
        $disabledbehaviours = explode(',', $disabledbehaviours);
    } else {
        $disabledbehaviours = array();
    }
    if (array_search('manualgraded', $disabledbehaviours) === false) {
        $disabledbehaviours[] = 'manualgraded';
        set_config('disabledbehaviours', implode(',', $disabledbehaviours), 'question');
    }
}
