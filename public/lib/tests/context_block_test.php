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

// Note: Technically this namespace is incorrect, but we should be moving to namespace things and core anyway.
namespace core;

/**
 * Unit tests specifically for context_block.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \context_block
 */
final class context_block_test extends \advanced_testcase {

    /**
     * Test setup.
     */
    public function setUp(): void {
        global $CFG;
        require_once("{$CFG->libdir}/accesslib.php");
        parent::setUp();
    }

    /**
     * Ensure that block contexts are correctly created for blocks where they are missing.
     *
     * @covers ::create_level_instances
     */
    public function test_context_creation(): void {
        global $DB;

        $this->resetAfterTest();

        // Create some parent contexts.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $coursecat = $generator->create_category();
        $course = $generator->create_course(['category' => $coursecat->id]);
        $activity = $generator->create_module('forum', ['course' => $course->id]);

        $contextlist = [
            \context_system::instance(),
            \context_user::instance($user->id),
            \context_coursecat::instance($coursecat->id),
            \context_course::instance($course->id),
            \context_module::instance($activity->cmid),
        ];

        // Create a number of blocks of different types in the DB only.
        // This is typically seen when creating large numbers in an upgrade script.
        $blocks = [];
        for ($i = 0; $i < 10; $i++) {
            foreach ($contextlist as $context) {
                $blocks[] = $DB->insert_record('block_instances', [
                    'blockname' => 'calendar_month',
                    'parentcontextid' => $context->id,
                    'showinsubcontexts' => 1,
                    'requiredbytheme' => 0,
                    'pagetypepattern' => 'my-index',
                    'subpagepattern' => 1,
                    'defaultregion' => 'content',
                    'defaultweight' => 1,
                    'timecreated' => time(),
                    'timemodified' => time(),
                ]);
            }
        }

        // Test data created. Call \context_helper::create_instances() which will create the records, and fix the paths.
        \context_helper::create_instances(CONTEXT_BLOCK);

        foreach ($blocks as $blockid) {
            $block = $DB->get_record('block_instances', ['id' => $blockid]);
            $context = \context_block::instance($block->id);
            $this->assertInstanceOf(\context_block::class, $context);

            // Note. There is no point checking the instanceid because the context was fetched using this.

            // Ensure that the contextlevel is correct.
            $this->assertEquals(CONTEXT_BLOCK, $context->contextlevel);

            // Fetch the parent context.
            $parentcontext = $context->get_parent_context();

            // This hsould match the parent context specified in the block instance configuration.
            $this->assertEquals($block->parentcontextid, $parentcontext->id);

            // Ensure that the path and depth are correctly specified.
            $this->assertEquals($parentcontext->path . "/{$context->id}", $context->path);
            $this->assertEquals($parentcontext->depth + 1, $context->depth);
        }
    }
}
