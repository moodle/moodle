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
 * Unit test for the filter_tex
 *
 * @package    filter_tex
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/tex/filter.php');


/**
 * Unit tests for filter_tex.
 *
 * Test the delimiter parsing used by the tex filter.
 *
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_tex_testcase extends advanced_testcase {

    protected $filter;

    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->filter = new filter_tex(context_system::instance(), array());
    }

    function run_with_delimiters($start, $end, $filtershouldrun) {
        $pre = 'Some pre text';
        $post = 'Some post text';
        $equation = ' \sum{a^b} ';

        $before = $pre . $start . $equation . $end . $post;

        $after = trim($this->filter->filter($before));

        if ($filtershouldrun) {
            $this->assertNotEquals($after, $before);
        } else {
            $this->assertEquals($after, $before);
        }
    }

    function test_delimiters() {
        // First test the list of supported delimiters.
        $this->run_with_delimiters('$$', '$$', true);
        $this->run_with_delimiters('\\(', '\\)', true);
        $this->run_with_delimiters('\\[', '\\]', true);
        $this->run_with_delimiters('[tex]', '[/tex]', true);
        $this->run_with_delimiters('<tex>', '</tex>', true);
        $this->run_with_delimiters('<tex alt="nonsense">', '</tex>', true);
        // Now test some cases that shouldn't be executed.
        $this->run_with_delimiters('<textarea>', '</textarea>', false);
        $this->run_with_delimiters('$', '$', false);
        $this->run_with_delimiters('(', ')', false);
        $this->run_with_delimiters('[', ']', false);
        $this->run_with_delimiters('$$', '\\]', false);
    }

}
