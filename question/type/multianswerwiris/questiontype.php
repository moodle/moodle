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
require_once($CFG->dirroot . '/question/type/multianswer/questiontype.php');

class qtype_multianswerwiris extends qtype_wq {

    public function __construct() {
        parent::__construct(new qtype_multianswer());
    }

    public function save_question_options($question) {
        global $DB;

        parent::save_question_options_impl($question, false);

        $result = new stdClass();

        // This function needs to be able to handle the case where the existing set of wrapped
        // questions does not match the new set of wrapped questions so that some need to be
        // created, some modified and some deleted.
        // Unfortunately the code currently simply overwrites existing ones in sequence. This
        // will make re-marking after a re-ordering of wrapped questions impossible and
        // will also create difficulties if questiontype specific tables reference the id.

        // First we get all the existing wrapped questions.
        $oldwrappedquestions = [];
        if ($oldwrappedids = $DB->get_field('question_multianswer', 'sequence',
                array('question' => $question->id))) {
            $oldwrappedidsarray = explode(',', $oldwrappedids);
            $unorderedquestions = $DB->get_records_list('question', 'id', $oldwrappedidsarray);

            // Keep the order as given in the sequence field.
            foreach ($oldwrappedidsarray as $questionid) {
                if (isset($unorderedquestions[$questionid])) {
                    $oldwrappedquestions[] = $unorderedquestions[$questionid];
                }
            }
        }

        $sequence = array();
        foreach ($question->options->questions as $wrapped) {
            if (!empty($wrapped)) {
                // If we still have some old wrapped question ids, reuse the next of them.

                if (is_array($oldwrappedquestions) &&
                        $oldwrappedquestion = array_shift($oldwrappedquestions)) {
                    $wrapped->id = $oldwrappedquestion->id;
                    if ($oldwrappedquestion->qtype != $wrapped->qtype) {
                        switch ($oldwrappedquestion->qtype) {
                            case 'multichoice':
                                $DB->delete_records('qtype_multichoice_options',
                                        array('questionid' => $oldwrappedquestion->id));
                                break;
                            case 'shortanswer':
                                $DB->delete_records('qtype_shortanswer_options',
                                        array('questionid' => $oldwrappedquestion->id));
                                break;
                            case 'numerical':
                                $DB->delete_records('question_numerical',
                                        array('question' => $oldwrappedquestion->id));
                                break;
                            case 'shortanswerwiris':
                                $DB->delete_records('qtype_wq',
                                        array('question' => $oldwrappedquestion->id));
                                $DB->delete_records('qtype_shortanswer_options',
                                        array('questionid' => $oldwrappedquestion->id));
                                break;
                            case 'multichoicewiris':
                                $DB->delete_records('qtype_wq',
                                        array('question' => $oldwrappedquestion->id));
                                break;
                            default:
                                throw new moodle_exception('qtypenotrecognized',
                                        'qtype_multianswer', '', $oldwrappedquestion->qtype);
                                $wrapped->id = 0;
                        }
                    }
                } else {
                    $wrapped->id = 0;
                }
            }
            $wrapped->name = $question->name;
            $wrapped->parent = $question->id;
            $previousid = $wrapped->id;
            // Save_question strips this extra bit off the category again.
            $wrapped->category = $question->category . ',1';
            $wrapped = question_bank::get_qtype($wrapped->qtype)->save_question(
                    $wrapped, clone($wrapped));
            $sequence[] = $wrapped->id;
            if ($previousid != 0 && $previousid != $wrapped->id) {
                // For some reasons a new question has been created
                // so delete the old one.
                question_delete_question($previousid);
            }
        }

        // Delete redundant wrapped questions.
        if (is_array($oldwrappedquestions) && count($oldwrappedquestions)) {
            foreach ($oldwrappedquestions as $oldwrappedquestion) {
                question_delete_question($oldwrappedquestion->id);
            }
        }

        if (!empty($sequence)) {
            $multianswer = new stdClass();
            $multianswer->question = $question->id;
            $multianswer->sequence = implode(',', $sequence);
            if ($oldid = $DB->get_field('question_multianswer', 'id',
                    array('question' => $question->id))) {
                $multianswer->id = $oldid;
                $DB->update_record('question_multianswer', $multianswer);
            } else {
                $DB->insert_record('question_multianswer', $multianswer);
            }
        }

        $this->save_hints($question, true);
    }

    public function save_question($authorizedquestion, $form) {
        $question = qtype_multianswer_extract_question($form->questiontext);
        $question->qtype = 'multianswerwiris';

        foreach ($question->options->questions as $key => $value) {
            if ($question->options->questions[$key]->qtype != 'numerical') {
                $question->options->questions[$key]->qtype = $value->qtype . 'wiris';
            }
        }

        if (isset($authorizedquestion->id)) {
            $question->id = $authorizedquestion->id;
        }

        global $CFG;
        if ($CFG->version >= 2022041900 /* Moodle 4.0.0 */) {
            $question->category = $form->category;
        } else {
            $question->category = $authorizedquestion->category;
        }

        $form->defaultmark = $question->defaultmark;
        $form->questiontext = $question->questiontext;
        $form->questiontextformat = 0;
        $form->options = clone($question->options);
        unset($question->options);
        return parent::save_question($question, $form);
    }

    public function create_editing_form($submiturl, $question, $category, $contexts, $formeditable) {
        global $CFG;
        require_once($CFG->dirroot . '/question/type/multianswerwiris/edit_multianswerwiris_form.php');
        $wform = new qtype_multianswer_edit_form_helper($submiturl, $question, $category, $contexts, $formeditable);
        return new qtype_multianswerwiris_edit_form($wform, $submiturl, $question, $category, $contexts, $formeditable);
    }

    public function initialise_question_instance(question_definition $question, $questiondata) {
        global $CFG;
        parent::initialise_question_instance($question, $questiondata);

        $question->subquestions = $question->base->subquestions;
        // Add Wiris Quizzes question to subquestions.
        foreach ($question->subquestions as $key => $subquestion) {
            if (substr($subquestion->get_type_name(), -5) == 'wiris') {
                $question->subquestions[$key]->wirisquestion = $question->wirisquestion;
            }
        }
        // Change wiris subquestions by moodle standard implementation in base object.
        foreach ($question->base->subquestions as $key => $subquestion) {
            if (isset($subquestion->base)) {
                // Put defaultmark to base. It was set up by multianswer
                // get_question_options because subquestions don't have entry
                // to quiz_question_instance table.

                if ($CFG->version >= 2023042402 /* v4.2.2 */) {
                    $subquestion->base->defaultmark = &$subquestion->defaultmark;
                } else {
                    $subquestion->base->maxmark = &$subquestion->maxmark;
                }
                $question->base->subquestions[$key] = $subquestion->base;
            }
        }
        $question->places = &$question->base->places;
        $question->textfragments = &$question->base->textfragments;
    }

    public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $expout = '';
        $expout .= "    <wirissubquestions>\n";

        foreach ($question->options->questions as $key => $value) {
            $expout .= "        <wirissubquestion>\n";
            if (isset($value->questiontext)) {
                $expout .= "            <![CDATA[" . $value->questiontext . "]]>\n";
            }
            $expout .= "        </wirissubquestion>\n";
        }

        $expout .= "    </wirissubquestions>\n";
        $expout .= parent::export_to_xml($question, $format);
        return $expout;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        if (isset($question) && $question == 0) {
            return false;
        }
        if (isset($data['#']['wirisquestion']) && substr($data['#']['wirisquestion'][0]['#'], 0, 9) == '«session') {
            // Moodle 1.9.
            $text = $data['#']['wirisquestiontext'][0]['#']['text'][0]['#'];
            $text = $this->wrsqz_adapttext($text);
            $data['#']['questiontext'][0]['#']['text'][0]['#'] = $text;
            $qo = $format->import_multianswer($data);
            $qo->qtype = 'multianswerwiris';

            foreach ($qo->options->questions as $key => $value) {
                if ($value->qtype != 'numerical') {
                    $qo->options->questions[$key]->qtype = $value->qtype . 'wiris';
                }
            }

            $wirisquestion = '<question><wirisCasSession>';
            $mathmldecode = $this->wrsqz_mathml_decode(trim($data['#']['wirisquestion'][0]['#']));
            $wirisquestion .= htmlspecialchars($mathmldecode, ENT_COMPAT, "UTF-8");
            $wirisquestion .= '</wirisCasSession>';

            if (isset($data['#']['wirisoptions']) && count($data['#']['wirisoptions'][0]['#']) > 0) {
                $wirisquestion .= '<localData>';
                $wirisquestion .= $this->wrsqz_get_cas_for_computations($data);
                $wirisquestion .= $this->wrsqz_hidden_initial_cas_value($data);
                $wirisquestion .= '</localData>';
            }

            $wirisquestion .= '</question>';
            $qo->wirisquestion = $wirisquestion;
            return $qo;
        } else {
            // Moodle 2.x.
            if (isset($data['#']['wirissubquestions'])) {
                foreach ($data['#']['wirissubquestions']['0']['#']['wirissubquestion'] as $index => $subq) {
                    $pos = $index + 1;
                    $text = $data['#']['questiontext']['0']['#']['text']['0']['#'];
                    $replacedtext = preg_replace('~{#' . $pos . '}~', trim($subq['#']), $text);
                    $data['#']['questiontext']['0']['#']['text']['0']['#'] = $replacedtext;
                }
            }

            $qo = $format->import_multianswer($data);
            $qo->qtype = 'multianswerwiris';

            foreach ($qo->options->questions as $key => $value) {
                if ($value->qtype != 'numerical') {
                    $qo->options->questions[$key]->qtype = $value->qtype . 'wiris';
                }
            }

            $qo->wirisquestion = trim($this->decode_html_entities($data['#']['wirisquestion'][0]['#']));
            return $qo;
        }
    }

}
