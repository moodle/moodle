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
require_once($CFG->dirroot . '/question/type/multichoice/questiontype.php');


class qtype_multichoicewiris extends qtype_wq {

    public function __construct() {
        parent::__construct(new qtype_multichoice());
    }

    public function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/multichoicewiris/edit_multichoicewiris_form.php');
        $wform = $this->base->create_editing_form($submiturl, $question, $category, $contexts, $formeditable);
        return new qtype_multichoicewiris_edit_form($wform, $submiturl, $question, $category, $contexts, $formeditable);
    }

    public function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        $question->shuffleanswers = &$question->base->shuffleanswers;
        $question->answernumbering = &$question->base->answernumbering;
        $question->layout = &$question->base->layout;
        $question->correctfeedback = &$question->base->correctfeedback;
        $question->correctfeedbackformat = &$question->base->correctfeedbackformat;
        $question->partiallycorrectfeedback = &$question->base->partiallycorrectfeedback;
        $question->partiallycorrectfeedbackformat = &$question->base->partiallycorrectfeedbackformat;
        $question->incorrectfeedback = &$question->base->incorrectfeedback;
        $question->incorrectfeedbackformat = &$question->base->incorrectfeedbackformat;
        $question->answers = &$question->base->answers;
    }

    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $expout = "    <single>" . $format->get_single($question->options->single) .
                "</single>\n";
        $expout .= "    <shuffleanswers>" .
                $format->get_single($question->options->shuffleanswers) .
                "</shuffleanswers>\n";
        $expout .= "    <answernumbering>" . $question->options->answernumbering .
                "</answernumbering>\n";
        $expout .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        $expout .= $format->write_answers($question->options->answers);
        $expout .= parent::export_to_xml($question, $format);
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
            $qo = $format->import_multichoice($data);
            $qo->qtype = 'multichoicewiris';
            $wirisquestion = '<question><wirisCasSession>';
            $wirisquestionmathmldecode = $this->wrsqz_mathml_decode(trim($data['#']['wirisquestion'][0]['#']));
            $wirisquestion .= htmlspecialchars($wirisquestionmathmldecode, ENT_COMPAT, "UTF-8");
            $wirisquestion .= '</wirisCasSession>';

            if (isset($data['#']['wirisoptions']) && count($data['#']['wirisoptions'][0]['#']) > 0) {
                $wirisquestion .= '<localData>';
                $wirisquestion .= $this->wrsqz_get_cas_for_computations($data);
                $wirisquestion .= $this->wrsqz_hidden_initial_cas_value($data);
                $wirisquestion .= '</localData>';
            }

            if (isset($data['#']['wirisoverrideanswer']) && $data['#']['wirisoverrideanswer'][0]['#'] != "") {
                $overrideanswers = explode(';', $data['#']['wirisoverrideanswer'][0]['#']);
                foreach ($overrideanswers as $key => $value) {
                    if (trim($value) != '') {
                        $msg = '<p><strong>' . get_string('warning') . '</strong>:<br />';
                        $msg .= '<em>' . $data['#']['name'][0]['#']['text'][0]['#'] . '</em><br />';
                        $msg .= get_string('multichoicewiris_cantimportoverride', 'qtype_multichoicewiris');
                        $msg .= '</p>';
                        echo $msg;
                    }
                }
            }

            $wirisquestion .= '</question>';
            $qo->wirisquestion = $wirisquestion;
            return $qo;
        } else {
            // Moodle 2.x.
            $qo = $format->import_multichoice($data);
            $qo->qtype = 'multichoicewiris';
            $qo->wirisquestion = trim($this->decode_html_entities($data['#']['wirisquestion'][0]['#']));
            return $qo;
        }
    }
}
