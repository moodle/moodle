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
 * Skype icons filter phpunit tests
 *
 * @package    filter_emoticon
 * @category   test
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/emoticon/filter.php'); // Include the code to test.

/**
 * Skype icons filter testcase.
 */
class filter_emoticon_testcase extends advanced_testcase {

    /**
     * Verify configured target formats are observed. Just that.
     */
    public function test_filter_emoticon_formats() {

        $this->resetAfterTest(true); // We are modifying the config.

        $filter = new testable_filter_emoticon();

        // Verify texts not matching target formats aren't filtered.
        $expected = '(grr)';
        $options = array('originalformat' => FORMAT_MOODLE); // Only FORMAT_HTML is filtered, see {@link testable_filter_emoticon}.
        $this->assertEquals($expected, $filter->filter('(grr)', $options));

        $options = array('originalformat' => FORMAT_MARKDOWN); // Only FORMAT_HTML is filtered, see {@link testable_filter_emoticon}.
        $this->assertEquals($expected, $filter->filter('(grr)', $options));

        $options = array('originalformat' => FORMAT_PLAIN); // Only FORMAT_HTML is filtered, see {@link testable_filter_emoticon}.
        $this->assertEquals($expected, $filter->filter('(grr)', $options));

        // And texts matching target formats are filtered.
        $expected = '<img class="icon emoticon" alt="angry" title="angry" src="https://www.example.com/moodle/theme/image.php/_s/boost/core/1/s/angry" />';
        $options = array('originalformat' => FORMAT_HTML); // Only FORMAT_HTML is filtered, see {@link testable_filter_emoticon}.
        $this->assertEquals($expected, $filter->filter('(grr)', $options));
    }
}

/**
 * Subclass for easier testing.
 */
class testable_filter_emoticon extends filter_emoticon {
    public function __construct() {
        // Use this context for filtering.
        $this->context = context_system::instance();
        // Define FORMAT_HTML as only one filtering in DB.
        set_config('formats', implode(',', array(FORMAT_HTML)), 'filter_emoticon');
    }
}
