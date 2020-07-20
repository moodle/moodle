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
     * Data provider for for has_capability tests when logged in as a different user.
     *
     * @return  array
     */
    public function login_as_provider(): array {
        return [
            [
                'system',
                [
                    'cat1course1block' => true,
                    'cat1course1' => true,
                    'cat1course2block' => true,
                    'cat1course2' => true,
                    'cat2course1block' => true,
                    'cat2course1' => true,
                    'cat2course2block' => true,
                    'cat2course2' => true,
                ],
            ],
            [
                'cat1',
                [
                    'cat1course1block' => true,
                    'cat1course1' => true,
                    'cat1course2block' => true,
                    'cat1course2' => true,

                    'cat2course1block' => false,
                    'cat2course1' => false,
                    'cat2course2block' => false,
                    'cat2course2' => false,
                ],
            ],
            [
                'cat1course1',
                [
                    'cat1course1block' => true,
                    'cat1course1' => true,

                    'cat1course2block' => false,
                    'cat1course2' => false,
                    'cat2course1block' => false,
                    'cat2course1' => false,
                    'cat2course2block' => false,
                    'cat2course2' => false,
                ],
            ],
            [
                'cat1course1block',
                [
                    'cat1course1block' => true,

                    'cat1course1' => false,
                    'cat1course2block' => false,
                    'cat1course2' => false,
                    'cat2course1block' => false,
                    'cat2course1' => false,
                    'cat2course2block' => false,
                    'cat2course2' => false,
                ],
            ],
        ];
    }

    /**
     * Test that the log in as functionality works as expected for an administrator.
     *
     * An administrator logged in as another user assumes all of their capabilities.
     *
     * @dataProvider    login_as_provider
     * @param   string $loginascontext
     * @param   string $testcontexts
     */
    public function test_login_as_admin(string $loginascontext, array $testcontexts) {
        $this->resetAfterTest();

        $contexts = $this->get_test_data();

        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();

        $testcontext = $contexts->$loginascontext;
        \core\session\manager::loginas($user->id, $testcontext);

        $capability = 'moodle/block:view';
        foreach ($testcontexts as $contextname => $hascapability) {
            $this->assertEquals($hascapability, has_capability($capability, $contexts->$contextname));
        }
    }

    /**
     * Test that the log in as functionality works as expected for a regulr user.
     *
     * @dataProvider    login_as_provider
     * @param   string $loginascontext
     * @param   string $testcontexts
     */
    public function test_login_as_user(string $loginascontext, array $testcontexts) {
        $this->resetAfterTest();

        $contexts = $this->get_test_data();

        $initialuser = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($initialuser);

        $testcontext = $contexts->$loginascontext;
        \core\session\manager::loginas($user->id, $testcontext);

        $capability = 'moodle/block:view';
        foreach ($testcontexts as $contextname => $hascapability) {
            $this->assertEquals($hascapability, has_capability($capability, $contexts->$contextname));
        }
    }

    /**
     * Get the test data contexts.
     *
     * @return  stdClass
     */
    protected function get_test_data(): stdclass {
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

        $cat2 = $generator->create_category();
        $cat2a = $generator->create_category(['parent' => $cat2->id]);
        $cat2b = $generator->create_category(['parent' => $cat2->id]);

        $contexts->cat2 = \context_coursecat::instance($cat2->id);
        $contexts->cat2a = \context_coursecat::instance($cat2a->id);
        $contexts->cat2b = \context_coursecat::instance($cat2b->id);

        $cat2course1 = $generator->create_course(['category' => $cat2->id]);
        $cat2course2 = $generator->create_course(['category' => $cat2->id]);
        $cat2acourse1 = $generator->create_course(['category' => $cat2a->id]);
        $cat2acourse2 = $generator->create_course(['category' => $cat2a->id]);
        $cat2bcourse1 = $generator->create_course(['category' => $cat2b->id]);
        $cat2bcourse2 = $generator->create_course(['category' => $cat2b->id]);

        $contexts->cat2course1 = \context_course::instance($cat2course1->id);
        $contexts->cat2acourse1 = \context_course::instance($cat2acourse1->id);
        $contexts->cat2bcourse1 = \context_course::instance($cat2bcourse1->id);
        $contexts->cat2course2 = \context_course::instance($cat2course2->id);
        $contexts->cat2acourse2 = \context_course::instance($cat2acourse2->id);
        $contexts->cat2bcourse2 = \context_course::instance($cat2bcourse2->id);

        $cat2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2->id]);
        $cat2ablock = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2a->id]);
        $cat2bblock = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2b->id]);
        $cat2course1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2course1->id]);
        $cat2course2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2course2->id]);
        $cat2acourse1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2acourse1->id]);
        $cat2acourse2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2acourse2->id]);
        $cat2bcourse1block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2bcourse1->id]);
        $cat2bcourse2block = $generator->create_block('online_users', ['parentcontextid' => $contexts->cat2bcourse2->id]);

        $contexts->cat2block = \context_block::instance($cat2block->id);
        $contexts->cat2ablock = \context_block::instance($cat2ablock->id);
        $contexts->cat2bblock = \context_block::instance($cat2bblock->id);
        $contexts->cat2course1block = \context_block::instance($cat2course1block->id);
        $contexts->cat2course2block = \context_block::instance($cat2course2block->id);
        $contexts->cat2acourse1block = \context_block::instance($cat2acourse1block->id);
        $contexts->cat2acourse2block = \context_block::instance($cat2acourse2block->id);
        $contexts->cat2bcourse1block = \context_block::instance($cat2bcourse1block->id);
        $contexts->cat2bcourse2block = \context_block::instance($cat2bcourse2block->id);

        return $contexts;
    }
}
