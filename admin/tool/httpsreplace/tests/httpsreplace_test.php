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
 * HTTPS find and replace Tests
 *
 * @package   tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_httpsreplace\tests;


defined('MOODLE_INTERNAL') || die();

class httpsreplace_test extends \advanced_testcase {

    public function test_find_and_replace() {
        global $DB;

        $this->resetAfterTest();
        $this->expectOutputRegex("/UPDATE/");

        $finder = new \tool_httpsreplace\url_finder();
        $results = $finder->http_link_stats();
        $this->assertEmpty($results);

        $generator = $this->getDataGenerator();
        $imglink1 = '<img src="http://intentionally.unavailable/link1.jpg">';
        $course1 = $generator->create_course((object) [
            'summary' => $imglink1,
        ]);

        $imglink2 = '<img src="http://intentionally.unavailable/link2.gif">';
        $course2 = $generator->create_course((object) [
            'summary' => $imglink2,
        ]);

        $imglink3 = '<img src="http://other.unavailable/link3.svg">';
        $course3 = $generator->create_course((object) [
            'summary' => $imglink1.$imglink2.$imglink3,
        ]);

        $kaltura = '<script src="http://cdnapi.kaltura.com/p/730212/sp/73021200/embedIframeJs">';
        $course4 = $generator->create_course((object) [
            'summary' => $kaltura,
        ]);

        $rackcdn = '<iframe src="http://fe8be92ac963979368eca.r38.cf1.rackcdn.com/Helpful_ET_Websites_Apps_Resources.pdf">';
        $course5 = $generator->create_course((object) [
            'summary' => $rackcdn,
        ]);

        $results = $finder->http_link_stats();
        $this->assertCount(2, $results);
        $this->assertEquals(4, $results['intentionally.unavailable']);
        $this->assertEquals(1, $results['other.unavailable']);

        $finder->upgrade_http_links();

        $results = $finder->http_link_stats();
        $this->assertEmpty($results);

        $summary1 = $DB->get_field('course', 'summary', ['id' => $course1->id]);
        $this->assertContains('https://intentionally.unavailable', $summary1);
        $this->assertNotContains('http://intentionally.unavailable', $summary1);

        $summary2 = $DB->get_field('course', 'summary', ['id' => $course2->id]);
        $this->assertContains('https://intentionally.unavailable', $summary2);
        $this->assertNotContains('http://intentionally.unavailable', $summary2);

        $summary3 = $DB->get_field('course', 'summary', ['id' => $course3->id]);
        $this->assertContains('https://other.unavailable', $summary3);
        $this->assertContains('https://intentionally.unavailable', $summary3);
        $this->assertNotContains('http://intentionally.unavailable', $summary3);

        $summary4 = $DB->get_field('course', 'summary', ['id' => $course4->id]);
        $this->assertContains('https://cdnapisec.kaltura.com', $summary4);

        $summary5 = $DB->get_field('course', 'summary', ['id' => $course5->id]);
        $expected = "https://fe8be92ac963979368eca.ssl.cf1.rackcdn.com/Helpful_ET_Websites_Apps_Resources.pdf";
        $this->assertContains($expected, $summary5);
    }

    public function test_upgrade_http_links_excluded_tables() {
        $this->resetAfterTest();

        set_config('test_upgrade_http_links', '<img src="http://somesite/someimage.png" />');

        $finder = new \tool_httpsreplace\url_finder();
        ob_start();
        $results = $finder->upgrade_http_links();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertTrue($results);
        $this->assertNotContains('https://somesite', $output);
        $testconf = get_config('core', 'test_upgrade_http_links');
        $this->assertContains('http://somesite', $testconf);
        $this->assertNotContains('https://somesite', $testconf);
    }

}
