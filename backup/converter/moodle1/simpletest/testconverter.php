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
 * Unit tests for the moodle1 converter
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/converter/moodle1/converter.class.php');

class moodle1_converter_test extends UnitTestCase {

    public static $includecoverage = array();

    /** @var string the name of the directory containing the unpacked Moodle 1.9 backup */
    protected $tempdir;

    public function setUp() {
        global $CFG;

        $this->tempdir = convert_helper::generate_id('simpletest');
        check_dir_exists("$CFG->dataroot/temp/backup/$this->tempdir");
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/moodle.xml",
            "$CFG->dataroot/temp/backup/$this->tempdir/moodle.xml"
        );
    }

    public function tearDown() {
        global $CFG;
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete("$CFG->dataroot/temp/backup/$this->tempdir");
        }
    }

    public function test_detect_format() {
        $detected = moodle1_converter::detect_format($this->tempdir);
        $this->assertEqual(backup::FORMAT_MOODLE1, $detected);
    }

    public function test_convert() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $this->assertIsA($converter, 'moodle1_converter');
        $converter->convert();
    }
}
