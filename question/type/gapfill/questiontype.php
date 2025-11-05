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
 * The question type class for the gapfill question type.
 *
 * @package    qtype_gapfill
 * @copyright  2018 Marcus Green
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');

/**
 *
 * The gapfill question class
 *
 * Load from database, and initialise class
 * A "fill in the gaps" cloze style question type
 * @package    qtype_gapfill
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapfill extends question_type {

    /**
     * Whether the quiz statistics report can analyse
     * all the student responses. See questiontypebase for more
     *
     * @return bool
     */
    public function can_analyse_responses() {
          return false;
    }
    /**
     * data used by export_to_xml (among other things possibly
     * @return array
     */
    public function extra_question_fields() {
        return ['question_gapfill', 'answerdisplay', 'delimitchars', 'casesensitive',
            'noduplicates', 'disableregex', 'fixedgapsize', 'optionsaftertext', 'letterhints', 'singleuse'];
    }


    /**
     * Called during question editing
     *
     * @param stdClass $question
     */
    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('question_gapfill', array('question' => $question->id), '*', MUST_EXIST);
        $question->options->itemsettings = $this->get_itemsettings($question);
        parent::get_question_options($question);
    }


    /**
     * called when previewing or at runtime in a quiz
     *
     * @param question_definition $question
     * @param stdClass $questiondata
     * @param boolean $forceplaintextanswers
     */
    protected function initialise_question_answers(question_definition $question, $questiondata, $forceplaintextanswers = true) {
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        // Remove html comments as they can contain delimiters, e.g. <!--[if !supportLists] .
        $question->questiontext = preg_replace('/<!--(.|\s)*?-->/', '', $question->questiontext);

        foreach ($questiondata->options->answers as $a) {
            if (strstr($a->fraction, '1') == false) {
                /* if this is a wronganswer/distractor strip any
                 * backslashes, this allows escaped backslashes to
                 * be used i.e. \, and not displayed in the draggable
                 * area
                 */
                $a->answer = stripslashes($a->answer);
            }
            if (!in_array($a->answer, $question->allanswers, true)) {
                array_push($question->allanswers, $a->answer);
            }
            /* answer in this context means correct answers, i.e. where
             * fraction contains a 1 */
            if (strpos($a->fraction, '1') !== false) {
                $question->answers[$a->id] = new question_answer($a->id, $a->answer, $a->fraction,
                        $a->feedback, $a->feedbackformat);
                $question->gapcount++;
                if (!$forceplaintextanswers) {
                    $question->answers[$a->id]->answerformat = $a->answerformat;
                }
            }
        }
    }

    /**
     * Get settings e.g. feedback for correct and incorrect responses
     *
     * @param qtype_gapfill_question $question
     * @return string (json encoded string)
     */
    public function get_itemsettings($question) {
        global $DB;
        $itemsettings = json_encode($DB->get_records('question_gapfill_settings', array('question' => $question->id)));
        return $itemsettings;
    }

    /**
     * Called when previewing a question or when displayed in a quiz
     *  (not from within the editing form)
     *
     * @param question_definition $question
     * @param stdClass $questiondata
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata, true);
        $this->initialise_combined_feedback($question, $questiondata);
        $question->itemsettings = $this->get_itemsettings($question);
        $question->places = array();
        $counter = 1;
        $question->maxgapsize = 0;
        foreach ($questiondata->options->answers as $choicedata) {
            /* find the width of the biggest gap */
            $len = $question->get_size($choicedata->answer);
            if ($len > $question->maxgapsize) {
                $question->maxgapsize = $len;
            }

            /* fraction contains a 1 */
            if (strpos($choicedata->fraction, '1') !== false) {
                $question->places[$counter] = $choicedata->answer;
                $counter++;
            }
        }

        /* Will put empty places '' where there is no text content.
         * l for left delimiter r for right delimiter
         */
        $l = substr($question->delimitchars, 0, 1);
        $r = substr($question->delimitchars, 1, 1);

        $nongapregex = '/\\' . $l . '.*?\\' . $r . '/';
        $nongaptext = preg_split($nongapregex, $question->questiontext, -1, PREG_SPLIT_DELIM_CAPTURE);
        $i = 0;
        while (!empty($nongaptext)) {
            $question->textfragments[$i] = array_shift($nongaptext);
            $i++;
        }
    }

    /**
     * Sets the default mark as 1* the number of gaps
     * Does not allow setting any other value per space/field at the moment
     * @param stdClass $question
     * @param \stdClass $form
     * @return object
     */
    public function save_question($question, $form) {
        $gaps = $this->get_gaps($form->delimitchars, $form->questiontext['text']);
        /* count the number of gaps
         * this is used to set the maximum
         * value for the whole question. Value for
         * each gap can be only 0 or 1
         */
        $form->defaultmark = count($gaps);
        return parent::save_question($question, $form);
    }

    /**
     * chop the delimit string into a two element array
     * this might be better done on initialisation
     *
     * @param string $delimitchars
     * @return array
     */
    public static function get_delimit_array($delimitchars) {
        $delimitarray = array();
        $delimitarray["l"] = substr($delimitchars, 0, 1);
        $delimitarray["r"] = substr($delimitchars, 1, 1);
        return $delimitarray;
    }

    /**
     * it really does need to be static
     *
     * @param string $delimitchars
     * @param string $questiontext
     * @return array
     */
    public static function get_gaps($delimitchars, $questiontext) {
        /* l for left delimiter r for right delimiter
         * defaults to []
         * e.g. l=[ and r=] where question is
         * The [cat] sat on the [mat]
         */
        $delim = self::get_delimit_array($delimitchars);
        $fieldregex = '/.*?\\' . $delim["l"] . '(.*?)\\' . $delim["r"] . '/';
        $matches = [];
        preg_match_all($fieldregex, $questiontext, $matches);
        return $matches[1];
    }

    /**
     * Save the answers and optionsassociated with this question.
     * @param stdClass $question
     * @return boolean to indicate success or failure.
     **/
    public function save_question_options($question) {
        /* Save the extra data to your database tables from the
          $question object, which has all the post data from editquestion.html */

        // Remove html comments as they can contain delimiters, e.g. <!--[if !supportLists] .
        $question->questiontext = preg_replace('/<!--(.|\s)*?-->/', '', $question->questiontext);

        $gaps = $this->get_gaps($question->delimitchars, $question->questiontext);
        /* answerwords are the text within gaps */
        $answerfields = $this->get_answer_fields($gaps, $question);
        global $DB;

        $context = $question->context;
        // Fetch old answer ids so that we can reuse them.
        $this->update_question_answers($question, $answerfields);

        $options = $DB->get_record('question_gapfill', array('question' => $question->id));
        $this->update_question_gapfill($question, $options, $context);
        $this->update_item_settings($question, 'question_gapfill_settings');

        $this->save_hints($question, true);
        return true;
    }


    /**
     * Writes to the database, runs from question editing form
     *
     * @param stdClass $question
     * @param stdClass $options
     * @param context_course_object $context
     */
    public function update_question_gapfill($question, $options, $context) {
        global $DB;
        $options = $DB->get_record('question_gapfill', array('question' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->question = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->answerdisplay = '';
            $options->delimitchars = '';
            $options->casesensitive = '';
            $options->noduplicates = '';
            $options->disableregex = '';
            $options->fixedgapsize = '';
            $options->optionsaftertext = '';
            $options->letterhints = '';
            $options->singleuse = '';
            $options->id = $DB->insert_record('question_gapfill', $options);
        }

        $options->delimitchars = $question->delimitchars;
        $options->answerdisplay = $question->answerdisplay;
        $options->casesensitive = $question->casesensitive;
        $options->noduplicates = $question->noduplicates;
        $options->disableregex = $question->disableregex;
        $options->fixedgapsize = $question->fixedgapsize;
        $options->optionsaftertext = $question->optionsaftertext;
        $options->letterhints = $question->letterhints;
        $options->singleuse = $question->singleuse;

        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('question_gapfill', $options);
    }

    /**
     * Write to the database during editing
     *
     * @param stdClass $question
     * @param array $answerfields
     */
    public function update_question_answers($question, array $answerfields) {
        global $DB;
        $oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC');
        // Insert all the new answers.
        foreach ($answerfields as $field) {
            // Save the true answer - update an existing answer if possible.
            if ($answer = array_shift($oldanswers)) {
                $answer->question = $question->id;
                $answer->answer = $field['value'];
                $answer->feedback = '';
                $answer->fraction = $field['fraction'];
                $DB->update_record('question_answers', $answer);
            } else {
                // Insert a blank record.
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = $field['value'];
                $answer->feedback = '';
                $answer->correctfeedback = '';
                $answer->partiallycorrectfeedback = '';
                $answer->incorrectfeedback = '';
                $answer->fraction = $field['fraction'];
                $answer->id = $DB->insert_record('question_answers', $answer);
            }
        }
        // Delete old answer records.
        foreach ($oldanswers as $oa) {
            $DB->delete_records('question_answers', array('id' => $oa->id));
        }
    }

    /**
     * Set up all the answer fields with respective fraction (mark values)
     * This is used to update the question_answers table. Answerwords has
     * been pulled from within the delimitchars e.g. the cat within [cat]
     * Wronganswers (distractors) has been pulled from a comma delimited edit
     * form field
     *
     * @param array $answerwords
     * @param stdClass $question
     * @return  array
     */
    public function get_answer_fields(array $answerwords, $question) {
        /* this code runs both on saving from a form and from importing and needs
         * improving as it mixes pulling information from the question object which
         * comes from the import and from $question->wronganswers field which
         * comes from the question_editing form.
         */
        $answerfields = array();
        /* this next block runs when importing from xml */
        if (property_exists($question, 'answer')) {
            foreach ($question->answer as $key => $value) {
                if ($question->fraction[$key] == 0) {
                    $answerfields[$key]['value'] = $question->answer[$key];
                    $answerfields[$key]['fraction'] = 0;
                } else {
                    $answerfields[$key]['value'] = $question->answer[$key];
                    $answerfields[$key]['fraction'] = 1;
                }
            }
        }

        /* the rest of this function runs when saving from edit form */
        if (!property_exists($question, 'answer')) {
            foreach ($answerwords as $key => $value) {
                $answerfields[$key]['value'] = $value;
                $answerfields[$key]['fraction'] = 1;
            }
        }
        if (property_exists($question, 'wronganswers')) {
            if ($question->wronganswers['text'] != '') {
                /* split by commas and trim white space */
                $wronganswers = array_map('trim', explode(',', $question->wronganswers['text']));
                $regex = '/(.*?[^\\\\](\\\\\\\\)*?),/';
                $wronganswers = preg_split($regex, $question->wronganswers['text'],
                        -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $wronganswerfields = array();
                foreach ($wronganswers as $key => $word) {
                    $wronganswerfields[$key]['value'] = $word;
                    $wronganswerfields[$key]['fraction'] = 0;
                }
                $answerfields = array_merge($answerfields, $wronganswerfields);
            }
        }
        return $answerfields;
    }

    /**
     * Take the data from the hidden form field or file import and write to the settings table
     * The first/main type of data is per gap feedback. Other data relating to
     * settings for a gap may be added later
     *
     * @param stdClass $question
     * @param string $table
     */
    public function update_item_settings(stdClass $question, $table) {
        global $DB;
        $oldsettings = $DB->get_records($table, array('question' => $question->id));
        $newsettings = [];
        if (isset($question->itemsettings) && (!isset($question->isimport))) {
            $newsettings = json_decode($question->itemsettings, true);
        }
        if (isset($question->itemsettings) && (isset($question->isimport))) {
            $newsettings = $question->itemsettings;
        }
        if (isset($newsettings)) {
            foreach ($newsettings as $set) {
                $setting = new stdClass();
                $setting->question = $question->id;
                $setting->itemid = $set['itemid'];
                $setting->gaptext = $set['gaptext'];
                $setting->correctfeedback = $set['correctfeedback'];
                $setting->incorrectfeedback = $set['incorrectfeedback'];
                $DB->insert_record('question_gapfill_settings', $setting);
            }
        }
        foreach ($oldsettings as $os) {
            $DB->delete_records('question_gapfill_settings', array('id' => $os->id));
        }
    }

    /**
     * Called from within questiontypebase
     *
     * @param  string $hint
     * @return question_hint_with_parts
     */
    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    /**
     * Move all the files belonging to this question from one context to another.
     * @param int $questionid the question being moved.
     * @param int $oldcontextid the context it is moving from.
     * @param int $newcontextid the context it is moving to.
     *
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        /* Thanks to Jean-Michel Vedrine for pointing out the need for this and delete_files function */
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * Delete all the files belonging to this question.Seems the same as in the parent
     * @param int $questionid the question being deleted.
     * @param int $contextid the context the question is in.
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    /**
     * The name of the key column in the foreign table (might have been questionid instead)
     * @return string
     */
    public function questionid_column_name() {
        return 'question';
    }

    /**
     * Create a question from reading in a file in Moodle xml format
     *
     * @param array $data
     * @param stdClass $question (might be an array)
     * @param qformat_xml $format
     * @param stdClass $extra
     * @return boolean
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'gapfill') {
            return false;
        }
        $question = parent::import_from_xml($data, $question, $format, null);
        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));
        $question->isimport = true;
        $question->itemsettings = [];
        if (isset($data['#']['gapsetting'])) {
            foreach ($data['#']['gapsetting'] as $key => $setxml) {
                $question->itemsettings[$key]['gaptext'] = $format->getpath($setxml, array('#', 'gaptext', 0, '#'), 0);
                $question->itemsettings[$key]['question'] = $format->getpath($setxml, array('#', 'question', 0, '#'), '', true);
                $question->itemsettings[$key]['itemid'] = $format->getpath($setxml, array('#', 'itemid', 0, '#'), '', true);
                $question->itemsettings[$key]['correctfeedback'] = $format->getpath($setxml, array('#', 'correctfeedback', 0, '#'),
                        '', true);
                $question->itemsettings[$key]['incorrectfeedback'] = $format->getpath($setxml,
                        array('#', 'incorrectfeedback', 0, '#'), '', true);
            }
        }
        return $question;

    }

    /**
     * Export question to the Moodle XML format
     *
     * @param object $question
     * @param qformat_xml $format
     * @param object $extra
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $CFG;
        $pluginmanager = core_plugin_manager::instance();
        $gapfillinfo = $pluginmanager->get_plugin_info('qtype_gapfill');
        /*convert json into an object */
        $question->options->itemsettings = json_decode($question->options->itemsettings);

        $output = parent::export_to_xml($question, $format);
        $output .= '    <delimitchars>' . $question->options->delimitchars .
                "</delimitchars>\n";
        $output .= '    <answerdisplay>' . $question->options->answerdisplay .
                "</answerdisplay>\n";
        $output .= '    <casesensitive>' . $question->options->casesensitive .
                "</casesensitive>\n";
        $output .= '    <noduplicates>' . $question->options->noduplicates .
                "</noduplicates>\n";
        $output .= '    <disableregex>' . $question->options->disableregex .
                "</disableregex>\n";
        $output .= '    <fixedgapsize>' . $question->options->fixedgapsize .
                "</fixedgapsize>\n";
        $output .= '    <optionsaftertext>' . $question->options->optionsaftertext .
                "</optionsaftertext>\n";
        $output .= '    <letterhints>' . $question->options->letterhints .
                "</letterhints>\n";
        foreach ($question->options->itemsettings as $set) {
            $output .= "      <gapsetting>\n";
            $output .= '        <question>' . $set->question . "</question>\n";
            $output .= '        <gaptext>' . $set->gaptext . "</gaptext>\n";
            $output .= '        <itemid>' . $set->itemid . "</itemid>\n";
            $output .= '        <correctfeedback><![CDATA[' . $set->correctfeedback . "]]></correctfeedback>\n";
            $output .= '        <incorrectfeedback><![CDATA[' . $set->incorrectfeedback . "]]></incorrectfeedback>\n";
            $output .= "     </gapsetting>\n";
        }
        $output .= '    <!-- Gapfill release:'
                . $gapfillinfo->release . ' version:' . $gapfillinfo->versiondisk . ' Moodle version:'
                . $CFG->version . ' release:' . $CFG->release
                . " -->\n";
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }

}
