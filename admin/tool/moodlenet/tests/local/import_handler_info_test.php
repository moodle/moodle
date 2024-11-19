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

namespace tool_moodlenet\local;

use tool_moodlenet\local\import_handler_info;
use tool_moodlenet\local\import_strategy;
use tool_moodlenet\local\import_strategy_file;

/**
 * Class tool_moodlenet_import_handler_info_testcase, providing test cases for the import_handler_info class.
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_handler_info_test extends \advanced_testcase {

    /**
     * Test init and the getters.
     *
     * @dataProvider handler_info_data_provider
     * @param string $modname the name of the mod.
     * @param string $description description of the mod.
     * @param bool $expectexception whether we expect an exception during init or not.
     */
    public function test_initialisation($modname, $description, $expectexception): void {
        $this->resetAfterTest();
        // Skip those cases we cannot init.
        if ($expectexception) {
            $this->expectException(\coding_exception::class);
            $handlerinfo = new import_handler_info($modname, $description, new import_strategy_file());
        }

        $handlerinfo = new import_handler_info($modname, $description, new import_strategy_file());

        $this->assertEquals($modname, $handlerinfo->get_module_name());
        $this->assertEquals($description, $handlerinfo->get_description());
        $this->assertInstanceOf(import_strategy::class, $handlerinfo->get_strategy());
    }


    /**
     * Data provider for creation of import_handler_info objects.
     *
     * @return array the data for creation of the info object.
     */
    public static function handler_info_data_provider(): array {
        return [
            'All data present' => ['label', 'Add a label to the course', false],
            'Empty module name' => ['', 'Add a file resource to the course', true],
            'Empty description' => ['resource', '', true],

        ];
    }
}
