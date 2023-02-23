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

declare(strict_types=1);

namespace core\external;

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/lib/tests/fixtures/testeable_dynamic_tab.php');

/**
 * Unit tests external dynamic tabs get content
 *
 * @package     core
 * @covers      \core\external\dynamic_tabs_get_content
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dynamic_tabs_get_content_test extends \externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $result = dynamic_tabs_get_content::execute(testeable_dynamic_tab::class, json_encode([]));
        $result = external_api::clean_returnvalue(dynamic_tabs_get_content::execute_returns(), $result);
        $this->assertEquals('templates/tabs/mytab', $result['template']);
        $this->assertEquals(json_encode(['content' => get_string('content')]), $result['content']);
        $this->assertNotEmpty($result['javascript']);
        $this->assertStringStartsWith('<script>', $result['javascript']);
    }
}
