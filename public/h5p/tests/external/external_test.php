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

namespace core_h5p\external;

use core_h5p\external;
use core_h5p\local\library\autoloader;

/**
 * Core h5p external functions tests
 *
 * @package    core_h5p
 * @category   external
 * @copyright  2019 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.8
 */
final class external_test extends \core_external\tests\externallib_testcase {
    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        autoloader::register();
    }

    /**
     * test_get_trusted_h5p_file description
     */
    public function test_get_trusted_h5p_file(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // This is a valid .H5P file.
        $filename = 'find-the-words.h5p';
        $syscontext = \context_system::instance();

        // Create a fake export H5P file with normal pluginfile call.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $deployedfile = $generator->create_export_file($filename,
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            $generator::PLUGINFILE);

        // Make the URL to pass to the WS.
        $url  = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            0,
            '/',
            $filename
        );

        // Call the WS.
        $result = external::get_trusted_h5p_file($url->out(false), 0, 0, 0, 0);
        $result = \core_external\external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on files and none on warnings.
        $this->assertCount(1, $result['files']);
        $this->assertCount(0, $result['warnings']);

        // Check info export file to compare with the ws's results.
        $this->assertEquals($deployedfile['filepath'], $result['files'][0]['filepath']);
        $this->assertEquals($deployedfile['mimetype'], $result['files'][0]['mimetype']);
        $this->assertEquals($deployedfile['filesize'], $result['files'][0]['filesize']);
        $this->assertEquals($deployedfile['timemodified'], $result['files'][0]['timemodified']);
        $this->assertStringContainsString($deployedfile['filename'], $result['files'][0]['filename']);
        $this->assertStringContainsString($deployedfile['fileurl'], $result['files'][0]['fileurl']);
    }

    /**
     * test_h5p_invalid_url description
     */
    public function test_h5p_invalid_url(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create an empty url.
        $urlempty = '';
        $result = external::get_trusted_h5p_file($urlempty, 0, 0, 0, 0);
        $result = \core_external\external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on warnings and none on files.
        $this->assertCount(0, $result['files']);
        $this->assertCount(1, $result['warnings']);
        // Check the warnings to be sure that h5pinvalidurl is the message by moodle_exception.
        $this->assertEquals($urlempty, $result['warnings'][0]['item']);
        $this->assertEquals(get_string('h5pinvalidurl', 'core_h5p'), $result['warnings'][0]['message']);

        // Create a non-local URL.
        $urlnonlocal = 'http://www.google.com/pluginfile.php/644/block_html/content/arithmetic-quiz-1-1.h5p';
        $result = external::get_trusted_h5p_file($urlnonlocal, 0, 0, 0, 0);
        $result = \core_external\external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
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
    public function test_h5p_file_not_found(): void {
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
        $result = \core_external\external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on warnings and none on files.
        $this->assertCount(0, $result['files']);
        $this->assertCount(1, $result['warnings']);
        // Check the warnings to be sure that h5pfilenotfound is the message by h5p error.
        $this->assertEquals($filenotfoundurl->out(), $result['warnings'][0]['item']);
        $this->assertEquals(get_string('h5pfilenotfound', 'core_h5p'), $result['warnings'][0]['message']);
    }

    /**
     * Test the request to get_trusted_h5p_file
     * using webservice/pluginfile.php as url param.
     */
    public function test_allow_webservice_pluginfile_in_url_param(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // This is a valid .H5P file.
        $filename = 'find-the-words.h5p';
        $syscontext = \context_system::instance();

        // Create a fake export H5P file with webservice call.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $deployedfile = $generator->create_export_file($filename,
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA);

        // Make the URL to pass to the WS.
        $url  = \moodle_url::make_webservice_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            0,
            '/',
            $filename
        );

        // Call the WS.
        $result = external::get_trusted_h5p_file($url->out(), 0, 0, 0, 0);
        $result = \core_external\external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);

        // Check info export file to compare with the ws's results.
        $this->assertEquals($deployedfile['filepath'], $result['files'][0]['filepath']);
        $this->assertEquals($deployedfile['mimetype'], $result['files'][0]['mimetype']);
        $this->assertEquals($deployedfile['filesize'], $result['files'][0]['filesize']);
        $this->assertEquals($deployedfile['timemodified'], $result['files'][0]['timemodified']);
        $this->assertStringContainsString($deployedfile['filename'], $result['files'][0]['filename']);
        $this->assertStringContainsString($deployedfile['fileurl'], $result['files'][0]['fileurl']);
    }

    /**
     * Test the request to get_trusted_h5p_file
     * using tokenpluginfile.php as url param.
     */
    public function test_allow_tokenluginfile_in_url_param(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // This is a valid .H5P file.
        $filename = 'find-the-words.h5p';
        $syscontext = \context_system::instance();

        // Create a fake export H5P file with tokenfile call.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $deployedfile = $generator->create_export_file($filename,
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            $generator::TOKENPLUGINFILE);

        // Make the URL to pass to the WS.
        $url  = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            0,
            '/',
            $filename,
            false,
            true
        );

        // Call the WS.
        $result = external::get_trusted_h5p_file($url->out(false), 0, 0, 0, 0);
        $result = \core_external\external_api::clean_returnvalue(external::get_trusted_h5p_file_returns(), $result);
        // Expected result: Just 1 record on files and none on warnings.
        $this->assertCount(1, $result['files']);
        $this->assertCount(0, $result['warnings']);

        // Check info export file to compare with the ws's results.
        $this->assertEquals($deployedfile['filepath'], $result['files'][0]['filepath']);
        $this->assertEquals($deployedfile['mimetype'], $result['files'][0]['mimetype']);
        $this->assertEquals($deployedfile['filesize'], $result['files'][0]['filesize']);
        $this->assertEquals($deployedfile['timemodified'], $result['files'][0]['timemodified']);
        $this->assertStringContainsString($deployedfile['filename'], $result['files'][0]['filename']);
        $this->assertStringContainsString($deployedfile['fileurl'], $result['files'][0]['fileurl']);
    }
}
