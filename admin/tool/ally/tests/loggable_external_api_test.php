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
 * Test for loggable_external_api.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

use tool_ally\abstract_testcase;
use tool_ally\webservice\log;
use tool_ally\webservice\version_info;
use Psr\Log\LogLevel;

defined('MOODLE_INTERNAL') || die();

/**
 * Test for loggable_external_api.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class loggable_external_api_test extends abstract_testcase {
    protected function setUp(): void {
        parent::setUp();
        global $CFG;
        require_once($CFG->dirroot.'/lib/externallib.php');
    }

    public function test_service_version_failure_logged() {
        $this->resetAfterTest();

        set_config('sitepolicy', 'sitepolicyURL.com');
        set_config('sitepolicyguest', 'sitepolicyURLguest.com');

        try {
            version_info::service();
        } catch (\Exception $e) {
            $this->setAdminUser();
            $logentries = log::service(null);
            $this->assertCount(1, $logentries['data']);
            $this->assertEquals('logger:servicefailure', $logentries['data'][0]->code);
            $this->assertEquals(LogLevel::ERROR, $logentries['data'][0]->level);
            $this->assertEquals(get_string('logger:servicefailure_exp', 'tool_ally', (object)[
                'class' => version_info::class,
                'params' => var_export([], true)
            ]), $logentries['data'][0]->explanation);
        }
    }
}
