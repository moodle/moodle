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

defined('MOODLE_INTERNAL') || die();

// @codingStandardsIgnoreLine
require_once($CFG->dirroot . '/question/type/wq/config.php');
require_once($CFG->dirroot . '/question/type/wq/quizzes/quizzes.php');

class qtype_wq extends question_type {

    protected $base;

    public function __construct(question_type $base = null) {
        $this->base = $base;
    }

    public function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/wq/edit_wq_form.php');
        $wform = $this->base->create_editing_form($submiturl, $question, $category, $contexts, $formeditable);
        return new qtype_wq_edit_form($wform, $submiturl, $question, $category, $contexts, $formeditable);
    }

    public function save_question_options($question) {
        $this->save_question_options_impl($question, true);
    }

    public function save_question_options_impl($question, $callbase) {
        global $DB;
        // We don't save another xml if we are in a cloze subquestion.
        if (empty($question->parent)) {
            $wiris = $DB->get_record('qtype_wq', array('question' => $question->id));
            if (empty($wiris->id)) {
                $wiris = new stdClass();
                $wiris->question = $question->id;
                $wiris->xml = $question->wirisquestion;
                $wiris->hash = '';
                $wiris->options = '';
                $wiris->id = $DB->insert_record('qtype_wq', $wiris);
            } else {
                $wiris->xml = $question->wirisquestion;
                $wiris->hash = '';
                $wiris->options = '';
                $DB->update_record('qtype_wq', $wiris);
            }
        }
        // Save question type options after wiris XML becaus if it fails we at
        // least have saved the Wiris part (relevant in multianswer case).
        if ($callbase) {
            return $this->base->save_question_options($question);
        }
    }

    public function delete_question($questionid, $contextid) {
        global $DB;

        $this->base->delete_question($questionid, $contextid);
        $DB->delete_records('qtype_wq', array('question' => $questionid));
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;
        if ($this->base->get_question_options($question) === false) {
            return false;
        }
        // Load question XML from DB.
        if (empty($question->parent)) {
            $record = $DB->get_record('qtype_wq', array('question' => $question->id), 'xml,options');
            if ($record !== false) {
                $question->options->wirisquestion = $record->xml;
                $question->options->wirisoptions = $record->options;
            } else {
                $OUTPUT->notification(get_string('failedtoloadwirisquizzesfromxml', 'qtype_wq') . ' ' . $question->id . '.');
                return false;
            }
        }
        return true;
    }

    protected function make_question_instance($questiondata) {
        question_bank::load_question_definition_classes($this->name());
        $basequestion = $this->base->make_question_instance($questiondata);
        $class = 'qtype_' . $this->name() . '_question';
        return new $class($basequestion);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        global $CFG;

        $this->base->initialise_question_instance($question->base, $questiondata);

        /** @var qtype_wq_question $question */
        $question->id = &$question->base->id;
        $question->idnumber = &$question->base->idnumber;
        $question->category = &$question->base->category;
        $question->contextid = &$question->base->contextid;
        $question->parent = &$question->base->parent;
        // Fix question type.
        $question->qtype = $this;
        $question->name = &$question->base->name;
        $question->questiontext = &$question->base->questiontext;
        $question->questiontextformat = &$question->base->questiontextformat;
        $question->generalfeedback = &$question->base->generalfeedback;
        $question->generalfeedbackformat = &$question->base->generalfeedbackformat;
        $question->defaultmark = &$question->base->defaultmark;
        $question->length = &$question->base->length;
        $question->penalty = &$question->base->penalty;
        $question->stamp = &$question->base->stamp;
        $question->version = &$question->base->version;
        if ($CFG->version >= 2022041900 /* v4.0.0 */) {
            $question->status = &$question->base->status;
        } else {
            $question->hidden = &$question->base->hidden;
        }
        $question->timecreated = &$question->base->timecreated;
        $question->timemodified = &$question->base->timemodified;
        $question->createdby = &$question->base->createdby;
        $question->modifiedby = &$question->base->modifiedby;
        $question->hints = &$question->base->hints;
        $question->questionbankentryid = &$question->base->questionbankentryid;

        // Load question xml into Wiris Quizzes API question object.
        if (empty($question->parent)) {
            $builder = com_wiris_quizzes_api_Quizzes::getInstance();
            if (isset($questiondata->options->wirisquestion)) {
                $question->wirisquestion = $builder->readQuestion($questiondata->options->wirisquestion);
            } else {
                $question->wirisquestion = $builder->newQuestion();
                $question->corrupt = true;
            }
        }
    }
    // This method has to be overriden in each real question.
    public function menu_name() {
        // Include JavaScript Hack to modify question chooser.
        global $CFG;
        global $PAGE;
        if ($CFG->version < 2014051200) {
            // Backwards compatibility.
            $PAGE->requires->js('/question/type/wq/js/display.js', false);
        } else if ($CFG->version >= 2022041900) {
            // Moodle 4.0.2 and up.
            $PAGE->requires->yui_module('moodle-qtype_wq-question_chooser_qbank', 'M.qtype_wq.question_chooser.init', array()); // @codingStandardsIgnoreLine
        } else {
            // Moodle 3.X.
            $PAGE->requires->yui_module('moodle-qtype_wq-question_chooser', 'M.qtype_wq.question_chooser.init');
        }

        return $this->local_name();
    }

    public function display_question_editing_page($mform, $question, $wizardnow) {
        // This method is used to load tiny_mce.js before quizzes.js.
        parent::display_question_editing_page($mform, $question, $wizardnow);
        global $PAGE;
        $PAGE->requires->js('/question/type/wq/quizzes/service.php?name=quizzes.js&service=resource');
    }

    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $DB;
        $xml = $DB->get_record('qtype_wq', array('question' => $question->id), 'xml')->xml;

        $expout = "    <wirisquestion>\n";
        $expout .= htmlspecialchars($xml, ENT_COMPAT, "UTF-8");
        $expout .= "    </wirisquestion>\n";

        return $expout;
    }

    public function extra_question_fields() {
        return $this->base->extra_question_fields();
    }

    public function response_file_areas() {
        if ($this->base != null) {
            return $this->base->response_file_areas();
        }
        return array();
    }

    public function wrsqz_mathml_decode($input) {
        // @codingStandardsIgnoreLine
        $from = array('«', '»', '¨', '§', '`');
        $to = array('<', '>', '"', '&', '\'');
        $r = str_replace($from, $to, $input);
        return $this->decode_html_entities($r);
    }

    protected function wrsqz_adapttext($text) {
        $n0 = 0;
        $n1 = stripos($text, '«math');
        if ($n1 === false) {
            return $text;
        }
        $output = '';
        while ($n1 !== false) {
            $output .= substr($text, $n0, $n1 - $n0);
            $n0 = $n1;
            $n1 = stripos($text, '«/math»', $n0) + 8;
            $innertext = substr($text, $n0, $n1 - $n0);
            $innertext = str_replace('&quot;', '¨', $innertext);
            $output .= $innertext;
            $n0 = $n1;
            $n1 = stripos($text, '«math', $n0);
        }
        $output .= substr($text, $n0);
        return $output;
    }

    protected function wrsqz_get_cas_for_computations($data) {

        $wrap = com_wiris_system_CallWrapper::getInstance();

        $wirisquestion = '';
        if (isset($data['#']['wirisoptions'][0]['#']['wirisCASForComputations'])) {
            if ($data['#']['wirisoptions'][0]['#']['wirisCASForComputations'][0]['#'] == 1) {
                // @codingStandardsIgnoreStart
                $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS . '">';
                $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_ADD;
                // @codingStandardsIgnoreEnd
                $wirisquestion .= '</data>';
            } else if ($data['#']['wirisoptions'][0]['#']['wirisCASForComputations'][0]['#'] == 2) {
                // @codingStandardsIgnoreStart
                $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS . '">';
                $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_REPLACE;
                // @codingStandardsIgnoreEnd
                $wirisquestion .= '</data>';
            }
        } else {
            // @codingStandardsIgnoreStart
            $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS . '">';
            $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_SHOW_CAS_FALSE;
            // @codingStandardsIgnoreEnd
            $wirisquestion .= '</data>';
        }
        return $wirisquestion;
    }

    protected function wrsqz_hidden_initial_cas_value($data) {

        $wrap = com_wiris_system_CallWrapper::getInstance();

        $wirisquestion = '';
        if (isset($data['#']['wirisoptions'][0]['#']['hiddenInitialCASValue'])) {
            // @codingStandardsIgnoreLine
            $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_CAS_INITIAL_SESSION . '">';
            $initialcasvalue = $data['#']['wirisoptions'][0]['#']['hiddenInitialCASValue'][0]['#'];
            $wirisquestion .= htmlspecialchars($this->wrsqz_mathml_decode(trim($initialcasvalue)), ENT_COMPAT, "utf-8");
            $wirisquestion .= '</data>';
        }

        return $wirisquestion;
    }

    protected function decode_html_entities($xml) {
        $htmlentitiestable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, 'UTF-8');
        $xmlentitiestable = get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT, 'UTF-8');
        $entitiestable = array_diff($htmlentitiestable, $xmlentitiestable);
        $decodetable = array_flip($entitiestable);
        $xml = str_replace(array_keys($decodetable), array_values($decodetable), $xml);
        return $xml;
    }

    public function get_possible_responses($questiondata) {
        return $this->base->get_possible_responses($questiondata);
    }
}
