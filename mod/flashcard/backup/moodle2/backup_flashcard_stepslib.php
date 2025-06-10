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
 * @package     mod_flashcard
 * @category    mod
 * @author      Tomasz Muras <nexor1984@gmail.com>
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class backup_flashcard_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        $flashcard = new backup_nested_element('flashcard', array('id'), array(
                    'name', 'intro', 'introformat', 'timemodified', 'starttime', 'endtime', 'questionid', 'models',
                    'autodowngrade', 'decks', 'deck2_release', 'deck3_release', 'deck4_release', 'deck1_delay',
                    'deck2_delay', 'deck3_delay', 'deck4_delay', 'questionsmediatype', 'answersmediatype',
                    'audiostart', 'flipdeck', 'custombackfileid', 'customfrontfileid', 'customemptyfileid',
                    'customreviewfileid', 'customreviewedfileid', 'customreviewemptyfileid',
                    'completionallviewed', 'completionallgood'));

        $decks = new backup_nested_element('group_decks');

        $deck = new backup_nested_element('deck', array('id'), array(
                    'questiontext', 'answertext'));

        $cards = new backup_nested_element('group_cards');

        $card = new backup_nested_element('card', array('id'), array(
                    'userid', 'entryid', 'deck', 'lastaccessed', 'accesscount'
                        ));

        $deckstates = new backup_nested_element('deckstates');

        $deckstate = new backup_nested_element('deckstate', array('id'), array(
                    'userid', 'deck', 'state'
                        ));

        $flashcard->add_child($decks);
        $decks->add_child($deck);

        $flashcard->add_child($cards);
        $cards->add_child($card);

        $flashcard->add_child($deckstates);
        $deckstates->add_child($deckstate);

        // Sources.
        $flashcard->set_source_table('flashcard', array('id' => backup::VAR_ACTIVITYID));
        $deck->set_source_table('flashcard_deckdata', array('flashcardid' => backup::VAR_PARENTID));

        if ($this->get_setting_value('userinfo')) {
            $card->set_source_table('flashcard_card', array('flashcardid' => backup::VAR_PARENTID));
            $deckstate->set_source_table('flashcard_userdeck_state', array('flashcardid' => backup::VAR_PARENTID));
        }

        // Define id annotations.
        $card->annotate_ids('user', 'userid');
        $deckstate->annotate_ids('user', 'userid');

        // Define file annotations.
        $flashcard->annotate_files('mod_flashcard', 'intro', null); // This file areas haven't itemid.
        $deck->annotate_files('mod_flashcard', 'questionsoundfile', 'id');
        $deck->annotate_files('mod_flashcard', 'questionimagefile', 'id');
        $deck->annotate_files('mod_flashcard', 'questionvideofile', 'id');
        $deck->annotate_files('mod_flashcard', 'answersoundfile', 'id');
        $deck->annotate_files('mod_flashcard', 'answerimagefile', 'id');
        $deck->annotate_files('mod_flashcard', 'answervideofile', 'id');
        $flashcard->annotate_files('mod_flashcard', 'customfront', null);
        $flashcard->annotate_files('mod_flashcard', 'customempty', null);
        $flashcard->annotate_files('mod_flashcard', 'customback', null);
        $flashcard->annotate_files('mod_flashcard', 'customreview', null);
        $flashcard->annotate_files('mod_flashcard', 'customreviewed', null);
        $flashcard->annotate_files('mod_flashcard', 'customreviewempty', null);

        return $this->prepare_activity_structure($flashcard);
    }
}
