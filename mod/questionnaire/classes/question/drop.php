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
 * This file contains the parent class for drop question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
defined('MOODLE_INTERNAL') || die();
use \html_writer;

class drop extends base {

    protected function responseclass() {
        return '\\mod_questionnaire\\response\\single';
    }

    public function helpname() {
        return 'dropdown';
    }

    /**
     * Return true if the question has choices.
     */
    public function has_choices() {
        return true;
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_drop';
    }

    /**
     * Override and return a form template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function response_template() {
        return 'mod_questionnaire/response_drop';
    }

    /**
     * Override this and return true if the question type allows dependent questions.
     * @return boolean
     */
    public function allows_dependents() {
        return true;
    }

    /**
     * True if question type supports feedback options. False by default.
     */
    public function supports_feedback() {
        return true;
    }

    /**
     * Return the context tags for the check question template.
     * @param object $data
     * @param array $dependants Array of all questions/choices depending on this question.
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($data, $dependants, $blankquestionnaire=false) {
        // Drop.
        $output = '';
        $options = [];

        $choicetags = new \stdClass();
        $choicetags->qelements = new \stdClass();
        $selected = isset($data->{'q'.$this->id}) ? $data->{'q'.$this->id} : false;
        $options[] = (object)['value' => '', 'label' => get_string('choosedots')];
        foreach ($this->choices as $key => $choice) {
            if ($pos = strpos($choice->content, '=')) {
                $choice->content = substr($choice->content, $pos + 1);
            }
            $option = new \stdClass();
            $option->value = $key;
            $option->label = $choice->content;
            if (($selected !== false) && ($key == $selected)) {
                $option->selected = true;
            }
            $options[] = $option;
        }
        $chobj = new \stdClass();
        $chobj->name = 'q'.$this->id;
        $chobj->id = self::qtypename($this->type_id) . $this->name;
        $chobj->class = 'select custom-select menu q'.$this->id;
        $chobj->options = $options;
        $choicetags->qelements->choice = $chobj;

        return $choicetags;
    }

    /**
     * Return the context tags for the drop response template.
     * @param object $data
     * @return object The check question response context tags.
     *
     */
    protected function response_survey_display($data) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->name = 'q' . $this->id.$uniquetag++;
        $resptags->id = 'menu' . $resptags->name;
        $resptags->class = 'select custom-select ' . $resptags->id;
        $resptags->options = [];
        foreach ($this->choices as $id => $choice) {
            $contents = questionnaire_choice_values($choice->content);
            $chobj = new \stdClass();
            $chobj->value = $id;
            $chobj->label = format_text($contents->text, FORMAT_HTML, ['noclean' => true]);
            if (isset($data->{'q'.$this->id}) && ($id == $data->{'q'.$this->id})) {
                $chobj->selected = 1;
                $resptags->selectedlabel = $chobj->label;
            }
            $resptags->options[] = $chobj;
        }

        return $resptags;
    }

    protected function form_length(\MoodleQuickForm $mform, $helpname = '') {
        return base::form_length_hidden($mform);
    }

    protected function form_precise(\MoodleQuickForm $mform, $helpname = '') {
        return base::form_precise_hidden($mform);
    }
}