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

namespace mod_qbank\task;

use context;
use context_course;
use context_coursecat;
use context_module;
use context_system;
use core\exception\moodle_exception;
use core\task\manager;
use core_question\local\bank\random_question_loader;
use core_question\local\bank\question_bank_helper;
use mod_quiz\quiz_settings;
use stdClass;
use core_question\local\bank\question_version_status;

/**
 * Before testing, we firstly need to create some data to emulate what sites can have pre-upgrade.
 * Namely, we are adding question categories and questions to deprecated contexts i.e. anything not CONTEXT_MODULE,
 * and to quiz local banks too as we need to test these don't get touched.
 * It also adds questions to some categories that are not used by quizzes anywhere.
 *
 * The tests cover a few areas.
 * 1: We validate the data setup is correct before we run the installation script testing.
 * 2: The installation test validates that any question categories not in CONTEXT_MODULE get transferred to relevant mod_qbank
 * instances including their questions. It also validates that any stale questions that are not in use by quizzes are removed
 * along with empty categories.
 *
 * @package    mod_qbank
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_qbank\task\transfer_question_categories
 */
final class transfer_question_categories_test extends \advanced_testcase {

    /** @var \core\context\coursecat test course category context */
    private \core\context\coursecat $coursecatcontext;

    /** @var \core\context\course  test course context */
    private \core\context\course $coursecontext;

    /** @var \core\context\course  test stale course context*/
    private \core\context\course $stalecoursecontext;

    /** @var \core\context\module  test quiz mod context */
    private \core\context\module $quizcontext;

    /** @var \core\context\course Course context with used and unused questions. */
    private \core\context\course $usedunusedcontext;

    /** @var stdClass[] test stale questions */
    private array $stalequestions;

    /**
     * Get question data from question category ids provided in the argument.
     *
     * @param array $categoryids
     * @return array
     */
    protected function get_question_data(array $categoryids): array {
        global $DB;

        [$insql, $inparams] = $DB->get_in_or_equal($categoryids);

        $sql = "SELECT q.id, qbe.questioncategoryid AS categoryid, qv.status
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid {$insql}";

        return $DB->get_records_sql($sql, $inparams);
    }

    /**
     * This is hacky, but we can't use the API to create these as non module contexts are deprecated for holding question
     * categories.
     *
     * @param string $name of the new category
     * @param int $contextid of the module holding the category
     * @param int $parentid of the new category
     * @return stdClass category object
     */
    protected function create_question_category(string $name, int $contextid, int $parentid = 0): stdClass {

        global $DB;

        if (!$parentid) {
            if (!$parent = $DB->get_record('question_categories', ['contextid' => $contextid, 'parent' => 0, 'name' => 'top'])) {
                $parent = new stdClass();
                $parent->name = 'top';
                $parent->info = '';
                $parent->contextid = $contextid;
                $parent->parent = 0;
                $parent->sortorder = 0;
                $parent->stamp = make_unique_id_code();
                $parent->id = $DB->insert_record('question_categories', $parent);
            }
            $parentid = $parent->id;
        }

        $record = (object) [
            'name' => $name,
            'parent' => $parentid,
            'contextid' => $contextid,
            'info' => '',
            'infoformat' => FORMAT_HTML,
            'stamp' => make_unique_id_code(),
            'sortorder' => 999,
            'idnumber' => null,
        ];

        $record->id = $DB->insert_record('question_categories', $record);
        return $record;
    }

    /**
     * Sets up the installation test with data.
     *
     * @return void
     */
    protected function setup_pre_install_data(): void {
        global $DB;
        self::setAdminUser();
        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');
        $quizgenerator = self::getDataGenerator()->get_plugin_generator('mod_quiz');

        // Setup 2 categories at site level context, with a question in each.
        $sitecontext = context_system::instance();
        $site = get_site();

        $siteparentcat = $this->create_question_category('Site Parent Cat', $sitecontext->id);

        $sitechildcat = $this->create_question_category('Site Child Cat', $sitecontext->id, $siteparentcat->id);

        $question1 = $questiongenerator->create_question(
            'shortanswer',
            null,
            ['category' => $siteparentcat->id, 'status' => question_version_status::QUESTION_STATUS_READY]
        );
        $question2 = $questiongenerator->create_question(
            'shortanswer',
            null,
            ['category' => $sitechildcat->id, 'status' => question_version_status::QUESTION_STATUS_READY]
        );

        // Add a quiz to the site course and put those questions into it.
        $quiz = $quizgenerator->create_instance(['course' => $site->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);
        quiz_add_quiz_question($question1->id, $quiz, 1);
        quiz_add_quiz_question($question2->id, $quiz, 1);

        // Create a course with a quiz containing a random question from the system context.
        $randomcourse = self::getDataGenerator()->create_course(['shortname' => 'Random']);
        $randomquiz = $quizgenerator->create_instance(
            [
                'course' => $randomcourse->id,
                'grade' => 100.0,
                'sumgrades' => 2,
                'layout' => '1,0',
            ],
        );
        $randomquizsettings = quiz_settings::create($randomquiz->id);
        $structure = $randomquizsettings->get_structure();
        $topcategory = $DB->get_record('question_categories', ['contextid' => $sitecontext->id, 'parent' => 0]);
        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => \core_question\local\bank\condition::JOINTYPE_DEFAULT,
                    'values' => [$topcategory->id],
                    'filteroptions' => ['includesubcategories' => true],
                ],
            ],
        ];
        $structure->add_random_questions(1, 1, $filtercondition);

        // Create a course category and then a question category attached to that context.
        $coursecategory = self::getDataGenerator()->create_category();
        $this->coursecatcontext = context_coursecat::instance($coursecategory->id);
        $coursecatcat = $this->create_question_category('Course Cat Parent Cat', $this->coursecatcontext->id);

        // Add a question to the category just made.
        $question3 = $questiongenerator->create_question('essay', 'files', ['category' => $coursecatcat->id]);

        // Add a quiz to the course category and put those questions into it.
        $course = self::getDataGenerator()->create_course(['category' => $coursecategory->id]);
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);
        quiz_add_quiz_question($question3->id, $quiz, 1);

        // Create an additional question with a missing type, to catch edge cases.
        $question4 = $questiongenerator->create_question('missingtype', 'invalid', ['category' => $coursecatcat->id]);
        $DB->set_field('question', 'qtype', 'invalid', ['id' => $question4->id]);

        // Create 2 nested categories with questions in them at course context level.
        $course = self::getDataGenerator()->create_course();
        $this->coursecontext = context_course::instance($course->id);
        $coursegrandparentcat = $this->create_question_category('Course Grandparent Cat', $this->coursecontext->id);
        $courseparentcat1 = $this->create_question_category(
            'Course Parent Cat',
            $this->coursecontext->id,
            $coursegrandparentcat->id,
        );
        $coursechildcat1 = $this->create_question_category(
            'Course Child Cat',
            $this->coursecontext->id,
            $courseparentcat1->id,
        );

        $question4 = $questiongenerator->create_question('shortanswer', null, ['category' => $courseparentcat1->id]);
        $question5 = $questiongenerator->create_question('shortanswer', null, ['category' => $coursechildcat1->id]);

        // Make the questions 'in use'.
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);
        quiz_add_quiz_question($question4->id, $quiz, 1);
        quiz_add_quiz_question($question5->id, $quiz, 1);

        // Include a stale question, which should not be migrated with the others.
        $question6 = $questiongenerator->create_question('shortanswer', null, ['category' => $coursechildcat1->id]);
        $DB->set_field(
            'question_versions',
            'status',
            question_version_status::QUESTION_STATUS_HIDDEN,
            ['questionid' => $question6->id],
        );

        // Create some nested categories with no questions in use.
        $course = self::getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $courseparentcat1 = $this->create_question_category('Stale Course Parent Cat1', $context->id);
        $coursechildcat1 = $this->create_question_category('Stale Course Child Cat1', $context->id, $courseparentcat1->id);
        $courseparentcat2 = $this->create_question_category('Stale Course Parent Cat2', $context->id);
        $coursechildcat2 = $this->create_question_category('Stale Course Child Cat2', $context->id, $courseparentcat2->id);
        $coursegrandchildcat1 = $this->create_question_category('Stale Course Grandchild Cat1', $context->id, $coursechildcat2->id);
        $this->stalecoursecontext = context_course::instance($course->id);

        // Make all the questions hidden.
        $this->stalequestions[] = $questiongenerator->create_question('shortanswer',
            null,
            ['category' => $courseparentcat1->id, 'status' => question_version_status::QUESTION_STATUS_HIDDEN]
        );
        $this->stalequestions[] = $questiongenerator->create_question('shortanswer',
            null,
            ['category' => $coursechildcat1->id, 'status' => question_version_status::QUESTION_STATUS_HIDDEN]
        );
        $this->stalequestions[] = $questiongenerator->create_question('shortanswer',
            null,
            ['category' => $courseparentcat2->id, 'status' => question_version_status::QUESTION_STATUS_HIDDEN]
        );
        $this->stalequestions[] = $questiongenerator->create_question('shortanswer',
            null,
            ['category' => $coursechildcat2->id, 'status' => question_version_status::QUESTION_STATUS_HIDDEN]
        );
        $this->stalequestions[] = $questiongenerator->create_question('shortanswer',
            null,
            ['category' => $coursegrandchildcat1->id, 'status' => question_version_status::QUESTION_STATUS_HIDDEN]
        );

        foreach ($this->stalequestions as $question) {
            $DB->set_field('question_versions',
                'status',
                question_version_status::QUESTION_STATUS_HIDDEN,
                ['questionid' => $question->id]
            );
        }

        // Create additional versions of a stale question, all hidden.
        $staleversionquestion = reset($this->stalequestions);
        $questiongenerator->update_question($staleversionquestion, overrides: (array) $staleversionquestion);
        $questiongenerator->update_question($staleversionquestion, overrides: (array) $staleversionquestion);

        // Set up a quiz with some categories and questions attached to it.
        $course = self::getDataGenerator()->create_course();
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);
        $this->quizcontext = context_module::instance($quiz->cmid);
        $quizparentcat1 = $this->create_question_category('Quiz Mod Parent Cat1', $this->quizcontext->id);
        $quizchildcat1 = $this->create_question_category('Quiz Mod Child Cat1', $this->quizcontext->id, $quizparentcat1->id);
        $question1 = $questiongenerator->create_question('shortanswer', null, ['category' => $quizparentcat1->id]);
        $question2 = $questiongenerator->create_question('shortanswer', null, ['category' => $quizchildcat1->id]);
        quiz_add_quiz_question($question1->id, $quiz, 1);
        quiz_add_quiz_question($question2->id, $quiz, 1);

        // Set up a course with three categories
        // - One contains questions including 1 that is used in a quiz.
        // - One contains questions that are not used anywhere, but are in "ready" state.
        // - One contains no questions.
        $course = self::getDataGenerator()->create_course(['shortname' => 'Used-Unused-Empty']);
        $this->usedunusedcontext = context_course::instance($course->id);
        $usedcategory = $this->create_question_category(name: 'Used Question Cat', contextid: $this->usedunusedcontext->id);
        $unusedcategory = $this->create_question_category('Unused Question Cat', $this->usedunusedcontext->id);
        $emptycategory = $this->create_question_category('Empty Cat', $this->usedunusedcontext->id);
        $question1 = $questiongenerator->create_question('shortanswer', null, ['category' => $usedcategory->id]);
        $question2 = $questiongenerator->create_question('shortanswer', null, ['category' => $usedcategory->id]);
        $question3 = $questiongenerator->create_question('shortanswer', null, ['category' => $unusedcategory->id]);
        $question4 = $questiongenerator->create_question('shortanswer', null, ['category' => $unusedcategory->id]);
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);
        quiz_add_quiz_question($question1->id, $quiz, 1);

        // The quiz also contains a random question from the used category.
        $quizsettings = quiz_settings::create($quiz->id);
        $structure = $quizsettings->get_structure();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'jointype' => \core_question\local\bank\condition::JOINTYPE_DEFAULT,
                    'values' => [$usedcategory->id],
                    'filteroptions' => ['includesubcategories' => false],
                ],
            ],
        ];
        $structure->add_random_questions(1, 1, $filtercondition);
    }

    /**
     * Asserts that the pre-installation setup is correct.
     *
     * @return void
     */
    public function test_setup_pre_install_data(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setup_pre_install_data();

        $sitecontext = context_system::instance();
        $allsitecats = $DB->get_records('question_categories', ['contextid' => $sitecontext->id], 'id ASC');

        // Make sure we have 2 site level question categories below 'top' and that the child is below the parent.
        $this->assertCount(3, $allsitecats);
        $parentcat = next($allsitecats);
        $childcat = end($allsitecats);
        $this->assertEquals($parentcat->id, $childcat->parent);

        // Make sure we have 1 question per the above site level question categories.
        $questions = $this->get_question_data(array_map(static fn($cat) => $cat->id, $allsitecats));
        usort($questions, static fn($a, $b) => $a->categoryid <=> $b->categoryid);
        $this->assertCount(2, $questions);
        $parentcatq = reset($questions);
        $childcatq = end($questions);
        $this->assertEquals($parentcat->id, $parentcatq->categoryid);
        $this->assertEquals($childcat->id, $childcatq->categoryid);

        // Make sure the "Random" course has 1 quiz with 1 random question that returns the questions from the system top category.
        $randomcourse = $DB->get_record('course', ['shortname' => 'Random']);
        $coursemods = get_course_mods($randomcourse->id);
        $randomquiz = reset($coursemods);
        $randomquizsettings = quiz_settings::create($randomquiz->instance);
        $structure = $randomquizsettings->get_structure();
        $randomquestionslot = $structure->get_question_in_slot(1);
        $this->assertEquals($randomquestionslot->contextid, $sitecontext->id);
        $loader = new random_question_loader(new \qubaid_list([]));
        $randomquestions = $loader->get_filtered_questions($randomquestionslot->filtercondition['filter']);
        $this->assertCount(2, $randomquestions);
        $randomq1 = reset($randomquestions);
        $randomq2 = end($randomquestions);
        $this->assertEquals($parentcatq->id, $randomq1->id);
        $this->assertEquals($parentcat->id, $randomq1->category);
        $this->assertEquals($childcatq->id, $randomq2->id);
        $this->assertEquals($childcat->id, $randomq2->category);

        // Make sure that the course category has a question category below 'top'.
        $allcoursecatcats = $DB->get_records('question_categories', ['contextid' => $this->coursecatcontext->id], 'id ASC');
        $this->assertCount(2, $allcoursecatcats);
        $topcat = reset($allcoursecatcats);
        $parentcat = end($allcoursecatcats);
        $this->assertEquals($topcat->id, $parentcat->parent);

        // Make sure we have 2 questions in the above course category level question category.
        $questions = $this->get_question_data(array_map(static fn($cat) => $cat->id, $allcoursecatcats));
        $this->assertCount(2, $questions);
        $question = reset($questions);
        $this->assertEquals($parentcat->id, $question->categoryid);
        // Make sure there are files in the expected fileareas for this question.
        $fs = get_file_storage();
        $this->assertTrue($fs->file_exists($this->coursecatcontext->id, 'question', 'questiontext', $question->id, '/', '1.png'));
        $this->assertTrue(
            $fs->file_exists($this->coursecatcontext->id, 'question', 'generalfeedback', $question->id, '/', '2.png'),
        );
        $this->assertTrue($fs->file_exists($this->coursecatcontext->id, 'qtype_essay', 'graderinfo', $question->id, '/', '3.png'));

        // Make sure we have 4 question categories at course level (including 'top') with some questions in them.
        $allcoursecats = $DB->get_records('question_categories', ['contextid' => $this->coursecontext->id], 'id ASC');
        $this->assertCount(4, $allcoursecats);
        $grandparentcat = next($allcoursecats);
        $parentcat = next($allcoursecats);
        $this->assertEquals($grandparentcat->id, $parentcat->parent);
        $childcat = end($allcoursecats);
        $this->assertEquals($parentcat->id, $childcat->parent);
        $questions = $this->get_question_data(array_map(static fn($cat) => $cat->id, $allcoursecats));
        // 2 active questions and 1 stale question, for a total of 3.
        $this->assertCount(3, $questions);

        // Make sure we have 6 stale question categories at course level (including 'top') with some questions in them.
        $questioncats = $DB->get_records('question_categories', ['contextid' => $this->stalecoursecontext->id], 'id ASC');
        $this->assertCount(6, $questioncats);
        $topcat = reset($questioncats);
        $parentcat1 = next($questioncats);
        $childcat1 = next($questioncats);
        $parentcat2 = next($questioncats);
        $childcat2 = next($questioncats);
        $grandchildcat1 = next($questioncats);
        $this->assertEquals($topcat->id, $parentcat1->parent);
        $this->assertEquals($topcat->id, $parentcat2->parent);
        $this->assertEquals($parentcat1->id, $childcat1->parent);
        $this->assertEquals($parentcat2->id, $childcat2->parent);
        $this->assertEquals($childcat2->id, $grandchildcat1->parent);
        // There should be 4 question bank entries with 1 version each, and 1 with 3 versions, for a total of 7.
        $questionids = $this->get_question_data(array_map(static fn($cat) => $cat->id, $questioncats));
        $this->assertCount(7, $questionids);

        // Make sure the "Used-Unused-Empty" course has 4 question categories (including 'top') with 0, 2, 2, and 0
        // questions respectively.
        $questioncats = $DB->get_records('question_categories', ['contextid' => $this->usedunusedcontext->id], 'id ASC');
        $this->assertCount(4, $questioncats);
        $topcat = reset($questioncats);
        $this->assertEmpty($this->get_question_data([$topcat->id]));
        $usedcat = next($questioncats);
        $this->assertCount(2, $this->get_question_data([$usedcat->id]));
        $unusedcat = next($questioncats);
        $this->assertCount(2, $this->get_question_data([$unusedcat->id]));
        $emptycat = next($questioncats);
        $this->assertCount(0, $this->get_question_data([$emptycat->id]));

        // The question reference for the random question is using the "used" category, and the site context.
        $coursemods = get_course_mods($this->usedunusedcontext->instanceid);
        $quiz = reset($coursemods);
        $quizsettings = quiz_settings::create($quiz->instance);
        $structure = $quizsettings->get_structure();
        $randomquestionslot = $structure->get_question_in_slot(2);
        $this->assertEquals($this->usedunusedcontext->id, $randomquestionslot->contextid);
        $this->assertEquals($usedcat->id, $randomquestionslot->filtercondition['filter']['category']['values'][0]);
    }

    /**
     * Assert the installation task handles the deprecated contexts correctly.
     *
     * @return void
     */
    public function test_qbank_install(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setup_pre_install_data();

        $task = new transfer_question_categories();
        $task->execute();

        // Site context checks.

        $sitecontext = context_system::instance();
        $sitecontextcats = $DB->get_records('question_categories', ['contextid' => $sitecontext->id]);

        // Should be no site context question categories left, not even 'top'.
        $this->assertCount(0, $sitecontextcats);

        $sitemodinfo = get_fast_modinfo(get_site());
        $siteqbanks = $sitemodinfo->get_instances_of('qbank');

        // We should have 1 new module on the site course.
        $this->assertCount(1, $siteqbanks);
        $siteqbank = reset($siteqbanks);

        // Make doubly sure it got put into section 0 as these mod types are not rendered to the course page.
        $this->assertEquals(0, $siteqbank->sectionnum);

        // It should have our determined name.
        $this->assertEquals('System shared question bank', $siteqbank->name);
        $sitemodcontext = context_module::instance($siteqbank->get_course_module_record()->id);

        // The 3 question categories including 'top' should now be at the new module context with their order intact.
        $sitemodcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $sitemodcontext->id],
            'id ASC'
        );
        $this->assertCount(2, $sitemodcats);
        $topcat = question_get_top_category($sitemodcontext->id);
        $parentcat = reset($sitemodcats);
        $childcat = next($sitemodcats);
        $this->assertEquals($topcat->id, $parentcat->parent);
        $this->assertEquals($parentcat->id, $childcat->parent);

        // The random question should now point to the questions in the site course question bank.
        $randomcourse = $DB->get_record('course', ['shortname' => 'Random']);
        $coursemods = get_course_mods($randomcourse->id);
        $randomquiz = reset($coursemods);
        $randomquizsettings = quiz_settings::create($randomquiz->instance);
        $structure = $randomquizsettings->get_structure();
        $randomquestionslot = $structure->get_question_in_slot(1);
        $this->assertEquals($randomquestionslot->contextid, $sitemodcontext->id);
        $loader = new random_question_loader(new \qubaid_list([]));
        $randomquestions = $loader->get_filtered_questions($randomquestionslot->filtercondition['filter']);
        $this->assertCount(2, $randomquestions);
        $randomq1 = reset($randomquestions);
        $randomq2 = end($randomquestions);
        $this->assertEquals($parentcat->id, $randomq1->category);
        $this->assertEquals($childcat->id, $randomq2->category);

        // Course category context checks.

        // Make sure that the course category has no question categories, not even 'top'.
        $this->assertEquals(0, $DB->count_records('question_categories', ['contextid' => $this->coursecatcontext->id]));

        $courses = $DB->get_records('course', ['category' => $this->coursecatcontext->instanceid], 'id ASC');
        // We should have 2 courses in this category now, the original and the new one that holds our new mod instance.
        $this->assertCount(2, $courses);
        $newcourse = end($courses);
        $coursecat = $DB->get_record('course_categories', ['id' => $newcourse->category]);

        // Make sure the new course shortname is a unique name based on the category name and id.
        $this->assertEquals("$coursecat->name-$coursecat->id", $newcourse->shortname);

        // Make sure the new course fullname is based on the category name.
        $this->assertEquals("Shared teaching resources for category: $coursecat->name", $newcourse->fullname);

        $coursemodinfo = get_fast_modinfo($newcourse);
        $coursecatqbanks = $coursemodinfo->get_instances_of('qbank');

        // We should have 1 new module on this course.
        $this->assertCount(1, $coursecatqbanks);
        $coursecatqbank = reset($coursecatqbanks);

        // Make sure the new module name is what we expect.
        $this->assertEquals("$coursecat->name shared question bank", $coursecatqbank->name);

        $coursecatqcats = $DB->get_records('question_categories', ['contextid' => $coursecatqbank->context->id], 'parent ASC');

        // The 2 question categories should be moved to the module context now.
        $this->assertCount(2, $coursecatqcats);
        $topcat = reset($coursecatqcats);
        $parentcat = end($coursecatqcats);

        // Make sure the parent orders are correct.
        $this->assertEquals($topcat->id, $parentcat->parent);

        // Course context checks.

        // Make sure that the course has no more question categories, not even 'top'.
        $this->assertEquals(0, $DB->count_records('question_categories', ['contextid' => $this->coursecontext->id]));

        $coursemodinfo = get_fast_modinfo($this->coursecontext->instanceid);
        $course = $coursemodinfo->get_course();
        $courseqbanks = $coursemodinfo->get_instances_of('qbank');

        // We should have only 1 new mod instance in this course.
        $this->assertCount(1, $coursecatqbanks);

        // The module name should be what we expect.
        $courseqbank = reset($courseqbanks);
        $this->assertEquals("$course->shortname shared question bank", $courseqbank->name);

        // Make sure the question categories still exist and that we have a new top one at the new module context.
        $topcat = question_get_top_category($courseqbank->context->id);
        $courseqcats = $DB->get_records_select('question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $courseqbank->context->id],
            'id ASC'
        );
        $grandparentcat = reset($courseqcats);
        $parentcat = next($courseqcats);
        $childcat = next($courseqcats);

        $this->assertEquals($topcat->id, $grandparentcat->parent);
        $this->assertEquals($grandparentcat->id, $parentcat->parent);
        $this->assertEquals($parentcat->id, $childcat->parent);
        // Make sure the two active questions were migrated with their categories, but not the stale question.
        $migratedquestions = $this->get_question_data([$parentcat->id, $childcat->id]);
        $this->assertCount(2, $migratedquestions);
        foreach ($migratedquestions as $migratedquestion) {
            $this->assertTrue($migratedquestion->status === question_version_status::QUESTION_STATUS_READY);
        }

        // Stale course context checks.

        // Make sure the stale course has no categories attached to it anymore and the questions were removed.
        $this->assertFalse($DB->record_exists('question_categories', ['contextid' => $this->stalecoursecontext->id]));
        foreach ($this->stalequestions as $stalequestion) {
            $this->assertFalse($DB->record_exists('question', ['id' => $stalequestion->id]));
        }
        // Make sure the we did not create a qbank in the stale course.
        $this->assertEmpty(get_fast_modinfo($this->stalecoursecontext->instanceid)->get_instances_of('qbank'));

        // Quiz module checks.

        // Make sure the 3 categories at quiz context, including 'top' have not been touched.
        $quizcategories = $DB->get_records('question_categories', ['contextid' => $this->quizcontext->id]);
        $this->assertCount(3, $quizcategories);
        $questions = $this->get_question_data(array_map(static fn($cat) => $cat->id, $quizcategories));
        $this->assertCount(2, $questions);

        // Used-Unused-Empty checks.
        // The empty category should have been removed. The other categories should both have been migrated to a qbank module,
        // with all of their questions.
        $usedunusedmodinfo = get_fast_modinfo($this->usedunusedcontext->instanceid);
        $usedunusedcourse = $usedunusedmodinfo->get_course();
        $usedunusedqbanks = $usedunusedmodinfo->get_instances_of('qbank');
        $usedunusedqbank = reset($usedunusedqbanks);
        $this->assertEquals("$usedunusedcourse->shortname shared question bank", $usedunusedqbank->name);

        // We should now only have 3 categories. Top, used and unused.
        $usedunusedcats = $DB->get_records(
            'question_categories',
            ['contextid' => $usedunusedqbank->context->id],
            fields: 'name, id',
        );
        $this->assertCount(3, $usedunusedcats);
        $this->assertArrayHasKey('top', $usedunusedcats);
        $this->assertArrayHasKey('Used Question Cat', $usedunusedcats);
        $this->assertArrayHasKey('Unused Question Cat', $usedunusedcats);
        $this->assertArrayNotHasKey('Empty Question Cat', $usedunusedcats);

        $this->assertEmpty($this->get_question_data([$usedunusedcats['top']->id]));
        $this->assertCount(2, $this->get_question_data([$usedunusedcats['Used Question Cat']->id]));
        $this->assertCount(2, $this->get_question_data([$usedunusedcats['Unused Question Cat']->id]));

        // The question reference for the random question is using the same category, but the new context.
        $modinfo = get_fast_modinfo($this->usedunusedcontext->instanceid);
        $quizzes = $modinfo->get_instances_of('quiz');
        $quiz = reset($quizzes);
        $quizsettings = quiz_settings::create($quiz->instance);
        $structure = $quizsettings->get_structure();
        $randomquestionslot = $structure->get_question_in_slot(2);
        $this->assertEquals($usedunusedqbank->context->id, $randomquestionslot->contextid);
        $this->assertEquals(
            $usedunusedcats['Used Question Cat']->id,
            $randomquestionslot->filtercondition['filter']['category']['values'][0]
        );
    }

    /**
     * Assert the installation task handles the missing contexts correctly.
     *
     * @return void
     */
    public function test_qbank_install_with_missing_context(): void {
        global $DB;
        $this->resetAfterTest();
        self::setAdminUser();

        $questiongenerator = self::getDataGenerator()->get_plugin_generator('core_question');

        // The problem is that question categories that used to related to contextids
        // which no longer exist are now all moved to the new system-level shared
        // question bank. This moving categories together can cause unique key violations.

        // Create 2 orphaned categories where the contextid no longer exists, with the same stamp and idnumber.
        // We need to do this by creating in a real context, then deleting the context,
        // because create category logs, which needs a valid context id.
        $tamperedstamp = make_unique_id_code();
        $context1 = context_course::instance(self::getDataGenerator()->create_course()->id);
        $oldcat1 = $this->create_question_category('Lost category 1', $context1->id);
        $oldcat1->stamp = $tamperedstamp;
        $oldcat1->idnumber = 'tamperedidnumber';
        $DB->update_record('question_categories', $oldcat1);
        $DB->delete_records('context', ['id' => $context1->id]);

        $context2 = context_course::instance(self::getDataGenerator()->create_course()->id);
        $oldcat2 = $this->create_question_category('Lost category 2', $context2->id);
        $oldcat2->stamp = $tamperedstamp;
        $oldcat2->idnumber = 'tamperedidnumber';
        $DB->update_record('question_categories', $oldcat2);
        $DB->delete_records('context', ['id' => $context2->id]);

        // Add a question to each category.
        $question1 = $questiongenerator->create_question('shortanswer', null, ['category' => $oldcat1->id]);
        $question2 = $questiongenerator->create_question('shortanswer', null, ['category' => $oldcat2->id]);

        // Make the questions 'in use'.
        $quizcourse = self::getDataGenerator()->create_course();
        $quiz = self::getDataGenerator()->get_plugin_generator('mod_quiz')->create_instance(
            ['course' => $quizcourse->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']
        );
        quiz_add_quiz_question($question1->id, $quiz);
        quiz_add_quiz_question($question2->id, $quiz);

        // Make sure the caches are reset so that the contexts are not cached.
        \core\context_helper::reset_caches();

        // Run the task.
        $task = new transfer_question_categories();
        $task->execute();
        // An important thing to verify is that the task completes without errors,
        // for example unique key violations.

        // Verify - there should be a single question bank in the site course with the expected name.
        $sitemodinfo = get_fast_modinfo(get_site());
        $siteqbanks = $sitemodinfo->get_instances_of('qbank');
        $this->assertCount(1, $siteqbanks);
        $siteqbank = reset($siteqbanks);
        $this->assertEquals('System shared question bank', $siteqbank->name);

        // The two previously orphaned categories should now be in this site questions bank, with a top category.
        $sitemodcontext = context_module::instance($siteqbank->get_course_module_record()->id);
        $sitemodcats = $DB->get_records_select(
            'question_categories',
            'parent <> 0 AND contextid = :contextid',
            ['contextid' => $sitemodcontext->id],
            'id ASC',
        );

        // Work out which category is which.
        $movedcat1 = null;
        $movedcat2 = null;
        foreach ($sitemodcats as $movedcat) {
            if ($movedcat->name === $oldcat1->name) {
                $movedcat1 = $movedcat;
            }
            if ($movedcat->name === $oldcat2->name) {
                $movedcat2 = $movedcat;
            }
        }
        $this->assertNotNull($movedcat1);
        $this->assertNotNull($movedcat2);

        // Verify the properties of the moved categories.
        $this->assertNotEquals($movedcat1->stamp, $movedcat2->stamp);
        $this->assertNotEquals($movedcat1->idnumber, $movedcat2->idnumber);
        $this->assertEquals(question_get_top_category($sitemodcontext->id)->id, $movedcat1->parent);
        $this->assertEquals(question_get_top_category($sitemodcontext->id)->id, $movedcat2->parent);
    }

    public function test_fix_wrong_parents(): void {
        $this->resetAfterTest();
        $this->setup_pre_install_data();

        // Create a second course.
        $course2 = self::getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);

        // In course2 we build this category structure:
        // - $course2parentcat -- context $course2context
        // - - $wrongchild1 -- context $this->coursecontext (wrong)
        // - - - $wronggrandchild1 -- context $this->coursecontext (same wrong)
        // - - - $doublywronggrandchild1 -- context $course2context (back right, but not matching its parent)
        // - - $wrongchild2 -- context non-existant A
        // - - - $wronggrandchild2 -- context non-existent A
        // - - - $doublywronggrandchild2 -- context non-existent B.
        $course2parentcat = $this->create_question_category(
            'Course2 parent cat', $course2context->id);

        $wrongchild1 = $this->create_question_category(
            'Child cat with wrong context', $this->coursecontext->id, $course2parentcat->id);
        $wronggrandchild1 = $this->create_question_category(
            'Grandchild of child1 in same wrong context', $this->coursecontext->id, $wrongchild1->id);
        $doublywronggrandchild1 = $this->create_question_category(
            'Grandchild of child1 back in the right context', $course2context->id, $wrongchild1->id);

        $wrongchild2 = $this->create_question_category(
            'Child cat with non-existent context', $course2context->id + 1000, $course2parentcat->id);
        $wronggrandchild2 = $this->create_question_category(
            'Grandchild of child2 with same non-existent context', $course2context->id + 1000, $wrongchild2->id);
        $doublywronggrandchild2 = $this->create_question_category(
            'Grandchild of child2 with different non-existent context', $course2context->id + 2000, $wrongchild2->id);

        // Before we clean up, check that the expected categories are picked up.
        // $wronggrandchild1 & $wronggrandchild2 are not seen, because their contexts match
        // their parent's even though both are wrong. They should still get fixed.
        $task = new transfer_question_categories();
        $this->assertEquals(
            [
                $wrongchild1->id => $wrongchild1->contextid,
                $doublywronggrandchild1->id => $course2context->id,
                $wrongchild2->id => $wrongchild2->contextid,
                $doublywronggrandchild2->id => $doublywronggrandchild2->contextid,
            ],
            $task->get_categories_in_a_different_context_to_their_parent(),
        );

        // Call the cleanup method.
        $task->fix_wrong_parents();

        // Now we expect no mismatches.
        $this->assertEmpty($task->get_categories_in_a_different_context_to_their_parent());

        // Assert that the child categories have been moved to the locations they should have been.
        $this->assert_category_is_in_context_with_parent($this->coursecontext, null, $wrongchild1->id);
        $this->assert_category_is_in_context_with_parent($this->coursecontext, $wrongchild1, $wronggrandchild1->id);
        $this->assert_category_is_in_context_with_parent($course2context, null, $doublywronggrandchild1->id);
        $this->assert_category_is_in_context_with_parent($course2context, $course2parentcat, $wrongchild2->id);
        $this->assert_category_is_in_context_with_parent($course2context, $wrongchild2, $wronggrandchild2->id);
        $this->assert_category_is_in_context_with_parent($course2context, $wrongchild2, $doublywronggrandchild2->id);
    }

    /**
     * Categories with missing contexts that would violate unique keys if moved to the same context as-is are correctly modified.
     */
    public function test_fix_wrong_parents_conflicting_indexes(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setup_pre_install_data();

        // Create a second course.
        $course2 = self::getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);

        // Create a parent category, and 2 child categories in a non-existant context.
        $course2parentcat = $this->create_question_category('Course2 parent cat', $course2context->id);
        $wrongchild1 = $this->create_question_category('Wrong context 1', $this->coursecontext->id + 1000, $course2parentcat->id);
        $wrongchild2 = $this->create_question_category('Wrong context 2', $this->coursecontext->id + 1000, $course2parentcat->id);

        // Set the stamp of one child and the idnumber of another child to match that of the parent context.
        // Moving either of these children to the correct context would cause a conflict.
        $tamperedstamp = make_unique_id_code();
        $wrongchild1->stamp = $tamperedstamp;
        $course2parentcat->stamp = $tamperedstamp;

        $tamperedidnumber = random_string();
        $wrongchild2->idnumber = $tamperedidnumber;
        $course2parentcat->idnumber = $tamperedidnumber;
        $DB->update_record('question_categories', $course2parentcat);
        $DB->update_record('question_categories', $wrongchild1);
        $DB->update_record('question_categories', $wrongchild2);

        // Before we clean up, check that the expected categories are picked up.
        $task = new transfer_question_categories();
        $this->assertEquals(
            [
                $wrongchild1->id => $wrongchild1->contextid,
                $wrongchild2->id => $wrongchild2->contextid,
            ],
            $task->get_categories_in_a_different_context_to_their_parent(),
        );

        // Call the cleanup method.
        $task->fix_wrong_parents();

        // Now we expect no mismatches.
        $this->assertEmpty($task->get_categories_in_a_different_context_to_their_parent());

        // Assert that the child categories have been moved to the parent context.
        $this->assert_category_is_in_context_with_parent($course2context, $course2parentcat, $wrongchild1->id);
        $this->assert_category_is_in_context_with_parent($course2context, $course2parentcat, $wrongchild2->id);

        // Categories with the same stamp should now be different.
        $this->assertNotEquals(
            $DB->get_field('question_categories', 'stamp', ['id' => $course2parentcat->id]),
            $DB->get_field('question_categories', 'stamp', ['id' => $wrongchild1->id]),
        );
        // Categories with same idnumber should now be different.
        $this->assertNotEquals(
            $DB->get_field('question_categories', 'idnumber', ['id' => $course2parentcat->id]),
            $DB->get_field('question_categories', 'idnumber', ['id' => $wrongchild2->id]),
        );
    }

    /**
     * Assert that the category with id $categoryid is in context $expectedcontext, with the given parent.
     *
     * @param context $expectedcontext the expected context for the category with id $categoryid.
     * @param stdClass|null $expectedparent the expected parent category.
     *      null means the Top category in $expectedcontext.
     * @param int $categoryid the id of the category to check.
     */
    protected function assert_category_is_in_context_with_parent(
        context $expectedcontext,
        ?stdClass $expectedparent,
        int $categoryid,
    ): void {
        global $DB;

        if ($expectedparent === null) {
            $expectedparent = $DB->get_record(
                'question_categories',
                ['contextid' => $expectedcontext->id, 'parent' => 0],
                '*',
                MUST_EXIST,
            );
        }

        $actualcategory = $DB->get_record('question_categories', ['id' => $categoryid]);
        $this->assertEquals($expectedparent->id, $actualcategory->parent,
            "Checking parent of category $actualcategory->name.");
        $this->assertEquals($expectedcontext->id, $actualcategory->contextid,
            "Checking context of category $actualcategory->name.");
    }

    public function test_transfer_questions(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setup_pre_install_data();

        $task = new \mod_qbank\task\transfer_question_categories();
        $task->execute();

        // Assert that files are still in their original context.
        $courses = $DB->get_records('course', ['category' => $this->coursecatcontext->instanceid], 'id ASC');
        $newcourse = end($courses);
        $coursemodinfo = get_fast_modinfo($newcourse);
        $coursecatqbanks = $coursemodinfo->get_instances_of('qbank');
        $coursecatqbank = reset($coursecatqbanks);
        $coursecatqcats = $DB->get_records('question_categories', ['contextid' => $coursecatqbank->context->id], 'parent ASC');
        $parentcat = end($coursecatqcats);
        $questions = get_questions_category($parentcat, true);
        $question = reset($questions);
        $fs = get_file_storage();
        $this->assertTrue($fs->file_exists(
            $this->coursecatcontext->id,
            'question',
            'questiontext',
            $question->id,
            '/',
            '1.png'
        ));
        $this->assertTrue($fs->file_exists(
            $this->coursecatcontext->id,
            'question',
            'generalfeedback',
            $question->id,
            '/',
            '2.png'
        ));
        $this->assertTrue($fs->file_exists(
            $this->coursecatcontext->id,
            'qtype_essay',
            'graderinfo',
            $question->id,
            '/',
            '3.png'
        ));
        $this->assertFalse($fs->file_exists(
            $coursecatqbank->context->id,
            'question',
            'questiontext',
            $question->id,
            '/',
            '1.png'
        ));
        $this->assertFalse($fs->file_exists(
            $coursecatqbank->context->id,
            'question',
            'generalfeedback',
            $question->id,
            '/',
            '2.png'
        ));
        $this->assertFalse($fs->file_exists(
            $coursecatqbank->context->id,
            'qtype_essay',
            'graderinfo',
            $question->id,
            '/',
            '3.png'
        ));

        $this->assertFalse(question_bank_helper::has_bank_migration_task_completed_successfully());

        $questiontasks = manager::get_adhoc_tasks(transfer_questions::class);

        // We should have a transfer_questions task for each category that was moved.
        // 2 site categories,
        // 1 coursecat category,
        // 3 regular course categories,
        // 2 used/unused course categories.
        $this->assertCount(8, $questiontasks);

        $this->expectOutputRegex('~Moving files and tags~');
        // Delete one of the categories before running the tasks, to ensure missing categories are handled gracefully.
        $unusedcat = $DB->get_record('question_categories', ['name' => 'Unused Question Cat']);
        question_category_delete_safe($unusedcat);
        $this->expectOutputRegex("~Could not find a category record for id {$unusedcat->id}. Terminating task.~");

        $this->runAdhocTasks();

        // The files have now been moved to the new context.
        $this->assertFalse($fs->file_exists(
            $this->coursecatcontext->id,
            'question',
            'questiontext',
            $question->id,
            '/',
            '1.png'
        ));
        $this->assertFalse($fs->file_exists(
            $this->coursecatcontext->id,
            'question',
            'generalfeedback',
            $question->id,
            '/',
            '2.png'
        ));
        $this->assertFalse($fs->file_exists(
            $this->coursecatcontext->id,
            'qtype_essay',
            'graderinfo',
            $question->id,
            '/',
            '3.png'
        ));
        $this->assertTrue($fs->file_exists(
            $coursecatqbank->context->id,
            'question',
            'questiontext',
            $question->id,
            '/',
            '1.png'
        ));
        $this->assertTrue($fs->file_exists(
            $coursecatqbank->context->id,
            'question',
            'generalfeedback',
            $question->id,
            '/',
            '2.png'
        ));
        $this->assertTrue($fs->file_exists(
            $coursecatqbank->context->id,
            'qtype_essay',
            'graderinfo',
            $question->id,
            '/',
            '3.png'
        ));

        $this->assertTrue(question_bank_helper::has_bank_migration_task_completed_successfully());
    }

    public function test_qbank_install_resilience(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setup_pre_install_data();

        require_once(__DIR__ . '/../fixtures/testable_transfer_question_categories.php');
        $task = new testable_transfer_question_categories();
        try {
            $task->execute();
        } catch (moodle_exception $e) {
            // We expect a failure here, but we ignore this.
            $this->assertStringContainsString('This is a mocked exception for testing purposes.', $e->getMessage());
        }
        // We want to verify a failure does not prevent the creation of tasks with hitherto transferred categories and their data.
        // We should have a transfer_questions task for two of the categories that were moved.
        $questiontasks = manager::get_adhoc_tasks(transfer_questions::class);
        $this->assertCount(2, $questiontasks);
    }
}
