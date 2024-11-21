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

namespace mod_questionnaire\question;

/**
 * This file contains the parent class for check question types.
 *
 * @author Mike Churchward
 * @copyright  2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class check extends question {

    /**
     * Return the responseclass used.
     * @return string
     */
    protected function responseclass() {
        return '\\mod_questionnaire\\responsetype\\multiple';
    }

    /**
     * Return the help name.
     * @return string
     */
    public function helpname() {
        return 'checkboxes';
    }

    /**
     * Return true if the question has choices.
     */
    public function has_choices() {
        return true;
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return string
     */
    public function question_template() {
        return 'mod_questionnaire/question_check';
    }

    /**
     * Override and return a form template if provided. Output of response_survey_display is iterpreted based on this.
     * @return string
     */
    public function response_template() {
        return 'mod_questionnaire/response_check';
    }

    /**
     * Override this and return true if the question type allows dependent questions.
     * @return boolean
     */
    public function allows_dependents() {
        return true;
    }

    /**
     * Return the context tags for the check question template.
     * @param \mod_questionnaire\responsetype\response\response $response
     * @param array $dependants Array of all questions/choices depending on this question.
     * @param boolean $blankquestionnaire
     * @return \stdClass The check question context tags.
     *
     */
    protected function question_survey_display($response, $dependants, $blankquestionnaire = false) {
        // Check boxes.
        $otherempty = false;
        if (!empty($response)) {
            // Verify that number of checked boxes (nbboxes) is within set limits (length = min; precision = max).
            if (!empty($response->answers[$this->id])) {
                $otherempty = false;
                $nbboxes = count($response->answers[$this->id]);
                foreach ($response->answers[$this->id] as $answer) {
                    $choice = $this->choices[$answer->choiceid];
                    if ($choice->is_other_choice()) {
                        $otherempty = empty($answer->value);
                    }
                }
                $nbchoices = count($this->choices);
                $min = $this->length;
                $max = $this->precise;
                if ($max == 0) {
                    $max = $nbchoices;
                }
                if ($min > $max) {
                    $min = $max; // Sanity check.
                }
                $min = min($nbchoices, $min);
                if ($nbboxes < $min || $nbboxes > $max) {
                    $msg = get_string('boxesnbreq', 'questionnaire');
                    if ($min == $max) {
                        $msg .= get_string('boxesnbexact', 'questionnaire', $min);
                    } else {
                        if ($min && ($nbboxes < $min)) {
                            $msg .= get_string('boxesnbmin', 'questionnaire', $min);
                            if ($nbboxes > $max) {
                                $msg .= ' & ' .get_string('boxesnbmax', 'questionnaire', $max);
                            }
                        } else {
                            if ($nbboxes > $max ) {
                                $msg .= get_string('boxesnbmax', 'questionnaire', $max);
                            }
                        }
                    }
                    $this->add_notification($msg);
                }
            }
        }

        $choicetags = new \stdClass();
        $choicetags->qelements = [];
        foreach ($this->choices as $id => $choice) {
            $checkbox = new \stdClass();
            $contents = questionnaire_choice_values($choice->content);
            $checked = false;
            if (!empty($response->answers[$this->id]) ) {
                $checked = isset($response->answers[$this->id][$id]);
            }
            $checkbox->name = 'q'.$this->id.'['.$id.']';
            $checkbox->value = $id;
            $checkbox->id = 'checkbox_'.$id;
            $checkbox->label = format_text($contents->text, FORMAT_HTML, ['noclean' => true]).$contents->image;
            if ($checked) {
                $checkbox->checked = $checked;
            }
            if ($choice->is_other_choice()) {
                $checkbox->oname = 'q'.$this->id.'['.$choice->other_choice_name().']';
                $checkbox->ovalue = (isset($response->answers[$this->id][$id]) && !empty($response->answers[$this->id][$id]) ?
                    format_string(stripslashes($response->answers[$this->id][$id]->value)) : '');
                $checkbox->label = format_text($choice->other_choice_display().'', FORMAT_HTML, ['noclean' => true]);
            }
            if (!empty($this->qlegend)) {
                $checkbox->alabel = strip_tags("{$this->qlegend} {$checkbox->label}");
            }
            $choicetags->qelements[] = (object)['choice' => $checkbox];
        }
        if ($otherempty) {
            $this->add_notification(get_string('otherempty', 'questionnaire'));
        }
        return $choicetags;
    }

    /**
     * Return the context tags for the check response template.
     * @param \mod_questionnaire\responsetype\response\response $response
     * @return \stdClass The check question response context tags.
     */
    protected function response_survey_display($response) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->choices = [];

        if (!isset($response->answers[$this->id])) {
            $response->answers[$this->id][] = new \mod_questionnaire\responsetype\answer\answer();
        }

        foreach ($this->choices as $id => $choice) {
            $chobj = new \stdClass();
            if (!$choice->is_other_choice()) {
                $contents = questionnaire_choice_values($choice->content);
                $choice->content = $contents->text.$contents->image;
                if (isset($response->answers[$this->id][$id])) {
                    $chobj->selected = 1;
                }
                $chobj->name = $id.$uniquetag++;
                $chobj->content = (($choice->content === '') ? $id : format_text($choice->content, FORMAT_HTML,
                    ['noclean' => true]));
            } else {
                $othertext = $choice->other_choice_display();
                if (isset($response->answers[$this->id][$id])) {
                    $oresp = $response->answers[$this->id][$id]->value;
                    $chobj->selected = 1;
                    $chobj->othercontent = (!empty($oresp) ? htmlspecialchars($oresp) : '&nbsp;');
                }
                $chobj->name = $id.$uniquetag++;
                $chobj->content = (($othertext === '') ? $id : $othertext);
            }
            if (!empty($this->qlegend)) {
                $chobj->alabel = strip_tags("{$this->qlegend} {$chobj->content}");
            }
            $resptags->choices[] = $chobj;
        }
        return $resptags;
    }

    /**
     * Check question's form data for complete response.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_complete($responsedata) {
        if (isset($responsedata->{'q'.$this->id}) && $this->required() &&
            is_array($responsedata->{'q'.$this->id})) {
            foreach ($responsedata->{'q' . $this->id} as $key => $choice) {
                // If only an 'other' choice is selected and empty, question is not completed.
                if ((strpos($key, 'o') === 0) && empty($choice)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        return parent::response_complete($responsedata);
    }

    /**
     * Check question's form data for valid response. Override this is type has specific format requirements.
     *
     * @param \stdClass $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_valid($responsedata) {
        $nbrespchoices = 0;
        $valid = true;
        if (is_a($responsedata, 'mod_questionnaire\responsetype\response\response')) {
            // If $responsedata is a response object, look through the answers.
            if (isset($responsedata->answers[$this->id]) && !empty($responsedata->answers[$this->id])) {
                foreach ($responsedata->answers[$this->id] as $answer) {
                    if (isset($this->choices[$answer->choiceid]) && $this->choices[$answer->choiceid]->is_other_choice()) {
                        $valid = !empty($answer->value);
                    } else {
                        $nbrespchoices++;
                    }
                }
            }
        } else if (isset($responsedata->{'q'.$this->id})) {
            foreach ($responsedata->{'q'.$this->id} as $key => $answer) {
                if (strpos($key, 'o') === 0) {
                    // ..."other" choice is checked but text box is empty.
                    $okey = substr($key, 1);
                    if (isset($responsedata->{'q'.$this->id}[$okey]) && empty(trim($answer))) {
                        $valid = false;
                        break;
                    }
                } else if (is_numeric($key)) {
                    $nbrespchoices++;
                }
            }
        } else {
            return parent::response_valid($responsedata);
        }

        $nbquestchoices = count($this->choices);
        $min = $this->length;
        $max = $this->precise;
        if ($max == 0) {
            $max = $nbquestchoices;
        }
        if ($min > $max) {
            $min = $max;     // Sanity check.
        }
        $min = min($nbquestchoices, $min);
        if ($nbrespchoices && (($nbrespchoices < $min) || ($nbrespchoices > $max))) {
            // Number of ticked boxes is not within min and max set limits.
            $valid = false;
        }

        return $valid;
    }

    /**
     * Return the length form element.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_length($mform, 'minforcedresponses');
    }

    /**
     * Return the precision form element.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_precise($mform, 'maxforcedresponses');
    }

    /**
     * True if question provides mobile support.
     *
     * @return bool
     */
    public function supports_mobile() {
        return true;
    }

    /**
     * Preprocess choice data.
     * @param \stdClass $formdata
     * @return bool
     */
    protected function form_preprocess_choicedata($formdata) {
        if (empty($formdata->allchoices)) {
            throw new \moodle_exception('enterpossibleanswers', 'mod_questionnaire');
        } else {
            // Sanity checks for min and max checked boxes.
            $allchoices = $formdata->allchoices;
            $allchoices = explode("\n", $allchoices);
            $nbvalues = count($allchoices);

            if ($formdata->length > $nbvalues) {
                $formdata->length = $nbvalues;
            }
            if ($formdata->precise > $nbvalues) {
                $formdata->precise = $nbvalues;
            }
            if ($formdata->precise != 0) {
                $formdata->precise = max($formdata->length, $formdata->precise);
            }
        }
        return true;
    }

    /**
     * Return the mobile question display.
     * @param int $qnum
     * @param bool $autonum
     * @return \stdClass
     */
    public function mobile_question_display($qnum, $autonum = false) {
        $mobiledata = parent::mobile_question_display($qnum, $autonum);
        $mobiledata->ischeckbox = true;
        return $mobiledata;
    }

    /**
     * Return the mobile question choices display.
     * @return array
     */
    public function mobile_question_choices_display() {
        $choices = parent::mobile_question_choices_display();
        foreach ($choices as $choicenum => $choice) {
            // Add a fieldkey for each choice.
            $choices[$choicenum]->choicefieldkey = $this->mobile_fieldkey($choice->id);
            if ($choice->is_other_choice()) {
                $choices[$choicenum]->otherchoicekey = $this->mobile_fieldkey($choice->other_choice_name());
                $choices[$choicenum]->content = format_text($choice->other_choice_display(), FORMAT_HTML, ['noclean' => true]);
            }
        }
        return $choices;
    }

    /**
     * Return the mobile response data.
     * @param \stdClass $response
     * @return array
     */
    public function get_mobile_response_data($response) {
        $resultdata = [];
        if (isset($response->answers[$this->id])) {
            foreach ($response->answers[$this->id] as $answer) {
                if (isset($this->choices[$answer->choiceid])) {
                    // Add a fieldkey for each choice.
                    $resultdata[$this->mobile_fieldkey($answer->choiceid)] = 1;
                    if ($this->choices[$answer->choiceid]->is_other_choice()) {
                        $resultdata[$this->mobile_fieldkey($this->choices[$answer->choiceid]->other_choice_name())] =
                            $answer->value;
                    }
                }
            }
        }
        return $resultdata;
    }
}
