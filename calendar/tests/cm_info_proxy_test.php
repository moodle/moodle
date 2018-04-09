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
 * cm_info_proxy tests.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\proxies\cm_info_proxy;

/**
 * cm_info_proxy testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_cm_info_proxy_testcase extends advanced_testcase {

    /**
     * Test creating cm_info_std_proxy, using getter and setter.
     */
    public function test_proxy() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('forum',
            ['course' => $course->id, 'idnumber' => '123456']);
        $proxy = new cm_info_proxy(
            'forum',
            $module->id,
            $course->id
        );

        $this->assertEquals('forum', $proxy->get('modname'));
        $this->assertEquals($module->id, $proxy->get('instance'));
        $this->assertEquals($course->id, $proxy->get('course'));
        $this->assertEquals('123456', $proxy->get('idnumber'));
        $this->assertEquals($module->cmid, $proxy->get('id'));
        $this->assertEquals('123456', $proxy->get_proxied_instance()->idnumber);
    }
}
