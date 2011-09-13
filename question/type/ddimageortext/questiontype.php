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
 * Question type class for the drag-and-drop images onto images question type.
 *
 * @package    qtype
 * @subpackage ddimageortext
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/gapselect/questiontypebase.php');

define('QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXWIDTH', 600);
define('QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXHEIGHT', 400);
define('QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXWIDTH', 150);
define('QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXHEIGHT', 100);

/**
 * The drag-and-drop words into sentences question type class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext extends question_type {
    protected function choice_group_key() {
        return 'draggroup';
    }

    public function requires_qtypes() {
        return array('gapselect');
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('qtype_ddimageortext',
                array('questionid' => $question->id), '*', MUST_EXIST);
        $question->options->drags = $DB->get_records('qtype_ddimageortext_drags',
                array('questionid' => $question->id), 'no ASC', '*');
        $question->options->drops = $DB->get_records('qtype_ddimageortext_drops',
                array('questionid' => $question->id), 'no ASC', '*');
        parent::get_question_options($question);
    }

    protected function make_choice($dragdata) {
        return new qtype_ddimageortext_drag_item($dragdata->label, $dragdata->no,
                                        $dragdata->draggroup, $dragdata->infinite, $dragdata->id);
    }

    protected function make_place($dropzonedata) {
        return new qtype_ddimageortext_drop_zone($dropzonedata->label, $dropzonedata->no,
                                                    $dropzonedata->group,
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

        $options = $DB->get_record('qtype_ddimageortext', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('qtype_ddimageortext', $options);
        }

        $options->shuffleanswers = !empty($formdata->shuffleanswers);
        $options = $this->save_combined_feedback_helper($options, $formdata, $context, true);
        $this->save_hints($formdata, true);
        $DB->update_record('qtype_ddimageortext', $options);
        $DB->delete_records('qtype_ddimageortext_drops', array('questionid' => $formdata->id));
        foreach (array_keys($formdata->drops) as $dropno) {
            if ($formdata->drops[$dropno]['choice'] == 0) {
                continue;
            }
            $drop = new stdClass();
            $drop->questionid = $formdata->id;
            $drop->no = $dropno + 1;
            $drop->xleft = $formdata->drops[$dropno]['xleft'];
            $drop->ytop = $formdata->drops[$dropno]['ytop'];
            $drop->choice = $formdata->drops[$dropno]['choice'];
            $drop->label = $formdata->drops[$dropno]['droplabel'];

            $DB->insert_record('qtype_ddimageortext_drops', $drop);
        }

        //an array of drag no -> drag id
        $olddragids = $DB->get_records_menu('qtype_ddimageortext_drags',
                                    array('questionid' => $formdata->id),
                                    '', 'no, id');
        foreach (array_keys($formdata->drags) as $dragno) {
            $info = file_get_draft_area_info($formdata->dragitem[$dragno]);
            if ($info['filecount'] > 0 || !empty($formdata->drags[$dragno]['draglabel'])) {
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
                    $DB->update_record('qtype_ddimageortext_drags', $drag);
                } else {
                    $drag->id = $DB->insert_record('qtype_ddimageortext_drags', $drag);
                }

                if ($formdata->dragitemtype[$dragno] == 'image') {
                    self::constrain_image_size_in_draft_area($draftitemid,
                                        QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXWIDTH,
                                        QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXHEIGHT);
                    file_save_draft_area_files($draftitemid, $formdata->context->id,
                                        'qtype_ddimageortext', 'dragimage', $drag->id,
                                        array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
                } else {
                    //delete any existing files for draggable text item type
                    $fs = get_file_storage();
                    $fs->delete_area_files($formdata->context->id, 'qtype_ddimageortext',
                                                                'dragimage', $drag->id);
                }

            }

        }
        if (!empty($olddragids)) {
            list($sql, $params) = $DB->get_in_or_equal(array_values($olddragids));
            $DB->delete_records_select('qtype_ddimageortext_drags', "id $sql", $params);
        }

        self::constrain_image_size_in_draft_area($formdata->bgimage,
                                                    QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXWIDTH,
                                                    QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXHEIGHT);
        file_save_draft_area_files($formdata->bgimage, $formdata->context->id,
                                    'qtype_ddimageortext', 'bgimage', $formdata->id,
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
                        $quality = null;
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
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs->move_area_files_to_new_context($oldcontextid,
                                    $newcontextid, 'qtype_ddimageortext', 'bgimage', $questionid);
        $dragids = $DB->get_records_menu('qtype_ddimageortext_drags',
                                                array('questionid' => $questionid), 'id', 'id,1');
        foreach ($dragids as $dragid => $notused) {
            $fs->move_area_files_to_new_context($oldcontextid,
                                    $newcontextid, 'qtype_ddimageortext', 'dragimage', $dragid);
        }

        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * Delete all the files belonging to this question.
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */

    protected function delete_files($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);

        $dragids = $DB->get_records_menu('qtype_ddimageortext_drags',
                                                array('questionid' => $questionid), 'id', 'id,1');
        foreach ($dragids as $dragid => $notused) {
            $fs->delete_area_files($contextid, 'qtype_ddimageortext', 'dragimage', $dragid);
        }

        $this->delete_files_in_combined_feedback($questionid, $contextid);
    }

    public function export_to_xml($question, $format, $extra = null) {
        $fs = get_file_storage();
        $contextid = $question->contextid;
        $output = '';

        if ($question->options->shuffleanswers) {
            $output .= "    <shuffleanswers/>\n";
        }
        $output .= $format->write_combined_feedback($question->options);
        $output .= $format->write_hints($question);
        $files = $fs->get_area_files($contextid, 'qtype_ddimageortext', 'bgimage', $question->id);
        $output .= "    ".$this->write_files($files, 2)."\n";;

        foreach ($question->options->drags as $drag) {
            $files =
                    $fs->get_area_files($contextid, 'qtype_ddimageortext', 'dragimage', $drag->id);
            $output .= "    <drag>\n";
            $output .= "      <no>{$drag->no}</no>\n";
            $output .= $format->writetext($drag->label, 3)."\n";
            $output .= "      <draggroup>{$drag->draggroup}</draggroup>\n";
            if ($drag->infinite) {
                $output .= "      <infinite/>\n";
            }
            $output .= $this->write_files($files, 3);
            $output .= "    </drag>\n";
        }
        foreach ($question->options->drops as $drop) {
            $output .= "    <drop>\n";
            $output .= $format->writetext($drop->label, 3);
            $output .= "      <no>{$drop->no}</no>\n";
            $output .= "      <choice>{$drop->choice}</choice>\n";
            $output .= "      <xleft>{$drop->xleft}</xleft>\n";
            $output .= "      <ytop>{$drop->ytop}</ytop>\n";
            $output .= "    </drop>\n";
        }

        return $output;
    }

    public function import_from_xml($data, $question, $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'ddimageortext') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'ddimageortext';

        $question->shuffleanswers = array_key_exists('shuffleanswers',
                                                    $format->getpath($data, array('#'), array()));

        $filexml = $format->getpath($data, array('#', 'file'), array());
        $question->bgimage = $this->import_files_to_draft_file_area($format, $filexml);
        $drags = $data['#']['drag'];
        $question->drags = array();

        foreach ($drags as $dragxml) {
            $dragno = $format->getpath($dragxml, array('#', 'no', 0, '#'), 0);
            $dragindex = $dragno -1;
            $question->drags[$dragindex] = array();
            $question->drags[$dragindex]['draglabel'] =
                        $format->getpath($dragxml, array('#', 'text', 0, '#'), '', true);
            $question->drags[$dragindex]['infinite'] = array_key_exists('infinite', $dragxml['#']);
            $question->drags[$dragindex]['draggroup'] =
                        $format->getpath($dragxml, array('#', 'draggroup', 0, '#'), 1);
            $filexml = $format->getpath($dragxml, array('#', 'file'), array());
            $question->dragitem[$dragindex] =
                                        $this->import_files_to_draft_file_area($format, $filexml);
            if (count($filexml)) {
                $question->dragitemtype[$dragindex] = 'image';
            } else {
                $question->dragitemtype[$dragindex] = 'word';
            }
        }

        $drops = $data['#']['drop'];
        $question->drops = array();
        foreach ($drops as $dropxml) {
            $dropno = $format->getpath($dropxml, array('#', 'no', 0, '#'), 0);
            $dropindex = $dropno -1;
            $question->drops[$dropindex] = array();
            $question->drops[$dropindex]['choice'] =
                        $format->getpath($dropxml, array('#', 'choice', 0, '#'), 0);
            $question->drops[$dropindex]['droplabel'] =
                        $format->getpath($dropxml, array('#', 'text', 0, '#'), '', true);
            $question->drops[$dropindex]['xleft'] =
                        $format->getpath($dropxml, array('#', 'xleft', 0, '#'), '');
            $question->drops[$dropindex]['ytop'] =
                        $format->getpath($dropxml, array('#', 'ytop', 0, '#'), '');
        }

        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true);

        return $question;
    }


    /**
     * Create a draft files area, import files into it and return the draft item id.
     * @param qformat_xml $format
     * @param array $xml an array of <file> nodes from the the parsed XML.
     * @return integer draftitemid
     */
    public function import_files_to_draft_file_area($format, $xml) {
        global $USER;
        $fs = get_file_storage();
        $files = $format->import_files($xml);
        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
        $draftitemid = file_get_unused_draft_itemid();
        foreach ($files as $file) {
            $record = new stdClass();
            $record->contextid = $usercontext->id;
            $record->component = 'user';
            $record->filearea  = 'draft';
            $record->itemid    = $draftitemid;
            $record->filename  = $file->name;
            $record->filepath  = '/';
            $fs->create_file_from_string($record, $this->decode_file($file));
        }
        return $draftitemid;
    }

    /**
     * Convert files into text output in the given format.
     * This method is copied from qformat_default as a quick fix, as the method there is
     * protected.
     * @param array
     * @param string encoding method
     * @return string $string
     */
    public function write_files($files, $indent) {
        if (empty($files)) {
            return '';
        }
        $string = '';
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }
            $string .= str_repeat('  ', $indent);
            $string .= '<file name="' . $file->get_filename() . '" encoding="base64">';
            $string .= base64_encode($file->get_content());
            $string .= "</file>\n";
        }
        return $string;
    }

    public function get_possible_responses($questiondata) {
        $question = $this->make_question($questiondata);

        $parts = array();
        foreach ($question->places as $placeno => $place) {
            $group = $place->group;
            $choices = array();

            foreach ($question->choices[$group] as $i => $choice) {
                $summarisechoice = $choice->summarise();

                $correct = $question->rightchoices[$placeno] == $i;
                $choices[$choice->no] = new question_possible_response(
                                                    $summarisechoice,
                                                    $correct?1:0);
            }
            $choices[null] = question_possible_response::no_response();

            $parts[$placeno] = $choices;
        }

        return $parts;
    }

    public function get_random_guess_score($questiondata) {
        $question = $this->make_question($questiondata);
        return $question->get_random_guess_score();
    }

}
