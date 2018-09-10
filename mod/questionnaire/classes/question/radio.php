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
 * This file contains the parent class for radio question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
defined('MOODLE_INTERNAL') || die();

class radio extends base {

    protected function responseclass() {
        return '\\mod_questionnaire\\response\\single';
    }

    public function helpname() {
        return 'radiobuttons';
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
        return 'mod_questionnaire/question_radio';
    }

    /**
     * Override and return a response template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function response_template() {
        return 'mod_questionnaire/response_radio';
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
    protected function question_survey_display($data, $dependants=[], $blankquestionnaire=false) {
        // Radio buttons
        global $idcounter;  // To make sure all radio buttons have unique ids. // JR 20 NOV 2007.

        $otherempty = false;
        // Find out which radio button is checked (if any); yields choice ID.
        if (isset($data->{'q'.$this->id})) {
            $checked = $data->{'q'.$this->id};
        } else {
            $checked = '';
        }
        $horizontal = $this->length;
        $ischecked = false;

        $choicetags = new \stdClass();
        $choicetags->qelements = [];
        foreach ($this->choices as $id => $choice) {
            $radio = new \stdClass();
            $other = strpos($choice->content, '!other');
            if ($horizontal) {
                $radio->horizontal = $horizontal;
            }

            if ($other !== 0) { // This is a normal radio button.
                $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);

                $radio->name = 'q'.$this->id;
                $radio->id = $htmlid;
                $radio->value = $id;
                if ($id == $checked) {
                    $radio->checked = true;
                    $ischecked = true;
                }
                $value = '';
                if ($blankquestionnaire) {
                    $radio->disabled = true;
                    $value = ' ('.$choice->value.') ';
                }
                $contents = questionnaire_choice_values($choice->content);
                $radio->label = $value.format_text($contents->text, FORMAT_HTML, ['noclean' => true]).$contents->image;
            } else {             // Radio button with associated !other text field.
                $othertext = preg_replace(["/^!other=/", "/^!other/"], ['', get_string('other', 'questionnaire')],
                    $choice->content);
                $cid = 'q'.$this->id.'_'.$id;
                $otherempty = false;
                if (substr($checked, 0, 6) == 'other_') { // Fix bug CONTRIB-222.
                    $checked = substr($checked, 6);
                }
                $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);

                $radio->name = 'q'.$this->id;
                $radio->id = $htmlid;
                $radio->value = 'other_'.$id;
                if (($id == $checked) || !empty($data->$cid)) {
                    $radio->checked = true;
                    $ischecked = true;
                    if (isset($data->$cid) && (trim($data->$cid) == false)) {
                        $otherempty = true;
                    }
                }
                $radio->label = format_text($othertext, FORMAT_HTML, ['noclean' => true]);
                $radio->oname = $cid;
                $radio->oid = $htmlid.'-other';
                if (isset($data->$cid)) {
                    $radio->ovalue = stripslashes($data->$cid);
                }
                $radio->olabel = 'Text for '.format_text($othertext, FORMAT_HTML, ['noclean' => true]);
            }
            $choicetags->qelements[] = (object)['choice' => $radio];
        }

        // CONTRIB-846.
        if (!$this->required()) {
            $radio = new \stdClass();
            $id = '';
            $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);
            if ($horizontal) {
                $radio->horizontal = $horizontal;
            }

            $radio->name = 'q'.$this->id;
            $radio->id = $htmlid;
            $radio->value = $id;

            if (!$ischecked && !$blankquestionnaire) {
                $radio->checked = true;
            }
            $content = get_string('noanswer', 'questionnaire');
            $radio->label = format_text($content, FORMAT_HTML, ['noclean' => true]);

            $choicetags->qelements[] = (object)['choice' => $radio];
        }
        // End CONTRIB-846.

        if ($otherempty) {
            $this->add_notification(get_string('otherempty', 'questionnaire'));
        }
        return $choicetags;
    }

    /**
     * Return the context tags for the radio response template.
     * @param object $data
     * @return object The radio question response context tags.
     *
     */
    protected function response_survey_display($data) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->choices = [];
        $output = '';

        $horizontal = $this->length;
        $checked = (isset($data->{'q'.$this->id}) ? $data->{'q'.$this->id} : '');
        foreach ($this->choices as $id => $choice) {
            $chobj = new \stdClass();
            if ($horizontal) {
                $chobj->horizontal = 1;
            }
            $chobj->name = $id.$uniquetag++;
            if (strpos($choice->content, '!other') !== 0) {
                $contents = questionnaire_choice_values($choice->content);
                $choice->content = $contents->text.$contents->image;
                if ($id == $checked) {
                    $chobj->selected = 1;
                }
                $chobj->content = ($choice->content === '' ? $id : format_text($choice->content, FORMAT_HTML, ['noclean' => true]));
            } else {
                $othertext = preg_replace(["/^!other=/", "/^!other/"], ['', get_string('other', 'questionnaire')],
                    $choice->content);
                $cid = 'q'.$this->id.'_'.$id;
                if (isset($data->{'q'.$this->id.'_'.$id})) {
                    $chobj->selected = 1;
                    $chobj->othercontent = (!empty($data->$cid) ? htmlspecialchars($data->$cid) : '&nbsp;');
                }
                $chobj->content = $othertext;
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
        if (isset($responsedata->{'q'.$this->id}) && ($this->required()) &&
                (strpos($responsedata->{'q'.$this->id}, 'other_') !== false)) {
            return (trim($responsedata->{'q'.$this->id.''.substr($responsedata->{'q'.$this->id}, 5)}) != false);
        } else {
            return parent::response_complete($responsedata);
        }
    }

    /**
     * Check question's form data for valid response. Override this is type has specific format requirements.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_valid($responsedata) {
        if (isset($responsedata->{'q'.$this->id}) && (strpos($responsedata->{'q'.$this->id}, 'other_') !== false)) {
            // False if "other" choice is checked but text box is empty.
            return (trim($responsedata->{'q'.$this->id.''.substr($responsedata->{'q'.$this->id}, 5)}) != false);
        } else {
            return parent::response_valid($responsedata);
        }
    }

    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        $lengroup = [];
        $lengroup[] =& $mform->createElement('radio', 'length', '', get_string('vertical', 'questionnaire'), '0');
        $lengroup[] =& $mform->createElement('radio', 'length', '', get_string('horizontal', 'questionnaire'), '1');
        $mform->addGroup($lengroup, 'lengroup', get_string('alignment', 'questionnaire'), ' ', false);
        $mform->addHelpButton('lengroup', 'alignment', 'questionnaire');
        $mform->setType('length', PARAM_INT);

        return $mform;
    }

    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        return base::form_precise_hidden($mform);
    }
}