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
 * This file contains the parent class for date question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
defined('MOODLE_INTERNAL') || die();
use \html_writer;

class date extends base {

    protected function responseclass() {
        return '\\mod_questionnaire\\response\\date';
    }

    public function helpname() {
        return 'date';
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_date';
    }

    /**
     * Override and return a form template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function response_template() {
        return 'mod_questionnaire/response_date';
    }

    /**
     * Return the context tags for the check question template.
     * @param object $data
     * @param string $descendantdata
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($data, $descendantsdata, $blankquestionnaire=false) {
        // Date.
        $questiontags = new \stdClass();
        if (!empty($data->{'q'.$this->id})) {
            $dateentered = $data->{'q'.$this->id};
            $setdate = questionnaire_check_date ($dateentered, false);
            if ($setdate == 'wrongdateformat') {
                $msg = get_string('wrongdateformat', 'questionnaire', $dateentered);
                $this->add_notification($msg);
            } else if ($setdate == 'wrongdaterange') {
                $msg = get_string('wrongdaterange', 'questionnaire');
                $this->add_notification($msg);
            } else {
                $data->{'q'.$this->id} = $setdate;
            }
        }
        $choice = new \stdClass();
        $choice->onkeypress = 'return event.keyCode != 13;';
        $choice->name = 'q'.$this->id;
        $choice->value = (isset($data->{'q'.$this->id}) ? $data->{'q'.$this->id} : '');
        $questiontags->qelements = new \stdClass();
        $questiontags->qelements->choice = $choice;
        return $questiontags;
    }

    /**
     * Return the context tags for the check response template.
     * @param object $data
     * @return object The check question response context tags.
     *
     */
    protected function response_survey_display($data) {
        $resptags = new \stdClass();
        if (isset($data->{'q'.$this->id})) {
            $resptags->content = $data->{'q'.$this->id};
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
        if (isset($responsedata->{'q'.$this->id})) {
            $checkdateresult = '';
            if ($responsedata->{'q'.$this->id} != '') {
                $checkdateresult = questionnaire_check_date($responsedata->{'q'.$this->id});
            }
            return (substr($checkdateresult, 0, 5) != 'wrong');
        } else {
            return parent::response_valid($responsedata);
        }
    }

    protected function form_length(\MoodleQuickForm $mform, $helpname = '') {
        return base::form_length_hidden($mform);
    }

    protected function form_precise(\MoodleQuickForm $mform, $helpname = '') {
        return base::form_precise_hidden($mform);
    }
}