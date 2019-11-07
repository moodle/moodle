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
 * Core h5p external functions tests.
 *
 * @package    core_h5p
 * @category   external
 * @copyright  2019 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.8
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_h5p\external;
use core_h5p\file_storage;
use core_h5p\autoloader;

/**
 * Core h5p external functions tests
 *
 * @package    core_h5p
 * @category   external
 * @copyright  2019 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.8
 */
class core_h5p_external_testcase extends externallib_advanced_testcase {

    protected function setUp() {
        parent::setUp();
        autoloader::register();
    }

    /**
     * test_get_trusted_h5p_file description
     */
    public function test_get_trusted_h5p_file() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // This is a valid .H5P file.
        $filename = 'find-the-words.h5p';
        $path = __DIR__ . '/fixtures/'.$filename;
        $syscontext = \context_system::instance();
        $filerecord = [
            'contextid' => $syscontext->id,
            'component' => \core_h5p\file_storage::COMPONENT,
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        // Load the h5p file into DB.
        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname($filerecord, $path);
        // Make the URL to pass to the WS.
        $url  = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            0,
            '/',
            $filename
        );
        // Call the WS.
        $result = external::get_trusted_h5p_file($url->out(), 0, 0, 0, 0);
        $result = external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on files and none on warnings.
        $this->assertCount(1, $result['files']);
        $this->assertCount(0, $result['warnings']);
        // Get the export file in the DB to compare with the ws's results.
        $fileh5p = $this->get_export_file($filename, $file->get_pathnamehash());
        $fileh5purl  = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            '',
            '',
            $fileh5p->get_filename()
        );
        $this->assertEquals($fileh5p->get_filepath(), $result['files'][0]['filepath']);
        $this->assertEquals($fileh5p->get_mimetype(), $result['files'][0]['mimetype']);
        $this->assertEquals($fileh5p->get_filesize(), $result['files'][0]['filesize']);
        $this->assertEquals($fileh5p->get_timemodified(), $result['files'][0]['timemodified']);
        $this->assertEquals($fileh5p->get_filename(), $result['files'][0]['filename']);
        $this->assertEquals($fileh5purl->out(), $result['files'][0]['fileurl']);
    }

    /**
     * test_h5p_invalid_url description
     */
    public function test_h5p_invalid_url() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create an empty url.
        $urlempty = '';
        $result = external::get_trusted_h5p_file($urlempty, 0, 0, 0, 0);
        $result = external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on warnings and none on files.
        $this->assertCount(0, $result['files']);
        $this->assertCount(1, $result['warnings']);
        // Check the warnings to be sure that h5pinvalidurl is the message by moodle_exception.
        $this->assertEquals($urlempty, $result['warnings'][0]['item']);
        $this->assertEquals(get_string('h5pinvalidurl', 'core_h5p'), $result['warnings'][0]['message']);

        // Create a non-local URL.
        $urlnonlocal = 'http://www.google.com/pluginfile.php/644/block_html/content/arithmetic-quiz-1-1.h5p';
        $result = external::get_trusted_h5p_file($urlnonlocal, 0, 0, 0, 0);
        $result = external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on warnings and none on files.
        $this->assertCount(0, $result['files']);
        $this->assertCount(1, $result['warnings']);
        // Check the warnings to be sure that h5pinvalidurl is the message by moodle_exception.
        $this->assertEquals($urlnonlocal, $result['warnings'][0]['item']);
        $this->assertEquals(get_string('h5pinvalidurl', 'core_h5p'), $result['warnings'][0]['message']);
    }

    /**
     * test_h5p_file_not_found description
     */
    public function test_h5p_file_not_found() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a valid url with an h5pfile which doesn't exist in DB.
        $syscontext = \context_system::instance();
        $filenotfoundurl  = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            0,
            '/',
            'notfound.h5p'
        );
        // Call the ws.
        $result = external::get_trusted_h5p_file($filenotfoundurl->out(), 0, 0, 0, 0);
        $result = external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on warnings and none on files.
        $this->assertCount(0, $result['files']);
        $this->assertCount(1, $result['warnings']);
        // Check the warnings to be sure that h5pfilenotfound is the message by h5p error.
        $this->assertEquals($filenotfoundurl->out(), $result['warnings'][0]['item']);
        $this->assertEquals(get_string('h5pfilenotfound', 'core_h5p'), $result['warnings'][0]['message']);
    }

    /**
     * Get the H5P export file.
     *
     * @param string $filename
     * @param string $pathnamehash
     * @return stored_file
     */
    protected function get_export_file($filename, $pathnamehash) {
        global $DB;

        // Simulate the filenameexport using slug as H5P does.
        $id = $DB->get_field('h5p', 'id', ['pathnamehash' => $pathnamehash]);
        $filenameexport = basename($filename, '.h5p').'-'.$id.'-'.$id.'.h5p';
        $syscontext = \context_system::instance();
        $fs = get_file_storage();
        $fileh5p = $fs->get_file($syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            0,
            '/',
            $filenameexport);
        return $fileh5p;
    }
}
