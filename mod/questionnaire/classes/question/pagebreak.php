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
 * This file contains the parent class for pagebreak question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
use mod_questionnaire\edit_question_form;
use \questionnaire;
defined('MOODLE_INTERNAL') || die();

class pagebreak extends base {

    protected function responseclass() {
        return '';
    }

    public function helpname() {
        return '';
    }

    protected function question_survey_display($data, $descendantsdata, $blankquestionnaire=false) {
        return '';
    }

    protected function response_survey_display($data) {
        return '';
    }

    public function edit_form(edit_question_form $form, questionnaire $questionnaire) {
        return false;
    }
}