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
 * PHPUnit block_dataformnotification generator tests.
 *
 * @package    block_dataformnotification
 * @category   phpunit
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * PHPUnit block_dataformnotification generator testcase.
 *
 * @package    block_dataformnotification
 * @category   phpunit
 * @group      block_dataformnotification
 * @group      mod_dataform
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_dataformnotification_generator_testcase extends advanced_testcase {

    public function test_generator() {
        global $DB;

        $this->resetAfterTest(true);

        $beforeblocks = $DB->count_records('block_instances');
        $beforecontexts = $DB->count_records('context');

        // Get the generator.
        $generator = $this->getDataGenerator()->get_plugin_generator('block_dataformnotification');
        $this->assertInstanceOf('block_dataformnotification_generator', $generator);
        $this->assertEquals('dataformnotification', $generator->get_blockname());

        $generator->create_instance();
        $generator->create_instance();
        $bi = $generator->create_instance();
        $this->assertEquals($beforeblocks + 3, $DB->count_records('block_instances'));
        $this->assertEquals($beforecontexts + 3, $DB->count_records('context'));
    }
}
