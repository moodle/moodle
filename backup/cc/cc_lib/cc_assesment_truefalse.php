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
 * @package    backup-convert
 * @copyright  2012 Darko Miletic <dmiletic@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

require_once('cc_asssesment.php');

class cc_assesment_question_truefalse extends cc_assesment_question_proc_base {
    public function __construct($quiz, $questions, $manifest, $section, $question_node, $rootpath, $contextid, $outdir) {
        parent::__construct($quiz, $questions, $manifest, $section, $question_node, $rootpath, $contextid, $outdir);
        $this->qtype = cc_qti_profiletype::true_false;
        $this->correct_answer_node_id = $this->questions->nodeValue(
            'plugin_qtype_truefalse_question/truefalse/trueanswer', $this->question_node);
        $maximum_quiz_grade = (int)$this->quiz->nodeValue('/activity/quiz/grade');
        $this->total_grade_value = ($maximum_quiz_grade + 1).'.0000000';
    }

    public function on_generate_answers() {
        // Add responses holder.
        $qresponse_lid = new cc_response_lidtype();
        $this->qresponse_lid = $qresponse_lid;
        $this->qpresentation->set_response_lid($qresponse_lid);
        $qresponse_choice = new cc_assesment_render_choicetype();
        $qresponse_lid->set_render_choice($qresponse_choice);
        // Mark that question has only one correct answer -
        // which applies for multiple choice and yes/no questions.
        $qresponse_lid->set_rcardinality(cc_qti_values::Single);
        // Are we to shuffle the responses?
        $shuffle_answers = (int)$this->quiz->nodeValue('/activity/quiz/shuffleanswers') > 0;
        $qresponse_choice->enable_shuffle($shuffle_answers);
        $answerlist = array();
        $qa_responses = $this->questions->nodeList('plugin_qtype_truefalse_question/answers/answer', $this->question_node);
        foreach ($qa_responses as $node) {
            $answer_content = $this->questions->nodeValue('answertext', $node);
            $id = ((int)$this->questions->nodeValue('@id', $node) == $this->correct_answer_node_id);
            $qresponse_label = cc_assesment_helper::add_answer( $qresponse_choice,
                                                                $answer_content,
                                                                cc_qti_values::htmltype);
            $answer_ident = strtolower(trim($answer_content));
            $qresponse_label->set_ident($answer_ident);
            $feedback_ident = ($id) ? 'correct_fb' : 'incorrect_fb';
            if (empty($this->correct_answer_ident) && $id) {
                $this->correct_answer_ident = $answer_ident;
            }
            // Add answer specific feedback if not empty.
            $content = $this->questions->nodeValue('feedback', $node);
            if (!empty($content)) {
                $result = cc_helpers::process_linked_files( $content,
                                                            $this->manifest,
                                                            $this->rootpath,
                                                            $this->contextid,
                                                            $this->outdir);


                cc_assesment_helper::add_feedback( $this->qitem,
                                                    $result[0],
                                                    cc_qti_values::htmltype,
                                                    $feedback_ident);

                pkg_resource_dependencies::instance()->add($result[1]);

                $answerlist[$answer_ident] = $feedback_ident;
            }
        }

        $this->answerlist = $answerlist;

    }

    public function on_generate_response_processing() {
        parent::on_generate_response_processing();

        // Response conditions.
        // General unconditional feedback must be added as a first respcondition
        // without any condition and just displayfeedback (if exists).
        if (!empty($this->general_feedback)) {
            $qrespcondition = new cc_assesment_respconditiontype();
            $qrespcondition->set_title('General feedback');
            $this->qresprocessing->add_respcondition($qrespcondition);
            $qrespcondition->enable_continue();
            // Define the condition for success.
            $qconditionvar = new cc_assignment_conditionvar();
            $qrespcondition->set_conditionvar($qconditionvar);
            $qother = new cc_assignment_conditionvar_othertype();
            $qconditionvar->set_other($qother);
            $qdisplayfeedback = new cc_assignment_displayfeedbacktype();
            $qrespcondition->add_displayfeedback($qdisplayfeedback);
            $qdisplayfeedback->set_feedbacktype(cc_qti_values::Response);
            $qdisplayfeedback->set_linkrefid('general_fb');
        }

        // Success condition.
        // For all question types outside of the Essay question, scoring is done in a
        // single <respcondition> with a continue flag set to No. The outcome is always
        // a variable named SCORE which value must be set to 100 in case of correct answer.
        // Partial scores (not 0 or 100) are not supported.
        $qrespcondition = new cc_assesment_respconditiontype();
        $qrespcondition->set_title('Correct');
        $this->qresprocessing->add_respcondition($qrespcondition);
        $qrespcondition->enable_continue(false);
        $qsetvar = new cc_assignment_setvartype(100);
        $qrespcondition->add_setvar($qsetvar);
        // Define the condition for success.
        $qconditionvar = new cc_assignment_conditionvar();
        $qrespcondition->set_conditionvar($qconditionvar);
        // TODO: recheck this.
        $qvarequal = new cc_assignment_conditionvar_varequaltype($this->correct_answer_ident);
        $qconditionvar->set_varequal($qvarequal);
        $qvarequal->set_respident($this->qresponse_lid->get_ident());

        if (array_key_exists($this->correct_answer_ident, $this->answerlist)) {
            $qdisplayfeedback = new cc_assignment_displayfeedbacktype();
            $qrespcondition->add_displayfeedback($qdisplayfeedback);
            $qdisplayfeedback->set_feedbacktype(cc_qti_values::Response);
            $qdisplayfeedback->set_linkrefid($this->answerlist[$this->correct_answer_ident]);
        }

        foreach ($this->correct_feedbacks as $ident) {
            $qdisplayfeedback = new cc_assignment_displayfeedbacktype();
            $qrespcondition->add_displayfeedback($qdisplayfeedback);
            $qdisplayfeedback->set_feedbacktype(cc_qti_values::Response);
            $qdisplayfeedback->set_linkrefid($ident);
        }

        // Rest of the conditions.
        foreach ($this->answerlist as $ident => $refid) {
            if ($ident == $this->correct_answer_ident) {
                continue;
            }

            $qrespcondition = new cc_assesment_respconditiontype();
            $this->qresprocessing->add_respcondition($qrespcondition);
            $qsetvar = new cc_assignment_setvartype(0);
            $qrespcondition->add_setvar($qsetvar);
            // Define the condition for fail.
            $qconditionvar = new cc_assignment_conditionvar();
            $qrespcondition->set_conditionvar($qconditionvar);
            $qvarequal = new cc_assignment_conditionvar_varequaltype($ident);
            $qconditionvar->set_varequal($qvarequal);
            $qvarequal->set_respident($this->qresponse_lid->get_ident());

            $qdisplayfeedback = new cc_assignment_displayfeedbacktype();
            $qrespcondition->add_displayfeedback($qdisplayfeedback);
            $qdisplayfeedback->set_feedbacktype(cc_qti_values::Response);
            $qdisplayfeedback->set_linkrefid($refid);

            foreach ($this->incorrect_feedbacks as $ident) {
                $qdisplayfeedback = new cc_assignment_displayfeedbacktype();
                $qrespcondition->add_displayfeedback($qdisplayfeedback);
                $qdisplayfeedback->set_feedbacktype(cc_qti_values::Response);
                $qdisplayfeedback->set_linkrefid($ident);
            }
        }
    }
}
