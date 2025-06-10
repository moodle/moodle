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
 * Test for version_info webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\webservice\version_info;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Test for version_info webservice.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_version_info_test extends abstract_testcase {
    /**
     * Test the web service.
     */
    public function test_service() {
        $this->resetAfterTest();

        $info = (object) version_info::service();

        $this->assertNotEmpty($info->moodle);
        $this->assertNotEmpty($info->moodle->version);
        $this->assertNotEmpty($info->moodle->release);
        $this->assertNotEmpty($info->moodle->branch);

        $this->assertNotEmpty($info->tool_ally);
        $this->assertNotEmpty($info->tool_ally->version);
        $this->assertNotEmpty($info->tool_ally->requires);
        $this->assertNotEmpty($info->tool_ally->release);

        // We do not test anything else with the filter as we have no control over it's state.
        $this->assertNotEmpty($info->filter_ally);

        // We do not test anything else with the report as we have no control over it's state.
        $this->assertNotEmpty($info->report_allylti);
    }

    public function test_warn_on_site_policy_not_accepted() {
        $this->resetAfterTest();
        global $DB, $CFG;
        set_config('sitepolicy', 'sitepolicyURL.com');
        set_config('sitepolicyguest', 'sitepolicyURLguest.com');

        try {
            version_info::service();
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\moodle_exception::class, $e);
            $this->assertEquals($e->errorcode, 'sitepolicynotagreed');
            $this->assertEquals($e->module, 'error');
            $this->assertEquals($e->a, 'sitepolicyURL.com');
        }

        $guest = $DB->get_record('user', array('id' => $CFG->siteguest));
        $this->setUser($guest);

        try {
            version_info::service();
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\moodle_exception::class, $e);
            $this->assertEquals($e->errorcode, 'sitepolicynotagreed');
            $this->assertEquals($e->module, 'error');
            $this->assertEquals($e->a, 'sitepolicyURLguest.com');
        }
    }
}
