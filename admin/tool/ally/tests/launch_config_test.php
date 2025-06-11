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
 * Test launch config class.
 * @author    Guy Thomas <dev@citri.city>
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\lti\launch_config;

/**
 * @group     tool_ally
 * @group     ally
 */
class launch_config_test extends \advanced_testcase {
    public function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot.'/mod/lti/locallib.php');
    }
    public function test_not_configured_nothing() {
        $this->expectExceptionMessage(get_string('notconfigured', 'report_allylti'));
        new launch_config((object) [], (object) []);
    }

    public function test_not_configured_partial_adminurl() {
        $this->expectExceptionMessage(get_string('notconfigured', 'report_allylti'));
        new launch_config((object) [
            'adminurl' => 'http://someurl.test'
        ], (object) []);
    }

    public function test_not_configured_partial_adminurl_secret() {
        $this->expectExceptionMessage(get_string('notconfigured', 'report_allylti'));
        new launch_config((object) [
            'adminurl' => 'http://someurl.test',
            'key'      => 'somekey'
        ], (object) []);
    }

    public function test_configured() {
        $key = 'somekey';
        $secret = 'somesecret';
        $url = 'http://someurl.test';
        $lc = new launch_config((object) [
            'adminurl' => $url,
            'key'      => $key,
            'secret'   => $secret
        ], (object) []);

        $this->assertNotEmpty($lc);
        $this->assertEquals($url, $lc->get_url());
        $this->assertEquals($key, $lc->get_key());
        $this->assertEquals($secret, $lc->get_secret());
    }
}
