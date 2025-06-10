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
 * This view allows checking deck states
 *
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @contributors Valery Fremaux
 * @version Moodle 2.0
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

// Print deferred header.

echo $out;

// Get available decks for user and calculate deck state.

if (!$decks = flashcard_get_deck_status($flashcard)) {
    // If deck status have bever been initialized initialized them.
    if (flashcard_initialize($flashcard, $USER->id)) {
        $decks = flashcard_get_deck_status($flashcard);
    } else {
        if (has_capability('mod/flashcard:manage', $context)) {
            $url = new moodle_url('/mod/flashcard/view.php', array('id' => $cm->id, 'view' => 'edit'));
        } else {
            $url = new moodle_url('/course/view.php', array('id' => $course->id));
        }
        echo $OUTPUT->notification(get_string('nocards', 'flashcard'));
        echo $OUTPUT->continue_button($url);
    }
} else {
    echo '<center>';
    echo $renderer->check_decks($flashcard, $cm, $decks);
    echo '</center>';
}