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
 * Contains unit tests for core_completion/activity_custom_completion.
 *
 * @package   mod_forum
 * @copyright Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_forum;

use advanced_testcase;
use cm_info;
use coding_exception;
use mod_forum\completion\custom_completion;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/mod/forum/tests/generator/lib.php');
require_once($CFG->dirroot . '/mod/forum/tests/generator_trait.php');

/**
 * Class for unit testing mod_forum/activity_custom_completion.
 *
 * @package   mod_forum
 * @copyright Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_completion_test extends advanced_testcase {

    use \mod_forum_tests_generator_trait;

    /**
     * Data provider for get_state().
     *
     * @return array[]
     */
    public static function get_state_provider(): array {
        return [
            'Undefined rule' => [
                'somenonexistentrule', 0, COMPLETION_TRACKING_NONE, 0, 0, 0, null, coding_exception::class
            ],
            'Completion discussions rule not available'  => [
                'completiondiscussions', 0,  COMPLETION_TRACKING_NONE, 0, 0, 0, null, moodle_exception::class
            ],
            'Completion discussions rule available, user has not created discussion' => [
                'completiondiscussions', 0, COMPLETION_TRACKING_AUTOMATIC, 5, 0, 0, COMPLETION_INCOMPLETE, null
            ],
            'Rule available, user has created discussions' => [
                'completiondiscussions', 5, COMPLETION_TRACKING_AUTOMATIC, 5, 0, 0, COMPLETION_COMPLETE, null
            ],
            'Completion replies rule not available' => [
                'completionreplies', 0, COMPLETION_TRACKING_NONE, 0, 0, 0, null, moodle_exception::class
            ],
            'Rule available, user has not replied' => [
                'completionreplies', 0, COMPLETION_TRACKING_AUTOMATIC, 0, 5, 0, COMPLETION_INCOMPLETE, null
            ],
            'Rule available, user has created replied' => [
                'completionreplies', 5, COMPLETION_TRACKING_AUTOMATIC, 0, 5, 0, COMPLETION_COMPLETE, null
            ],
            'Completion posts rule not available' => [
                'completionposts', 0, COMPLETION_TRACKING_NONE, 0, 0, 0, null, moodle_exception::class
            ],
            'Rule available, user has not posted' => [
                'completionposts', 0, COMPLETION_TRACKING_AUTOMATIC, 0, 0, 5, COMPLETION_INCOMPLETE, null
            ],
            'Rule available, user has posted' => [
                'completionposts', 5, COMPLETION_TRACKING_AUTOMATIC, 0, 0, 5, COMPLETION_COMPLETE, null
            ],
        ];
    }

    /**
     * Test for get_state().
     *
     * @dataProvider get_state_provider
     * @param string $rule The custom completion rule.
     * @param int $rulecount Quantity of discussions, replies or posts to be created.
     * @param int $available Whether this rule is available.
     * @param int|null $discussions The number of discussions.
     * @param int|null $replies The number of replies.
     * @param int|null $posts The number of posts.
     * @param int|null $status Expected status.
     * @param string|null $exception Expected exception.
     */
    public function test_get_state(string $rule, int $rulecount, int $available, ?int $discussions, ?int $replies,
                                   ?int $posts, ?int $status, ?string $exception): void {

        if (!is_null($exception)) {
            $this->expectException($exception);
        }

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $forumgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $params = [
            'course' => $course->id,
            'completion' => $available,
            'completiondiscussions' => $discussions,
            'completionreplies' => $replies,
            'completionposts' => $posts
        ];
        $forum = $this->getDataGenerator()->create_module('forum', $params);

        $cm = get_coursemodule_from_instance('forum', $forum->id);

        if ($rulecount > 0) {
            if ($rule == 'completiondiscussions') {
                // Create x number of discussions.
                for ($i = 0; $i < $rulecount; $i++) {
                    $forumgenerator->create_discussion((object) [
                        'course' => $forum->course,
                        'userid' => $student->id,
                        'forum' => $forum->id,
                    ]);
                }
            } else if ($rule == 'completionreplies') {
                [$discussion1, $post1] = $this->helper_post_to_forum($forum, $student);
                for ($i = 0; $i < $rulecount; $i++) {
                    $this->helper_reply_to_post($post1, $student);
                }
            } else if ($rule == 'completionposts') {
                for ($i = 0; $i < $rulecount; $i++) {
                    $this->helper_post_to_forum($forum, $student);
                }
            }
        }

        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $customcompletion = new custom_completion($cm, (int)$student->id);
        $this->assertEquals($status, $customcompletion->get_state($rule));
    }

    /**
     * Test for get_defined_custom_rules().
     */
    public function test_get_defined_custom_rules(): void {
        $rules = custom_completion::get_defined_custom_rules();
        $this->assertCount(3, $rules);
        $this->assertEquals('completiondiscussions', reset($rules));
    }

    /**
     * Test for get_defined_custom_rule_descriptions().
     */
    public function test_get_custom_rule_descriptions(): void {
        // Get defined custom rules.
        $rules = custom_completion::get_defined_custom_rules();

        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();
        // Instantiate a custom_completion object using the mocked cm_info.
        $customcompletion = new custom_completion($mockcminfo, 1);

        // Get custom rule descriptions.
        $ruledescriptions = $customcompletion->get_custom_rule_descriptions();

        // Confirm that defined rules and rule descriptions are consistent with each other.
        $this->assertEquals(count($rules), count($ruledescriptions));
        foreach ($rules as $rule) {
            $this->assertArrayHasKey($rule, $ruledescriptions);
        }
    }

    /**
     * Test for is_defined().
     */
    public function test_is_defined(): void {
        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customcompletion = new custom_completion($mockcminfo, 1);

        // Rule is defined.
        $this->assertTrue($customcompletion->is_defined('completiondiscussions'));

        // Undefined rule.
        $this->assertFalse($customcompletion->is_defined('somerandomrule'));
    }

    /**
     * Data provider for test_get_available_custom_rules().
     *
     * @return array[]
     */
    public static function get_available_custom_rules_provider(): array {
        return [
            'Completion discussions available' => [
                COMPLETION_ENABLED, ['completiondiscussions']
            ],
            'Completion discussions not available' => [
                COMPLETION_DISABLED, []
            ],
            'Completion replies available' => [
                COMPLETION_ENABLED, ['completionreplies']
            ],
            'Completion replies not available' => [
                COMPLETION_DISABLED, []
            ],
            'Completion posts available' => [
                COMPLETION_ENABLED, ['completionposts']
            ],
            'Completion posts not available' => [
                COMPLETION_DISABLED, []
            ],
        ];
    }

    /**
     * Test for get_available_custom_rules().
     *
     * @dataProvider get_available_custom_rules_provider
     * @param int $status
     * @param array $expected
     */
    public function test_get_available_custom_rules(int $status, array $expected): void {
        $customdataval = [
            'customcompletionrules' => []
        ];
        if ($status == COMPLETION_ENABLED) {
            $rule = $expected[0];
            $customdataval = [
                'customcompletionrules' => [$rule => $status]
            ];
        }

        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();

        // Mock the return of magic getter for the customdata attribute.
        $mockcminfo->expects($this->any())
            ->method('__get')
            ->with('customdata')
            ->willReturn($customdataval);

        $customcompletion = new custom_completion($mockcminfo, 1);
        $this->assertEquals($expected, $customcompletion->get_available_custom_rules());
    }
}
