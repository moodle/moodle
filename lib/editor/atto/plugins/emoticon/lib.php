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
 * Atto text editor emoticon plugin lib.
 *
 * @package    atto_emoticon
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Initialise the strings required for JS.
 *
 * @return void
 */
function atto_emoticon_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('insertemoticon'), 'atto_emoticon');

    // Load the strings required by the emotes.
    $manager = get_emoticon_manager();
    foreach ($manager->get_emoticons(true) as $emote) {
        $PAGE->requires->string_for_js($emote->altidentifier, $emote->altcomponent);
    }
}

/**
 * Sends the parameters to JS module.
 *
 * @return array
 */
function atto_emoticon_params_for_js($elementid, $options, $fpoptions) {
    $manager = get_emoticon_manager();
    return array(
        'emoticons' => $manager->get_emoticons(true)
    );
}
