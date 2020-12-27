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
 * Test helpers for the drag-and-drop markers question type.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the drag-and-drop markers question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('fox', 'maths', 'mkmap', 'zerodrag');
    }

    /**
     * @return qtype_ddmarker_question
     */
    public function make_ddmarker_question_fox() {
        question_bank::load_question_definition_classes('ddmarker');
        $dd = new qtype_ddmarker_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop markers question';
        $dd->questiontext = 'The quick brown fox jumped over the lazy dog.';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddmarker');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                    new qtype_ddmarker_drag_item('quick', 1, 0, 1),
                    new qtype_ddmarker_drag_item('fox', 2, 0, 1),
                    new qtype_ddmarker_drag_item('lazy', 3, 0, 1)

        ));

        $dd->places = $this->make_place_structure(array(
                            new qtype_ddmarker_drop_zone(1, 'circle', '50,50;50'),
                            new qtype_ddmarker_drop_zone(2, 'rectangle', '100,0;100,100'),
                            new qtype_ddmarker_drop_zone(3, 'polygon', '0,100;200,100;200,200;0,200')
        ));
        $dd->rightchoices = array(1 => 1, 2 => 2, 3 => 3);

        return $dd;
    }

    protected function make_choice_structure($choices) {
        $choicestructure = array();
        foreach ($choices as $choice) {
            $group = $choice->choice_group();
            if (!isset($choicestructure[$group])) {
                $choicestructure[$group] = array();
            }
            $choicestructure[$group][$choice->no] = $choice;
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
     * @return qtype_ddmarker_question
     */
    public function make_ddmarker_question_maths() {
        question_bank::load_question_definition_classes('ddmarker');
        $dd = new qtype_ddmarker_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop markers question';
        $dd->questiontext = 'Fill in the operators to make this equation work: ';
        $dd->generalfeedback = 'Hmmmm...';
        $dd->qtype = question_bank::get_qtype('ddmarker');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                    new qtype_ddmarker_drag_item('+', 1, 1, 0),
                    new qtype_ddmarker_drag_item('-', 2, 1, 0),
                    new qtype_ddmarker_drag_item('*', 3, 1, 0),
                    new qtype_ddmarker_drag_item('/', 4, 1, 0)

        ));

        $dd->places = $this->make_place_structure(array(
                    new qtype_ddmarker_drop_zone(1, 'circle', '50,50;50'),
                    new qtype_ddmarker_drop_zone(2, 'rectangle', '100,0;100,100'),
                    new qtype_ddmarker_drop_zone(3, 'polygon', '0,100;100,100;100,200;0,200')
        ));
        $dd->rightchoices = array(1 => 1, 2 => 1, 3 => 1);

        return $dd;
    }

    /**
     * @return stdClass date to create a ddmarkers question.
     */
    public function get_ddmarker_question_form_data_mkmap() {
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
        $filerecord->filename = 'mkmap.png';
        $fs->create_file_from_pathname($filerecord, $CFG->dirroot .
                '/question/type/ddmarker/tests/fixtures/mkmap.png');

        $fromform->name = 'Milton Keynes landmarks';
        $fromform->questiontext = array(
            'text' => 'Please place the markers on the map of Milton Keynes and be aware that '.
                      'there is more than one railway station.',
            'format' => FORMAT_HTML,
        );
        $fromform->defaultmark = 1;
        $fromform->generalfeedback = array(
            'text' => 'The Open University is at the junction of Brickhill Street and Groveway. '.
                      'There are three railway stations, Wolverton, Milton Keynes Central and Bletchley.',
            'format' => FORMAT_HTML,
        );
        $fromform->bgimage = $bgdraftitemid;
        $fromform->shuffleanswers = 0;

        $fromform->drags = array(
            array('label' => 'OU', 'noofdrags' => 1),
            array('label' => 'Railway station', 'noofdrags' => 3),
        );

        $fromform->drops = array(
            array('shape' => 'circle', 'coords' => '322,213;10', 'choice' => 1),
            array('shape' => 'circle', 'coords' => '144,84;10', 'choice' => 2),
            array('shape' => 'circle', 'coords' => '195,180;10', 'choice' => 2),
            array('shape' => 'circle', 'coords' => '267,302;10', 'choice' => 2),
        );

        test_question_maker::set_standard_combined_feedback_form_data($fromform);

        $fromform->penalty = '0.3333333';
        $fromform->hint = array(
            array(
                'text' => 'You are trying to place four markers on the map.',
                'format' => FORMAT_HTML,
            ),
            array(
                'text' => 'You are trying to mark three railway stations.',
                'format' => FORMAT_HTML,
            ),
        );
        $fromform->hintshownumcorrect = array(1, 1);
        $fromform->hintclearwrong = array(0, 1);
        $fromform->hintoptions = array(0, 1);

        return $fromform;
    }

    /**
     * Return the test data needed by the question generator (the data that
     * would come from saving the editing form).
     * @return stdClass date to create a ddmarkers question where one of the drag items has text '0'.
     */
    public function get_ddmarker_question_form_data_zerodrag() {
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
        $filerecord->filename = 'mkmap.png';
        $fs->create_file_from_pathname($filerecord, $CFG->dirroot .
                '/question/type/ddmarker/tests/fixtures/mkmap.png');

        $fromform->name = 'Drag digits';
        $fromform->questiontext = array(
            'text' => 'Put 0 in the left of the image, and 1 in the right.',
            'format' => FORMAT_HTML,
        );
        $fromform->defaultmark = 2;
        $fromform->generalfeedback = array(
            'text' => '',
            'format' => FORMAT_HTML,
        );
        $fromform->bgimage = $bgdraftitemid;
        $fromform->shuffleanswers = 0;

        $fromform->drags = array(
            array('label' => '0', 'noofdrags' => 1),
            array('label' => '1', 'noofdrags' => 1),
        );

        $fromform->drops = array(
            array('shape' => 'Rectangle', 'coords' => '0,0;272,389', 'choice' => 1),
            array('shape' => 'Rectangle', 'coords' => '272,0;272,389', 'choice' => 2),
        );

        test_question_maker::set_standard_combined_feedback_form_data($fromform);

        $fromform->penalty = '0.3333333';
        $fromform->hint = array(
            array(
                'text' => 'Hint 1.',
                'format' => FORMAT_HTML,
            ),
            array(
                'text' => 'Hint 2.',
                'format' => FORMAT_HTML,
            ),
        );
        $fromform->hintshownumcorrect = array(1, 1);
        $fromform->hintclearwrong = array(0, 1);
        $fromform->hintoptions = array(0, 1);

        return $fromform;
    }
}
