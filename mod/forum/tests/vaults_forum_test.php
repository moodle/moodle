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
 * The forum vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The forum vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_vaults_forum_testcase extends advanced_testcase {
    /** @var \mod_forum\local\vaults\discussion */
    private $vault;

    /**
     * Set up function for tests.
     */
    public function setUp() {
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $this->vault = $vaultfactory->get_forum_vault();
    }

    /**
     * Test get_from_id.
     */
    public function test_get_from_id() {
        $this->resetAfterTest();

        $vault = $this->vault;
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $entity = $vault->get_from_id($forum->id);

        $this->assertEquals($forum->id, $entity->get_id());
    }

    /**
     * Test get_from_course_module_id.
     */
    public function test_get_from_course_module_id() {
        $this->resetAfterTest();

        $vault = $this->vault;
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule1 = get_coursemodule_from_instance('forum', $forum1->id);
        $coursemodule2 = get_coursemodule_from_instance('forum', $forum2->id);

        // Don't exist.
        $entity = $vault->get_from_course_module_id($coursemodule1->id + 100);
        $this->assertEquals(null, $entity);

        $entity = $vault->get_from_course_module_id($coursemodule1->id);
        $this->assertEquals($forum1->id, $entity->get_id());
    }

    /**
     * Test get_from_course_module_ids.
     */
    public function test_get_from_course_module_ids() {
        $this->resetAfterTest();

        $vault = $this->vault;
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule1 = get_coursemodule_from_instance('forum', $forum1->id);
        $coursemodule2 = get_coursemodule_from_instance('forum', $forum2->id);

        // Don't exist.
        $entities = array_values($vault->get_from_course_module_ids([$coursemodule1->id + 100, $coursemodule1->id + 200]));
        $this->assertEquals([], $entities);

        $entities = array_values($vault->get_from_course_module_ids([$coursemodule1->id, $coursemodule2->id]));
        $this->assertCount(2, $entities);
        $this->assertEquals($forum2->id, $entities[0]->get_id());
        $this->assertEquals($forum1->id, $entities[1]->get_id());

        $entities = array_values($vault->get_from_course_module_ids([$coursemodule1->id]));
        $this->assertCount(1, $entities);
        $this->assertEquals($forum1->id, $entities[0]->get_id());
    }
}
