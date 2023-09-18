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
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/ddimageortext/questiontypebase.php');

/**
 * Question hint for ddmarker.
 *
 * An extension of {@link question_hint} for questions like match and multiple
 * choice with multile answers, where there are options for whether to show the
 * number of parts right at each stage, and to reset the wrong parts.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_hint_ddmarker extends question_hint_with_parts {

    public $statewhichincorrect;

    /**
     * Constructor.
     * @param int the hint id from the database.
     * @param string $hint The hint text
     * @param int the corresponding text FORMAT_... type.
     * @param bool $shownumcorrect whether the number of right parts should be shown
     * @param bool $clearwrong whether the wrong parts should be reset.
     */
    public function __construct($id, $hint, $hintformat, $shownumcorrect,
                                                            $clearwrong, $statewhichincorrect) {
        parent::__construct($id, $hint, $hintformat, $shownumcorrect, $clearwrong);
        $this->statewhichincorrect = $statewhichincorrect;
    }

    /**
     * Create a basic hint from a row loaded from the question_hints table in the database.
     * @param object $row with property options as well as hint, shownumcorrect and clearwrong set.
     * @return question_hint_ddmarker
     */
    public static function load_from_record($row) {
        return new question_hint_ddmarker($row->id, $row->hint, $row->hintformat,
                $row->shownumcorrect, $row->clearwrong, $row->options);
    }
}



/**
 * The drag-and-drop markers question type class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker extends qtype_ddtoimage_base {

    public function save_defaults_for_new_questions(stdClass $fromform): void {
        parent::save_defaults_for_new_questions($fromform);
        $this->set_default_value('showmisplaced', $fromform->showmisplaced);
        $this->set_default_value('shuffleanswers', $fromform->shuffleanswers);
    }

    public function save_question_options($formdata) {
        global $DB, $USER;
        $context = $formdata->context;

        $options = $DB->get_record('qtype_ddmarker', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('qtype_ddmarker', $options);
        }

        $options->shuffleanswers = !empty($formdata->shuffleanswers);
        $options->showmisplaced = !empty($formdata->showmisplaced);
        $options = $this->save_combined_feedback_helper($options, $formdata, $context, true);
        $this->save_hints($formdata, true);
        $DB->update_record('qtype_ddmarker', $options);
        $DB->delete_records('qtype_ddmarker_drops', array('questionid' => $formdata->id));
        foreach (array_keys($formdata->drops) as $dropno) {
            if ($formdata->drops[$dropno]['choice'] == 0) {
                continue;
            }
            $drop = new stdClass();
            $drop->questionid = $formdata->id;
            $drop->no = $dropno + 1;
            $drop->shape = $formdata->drops[$dropno]['shape'];
            $drop->coords = $formdata->drops[$dropno]['coords'];
            $drop->choice = $formdata->drops[$dropno]['choice'];

            $DB->insert_record('qtype_ddmarker_drops', $drop);
        }

        // An array of drag no -> drag id.
        $olddragids = $DB->get_records_menu('qtype_ddmarker_drags',
                                    array('questionid' => $formdata->id),
                                    '', 'no, id');
        foreach (array_keys($formdata->drags) as $dragno) {
            if ($formdata->drags[$dragno]['label'] !== '') {
                $drag = new stdClass();
                $drag->questionid = $formdata->id;
                $drag->no = $dragno + 1;
                if ($formdata->drags[$dragno]['noofdrags'] == 0) {
                    $drag->infinite = 1;
                    $drag->noofdrags = 1;
                } else {
                    $drag->infinite = 0;
                    $drag->noofdrags = $formdata->drags[$dragno]['noofdrags'];
                }
                $drag->label = $formdata->drags[$dragno]['label'];

                if (isset($olddragids[$dragno + 1])) {
                    $drag->id = $olddragids[$dragno + 1];
                    unset($olddragids[$dragno + 1]);
                    $DB->update_record('qtype_ddmarker_drags', $drag);
                } else {
                    $drag->id = $DB->insert_record('qtype_ddmarker_drags', $drag);
                }
            }
        }

        if (!empty($olddragids)) {
            list($sql, $params) = $DB->get_in_or_equal(array_values($olddragids));
            $DB->delete_records_select('qtype_ddmarker_drags', "id $sql", $params);
        }
        file_save_draft_area_files($formdata->bgimage, $formdata->context->id,
                                    'qtype_ddmarker', 'bgimage', $formdata->id,
                                    array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
    }

    public function save_hints($formdata, $withparts = false) {
        global $DB;
        $context = $formdata->context;

        $oldhints = $DB->get_records('question_hints',
                array('questionid' => $formdata->id), 'id ASC');

        if (!empty($formdata->hint)) {
            $numhints = max(array_keys($formdata->hint)) + 1;
        } else {
            $numhints = 0;
        }

        if ($withparts) {
            if (!empty($formdata->hintclearwrong)) {
                $numclears = max(array_keys($formdata->hintclearwrong)) + 1;
            } else {
                $numclears = 0;
            }
            if (!empty($formdata->hintshownumcorrect)) {
                $numshows = max(array_keys($formdata->hintshownumcorrect)) + 1;
            } else {
                $numshows = 0;
            }
            $numhints = max($numhints, $numclears, $numshows);
        }

        for ($i = 0; $i < $numhints; $i += 1) {
            if (html_is_blank($formdata->hint[$i]['text'])) {
                $formdata->hint[$i]['text'] = '';
            }

            if ($withparts) {
                $clearwrong = !empty($formdata->hintclearwrong[$i]);
                $shownumcorrect = !empty($formdata->hintshownumcorrect[$i]);
                $statewhichincorrect = !empty($formdata->hintoptions[$i]);
            }

            if (empty($formdata->hint[$i]['text']) && empty($clearwrong) &&
                    empty($shownumcorrect) && empty($statewhichincorrect)) {
                continue;
            }

            // Update an existing hint if possible.
            $hint = array_shift($oldhints);
            if (!$hint) {
                $hint = new stdClass();
                $hint->questionid = $formdata->id;
                $hint->hint = '';
                $hint->id = $DB->insert_record('question_hints', $hint);
            }

            $hint->hint = $this->import_or_save_files($formdata->hint[$i],
                    $context, 'question', 'hint', $hint->id);
            $hint->hintformat = $formdata->hint[$i]['format'];
            if ($withparts) {
                $hint->clearwrong = $clearwrong;
                $hint->shownumcorrect = $shownumcorrect;
                $hint->options = $statewhichincorrect;
            }
            $DB->update_record('question_hints', $hint);
        }

        // Delete any remaining old hints.
        $fs = get_file_storage();
        foreach ($oldhints as $oldhint) {
            $fs->delete_area_files($context->id, 'question', 'hint', $oldhint->id);
            $DB->delete_records('question_hints', array('id' => $oldhint->id));
        }
    }

    protected function make_hint($hint) {
        return question_hint_ddmarker::load_from_record($hint);
    }
    protected function make_choice($dragdata) {
        return new qtype_ddmarker_drag_item($dragdata->label, $dragdata->no, $dragdata->infinite, $dragdata->noofdrags);
    }

    protected function make_place($dropdata) {
        return new qtype_ddmarker_drop_zone($dropdata->no, $dropdata->shape, $dropdata->coords);
    }

    protected function initialise_combined_feedback(question_definition $question,
                                                                $questiondata, $withparts = false) {
        parent::initialise_combined_feedback($question, $questiondata, $withparts);
        $question->showmisplaced = $questiondata->options->showmisplaced;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs->move_area_files_to_new_context($oldcontextid,
                                    $newcontextid, 'qtype_ddmarker', 'bgimage', $questionid);

        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
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

        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        $fs = get_file_storage();
        $contextid = $question->contextid;
        $output = '';

        if ($question->options->shuffleanswers) {
            $output .= "    <shuffleanswers/>\n";
        }
        if ($question->options->showmisplaced) {
            $output .= "    <showmisplaced/>\n";
        }
        $output .= $format->write_combined_feedback($question->options,
                                                    $question->id,
                                                    $question->contextid);
        $files = $fs->get_area_files($contextid, 'qtype_ddmarker', 'bgimage', $question->id);
        $output .= "    ".$this->write_files($files, 2)."\n";;

        foreach ($question->options->drags as $drag) {
            $files =
                    $fs->get_area_files($contextid, 'qtype_ddmarker', 'dragimage', $drag->id);
            $output .= "    <drag>\n";
            $output .= "      <no>{$drag->no}</no>\n";
            $output .= $format->writetext($drag->label, 3);
            if ($drag->infinite) {
                $output .= "      <infinite/>\n";
            }
            $output .= "      <noofdrags>{$drag->noofdrags}</noofdrags>\n";
            $output .= "    </drag>\n";
        }
        foreach ($question->options->drops as $drop) {
            $output .= "    <drop>\n";
            $output .= "      <no>{$drop->no}</no>\n";
            $output .= "      <shape>{$drop->shape}</shape>\n";
            $output .= "      <coords>{$drop->coords}</coords>\n";
            $output .= "      <choice>{$drop->choice}</choice>\n";
            $output .= "    </drop>\n";
        }

        return $output;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'ddmarker') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'ddmarker';

        $question->shuffleanswers = array_key_exists('shuffleanswers',
                                                    $format->getpath($data, array('#'), array()));
        $question->showmisplaced = array_key_exists('showmisplaced',
                                                    $format->getpath($data, array('#'), array()));

        $filexml = $format->getpath($data, array('#', 'file'), array());
        $question->bgimage = $format->import_files_as_draft($filexml);
        $drags = $data['#']['drag'];
        $question->drags = array();

        foreach ($drags as $dragxml) {
            $dragno = $format->getpath($dragxml, array('#', 'no', 0, '#'), 0);
            $dragindex = $dragno - 1;
            $question->drags[$dragindex] = array();
            $question->drags[$dragindex]['label'] =
                        $format->getpath($dragxml, array('#', 'text', 0, '#'), '', true);
            if (array_key_exists('infinite', $dragxml['#'])) {
                $question->drags[$dragindex]['noofdrags'] = 0; // Means infinite in the form.
            } else {
                // Defaults to 1 if 'noofdrags' not set.
                $question->drags[$dragindex]['noofdrags'] = $format->getpath($dragxml, array('#', 'noofdrags', 0, '#'), 1);
            }
        }

        $drops = $data['#']['drop'];
        $question->drops = array();
        foreach ($drops as $dropxml) {
            $dropno = $format->getpath($dropxml, array('#', 'no', 0, '#'), 0);
            $dropindex = $dropno - 1;
            $question->drops[$dropindex] = array();
            $question->drops[$dropindex]['choice'] =
                        $format->getpath($dropxml, array('#', 'choice', 0, '#'), 0);
            $question->drops[$dropindex]['shape'] =
                        $format->getpath($dropxml, array('#', 'shape', 0, '#'), '');
            $question->drops[$dropindex]['coords'] =
                        $format->getpath($dropxml, array('#', 'coords', 0, '#'), '');
        }

        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true, true,
                $format->get_format($question->questiontextformat));

        return $question;
    }

    public function get_random_guess_score($questiondata) {
        return null;
    }

}
