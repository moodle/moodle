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
 * Unit tests for get_entity_generator web service
 *
 * @package   tool_behat
 * @copyright 2022 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_behat\external;

/**
 * Tests for get_entity_generator web service
 *
 * @covers \tool_behat\external\get_entity_generator
 */
class get_entity_generator_test extends \advanced_testcase {

    /**
     * Log in as admin
     *
     * @return void
     */
    public function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Get the generator for a core entity.
     *
     * @return void
     */
    public function test_execute_core_entity(): void {
        $generator = get_entity_generator::execute('users');
        $this->assertEquals(['required' => ['username']], $generator);
    }

    /**
     * Get the generator for the plugin entity.
     *
     * @return void
     */
    public function test_execute_plugin_entity(): void {
        $generator = get_entity_generator::execute('mod_book > chapters');
        $this->assertEquals(['required' => ['book', 'title', 'content']], $generator);
    }

    /**
     * Get the generator for an entity with no required fields.
     *
     * @return void
     */
    public function test_execute_no_requried(): void {
        $generator = get_entity_generator::execute('mod_forum > posts');
        $this->assertEquals(['required' => []], $generator);
    }

    /**
     * Attempt to get the generator for a core entity that does not exist.
     *
     * @return void
     */
    public function test_execute_invalid_entity(): void {
        $this->expectException('coding_exception');
        get_entity_generator::execute('foo');
    }

    /**
     * Attempt to get a generator form a plugin that does not exist.
     *
     * @return void
     */
    public function test_execute_invalid_plugin(): void {
        $this->expectException('coding_exception');
        get_entity_generator::execute('foo > bar');
    }

    /**
     * Attempt to get a generator for an entity that does not exist, from a plugin that does.
     *
     * @return void
     */
    public function test_execute_invalid_plugin_entity(): void {
        $this->expectException('coding_exception');
        get_entity_generator::execute('mod_book > bar');
    }
}
