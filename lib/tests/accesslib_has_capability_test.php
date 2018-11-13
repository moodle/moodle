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
 * A collection of tests for accesslib::has_capability().
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests tests for has_capability.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accesslib_has_capability_testcase extends \advanced_testcase {

    /**
     * Unit tests to check the operation of locked contexts.
     *
     * Note: We only check the admin user here.
     * If the admin cannot do it, then no-one can.
     *
     * @dataProvider locked_context_provider
     * @param   string[]    $lockedcontexts The list of contexts, by name, to mark as locked
     * @param   string[]    $blocked The list of contexts which will be 'blocked' by has_capability
     */
    public function test_locked_contexts($lockedcontexts, $blocked) {
        global $DB;

        $this->resetAfterTest();
        set_config('contextlocking', 1);

        $generator = $this->getDataGenerator();
        $otheruser = $generator->create_user();

        // / (system)
        // /Cat1
        // /Cat1/Block
        // /Cat1/Course1
        // /Cat1/Course1/Block
        // /Cat1/Course2
        // /Cat1/Course2/Block
        // /Cat1/Cat1a
        // /Cat1/Cat1a/Block
        // /Cat1/Cat1a/Course1
        // /Cat1/Cat1a/Course1/Block
        // /Cat1/Cat1a/Course2
        // /Cat1/Cat1a/Course2/Block
        // /Cat1/Cat1b
        // /Cat1/Cat1b/Block
        // /Cat1/Cat1b/Course1
        // /Cat1/Cat1b/Course1/Block
        // /Cat1/Cat1b/Course2
        // /Cat1/Cat1b/Course2/Block
        // /Cat2
        // /Cat2/Block
        // /Cat2/Course1
        // /Cat2/Course1/Block
        // /Cat2/Course2
        // /Cat2/Course2/Block
        // /Cat2/Cat2a
        // /Cat2/Cat2a/Block
        // /Cat2/Cat2a/Course1
        // /Cat2/Cat2a/Course1/Block
        // /Cat2/Cat2a/Course2
        // /Cat2/Cat2a/Course2/Block
        // /Cat2/Cat2b
        // /Cat2/Cat2b/Block
        // /Cat2/Cat2b/Course1
        // /Cat2/Cat2b/Course1/Block
        // /Cat2/Cat2b/Course2
        // /Cat2/Cat2b/Course2/Block

        $adminuser = \core_user::get_user_by_username('admin');
        $contexts = (object) [
            'system' => \context_system::instance(),
            'adminuser' => \context_user::instance($adminuser->id),
        ];

        $cat1 = $generator->create_category();
        $cat1a = $generator->create_category(['parent' => $cat1->id]);
        $cat1b = $generator->create_category(['parent' => $cat1->id]);

        $contexts->cat1 = \context_coursecat::instance($cat1->id);
        $contexts->cat1a = \context_coursecat::instance($cat1a->id);
        $contexts->cat1b = \context_coursecat::instance($cat1b->id);

        $cat1course1 = $generator->create_course(['category' => $cat1->id]);
        $cat1course2 = $generator->create_course(['category' => $cat1->id]);
        $cat1acourse1 = $generator->create_course(['category' => $cat1a->id]);
        $cat1acourse2 = $generator->create_course(['category' => $cat1a->id]);
        $cat1bcourse1 = $generator->create_course(['category' => $cat1b->id]);
        $cat1bcourse2 = $generator->create_course(['category' => $cat1b->id]);

        $contexts->cat1course1 = \context_course::instance($cat1course1->id);
        $contexts->cat1acourse1 = \context_course::instance($cat1acourse1->id);
        $contexts->cat1bcourse1 = \context_course::instance($cat1bcourse1->id);
        $contexts->cat1course2 = \context_course::instance($cat1course2->id);
        $contexts->cat1acourse2 = \context_course::instance($cat1acourse2->id);
        $contexts->cat1bcourse2 = \context_course::instance($cat1bcourse2->id);

        $cat1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1->id]);
        $cat1ablock = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1a->id]);
        $cat1bblock = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1b->id]);
        $cat1course1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1course1->id]);
        $cat1course2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1course2->id]);
        $cat1acourse1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1acourse1->id]);
        $cat1acourse2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1acourse2->id]);
        $cat1bcourse1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1bcourse1->id]);
        $cat1bcourse2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1bcourse2->id]);

        $contexts->cat1block = \context_block::instance($cat1block->id);
        $contexts->cat1ablock = \context_block::instance($cat1ablock->id);
        $contexts->cat1bblock = \context_block::instance($cat1bblock->id);
        $contexts->cat1course1block = \context_block::instance($cat1course1block->id);
        $contexts->cat1course2block = \context_block::instance($cat1course2block->id);
        $contexts->cat1acourse1block = \context_block::instance($cat1acourse1block->id);
        $contexts->cat1acourse2block = \context_block::instance($cat1acourse2block->id);
        $contexts->cat1bcourse1block = \context_block::instance($cat1bcourse1block->id);
        $contexts->cat1bcourse2block = \context_block::instance($cat1bcourse2block->id);

        $writecapability = 'moodle/block:edit';
        $readcapability = 'moodle/block:view';
        $managecapability = 'moodle/site:managecontextlocks';

        $this->setAdminUser();
        $totest = (array) $contexts;
        foreach ($totest as $context) {
            $this->assertTrue(has_capability($writecapability, $context));
            $this->assertTrue(has_capability($readcapability, $context));
            $this->assertTrue(has_capability($managecapability, $context));
        }

        // Lock the specified contexts.
        foreach ($lockedcontexts as $contextname => $value) {
            $contexts->$contextname->set_locked($value);
        }

        // All read capabilities should remain.
        foreach ((array) $contexts as $context) {
            $this->assertTrue(has_capability($readcapability, $context));
            $this->assertTrue(has_capability($managecapability, $context));
        }

        // Check writes.
        foreach ((array) $contexts as $contextname => $context) {
            if (false !== array_search($contextname, $blocked)) {
                $this->assertFalse(has_capability($writecapability, $context));
            } else {
                $this->assertTrue(has_capability($writecapability, $context));
            }
        }

        $this->setUser($otheruser);
        // Check writes.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertFalse(has_capability($writecapability, $context));
        }

        // Disable the contextlocking experimental feature.
        set_config('contextlocking', 0);

        $this->setAdminUser();
        // All read capabilities should remain.
        foreach ((array) $contexts as $context) {
            $this->assertTrue(has_capability($readcapability, $context));
            $this->assertTrue(has_capability($managecapability, $context));
        }

        // All write capabilities should now be present again.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertTrue(has_capability($writecapability, $context));
        }

        $this->setUser($otheruser);
        // Check writes.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertFalse(has_capability($writecapability, $context));
        }
    }

    /**
     * Unit tests to check the operation of locked contexts.
     *
     * Note: We only check the admin user here.
     * If the admin cannot do it, then no-one can.
     *
     * @dataProvider locked_context_provider
     * @param   string[]    $lockedcontexts The list of contexts, by name, to mark as locked
     * @param   string[]    $blocked The list of contexts which will be 'blocked' by has_capability
     */
    public function test_locked_contexts_for_admin_with_config($lockedcontexts, $blocked) {
        global $DB;

        $this->resetAfterTest();
        set_config('contextlocking', 1);
        set_config('contextlockappliestoadmin', 0);

        $generator = $this->getDataGenerator();
        $otheruser = $generator->create_user();

        // / (system)
        // /Cat1
        // /Cat1/Block
        // /Cat1/Course1
        // /Cat1/Course1/Block
        // /Cat1/Course2
        // /Cat1/Course2/Block
        // /Cat1/Cat1a
        // /Cat1/Cat1a/Block
        // /Cat1/Cat1a/Course1
        // /Cat1/Cat1a/Course1/Block
        // /Cat1/Cat1a/Course2
        // /Cat1/Cat1a/Course2/Block
        // /Cat1/Cat1b
        // /Cat1/Cat1b/Block
        // /Cat1/Cat1b/Course1
        // /Cat1/Cat1b/Course1/Block
        // /Cat1/Cat1b/Course2
        // /Cat1/Cat1b/Course2/Block
        // /Cat2
        // /Cat2/Block
        // /Cat2/Course1
        // /Cat2/Course1/Block
        // /Cat2/Course2
        // /Cat2/Course2/Block
        // /Cat2/Cat2a
        // /Cat2/Cat2a/Block
        // /Cat2/Cat2a/Course1
        // /Cat2/Cat2a/Course1/Block
        // /Cat2/Cat2a/Course2
        // /Cat2/Cat2a/Course2/Block
        // /Cat2/Cat2b
        // /Cat2/Cat2b/Block
        // /Cat2/Cat2b/Course1
        // /Cat2/Cat2b/Course1/Block
        // /Cat2/Cat2b/Course2
        // /Cat2/Cat2b/Course2/Block

        $adminuser = \core_user::get_user_by_username('admin');
        $contexts = (object) [
            'system' => \context_system::instance(),
            'adminuser' => \context_user::instance($adminuser->id),
        ];

        $cat1 = $generator->create_category();
        $cat1a = $generator->create_category(['parent' => $cat1->id]);
        $cat1b = $generator->create_category(['parent' => $cat1->id]);

        $contexts->cat1 = \context_coursecat::instance($cat1->id);
        $contexts->cat1a = \context_coursecat::instance($cat1a->id);
        $contexts->cat1b = \context_coursecat::instance($cat1b->id);

        $cat1course1 = $generator->create_course(['category' => $cat1->id]);
        $cat1course2 = $generator->create_course(['category' => $cat1->id]);
        $cat1acourse1 = $generator->create_course(['category' => $cat1a->id]);
        $cat1acourse2 = $generator->create_course(['category' => $cat1a->id]);
        $cat1bcourse1 = $generator->create_course(['category' => $cat1b->id]);
        $cat1bcourse2 = $generator->create_course(['category' => $cat1b->id]);

        $contexts->cat1course1 = \context_course::instance($cat1course1->id);
        $contexts->cat1acourse1 = \context_course::instance($cat1acourse1->id);
        $contexts->cat1bcourse1 = \context_course::instance($cat1bcourse1->id);
        $contexts->cat1course2 = \context_course::instance($cat1course2->id);
        $contexts->cat1acourse2 = \context_course::instance($cat1acourse2->id);
        $contexts->cat1bcourse2 = \context_course::instance($cat1bcourse2->id);

        $cat1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1->id]);
        $cat1ablock = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1a->id]);
        $cat1bblock = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1b->id]);
        $cat1course1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1course1->id]);
        $cat1course2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1course2->id]);
        $cat1acourse1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1acourse1->id]);
        $cat1acourse2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1acourse2->id]);
        $cat1bcourse1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1bcourse1->id]);
        $cat1bcourse2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat1bcourse2->id]);

        $contexts->cat1block = \context_block::instance($cat1block->id);
        $contexts->cat1ablock = \context_block::instance($cat1ablock->id);
        $contexts->cat1bblock = \context_block::instance($cat1bblock->id);
        $contexts->cat1course1block = \context_block::instance($cat1course1block->id);
        $contexts->cat1course2block = \context_block::instance($cat1course2block->id);
        $contexts->cat1acourse1block = \context_block::instance($cat1acourse1block->id);
        $contexts->cat1acourse2block = \context_block::instance($cat1acourse2block->id);
        $contexts->cat1bcourse1block = \context_block::instance($cat1bcourse1block->id);
        $contexts->cat1bcourse2block = \context_block::instance($cat1bcourse2block->id);

        $writecapability = 'moodle/block:edit';
        $readcapability = 'moodle/block:view';
        $managecapability = 'moodle/site:managecontextlocks';

        $this->setAdminUser();
        $totest = (array) $contexts;
        foreach ($totest as $context) {
            $this->assertTrue(has_capability($writecapability, $context));
            $this->assertTrue(has_capability($readcapability, $context));
            $this->assertTrue(has_capability($managecapability, $context));
        }

        // Lock the specified contexts.
        foreach ($lockedcontexts as $contextname => $value) {
            $contexts->$contextname->set_locked($value);
        }

        // All read capabilities should remain.
        foreach ((array) $contexts as $context) {
            $this->assertTrue(has_capability($readcapability, $context));
            $this->assertTrue(has_capability($managecapability, $context));
        }

        // Check writes.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertTrue(has_capability($writecapability, $context));
        }

        $this->setUser($otheruser);
        // Check writes.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertFalse(has_capability($writecapability, $context));
        }

        // Disable the contextlocking experimental feature.
        set_config('contextlocking', 0);

        $this->setAdminUser();
        // All read capabilities should remain.
        foreach ((array) $contexts as $context) {
            $this->assertTrue(has_capability($readcapability, $context));
            $this->assertTrue(has_capability($managecapability, $context));
        }

        // All write capabilities should now be present again.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertTrue(has_capability($writecapability, $context));
        }

        $this->setUser($otheruser);
        // Check writes.
        foreach ((array) $contexts as $contextname => $context) {
            $this->assertFalse(has_capability($writecapability, $context));
        }
    }

    /**
     * Data provider for testing that has_capability() deals with locked contexts.
     *
     * @return  array
     */
    public function locked_context_provider() {
        return [
            'All unlocked' => [
                'locked' => [
                ],
                'blockedwrites' => [
                ],
            ],
            'User is locked (yes, this is weird)' => [
                'locked' => [
                    'adminuser' => true,
                ],
                'blockedwrites' => [
                    'adminuser',
                ],
            ],
            'Cat1/Block locked' => [
                'locked' => [
                    'cat1block' => true,
                ],
                'blockedwrites' => [
                    'cat1block',
                ],
            ],
            'Cat1' => [
                'locked' => [
                    'cat1' => true,
                ],
                'blockedwrites' => [
                    'cat1',
                    'cat1block',
                    'cat1a',
                    'cat1ablock',
                    'cat1b',
                    'cat1bblock',
                    'cat1course1',
                    'cat1course1block',
                    'cat1course2',
                    'cat1course2block',
                    'cat1acourse1',
                    'cat1acourse1block',
                    'cat1acourse2',
                    'cat1acourse2block',
                    'cat1bcourse1',
                    'cat1bcourse1block',
                    'cat1bcourse2',
                    'cat1bcourse2block',
                ],
            ],
            'Cat1 locked and a child explicitly unlocked' => [
                'locked' => [
                    'cat1' => true,
                    'cat1a' => false,
                ],
                'blockedwrites' => [
                    'cat1',
                    'cat1block',
                    'cat1a',
                    'cat1ablock',
                    'cat1b',
                    'cat1bblock',
                    'cat1course1',
                    'cat1course1block',
                    'cat1course2',
                    'cat1course2block',
                    'cat1acourse1',
                    'cat1acourse1block',
                    'cat1acourse2',
                    'cat1acourse2block',
                    'cat1bcourse1',
                    'cat1bcourse1block',
                    'cat1bcourse2',
                    'cat1bcourse2block',
                ],
            ],
        ];
    }
}
