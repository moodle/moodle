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
 * This file contains the parent class for radio question types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class radio extends question {

    /**
     * Each question type must define its response class.
     * @return object The response object based off of questionnaire_response_base.
     */
    protected function responseclass() {
        return '\\mod_questionnaire\\responsetype\\single';
    }

    /**
     * Short name for this question type - no spaces, etc..
     * @return string
     */
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
     * @param \mod_questionnaire\responsetype\response\response $response
     * @param array $dependants Array of all questions/choices depending on this question.
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($response, $dependants=[], $blankquestionnaire=false) {
        // Radio buttons.
        global $idcounter;  // To make sure all radio buttons have unique ids. // JR 20 NOV 2007.

        $otherempty = false;
        $horizontal = $this->length;
        $ischecked = false;

        $choicetags = new \stdClass();
        $choicetags->qelements = [];

        foreach ($this->choices as $id => $choice) {
            $radio = new \stdClass();
            if ($horizontal) {
                $radio->horizontal = $horizontal;
            }

            if (!$choice->is_other_choice()) { // This is a normal radio button.
                $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);

                $radio->name = 'q'.$this->id;
                $radio->id = $htmlid;
                $radio->value = $id;
                if (isset($response->answers[$this->id][$id])) {
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
                if (!empty($this->qlegend)) {
                    $radio->alabel = strip_tags("{$this->qlegend} {$radio->label}");
                }
            } else {             // Radio button with associated !other text field.
                $othertext = $choice->other_choice_display();
                $cname = choice::id_other_choice_name($id);
                $odata = isset($response->answers[$this->id][$id]) ? $response->answers[$this->id][$id]->value : '';
                $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);

                $radio->name = 'q'.$this->id;
                $radio->id = $htmlid;
                $radio->value = $id;
                if (isset($response->answers[$this->id][$id]) || !empty($odata)) {
                    $radio->checked = true;
                    $ischecked = true;
                }
                $otherempty = !empty($radio->checked) && empty($odata);
                $radio->label = format_text($othertext, FORMAT_HTML, ['noclean' => true]);
                $radio->oname = 'q'.$this->id.choice::id_other_choice_name($id);
                $radio->oid = $htmlid.'-other';
                if (isset($odata)) {
                    $radio->ovalue = format_string(stripslashes($odata));
                }
                $radio->olabel = 'Text for '.format_text($othertext, FORMAT_HTML, ['noclean' => true]);
                if (!empty($this->qlegend)) {
                    $radio->alabel = strip_tags("{$this->qlegend} {$radio->label}");
                    $radio->aolabel = strip_tags("{$this->qlegend} {$radio->olabel}");
                }
            }
            $choicetags->qelements[] = (object)['choice' => $radio];
        }

        // CONTRIB-846.
        if (!$this->required()) {
            $radio = new \stdClass();
            $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);
            if ($horizontal) {
                $radio->horizontal = $horizontal;
            }

            $radio->name = 'q'.$this->id;
            $radio->id = $htmlid;
            $radio->value = 0;

            if (!$ischecked && !$blankquestionnaire) {
                $radio->checked = true;
            }
            $content = get_string('noanswer', 'questionnaire');
            $radio->label = format_text($content, FORMAT_HTML, ['noclean' => true]);
            if (!empty($this->qlegend)) {
                $radio->alabel = strip_tags("{$this->qlegend} {$radio->label}");
            }

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
     * @param \mod_questionnaire\responsetype\response\response $response
     * @return object The radio question response context tags.
     */
    protected function response_survey_display($response) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->choices = [];

        $qdata = new \stdClass();
        $horizontal = $this->length;
        if (isset($response->answers[$this->id])) {
            $answer = reset($response->answers[$this->id]);
            $checked = $answer->choiceid;
        } else {
            $checked = null;
        }
        foreach ($this->choices as $id => $choice) {
            $chobj = new \stdClass();
            if ($horizontal) {
                $chobj->horizontal = 1;
            }
            $chobj->name = $id.$uniquetag++;
            $contents = questionnaire_choice_values($choice->content);
            $choice->content = $contents->text.$contents->image;
            if ($id == $checked) {
                $chobj->selected = 1;
                if ($choice->is_other_choice()) {
                    $chobj->othercontent = $answer->value;
                }
            }
            if ($choice->is_other_choice()) {
                $chobj->content = $choice->other_choice_display();
            } else {
                $chobj->content = ($choice->content === '' ? $id : format_text($choice->content, FORMAT_HTML, ['noclean' => true]));
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
        if (isset($responsedata->{'q'.$this->id}) && ($this->required()) &&
                (strpos($responsedata->{'q'.$this->id}, 'other_') !== false)) {
            return (trim($responsedata->{'q'.$this->id.''.substr($responsedata->{'q'.$this->id}, 5)}) != false);
        } else {
            return parent::response_complete($responsedata);
        }
    }

    /**
     * Return the length form element.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        $lengroup = [];
        $lengroup[] =& $mform->createElement('radio', 'length', '', get_string('vertical', 'questionnaire'), '0');
        $lengroup[] =& $mform->createElement('radio', 'length', '', get_string('horizontal', 'questionnaire'), '1');
        $mform->addGroup($lengroup, 'lengroup', get_string('alignment', 'questionnaire'), ' ', false);
        $mform->addHelpButton('lengroup', 'alignment', 'questionnaire');
        $mform->setType('length', PARAM_INT);

        return $mform;
    }

    /**
     * Return the precision form element.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        return question::form_precise_hidden($mform);
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
     * Override and return false if not supporting mobile app.
     * @param int $qnum
     * @param bool $autonum
     * @return \stdClass
     */
    public function mobile_question_display($qnum, $autonum = false) {
        $mobiledata = parent::mobile_question_display($qnum, $autonum);
        $mobiledata->isradiobutton = true;
        return $mobiledata;
    }

    /**
     * Override and return false if not supporting mobile app.
     * @return array
     */
    public function mobile_question_choices_display() {
        $choices = parent::mobile_question_choices_display();
        foreach ($choices as $choicenum => $choice) {
            if ($choice->is_other_choice()) {
                $choices[$choicenum]->otherchoicekey = $this->mobile_fieldkey($choice->other_choice_name());
                $choices[$choicenum]->content = format_text($choice->other_choice_display(), FORMAT_HTML, ['noclean' => true]);
            }
        }
        return $choices;
    }

    /**
     * Return the mobile response data.
     * @param response $response
     * @return array
     */
    public function get_mobile_response_data($response) {
        $resultdata = [];
        if (isset($response->answers[$this->id])) {
            foreach ($response->answers[$this->id] as $answer) {
                // Add a fieldkey for each choice.
                $resultdata[$this->mobile_fieldkey()] = $answer->choiceid;
                if ($this->choices[$answer->choiceid]->is_other_choice()) {
                    $resultdata[$this->mobile_fieldkey($this->choices[$answer->choiceid]->other_choice_name())] = $answer->value;
                }
            }
        }
        return $resultdata;
    }
}
