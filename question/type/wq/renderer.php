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
require_once($CFG->dirroot . '/question/type/essay/renderer.php');

class qtype_wq_renderer extends qtype_renderer {

    protected $base;

    public function __construct(qtype_renderer $base = null, moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->base = $base;
    }

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        $result = $this->base->formulation_and_controls($qa, $options);

        // Auxiliar text.
        $slots = $qa->get_question()->wirisquestion->question->getSlots();
        if (isset($slots[0])) {
            $showauxiliartextinput = $slots[0]->getProperty(com_wiris_quizzes_api_PropertyName::$SHOW_AUXILIARY_TEXT_INPUT); // @codingStandardsIgnoreLine
        } else {
            $showauxiliartextinput = $qa->get_question()->wirisquestion->question->getProperty(com_wiris_quizzes_api_PropertyName::$SHOW_AUXILIARY_TEXT_INPUT); // @codingStandardsIgnoreLine
        }

        if ($showauxiliartextinput == "true") {
            $result .= $this->auxiliar_text($qa, $options);
        }

        $this->add_javascript();
        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= $this->lang();
        $result .= $this->auxiliar_cas();
        $result .= $this->question($qa);
        $result .= $this->question_instance($qa);
        $result .= html_writer::end_tag('div');
        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->base->specific_feedback($qa);
    }

    public function correct_response(question_attempt $qa) {
        return $this->base->correct_response($qa);
    }

    protected function add_javascript() {
        // Add javascript to launch editor and quizzes.
        $this->page->requires->js('/question/type/wq/quizzes/service.php?name=quizzes.js&service=resource', false);
    }
    protected function question(question_attempt $qa) {
        // Add question definition.
        $question = $qa->get_question();
        $wirisquestion = $question->wirisquestion;
        $studentquestion = $wirisquestion->getStudentQuestion();
        $sq = $studentquestion->serialize();
        $wirisquestionattributes = array(
            'type' => 'hidden',
            'value' => $sq,
            'class' => 'wirisquestion',
        );
        return html_writer::empty_tag('input', $wirisquestionattributes);
    }
    protected function question_instance(question_attempt $qa) {
        // Add question instance.
        $question = $qa->get_question();
        $xml = $qa->get_last_qt_var('_sqi');
        if (!empty($xml)) {
            // For some reason interactive questions with multiple tries escape their variables.
            if (substr($xml, 0, 4) == "&lt;") {
                $xml = html_entity_decode($xml);
            }
            $builder = com_wiris_quizzes_api_Quizzes::getInstance();
            $sqi = $builder->readQuestionInstance($xml, $question->wirisquestion);
            $question->wirisquestioninstance->updateFromStudentQuestionInstance($sqi);
        }
        $sqi = $question->wirisquestioninstance->getStudentQuestionInstance();
        $xml = $sqi->serialize();

        $sqiname = $qa->get_qt_field_name('_sqi');
        $wirisquestioninstanceattributes = array(
            'type' => 'hidden',
            'value' => $xml,
            'class' => 'wirisquestioninstance',
            'name' => $sqiname,
            'id' => $sqiname,
        );
        return html_writer::empty_tag('input', $wirisquestioninstanceattributes);
    }
    protected function auxiliar_cas() {
        return html_writer::empty_tag('input', array('class' => 'wirisauxiliarcasapplet', 'type' => 'hidden'));
    }

    protected function auxiliar_text(question_attempt $qa, question_display_options $options) {
        global $CFG;
        require_once($CFG->dirroot . '/repository/lib.php');

        $result = '';
        $result .= html_writer::empty_tag('hr');
        $result .= get_string('auxiliar_text', 'qtype_wq');

        // Answer field.
        $step = $qa->get_last_step_with_qt_var('auxiliar_text');
        $question = $qa->get_question();

        /** @var qtype_essay_format_renderer_base $responseoutput */
        $responseoutput = $this->page->get_renderer('qtype_wq', 'auxiliar_text');
        if (method_exists($responseoutput, 'set_displayoptions')) {
            // Moodle 4.1.2 and up require this.
            $responseoutput->set_displayoptions($options);
        }
        if (!$step->has_qt_var('auxiliar_text') && empty($options->readonly)) {
            // Auxiliar text has never been filled.
            $step = new question_attempt_step();
        }
        if (empty($options->readonly)) {
            $auxiliartext = $responseoutput->response_area_input(
                'auxiliar_text',
                $qa,
                $step,
                $question->auxiliartextfieldlines,
                $options->context
            );
        } else {
            $auxiliartext = $responseoutput->response_area_read_only(
                'auxiliar_text',
                $qa,
                $step,
                $question->auxiliartextfieldlines,
                $options->context
            );
        }

        $result .= html_writer::tag('div', $auxiliartext);

        return $result;
    }

    protected function lang() {
        return html_writer::empty_tag('input', array('class' => 'wirislang', 'type' => 'hidden', 'value' => current_language()));
    }

    /**
     * Display any extra question-type specific content that should be visible
     * when grading, if appropriate.
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function manual_comment(question_attempt $qa, question_display_options $options) {
        return $this->base->manual_comment($qa, $options);
    }

    public function feedback_class($fraction) {
        return $this->base->feedback_class($fraction);
    }
}

/**
 * Represents an essay with filepicker renderer. Is used to render an editor
 * with a filepicker as auxiliar text.
 */
class qtype_wq_auxiliar_text_renderer extends qtype_essay_format_editorfilepicker_renderer {
    protected function class_name() {
        return 'qtype_wq_auxiliar_renderer';
    }

    /**
     * Rewrite the auxiliar_text field response. To create the proper URL's for auxiliar_text
     * qt variable.
     */
    protected function prepare_response(
        $name,
        question_attempt $qa,
        question_attempt_step $step,
        $context
    ) {
        if (!$step->has_qt_var($name)) {
            return '';
        }

        $formatoptions = new stdClass();
        $formatoptions->para = false;
        $text = $qa->rewrite_response_pluginfile_urls(
            $step->get_qt_var($name),
            $context->id,
            'auxiliar_text',
            $step
        );
        return format_text($text, FORMAT_MOODLE, $formatoptions);
    }
}
