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
 * This file contains the parent class for slider question types.
 *
 * @author Hieu Vu Van
 * @copyright 2022 The Open University.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class slider extends question {

    /**
     * Return the responseclass used.
     * @return string
     */
    protected function responseclass() {
        return '\\mod_questionnaire\\responsetype\\slider';
    }

    /**
     * Return the help name.
     * @return string
     */
    public function helpname() {
        return 'slider';
    }

    /**
     * Return true if the question has choices.
     */
    public function has_choices() {
        return false;
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     *
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_slider';
    }

    /**
     * Override and return a response template if provided. Output of response_survey_display is iterpreted based on this.
     *
     * @return boolean | string
     */
    public function response_template() {
        return 'mod_questionnaire/response_slider';
    }

    /**
     * True if question type supports feedback options. False by default.
     * @return bool
     */
    public function supports_feedback() {
        return true;
    }

    /**
     * True if the question supports feedback and has valid settings for feedback. Override if the default logic is not enough.
     * @return bool
     */
    public function valid_feedback() {
        $extradata = json_decode($this->extradata);
        $minrange = $extradata->minrange;
        // Negative scores are not accepted in Feedback.
        return $this->supports_feedback() && !empty($this->name) && $minrange >= 0;
    }

    /**
     * Get the maximum score possible for feedback if appropriate. Override if default behaviour is not correct.
     * @return int | boolean
     */
    public function get_feedback_maxscore() {
        if ($this->valid_feedback()) {
            $extradata = json_decode($this->extradata);
            $maxscore = $extradata->maxrange;
        } else {
            $maxscore = false;
        }
        return $maxscore;
    }

    /**
     * Return the context tags for the check question template.
     *
     * @param \mod_questionnaire\responsetype\response\response $response
     * @param array $dependants Array of all questions/choices depending on this question.
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($response, $dependants = [], $blankquestionnaire = false) {
        global $PAGE;
        $PAGE->requires->js_init_call('M.mod_questionnaire.init_slider', null, false, questionnaire_get_js_module());
        $extradata = json_decode($this->extradata);
        $questiontags = new \stdClass();
        if (isset($response->answers[$this->id][0])) {
            $extradata->startingvalue = $response->answers[$this->id][0]->value;
        }
        $extradata->name = 'q' . $this->id;
        $extradata->id = self::qtypename($this->type_id) . $this->id;
        $questiontags->qelements = new \stdClass();
        $questiontags->qelements->extradata = $extradata;
        return $questiontags;
    }

    /**
     * Return the context tags for the slider response template.
     * @param \mod_questionnaire\responsetype\response\response $response
     * @return \stdClass The check question response context tags.
     */
    protected function response_survey_display($response) {
        global $PAGE;
        $PAGE->requires->js_init_call('M.mod_questionnaire.init_slider', null, false, questionnaire_get_js_module());

        $resptags = new \stdClass();
        if (isset($response->answers[$this->id])) {
            $answer = reset($response->answers[$this->id]);
            $resptags->content = format_text($answer->value, FORMAT_HTML);
            if (!empty($response->answers[$this->id]['extradata'])) {
                $resptags->extradata = $response->answers[$this->id]['extradata'];
            } else {
                $extradata = json_decode($this->extradata);
                $resptags->extradata = $extradata;
            }
        }
        return $resptags;
    }

    /**
     * Add the form required field.
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm
     */
    protected function form_required(\MoodleQuickForm $mform) {
        return $mform;
    }

    /**
     * Return the form precision.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     * @return \MoodleQuickForm|void
     */
    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        return question::form_precise_hidden($mform);
    }

    /**
     * Return the form length.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     * @return \MoodleQuickForm|void
     */
    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        return question::form_length_hidden($mform);
    }

    /**
     * Override if the question uses the extradata field.
     * @param \MoodleQuickForm $mform
     * @param string $helpname
     * @return \MoodleQuickForm
     */
    protected function form_extradata(\MoodleQuickForm $mform, $helpname = '') {
        $minelementname = 'minrange';
        $maxelementname = 'maxrange';
        $startingvalue = 'startingvalue';
        $stepvalue = 'stepvalue';

        $ranges = [];
        if (!empty($this->extradata)) {
            $ranges = json_decode($this->extradata);
        }
        $mform->addElement('text', 'leftlabel', get_string('leftlabel', 'questionnaire'));
        $mform->setType('leftlabel', PARAM_RAW);
        if (isset($ranges->leftlabel)) {
            $mform->setDefault('leftlabel', $ranges->leftlabel);
        }
        $mform->addElement('text', 'centerlabel', get_string('centerlabel', 'questionnaire'));
        $mform->setType('centerlabel', PARAM_RAW);
        if (isset($ranges->centerlabel)) {
            $mform->setDefault('centerlabel', $ranges->centerlabel);
        }
        $mform->addElement('text', 'rightlabel', get_string('rightlabel', 'questionnaire'));
        $mform->setType('rightlabel', PARAM_RAW);
        if (isset($ranges->rightlabel)) {
            $mform->setDefault('rightlabel', $ranges->rightlabel);
        }

        $patterint = '/^-?\d+$/';
        $mform->addElement('text', $minelementname, get_string($minelementname, 'questionnaire'), ['size' => '3']);
        $mform->setType($minelementname, PARAM_RAW);
        $mform->addRule($minelementname, get_string('err_required', 'form'), 'required', null, 'client');
        $mform->addRule($minelementname, get_string('err_numeric', 'form'), 'numeric', '', 'client');
        $mform->addRule($minelementname, get_string('err_numeric', 'form'), 'regex', $patterint, 'client');
        $mform->addHelpButton($minelementname, $minelementname, 'questionnaire');
        if (isset($ranges->minrange)) {
            $mform->setDefault($minelementname, $ranges->minrange);
        } else {
            $mform->setDefault($minelementname, 1);
        }

        $mform->addElement('text', $maxelementname, get_string($maxelementname, 'questionnaire'), ['size' => '3']);
        $mform->setType($maxelementname, PARAM_RAW);
        $mform->addHelpButton($maxelementname, $maxelementname, 'questionnaire');
        $mform->addRule($maxelementname, get_string('err_required', 'form'), 'required', null, 'client');
        $mform->addRule($maxelementname, get_string('err_numeric', 'form'), 'numeric', '', 'client');
        $mform->addRule($maxelementname, get_string('err_numeric', 'form'), 'regex', $patterint, 'client');
        if (isset($ranges->maxrange)) {
            $mform->setDefault($maxelementname, $ranges->maxrange);
        } else {
            $mform->setDefault($maxelementname, 10);
        }

        $mform->addElement('text', $startingvalue, get_string($startingvalue, 'questionnaire'), ['size' => '3']);
        $mform->setType($startingvalue, PARAM_RAW);
        $mform->addHelpButton($startingvalue, $startingvalue, 'questionnaire');
        $mform->addRule($startingvalue, get_string('err_required', 'form'), 'required', null, 'client');
        $mform->addRule($startingvalue, get_string('err_numeric', 'form'), 'numeric', '', 'client');
        $mform->addRule($startingvalue, get_string('err_numeric', 'form'), 'regex', $patterint, 'client');
        if (isset($ranges->startingvalue)) {
            $mform->setDefault($startingvalue, $ranges->startingvalue);
        } else {
            $mform->setDefault($startingvalue, 5);
        }

        $mform->addElement('text', $stepvalue, get_string($stepvalue, 'questionnaire'), ['size' => '3']);
        $mform->setType($stepvalue, PARAM_RAW);
        $mform->addHelpButton($stepvalue, $stepvalue, 'questionnaire');
        $mform->addRule($stepvalue, get_string('err_required', 'form'), 'required', null, 'client');
        $mform->addRule($stepvalue, get_string('err_numeric', 'form'), 'numeric', '', 'client');
        $mform->addRule($stepvalue, get_string('err_numeric', 'form'), 'regex', '/^-?\d+$/', 'client');

        if (isset($ranges->stepvalue)) {
            $mform->setDefault($stepvalue, $ranges->stepvalue);
        } else {
            $mform->setDefault($stepvalue, 1);
        }
        return $mform;
    }

    /**
     * Any preprocessing of general data.
     * @param \stdClass $formdata
     * @return bool
     */
    protected function form_preprocess_data($formdata) {
        $ranges = [];
        if (isset($formdata->minrange)) {
            $ranges['minrange'] = $formdata->minrange;
        }
        if (isset($formdata->maxrange)) {
            $ranges['maxrange'] = $formdata->maxrange;
        }
        if (isset($formdata->startingvalue)) {
            $ranges['startingvalue'] = $formdata->startingvalue;
        }
        if (isset($formdata->stepvalue)) {
            $ranges['stepvalue'] = $formdata->stepvalue;
        }
        if (isset($formdata->leftlabel)) {
            $ranges['leftlabel'] = $formdata->leftlabel;
        }
        if (isset($formdata->rightlabel)) {
            $ranges['rightlabel'] = $formdata->rightlabel;
        }
        if (isset($formdata->centerlabel)) {
            $ranges['centerlabel'] = $formdata->centerlabel;
        }

        // Now store the new named degrees in extradata.
        $formdata->extradata = json_encode($ranges);
        return parent::form_preprocess_data($formdata);
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
     * True if question need extradata for mobile app.
     *
     * @return bool
     */
    public function mobile_question_extradata_display() {
        return true;
    }

    /**
     * Return the mobile question display.
     *
     * @param int $qnum
     * @param bool $autonum
     * @return \stdClass
     */
    public function mobile_question_display($qnum, $autonum = false) {
        $mobiledata = parent::mobile_question_display($qnum, $autonum);
        $mobiledata->isslider = true;
        return $mobiledata;
    }

    /**
     * Return the otherdata to be used by the mobile app.
     *
     * @return array
     */
    public function mobile_otherdata() {
        $extradata = json_decode($this->extradata);
        return [$this->mobile_fieldkey() => $extradata->startingvalue];
    }
}
