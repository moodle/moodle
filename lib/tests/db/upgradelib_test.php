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

// Note: This namespace is not technically correct, but we have to make it different to the tests for lib/upgradelib.php
// and this is more correct than alternatives.
namespace core\db;

/**
 * Unit tests for the lib/db/upgradelib.php library.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2022 Andrew Lyons <andrew@thelyons.family>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgradelib_test extends \advanced_testcase {

    /**
     * Shared setup for the testcase.
     */
    public function setUp(): void {
        global $CFG;

        require_once("{$CFG->libdir}/db/upgradelib.php");
        require_once("{$CFG->dirroot}/my/lib.php");
        parent::setUp();
    }

    /**
     * Ensure that the upgrade_block_set_defaultregion function performs as expected.
     *
     * Only targetted blocks and pages should be affected.
     *
     * @covers ::upgrade_block_set_defaultregion
     */
    public function test_upgrade_block_set_defaultregion(): void {
        global $DB;

        $this->resetAfterTest();

        // Ensure that only the targetted blocks are affected.

        // Create a my-index entry for the Dashboard.
        $dashboardid = $DB->insert_record('my_pages', (object) [
            'name' => '__default',
            'private' => MY_PAGE_PRIVATE,
        ]);

        // Create a page for the my-courses page.
        $mycoursesid = $DB->insert_record('my_pages', (object) [
            'name' => '__courses',
            'private' => MY_PAGE_PRIVATE,
        ]);

        $unchanged = [];
        $changed = [];

        // Create several blocks of different types.
        // These are not linked to the my-index page above, so should not be modified.
        $unchanged[] = $this->getDataGenerator()->create_block('online_users', [
            'defaultregion' => 'left-side',
        ]);
        $unchanged[] = $this->getDataGenerator()->create_block('myoverview', [
            'defaultregion' => 'left-side',
        ]);
        $unchanged[] = $this->getDataGenerator()->create_block('calendar_month', [
            'defaultregion' => 'left-side',
        ]);

        // These are on the my-index above, but are not the block being updated.
        $unchanged[] = $this->getDataGenerator()->create_block('online_users', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);
        $unchanged[] = $this->getDataGenerator()->create_block('myoverview', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);

        // This is on a my-index page, and is the affected block, but is on the mycourses page, not the dashboard.
        $unchanged[] = $this->getDataGenerator()->create_block('calendar_month', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $mycoursesid,
            'defaultregion' => 'left-side',
        ]);

        // This is on the default dashboard, and is the affected block, but not a my-index page.
        $unchanged[] = $this->getDataGenerator()->create_block('calendar_month', [
            'pagetypepattern' => 'not-my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);

        // This is the match which should be changed.
        $changed[] = $this->getDataGenerator()->create_block('calendar_month', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);

        // Perform the operation.
        // Target all calendar_month blocks matching 'my-index' and update them to the 'content' region where they
        // belong to the user dashboard ('pagename' == '__default').
        upgrade_block_set_defaultregion('calendar_month', '__default', 'my-index', 'content');

        // Ensure that the relevant blocks remain unchanged.
        foreach ($unchanged as $original) {
            $block = $DB->get_record('block_instances', ['id' => $original->id]);
            $this->assertEquals($original, $block);
        }

        // Ensure that only the expected blocks were changed.
        foreach ($changed as $original) {
            $block = $DB->get_record('block_instances', ['id' => $original->id]);
            $this->assertNotEquals($original, $block);

            // Only the defaultregion should be updated to content. No other changes are expected.
            $expected = (object) $original;
            $expected->defaultregion = 'content';
            $this->assertEquals($expected, $block);
        }
    }

    /**
     * Ensure that the upgrade_block_set_defaultregion function performs as expected.
     *
     * Missing block entries will be created.
     *
     * @covers ::upgrade_block_set_defaultregion
     */
    public function test_upgrade_block_set_defaultregion_create_missing(): void {
        global $DB;

        $this->resetAfterTest();

        // Ensure that only the targetted blocks are affected.

        $dashboards = [];
        $mycourses = [];
        // Create dashboard pages for a number of users.
        while (count($dashboards) < 10) {
            $user = $this->getDataGenerator()->create_user();
            $dashboards[] = $DB->insert_record('my_pages', (object) [
                'userid' => $user->id,
                'name' => '__default',
                'private' => MY_PAGE_PRIVATE,
            ]);

            $mycourses[] = $DB->insert_record('my_pages', (object) [
                'userid' => $user->id,
                'name' => '__courses',
                'private' => MY_PAGE_PRIVATE,
            ]);
        }

        // Enusre that there are no blocks initially.
        foreach ($dashboards as $dashboardid) {
            $this->assertEquals(0, $DB->count_records('block_instances', [
                'subpagepattern' => $dashboardid,
            ]));
        }

        // Perform the operation.
        // Target all calendar_month blocks matching 'my-index' and update them to the 'content' region where they
        // belong to the user dashboard ('pagename' == '__default').
        // Any dashboards which are missing the block will have it created by the operation.
        upgrade_block_set_defaultregion('calendar_month', '__default', 'my-index', 'content');

        // Each of the dashboards should now have a block instance of the calendar_month block in the 'content' region
        // on 'my-index' only.
        foreach ($dashboards as $dashboardid) {
            // Only one block should have been created.
            $blocks = $DB->get_records('block_instances', [
                'subpagepattern' => $dashboardid,
            ]);
            $this->assertCount(1, $blocks);

            $theblock = reset($blocks);
            $this->assertEquals('calendar_month', $theblock->blockname);
            $this->assertEquals('content', $theblock->defaultregion);
            $this->assertEquals('my-index', $theblock->pagetypepattern);

            // Fetch the user details.
            $dashboard = $DB->get_record('my_pages', ['id' => $dashboardid]);
            $usercontext = \context_user::instance($dashboard->userid);

            $this->assertEquals($usercontext->id, $theblock->parentcontextid);
        }

        // Enusre that there are no blocks on the mycourses page.
        foreach ($mycourses as $pageid) {
            $this->assertEquals(0, $DB->count_records('block_instances', [
                'subpagepattern' => $pageid,
            ]));
        }
    }

    /**
     * Ensure that the upgrade_block_delete_instances function performs as expected.
     *
     * Missing block entries will be created.
     *
     * @covers ::upgrade_block_delete_instances
     */
    public function test_upgrade_block_delete_instances(): void {
        global $DB;

        $this->resetAfterTest();

        $DB->delete_records('block_instances');

        // Ensure that only the targetted blocks are affected.

        // Get the my-index entry for the Dashboard.
        $dashboardid = $DB->get_record('my_pages', [
            'userid' => null,
            'name' => '__default',
            'private' => MY_PAGE_PRIVATE,
        ], 'id')->id;

        // Get the page for the my-courses page.
        $mycoursesid = $DB->get_record('my_pages', [
            'name' => MY_PAGE_COURSES,
        ], 'id')->id;

        $dashboards = [];
        $unchanged = [];
        $unchangedcontexts = [];
        $unchangedpreferences = [];
        $deleted = [];
        $deletedcontexts = [];
        $deletedpreferences = [];

        // Create several blocks of different types.
        // These are not linked to the my page above, so should not be modified.
        $unchanged[] = $this->getDataGenerator()->create_block('online_users', [
            'defaultregion' => 'left-side',
        ]);
        $unchanged[] = $this->getDataGenerator()->create_block('myoverview', [
            'defaultregion' => 'left-side',
        ]);
        $unchanged[] = $this->getDataGenerator()->create_block('calendar_month', [
            'defaultregion' => 'left-side',
        ]);

        // These are on the my-index above, but are not the block being updated.
        $unchanged[] = $this->getDataGenerator()->create_block('online_users', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);
        $unchanged[] = $this->getDataGenerator()->create_block('myoverview', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);

        // This is on a my-index page, and is the affected block, but is on the mycourses page, not the dashboard.
        $unchanged[] = $this->getDataGenerator()->create_block('calendar_month', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $mycoursesid,
            'defaultregion' => 'left-side',
        ]);

        // This is on the default dashboard, and is the affected block, but not a my-index page.
        $unchanged[] = $this->getDataGenerator()->create_block('calendar_month', [
            'pagetypepattern' => 'not-my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);

        // This is the match which should be changed.
        $deleted[] = $this->getDataGenerator()->create_block('calendar_month', [
            'pagetypepattern' => 'my-index',
            'subpagepattern' => $dashboardid,
            'defaultregion' => 'left-side',
        ]);

        // Create blocks for users with preferences now.
        while (count($dashboards) < 10) {
            $userunchangedblocks = [];
            $userdeletedblocks = [];

            $user = $this->getDataGenerator()->create_user();
            $userdashboardid = $DB->insert_record('my_pages', (object) [
                'userid' => $user->id,
                'name' => '__default',
                'private' => MY_PAGE_PRIVATE,
            ]);
            $dashboards[] = $userdashboardid;

            $usermycoursesid = $DB->insert_record('my_pages', (object) [
                'userid' => $user->id,
                'name' => '__courses',
                'private' => MY_PAGE_PRIVATE,
            ]);

            // These are on the my-index above, but are not the block being updated.
            $userunchangedblocks[] = $this->getDataGenerator()->create_block('online_users', [
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $userdashboardid,
                'defaultregion' => 'left-side',
            ]);
            $userunchangedblocks[] = $this->getDataGenerator()->create_block('myoverview', [
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $userdashboardid,
                'defaultregion' => 'left-side',
            ]);

            // This is on a my-index page, and is the affected block, but is on the mycourses page, not the dashboard.
            $userunchangedblocks[] = $this->getDataGenerator()->create_block('calendar_month', [
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $usermycoursesid,
                'defaultregion' => 'left-side',
            ]);

            // This is on the default dashboard, and is the affected block, but not a my-index page.
            $userunchangedblocks[] = $this->getDataGenerator()->create_block('calendar_month', [
                'pagetypepattern' => 'not-my-index',
                'subpagepattern' => $userdashboardid,
                'defaultregion' => 'left-side',
            ]);

            // This is the match which should be changed.
            $userdeletedblocks[] = $this->getDataGenerator()->create_block('calendar_month', [
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $userdashboardid,
                'defaultregion' => 'left-side',
            ]);

            $unchanged += $userunchangedblocks;
            $deleted += $userdeletedblocks;

            foreach ($userunchangedblocks as $block) {
                // Create user preferences for these blocks.
                set_user_preference("block{$block->id}hidden", 1, $user);
                set_user_preference("docked_block_instance_{$block->id}", 1, $user);
                $unchangedpreferences[] = $block->id;
            }

            foreach ($userdeletedblocks as $block) {
                // Create user preferences for these blocks.
                set_user_preference("block{$block->id}hidden", 1, $user);
                set_user_preference("docked_block_instance_{$block->id}", 1, $user);
                $deletedpreferences[] = $block->id;
            }
        }

        // Create missing contexts.
        \context_helper::create_instances(CONTEXT_BLOCK);

        // Ensure that other related test data is present.
        $systemcontext = \context_system::instance();
        foreach ($unchanged as $block) {
            // Get contexts.
            $unchangedcontexts[] = \context_block::instance($block->id);

            // Create a block position.
            $DB->insert_record('block_positions', [
                'blockinstanceid' => $block->id,
                'contextid' => $systemcontext->id,
                'pagetype' => 'course-view-topics',
                'region' => 'site-post',
                'weight' => 1,
                'visible' => 1,
            ]);
        }

        foreach ($deleted as $block) {
            // Get contexts.
            $deletedcontexts[] = \context_block::instance($block->id);

            // Create a block position.
            $DB->insert_record('block_positions', [
                'blockinstanceid' => $block->id,
                'contextid' => $systemcontext->id,
                'pagetype' => 'course-view-topics',
                'region' => 'site-post',
                'weight' => 1,
                'visible' => 1,
            ]);
        }

        // Perform the operation.
        // Target all calendar_month blocks matching 'my-index' and update them to the 'content' region where they
        // belong to the user dashboard ('pagename' == '__default').
        upgrade_block_delete_instances('calendar_month', '__default', 'my-index');

        // Ensure that the relevant blocks remain unchanged.
        foreach ($unchanged as $original) {
            $block = $DB->get_record('block_instances', ['id' => $original->id]);
            $this->assertEquals($original, $block);

            // Ensure that the block positions remain.
            $this->assertEquals(1, $DB->count_records('block_positions', ['blockinstanceid' => $original->id]));
        }

        foreach ($unchangedcontexts as $context) {
            // Ensure that the context still exists.
            $this->assertEquals(1, $DB->count_records('context', ['id' => $context->id]));
        }

        foreach ($unchangedpreferences as $blockid) {
            // Ensure that the context still exists.
            $this->assertEquals(1, $DB->count_records('user_preferences', ['name' => "block{$blockid}hidden"]));
            $this->assertEquals(1, $DB->count_records('user_preferences', [
                'name' => "docked_block_instance_{$blockid}",
            ]));
        }

        // Ensure that only the expected blocks were changed.
        foreach ($deleted as $original) {
            $this->assertCount(0, $DB->get_records('block_instances', ['id' => $original->id]));

            // Ensure that the block positions was removed.
            $this->assertEquals(0, $DB->count_records('block_positions', ['blockinstanceid' => $original->id]));
        }

        foreach ($deletedcontexts as $context) {
            // Ensure that the context still exists.
            $this->assertEquals(0, $DB->count_records('context', ['id' => $context->id]));
        }

        foreach ($deletedpreferences as $blockid) {
            // Ensure that the context still exists.
            $this->assertEquals(0, $DB->count_records('user_preferences', ['name' => "block{$blockid}hidden"]));
            $this->assertEquals(0, $DB->count_records('user_preferences', [
                'name' => "docked_block_instance_{$blockid}",
            ]));
        }
    }

    /**
     * Ensrue that the upgrade_block_set_my_user_parent_context function performs as expected.
     *
     * @covers ::upgrade_block_set_my_user_parent_context
     */
    public function test_upgrade_block_set_my_user_parent_context(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $systemcontext = \context_system::instance();

        $dashboards = [];
        $otherblocknames = [
            'online_users',
            'myoverview',
            'calendar_month',
        ];
        $affectedblockname = 'timeline';

        // Create dashboard pages for a number of users.
        while (count($dashboards) < 10) {
            $user = $this->getDataGenerator()->create_user();
            $dashboard = $DB->insert_record('my_pages', (object) [
                'userid' => $user->id,
                'name' => '__default',
                'private' => MY_PAGE_PRIVATE,
            ]);
            $dashboards[] = $dashboard;

            $mycourse = $DB->insert_record('my_pages', (object) [
                'userid' => $user->id,
                'name' => '__courses',
                'private' => MY_PAGE_PRIVATE,
            ]);

            // These are on the my-index above, but are not the block being updated.
            foreach ($otherblocknames as $blockname) {
                $unchanged[] = $this->getDataGenerator()->create_block($blockname, [
                    'parentcontextid' => $systemcontext->id,
                    'pagetypepattern' => 'my-index',
                    'subpagepattern' => $dashboard,
                ]);
            }

            // This is on a my-index page, and is the affected block, but is on the mycourses page, not the dashboard.
            $unchanged[] = $this->getDataGenerator()->create_block($affectedblockname, [
                'parentcontextid' => $systemcontext->id,
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $mycourse,
            ]);

            // This is on the default dashboard, and is the affected block, but not a my-index page.
            $unchanged[] = $this->getDataGenerator()->create_block($affectedblockname, [
                'parentcontextid' => $systemcontext->id,
                'pagetypepattern' => 'not-my-index',
                'subpagepattern' => $dashboard,
            ]);

            // This is the match which should be changed.
            $changed[] = $this->getDataGenerator()->create_block($affectedblockname, [
                'parentcontextid' => $systemcontext->id,
                'pagetypepattern' => 'my-index',
                'subpagepattern' => $dashboard,
            ]);
        }

        // Perform the operation.
        // Target all affected blocks matching 'my-index' and correct the context to the relevant user's contexct.
        // Only the '__default' dashboard on the 'my-index' my_page should be affected.
        upgrade_block_set_my_user_parent_context($affectedblockname, '__default', 'my-index');

        // Ensure that the relevant blocks remain unchanged.
        foreach ($unchanged as $original) {
            $block = $DB->get_record('block_instances', ['id' => $original->id]);
            $this->assertEquals($original, $block);
        }

        // Ensure that only the expected blocks were changed.
        foreach ($changed as $original) {
            $block = $DB->get_record('block_instances', ['id' => $original->id]);
            $this->assertNotEquals($original, $block);

            // Fetch the my page and user details.
            $dashboard = $DB->get_record('my_pages', ['id' => $original->subpagepattern]);
            $usercontext = \context_user::instance($dashboard->userid);

            // Only the contextid should be updated to the relevant user's context.
            // No other changes are expected.
            $expected = (object) $original;
            $expected->parentcontextid = $usercontext->id;
            $this->assertEquals($expected, $block);
        }
    }
}
