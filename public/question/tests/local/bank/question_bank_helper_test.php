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

namespace core_question;

use core\exception\coding_exception;
use core_question\local\bank\question_bank_helper;

/**
 * question bank helper class tests.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_question\local\bank\question_bank_helper
 */
final class question_bank_helper_test extends \advanced_testcase {

    /**
     * Assert that at least 1 module type that shares questions exists and that mod_qbank is in the returned list.
     *
     * @return void
     * @covers ::get_activity_types_with_shareable_questions
     */
    public function test_get_shareable_modules(): void {
        $openmods = question_bank_helper::get_activity_types_with_shareable_questions();
        $this->assertGreaterThanOrEqual(1, count($openmods));
        $this->assertContains('qbank', $openmods);
        $this->assertNotContains('quiz', $openmods);
    }

    /**
     * Assert that at least 1 module type that does not share questions exists and that mod_quiz is in the returned list.
     *
     * @return void
     * @covers ::get_activity_types_with_private_questions
     */
    public function test_get_private_modules(): void {
        $closedmods = question_bank_helper::get_activity_types_with_private_questions();
        $this->assertGreaterThanOrEqual(1, count($closedmods));
        $this->assertContains('quiz', $closedmods);
        $this->assertNotContains('qbank', $closedmods);
    }

    /**
     * Setup some courses with quiz and qbank module instances and set different permissions for a user.
     * Then assert that the correct results are returned from calls to the class methods.
     *
     * @covers ::get_activity_instances_with_shareable_questions
     * @covers ::get_activity_instances_with_private_questions
     * @return void
     */
    public function test_get_instances(): void {
        global $DB;

        $this->resetAfterTest();
        $user = self::getDataGenerator()->create_user();
        $roles = $DB->get_records('role', [], '', 'shortname, id');
        self::setUser($user);

        $qgen = self::getDataGenerator()->get_plugin_generator('core_question');
        $sharedmodgen = self::getDataGenerator()->get_plugin_generator('mod_qbank');
        $privatemodgen = self::getDataGenerator()->get_plugin_generator('mod_quiz');
        $category1 = self::getDataGenerator()->create_category();
        $category2 = self::getDataGenerator()->create_category();
        $course1 = self::getDataGenerator()->create_course(['category' => $category1->id]);
        $course2 = self::getDataGenerator()->create_course(['category' => $category1->id]);
        $course3 = self::getDataGenerator()->create_course(['category' => $category2->id]);
        $course4 = self::getDataGenerator()->create_course(['category' => $category2->id]);

        $sharedmod1 = $sharedmodgen->create_instance(['course' => $course1]);
        $sharedmod1context = \context_module::instance($sharedmod1->cmid);
        $sharedmod1qcat1 = question_get_default_category($sharedmod1context->id);
        $sharedmod1qcat2 = $qgen->create_question_category(['contextid' => $sharedmod1context->id]);
        $sharedmod1qcat2child = $qgen->create_question_category([
            'contextid' => $sharedmod1context->id,
            'parent' => $sharedmod1qcat2->id,
            'name' => 'A, B, C',
        ]);
        $privatemod1 = $privatemodgen->create_instance(['course' => $course1]);
        $privatemod1context = \context_module::instance($privatemod1->cmid);
        $privatemod1qcat1 = question_get_default_category($privatemod1context->id);
        role_assign($roles['editingteacher']->id, $user->id, \context_module::instance($sharedmod1->cmid));
        role_assign($roles['editingteacher']->id, $user->id, \context_module::instance($privatemod1->cmid));

        $sharedmod2 = $sharedmodgen->create_instance(['course' => $course2]);
        $sharedmod2context = \context_module::instance($sharedmod2->cmid);
        $sharedmod2qcat1 = question_get_default_category($sharedmod2context->id);
        $sharedmod2qcat2 = $qgen->create_question_category(['contextid' => $sharedmod2context->id]);
        $sharedmod2qcat2child = $qgen->create_question_category([
            'contextid' => $sharedmod2context->id,
            'parent' => $sharedmod2qcat2->id,
        ]);
        $privatemod2 = $privatemodgen->create_instance(['course' => $course2]);
        $privatemod2context = \context_module::instance($privatemod2->cmid);
        $privatemod1qcat1 = question_get_default_category($privatemod2context->id);
        role_assign($roles['editingteacher']->id, $user->id, \context_module::instance($sharedmod2->cmid));
        role_assign($roles['editingteacher']->id, $user->id, \context_module::instance($privatemod2->cmid));

        // User doesn't have the capability on this one.
        $sharedmod3 = $sharedmodgen->create_instance(['course' => $course3]);
        $privatemod3 = $privatemodgen->create_instance(['course' => $course3]);

        // Exclude this course in the results despite having the capability.
        $sharedmod4 = $sharedmodgen->create_instance(['course' => $course4]);
        role_assign($roles['editingteacher']->id, $user->id, \context_module::instance($sharedmod4->cmid));

        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(
            [],
            [$course4->id],
            ['moodle/question:add'],
            true
        );

        $count = 0;
        foreach ($sharedbanks as $courseinstance) {
            // Must all be mod_qbanks.
            $this->assertEquals('qbank', $courseinstance->cminfo->modname);
            // Must have 2 categories each bank.
            $this->assertCount(3, $courseinstance->questioncategories);
            // Must not include the bank the user does not have access to.
            $this->assertNotEquals($sharedmod3->name, $courseinstance->name);
            $this->assertNotEquals($privatemod3->name, $courseinstance->name);
            $count++;
        }
        // Expect count of 2 bank instances.
        $this->assertEquals(2, $count);

        $privatebanks = question_bank_helper::get_activity_instances_with_private_questions(
            [$course1->id],
            [],
            ['moodle/question:add'],
            true
        );

        $count = 0;
        foreach ($privatebanks as $courseinstance) {
            // Must all be mod_quiz.
            $this->assertEquals('quiz', $courseinstance->cminfo->modname);
            // Must have 1 category in each bank.
            $this->assertCount(1, $courseinstance->questioncategories);
            // Must only include the bank from course 1.
            $this->assertNotContains($courseinstance->cminfo->course, [$course2->id, $course3->id, $course4->id]);
            $count++;
        }
        // Expect count of 1 bank instances.
        $this->assertEquals(1, $count);
    }

    /**
     * Tests if applying the limit and capability checks are interacting properly.
     *
     * @covers ::get_activity_instances_with_shareable_questions
     * @covers ::get_activity_instances_with_private_questions
     */
    public function test_get_instances_with_limit_and_capabilities(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = self::getDataGenerator()->create_user();
        self::setUser($teacher);
        $editingteacherroleid = $DB->get_record('role', ['shortname' => 'editingteacher'])->id;

        $sharedmodgen = self::getDataGenerator()->get_plugin_generator('mod_qbank');
        // Create 20 question banks, and give the teacher permission to edit only in the last 5.
        for ($i = 0; $i < 20; $i++) {
            $sharedmod = $sharedmodgen->create_instance(['course' => $course]);
            if ($i >= 15) {
                role_assign($editingteacherroleid, $teacher->id, \context_module::instance($sharedmod->cmid));
            }
        }

        // We now have created 20 banks. If the limit is below 20, we have to make sure that the code does NOT first apply a limit
        // of, for example, 15 and check capabilities afterward. This would mean we end up returning 0 qbanks.
        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(
            havingcap: ['moodle/question:add'],
            limit: 15
        );
        $this->assertCount(5, $sharedbanks);

        // On the other hand, check if the limit parameter works at all and is being applied correctly.
        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(
            havingcap: ['moodle/question:add'],
            limit: 2
        );
        $this->assertCount(2, $sharedbanks);

        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(limit: 10);
        $this->assertCount(10, $sharedbanks);

        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(limit: 30);
        $this->assertCount(20, $sharedbanks);

        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(limit: 0);
        $this->assertCount(20, $sharedbanks);
    }

    /**
     * We should be able to filter sharable question bank instances by name.
     *
     * @covers ::get_activity_instances_with_shareable_questions
     * @return void
     */
    public function test_get_instances_by_name(): void {
        global $DB;

        $this->resetAfterTest();
        $user = self::getDataGenerator()->create_user();
        $roles = $DB->get_records('role', [], '', 'shortname, id');
        self::setUser($user);

        $sharedmodgen = self::getDataGenerator()->get_plugin_generator('mod_qbank');
        $category1 = self::getDataGenerator()->create_category();
        $course1 = self::getDataGenerator()->create_course(['category' => $category1->id]);
        role_assign($roles['editingteacher']->id, $user->id, \core\context\course::instance($course1->id));

        $sharedmods = [];
        for ($i = 1; $i <= 21; $i++) {
            $sharedmods[$i] = $sharedmodgen->create_instance(['course' => $course1, 'name' => "Shared bank {$i}"]);
        }
        $sharedmods[22] = $sharedmodgen->create_instance(['course' => $course1, 'name' => "Another bank"]);

        // We get all banks with no parameters.
        $allsharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions();
        $this->assertCount(22, $allsharedbanks);

        // Searching for "2", we get the 4 banks with "2" in the name.
        $twobanks = question_bank_helper::get_activity_instances_with_shareable_questions(search: '2');
        $this->assertCount(4, $twobanks);
        $this->assertEquals(
            [$sharedmods[2]->cmid, $sharedmods[12]->cmid, $sharedmods[20]->cmid, $sharedmods[21]->cmid],
            array_map(fn($bank) => $bank->modid, $twobanks),
        );

        // Searching for "Shared bank" with no limit, we should get all 21, but not "Another bank".
        $sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(search: 'Shared bank');
        $this->assertCount(21, $sharedbanks);
        $this->assertEmpty(array_filter($sharedbanks, fn($bank) => in_array($bank->name, ['Another bank'])));

        // Searching for "Shared bank" with a limit of 20, we should get all except number 21 and "Another bank".
        $limitedbanks = question_bank_helper::get_activity_instances_with_shareable_questions(search: 'Shared bank', limit: 20);
        $this->assertCount(20, $limitedbanks);
        $this->assertEmpty(array_filter($limitedbanks, fn($bank) => in_array($bank->name, ['Shared bank 21', 'Another bank'])));
    }

    /**
     * Assert creating a default mod_qbank instance on a course provides the expected boilerplate settings.
     *
     * @return void
     * @covers ::create_default_open_instance
     */
    public function test_create_default_open_instance(): void {
        global $DB;

        $this->resetAfterTest();
        self::setAdminUser();

        $course = self::getDataGenerator()->create_course();

        // Create the instance and assert default values.
        question_bank_helper::create_default_open_instance($course, $course->fullname);
        $modinfo = get_fast_modinfo($course);
        $cminfos = $modinfo->get_instances_of('qbank');
        $this->assertCount(1, $cminfos);
        $cminfo = reset($cminfos);
        $this->assertEquals($course->fullname, $cminfo->get_name());
        $this->assertEquals(0, $cminfo->sectionnum);
        $modrecord = $DB->get_record('qbank', ['id' => $cminfo->instance]);
        $this->assertEquals(question_bank_helper::TYPE_STANDARD, $modrecord->type);
        $this->assertEmpty($cminfo->idnumber);
        $this->assertEmpty($cminfo->content);

        // Create a system type bank.
        question_bank_helper::create_default_open_instance($course, 'System bank 1', question_bank_helper::TYPE_SYSTEM);

        // Try and create another system type bank.
        question_bank_helper::create_default_open_instance($course, 'System bank 2', question_bank_helper::TYPE_SYSTEM);

        $modinfo = get_fast_modinfo($course);
        $cminfos = $modinfo->get_instances_of('qbank');
        $cminfos = array_filter($cminfos, static function($cminfo) {
            global $DB;
            return $DB->record_exists('qbank', ['id' => $cminfo->instance, 'type' => question_bank_helper::TYPE_SYSTEM]);
        });

        // Can only be 1 system 'type' bank per course.
        $this->assertCount(1, $cminfos);
        $cminfo = reset($cminfos);
        $this->assertEquals('System bank 1', $cminfo->get_name());
        $moddata = $DB->get_record('qbank', ['id' => $cminfo->instance]);
        $this->assertEquals(get_string('systembankdescription', 'question'), $moddata->intro);
        $this->assertEquals(1, $cminfo->showdescription);
    }

    /**
     * Create a default instance, passing a name that is too long for the database.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function test_create_default_open_instance_with_long_name(): void {
        $this->resetAfterTest();
        self::setAdminUser();

        $coursename = random_string(question_bank_helper::BANK_NAME_MAX_LENGTH);
        $courseshortname = \core_text::substr($coursename, 0, 255);
        $course = self::getDataGenerator()->create_course(['shortname' => $courseshortname]);

        $this->expectExceptionMessage('The provided bankname is too long for the database field.');
        question_bank_helper::create_default_open_instance(
            $course,
            get_string('defaultbank', 'core_question', ['coursename' => $coursename]),
        );
    }

    /**
     * Create a default instance, passing a multibyte-character name.
     *
     * The name has more bytes than the max length, but is within the character limit as they are multibyte characters.
     */
    public function test_create_default_open_instance_with_multibyte_name(): void {
        $this->resetAfterTest();
        self::setAdminUser();

        $coursename = '';
        while (strlen($coursename) < question_bank_helper::BANK_NAME_MAX_LENGTH) {
            $coursename .= '🙂';
        }
        $course = self::getDataGenerator()->create_course(['shortname' => '🙂']);
        $bankname = get_string('defaultbank', 'core_question', ['coursename' => $coursename]);
        $this->assertTrue(strlen($bankname) > question_bank_helper::BANK_NAME_MAX_LENGTH);
        $this->assertTrue(\core_text::strlen($bankname) < question_bank_helper::BANK_NAME_MAX_LENGTH);

        question_bank_helper::create_default_open_instance($course, $bankname);

        $modinfo = get_fast_modinfo($course);
        $cminfos = $modinfo->get_instances_of('qbank');
        $this->assertCount(1, $cminfos);
        $cminfo = reset($cminfos);
        $this->assertEquals($bankname, $cminfo->get_name());
    }

    /**
     * Attempting to create a default bank with an empty name throws an exception and does not create the bank.
     */
    public function test_create_default_open_instance_with_empty_name(): void {
        $this->resetAfterTest();
        self::setAdminUser();

        $course = self::getDataGenerator()->create_course();
        $bankname = '';

        try {
            question_bank_helper::create_default_open_instance($course, $bankname);
        } catch (coding_exception $e) {
            $this->assertStringEndsWith(
                'The provided bankname is empty. You must provide a name for the question bank.',
                $e->getMessage(),
            );
        }

        $modinfo = get_fast_modinfo($course);
        $cminfos = $modinfo->get_instances_of('qbank');
        $this->assertCount(0, $cminfos);
    }

    /**
     * Assert that viewing a question bank logs the view for that user up to a maximum of 5 unique bank views.
     *
     * @return void
     * @covers ::get_recently_used_open_banks
     * @covers ::add_bank_context_to_recently_viewed
     */
    public function test_recently_viewed_question_banks(): void {
        $this->resetAfterTest();

        $user = self::getDataGenerator()->create_user();
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();
        self::getDataGenerator()->enrol_user($user->id, $course1->id, 'editingteacher');
        $banks = [];
        $banks[] = self::getDataGenerator()->create_module('qbank', ['course' => $course1->id]);
        $banks[] = self::getDataGenerator()->create_module('qbank', ['course' => $course1->id]);
        $banks[] = self::getDataGenerator()->create_module('qbank', ['course' => $course1->id]);
        $banks[] = self::getDataGenerator()->create_module('qbank', ['course' => $course2->id]);
        $banks[] = self::getDataGenerator()->create_module('qbank', ['course' => $course2->id]);
        $banks[] = self::getDataGenerator()->create_module('qbank', ['course' => $course2->id]);

        self::setUser($user);

        // Trigger bank view on each of them.
        foreach ($banks as $bank) {
            $cat = question_get_default_category(\context_module::instance($bank->cmid)->id, true);
            $context = \context::instance_by_id($cat->contextid);
            question_bank_helper::add_bank_context_to_recently_viewed($context);
        }

        $viewedorder = array_reverse($banks);
        // Check that the courseid filter works.
        $recentlyviewed = question_bank_helper::get_recently_used_open_banks($user->id, $course1->id);
        $this->assertCount(3, $recentlyviewed);
        // We should have the viewed banks in course 2.
        $courseviewed = array_slice($banks, 3, 3);
        $this->assertEqualsCanonicalizing(array_column($recentlyviewed, 'modid'), array_column($courseviewed, 'cmid'));

        // Check that the capability filter works.
        $recentlyviewed = question_bank_helper::get_recently_used_open_banks($user->id, havingcap: ['moodle/question:useall']);
        $this->assertCount(2, $recentlyviewed);
        // We should have the 2 most recently viewed banks in course 1.
        $capabilityviewed = array_slice($banks, 1, 2);
        $this->assertEqualsCanonicalizing(array_column($recentlyviewed, 'modid'), array_column($capabilityviewed, 'cmid'));

        $recentlyviewed = question_bank_helper::get_recently_used_open_banks($user->id);

        // We only keep a record of 5 maximum.
        $this->assertCount(5, $recentlyviewed);
        foreach ($recentlyviewed as $order => $record) {
            $this->assertEquals($viewedorder[$order]->cmid, $record->modid);
        }

        // Now if we view one of those again it should get bumped to the front of the list.
        $bank3cat = question_get_default_category(\context_module::instance($banks[2]->cmid)->id, true);
        $bank3context = \context::instance_by_id($bank3cat->contextid);
        question_bank_helper::add_bank_context_to_recently_viewed($bank3context);

        $recentlyviewed = question_bank_helper::get_recently_used_open_banks($user->id);

        // We should still have 5 maximum.
        $this->assertCount(5, $recentlyviewed);
        // The recently viewed on got bumped to the front.
        $this->assertEquals($banks[2]->cmid, $recentlyviewed[0]->modid);
        // The others got sorted accordingly behind it.
        $this->assertEquals($banks[5]->cmid, $recentlyviewed[1]->modid);
        $this->assertEquals($banks[4]->cmid, $recentlyviewed[2]->modid);
        $this->assertEquals($banks[3]->cmid, $recentlyviewed[3]->modid);
        $this->assertEquals($banks[1]->cmid, $recentlyviewed[4]->modid);

        // Now create a quiz and trigger the bank view of it.
        $quiz = self::getDataGenerator()->get_plugin_generator('mod_quiz')->create_instance(['course' => $course1]);
        $quizcat = question_get_default_category(\context_module::instance($quiz->cmid)->id, true);
        $quizcontext = \context::instance_by_id($quizcat->contextid);
        question_bank_helper::add_bank_context_to_recently_viewed($quizcontext);

        $recentlyviewed = question_bank_helper::get_recently_used_open_banks($user->id);
        // We should still have 5 maximum.
        $this->assertCount(5, $recentlyviewed);

        // Make sure that we only store bank views for plugins that support FEATURE_PUBLISHES_QUESTIONS.
        foreach ($recentlyviewed as $record) {
            $this->assertNotEquals($quiz->cmid, $record->modid);
        }

        // Now delete one of the viewed bank modules and get the records again.
        \core_courseformat\formatactions::cm($course1->id)->delete($banks[2]->cmid);
        $recentlyviewed = question_bank_helper::get_recently_used_open_banks($user->id);
        $this->assertCount(4, $recentlyviewed);

        // Check the order was retained.
        $this->assertEquals($banks[5]->cmid, $recentlyviewed[0]->modid);
        $this->assertEquals($banks[4]->cmid, $recentlyviewed[1]->modid);
        $this->assertEquals($banks[3]->cmid, $recentlyviewed[2]->modid);
        $this->assertEquals($banks[1]->cmid, $recentlyviewed[3]->modid);
    }

    /**
     * Assert that getting a default qbank instance on a course works with and without the "$createifnotexists" argument.
     *
     * @return void
     * @covers ::get_default_open_instance_system_type
     */
    public function test_get_default_open_instance_system_type(): void {
        global $DB;

        $this->resetAfterTest();
        self::setAdminUser();

        $course = self::getDataGenerator()->create_course();
        $modinfo = get_fast_modinfo($course);
        $qbanks = $modinfo->get_instances_of('qbank');
        $this->assertCount(0, $qbanks);
        $qbank = question_bank_helper::get_default_open_instance_system_type($course);
        $this->assertNull($qbank);
        $qbank = question_bank_helper::get_default_open_instance_system_type($course, true);
        $this->assertEquals(get_string('systembank', 'question'), $qbank->get_name());
        $modrecord = $DB->get_record('qbank', ['id' => $qbank->instance]);
        $this->assertEquals(question_bank_helper::TYPE_SYSTEM, $modrecord->type);
        // Create module other than a qbank with an ID that isn't used by a qbank yet.
        do {
            $wiki = self::getDataGenerator()->create_module('wiki', [
                'course' => $course->id,
            ]);
        } while ($DB->record_exists('qbank', ['id' => $wiki->id]));
        // Swap the qbank instance record for one with the same ID as the wiki instance.
        $newqbank = clone($modrecord);
        $newqbank->id = $wiki->id;
        $DB->insert_record_raw('qbank', $newqbank, customsequence: true);
        $DB->delete_records('qbank', ['id' => $qbank->id]);
        $DB->set_field('course_modules', 'instance', $newqbank->id, ['instance' => $qbank->instance]);
        // Retry the above again.
        \course_modinfo::purge_course_caches([$course->id]);
        $qbank = question_bank_helper::get_default_open_instance_system_type($course);
        $this->assertEquals(get_string('systembank', 'question'), $qbank->get_name());
        $modrecord = $DB->get_record('qbank', ['id' => $qbank->instance]);
        $this->assertEquals(question_bank_helper::TYPE_SYSTEM, $modrecord->type);
    }

    /**
     * Assert that get_bank_name_string returns suitably truncated strings.
     *
     * @dataProvider bank_name_strings
     * @param string $identifier
     * @param string $component
     * @param mixed $params
     * @param string $expected
     */
    public function test_get_bank_name_string(string $identifier, string $component, mixed $params, string $expected): void {
        $this->assertEquals($expected, question_bank_helper::get_bank_name_string($identifier, $component, $params));
    }

    /**
     * Get string examples with different parameter types and lengths.
     *
     * @return array[]
     */
    public static function bank_name_strings(): array {
        $longname = 'One two three four five six seven eight nine ten eleven twelve thirteen fourteen fifteen sixteen seventeen ' .
            'eighteen nineteen twenty twenty-one twenty-two twenty-three twenty-four twenty-five twenty-six twenty-seven ' .
            'twenty-eight twenty-nine thirty thirty-one thirty-two thirty-three thirty-four thirty-five thirty-six thirty-seven ' .
            'thirty-eight thirty-nine forty forty-one forty-two forty-three forty-four forty-five forty-six forty-seven ' .
            'forty-eight forty-nine fifty fifty-one fifty-two fifty-three fifty-four fifty-five fifty-six fifty-seven ' .
            'fifty-eight fifty-nine sixty sixty-one sixty-two sixty-three sixty-four sixty-five sixty-six sixty-seven ' .
            'sixty-eight sixty-nine seventy seventy-one seventy-two seventy-three seventy-four seventy-five seventy-six ' .
            'seventy-seven seventy-eight seventy-nine eighty eighty-one eighty-two eighty-three eighty-four eighty-five ' .
            'eighty-six eighty-seven eighty-eight eighty-nine ninety ninety-one ninety-two ninety-three ninety-four ninety-five ' .
            'ninety-six ninety-seven ninety-eight ninety-nine one hundred one hundred one one hundred two one hundred three ' .
            'one hundred four one hundred five one hundred six one hundred seven one hundred eight one hundred nine ' .
            'one hundred ten one hundred eleven one hundred twelve one hundred thirteen one hundred fourteen one hundred fifteen ' .
            'one hundred sixteen one hundred seventeen';
        return [
            'String with no parameters' => [
                'systembank',
                'question',
                null,
                'System shared question bank',
            ],
            'String with short string parameter' => [
                'topfor',
                'question',
                'Test course',
                'Top for Test course',
            ],
            'String with long string parameter' => [
                'topfor',
                'question',
                $longname,
                'Top for One two three four five six seven eight nine ten eleven twelve thirteen fourteen fifteen sixteen ' .
                    'seventeen eighteen nineteen twenty twenty-one twenty-two twenty-three twenty-four twenty-five twenty-six ' .
                    'twenty-seven twenty-eight twenty-nine thirty thirty-one thirty-two thirty-three thirty-four thirty-five ' .
                    'thirty-six thirty-seven thirty-eight thirty-nine forty forty-one forty-two forty-three forty-four ' .
                    'forty-five forty-six forty-seven forty-eight forty-nine fifty fifty-one fifty-two fifty-three fifty-four ' .
                    'fifty-five fifty-six fifty-seven fifty-eight fifty-nine sixty sixty-one sixty-two sixty-three sixty-four ' .
                    'sixty-five sixty-six sixty-seven sixty-eight sixty-nine seventy seventy-one seventy-two seventy-three ' .
                    'seventy-four seventy-five seventy-six seventy-seven seventy-eight seventy-nine eighty eighty-one ' .
                    'eighty-two eighty-three eighty-four eighty-five eighty-six eighty-seven eighty-eight eighty-nine ' .
                    'ninety ninety-one ninety-two ninety-three ninety-four ninety-five ninety-six ninety-seven ninety-eight ' .
                    'ninety-nine one hundred one hundred one one hundred two one hundred three one hundred four ' .
                    'one hundred five one hundred six one hundred seven one hundred eight one hundred nine one hundred ten ' .
                    'one hundred eleven one hundred twelve one hundred thirteen one hundred fourteen one hundred fifteen one ...',
            ],
            'String with short array parameter' => [
                'defaultbank',
                'question',
                ['coursename' => 'Test course'],
                'Test course course question bank',
            ],
            'String with long array parameter' => [
                'defaultbank',
                'question',
                ['coursename' => $longname],
                'One two three four five six seven eight nine ten eleven twelve thirteen fourteen fifteen sixteen seventeen ' .
                    'eighteen nineteen twenty twenty-one twenty-two twenty-three twenty-four twenty-five twenty-six ' .
                    'twenty-seven twenty-eight twenty-nine thirty thirty-one thirty-two thirty-three thirty-four thirty-five ' .
                    'thirty-six thirty-seven thirty-eight thirty-nine forty forty-one forty-two forty-three forty-four ' .
                    'forty-five forty-six forty-seven forty-eight forty-nine fifty fifty-one fifty-two fifty-three fifty-four ' .
                    'fifty-five fifty-six fifty-seven fifty-eight fifty-nine sixty sixty-one sixty-two sixty-three sixty-four ' .
                    'sixty-five sixty-six sixty-seven sixty-eight sixty-nine seventy seventy-one seventy-two seventy-three ' .
                    'seventy-four seventy-five seventy-six seventy-seven seventy-eight seventy-nine eighty eighty-one ' .
                    'eighty-two eighty-three eighty-four eighty-five eighty-six eighty-seven eighty-eight eighty-nine ninety ' .
                    'ninety-one ninety-two ninety-three ninety-four ninety-five ninety-six ninety-seven ninety-eight ' .
                    'ninety-nine one hundred one hundred one one hundred two one hundred three one hundred four ' .
                    'one hundred five one hundred six one hundred seven one hundred eight one hundred nine one hundred ten ' .
                    'one hundred eleven one hundred twelve one hundred thirteen one hundred fourteen one hundred fifteen ... ' .
                    'course question bank',
            ],
            'String with multiple long array parameters' => [
                'markoutofmax',
                'question',
                ['mark' => $longname, 'max' => $longname],
                'Mark One two three four five six seven eight nine ten eleven twelve thirteen fourteen fifteen sixteen seventeen ' .
                    'eighteen nineteen twenty twenty-one twenty-two twenty-three twenty-four twenty-five twenty-six twenty-seven ' .
                    'twenty-eight twenty-nine thirty thirty-one thirty-two thirty-three thirty-four thirty-five thirty-six ' .
                    'thirty-seven thirty-eight thirty-nine forty forty-one forty-two forty-three forty-four forty-five forty-six ' .
                    'forty-seven forty-eight forty-nine fifty fifty-one fifty-two fifty-three fifty-four fifty-five fifty-six ' .
                    'fifty-seven fifty-eight fifty-nine sixty sixty-one sixty-two sixty-three sixty-four sixty-five sixty-six ' .
                    'sixty-seven ... out of One two three four five six seven eight nine ten eleven twelve thirteen fourteen ' .
                    'fifteen sixteen seventeen eighteen nineteen twenty twenty-one twenty-two twenty-three twenty-four ' .
                    'twenty-five twenty-six twenty-seven twenty-eight twenty-nine thirty thirty-one thirty-two thirty-three ' .
                    'thirty-four thirty-five thirty-six thirty-seven thirty-eight thirty-nine forty forty-one forty-two ' .
                    'forty-three forty-four forty-five forty-six forty-seven forty-eight forty-nine fifty fifty-one fifty-two ' .
                    'fifty-three fifty-four fifty-five fifty-six fifty-seven fifty-eight fifty-nine sixty sixty-one sixty-two ' .
                    'sixty-three sixty-four sixty-five sixty-six sixty-seven ...',
            ],
            'Long lang string' => [
                'howquestionsbehave_help',
                'question',
                null,
                "Students can interact with the questions in the quiz in various different ways. For example, you may wish the " .
                    "students to enter an answer to each question and then submit the entire quiz, before anything is graded or " .
                    "they get any feedback. That would be 'Deferred feedback' mode.\n\n" .
                    "Alternatively, you may wish for students to submit each question as they go along to get immediate " .
                    "feedback, and if they do not get it right immediately, have another try for fewer marks. That would be " .
                    "'Interactive with multiple tries' mode.\n" .
                    "\n" .
                    "Those are probably the two most commonly used modes of behaviour. ",
            ],
        ];
    }
}
