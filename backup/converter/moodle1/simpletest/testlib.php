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

require_once($CFG->dirroot . '/backup/converter/moodle1/lib.php');

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

    public function test_convert_factory() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $this->assertIsA($converter, 'moodle1_converter');
    }

    public function test_stash_storage_not_created() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $this->expectException('moodle1_convert_storage_exception');
        $converter->set_stash('tempinfo', 12);
    }

    public function test_stash_requiring_empty_stash() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $converter->set_stash('tempinfo', 12);
        $this->expectException('moodle1_convert_empty_storage_exception');
        try {
            $converter->get_stash('anothertempinfo');

        } catch (moodle1_convert_empty_storage_exception $e) {
            // we must drop the storage here so we are able to re-create it in the next test
            $converter->drop_stash_storage();
            throw new moodle1_convert_empty_storage_exception('rethrowing');
        }
    }

    public function test_stash_storage() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();

        // test stashes without itemid
        $converter->set_stash('tempinfo1', 12);
        $converter->set_stash('tempinfo2', array('a' => 2, 'b' => 3));
        $this->assertIdentical(12, $converter->get_stash('tempinfo1'));
        $this->assertIdentical(array('a' => 2, 'b' => 3), $converter->get_stash('tempinfo2'));

        // overwriting a stashed value is allowed
        $converter->set_stash('tempinfo1', '13');
        $this->assertNotIdentical(13, $converter->get_stash('tempinfo1'));
        $this->assertIdentical('13', $converter->get_stash('tempinfo1'));

        // repeated reading is allowed
        $this->assertIdentical('13', $converter->get_stash('tempinfo1'));

        // test stashes with itemid
        $converter->set_stash('tempinfo', 'Hello', 1);
        $converter->set_stash('tempinfo', 'World', 2);
        $this->assertIdentical('Hello', $converter->get_stash('tempinfo', 1));
        $this->assertIdentical('World', $converter->get_stash('tempinfo', 2));

        $converter->drop_stash_storage();
    }

    public function test_get_contextid() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);

        // stash storage must be created in advance
        $converter->create_stash_storage();

        // ids are generated on the first call
        $id1 = $converter->get_contextid(CONTEXT_COURSE, 10);
        $id2 = $converter->get_contextid(CONTEXT_COURSE, 11);
        $id3 = $converter->get_contextid(CONTEXT_MODULE, 10);

        $this->assertNotEqual($id1, $id2);
        $this->assertNotEqual($id1, $id3);
        $this->assertNotEqual($id2, $id3);

        // and then re-used if called with the same params
        $this->assertEqual($id1, $converter->get_contextid(CONTEXT_COURSE, 10));
        $this->assertEqual($id2, $converter->get_contextid(CONTEXT_COURSE, 11));
        $this->assertEqual($id3, $converter->get_contextid(CONTEXT_MODULE, 10));

        $converter->drop_stash_storage();
    }

    public function test_convert_run_convert() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $converter->convert();
    }
}
