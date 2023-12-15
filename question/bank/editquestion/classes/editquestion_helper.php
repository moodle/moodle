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
 * Helper class for adding/editing a question.
 *
 * This code is based on question/editlib.php by Martin Dougiamas.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_editquestion;

use core_question\local\bank\question_version_status;
use qbank_editquestion\output\add_new_question;

/**
 * Class editquestion_helper for methods related to add/edit/copy
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 */
class editquestion_helper {

    /**
     * Print a form to let the user choose which question type to add.
     * When the form is submitted, it goes to the question.php script.
     *
     * @param array|null $hiddenparams hidden parameters to add to the form, in addition to
     *      the qtype radio buttons.
     * @param array|null $allowedqtypes optional list of qtypes that are allowed. If given, only
     *      those qtypes will be shown. Example value array('description', 'multichoice').
     * @param bool $enablejs
     * @return bool|string
     */
    public static function print_choose_qtype_to_add_form(array $hiddenparams, array $allowedqtypes = null, $enablejs = true) {
        global $PAGE;

        $chooser = \qbank_editquestion\qbank_chooser::get($PAGE->course, $hiddenparams, $allowedqtypes);
        $renderer = $PAGE->get_renderer('qbank_editquestion');

        return $renderer->render($chooser);
    }

    /**
     * Print a button for creating a new question. This will open question/addquestion.php,
     * which in turn goes to question/question.php before getting back to $params['returnurl']
     * (by default the question bank screen).
     *
     * @param int $categoryid The id of the category that the new question should be added to.
     * @param array $params Other paramters to add to the URL. You need either $params['cmid'] or
     *      $params['courseid'], and you should probably set $params['returnurl']
     * @param bool $canadd the text to display on the button.
     * @param string $tooltip a tooltip to add to the button (optional).
     * @param bool $disabled if true, the button will be disabled.
     * @deprecated since Moodle 4.3. Use {@see add_new_question} renderable instead
     * @todo Final deprecation in Moodle 4.7
     */
    public static function create_new_question_button($categoryid, $params, $canadd, $tooltip = '', $disabled = false) {
        global $OUTPUT;
        debugging('create_new_question_button() is deprecated. Use the add_new_question renderable instead.');
        return $OUTPUT->render(new add_new_question($categoryid, $params, $canadd));
    }

    /**
     * Get the string for the status of the question.
     *
     * @param string $status
     * @return string
     */
    public static function get_question_status_string($status): string {
        return get_string('questionstatus' . $status, 'qbank_editquestion');
    }

    /**
     * Get the array of status of the questions.
     *
     * @return array
     */
    public static function get_question_status_list(): array {
        $statuslist = [];
        $statuslist[question_version_status::QUESTION_STATUS_READY] = get_string('questionstatusready', 'qbank_editquestion');
        $statuslist[question_version_status::QUESTION_STATUS_DRAFT] = get_string('questionstatusdraft', 'qbank_editquestion');
        return $statuslist;
    }

}
