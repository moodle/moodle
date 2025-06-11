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
 * Unit tests for the relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_relativedate;

use availability_relativedate\condition;
use context_module;
use core\event\course_module_completion_updated;
use core_availability\{tree, mock_info, info_module, info_section};
use stdClass;

/**
 * Unit tests for the relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \availability_relativedate\condition
 */
final class condition_test extends \advanced_testcase {
    /** @var stdClass course. */
    private $course;

    /** @var stdClass user. */
    private $user;

    /**
     * Create course and page.
     */
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info_module.php');
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info_section.php');
        require_once($CFG->libdir . '/completionlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        set_config('enableavailability', true);
        $dg = $this->getDataGenerator();
        $now = time();
        $this->course = $dg->create_course(['startdate' => $now, 'enddate' => $now + 7 * WEEKSECS, 'enablecompletion' => 1]);
        $this->user = $dg->create_user(['timezone' => 'UTC']);
        $dg->enrol_user($this->user->id, $this->course->id, 5, time());
    }

    /**
     * Relative dates tree provider.
     */
    public static function tree_provider(): array {
        return [
            'After start course' => [2, 1, 1, "+2 hour", "From", false, true],
            'Before end course' => [3, 2, 2, '-3 day', 'Until', false, true],
            'After end enrol' => [4, 3, 3, '+4 week', 'From', false, true],
            'After end method' => [4, 3, 4, '+4 week', 'From', false, true],
            'After end course' => [3, 2, 5, '+3 day', 'From', false, true],
            'Before start course' => [2, 1, 6, '-2 hour', 'Until', true, false],
        ];
    }

    /**
     * Test tree.
     *
     * @dataProvider tree_provider
     * @param int $n number to skip
     * @param int $d Minute - hour - day - week  - month
     * @param int $s relative to
     * @param string $str
     * @param string $result
     * @param bool $availablefalse
     * @param bool $availabletrue
     * @covers \availability_relativedate\condition
     */
    public function test_tree($n, $d, $s, $str, $result, $availablefalse, $availabletrue): void {
        $arr = (object)['type' => 'relativedate', 'n' => $n, 'd' => $d, 's' => $s, 'm' => 9999999];
        $stru = (object)['op' => '|', 'show' => true, 'c' => [$arr]];
        $tree = new tree($stru);
        $this->assertFalse($tree->is_available_for_all());
        $this->setUser($this->user);
        $info = new mock_info($this->course, $this->user->id);
        $strf = get_string('strftimedatetime', 'langconfig');
        $nau = 'Not available unless:';
        $calc = userdate(strtotime($str, $this->get_reldate($s)), $strf, 0);
        $this->assertEquals("$nau $result $calc", $tree->get_full_information($info));
        $this->assertEquals($availablefalse, $tree->check_available(false, $info, false, $this->user->id)->is_available());
        $this->assertEquals($availabletrue, $tree->check_available(true, $info, false, $this->user->id)->is_available());
    }

    /**
     * Tests relative module.
     * @covers \availability_relativedate\condition
     */
    public function test_relative_module(): void {
        $this->setTimezone('UTC');
        $dg = $this->getDataGenerator();
        $page = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $this->course]);
        $stru = (object)['op' => '|', 'show' => true,
            'c' => [(object)['type' => 'relativedate', 'n' => 7, 'd' => 0, 's' => 7, 'm' => $page->cmid]],
        ];
        $tree = new tree($stru);
        $this->setUser($this->user);
        $info = new mock_info($this->course, $this->user->id);
        [$sql, $params] = $tree->get_user_list_sql(false, $info, false);
        $this->assertEquals('', $sql);
        $this->assertEquals([], $params);
        // 7 Minutes after completion of module.
        $this->assertStringContainsString('7 minutes after completion of activity', $tree->get_full_information($info));
        $this->do_cron();
        $this->assertFalse($tree->is_available_for_all());
    }

    /**
     * Relative dates description provider.
     */
    public static function description_provider(): array {
        return [
            'After start course' => [2, 1, 1, '+2 hour', 'From', 'Until', '2 hours after course start date'],
            'Before end course' => [3, 2, 2, '-3 day', 'Until', 'From', '3 days before course end date'],
            'After end enrol' => [4, 3, 3, '+4 week', 'From', 'Until', '4 weeks after user enrolment date'],
            'After end method' => [4, 3, 4, '+4 week', 'From', 'Until', '4 weeks after enrolment method end date'],
            'After end course' => [3, 2, 5, '+3 day', 'From', 'Until', '3 days after course end date'],
            'Before start course' => [2, 1, 6, '-2 hour', 'Until', 'From', '2 hours before course start date'],
        ];
    }

    /**
     * Test description.
     *
     * @dataProvider description_provider
     * @param int $n number to skip
     * @param int $d Minute - hour - day - week  - month
     * @param int $s relative to
     * @param string $str
     * @param string $result1
     * @param string $result2
     * @param string $result3
     * @covers \availability_relativedate\condition
     */
    public function test_description($n, $d, $s, $str, $result1, $result2, $result3): void {
        $strf = get_string('strftimedatetime', 'langconfig');
        $nau = 'Not available unless:';
        $info = new mock_info($this->course, $this->user->id);
        $this->setUser($this->user);
        $cond = new condition((object)['type' => 'relativedate', 'n' => $n, 'd' => $d, 's' => $s, 'm' => 99999]);
        $calc = userdate(strtotime($str, $this->get_reldate($s)), $strf);
        $this->assertEquals("$result1 $calc", $cond->get_description(true, false, $info));
        $this->assertEquals("$result2 $calc", $cond->get_description(true, true, $info));
        $this->assertEquals("$result1 $calc", $cond->get_description(false, false, $info));
        $this->assertEquals("$result2 $calc", $cond->get_description(false, true, $info));
        $this->assertEquals("$nau $result1 $calc", $cond->get_standalone_description(false, false, $info));
        $this->assertEquals("$nau $result2 $calc", $cond->get_standalone_description(false, true, $info));

        $this->setAdminUser();
        $this->assertStringContainsString($result3, $cond->get_description(true, false, $info));
        $this->assertNotEquals("$nau $result1 $calc", $cond->get_standalone_description(false, false, $info));
        $this->assertNotEquals("$nau $result2 $calc", $cond->get_standalone_description(false, true, $info));
        $this->assertNotEquals("$nau $result1 $calc", $cond->get_standalone_description(true, false, $info));
        $this->assertNotEquals("$nau $result2 $calc", $cond->get_standalone_description(true, true, $info));
    }

    /**
     * Tests the get_description and get_standalone_description functions.
     * @covers \availability_relativedate\condition
     */
    public function test_get_description(): void {
        global $DB;
        $this->get_reldate(4);
        $info = new mock_info($this->course, $this->user->id);
        $this->setUser($this->user);

        $pg = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page0 = $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page1 = $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $pg->create_instance(['course' => $this->course]);

        $str = '{"op":"|","show":true,"c":[{"type":"relativedate","n":4,"d":4,"s":7,"m":' . $page1->cmid . '}]}';
        $DB->set_field('course_modules', 'availability', $str, ['id' => $page0->cmid]);
        rebuild_course_cache($this->course->id, true);
        $cond = new condition((object)['type' => 'relativedate', 'n' => 4, 'd' => 4, 's' => 7, 'm' => $page1->cmid]);
        $str = "(4 months after completion of activity <AVAILABILITY_CMNAME_$page1->cmid/>)";
        $this->assertEquals($str, $cond->get_description(true, false, $info));
        $this->assertEquals($str, $cond->get_description(true, true, $info));
        $this->assertFalse($cond->completion_value_used($this->course, $page0->cmid));
        $this->assertTrue($cond->completion_value_used($this->course, $page1->cmid));

        $modinfo = get_fast_modinfo($this->course);
        $str = '{"op":"|","show":true,"c":[{"type":"relativedate","n":4,"d":4,"s":7,"m":' . $page0->cmid . '}]}';
        foreach ($modinfo->get_section_info_all() as $section) {
            $DB->set_field('course_sections', 'availability', $str, ['id' => $section->id]);
        }
        $this->do_cron();
        $cond = new condition((object)['type' => 'relativedate', 'n' => 4, 'd' => 4, 's' => 7, 'm' => $page1->cmid]);
        $this->assertTrue($cond->completion_value_used($this->course, $page0->cmid));
        $this->assertTrue($cond->completion_value_used($this->course, $page1->cmid));
        $completion = new \completion_info($this->course);
        $completion->reset_all_state($modinfo->get_cm($page1->cmid));

        $cond = new condition((object)['type' => 'relativedate', 'n' => 4, 'd' => 4, 's' => 7, 'm' => $page0->cmid]);
        $this->assertTrue($cond->update_dependency_id('course_sections', $page0->cmid, 3));
        $this->assertFalse($cond->update_dependency_id('course_sections', $page0->cmid, $page0->cmid));
        $this->assertFalse($cond->update_dependency_id('course_modules', $page0->cmid, $page0->cmid));
        $this->assertFalse($cond->update_dependency_id('course_modules', $page0->cmid, $page1->cmid));
        $this->assertFalse($cond->update_dependency_id('course_modules', $page0->cmid, 3));
        $this->assertFalse($cond->update_dependency_id('course_modules', $page1->cmid, 3));
        $this->assertFalse($cond->update_dependency_id('', $page1->cmid, 3));
        $this->assertFalse($cond->update_dependency_id('', $page1->cmid, $page0->cmid));
        $this->assertFalse($cond->update_dependency_id('course_modules', $page1->cmid, $page1->cmid));
        $cond = new condition((object)['type' => 'relativedate', 'n' => 4, 'd' => 4, 's' => 7, 'm' => $page1->cmid]);
        $this->assertTrue($cond->update_dependency_id('course_modules', $page1->cmid, 4));
        $this->assertFalse($cond->update_dependency_id('course_modules', $page1->cmid, -1));
    }

    /**
     * Tests a course with no enddate.
     * @covers \availability_relativedate\condition
     */
    public function test_no_enddate(): void {
        global $DB, $USER;
        $dg = $this->getDataGenerator();
        $now = time();
        $course1 = $dg->create_course(['enablecompletion' => 1]);
        $course2 = $dg->create_course(['enddate' => $now + 14 * WEEKSECS, 'enablecompletion' => 1]);
        $user = $dg->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $dg->enrol_user($user->id, $course1->id, $roleid);
        $dg->enrol_user($user->id, $course2->id, $roleid);
        $pg = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page1 = $pg->create_instance(['course' => $course1, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page2 = $pg->create_instance(['course' => $course2, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $pg->create_instance(['course' => $course1]);
        $pg->create_instance(['course' => $course2]);
        $modinfo1 = get_fast_modinfo($course1);
        $modinfo2 = get_fast_modinfo($course2);
        $info = new info_section($modinfo2->get_section_info_all()[1]);
        $cond = new condition((object)['type' => 'relativedate', 'n' => 6, 'd' => 2, 's' => 2, 'm' => 1]);
        $information = $cond->get_description(false, false, $info);
        $strf = get_string('strftimedatetime', 'langconfig');
        $str = userdate($course2->enddate - (6 * 24 * 3600), $strf);
        $this->assertEquals("Until $str", $information);

        $cm1 = $modinfo1->get_cm($page1->cmid);
        $cm2 = $modinfo2->get_cm($page2->cmid);
        $info = new info_module($cm1);
        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 2, 'm' => 1]);
        $information = $cond->get_description(true, false, $info);
        $this->assertEquals('This course has no end date', $information);
        $this->assertEquals('{relativedate: 7 days before course end date}', "$cond");
        // No enddate => Never available.
        $this->assertFalse($cond->is_available(false, $info, false, $user->id));
        $this->assertFalse($cond->is_available(true, $info, false, $user->id));
        $info = new info_module($cm2);
        $information = $cond->get_description(true, false, $info);
        $this->assertStringNotContainsString('(No course enddate)', $information);
        $str = userdate($course2->enddate - (7 * 24 * 3600), $strf);
        $this->assertEquals("Until $str (7 days before course end date)", $information);
        $this->assertEquals('{relativedate: 7 days before course end date}', "$cond");
        $this->assertFalse($cond->is_available(false, $info, false, $user->id));
        $this->assertTrue($cond->is_available(true, $info, false, $user->id));
        $this->assertFalse($cond->is_available(false, $info, false, null));
        $this->assertTrue($cond->is_available(true, $info, false, null));

        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 3, 'm' => 1]);
        $information = $cond->get_description(true, false, $info);
        $this->assertEquals('(7 days after user enrolment date)', $information);
        $this->assertFalse($cond->is_available(false, $info, false, $USER->id));
        $this->assertFalse($cond->is_available(true, $info, false, $USER->id));

        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 4, 'm' => 1]);
        $information = $cond->get_description(false, false, $info);
        $this->assertEquals('(7 days after enrolment method end date)', $information);

        $info = new info_module($cm1);
        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 5, 'm' => 1]);
        $information = $cond->get_description(false, false, $info);
        $this->assertEquals('This course has no end date', $information);

        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 6, 'm' => 1]);
        $information = $cond->get_description(false, false, $info);
        $str = userdate($course2->startdate - (7 * 24 * 3600), $strf);
        $this->assertEquals("Until $str", $information);
        $this->assertEquals('{relativedate: 7 days before course start date}', "$cond");

        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 6, 'm' => 9999999]);
        $information = $cond->get_description(false, false, $info);
        $this->assertEquals("Until $str", $information);
        $this->assertEquals('{relativedate: 7 days before course start date}', "$cond");

        $cond = new condition((object)['type' => 'relativedate', 'n' => 7, 'd' => 2, 's' => 6, 'm' => -1]);
        $information = $cond->get_description(false, false, $info);
        $this->assertEquals("Until $str", $information);
        $this->assertEquals('{relativedate: 7 days before course start date}', "$cond");

        $cond = new condition((object)['type' => 'relativedate', 'n' => '7', 'd' => '2', 's' => '6', 'm' => '1']);
        $information = $cond->get_description(false, false, $info);
        $this->assertEquals("Until $str", $information);
        $this->assertEquals('{relativedate: 7 days before course start date}', "$cond");

        $cond = new condition((object)['type' => 'relativedate', 'n' => 'null', 'd' => 'null', 's' => 'null', 'm' => 'null']);
        $information = $cond->get_description(false, false, $info);
        $this->assertNotEquals("Until $str", $information);
        $this->assertEquals('{relativedate: 0 minutes }', "$cond");
    }

    /**
     * Tests debug strings (reflection).
     * @covers \availability_relativedate\condition
     */
    public function test_reflection_debug_strings(): void {
        $name = 'availability_relativedate\condition';
        $daybefore = ' 1 ' . get_string('day', 'availability_relativedate');
        $pg = self::getDataGenerator()->get_plugin_generator('mod_page');
        $page0 = $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);

        $condition = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 7, 'm' => $page0->cmid]);
        $result = \phpunit_util::call_internal_method($condition, 'get_debug_string', [], $name);
        $this->assertEquals(
            $daybefore . ' ' .
            get_string('datecompletion', 'availability_relativedate')
            . ' ' . condition::description_cm_name($page0->cmid),
            $result
        );

        $condition = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 7, 'm' => 999999]);
        $result = \phpunit_util::call_internal_method($condition, 'get_debug_string', [], $name);
        $this->assertStringContainsString(get_string('missing', 'availability_relativedate'), $result);
        $this->assertStringContainsString('alert', $result);
    }

    /**
     * Tests a reflection.
     * @covers \availability_relativedate\condition
     */
    public function test_reflection_calc(): void {
        global $DB;
        $name = 'availability_relativedate\condition';
        $pg = self::getDataGenerator()->get_plugin_generator('mod_page');
        $page0 = $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $context = context_module::instance($page0->cmid);
        $activitycompletion = $this->create_course_module_completion($page0->cmid);
        $arr = [
            'objectid' => $activitycompletion->id,
            'relateduserid' => $this->user->id,
            'context' => $context,
        ];
        course_module_completion_updated::create($arr)->trigger();

        $condition1 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 1, 'm' => 999999]);
        $result1 = \phpunit_util::call_internal_method($condition1, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($this->course->startdate + DAYSECS, $result1);

        $condition2 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 2, 'm' => 999999]);
        $result2 = \phpunit_util::call_internal_method($condition2, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($this->course->enddate - DAYSECS, $result2);

        self::getDataGenerator()->enrol_user($this->user->id, $this->course->id);
        $enrol1 = $DB->get_record('user_enrolments', ['userid' => $this->user->id]);
        $condition31 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 3, 'm' => 999999]);
        $result31 = \phpunit_util::call_internal_method($condition31, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($enrol1->timecreated + DAYSECS, $result31);

        $user2 = self::getDataGenerator()->create_user(['timezone' => 'UTC']);
        self::getDataGenerator()->enrol_user(
            $user2->id,
            $this->course->id,
            null,
            'manual',
            (int)$this->course->startdate + HOURSECS,
            (int)$this->course->startdate + HOURSECS * 24
        );
        $enrol2 = $DB->get_record('user_enrolments', ['userid' => $user2->id]);
        $condition32 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 3, 'm' => 999999]);
        $result32 = \phpunit_util::call_internal_method($condition32, 'calc', [$this->course, $user2->id], $name);
        $this->assertEquals((int)$enrol2->timestart + DAYSECS, $result32);
        $this->assertEquals((int)$this->course->startdate + HOURSECS * 25, $result32);

        $courseself = $DB->get_record('enrol', ['courseid' => $this->course->id, 'enrol' => 'manual']);
        $courseself->enrolenddate = $this->course->enddate - 12 * HOURSECS;
        $DB->update_record('enrol', $courseself);
        $condition4 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 4, 'm' => 999999]);
        $result4 = \phpunit_util::call_internal_method($condition4, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($courseself->enrolenddate + DAYSECS, $result4);

        $condition5 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 5, 'm' => 999999]);
        $result5 = \phpunit_util::call_internal_method($condition5, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($this->course->enddate + DAYSECS, $result5);

        $condition6 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 6, 'm' => 999999]);
        $result6 = \phpunit_util::call_internal_method($condition6, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($this->course->startdate - DAYSECS, $result6);

        $condition71 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 7, 'm' => 0]);
        $result71 = \phpunit_util::call_internal_method($condition71, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals(0, $result71);

        $condition72 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 7, 'm' => $page0->cmid]);
        $result72 = \phpunit_util::call_internal_method($condition72, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals($activitycompletion->timemodified + DAYSECS, $result72);

        $condition73 = new condition((object)['type' => 'relativedate', 'n' => 1, 'd' => 2, 's' => 7, 'm' => 999999]);
        $result73 = \phpunit_util::call_internal_method($condition73, 'calc', [$this->course, $this->user->id], $name);
        $this->assertEquals(0, $result73);
    }

    /**
     * Create course module completion.
     *
     * @param int $cmid course module id
     * @return stdClass
     */
    private function create_course_module_completion(int $cmid): stdClass {
        global $DB;
        $activitycompletion = new stdClass();
        $activitycompletion->coursemoduleid = $cmid;
        $activitycompletion->userid = $this->user->id;
        $activitycompletion->viewed = null;
        $activitycompletion->overrideby = null;
        $activitycompletion->completionstate = 1;
        $activitycompletion->timemodified = time();
        $activitycompletion->id = $DB->insert_record('course_modules_completion', $activitycompletion);
        return $activitycompletion;
    }

    /**
     * Tests the autoupdate event.
     * @covers \availability_relativedate\autoupdate
     */
    public function test_autoupdate(): void {
        global $DB;
        $pg = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $page0 = $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $page1 = $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);
        $str = '{"op":"|","show":true,"c":[{"type":"relativedate","n":4,"d":4,"s":7,"m":' . $page0->cmid . '}]}';
        $DB->set_field('course_modules', 'availability', $str, ['id' => $page1->cmid]);
        $this->do_cron();
        $first = $DB->get_field('course', 'cacherev', ['id' => $this->course->id]);
        $this->assertGreaterThan(0, $first);

        $event = \core\event\course_module_deleted::create([
            'objectid' => $page0->cmid,
            'relateduserid' => 1,
            'context' => \context_course::instance($this->course->id),
            'courseid' => $this->course->id,
            'other' => [
                'relateduserid' => 1,
                'modulename' => 'page',
                'instanceid' => $page0->cmid,
                'name' => $page0->name,
            ],
        ]);
        $event->trigger();

        $actual = $DB->get_record('course_modules', ['id' => $page1->cmid]);
        self::assertEquals(
            '{"op":"|","show":true,"c":[{"type":"relativedate","n":4,"d":4,"s":7,"m":-1}]}',
            $actual->availability
        );
        $last = $DB->get_field('course', 'cacherev', ['id' => $this->course->id]);
        $this->assertGreaterThan($first, $last);
    }

    /**
     * Cron function.
     * @coversNothing
     */
    private function do_cron(): void {
        $task = new \core\task\completion_regular_task();
        ob_start();
        $task->execute();
        sleep(1);
        $task->execute();
        \phpunit_util::run_all_adhoc_tasks();
        ob_end_clean();
        get_fast_modinfo(0, 0, true);
        rebuild_course_cache($this->course->id);
    }

    /**
     * Which date.
     * @coversNothing
     *
     * @param int $s
     * @return int
     */
    private function get_reldate($s): int {
        global $DB;
        switch ($s) {
            case 1:
            case 6:
                return $this->course->startdate;
            case 2:
            case 5:
                return $this->course->enddate;
            case 3:
            case 4:
                $now = time();
                $selfplugin = enrol_get_plugin('self');
                $instance = $DB->get_record('enrol', ['courseid' => $this->course->id, 'enrol' => 'self'], '*', MUST_EXIST);
                $DB->set_field('enrol', 'enrolenddate', $now + 1000, ['id' => $instance->id]);
                $instance = $DB->get_record('enrol', ['courseid' => $this->course->id, 'enrol' => 'self'], '*', MUST_EXIST);
                $selfplugin->enrol_user($instance, $this->user->id, 5, $now);
                return ($s === 3) ? $now : $now + 1000;
        }
        return 0;
    }
}
