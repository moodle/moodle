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
 * Question type class for the drag-and-drop words into sentences question type.
 *
 * @package    qtype
 * @subpackage ddimagetoimage
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/gapselect/questiontypebase.php');


/**
 * The drag-and-drop words into sentences question type class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimagetoimage extends question_type {
    protected function choice_group_key() {
        return 'draggroup';
    }

    public function requires_qtypes() {
        return array('gapselect');
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('qtype_ddimagetoimage',
                array('questionid' => $question->id), '*', MUST_EXIST);
        $question->options->drags = $DB->get_records('qtype_ddimagetoimage_drags',
                array('questionid' => $question->id), 'no ASC', '*');
        $question->options->drops = $DB->get_records('qtype_ddimagetoimage_drops',
                array('questionid' => $question->id), 'no ASC', '*');
        parent::get_question_options($question);
    }

    public function save_question_options($formdata) {
        global $DB;
        $context = $formdata->context;

        $options = $DB->get_record('qtype_ddimagetoimage', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('qtype_ddimagetoimage', $options);
        }

        $options->shuffleanswers = !empty($formdata->shuffleanswers);
        $options = $this->save_combined_feedback_helper($options, $formdata, $context, true);
        $this->save_hints($formdata, true);
        $DB->update_record('qtype_ddimagetoimage', $options);
        $DB->delete_records('qtype_ddimagetoimage_drops', array('questionid' => $formdata->id));
        foreach (array_keys($formdata->drops) as $dropno){
            if ($formdata->drops[$dropno]['choice'] == 0){
                continue;
            }
            $drop = new stdClass();
            $drop->questionid = $formdata->id;
            $drop->no = $dropno + 1;
            $drop->xleft = $formdata->drops[$dropno]['xleft'];
            $drop->ytop = $formdata->drops[$dropno]['ytop'];
            $drop->choice = $formdata->drops[$dropno]['choice'];
            $drop->label = $formdata->drops[$dropno]['droplabel'];

            $DB->insert_record('qtype_ddimagetoimage_drops', $drop);
        }

        $DB->delete_records('qtype_ddimagetoimage_drags', array('questionid' => $formdata->id));
        foreach (array_keys($formdata->drags) as $dragno){
            $info = file_get_draft_area_info($formdata->dragitem[$dragno]);
            if ($info['filecount'] > 0) {
                //numbers not allowed in filearea name
                $filearea = str_replace(range('0', '9'), range('a', 'j'), "drag_$dragno");
                file_save_draft_area_files($formdata->dragitem[$dragno], $formdata->context->id,
                                        'qtype_ddimagetoimage', $filearea, $formdata->id,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
                $drag = new stdClass();
                $drag->questionid = $formdata->id;
                $drag->no = $dragno + 1;
                $drag->draggroup = $formdata->drags[$dragno]['draggroup'];
                $drag->infinite = empty($formdata->drags[$dragno]['infinite'])? 0 : 1;
                $drag->label = $formdata->drags[$dragno]['draglabel'];

                $DB->insert_record('qtype_ddimagetoimage_drags', $drag);
            }
        }

        file_save_draft_area_files($formdata->bgimage, $formdata->context->id,
                                    'qtype_ddimagetoimage', 'bgimage', $formdata->id,
                                    array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
    }


}
