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

namespace mod_quiz\external;

use core_external\external_api;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_function_parameters;
use core_external\external_value;
use core_user_external;
use core_external\external_warnings;
use mod_quiz\quiz_settings;
use user_picture;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');

/**
 * Web service to get user in quiz report.
 *
 * @package    mod_quiz
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_users_in_report extends external_api {

    /** @var int Maximum number of students that can be shown on one page */
    public const MAX_STUDENTS_PER_PAGE = 5000;
    /** @var \context_module $context Context module object */
    public static $context;


    /**
     * Returns description of parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The cmid.'),
            'mode' => new external_value(PARAM_TEXT, 'Report mode'),
            'tableclass' => new external_value(PARAM_TEXT, 'Class of table'),
            'optionclass' => new external_value(PARAM_TEXT, 'Class of report setting'),
            'attempts' => new external_value(PARAM_TEXT, 'Attempts filter option'),
            'states' => new external_value(PARAM_TEXT, 'States filter option'),
            'onlygraded' => new external_value(PARAM_BOOL, 'Only graded filter option'),
        ]);
    }

    /**
     * Retrieve the user dataset for the quiz report.
     *
     * @param int $cmid the cmid.
     * @param string $mode the report mode.
     * @param string $tableclass the table class.
     * @param string $optionclass the report setting class.
     * @param string $attempts Filter attempts type.
     * @param string $states States report attempts type.
     * @param bool $onlygraded Only graded attempt filter.
     * @return array User list.
     */
    public static function execute(int $cmid, string $mode, string $tableclass,
            string $optionclass, string $attempts, string $states, bool $onlygraded): array {
        global $CFG, $PAGE, $DB, $SESSION;

        $warnings = [];
        $users = [];
        self::validate_parameters(
            self::execute_parameters(),
            [
                'cmid' => $cmid,
                'mode' => $mode,
                'tableclass' => $tableclass,
                'optionclass' => $optionclass,
                'attempts' => $attempts,
                'states' => $states,
                'onlygraded' => $onlygraded,
            ],
        );

        // Open the selected quiz report and display it.
        $file = $CFG->dirroot . '/mod/quiz/report/' . $mode . '/report.php';
        if (is_readable($file)) {
            include_once($file);
        }
        $reportclassname = 'quiz_' . $mode . '_report';
        if (!class_exists($reportclassname)) {
            throw new \moodle_exception('preprocesserror', 'quiz');
        }

        $quizobj = quiz_settings::create_for_cmid($cmid);
        $quiz = $quizobj->get_quiz();
        $cm = $quizobj->get_cm();
        $course = $quizobj->get_course();

        $report = new $reportclassname();
        $context = $quizobj->get_context();
        static::$context = $context;
        $PAGE->set_context($context);
        [$currentgroup, $studentsjoins, $groupstudentsjoins, $allowedjoins] = $report->init(
            $mode, 'quiz_' . $mode . '_settings_form', $quiz, $cm, $course);
        $qmsubselect = quiz_report_qm_filter_select($quiz);
        $options = new $optionclass($mode, $quiz, $cm, $course);
        // Set up filter for report.
        if (!empty($states)) {
            $states = explode('-', $states);
            $options->states = $states;
        }
        if (!empty($attempts)) {
            $options->attempts = $attempts;
        }
        if ($onlygraded) {
            $options->onlygraded = $onlygraded;
        }
        $questions = quiz_report_get_significant_questions($quiz);
        $table = new $tableclass($quiz, $context, $qmsubselect,
            $options, $groupstudentsjoins, $studentsjoins, $questions, $options->get_url());
        // Since the column setup is unnecessary, we have manually configured the setup flag.
        $table->setup = true;
        // We need to filter by either their first name or last name.
        if (!empty($SESSION->flextable[$table->uniqueid]['i_first'] ?? '')) {
            $allowedjoins->wheres .= " AND " . $DB->sql_like('firstname', ':ifirstc', false, false);
            $allowedjoins->params['ifirstc'] = $SESSION->flextable[$table->uniqueid]['i_first'] . '%';
        }
        if (!empty($SESSION->flextable[$table->uniqueid]['i_last'] ?? '')) {
            $allowedjoins->wheres .= " AND " . $DB->sql_like('lastname', ':ilastc', false, false);
            $allowedjoins->params['ilastc'] = $SESSION->flextable[$table->uniqueid]['i_last'] . '%';
        }
        $table->setup_sql_queries($allowedjoins);
        $table->query_db(static::MAX_STUDENTS_PER_PAGE);
        $userfieldsapi = \core_user\fields::for_identity($context)->with_userpic();
        $allowfields = $userfieldsapi->get_required_fields();
        $useridstmp = [];
        $users = array_reduce($table->rawdata, function($response, $user) use ($PAGE, $allowfields, &$useridstmp) {
            // We need to remove duplicate users in the report.
            if (!in_array($user->userid, $useridstmp)) {
                $userref = new \stdClass();
                foreach ($allowfields as $field) {
                    $userref->{$field} = $user->{$field} ?? '';
                }
                $userref->id = $user->userid;
                $userref->fullname = fullname($userref);
                $userpicture = new user_picture($userref);
                $userpicture->size = 1;
                $userref->profileimageurl = $userpicture->get_url($PAGE)->out(false);
                $userpicture->size = 0; // Size f2.
                $userref->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);
                $useridstmp[] = $user->userid;
                $response[] = $userref;
            }

            return $response;
        }, []);

        return [
            'users' => $users,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description and structure of what the users and warnings should return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        global $CFG;
        require_once($CFG->dirroot . '/user/externallib.php');

        // Additional names.
        $additionalfields = [
            'username' => new external_value(\core_user::get_property_type('username'), 'The username',
                VALUE_OPTIONAL),
        ];
        $userfieldsapi = \core_user\fields::for_identity(static::$context)->with_userpic();
        $extrasearchfields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);
        foreach ($extrasearchfields as $field) {
            $additionalfields[$field] = new external_value(PARAM_RAW, '');
        }

        return new external_single_structure([
            'users' => new external_multiple_structure(core_user_external::user_description($additionalfields)),
            'warnings' => new external_warnings(),
        ]);
    }
}
