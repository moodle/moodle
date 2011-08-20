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

define('QTYPE_DDIMAGETOIMAGE_BGIMAGE_MAXWIDTH', 600);
define('QTYPE_DDIMAGETOIMAGE_BGIMAGE_MAXHEIGHT', 400);
define('QTYPE_DDIMAGETOIMAGE_DRAGIMAGE_MAXWIDTH', 150);
define('QTYPE_DDIMAGETOIMAGE_DRAGIMAGE_MAXHEIGHT', 100);

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

    protected function make_choice($dragdata) {
        return new qtype_ddimagetoimage_drag_item($dragdata->label, $dragdata->no,
                                        $dragdata->draggroup, $dragdata->infinite, $dragdata->id);
    }

    protected function make_place($dropzonedata) {
        return new qtype_ddimagetoimage_drop_zone($dropzonedata->label, $dropzonedata->group,
                                                    $dropzonedata->xleft, $dropzonedata->ytop);
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->shufflechoices = $questiondata->options->shuffleanswers;

        $this->initialise_combined_feedback($question, $questiondata, true);

        $question->choices = array();
        $choiceindexmap= array();

        // Store the choices in arrays by group.
        foreach ($questiondata->options->drags as $dragdata) {

            $choice = $this->make_choice($dragdata);

            if (array_key_exists($choice->choice_group(), $question->choices)) {
                $question->choices[$choice->choice_group()][$dragdata->no] = $choice;
            } else {
                $question->choices[$choice->choice_group()][1] = $choice;
            }

            end($question->choices[$choice->choice_group()]);
            $choiceindexmap[$dragdata->no] = array($choice->choice_group(),
                    key($question->choices[$choice->choice_group()]));
        }

        $question->places = array();
        $question->rightchoices = array();

        $i = 1;

        foreach ($questiondata->options->drops as $dropdata) {
            list($group, $choiceindex) = $choiceindexmap[$dropdata->choice];
            $dropdata->group = $group;
            $question->places[$dropdata->no] = $this->make_place($dropdata);
            $question->rightchoices[$dropdata->no] = $choiceindex;
        }
    }

    public function save_question_options($formdata) {
        global $DB, $USER;
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

        //an array of drag no -> drag id
        $olddragids = $DB->get_records_menu('qtype_ddimagetoimage_drags',
                                    array('questionid' => $formdata->id),
                                    '', 'no, id');
        foreach (array_keys($formdata->drags) as $dragno){
            $info = file_get_draft_area_info($formdata->dragitem[$dragno]);
            if ($info['filecount'] > 0) {
                $draftitemid = $formdata->dragitem[$dragno];

                $drag = new stdClass();
                $drag->questionid = $formdata->id;
                $drag->no = $dragno + 1;
                $drag->draggroup = $formdata->drags[$dragno]['draggroup'];
                $drag->infinite = empty($formdata->drags[$dragno]['infinite'])? 0 : 1;
                $drag->label = $formdata->drags[$dragno]['draglabel'];

                if (isset($olddragids[$dragno +1])) {
                    $drag->id = $olddragids[$dragno +1];
                    unset($olddragids[$dragno +1]);
                    $DB->update_record('qtype_ddimagetoimage_drags', $drag);
                } else {
                    $drag->id = $DB->insert_record('qtype_ddimagetoimage_drags', $drag);
                }



                self::constrain_image_size_in_draft_area($draftitemid,
                                        QTYPE_DDIMAGETOIMAGE_DRAGIMAGE_MAXWIDTH,
                                        QTYPE_DDIMAGETOIMAGE_DRAGIMAGE_MAXHEIGHT);
                file_save_draft_area_files($draftitemid, $formdata->context->id,
                                        'qtype_ddimagetoimage', 'dragimage', $drag->id,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
            }
        }
        if (!empty($olddragids)) {
            list($sql, $params) = $DB->get_in_or_equal(array_values($olddragids));
            $DB->delete_records_select('qtype_ddimagetoimage_drags', "id $sql", $params);
        }

        self::constrain_image_size_in_draft_area($formdata->bgimage,
                                                    QTYPE_DDIMAGETOIMAGE_BGIMAGE_MAXWIDTH,
                                                    QTYPE_DDIMAGETOIMAGE_BGIMAGE_MAXHEIGHT);
        file_save_draft_area_files($formdata->bgimage, $formdata->context->id,
                                    'qtype_ddimagetoimage', 'bgimage', $formdata->id,
                                    array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
    }

    public static function constrain_image_size_in_draft_area($draftitemid, $maxwidth, $maxheight) {
        global $USER;
        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
        $fs = get_file_storage();
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id');
        if ($draftfiles) {
            foreach ($draftfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $imageinfo = $file->get_imageinfo();
                $width    = $imageinfo['width'];
                $height   = $imageinfo['height'];
                $mimetype = $imageinfo['mimetype'];
                switch ($mimetype) {
                    case 'image/jpeg' :
                        $quality = 80;
                        break;
                    case 'image/png' :
                        $quality = 8;
                        break;
                    default :
                        $quality = NULL;
                }
                $newwidth = min($maxwidth, $width);
                $newheight = min($maxheight, $height);
                if ($newwidth != $width || $newheight != $height) {
                    $newimagefilename = $file->get_filename();
                    $newimagefilename =
                        preg_replace('!\.!', "_{$newwidth}x{$newheight}.", $newimagefilename, 1);
                    $newrecord = new stdClass();
                    $newrecord->contextid = $usercontext->id;
                    $newrecord->component = 'user';
                    $newrecord->filearea  = 'draft';
                    $newrecord->itemid    = $draftitemid;
                    $newrecord->filepath  = '/';
                    $newrecord->filename  = $newimagefilename;
                    $fs->convert_image($newrecord, $file, $newwidth, $newheight, true, $quality);
                    $file->delete();
                }
            }
        }
    }


}
