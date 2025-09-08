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

namespace mod_forum\courseformat;

use core_courseformat\local\overview\overviewfactory;
use mod_forum\subscriptions;

/**
 * Tests for Forum overview integration.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;

        parent::setUpBeforeClass();
        require_once($CFG->dirroot . '/mod/forum/lib.php');
    }

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        // We must clear the subscription caches.
        // This has to be done both before each test, and after in case of other tests using these functions.
        subscriptions::reset_forum_cache();
    }

    #[\Override]
    public function tearDown(): void {
        // We must clear the subscription caches.
        // This has to be done both before each test, and after in case of other tests using these functions.
        subscriptions::reset_forum_cache();
        parent::tearDown();
    }

    /**
     * Test get_actions_overview method.
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $teacher = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $activity = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Check the forum has no discussions/posts yet.
        $this->setUser($teacher);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('posts'), $item->get_name());
        $this->assertEquals(0, $item->get_value());
        $this->assertEquals(0, $item->get_alert_count());
        $this->assertEquals(get_string('unreadposts', 'forum'), $item->get_alert_label());

        // Post discussions/posts (1 as teacher, 2 as student).
        $forumgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $post1 = $forumgenerator->create_content($activity);
        $this->setUser($student);
        $post2 = $forumgenerator->create_content($activity);
        $post3 = $forumgenerator->create_content($activity);

        // Check for teacher.
        $this->setUser($teacher);
        // Reset static cache for further tests.
        forum_tp_count_forum_unread_posts($cm, $course, true);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('posts'), $item->get_name());
        $this->assertEquals(3, $item->get_value());
        $this->assertEquals(2, $item->get_alert_count());
        $this->assertEquals(get_string('unreadposts', 'forum'), $item->get_alert_label());

        // Check for student.
        $this->setUser($student);
        // Reset static cache for further tests.
        forum_tp_count_forum_unread_posts($cm, $course, true);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('posts'), $item->get_name());
        $this->assertEquals(3, $item->get_value());
        $this->assertEquals(1, $item->get_alert_count());
        $this->assertEquals(get_string('unreadposts', 'forum'), $item->get_alert_label());
    }

    /**
     * Test get_due_date_overview method.
     *
     * @param int|null $timeincrement null if no due date, or due date increment.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_due_date_overview_provider')]
    public function test_get_due_date_overview(
        int|null $timeincrement,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();
        if ($timeincrement === null) {
            $expectedtime = null;
        } else {
            $expectedtime = $this->mock_clock_with_frozen()->time() + $timeincrement;
        }

        $activity = $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'duedate' => !empty($expectedtime) ? $expectedtime : 0,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $this->setUser($teacher);
        $item = overviewfactory::create($cm)->get_due_date_overview();
        $this->assertNull($item);

        $this->setUser($student);
        $item = overviewfactory::create($cm)->get_due_date_overview();
        $this->assertEquals(get_string('duedate', 'forum'), $item->get_name());
        $this->assertEquals($expectedtime, $item->get_value());
    }

    /**
     * Provider for get_due_date_overview.
     *
     * @return \Generator
     */
    public static function get_due_date_overview_provider(): \Generator {
        yield 'no_due' => [
            'timeincrement' => null,
        ];
        yield 'past_due' => [
            'timeincrement' => -1 * (4 * DAYSECS),
        ];
        yield 'future_due' => [
            'timeincrement' => (4 * DAYSECS),
        ];
    }

    /**
     * Test get_extra_forumtype_overview method.
     *
     * @param string $forumtype Forum type to test.
     * @param string $expected Expected string for the forum type.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_forumtype_overview_provider')]
    public function test_get_extra_forumtype_overview(
        string $forumtype,
        string $expected,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $teacher = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $activity = $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'type' => $forumtype,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Check student can't see the forum type.
        $this->setUser($student);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_forumtype_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Check teacher sees the forum type.
        $this->setUser($teacher);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_forumtype_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('forumtype', 'forum'), $item->get_name());
        $this->assertEquals($forumtype, $item->get_value());
        $this->assertEquals($expected, $item->get_content());
    }

    /**
     * Provider for get_extra_forumtype_overview.
     *
     * @return \Generator
     */
    public static function get_extra_forumtype_overview_provider(): \Generator {
        yield 'General' => [
            'forumtype' => 'general',
            'expected' => get_string('generalforum', 'forum'),
        ];
        yield 'Single discussion' => [
            'forumtype' => 'single',
            'expected' => get_string('singleforum', 'forum'),
        ];
        yield 'Each user' => [
            'forumtype' => 'eachuser',
            'expected' => get_string('eachuserforum', 'forum'),
        ];
        yield 'Question&Answer' => [
            'forumtype' => 'qanda',
            'expected' => get_string('qandaforum', 'forum'),
        ];
        yield 'Blog' => [
            'forumtype' => 'blog',
            'expected' => get_string('blogforum', 'forum'),
        ];
        yield 'News' => [
            'forumtype' => 'news',
            'expected' => get_string('namenews', 'forum'),
        ];
    }

    /**
     * Test get_extra_track_overview method.
     *
     * @param string $role User role to test, 'student' or 'teacher'.
     * @param int $forumtype Forum tracking type to test.
     * @param int $tracked Expected tracked state of the forum.
     * @param bool $disabled Whether the toggle should be disabled.
     * @param bool $allowforced Whether the $CFG->forum_allowforcedreadtracking setting is enabled.
     * @param int $trackforums Whether the user has the trackforums setting enabled.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_track_overview_provider')]
    public function test_get_extra_track_overview(
        string $role = 'student',
        int $forumtype = FORUM_TRACKING_OPTIONAL,
        int $tracked = 1,
        bool $disabled = false,
        bool $allowforced = true,
        int $trackforums = 1,
    ): void {
        global $CFG;

        $this->resetAfterTest();

        // Allow force.
        $CFG->forum_allowforcedreadtracking = $allowforced;

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user(['trackforums' => $trackforums]);
        $teacher = $this->getDataGenerator()->create_user(['trackforums' => $trackforums]);
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $activity = $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'trackingtype' => $forumtype,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $this->setUser(${$role});
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_track_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('tracking', 'forum'), $item->get_name());
        $this->assertStringContainsString('data-type="forum-track-toggle"', $item->get_content());
        $this->assertStringContainsString('data-action="toggle"', $item->get_content());
        $this->assertStringContainsString('data-forumid="' . $activity->id . '"', $item->get_content());
        $this->assertStringContainsString('data-targetstate="' . ((int)!$tracked) . '"', $item->get_content());
        if ($disabled) {
            $this->assertStringContainsString('disabled', $item->get_content());
        } else {
            $this->assertStringNotContainsString('disabled', $item->get_content());
        }
    }

    /**
     * Data provider for test_get_extra_track_overview.
     *
     * @return \Generator
     */
    public static function get_extra_track_overview_provider(): \Generator {
        // Student role tests.
        yield 'Student. Tracking optional' => [
            'forumtype' => FORUM_TRACKING_OPTIONAL,
            'tracked' => 1,
            'disabled' => false,
        ];
        yield 'Student. Tracking forced off' => [
            'forumtype' => FORUM_TRACKING_OFF,
            'tracked' => 0,
            'disabled' => true,
        ];
        yield 'Student. Tracking forced on' => [
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 1,
            'disabled' => true,
        ];
        yield 'Student. Tracking forced on, with $CFG->forum_allowforcedreadtracking disabled' => [
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 1,
            'disabled' => false,
            'allowforced' => false,
        ];
        yield 'Student. $USER->trackforums disabled. Tracking optional. ' => [
            'forumtype' => FORUM_TRACKING_OPTIONAL,
            'tracked' => 0,
            'disabled' => true,
            'trackforums' => 0,
        ];
        yield 'Student. $USER->trackforums disabled. Tracking forced off' => [
            'forumtype' => FORUM_TRACKING_OFF,
            'tracked' => 0,
            'disabled' => true,
            'trackforums' => 0,
        ];
        yield 'Student. $USER->trackforums disabled. Tracking forced on' => [
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 1,
            'disabled' => true,
            'trackforums' => 0,
        ];
        yield 'Student. $USER->trackforums disabled. Tracking forced on, with $CFG->forum_allowforcedreadtracking disabled' => [
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 0,
            'disabled' => true,
            'allowforced' => false,
            'trackforums' => 0,
        ];
        // Teacher role tests.
        yield 'Teacher. Tracking optional' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_OPTIONAL,
            'tracked' => 1,
            'disabled' => false,
        ];
        yield 'Teacher. Tracking forced off' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_OFF,
            'tracked' => 0,
            'disabled' => true,
        ];
        yield 'Teacher. Tracking forced on' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 1,
            'disabled' => true,
        ];
        yield 'Teacher. Tracking forced on, with $CFG->forum_allowforcedreadtracking disabled' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 1,
            'disabled' => false,
            'allowforced' => false,
        ];
        yield 'Teacher. $USER->trackforums disabled. Tracking optional' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_OPTIONAL,
            'tracked' => 0,
            'disabled' => true,
            'trackforums' => 0,
        ];
        yield 'Teacher. $USER->trackforums disabled. Tracking forced off' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_OFF,
            'tracked' => 0,
            'disabled' => true,
            'trackforums' => 0,
        ];
        yield 'Teacher. $USER->trackforums disabled. Tracking forced on' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 1,
            'disabled' => true,
            'trackforums' => 0,
        ];
        yield 'Teacher. $USER->trackforums disabled. Tracking forced on, with $CFG->forum_allowforcedreadtracking disabled' => [
            'role' => 'teacher',
            'forumtype' => FORUM_TRACKING_FORCED,
            'tracked' => 0,
            'disabled' => true,
            'allowforced' => false,
            'trackforums' => 0,
        ];
    }

    /**
     * Test get_extra_subscribed_overview method.
     *
     * @param string $role User role to test, 'student' or 'teacher'.
     * @param int $forumtype Forum subscribe type to test.
     * @param int $subscribed Expected subscribed state of the forum.
     * @param bool $disabled Whether the toggle should be disabled.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_subscribed_overview_provider')]
    public function test_get_extra_subscribed_overview(
        string $role = 'student',
        int $forumtype = FORUM_CHOOSESUBSCRIBE,
        int $subscribed = 1,
        bool $disabled = false,
    ): void {

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $activity = $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'forcesubscribe' => $forumtype,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $this->setUser(${$role});
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_subscribed_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('subscribed', 'forum'), $item->get_name());
        $this->assertStringContainsString('data-type="forum-subscription-toggle"', $item->get_content());
        $this->assertStringContainsString('data-action="toggle"', $item->get_content());
        $this->assertStringContainsString('data-forumid="' . $activity->id . '"', $item->get_content());
        $this->assertStringContainsString('data-targetstate="' . ((int)!$subscribed) . '"', $item->get_content());
        if ($disabled) {
            $this->assertStringContainsString('disabled', $item->get_content());
        } else {
            $this->assertStringNotContainsString('disabled', $item->get_content());
        }
    }

    /**
     * Data provider for get_extra_subscribed_overview.
     *
     * @return \Generator
     */
    public static function get_extra_subscribed_overview_provider(): \Generator {
        // Student role tests.
        yield 'Student. Tracking forced on' => [
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'subscribed' => 1,
            'disabled' => true,
        ];
        yield 'Student. Tracking forced off' => [
            'forumtype' => FORUM_DISALLOWSUBSCRIBE,
            'subscribed' => 0,
            'disabled' => true,
        ];
        yield 'Student. Tracking choose' => [
            'forumtype' => FORUM_CHOOSESUBSCRIBE,
            'subscribed' => 0,
            'disabled' => false,
        ];
        yield 'Student. Tracking initial on' => [
            'forumtype' => FORUM_INITIALSUBSCRIBE,
            'subscribed' => 1,
            'disabled' => false,
        ];
        yield // Teacher role tests.
        'Teacher. Tracking forced on' => [
            'role' => 'teacher',
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'subscribed' => 1,
            'disabled' => true,
        ];
        yield 'Teacher. Tracking forced off' => [
            'role' => 'teacher',
            'forumtype' => FORUM_DISALLOWSUBSCRIBE,
            'subscribed' => 0,
            'disabled' => false,
        ];
        yield 'Teacher. Tracking choose' => [
            'role' => 'teacher',
            'forumtype' => FORUM_CHOOSESUBSCRIBE,
            'subscribed' => 0,
            'disabled' => false,
        ];
        yield 'Teacher. Tracking initial on' => [
            'role' => 'teacher',
            'forumtype' => FORUM_INITIALSUBSCRIBE,
            'subscribed' => 1,
            'disabled' => false,
        ];
    }

    /**
     * Test get_extra_emaildigest_overview method.
     *
     * @param string $role User role to test, 'student' or 'teacher'.
     * @param int $forumtype Forum subscribe type to test.
     * @param string $emaildigestvalue Expected email digest value for the forum.
     * @param int $usermaildigest User's email digest setting.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_emaildigest_overview_provider')]
    public function test_get_extra_emaildigest_overview(
        string $role = 'student',
        int $forumtype = FORUM_CHOOSESUBSCRIBE,
        string $emaildigestvalue = '-',
        int $usermaildigest = 0,
    ): void {
        global $USER;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $activity = $this->getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'forcesubscribe' => $forumtype,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $this->setUser(${$role});

        $USER->maildigest = $usermaildigest;

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_emaildigest_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('digesttype', 'forum'), $item->get_name());
        if ($emaildigestvalue === '-') {
            $this->assertStringNotContainsString('data-inplaceeditable="1"', $item->get_content());
        } else {
            $this->assertStringContainsString('data-inplaceeditable="1"', $item->get_content());
            switch ($usermaildigest) {
                case 0:
                    $default = get_string('emaildigestoffshort', 'forum');
                    break;
                case 1:
                    $default = get_string('emaildigestcompleteshort', 'forum');
                    break;
                case 2:
                    $default = get_string('emaildigestsubjectsshort', 'forum');
                    break;
            }
            $emaildigestvalue = get_string(
                'emaildigestdefault',
                'forum',
                $default,
            );
        }
        $this->assertStringContainsString($emaildigestvalue, $item->get_content());
    }

    /**
     * Data provider for get_extra_emaildigest_overview.
     *
     * @return \Generator
     */
    public static function get_extra_emaildigest_overview_provider(): \Generator {
        // Student role tests.
        yield 'Student. Tracking forced on. No digest' => [
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Student. Tracking forced off' => [
            'forumtype' => FORUM_DISALLOWSUBSCRIBE,
            'emaildigestvalue' => '-',
        ];
        yield 'Student. Tracking choose' => [
            'forumtype' => FORUM_CHOOSESUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Student. Tracking initial on' => [
            'forumtype' => FORUM_INITIALSUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Student. Complete' => [
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'emaildigestvalue' => 'default',
            'usermaildigest' => 1,
        ];
        yield 'Student. Subjects' => [
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'emaildigestvalue' => 'default',
            'usermaildigest' => 2,
        ];
        // Teacher role tests.
        yield 'Teacher. Tracking forced on. No digest' => [
            'role' => 'teacher',
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Teacher. Tracking forced off' => [
            'role' => 'teacher',
            'forumtype' => FORUM_DISALLOWSUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Teacher. Tracking choose' => [
            'role' => 'teacher',
            'forumtype' => FORUM_CHOOSESUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Teacher. Tracking initial on' => [
            'role' => 'teacher',
            'forumtype' => FORUM_INITIALSUBSCRIBE,
            'emaildigestvalue' => 'default',
        ];
        yield 'Teacher. Complete' => [
            'role' => 'teacher',
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'emaildigestvalue' => 'default',
            'usermaildigest' => 1,
        ];
        yield 'Teacher. Subjects' => [
            'role' => 'teacher',
            'forumtype' => FORUM_FORCESUBSCRIBE,
            'emaildigestvalue' => 'default',
            'usermaildigest' => 2,
        ];
    }

    /**
     * Test get_extra_discussions_overview method.
     */
    public function test_get_extra_discussions_overview(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $teacher = $this->getDataGenerator()->create_user(['trackforums' => 1]);
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $activity = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Check the forum has no discussions yet.
        $this->setUser($teacher);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_discussions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('discussions', 'forum'), $item->get_name());
        $this->assertEquals(0, $item->get_value());

        // Post discussions (1 as teacher, 2 as student).
        $forumgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $forumgenerator->create_content($activity);
        $this->setUser($student);
        $forumgenerator->create_content($activity);
        $forumgenerator->create_content($activity);

        // Check for teacher.
        $this->setUser($teacher);
        // Reset static cache for further tests.
        forum_tp_count_forum_unread_posts($cm, $course, true);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_discussions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('discussions', 'forum'), $item->get_name());
        $this->assertEquals(3, $item->get_value());

        // Check for student.
        $this->setUser($student);
        // Reset static cache for further tests.
        forum_tp_count_forum_unread_posts($cm, $course, true);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_discussions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('discussions', 'forum'), $item->get_name());
        $this->assertEquals(3, $item->get_value());
    }
}
