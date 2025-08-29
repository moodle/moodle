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

namespace core_search\external;

use core_external\external_api;

/**
 * Tests for the get_search_areas_list external function.
 *
 * @package    core_search
 * @category   test
 * @copyright  2023 Juan Leyva (juan@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_search\external\get_search_areas_list
 */
final class get_search_areas_list_test extends \core_external\tests\externallib_testcase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * test external api
     *
     * @covers ::execute
     * @return void
     */
    public function test_external_get_search_areas_list(): void {

        set_config('enableglobalsearch', true);
        set_config('searchenablecategories', true);

        $this->setAdminUser();

        $result = get_search_areas_list::execute();
        $result = external_api::clean_returnvalue(get_search_areas_list::execute_returns(), $result);

        $this->assertNotEmpty($result['areas']);
        $totalareas = count($result['areas']);

        // Filter.
        $result = get_search_areas_list::execute('core-users');
        $result = external_api::clean_returnvalue(get_search_areas_list::execute_returns(), $result);
        $totalfilterareas = count($result['areas']);

        // Just count numbers, plugins can inject areas.
        $this->assertLessThan($totalareas, $totalfilterareas);
    }
}
