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
 * Steps definitions related to mod_quiz.
 *
 * @package   mod_quiz
 * @category  test
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../question/tests/behat/behat_question_base.php');

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;

/**
 * Steps definitions related to mod_quiz.
 *
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_quiz extends behat_question_base {

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
                throw new Exception('Unrecognised quiz page type "' . $page . '."');
        }
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype          | name meaning                                | description                                  |
     * | View              | Quiz name                                   | The quiz info page (view.php)                |
     * | Edit              | Quiz name                                   | The edit quiz page (edit.php)                |
     * | Group overrides   | Quiz name                                   | The manage group overrides page              |
     * | User overrides    | Quiz name                                   | The manage user overrides page               |
     * | Grades report     | Quiz name                                   | The overview report for a quiz               |
     * | Responses report  | Quiz name                                   | The responses report for a quiz              |
     * | Manual grading report | Quiz name                               | The manual grading report for a quiz         |
     * | Statistics report | Quiz name                                   | The statistics report for a quiz             |
     * | Attempt review    | Quiz name > username > [Attempt] attempt no | Review page for a given attempt (review.php) |
     * | Question bank     | Quiz name                                   | The question bank page for a quiz            |
     *
     * @param string $type identifies which type of page this is, e.g. 'Attempt review'.
     * @param string $identifier identifies the particular page, e.g. 'Test quiz > student > Attempt 1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        global $DB;

        switch (strtolower($type)) {
            case 'view':
                return new moodle_url('/mod/quiz/view.php',
                        ['id' => $this->get_cm_by_quiz_name($identifier)->id]);

            case 'edit':
                return new moodle_url('/mod/quiz/edit.php',
                        ['cmid' => $this->get_cm_by_quiz_name($identifier)->id]);

            case 'multiple grades setup':
                return new moodle_url('/mod/quiz/editgrading.php',
                        ['cmid' => $this->get_cm_by_quiz_name($identifier)->id]);

            case 'group overrides':
                return new moodle_url('/mod/quiz/overrides.php',
                    ['cmid' => $this->get_cm_by_quiz_name($identifier)->id, 'mode' => 'group']);

            case 'user overrides':
                return new moodle_url('/mod/quiz/overrides.php',
                    ['cmid' => $this->get_cm_by_quiz_name($identifier)->id, 'mode' => 'user']);

            case 'grades report':
                return new moodle_url('/mod/quiz/report.php',
                    ['id' => $this->get_cm_by_quiz_name($identifier)->id, 'mode' => 'overview']);

            case 'responses report':
                return new moodle_url('/mod/quiz/report.php',
                    ['id' => $this->get_cm_by_quiz_name($identifier)->id, 'mode' => 'responses']);

            case 'statistics report':
                return new moodle_url('/mod/quiz/report.php',
                    ['id' => $this->get_cm_by_quiz_name($identifier)->id, 'mode' => 'statistics']);

            case 'manual grading report':
                return new moodle_url('/mod/quiz/report.php',
                        ['id' => $this->get_cm_by_quiz_name($identifier)->id, 'mode' => 'grading']);
            case 'attempt view':
                list($quizname, $username, $attemptno, $pageno) = explode(' > ', $identifier);
                $pageno = intval($pageno);
                $pageno = $pageno > 0 ? $pageno - 1 : 0;
                $attemptno = (int) trim(str_replace ('Attempt', '', $attemptno));
                $quiz = $this->get_quiz_by_name($quizname);
                $quizcm = get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course);
                $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
                $attempt = $DB->get_record('quiz_attempts',
                    ['quiz' => $quiz->id, 'userid' => $user->id, 'attempt' => $attemptno], '*', MUST_EXIST);
                return new moodle_url('/mod/quiz/attempt.php', [
                    'attempt' => $attempt->id,
                    'cmid' => $quizcm->id,
                    'page' => $pageno
                ]);
            case 'attempt review':
                if (substr_count($identifier, ' > ') !== 2) {
                    throw new coding_exception('For "attempt review", name must be ' .
                            '"{Quiz name} > {username} > Attempt {attemptnumber}", ' .
                            'for example "Quiz 1 > student > Attempt 1".');
                }
                list($quizname, $username, $attemptno) = explode(' > ', $identifier);
                $attemptno = (int) trim(str_replace ('Attempt', '', $attemptno));
                $quiz = $this->get_quiz_by_name($quizname);
                $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
                $attempt = $DB->get_record('quiz_attempts',
                        ['quiz' => $quiz->id, 'userid' => $user->id, 'attempt' => $attemptno], '*', MUST_EXIST);
                return new moodle_url('/mod/quiz/review.php', ['attempt' => $attempt->id]);

            case 'question bank':
                // The question bank does not handle fields at the edge of the viewport well.
                // Increase the size to avoid this.
                $this->execute('behat_general::i_change_window_size_to', ['window', 'large']);
                return new moodle_url('/question/edit.php', [
                    'cmid' => $this->get_cm_by_quiz_name($identifier)->id,
                ]);
            case 'question categories':
                return new moodle_url('/question/bank/managecategories/category.php', [
                    'cmid' => $this->get_cm_by_quiz_name($identifier)->id,
                ]);

            default:
                throw new Exception('Unrecognised quiz page type "' . $type . '."');
        }
    }

    /**
     * Get a quiz by name.
     *
     * @param string $name quiz name.
     * @return stdClass the corresponding DB row.
     */
    protected function get_quiz_by_name(string $name): stdClass {
        global $DB;
        return $DB->get_record('quiz', ['name' => $name], '*', MUST_EXIST);
    }

    /**
     * Get a quiz cmid from the quiz name.
     *
     * @param string $name quiz name.
     * @return stdClass cm from get_coursemodule_from_instance.
     */
    protected function get_cm_by_quiz_name(string $name): stdClass {
        $quiz = $this->get_quiz_by_name($name);
        return get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course);
    }

    /**
     * Put the specified questions on the specified pages of a given quiz.
     *
     * The first row should be column names:
     * | question | page | maxmark | requireprevious |
     * The first two of those are required. The others are optional.
     *
     * question        needs to uniquely match a question name.
     * page            is a page number. Must start at 1, and on each following
     *                 row should be the same as the previous, or one more.
     * maxmark         What the question is marked out of. Defaults to question.defaultmark.
     * requireprevious The question can only be attempted after the previous one was completed.
     *
     * Then there should be a number of rows of data, one for each question you want to add.
     *
     * For backwards-compatibility reasons, specifying the column names is optional
     * (but strongly encouraged). If not specified, the columns are asseumed to be
     * | question | page | maxmark |.
     *
     * @param string $quizname the name of the quiz to add questions to.
     * @param TableNode $data information about the questions to add.
     *
     * @Given /^quiz "([^"]*)" contains the following questions:$/
     */
    public function quiz_contains_the_following_questions($quizname, TableNode $data) {
        global $DB;

        $quiz = $this->get_quiz_by_name($quizname);

        // Deal with backwards-compatibility, optional first row.
        $firstrow = $data->getRow(0);
        if (!in_array('question', $firstrow) && !in_array('page', $firstrow)) {
            if (count($firstrow) == 2) {
                $headings = ['question', 'page'];
            } else if (count($firstrow) == 3) {
                $headings = ['question', 'page', 'maxmark'];
            } else {
                throw new ExpectationException('When adding questions to a quiz, you should give 2 or three 3 things: ' .
                        ' the question name, the page number, and optionally the maximum mark. ' .
                        count($firstrow) . ' values passed.', $this->getSession());
            }
            $rows = $data->getRows();
            array_unshift($rows, $headings);
            $data = new TableNode($rows);
        }

        // Add the questions.
        $lastpage = 0;
        foreach ($data->getHash() as $questiondata) {
            if (!array_key_exists('question', $questiondata)) {
                throw new ExpectationException('When adding questions to a quiz, ' .
                        'the question name column is required.', $this->getSession());
            }
            if (!array_key_exists('page', $questiondata)) {
                throw new ExpectationException('When adding questions to a quiz, ' .
                        'the page number column is required.', $this->getSession());
            }

            // Question id, category and type.
            $sql = 'SELECT q.id AS id, qbe.questioncategoryid AS category, q.qtype AS qtype
                      FROM {question} q
                      JOIN {question_versions} qv ON qv.questionid = q.id
                      JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                     WHERE q.name = :name';
            $question = $DB->get_record_sql($sql, ['name' => $questiondata['question']], MUST_EXIST);

            // Page number.
            $page = clean_param($questiondata['page'], PARAM_INT);
            if ($page <= 0 || (string) $page !== $questiondata['page']) {
                throw new ExpectationException('The page number for question "' .
                         $questiondata['question'] . '" must be a positive integer.',
                        $this->getSession());
            }
            if ($page < $lastpage || $page > $lastpage + 1) {
                throw new ExpectationException('When adding questions to a quiz, ' .
                        'the page number for each question must either be the same, ' .
                        'or one more, then the page number for the previous question.',
                        $this->getSession());
            }
            $lastpage = $page;

            // Max mark.
            if (!array_key_exists('maxmark', $questiondata) || $questiondata['maxmark'] === '') {
                $maxmark = null;
            } else {
                $maxmark = clean_param($questiondata['maxmark'], PARAM_LOCALISEDFLOAT);
                if (!is_numeric($maxmark) || $maxmark < 0) {
                    throw new ExpectationException('The max mark for question "' .
                            $questiondata['question'] . '" must be a positive number.',
                            $this->getSession());
                }
            }

            if ($question->qtype == 'random') {
                if (!array_key_exists('includingsubcategories', $questiondata) || $questiondata['includingsubcategories'] === '') {
                    $includingsubcategories = false;
                } else {
                    $includingsubcategories = clean_param($questiondata['includingsubcategories'], PARAM_BOOL);
                }

                $filter = [
                    'category' => [
                        'jointype' => \qbank_managecategories\category_condition::JOINTYPE_DEFAULT,
                        'values' => [$question->category],
                        'filteroptions' => ['includesubcategories' => $includingsubcategories],
                    ],
                ];
                $filtercondition['filter'] = $filter;
                $settings = quiz_settings::create($quiz->id);
                $structure = \mod_quiz\structure::create_for_quiz($settings);
                $structure->add_random_questions($page, 1, $filtercondition);
            } else {
                // Add the question.
                quiz_add_quiz_question($question->id, $quiz, $page, $maxmark);
            }

            // Look for additional properties we might want to set on the new slot.
            $extraslotproperties = [];

            // Display number (allowing editable customised question number).
            if (array_key_exists('displaynumber', $questiondata)) {
                if (!is_number($questiondata['displaynumber']) && !is_string($questiondata['displaynumber'])) {
                    throw new ExpectationException('Displayed question number for "' . $questiondata['question'] .
                            '" should either be \'i\', automatically numbered (eg. 1, 2, 3),
                            or customised (eg. A.1, A.2, 1.1, 1.2)', $this->getSession());
                }
                $extraslotproperties['displaynumber'] = $questiondata['displaynumber'];
            }

            // Require previous.
            if (array_key_exists('requireprevious', $questiondata)) {
                if ($questiondata['requireprevious'] === '1') {
                    $extraslotproperties['requireprevious'] = 1;
                } else if ($questiondata['requireprevious'] !== '' && $questiondata['requireprevious'] !== '0') {
                    throw new ExpectationException('Require previous for question "' .
                        $questiondata['question'] . '" should be 0, 1 or blank.',
                        $this->getSession());
                }
            }

            // Grade item.
            if (array_key_exists('grade item', $questiondata) && trim($questiondata['grade item']) !== '') {
                $extraslotproperties['quizgradeitemid'] =
                    $DB->get_field('quiz_grade_items', 'id',
                        ['quizid' => $quiz->id, 'name' => $questiondata['grade item']], MUST_EXIST);
            }

            // If there were any extra properties, save them.
            if ($extraslotproperties) {
                // We assume that the slot was just created for this row of data is the highest numbered one.
                $extraslotproperties['id'] = $DB->get_field('quiz_slots', 'MAX(id)', ['quizid' => $quiz->id]);

                $DB->update_record('quiz_slots', $extraslotproperties);
            }
        }

        $quizobj = quiz_settings::create($quiz->id);
        $quizobj->get_grade_calculator()->recompute_quiz_sumgrades();
    }

    /**
     * Put the specified section headings to start at specified pages of a given quiz.
     *
     * The first row should be column names:
     * | heading | firstslot | shufflequestions |
     *
     * heading   is the section heading text
     * firstslot is the slot number where the section starts
     * shuffle   whether this section is shuffled (0 or 1)
     *
     * Then there should be a number of rows of data, one for each section you want to add.
     *
     * @param string $quizname the name of the quiz to add sections to.
     * @param TableNode $data information about the sections to add.
     *
     * @Given /^quiz "([^"]*)" contains the following sections:$/
     */
    public function quiz_contains_the_following_sections($quizname, TableNode $data) {
        global $DB;

        $quiz = $DB->get_record('quiz', ['name' => $quizname], '*', MUST_EXIST);

        // Add the sections.
        $previousfirstslot = 0;
        foreach ($data->getHash() as $rownumber => $sectiondata) {
            if (!array_key_exists('heading', $sectiondata)) {
                throw new ExpectationException('When adding sections to a quiz, ' .
                        'the heading name column is required.', $this->getSession());
            }
            if (!array_key_exists('firstslot', $sectiondata)) {
                throw new ExpectationException('When adding sections to a quiz, ' .
                        'the firstslot name column is required.', $this->getSession());
            }
            if (!array_key_exists('shuffle', $sectiondata)) {
                throw new ExpectationException('When adding sections to a quiz, ' .
                        'the shuffle name column is required.', $this->getSession());
            }

            if ($rownumber == 0) {
                $section = $DB->get_record('quiz_sections', ['quizid' => $quiz->id], '*', MUST_EXIST);
            } else {
                $section = new stdClass();
                $section->quizid = $quiz->id;
            }

            // Heading.
            $section->heading = $sectiondata['heading'];

            // First slot.
            $section->firstslot = clean_param($sectiondata['firstslot'], PARAM_INT);
            if ($section->firstslot <= $previousfirstslot ||
                    (string) $section->firstslot !== $sectiondata['firstslot']) {
                throw new ExpectationException('The firstslot number for section "' .
                        $sectiondata['heading'] . '" must an integer greater than the previous section firstslot.',
                        $this->getSession());
            }
            if ($rownumber == 0 && $section->firstslot != 1) {
                throw new ExpectationException('The first section must have firstslot set to 1.',
                        $this->getSession());
            }

            // Shuffle.
            $section->shufflequestions = clean_param($sectiondata['shuffle'], PARAM_INT);
            if ((string) $section->shufflequestions !== $sectiondata['shuffle']) {
                throw new ExpectationException('The shuffle value for section "' .
                        $sectiondata['heading'] . '" must be 0 or 1.',
                        $this->getSession());
            }

            if ($rownumber == 0) {
                $DB->update_record('quiz_sections', $section);
            } else {
                $DB->insert_record('quiz_sections', $section);
            }
        }

        if ($section->firstslot > $DB->count_records('quiz_slots', ['quizid' => $quiz->id])) {
            throw new ExpectationException('The section firstslot must be less than the total number of slots in the quiz.',
                    $this->getSession());
        }
    }

    /**
     * Adds a question to the existing quiz with filling the form.
     *
     * The form for creating a question should be on one page.
     *
     * @When /^I add a "(?P<question_type_string>(?:[^"]|\\")*)" question to the "(?P<quiz_name_string>(?:[^"]|\\")*)" quiz with:$/
     * @param string $questiontype
     * @param string $quizname
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_add_question_to_the_quiz_with($questiontype, $quizname, TableNode $questiondata) {
        $quizname = $this->escape($quizname);
        $addaquestion = $this->escape(get_string('addaquestion', 'quiz'));

        $this->execute('behat_navigation::i_am_on_page_instance', [
            $quizname,
            'mod_quiz > Edit',
        ]);

        if ($this->running_javascript()) {
            $this->execute("behat_action_menu::i_open_the_action_menu_in", ['.slots', "css_element"]);
            $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", [$addaquestion]);
        } else {
            $this->execute('behat_general::click_link', $addaquestion);
        }

        $this->finish_adding_question($questiontype, $questiondata);
    }

    /**
     * Set the max mark for a question on the Edit quiz page.
     *
     * @When /^I set the max mark for question "(?P<question_name_string>(?:[^"]|\\")*)" to "(?P<new_mark_string>(?:[^"]|\\")*)"$/
     * @param string $questionname the name of the question to set the max mark for.
     * @param string $newmark the mark to set
     */
    public function i_set_the_max_mark_for_quiz_question($questionname, $newmark) {
        $this->execute('behat_general::click_link', $this->escape(get_string('editmaxmark', 'quiz')));

        $this->execute('behat_general::wait_until_exists', ["li input[name=maxmark]", "css_element"]);

        $this->execute('behat_general::assert_page_contains_text', $this->escape(get_string('edittitleinstructions')));

        $this->execute('behat_general::i_type', [$newmark]);
        $this->execute('behat_general::i_press_named_key', ['', 'enter']);
    }

    /**
     * Open the add menu on a given page, or at the end of the Edit quiz page.
     * @Given /^I open the "(?P<page_n_or_last_string>(?:[^"]|\\")*)" add to quiz menu$/
     * @param string $pageorlast either "Page n" or "last".
     */
    public function i_open_the_add_to_quiz_menu_for($pageorlast) {

        if (!$this->running_javascript()) {
            throw new DriverException('Activities actions menu not available when Javascript is disabled');
        }

        if ($pageorlast == 'last') {
            $xpath = "//div[@class = 'last-add-menu']//a[contains(@data-bs-toggle, 'dropdown') and contains(., 'Add')]";
        } else if (preg_match('~Page (\d+)~', $pageorlast, $matches)) {
            $xpath = "//li[@id = 'page-{$matches[1]}']//a[contains(@data-bs-toggle, 'dropdown') and contains(., 'Add')]";
        } else {
            throw new ExpectationException("The I open the add to quiz menu step must specify either 'Page N' or 'last'.",
                $this->getSession());
        }
        $this->find('xpath', $xpath)->click();
    }

    /**
     * Check whether a particular question is on a particular page of the quiz on the Edit quiz page.
     * @Given /^I should see "(?P<question_name>(?:[^"]|\\")*)" on quiz page "(?P<page_number>\d+)"$/
     * @param string $questionname the name of the question we are looking for.
     * @param number $pagenumber the page it should be found on.
     */
    public function i_should_see_on_quiz_page($questionname, $pagenumber) {
        $xpath = "//li[contains(., '" . $this->escape($questionname) .
            "')][./preceding-sibling::li[contains(@class, 'pagenumber')][1][contains(., 'Page " .
            $pagenumber . "')]]";

        $this->execute('behat_general::should_exist', [$xpath, 'xpath_element']);
    }

    /**
     * Check whether a particular question is not on a particular page of the quiz on the Edit quiz page.
     * @Given /^I should not see "(?P<question_name>(?:[^"]|\\")*)" on quiz page "(?P<page_number>\d+)"$/
     * @param string $questionname the name of the question we are looking for.
     * @param number $pagenumber the page it should be found on.
     */
    public function i_should_not_see_on_quiz_page($questionname, $pagenumber) {
        $xpath = "//li[contains(., '" . $this->escape($questionname) .
                "')][./preceding-sibling::li[contains(@class, 'pagenumber')][1][contains(., 'Page " .
                $pagenumber . "')]]";

        $this->execute('behat_general::should_not_exist', [$xpath, 'xpath_element']);
    }

    /**
     * Check whether one question comes before another on the Edit quiz page.
     * The two questions must be on the same page.
     * @Given /^I should see "(?P<first_q_name>(?:[^"]|\\")*)" before "(?P<second_q_name>(?:[^"]|\\")*)" on the edit quiz page$/
     * @param string $firstquestionname the name of the question that should come first in order.
     * @param string $secondquestionname the name of the question that should come immediately after it in order.
     */
    public function i_should_see_before_on_the_edit_quiz_page($firstquestionname, $secondquestionname) {
        $xpath = "//li[contains(., '" . $this->escape($firstquestionname) .
                "')]/following-sibling::li" .
                "[contains(., '" . $this->escape($secondquestionname) . "')]";

        $this->execute('behat_general::should_exist', [$xpath, 'xpath_element']);
    }

    /**
     * Check the number displayed alongside a question on the Edit quiz page.
     * @Given /^"(?P<question_name>(?:[^"]|\\")*)" should have number "(?P<number>(?:[^"]|\\")*)" on the edit quiz page$/
     * @param string $questionname the name of the question we are looking for.
     * @param number $number the number (or 'i') that should be displayed beside that question.
     */
    public function should_have_number_on_the_edit_quiz_page($questionname, $number) {
        if ($number !== get_string('infoshort', 'quiz')) {
            // Logic here copied from edit_renderer, which is not ideal, but necessary.
            $number = get_string('question') . ' ' . $number;
        }
        $xpath = "//li[contains(@class, 'slot') and contains(., '" . $this->escape($questionname) .
                "')]//span[contains(@class, 'slotnumber') and normalize-space(.) = '" . $this->escape($number) . "']";
        $this->execute('behat_general::should_exist', [$xpath, 'xpath_element']);
    }

    /**
     * Get the xpath for a partcular add/remove page-break icon.
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon.
     * @return string the requried xpath.
     */
    protected function get_xpath_page_break_icon_after_question($addorremoves, $questionname) {
        return "//li[contains(@class, 'slot') and contains(., '" . $this->escape($questionname) .
                "')]//a[contains(@class, 'page_split_join') and @title = '" . $addorremoves . " page break']";
    }

    /**
     * Click the add or remove page-break icon after a particular question.
     * @When /^I click on the "(Add|Remove)" page break icon after question "(?P<question_name>(?:[^"]|\\")*)"$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     */
    public function i_click_on_the_page_break_icon_after_question($addorremoves, $questionname) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);

        $this->execute("behat_general::i_click_on", [$xpath, "xpath_element"]);
    }

    /**
     * Assert the add or remove page-break icon after a particular question exists.
     * @When /^the "(Add|Remove)" page break icon after question "(?P<question_name>(?:[^"]|\\")*)" should exist$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @return array of steps.
     */
    public function the_page_break_icon_after_question_should_exist($addorremoves, $questionname) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);

        $this->execute('behat_general::should_exist', [$xpath, 'xpath_element']);
    }

    /**
     * Assert the add or remove page-break icon after a particular question does not exist.
     * @When /^the "(Add|Remove)" page break icon after question "(?P<question_name>(?:[^"]|\\")*)" should not exist$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @return array of steps.
     */
    public function the_page_break_icon_after_question_should_not_exist($addorremoves, $questionname) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);

        $this->execute('behat_general::should_not_exist', [$xpath, 'xpath_element']);
    }

    /**
     * Check the add or remove page-break link after a particular question contains the given parameters in its url.
     *
     * @When /^the "(Add|Remove)" page break link after question "(?P<question_name>(?:[^"]|\\")*) should contain:$/
     * @When /^the "(Add|Remove)" page break link after question "(?P<question_name>(?:[^"]|\\")*) should contain:"$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @param TableNode $paramdata with data for checking the page break url
     * @return array of steps.
     */
    public function the_page_break_link_after_question_should_contain($addorremoves, $questionname, $paramdata) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);

        $this->execute("behat_general::i_click_on", [$xpath, "xpath_element"]);
    }

    /**
     * Set Shuffle for shuffling questions within sections
     *
     * @param string $heading the heading of the section to change shuffle for.
     *
     * @Given /^I click on shuffle for section "([^"]*)" on the quiz edit page$/
     */
    public function i_click_on_shuffle_for_section($heading) {
        $xpath = $this->get_xpath_for_shuffle_checkbox($heading);
        $checkbox = $this->find('xpath', $xpath);
        $checkbox->click();
    }

    /**
     * Check the shuffle checkbox for a particular section.
     *
     * @param string $heading the heading of the section to check shuffle for
     * @param int $value whether the shuffle checkbox should be on or off.
     *
     * @Given /^shuffle for section "([^"]*)" should be "(On|Off)" on the quiz edit page$/
     */
    public function shuffle_for_section_should_be($heading, $value) {
        $xpath = $this->get_xpath_for_shuffle_checkbox($heading);
        $checkbox = $this->find('xpath', $xpath);
        $this->ensure_node_is_visible($checkbox);
        if ($value == 'On' && !$checkbox->isChecked()) {
            $msg = "Shuffle for section '$heading' is not checked, but you are expecting it to be checked ($value). " .
                    "Check the line with: \nshuffle for section \"$heading\" should be \"$value\" on the quiz edit page" .
                    "\nin your behat script";
            throw new ExpectationException($msg, $this->getSession());
        } else if ($value == 'Off' && $checkbox->isChecked()) {
            $msg = "Shuffle for section '$heading' is checked, but you are expecting it not to be ($value). " .
                    "Check the line with: \nshuffle for section \"$heading\" should be \"$value\" on the quiz edit page" .
                    "\nin your behat script";
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * Return the xpath for shuffle checkbox in section heading
     * @param string $heading
     * @return string
     */
    protected function get_xpath_for_shuffle_checkbox($heading) {
         return "//div[contains(@class, 'section-heading') and contains(., '" . $this->escape($heading) .
                "')]//input[@type = 'checkbox']";
    }

    /**
     * Move a question on the Edit quiz page by first clicking on the Move icon,
     * then clicking one of the "After ..." links.
     * @When /^I move "(?P<question_name>(?:[^"]|\\")*)" to "(?P<target>(?:[^"]|\\")*)" in the quiz by clicking the move icon$/
     * @param string $questionname the name of the question we are looking for.
     * @param string $target the target place to move to. One of the links in the pop-up like
     *      "After Page 1" or "After Question N".
     */
    public function i_move_question_after_item_by_clicking_the_move_icon($questionname, $target) {
        $iconxpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($questionname) .
                "')]//span[contains(@class, 'editing_move')]";

        $this->execute("behat_general::i_click_on", [$iconxpath, "xpath_element"]);
        $this->execute("behat_general::i_click_on", [$this->escape($target), "button"]);
    }

    /**
     * Move a question on the Edit quiz page by dragging a given question on top of another item.
     * @When /^I move "(?P<question_name>(?:[^"]|\\")*)" to "(?P<target>(?:[^"]|\\")*)" in the quiz by dragging$/
     * @param string $questionname the name of the question we are looking for.
     * @param string $target the target place to move to. Ether a question name, or "Page N"
     */
    public function i_move_question_after_item_by_dragging($questionname, $target) {
        $iconxpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($questionname) .
                "')]//span[contains(@class, 'editing_move')]//img";
        $destinationxpath = "//li[contains(@class, ' slot ') or contains(@class, 'pagenumber ')]" .
                "[contains(., '" . $this->escape($target) . "')]";

        $this->execute('behat_general::i_drag_and_i_drop_it_in',
            [$iconxpath, 'xpath_element', $destinationxpath, 'xpath_element']
        );
    }

    /**
     * Delete a question on the Edit quiz page by first clicking on the Delete icon,
     * then clicking one of the "After ..." links.
     * @When /^I delete "(?P<question_name>(?:[^"]|\\")*)" in the quiz by clicking the delete icon$/
     * @param string $questionname the name of the question we are looking for.
     * @return array of steps.
     */
    public function i_delete_question_by_clicking_the_delete_icon($questionname) {
        $slotxpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($questionname) .
                "')]";
        $deletexpath = "//a[contains(@class, 'editing_delete')]";

        $this->execute("behat_general::i_click_on", [$slotxpath . $deletexpath, "xpath_element"]);

        $this->execute('behat_general::i_click_on_in_the',
            ['Yes', "button", "Confirm", "dialogue"]
        );
    }

    /**
     * Set the section heading for a given section on the Edit quiz page
     *
     * @When /^I change quiz section heading "(?P<section_name_string>(?:[^"]|\\")*)" to "(?P<new_section_heading_string>(?:[^"]|\\")*)"$/
     * @param string $sectionname the heading to change.
     * @param string $sectionheading the new heading to set.
     */
    public function i_set_the_section_heading_for($sectionname, $sectionheading) {
        // Empty section headings will have a default names of "Untitled heading".
        if (empty($sectionname)) {
            $sectionname = get_string('sectionnoname', 'quiz');
        }
        $this->execute('behat_general::click_link', $this->escape("Edit heading '{$sectionname}'"));

        $this->execute('behat_general::assert_page_contains_text', $this->escape(get_string('edittitleinstructions')));

        $this->execute('behat_general::i_press_named_key', ['', 'backspace']);
        $this->execute('behat_general::i_type', [$sectionheading]);
        $this->execute('behat_general::i_press_named_key', ['', 'enter']);
    }

    /**
     * Check that a given question comes after a given section heading in the
     * quiz navigation block.
     *
     * @Then /^I should see question "(?P<questionnumber>(?:[^"]|\\")*)" in section "(?P<section_heading_string>(?:[^"]|\\")*)" in the quiz navigation$/
     * @param string $questionnumber the number of the question to check.
     * @param string $sectionheading which section heading it should appear after.
     */
    public function i_should_see_question_in_section_in_the_quiz_navigation($questionnumber, $sectionheading) {

        // Using xpath literal to avoid quotes problems.
        $questionnumberliteral = behat_context_helper::escape($questionnumber);
        $headingliteral = behat_context_helper::escape($sectionheading);

        // Split in two checkings to give more feedback in case of exception.
        $exception = new ExpectationException('Question "' . $questionnumber . '" is not in section "' .
                $sectionheading . '" in the quiz navigation.', $this->getSession());
        $xpath = "//*[@id = 'mod_quiz_navblock']//*[contains(concat(' ', normalize-space(@class), ' '), ' qnbutton ') and " .
                "contains(., {$questionnumberliteral}) and contains(preceding-sibling::h3[1], {$headingliteral})]";
        $this->find('xpath', $xpath, $exception);
    }

    /**
     * Helper used by user_has_attempted_with_responses,
     * user_has_started_an_attempt_at_quiz_with_details, etc.
     *
     * @param TableNode $attemptinfo data table from the Behat step
     * @return array with two elements, $forcedrandomquestions, $forcedvariants,
     *      that can be passed to $quizgenerator->create_attempt.
     */
    protected function extract_forced_randomisation_from_attempt_info(TableNode $attemptinfo) {
        global $DB;

        $forcedrandomquestions = [];
        $forcedvariants = [];
        foreach ($attemptinfo->getHash() as $slotinfo) {
            if (empty($slotinfo['slot'])) {
                throw new ExpectationException('When simulating a quiz attempt, ' .
                        'the slot column is required.', $this->getSession());
            }

            if (!empty($slotinfo['actualquestion'])) {
                $forcedrandomquestions[$slotinfo['slot']] = $DB->get_field('question', 'id',
                        ['name' => $slotinfo['actualquestion']], MUST_EXIST);
            }

            if (!empty($slotinfo['variant'])) {
                $forcedvariants[$slotinfo['slot']] = (int) $slotinfo['variant'];
            }
        }
        return [$forcedrandomquestions, $forcedvariants];
    }

    /**
     * Helper used by user_has_attempted_with_responses, user_has_checked_answers_in_their_attempt_at_quiz,
     * user_has_input_answers_in_their_attempt_at_quiz, etc.
     *
     * @param TableNode $attemptinfo data table from the Behat step
     * @return array of responses that can be passed to $quizgenerator->submit_responses.
     */
    protected function extract_responses_from_attempt_info(TableNode $attemptinfo) {
        $responses = [];
        foreach ($attemptinfo->getHash() as $slotinfo) {
            if (empty($slotinfo['slot'])) {
                throw new ExpectationException('When simulating a quiz attempt, ' .
                        'the slot column is required.', $this->getSession());
            }
            if (!array_key_exists('response', $slotinfo)) {
                throw new ExpectationException('When simulating a quiz attempt, ' .
                        'the response column is required.', $this->getSession());
            }
            $responses[$slotinfo['slot']] = $slotinfo['response'];
        }
        return $responses;
    }

    /**
     * Attempt a quiz.
     *
     * The first row should be column names:
     * | slot | actualquestion | variant | response |
     * The first two of those are required. The others are optional.
     *
     * slot           The slot
     * actualquestion This column is optional, and is only needed if the quiz contains
     *                random questions. If so, this will let you control which actual
     *                question gets picked when this slot is 'randomised' at the
     *                start of the attempt. If you don't specify, then one will be picked
     *                at random (which might make the response meaningless).
     *                Give the question name.
     * variant        This column is similar, and also options. It is only needed if
     *                the question that ends up in this slot returns something greater
     *                than 1 for $question->get_num_variants(). Like with actualquestion,
     *                if you specify a value here it is used the fix the 'random' choice
     *                made when the quiz is started.
     * response       The response that was submitted. How this is interpreted depends on
     *                the question type. It gets passed to
     *                {@link core_question_generator::get_simulated_post_data_for_question_attempt()}
     *                and therefore to the un_summarise_response method of the question to decode.
     *
     * Then there should be a number of rows of data, one for each question you want to add.
     * There is no need to supply answers to all questions. If so, other qusetions will be
     * left unanswered.
     *
     * @param string $username the username of the user that will attempt.
     * @param string $quizname the name of the quiz the user will attempt.
     * @param TableNode $attemptinfo information about the questions to add, as above.
     * @param string|null $timefinish if specified, the attempt will be submitted with this
     * @Given /^user "([^"]*)" has attempted "([^"]*)" with responses:$/
     * @Given /^user "([^"]*)" has attempted "([^"]*)" with responses submitting "([^"]*)":$/
     */
    public function user_has_attempted_with_responses($username, $quizname, TableNode $attemptinfo, $timefinish = null) {
        global $DB;

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = behat_util::get_data_generator()->get_plugin_generator('mod_quiz');

        $quiz = $DB->get_record('quiz', ['name' => $quizname], 'id, timeclose', MUST_EXIST);
        $quizid = $quiz->id;
        $timeclose = $quiz->timeclose;

        // If timefinish is provided, check against timeclose.
        if ($timefinish !== null) {
            if ($timeclose && $timefinish > $timeclose) {
                throw new ExpectationException(
                    "Attempt submission cannot be after the time the quiz closed.",
                    $this->getSession()
                );
            }
        }
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);

        list($forcedrandomquestions, $forcedvariants) =
                $this->extract_forced_randomisation_from_attempt_info($attemptinfo);
        $responses = $this->extract_responses_from_attempt_info($attemptinfo);

        $this->set_user($user);

        $attempt = $quizgenerator->create_attempt($quizid, $user->id,
                $forcedrandomquestions, $forcedvariants);

        $quizgenerator->submit_responses($attempt->id, $responses, false, true, $timefinish);

        $this->set_user();
    }

    /**
     * Start a quiz attempt without answers.
     *
     * @param string $username the username of the user that will attempt.
     * @param string $quizname the name of the quiz the user will attempt.
     * @Given /^user "([^"]*)" has started an attempt at quiz "([^"]*)"$/
     */
    public function user_has_started_an_attempt_at_quiz($username, $quizname) {
        global $DB;

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = behat_util::get_data_generator()->get_plugin_generator('mod_quiz');

        $quizid = $DB->get_field('quiz', 'id', ['name' => $quizname], MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
        $this->set_user($user);
        $quizgenerator->create_attempt($quizid, $user->id);
        $this->set_user();
    }

    /**
     * Start a quiz attempt without answers.
     *
     * The supplied data table for have a row for each slot where you want
     * to force either which random question was chose, or which random variant
     * was used, as for {@link user_has_attempted_with_responses()} above.
     *
     * @param string $username the username of the user that will attempt.
     * @param string $quizname the name of the quiz the user will attempt.
     * @param TableNode $attemptinfo information about the questions to add, as above.
     * @Given /^user "([^"]*)" has started an attempt at quiz "([^"]*)" randomised as follows:$/
     */
    public function user_has_started_an_attempt_at_quiz_with_details($username, $quizname, TableNode $attemptinfo) {
        global $DB;

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = behat_util::get_data_generator()->get_plugin_generator('mod_quiz');

        $quizid = $DB->get_field('quiz', 'id', ['name' => $quizname], MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);

        list($forcedrandomquestions, $forcedvariants) =
                $this->extract_forced_randomisation_from_attempt_info($attemptinfo);

        $this->set_user($user);

        $quizgenerator->create_attempt($quizid, $user->id,
                $forcedrandomquestions, $forcedvariants);

        $this->set_user();
    }

    /**
     * Input answers to particular questions an existing quiz attempt, without
     * simulating a click of the 'Check' button, if any.
     *
     * Then there should be a number of rows of data, with two columns slot and response,
     * as for {@link user_has_attempted_with_responses()} above.
     * There is no need to supply answers to all questions. If so, other questions will be
     * left unanswered.
     *
     * @param string $username the username of the user that will attempt.
     * @param string $quizname the name of the quiz the user will attempt.
     * @param TableNode $attemptinfo information about the questions to add, as above.
     * @throws \Behat\Mink\Exception\ExpectationException
     * @Given /^user "([^"]*)" has input answers in their attempt at quiz "([^"]*)":$/
     */
    public function user_has_input_answers_in_their_attempt_at_quiz($username, $quizname, TableNode $attemptinfo) {
        global $DB;

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = behat_util::get_data_generator()->get_plugin_generator('mod_quiz');

        $quizid = $DB->get_field('quiz', 'id', ['name' => $quizname], MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);

        $responses = $this->extract_responses_from_attempt_info($attemptinfo);

        $this->set_user($user);

        $attempts = quiz_get_user_attempts($quizid, $user->id, 'unfinished', true);
        $quizgenerator->submit_responses(key($attempts), $responses, false, false);

        $this->set_user();
    }

    /**
     * Submit answers to questions an existing quiz attempt, with a simulated click on the 'Check' button.
     *
     * This step should only be used with question behaviours that have have
     * a 'Check' button. Those include Interactive with multiple tires, Immediate feedback
     * and Immediate feedback with CBM.
     *
     * Then there should be a number of rows of data, with two columns slot and response,
     * as for {@link user_has_attempted_with_responses()} above.
     * There is no need to supply answers to all questions. If so, other questions will be
     * left unanswered.
     *
     * @param string $username the username of the user that will attempt.
     * @param string $quizname the name of the quiz the user will attempt.
     * @param TableNode $attemptinfo information about the questions to add, as above.
     * @throws \Behat\Mink\Exception\ExpectationException
     * @Given /^user "([^"]*)" has checked answers in their attempt at quiz "([^"]*)":$/
     */
    public function user_has_checked_answers_in_their_attempt_at_quiz($username, $quizname, TableNode $attemptinfo) {
        global $DB;

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = behat_util::get_data_generator()->get_plugin_generator('mod_quiz');

        $quizid = $DB->get_field('quiz', 'id', ['name' => $quizname], MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);

        $responses = $this->extract_responses_from_attempt_info($attemptinfo);

        $this->set_user($user);

        $attempts = quiz_get_user_attempts($quizid, $user->id, 'unfinished', true);
        $quizgenerator->submit_responses(key($attempts), $responses, true, false);

        $this->set_user();
    }

    /**
     * Finish an existing quiz attempt.
     *
     * @param string $username the username of the user that will attempt.
     * @param string $quizname the name of the quiz the user will attempt.
     * @Given /^user "([^"]*)" has finished an attempt at quiz "([^"]*)"$/
     */
    public function user_has_finished_an_attempt_at_quiz($username, $quizname) {
        global $DB;

        $quizid = $DB->get_field('quiz', 'id', ['name' => $quizname], MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);

        $this->set_user($user);

        $attempts = quiz_get_user_attempts($quizid, $user->id, 'unfinished', true);
        $attemptobj = quiz_attempt::create(key($attempts));
        $attemptobj->process_submit(time(), true);
        $attemptobj->process_grade_submission(time());

        $this->set_user();
    }

    /**
     * Finish an existing quiz attempt.
     *
     * @param string $quizname the name of the quiz the user will attempt.
     * @param string $username the username of the user that will attempt.
     * @Given the attempt at :quizname by :username was never submitted
     */
    public function attempt_was_abandoned($quizname, $username) {
        global $DB;

        $quizid = $DB->get_field('quiz', 'id', ['name' => $quizname], MUST_EXIST);
        $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);

        $this->set_user($user);

        $attempt = quiz_get_user_attempt_unfinished($quizid, $user->id);
        if (!$attempt) {
            throw new coding_exception("No in-progress attempt found for $username and quiz $quizname.");
        }
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_abandon(time(), false);

        $this->set_user();
    }

    /**
     * Return a list of the exact named selectors for the component.
     *
     * @return behat_component_named_selector[]
     */
    public static function get_exact_named_selectors(): array {
        return [
            new behat_component_named_selector('Edit slot',
            ["//li[contains(@class,'qtype')]//span[@class='slotnumber' and contains(., %locator%)]/.."])
        ];
    }

    /**
     * Generate pre-created attempts for a quiz.
     *
     * @param string $quizname the name of the quiz to create attempts for.
     * @Given quiz :quizname has pre-created attempts
     */
    public function quiz_has_precreated_attempts(string $quizname): void {
        global $DB;

        $quiz = $DB->get_record('quiz', ['name' => $quizname], 'id, course', MUST_EXIST);
        \mod_quiz\task\precreate_attempts::precreate_attempts_for_quiz($quiz->id, $quiz->course);
    }
}
