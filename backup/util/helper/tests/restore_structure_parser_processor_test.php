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
 * Tests for restore_structure_parser_processor class.
 *
 * @package     core_backup
 * @category    test
 * @copyright   2017 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_structure_parser_processor.class.php');

/**
 * Tests for restore_structure_parser_processor class.
 *
 * @package core_backup
 * @copyright 2017 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_structure_parser_processor_test extends advanced_testcase {

    /**
     * Initial set up.
     */
    public function setUp(): void {
        parent::setUp();

        $this->resetAfterTest(true);
    }

    /**
     * Data provider for ::test_process_cdata.
     *
     * @return array
     */
    public function process_cdata_data_provider() {
        return array(
            array(null, null, true),
            array("$@NULL@$", null, true),
            array("$@NULL@$ ", "$@NULL@$ ", true),
            array(1, 1, true),
            array(" ", " ", true),
            array("1", "1", true),
            array("$@FILEPHP@$1.jpg", "$@FILEPHP@$1.jpg", true),
            array(
                "http://test.test/$@SLASH@$",
                "http://test.test/$@SLASH@$",
                true
            ),
            array(
                "<a href='$@FILEPHP@$1.jpg'>Image</a>",
                "<a href='http://test.test/file.php/11.jpg'>Image</a>",
                true
            ),
            array(
                "<a href='$@FILEPHP@$$@SLASH@$1.jpg'>Image</a>",
                "<a href='http://test.test/file.php/1/1.jpg'>Image</a>",
                true
            ),
            array(
                "<a href='$@FILEPHP@$$@SLASH@$$@SLASH@$1.jpg'>Image</a>",
                "<a href='http://test.test/file.php/1//1.jpg'>Image</a>",
                true
            ),
            array(
                "<a href='$@FILEPHP@$1.jpg'>Image</a>",
                "<a href='http://test.test/file.php?file=%2F11.jpg'>Image</a>",
                false
            ),
            array(
                "<a href='$@FILEPHP@$$@SLASH@$1.jpg'>Image</a>",
                "<a href='http://test.test/file.php?file=%2F1%2F1.jpg'>Image</a>",
                false
            ),
            array(
                "<a href='$@FILEPHP@$$@SLASH@$$@SLASH@$1.jpg'>Image</a>",
                "<a href='http://test.test/file.php?file=%2F1%2F%2F1.jpg'>Image</a>",
                false
            ),
            array(
                "<a href='$@FILEPHP@$$@SLASH@$1.jpg$@FORCEDOWNLOAD@$'>Image</a>",
                "<a href='http://test.test/file.php/1/1.jpg?forcedownload=1'>Image</a>",
                true
            ),
            array(
                "<a href='$@FILEPHP@$$@SLASH@$1.jpg$@FORCEDOWNLOAD@$'>Image</a>",
                "<a href='http://test.test/file.php?file=%2F1%2F1.jpg&amp;forcedownload=1'>Image</a>",
                false
            ),
            array(
                "<iframe src='$@H5PEMBED@$?url=testurl'></iframe>",
                "<iframe src='http://test.test/h5p/embed.php?url=testurl'></iframe>",
                true
            ),
        );
    }

    /**
     * Test that restore_structure_parser_processor replaces $@FILEPHP@$ to correct file php links.
     *
     * @dataProvider process_cdata_data_provider
     * @param string $content Testing content.
     * @param string $expected Expected result.
     * @param bool $slasharguments A value for $CFG->slasharguments setting.
     */
    public function test_process_cdata($content, $expected, $slasharguments) {
        global $CFG;

        $CFG->slasharguments = $slasharguments;
        $CFG->wwwroot = 'http://test.test';

        $processor = new restore_structure_parser_processor(1, 1);

        $this->assertEquals($expected, $processor->process_cdata($content));
    }

}
