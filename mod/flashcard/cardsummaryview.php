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
 * This view provides a summary for the teacher
 *
 * @package mod_flashcard
 * @category mod
 * @author Valery Fremaux, Gustav Delius
 * @contributors
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

// Deffered header.
echo $out;

$cards = flashcard_get_card_status($flashcard);

$strcard = get_string('card', 'flashcard');
$strviewed = get_string('viewed', 'flashcard');
$strdecks = get_string('decks', 'flashcard');

$table = new html_table();

$table->head = array("<b>$strcard</b>", "<b>$strdecks</b>", "<b>$strviewed</b>");
$table->size = array('30%', '35%', '35%');
$table->width = "100%";

foreach ($cards as $cardquestion => $acard) {
    $cardcounters = $renderer->print_cardcounts($flashcard, $acard);
    $table->data[] = array(format_string($cardquestion), $cardcounters, $acard->accesscount);
}

echo html_writer::table($table);

