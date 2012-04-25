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
 * Unit tests for /lib/filestorage/file_storage.php
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

class filestoragelib_testcase extends advanced_testcase {

    /**
     * Local files can be added to the filepool
     */
    public function test_create_file_from_pathname() {
        global $CFG;

        $this->resetAfterTest(false);

        $filepath = $CFG->dirroot.'/lib/filestorage/tests/fixtures/testimage.jpg';
        $syscontext = context_system::instance();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/images/',
            'filename'  => 'testimage.jpg',
        );

        $fs = get_file_storage();
        $fs->create_file_from_pathname($filerecord, $filepath);

        $this->assertTrue($fs->file_exists($syscontext->id, 'core', 'unittest', 0, '/images/', 'testimage.jpg'));

        return $fs->get_file($syscontext->id, 'core', 'unittest', 0, '/images/', 'testimage.jpg');
    }

    /**
     * Local images can be added to the filepool and their preview can be obtained
     *
     * @depends test_create_file_from_pathname
     */
    public function test_get_file_preview(stored_file $file) {
        global $CFG;

        $this->resetAfterTest(true);
        $fs = get_file_storage();

        $previewtinyicon = $fs->get_file_preview($file, 'tinyicon');
        $this->assertInstanceOf('stored_file', $previewtinyicon);
        $this->assertEquals('6b9864ae1536a8eeef54e097319175a8be12f07c', $previewtinyicon->get_filename());

        $previewtinyicon = $fs->get_file_preview($file, 'thumb');
        $this->assertInstanceOf('stored_file', $previewtinyicon);
        $this->assertEquals('6b9864ae1536a8eeef54e097319175a8be12f07c', $previewtinyicon->get_filename());
    }
}
