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
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/wq/quizzes/quizzes.php');
require_once($CFG->dirroot . '/question/type/shortanswerwiris/lib.php');

class qtype_shortanswerwiris extends qtype_wq {

    public function __construct() {
        parent::__construct(new qtype_shortanswer());
    }

    public function extra_question_fields() {
        return array('qtype_shortanswer_options', 'usecase');
    }

    public function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/shortanswerwiris/edit_shortanswerwiris_form.php');
        $wform = new qtype_shortanswerwiris_helper_edit_form($submiturl, $question, $category, $contexts, $formeditable);
        return new qtype_shortanswerwiris_edit_form($wform, $submiturl, $question, $category, $contexts, $formeditable);
    }
    public function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->answers = &$question->base->answers;
    }

    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $expout = "    <usecase>{$question->options->usecase}</usecase>\n";
        $expout .= $format->write_answers($question->options->answers);
        $expout .= parent::export_to_xml($question, $format);
        return $expout;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        $wrap = com_wiris_system_CallWrapper::getInstance();

        if (isset($question) && $question == 0) {
            return false;
        }
        if (isset($data['#']['wirisquestion']) && substr($data['#']['wirisquestion'][0]['#'], 0, 9) == '«session') {
            // Import from Moodle 1.x.
            $iscompound = false;

            if (isset($data['#']['wiriseditor'])) {
                $wiriseditor = array();
                parse_str($data['#']['wiriseditor'][0]['#'], $wiriseditor);
                if (isset($wiriseditor['multipleAnswers']) && $wiriseditor['multipleAnswers'] == true) {
                    if (isset($wiriseditor['testFunctionName'])) {
                        $msg = '<p><strong>' . get_string('warning') . '</strong>:<br />';
                        $msg .= '<em>' . $data['#']['name'][0]['#']['text'][0]['#'] . '</em><br />';
                        $msg .= get_string('shortanswerwiris_cantimportcompoundtest', 'qtype_shortanswerwiris');
                        $msg .= '</p>';
                        echo $msg;
                    }
                    $iscompound = true;
                }
            }

            $text = $data['#']['questiontext'][0]['#']['text'][0]['#'];
            $text = $this->wrsqz_adapttext($text);
            $data['#']['questiontext'][0]['#']['text'][0]['#'] = $text;
            $answers = $data['#']['answer'];
            foreach ($answers as $key => $value) {
                $text = $answers[$key]['#']['feedback'][0]['#']['text'][0]['#'];
                $text = $this->wrsqz_adapttext($text);
                $data['#']['answer'][$key]['#']['feedback'][0]['#']['text'][0]['#'] = $text;
                if ($iscompound) {
                    // Compound answers
                    // $originaltext will be used to check if there's a distribution.
                    $originaltext = $answers[$key]['#']['text'][0]['#'];
                    $text = wrsqz_convert_for_compound($originaltext);
                    $data['#']['answer'][$key]['#']['text'][0]['#'] = $text;
                }
            }
            $text = $data['#']['generalfeedback'][0]['#']['text'][0]['#'];
            $text = $this->wrsqz_adapttext($text);
            $data['#']['generalfeedback'][0]['#']['text'][0]['#'] = $text;
            $qo = parent::import_from_xml($data, $question, $format, $extra);
            $wirisquestion = '<question><wirisCasSession>';
            $wirisquestiondecoded = $this->wrsqz_mathml_decode(trim($data['#']['wirisquestion'][0]['#']));
            $wirisquestion .= htmlspecialchars($wirisquestiondecoded, ENT_COMPAT, "UTF-8");
            $wirisquestion .= '</wirisCasSession>';

            $wirisquestion .= '<correctAnswers>';

            foreach ($data['#']['answer'] as $key => $value) {
                $answertext = $value['#']['text'][0]['#'];
                $wirisquestion .= '<correctAnswer type="mathml">';
                if ($iscompound) {
                    $wirisquestion .= htmlspecialchars($answertext, ENT_COMPAT, "UTF-8");
                } else {
                    $wirisquestion .= $answertext;
                }
                $wirisquestion .= '</correctAnswer>';
            }

            $wirisquestion .= '</correctAnswers>';

            if (isset($data['#']['wiriseditor'])) {

                $wiriseditor = array();
                parse_str($data['#']['wiriseditor'][0]['#'], $wiriseditor);

                if (count($wiriseditor) > 0) {
                    // Grade function.
                    $wrap->start();
                    $wirisquestion .= '<assertions>';
                    if (isset($wiriseditor['testFunctionName'])) {
                        foreach ($answers as $key => $value) {
                            // @codingStandardsIgnoreStart
                            $wirisquestion .= '<assertion name="' .
                                    com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION . '" correctAnswer="' . $key . '"/>';
                        }
                            // @codingStandardsIgnoreEnd
                        foreach ($wiriseditor['testFunctionName'] as $key => $value) {
                            // @codingStandardsIgnoreStart
                            $wirisquestion .= '<assertion name="' .
                                    com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION . '" correctAnswer="' . $key . '">';
                            // @codingStandardsIgnoreEnd
                            if (substr($value, 0, 1) == '#') {
                                $value = substr($value, 1);
                            }
                            $wirisquestion .= '<param name="name">' . $value . '</param>';
                            $wirisquestion .= '</assertion>';
                        }
                    } else {
                        foreach ($answers as $key => $value) {
                            // @codingStandardsIgnoreStart
                            $wirisquestion .= '<assertion name="' .
                                    com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION. '" correctAnswer="' . $key . '"/>';
                            $wirisquestion .= '<assertion name="' .
                                    com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC . '" correctAnswer="' . $key . '"/>';
                            // @codingStandardsIgnoreEnd
                        }
                    }
                    $wirisquestion .= '</assertions>';
                    $wrap->stop();

                    // Editor and compound answer.
                    $wirisquestion .= '<localData>';
                    if (!$iscompound) {
                        if (isset($wiriseditor['editor']) && $wiriseditor['editor'] == true) {
                            // @codingStandardsIgnoreStart
                            $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_ANSWER_FIELD_TYPE . '">';
                            $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_INLINE_EDITOR;
                            // @codingStandardsIgnoreEnd
                            $wirisquestion .= '</data>';
                        } else {
                            // @codingStandardsIgnoreStart
                            $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_ANSWER_FIELD_TYPE . '">';
                            $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_TEXT;
                            // @codingStandardsIgnoreEnd
                            $wirisquestion .= '</data>';
                        }
                    } else {
                        // For compound answer set as default Popup editor.
                        // @codingStandardsIgnoreStart
                        $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_ANSWER_FIELD_TYPE . '">';
                        $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_POPUP_EDITOR;
                        // @codingStandardsIgnoreEnd
                        $wirisquestion .= '</data>';

                        // @codingStandardsIgnoreStart
                        $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE . '">';
                        $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_DISTRIBUTE;
                        // @codingStandardsIgnoreEnd
                        $wirisquestion .= '</data>';

                        $distribution = $this->wrsqz_get_distribution($originaltext);
                        // @codingStandardsIgnoreStart
                        $wirisquestion .= '<data name="' .
                                com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE_DISTRIBUTION . '">';
                        // @codingStandardsIgnoreEnd
                        if ($distribution != '') {
                            $wirisquestion .= $distribution;
                        }
                        $wirisquestion .= '</data>';
                    }
                    if (isset($wiriseditor['multipleAnswers']) && $wiriseditor['multipleAnswers'] == true) {
                        // @codingStandardsIgnoreStart
                        $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER . '">';
                        $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE;
                        // @codingStandardsIgnoreEnd
                        $wirisquestion .= '</data>';
                    } else {
                        // @codingStandardsIgnoreStart
                        $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER . '">';
                        $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_FALSE;
                        // @codingStandardsIgnoreEnd
                        $wirisquestion .= '</data>';
                    }
                    if (isset($data['#']['wirisoptions']) && count($data['#']['wirisoptions'][0]['#']) > 0) {
                        $wirisquestion .= $this->wrsqz_get_cas_for_computations($data);
                        $wirisquestion .= $this->wrsqz_hidden_initial_cas_value($data);
                    }

                    $wirisquestion .= '</localData>';
                } else {
                    $wirisquestion .= '<localData>';
                    if (isset($data['#']['wirisoptions']) && count($data['#']['wirisoptions'][0]['#']) > 0) {
                        $wirisquestion .= $this->wrsqz_get_cas_for_computations($data);
                        $wirisquestion .= $this->wrsqz_hidden_initial_cas_value($data);
                    }
                    // @codingStandardsIgnoreStart
                    $wirisquestion .= '<data name="' . com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_ANSWER_FIELD_TYPE . '">';
                    $wirisquestion .= com_wiris_quizzes_api_QuizzesConstants::$ANSWER_FIELD_TYPE_TEXT;
                    // @codingStandardsIgnoreEnd
                    $wirisquestion .= '</data>';
                    $wirisquestion .= '</localData>';
                }
            }
            $wirisquestion .= '</question>';
            $qo->wirisquestion = $wirisquestion;
        } else {
            // Import from Moodle 2.x.
            $qo = $format->import_shortanswer($data);
            $qo->qtype = 'shortanswerwiris';
            $qo->wirisquestion = trim($this->decode_html_entities($data['#']['wirisquestion'][0]['#']));
        }
        return $qo;
    }

    private function wrsqz_get_distribution($text) {
        $distribution = '';
        $text = trim($text);
        $answerarray = explode("#", $text);

        foreach ($answerarray as $key => $value) {
            if (strpos($value, '(')) {
                $value = trim($value);
                $compoundarray = explode(" ", $value);
                $distribution .= $compoundarray[1] . ' ';
            }
        }
        $distribution = str_replace('(', '', $distribution);
        $distribution = str_replace(')', '', $distribution);
        return trim($distribution);
    }

}
