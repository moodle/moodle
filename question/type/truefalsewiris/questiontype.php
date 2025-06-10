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

require_once($CFG->dirroot . '/question/type/wq/questiontype.php');
require_once($CFG->dirroot . '/question/type/truefalse/questiontype.php');

class qtype_truefalsewiris extends qtype_wq {

    public function __construct() {
        parent::__construct(new qtype_truefalse());
    }

    public function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/truefalsewiris/edit_truefalsewiris_form.php');
        $wform = $this->base->create_editing_form($submiturl, $question, $category, $contexts, $formeditable);
        return new qtype_truefalsewiris_edit_form($wform, $submiturl, $question, $category, $contexts, $formeditable);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->wirisoverrideanswer = $questiondata->options->wirisoptions;
        $question->rightanswer = &$question->base->rightanswer;
        $question->truefeedback = &$question->base->truefeedback;
        $question->falsefeedback = &$question->base->falsefeedback;
        $question->truefeedbackformat = &$question->base->truefeedbackformat;
        $question->falsefeedbackformat = &$question->base->falsefeedbackformat;
        $question->trueanswerid = &$question->base->trueanswerid;
        $question->falseanswerid = &$question->base->falseanswerid;
    }

    public function save_question_options($question) {
        global $DB;

        if (!isset($question->correctanswer)) {
            $question->correctanswer = 0;
        }

        parent::save_question_options($question);

        $wiris = $DB->get_record('qtype_wq', array('question' => $question->id));
        $wiris->options = $question->wirisoverrideanswer;
        $DB->update_record('qtype_wq', $wiris);

        return true;
    }

    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        global $DB;
        $correctanswer = $DB->get_record('qtype_wq', array('question' => $question->id), 'options')->options;

        // Xml file needs to have only true or false string values, not True,False,tRuE...
        foreach ($question->options->answers as $index => $answer) {
            $answer->answer = strtolower($answer->answer);
        }
        $expout = $format->write_answers($question->options->answers);
        $expout .= parent::export_to_xml($question, $format);
        $expout .= "    <wirisoverrideanswer>\n";
        $expout .= "<![CDATA[" . $correctanswer . "]]>\n";
        $expout .= "    </wirisoverrideanswer>\n";

        return $expout;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        if (isset($question) && $question == 0) {
            return false;
        }
        if (isset($data['#']['wirisquestion']) && substr($data['#']['wirisquestion'][0]['#'], 0, 9) == '«session') {
            // Moodle 1.9.
            $text = $data['#']['questiontext'][0]['#']['text'][0]['#'];
            $text = $this->wrsqz_adapttext($text);
            $data['#']['questiontext'][0]['#']['text'][0]['#'] = $text;
            $qo = $format->import_truefalse($data);
            $qo->qtype = 'truefalsewiris';
            $wirisquestion = '<question><wirisCasSession>';
            $wirisquestionmathmldecode = $this->wrsqz_mathml_decode(trim($data['#']['wirisquestion'][0]['#']));
            $wirisquestion .= htmlspecialchars($wirisquestionmathmldecode, ENT_COMPAT, "UTF-8");
            $wirisquestion .= '</wirisCasSession>';

            if (count($data['#']['wirisoptions'][0]['#']) > 0) {
                $wirisquestion .= '<localData>';
                $wirisquestion .= $this->wrsqz_get_cas_for_computations($data);
                $wirisquestion .= $this->wrsqz_hidden_initial_cas_value($data);
                $wirisquestion .= '</localData>';
            }

            $wirisquestion .= '</question>';
            $qo->wirisquestion = $wirisquestion;
            if (isset($data['#']['wirisoverrideanswer'])) {
                $qo->wirisoverrideanswer = trim($data['#']['wirisoverrideanswer'][0]['#']);
            }
            return $qo;
        } else {
            // Moodle 2.x.
            $qo = $format->import_truefalse($data);
            $qo->qtype = 'truefalsewiris';
            $qo->wirisquestion = trim($data['#']['wirisquestion'][0]['#']);
            if (isset($data['#']['wirisoverrideanswer'])) {
                $qo->wirisoverrideanswer = trim($this->decode_html_entities($data['#']['wirisoverrideanswer'][0]['#']));
            }
            return $qo;
        }
    }
}
