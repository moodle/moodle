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
 * PHPUnit tests for the renderer class.
 *
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/adaptivequiz/locallib.php');
require_once($CFG->dirroot.'/mod/adaptivequiz/renderer.php');
require_once($CFG->dirroot.'/tag/lib.php');

use advanced_testcase;
use mod_adaptivequiz_renderer;
use moodle_page;
use moodle_url;
use stdClass;

/**
 * @group mod_adaptivequiz
 * @covers \mod_adaptivequiz_renderer
 */
class renderer_test extends advanced_testcase {

    /**
     * This function tests the output from the get_js_module.
     */
    public function test_adaptivequiz_get_js_module(): void {
        $dummypage = new moodle_page();
        $target = 'mod_adaptivequiz';
        $renderer = new mod_adaptivequiz_renderer($dummypage, $target);
        $output = $renderer->adaptivequiz_get_js_module();

        $this->assertArrayHasKey('name', $output);
        $this->assertContains('mod_adaptivequiz', $output);
        $this->assertArrayHasKey('fullpath', $output);
        $this->assertContains('/mod/adaptivequiz/module.js', $output);
        $this->assertArrayHasKey('requires', $output);
        $this->assertEquals(
            ['base', 'dom', 'event-delegate', 'event-key', 'core_question_engine', 'moodle-core-formchangechecker'],
            $output['requires']
        );
        $this->assertArrayHasKey('strings', $output);
    }

    /**
     * This function tests how init_metadata() handlss an integer
     */
    public function test_init_metadata_with_integer() {
        $dummypage = new moodle_page();
        $target = 'mod_adaptivequiz';
        $renderer = new mod_adaptivequiz_renderer($dummypage, $target);

        $mockquba = $this->createPartialMock('question_usage_by_activity', ['render_question_head_html']);
        $mockquba->expects($this->once())
            ->method('render_question_head_html')
            ->willReturn('');

        // Only testing that the mock object's method is called once.
        $renderer->init_metadata($mockquba, 1);
    }

    /**
     * This function tests the output from print_form_and_button()
     */
    public function test_print_form_and_button() {
        $dummypage = new moodle_page();
        $target = 'mod_adaptivequiz';
        $renderer = new mod_adaptivequiz_renderer($dummypage, $target);
        $url = new moodle_url('/test/phpunittest/test.php', array('cmid' => 99));
        $text = 'phpunit test button';

        $output = $renderer->print_form_and_button($url, $text);
        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('<input', $output);
        $this->assertStringContainsString('type="submit"', $output);
        $this->assertStringContainsString('/test/phpunittest/test.php', $output);
        $this->assertStringContainsString('cmid=99', $output);
        $this->assertStringContainsString('phpunit test button', $output);
        $this->assertStringContainsString('<center>', $output);
        $this->assertStringContainsString('</center>', $output);
        $this->assertStringContainsString('</form>', $output);
    }

    /**
     * This function tests the output from format_report_table_headers()
     */
    public function test_format_report_table_headers() {
        $dummypage = new moodle_page();
        $target = 'mod_adaptivequiz';
        $renderer = new mod_adaptivequiz_renderer($dummypage, $target);
        $dummycm = new stdClass();
        $dummycm->id = 99;

        $output = $renderer->format_report_table_headers($dummycm, 'stderror', 'ASC');
        $this->assertEquals(6, count($output));
        $this->assertStringContainsString('/mod/adaptivequiz/viewreport.php', $output[0]);
        $this->assertStringContainsString('sort=firstname&amp;sortdir=ASC', $output[0]);
        $this->assertStringContainsString('sort=lastname&amp;sortdir=ASC', $output[0]);
        $this->assertStringContainsString('/mod/adaptivequiz/viewreport.php', $output[1]);
        $this->assertStringContainsString('sort=email&amp;sortdir=ASC', $output[1]);
        $this->assertStringContainsString('/mod/adaptivequiz/viewreport.php', $output[2]);
        $this->assertStringContainsString('sort=attempts&amp;sortdir=ASC', $output[2]);
        $this->assertStringContainsString('/mod/adaptivequiz/viewreport.php', $output[3]);
        $this->assertStringContainsString('sort=measure&amp;sortdir=ASC', $output[3]);
        $this->assertStringContainsString('/mod/adaptivequiz/viewreport.php', $output[4]);
        $this->assertStringContainsString('sort=stderror&amp;sortdir=DESC', $output[4]);
        $this->assertStringContainsString('/mod/adaptivequiz/viewreport.php', $output[5]);
        $this->assertStringContainsString('sort=timemodified&amp;sortdir=ASC', $output[5]);
    }
}
