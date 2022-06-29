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

class cc_assesment_question_sfib extends cc_assesment_question_proc_base {
    public function __construct($quiz, $questions, $manifest, $section, $question_node, $rootpath, $contextid, $outdir) {
        parent::__construct($quiz, $questions, $manifest, $section, $question_node, $rootpath, $contextid, $outdir);
        $this->qtype = cc_qti_profiletype::field_entry;
        $this->correct_answer_node_id = $this->questions->nodeValue(
            'plugin_qtype_truefalse_question/truefalse/trueanswer',
            $this->question_node
        );
        $maximum_quiz_grade = (int)$this->quiz->nodeValue('/activity/quiz/grade');
        $this->total_grade_value = ($maximum_quiz_grade + 1).'.0000000';
    }

    public function on_generate_metadata() {
        parent::on_generate_metadata();

        $category = $this->questions->nodeValue('../../name', $this->question_node);
        if (!empty($category)) {
            $this->qmetadata->set_category($category);
        }
    }

    public function on_generate_presentation() {
        parent::on_generate_presentation();
        $response_str = new cc_assesment_response_strtype();
        $response_fib = new cc_assesment_render_fibtype();

         // The standard requires that only rows attribute must be set,
         // the rest may or may not be configured. For the sake of brevity we leave it empty.
        $response_fib->set_rows(1);
        $response_str->set_render_fib($response_fib);
        $this->qpresentation->set_response_str($response_str);
    }

    public function on_generate_feedbacks() {
        parent::on_generate_feedbacks();
        // Question combined feedback.
        $responsenodes = $this->questions->nodeList('plugin_qtype_shortanswer_question//answer', $this->question_node);
        $count = 0;
        foreach ($responsenodes as $respnode) {
            $content = $this->questions->nodeValue('feedback', $respnode);
            if (empty($content)) {
                continue;
            }

            $correct = (int)$this->questions->nodeValue('fraction', $respnode) == 1;
            $answerid = (int)$this->questions->nodeValue('@id', $respnode);

            $result = cc_helpers::process_linked_files( $content,
                                                        $this->manifest,
                                                        $this->rootpath,
                                                        $this->contextid,
                                                        $this->outdir);
            $ident = $correct ? 'correct' : 'incorrect';
            $ident .= '_'.$count.'_fb';
            cc_assesment_helper::add_feedback( $this->qitem,
                                                $result[0],
                                                cc_qti_values::htmltype,
                                                $ident);

            pkg_resource_dependencies::instance()->add($result[1]);

            if ($correct) {
                $this->correct_feedbacks[$answerid] = $ident;
            } else {
                $this->incorrect_feedbacks[$answerid] = $ident;
            }

            ++$count;
        }
    }

    public function on_generate_response_processing() {
        parent::on_generate_response_processing();

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

        // Answer separate conditions.
        $correct_responses = $this->questions->nodeList(
            'plugin_qtype_shortanswer_question//answer[fraction=1]', $this->question_node);
        $incorrect_responses = $this->questions->nodeList(
            'plugin_qtype_shortanswer_question//answer[fraction<1]', $this->question_node);
        $items = array(
            array($correct_responses, $this->correct_feedbacks),
            array($incorrect_responses, $this->incorrect_feedbacks)
        );
        foreach ($items as $respfeed) {
            foreach ($respfeed[0] as $coresponse) {
                $qrespcondition = new cc_assesment_respconditiontype();
                $qrespcondition->enable_continue();
                $this->qresprocessing->add_respcondition($qrespcondition);
                $qconditionvar = new cc_assignment_conditionvar();
                $qrespcondition->set_conditionvar($qconditionvar);
                $respc = $this->questions->nodeValue('answertext', $coresponse);
                $resid = $this->questions->nodeValue('@id', $coresponse);
                $qvarequal = new cc_assignment_conditionvar_varequaltype($respc);
                $qconditionvar->set_varequal($qvarequal);
                $qvarequal->set_respident('response');
                $qvarequal->enable_case(false);
                if (!empty($respfeed[1][$resid])) {
                    $qdisplayfeedback = new cc_assignment_displayfeedbacktype();
                    $qrespcondition->add_displayfeedback($qdisplayfeedback);
                    $qdisplayfeedback->set_feedbacktype(cc_qti_values::Response);
                    $qdisplayfeedback->set_linkrefid($respfeed[1][$resid]);
                }
            }
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

        foreach ($correct_responses as $coresponse) {
            $respc = $this->questions->nodeValue('answertext', $coresponse);
            $qvarequal = new cc_assignment_conditionvar_varequaltype($respc);
            $qconditionvar->set_varequal($qvarequal);
            $qvarequal->set_respident('response');
            $qvarequal->enable_case(false);
        }

        // Add incorrect handling.
        $qrespcondition = new cc_assesment_respconditiontype();
        $this->qresprocessing->add_respcondition($qrespcondition);
        $qrespcondition->enable_continue(false);
        // Define the condition for failure.
        $qconditionvar = new cc_assignment_conditionvar();
        $qrespcondition->set_conditionvar($qconditionvar);
        $qother = new cc_assignment_conditionvar_othertype();
        $qconditionvar->set_other($qother);
        $qsetvar = new cc_assignment_setvartype(0);
        $qrespcondition->add_setvar($qsetvar);
    }
}
