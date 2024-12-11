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

namespace core_calendar;

use core_calendar\local\event\proxies\coursecat_proxy;

/**
 * coursecat_proxy testcase.
 *
 * @package     core_calendar
 * @copyright   2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class coursecat_proxy_test extends \advanced_testcase {

    public function test_valid_coursecat(): void {
        global $DB;
        $this->resetAfterTest();

        $name = '2027-2028 Academic Year';
        $generator = $this->getDataGenerator();
        $category = $generator->create_category([
                'name' => $name,
            ]);
        \cache_helper::purge_by_event('changesincoursecat');

        // Fetch the proxy.
        $startreads = $DB->perf_get_reads();
        $proxy = new coursecat_proxy($category->id);
        $this->assertInstanceOf(coursecat_proxy::class, $proxy);
        $this->assertEquals(0, $DB->perf_get_reads() - $startreads);

        // Fetch the ID - this is known and doesn't require a cache read.
        $this->assertEquals($category->id, $proxy->get('id'));
        $this->assertEquals(0, $DB->perf_get_reads() - $startreads);

        // Fetch the name - not known, and requires a read.
        $this->assertEquals($name, $proxy->get('name'));
        $this->assertEquals(1, $DB->perf_get_reads() - $startreads);

        $this->assertInstanceOf('core_course_category', $proxy->get_proxied_instance());
    }
}
