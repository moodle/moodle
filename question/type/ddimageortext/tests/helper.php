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
 * Test helpers for the drag-and-drop onto image question type.
 *
 * @package    qtype_ddimageortext
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the drag-and-drop onto image question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('fox', 'maths', 'xsection', 'mixedlang');
    }

    /**
     * @return qtype_ddimageortext_question
     */
    public function make_ddimageortext_question_fox() {
        question_bank::load_question_definition_classes('ddimageortext');
        $dd = new qtype_ddimageortext_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop onto image question';
        $dd->questiontext = 'The quick brown fox jumped over the lazy dog.';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddimageortext');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                    new qtype_ddimageortext_drag_item('quick', 1, 1),
                    new qtype_ddimageortext_drag_item('fox', 2, 1),
                    new qtype_ddimageortext_drag_item('lazy', 3, 2),
                    new qtype_ddimageortext_drag_item('dog', 4, 2)

        ));

        $dd->places = $this->make_place_structure(array(
                            new qtype_ddimageortext_drop_zone('', 1, 1),
                            new qtype_ddimageortext_drop_zone('', 2, 1),
                            new qtype_ddimageortext_drop_zone('', 3, 2),
                            new qtype_ddimageortext_drop_zone('', 4, 2)
        ));
        $dd->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 4);

        return $dd;
    }

    protected function make_choice_structure($choices) {
        $choicestructure = array();
        foreach ($choices as $choice) {
            if (!isset($choicestructure[$choice->group])) {
                $choicestructure[$choice->group][1] = $choice;
            } else {
                $choicestructure[$choice->group][$choice->no] = $choice;
            }
        }
        return $choicestructure;
    }

    protected function make_place_structure($places) {
        $placestructure = array();
        foreach ($places as $place) {
            $placestructure[$place->no] = $place;
        }
        return $placestructure;
    }

    /**
     * Make a mathematical ddimageortext question.
     *
     * @return qtype_ddimageortext_question
     */
    public function make_ddimageortext_question_maths() {
        question_bank::load_question_definition_classes('ddimageortext');
        $dd = new qtype_ddimageortext_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop onto image question';
        $dd->questiontext = 'Fill in the operators to make this equation work: ' .
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddimageortext');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                new qtype_ddimageortext_drag_item('+', 1, 1),
                new qtype_ddimageortext_drag_item('-', 2, 1)
        ));

        $dd->places = $this->make_place_structure(array(
                            new qtype_ddimageortext_drop_zone('', 1, 1),
                            new qtype_ddimageortext_drop_zone('', 2, 1),
                            new qtype_ddimageortext_drop_zone('', 3, 1),
                            new qtype_ddimageortext_drop_zone('', 4, 1)
        ));
        $dd->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 2);

        return $dd;
    }

    /**
     * @return stdClass date to create a ddimageortext question.
     */
    public function get_ddimageortext_question_form_data_xsection() {
        global $CFG, $USER;
        $fromform = new stdClass();

        $bgdraftitemid = 0;
        file_prepare_draft_area($bgdraftitemid, null, null, null, null);
        $fs = get_file_storage();
        $filerecord = new stdClass();
        $filerecord->contextid = context_user::instance($USER->id)->id;
        $filerecord->component = 'user';
        $filerecord->filearea = 'draft';
        $filerecord->itemid = $bgdraftitemid;
        $filerecord->filepath = '/';
        $filerecord->filename = 'oceanfloorbase.jpg';
        $fs->create_file_from_pathname($filerecord, $CFG->dirroot .
                '/question/type/ddimageortext/tests/fixtures/oceanfloorbase.jpg');

        $fromform->name = 'Geography cross-section';
        $fromform->questiontext = array(
            'text' => '<p>Identify the features in this cross-section by dragging the labels into the boxes.</p>
                       <p><em>Use the mouse to drag the boxed words into the empty boxes. '.
                      'Alternatively, use the tab key to select an empty box, '.
                      'then use the space key to cycle through the options.</em></p>',
            'format' => FORMAT_HTML,
        );
        $fromform->defaultmark = 1;
        $fromform->generalfeedback = array(
            'text' => '<p>More information about the major features of the Earth\'s surface '.
                      'can be found in Block 3, Section 6.2.</p>',
            'format' => FORMAT_HTML,
        );
        $fromform->bgimage = $bgdraftitemid;
        $fromform->shuffleanswers = 0;
        $fromform->drags = array(
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
            array('dragitemtype' => 'word', 'draggroup' => '1', 'infinite' => '0'),
        );
        $fromform->dragitem = array(0, 0, 0, 0, 0, 0, 0, 0);
        $fromform->draglabel =
        array(
            'island<br/>arc',
            'mid-ocean<br/>ridge',
            'abyssal<br/>plain',
            'continental<br/>rise',
            'ocean<br/>trench',
            'continental<br/>slope',
            'mountain<br/>belt',
            'continental<br/>shelf',
        );
        $fromform->drops = array(
            array('xleft' => '53', 'ytop' => '17', 'choice' => '7', 'droplabel' => ''),
            array('xleft' => '172', 'ytop' => '2', 'choice' => '8', 'droplabel' => ''),
            array('xleft' => '363', 'ytop' => '31', 'choice' => '5', 'droplabel' => ''),
            array('xleft' => '440', 'ytop' => '13', 'choice' => '3', 'droplabel' => ''),
            array('xleft' => '115', 'ytop' => '74', 'choice' => '6', 'droplabel' => ''),
            array('xleft' => '210', 'ytop' => '94', 'choice' => '4', 'droplabel' => ''),
            array('xleft' => '310', 'ytop' => '87', 'choice' => '1', 'droplabel' => ''),
            array('xleft' => '479', 'ytop' => '84', 'choice' => '2', 'droplabel' => ''),
        );

        test_question_maker::set_standard_combined_feedback_form_data($fromform);

        $fromform->penalty = '0.3333333';
        $fromform->hint = array(
            array(
                'text' => '<p>Incorrect placements will be removed.</p>',
                'format' => FORMAT_HTML,
            ),
            array(
                'text' => '<ul>
                           <li>The abyssal plain is a flat almost featureless expanse of ocean '.
                           'floor 4km to 6km below sea-level.</li>
                           <li>The continental rise is the gently sloping part of the ocean floor beyond the continental slope.</li>
                           <li>The continental shelf is the gently sloping ocean floor just offshore from the land.</li>
                           <li>The continental slope is the relatively steep part of the ocean floor '.
                           'beyond the continental shelf.</li>
                           <li>A mid-ocean ridge is a broad submarine ridge several kilometres high.</li>
                           <li>A mountain belt is a long range of mountains.</li>
                           <li>An island arc is a chain of volcanic islands.</li>
                           <li>An oceanic trench is a deep trough in the ocean floor.</li>
                           </ul>',
                'format' => FORMAT_HTML,
            ),
            array(
                'text' => '<p>Incorrect placements will be removed.</p>',
                'format' => FORMAT_HTML,
            ),
            array(
                'text' => '<ul>
                           <li>The abyssal plain is a flat almost featureless expanse of ocean '.
                           'floor 4km to 6km below sea-level.</li>
                           <li>The continental rise is the gently sloping part of the ocean floor beyond the continental slope.</li>
                           <li>The continental shelf is the gently sloping ocean floor just offshore from the land.</li>
                           <li>The continental slope is the relatively steep part of the ocean floor '.
                           'beyond the continental shelf.</li>
                           <li>A mid-ocean ridge is a broad submarine ridge several kilometres high.</li>
                           <li>A mountain belt is a long range of mountains.</li>
                           <li>An island arc is a chain of volcanic islands.</li>
                           <li>An oceanic trench is a deep trough in the ocean floor.</li>
                           </ul>',
                'format' => FORMAT_HTML,
            ),
        );
        $fromform->hintclearwrong = array(1, 0, 1, 0);
        $fromform->hintshownumcorrect = array(1, 1, 1, 1);

        return $fromform;
    }

    /**
     * Make a test question where the drag items are a different language than the main question text.
     *
     * @return qtype_ddimageortext_question
     */
    public function make_ddimageortext_question_mixedlang() {
        question_bank::load_question_definition_classes('ddimageortext');
        $dd = new qtype_ddimageortext_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Question about French in English.';
        $dd->questiontext = '<p>Complete the blanks in this sentence.</p>' .
                '<p lang="fr">J\'ai perdu [[1]] plume de [[2]] tante - l\'avez-vous vue?</p>';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddimageortext');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                new qtype_ddimageortext_drag_item('<span lang="fr">la</span>', 1, 1),
                new qtype_ddimageortext_drag_item('<span lang="fr">ma</span>', 2, 1),
        ));

        $dd->places = $this->make_place_structure(array(
                new qtype_ddimageortext_drop_zone('', 1, 1),
                new qtype_ddimageortext_drop_zone('', 2, 1)
        ));
        $dd->rightchoices = array(1 => 1, 2 => 2);

        return $dd;
    }
}
