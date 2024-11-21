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
use mod_questionnaire\edit_question_form;
use \questionnaire;

/**
 * This file contains the parent class for pagebreak question types.
 *
 * @author Mike Churchward
 * @copyright  2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class pagebreak extends question {

    /**
     * Each question type must define its response class.
     * @return object The response object based off of questionnaire_response_base.
     */
    protected function responseclass() {
        return '';
    }

    /**
     * Short name for this question type - no spaces, etc..
     * @return string
     */
    public function helpname() {
        return '';
    }

    /**
     * Get the output for the start of the questions in a survey.
     * @param int $qnum
     * @param \mod_questionnaire\responsetype\response\response $response
     * @return \stdClass
     */
    public function questionstart_survey_display($qnum, $response=null) {
        return '';
    }

    /**
     * Question specific display method.
     * @param \stdClass $data
     * @param array $descendantsdata
     * @param bool $blankquestionnaire
     *
     */
    protected function question_survey_display($data, $descendantsdata, $blankquestionnaire=false) {
        return '';
    }

    /**
     * Question specific response display method.
     * @param \stdClass $data
     *
     */
    protected function response_survey_display($data) {
        return '';
    }

    /**
     * Override this, or any of the internal methods, to provide specific form data for editing the question type.
     * The structure of the elements here is the default layout for the question form.
     * @param edit_question_form $form The main moodleform object.
     * @param questionnaire $questionnaire The questionnaire being edited.
     * @return bool
     */
    public function edit_form(edit_question_form $form, questionnaire $questionnaire) {
        return false;
    }

    /**
     * True if question provides mobile support.
     * @return bool
     */
    public function supports_mobile() {
        return false;
    }

    /**
     * Override and return false if not supporting mobile app.
     * @param int $qnum
     * @param bool $autonum
     * @return \stdClass
     */
    public function mobile_question_display($qnum, $autonum = false) {
        return false;
    }

    /**
     * Override and return false if a number should not be rendered for this question in any context.
     * @return bool
     */
    public function is_numbered() {
        return false;
    }
}
