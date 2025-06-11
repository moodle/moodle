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

namespace tool_ally;

use tool_ally\adminsetting\ally_trim;

/**
 * @package   tool_ally
 * @author    Guy Thomas <dev@citri.city>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminsetting_ally_trim_test extends \advanced_testcase {
    /**
     * Test settings are trimmed.
     */
    public function test_trim() {
        $this->resetAfterTest();
        $text = '    This should be trimmed    ';
        $setting = new ally_trim('tool_ally/testtrim', new \lang_string('key', 'tool_ally'),
            new \lang_string('keydesc', 'tool_ally'), '', PARAM_TEXT);
        $setting->write_setting($text);
        $testtrimtext = get_config('tool_ally', 'testtrim');
        $this->assertEquals(trim($text), $testtrimtext);
    }
}
