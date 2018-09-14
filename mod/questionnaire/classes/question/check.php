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
 * This file contains the parent class for check question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
defined('MOODLE_INTERNAL') || die();
use \html_writer;

class check extends base {

    protected function responseclass() {
        return '\\mod_questionnaire\\response\\multiple';
    }

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
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_check';
    }

    /**
     * Override and return a form template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
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
     * @param object $data
     * @param array $dependants Array of all questions/choices depending on this question.
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($data, $dependants, $blankquestionnaire=false) {
        // Check boxes.
        $otherempty = false;
        if (!empty($data) ) {
            if (!isset($data->{'q'.$this->id}) || !is_array($data->{'q'.$this->id})) {
                $data->{'q'.$this->id} = array();
            }
            // Verify that number of checked boxes (nbboxes) is within set limits (length = min; precision = max).
            if ( $data->{'q'.$this->id} ) {
                $otherempty = false;
                $boxes = $data->{'q'.$this->id};
                $nbboxes = count($boxes);
                foreach ($boxes as $box) {
                    $pos = strpos($box, 'other_');
                    if (is_int($pos) == true) {
                        $resp = 'q'.$this->id.''.substr($box, 5);
                        if (isset($data->$resp) && (trim($data->$resp) == false)) {
                            $otherempty = true;
                        }
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
                        $msg .= '&nbsp;'.get_string('boxesnbexact', 'questionnaire', $min);
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

            $other = strpos($choice->content, '!other');
            $checkbox = new \stdClass();
            if ($other !== 0) { // This is a normal check box.
                $contents = questionnaire_choice_values($choice->content);
                $checked = false;
                if (!empty($data) ) {
                    $checked = in_array($id, $data->{'q'.$this->id});
                }
                $checkbox->name = 'q'.$this->id.'[]';
                $checkbox->value = $id;
                $checkbox->id = 'checkbox_'.$id;
                $checkbox->label = format_text($contents->text, FORMAT_HTML, ['noclean' => true]).$contents->image;
                if ($checked) {
                    $checkbox->checked = $checked;
                }
            } else {             // Check box with associated !other text field.
                // In case length field has been used to enter max number of choices, set it to 20.
                $othertext = preg_replace(
                        array("/^!other=/", "/^!other/"),
                        array('', get_string('other', 'questionnaire')),
                        $choice->content);
                $cid = 'q'.$this->id.'_'.$id;
                if (!empty($data) && isset($data->$cid) && (trim($data->$cid) != false)) {
                    $checked = true;
                } else {
                    $checked = false;
                }
                $name = 'q'.$this->id.'[]';
                $value = 'other_'.$id;

                $checkbox->name = $name;
                $checkbox->oname = $cid;
                $checkbox->value = $value;
                $checkbox->ovalue = (isset($data->$cid) && !empty($data->$cid) ? stripslashes($data->$cid) : '');
                $checkbox->id = 'checkbox_'.$id;
                $checkbox->label = format_text($othertext.'', FORMAT_HTML, ['noclean' => true]);
                if ($checked) {
                    $checkbox->checked = $checked;
                }
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
     * @param object $data
     * @return object The check question response context tags.
     *
     */
    protected function response_survey_display($data) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->choices = [];

        if (!isset($data->{'q'.$this->id}) || !is_array($data->{'q'.$this->id})) {
            $data->{'q'.$this->id} = array();
        }

        foreach ($this->choices as $id => $choice) {
            $chobj = new \stdClass();
            if (strpos($choice->content, '!other') !== 0) {
                $contents = questionnaire_choice_values($choice->content);
                $choice->content = $contents->text.$contents->image;
                if (in_array($id, $data->{'q'.$this->id})) {
                    $chobj->selected = 1;
                }
                $chobj->name = $id.$uniquetag++;
                $chobj->content = (($choice->content === '') ? $id : format_text($choice->content, FORMAT_HTML,
                    ['noclean' => true]));
            } else {
                $othertext = preg_replace(
                        array("/^!other=/", "/^!other/"),
                        array('', get_string('other', 'questionnaire')),
                        $choice->content);
                $cid = 'q'.$this->id.'_'.$id;

                if (isset($data->$cid)) {
                    $chobj->selected = 1;
                    $chobj->othercontent = (!empty($data->$cid) ? htmlspecialchars($data->$cid) : '&nbsp;');
                }
                $chobj->name = $id.$uniquetag++;
                $chobj->content = (($othertext === '') ? $id : $othertext);
            }
            $resptags->choices[] = $chobj;
        }
        return $resptags;
    }

    /**
     * Check question's form data for valid response. Override this is type has specific format requirements.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_valid($responsedata) {
        $valid = true;
        if (isset($responsedata->{'q'.$this->id})) {
            $nbrespchoices = 0;
            foreach ($responsedata->{'q'.$this->id} as $resp) {
                if (strpos($resp, 'other_') !== false) {
                    // ..."other" choice is checked but text box is empty.
                    $othercontent = "q".$this->id.substr($resp, 5);
                    if (trim($responsedata->$othercontent) == false) {
                        $valid = false;
                        break;
                    }
                    $nbrespchoices++;
                } else if (is_numeric($resp)) {
                    $nbrespchoices++;
                }
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
        } else {
            $valid = parent::response_valid($responsedata);
        }

        return $valid;
    }

    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_length($mform, 'minforcedresponses');
    }

    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_precise($mform, 'maxforcedresponses');
    }

    /**
     * Preprocess choice data.
     */
    protected function form_preprocess_choicedata($formdata) {
        if (empty($formdata->allchoices)) {
            error (get_string('enterpossibleanswers', 'questionnaire'));
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
            $formdata->precise = max($formdata->length, $formdata->precise);
        }
        return true;
    }
}