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

namespace mod_forum;

/**
 * Tests for the local\container class.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_forum\local\container
 */
class local_container_test extends \advanced_testcase {
    /**
     * Ensure that a renderer factory is returned.
     *
     * @covers ::get_renderer_factory
     */
    public function test_get_renderer_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\renderer::class, \mod_forum\local\container::get_renderer_factory());
    }

    /**
     * Ensure that a legacy_data_mapper_factory factory is returned.
     *
     * @covers ::get_legacy_data_mapper_factory
     */
    public function test_get_legacy_data_mapper_factory() {
        $this->assertInstanceOf(
            \mod_forum\local\factories\legacy_data_mapper::class,
            \mod_forum\local\container::get_legacy_data_mapper_factory()
        );
    }

    /**
     * Ensure that a exporter factory is returned.
     *
     * @covers ::get_exporter_factory
     */
    public function test_get_exporter_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\exporter::class, \mod_forum\local\container::get_exporter_factory());
    }

    /**
     * Ensure that a vault factory is returned.
     *
     * @covers ::get_vault_factory
     */
    public function test_get_vault_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\vault::class, \mod_forum\local\container::get_vault_factory());
    }

    /**
     * Ensure that a manager factory is returned.
     *
     * @covers ::get_manager_factory
     */
    public function test_get_manager_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\manager::class, \mod_forum\local\container::get_manager_factory());
    }

    /**
     * Ensure that a entity factory is returned.
     *
     * @covers ::get_entity_factory
     */
    public function test_get_entity_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\entity::class, \mod_forum\local\container::get_entity_factory());
    }

    /**
     * Ensure that a builder factory is returned.
     *
     * @covers ::get_builder_factory
     */
    public function test_get_builder_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\builder::class, \mod_forum\local\container::get_builder_factory());
    }

    /**
     * Ensure that a url factory is returned.
     *
     * @covers ::get_url_factory
     */
    public function test_get_url_factory() {
        $this->assertInstanceOf(\mod_forum\local\factories\url::class, \mod_forum\local\container::get_url_factory());
    }
}
