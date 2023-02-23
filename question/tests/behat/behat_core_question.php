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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/behat_question_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Behat navigation hooks for core_question.
 *
 * @package    core_question
 * @category   test
 * @copyright  2022 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_question extends behat_question_base {

    /**
     * Convert page names to URLs for steps like 'When I am on the "[page name]" page'.
     *
     * Recognised page names are:
     * | None so far!      |                                                              |
     *
     * @param string $page name of the page, with the component name removed e.g. 'Admin notification'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_url(string $page): moodle_url {
        switch (strtolower($page)) {
            default:
                throw new Exception('Unrecognised core_question page type "' . $page . '."');
        }
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype               | name meaning               | description                              |
     * | course question bank   | Course name                | The question bank for a course           |
     * | course question import | Course name                | The import questions screen for a course |
     * | course question export | Course name                | The export questions screen for a course |
     * | preview                | Question name              | The screen to preview a question         |
     * | edit                   | Question name              | The screen to edit a question            |
     *
     * @param string $type identifies which type of page this is, e.g. 'Preview'.
     * @param string $identifier identifies the particular page, e.g. 'My question'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        switch (strtolower($type)) {
            case 'course question bank':
                return new moodle_url('/question/edit.php',
                        ['courseid' => $this->get_course_id($identifier)]);

            case 'course question categories':
                return new moodle_url('/question/category.php',
                        ['courseid' => $this->get_course_id($identifier)]);

            case 'course question import':
                return new moodle_url('/question/import.php',
                        ['courseid' => $this->get_course_id($identifier)]);

            case 'course question export':
                return new moodle_url('/question/export.php',
                        ['courseid' => $this->get_course_id($identifier)]);

            case 'preview':
                [$questionid, $otheridtype, $otherid] = $this->find_question_by_name($identifier);
                return new moodle_url('/question/preview.php',
                        ['id' => $questionid, $otheridtype => $otherid]);

            case 'edit':
                [$questionid, $otheridtype, $otherid] = $this->find_question_by_name($identifier);
                return new moodle_url('/question/question.php',
                        ['id' => $questionid, $otheridtype => $otherid]);

            default:
                throw new Exception('Unrecognised core_question page type "' . $type . '."');
        }
    }

    /**
     * Find a question, and where it is, from the question name.
     *
     * This is a helper used by resolve_page_instance_url.
     *
     * @param string $questionname
     * @return array with three elemnets, int question id, a string 'cmid' or 'courseid',
     *     and int either cmid or courseid as applicable.
     */
    protected function find_question_by_name(string $questionname): array {
        global $DB;
        $questionid = $DB->get_field('question', 'id', ['name' => $questionname], MUST_EXIST);
        $question = question_bank::load_question_data($questionid);
        $context = context_helper::instance_by_id($question->contextid);

        if ($context->contextlevel == CONTEXT_MODULE) {
            return [$questionid, 'cmid', $context->instanceid];
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            return [$questionid, 'courseid', $context->instanceid];
        } else {
            throw new coding_exception('Unsupported context level ' . $context->contextlevel);
        }
    }

    /**
     * Change the question type of the give question to a type that does not exist.
     *
     * This is useful for testing robustness of the code when a question type
     * has been uninstalled, even though there are still questions of that type
     * or attempts at them.
     *
     * In order to set things up, you probably need to start by generating
     * questions of a valid type, then using this to change the type once the
     * data is created.
     *
     * @Given question :questionname is changed to simulate being of an uninstalled type
     * @param string $questionname the question name.
     */
    public function change_question_to_nonexistant_type($questionname) {
        global $DB;
        [$id] = $this->find_question_by_name($questionname);

        // Check our assumption.
        $nonexistanttype = 'invalidqtype';
        if (question_bank::is_qtype_installed($nonexistanttype)) {
            throw new coding_exception('This code assumes that the qtype_' . $nonexistanttype .
                    ' is not a valid plugin name, but that plugin now seems to exist!');
        }

        $DB->set_field('question', 'qtype', $nonexistanttype, ['id' => $id]);
        question_bank::notify_question_edited($id);
    }

    /**
     * Forcibly delete a question from the database.
     *
     * This is useful for testing robustness of the code when a question
     * record is no longer in the database, even though it is referred to.
     * Obviously, this should never happen, but it has been known to in the past
     * and so we sometimes need to be able to test the code can handle this situation.
     *
     * In order to set things up, you probably need to start by generating
     * a valid questions, then using this to remove it once the data is created.
     *
     * @Given question :questionname no longer exists in the database
     * @param string $questionname the question name.
     */
    public function remove_question_from_db($questionname) {
        global $DB;
        [$id] = $this->find_question_by_name($questionname);
        $DB->delete_records('question', ['id' => $id]);
        question_bank::notify_question_edited($id);
    }
}
