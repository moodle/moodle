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
 * Post-install script for the manual graded question behaviour.
 *
 * @package   qbehaviour_manualgraded
 * @copyright 2013 The Open Universtiy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Manual graded question behaviour upgrade code.
 */
function xmldb_qbehaviour_manualgraded_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2013050200) {
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

        // Manual graded question behaviour savepoint reached.
        upgrade_plugin_savepoint(true, 2013050200, 'qbehaviour', 'manualgraded');
    }

    if ($oldversion < 2013050800) {
        // Also, fix any other admin settings that currently select manualgraded behaviour.

        // Work out a sensible default alternative to manualgraded.
        require_once($CFG->libdir . '/questionlib.php');
        $behaviours = question_engine::get_behaviour_options('');
        if (array_key_exists('deferredfeedback', $behaviours)) {
             $defaultbehaviour = 'deferredfeedback';
        } else {
            reset($behaviours);
            $defaultbehaviour = key($behaviours);
        }

        // Fix the question preview default.
        if (get_config('question_preview', 'behaviour') == 'manualgraded') {
            set_config('behaviour', $defaultbehaviour, 'question_preview');
        }

        // Fix the quiz settings default.
        if (get_config('quiz', 'preferredbehaviour') == 'manualgraded') {
            set_config('preferredbehaviour', $defaultbehaviour, 'quiz');
        }

        // Manual graded question behaviour savepoint reached.
        upgrade_plugin_savepoint(true, 2013050800, 'qbehaviour', 'manualgraded');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

