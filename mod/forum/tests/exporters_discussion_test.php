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
 * The discussion exporter tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\exporters\discussion as discussion_exporter;

/**
 * The discussion exporter tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_exporters_discussion_testcase extends advanced_testcase {
    /**
     * Test set up function.
     */
    public function setUp(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        $builderfactory = \mod_forum\local\container::get_builder_factory();
        $this->builder = $builderfactory->get_exported_posts_builder();
    }

    /**
     * Test tear down function.
     */
    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
    }

    /**
     * Test the export function returns expected values.
     */
    public function test_export() {
        global $PAGE;
        $this->resetAfterTest();

        $renderer = $PAGE->get_renderer('core');
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = context_module::instance($coursemodule->id);
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $forum = $entityfactory->get_forum_from_stdclass($forum, $context, $coursemodule, $course);
        $group = $datagenerator->create_group(['courseid' => $course->id]);
        $now = time();
        $discussion = new discussion_entity(
            1,
            $course->id,
            $forum->get_id(),
            'test discussion',
            1,
            $user->id,
            $group->id,
            false,
            $now,
            $now,
            0,
            0,
            false,
            0
        );

        $exporter = new discussion_exporter($discussion, [
            'legacydatamapperfactory' => \mod_forum\local\container::get_legacy_data_mapper_factory(),
            'urlfactory' => \mod_forum\local\container::get_url_factory(),
            'capabilitymanager' => (\mod_forum\local\container::get_manager_factory())->get_capability_manager($forum),
            'context' => $context,
            'forum' => $forum,
            'user' => $user,
            'groupsbyid' => [$group->id => $group],
            'latestpostid' => 7
        ]);

        $exporteddiscussion = $exporter->export($renderer);

        $this->assertEquals(1, $exporteddiscussion->id);
        $this->assertEquals($forum->get_id(), $exporteddiscussion->forumid);
        $this->assertEquals(false, $exporteddiscussion->pinned);
        $this->assertEquals('test discussion', $exporteddiscussion->name);
        $this->assertEquals($now, $exporteddiscussion->times['modified']);
        $this->assertEquals(0, $exporteddiscussion->times['start']);
        $this->assertEquals(0, $exporteddiscussion->times['end']);
        $this->assertEquals($group->name, $exporteddiscussion->group['name']);
    }
}
