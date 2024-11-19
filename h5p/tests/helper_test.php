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

declare(strict_types = 1);

namespace core_h5p;

use core_h5p\local\library\autoloader;

/**
 * Test class covering the H5P helper.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_h5p\helper
 */
class helper_test extends \advanced_testcase {

    /**
     * Register the H5P autoloader
     */
    protected function setUp(): void {
        parent::setUp();
        autoloader::register();
    }

    /**
     * Test the behaviour of get_display_options().
     *
     * @dataProvider display_options_provider
     * @param  bool   $frame     Whether the frame should be displayed or not
     * @param  bool   $export    Whether the export action button should be displayed or not
     * @param  bool   $embed     Whether the embed action button should be displayed or not
     * @param  bool   $copyright Whether the copyright action button should be displayed or not
     * @param  int    $expected The expectation with the displayoptions value
     */
    public function test_display_options(bool $frame, bool $export, bool $embed, bool $copyright, int $expected): void {
        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();

        $factory = new \core_h5p\factory();
        $core = $factory->get_core();
        $config = (object)[
            'frame' => $frame,
            'export' => $export,
            'embed' => $embed,
            'copyright' => $copyright,
        ];

        // Test getting display options.
        $displayoptions = helper::get_display_options($core, $config);
        $this->assertEquals($expected, $displayoptions);

        // Test decoding display options.
        $decoded = helper::decode_display_options($core, $expected);
        $this->assertEquals($decoded->export, $config->export);
        $this->assertEquals($decoded->embed, $config->embed);
        $this->assertEquals($decoded->copyright, $config->copyright);
    }

    /**
     * Data provider for test_get_display_options().
     *
     * @return array
     */
    public static function display_options_provider(): array {
        return [
            'All display options disabled' => [
                false,
                false,
                false,
                false,
                15,
            ],
            'All display options enabled' => [
                true,
                true,
                true,
                true,
                0,
            ],
            'Frame disabled and the rest enabled' => [
                false,
                true,
                true,
                true,
                0,
            ],
            'Only export enabled' => [
                false,
                true,
                false,
                false,
                12,
            ],
            'Only embed enabled' => [
                false,
                false,
                true,
                false,
                10,
            ],
            'Only copyright enabled' => [
                false,
                false,
                false,
                true,
                6,
            ],
        ];
    }

    /**
     * Test the behaviour of save_h5p() when there are some missing libraries in the system.
     * @runInSeparateProcess
     */
    public function test_save_h5p_missing_libraries(): void {
        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // This is a valid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'greeting-card.h5p');
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);
        $factory->get_framework()->set_file($file);

        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        // There are some missing libraries in the system, so an error should be returned.
        $h5pid = helper::save_h5p($factory, $file, $config);
        $this->assertFalse($h5pid);
        $errors = $factory->get_framework()->getMessages('error');
        $this->assertCount(1, $errors);
        $error = reset($errors);
        $this->assertEquals('missing-main-library', $error->code);
        $this->assertEquals('Missing main library H5P.GreetingCard 1.0', $error->message);
    }

    /**
     * Test the behaviour of save_h5p() when the libraries exist in the system.
     * @runInSeparateProcess
     */
    public function test_save_h5p_existing_libraries(): void {
        global $DB;

        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // This is a valid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'greeting-card.h5p');
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);
        $factory->get_framework()->set_file($file);

        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];
        // The required libraries exist in the system before saving the .h5p file.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $lib = $generator->create_library_record('H5P.GreetingCard', 'GreetingCard', 1, 0);
        $h5pid = helper::save_h5p($factory, $file, $config);
        $this->assertNotEmpty($h5pid);

        // No errors are raised.
        $errors = $factory->get_framework()->getMessages('error');
        $this->assertCount(0, $errors);

        // And the content in the .h5p file has been saved as expected.
        $h5p = $DB->get_record('h5p', ['id' => $h5pid]);
        $this->assertEquals($lib->id, $h5p->mainlibraryid);
        $this->assertEquals(helper::get_display_options($factory->get_core(), $config), $h5p->displayoptions);
        $this->assertStringContainsString('Hello world!', $h5p->jsoncontent);
    }

    /**
     * Test the behaviour of save_h5p() when the H5P file contains metadata.
     *
     * @runInSeparateProcess
     */
    public function test_save_h5p_metadata(): void {
        global $DB;

        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // This is a valid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'guess-the-answer.h5p');
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);
        $factory->get_framework()->set_file($file);

        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 1,
        ];
        // The required libraries exist in the system before saving the .h5p file.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $lib = $generator->create_library_record('H5P.GuessTheAnswer', 'Guess the Answer', 1, 5);
        $generator->create_library_record('H5P.Image', 'Image', 1, 1);
        $generator->create_library_record('FontAwesome', 'Font Awesome', 4, 5);
        $h5pid = helper::save_h5p($factory, $file, $config);
        $this->assertNotEmpty($h5pid);

        // No errors are raised.
        $errors = $factory->get_framework()->getMessages('error');
        $this->assertCount(0, $errors);

        // And the content in the .h5p file has been saved as expected.
        $h5p = $DB->get_record('h5p', ['id' => $h5pid]);
        $this->assertEquals($lib->id, $h5p->mainlibraryid);
        $this->assertEquals(helper::get_display_options($factory->get_core(), $config), $h5p->displayoptions);
        $this->assertStringContainsString('Which fruit is this?', $h5p->jsoncontent);
        // Metadata has been also saved.
        $this->assertStringContainsString('This is licence extras information added for testing purposes.', $h5p->jsoncontent);
        $this->assertStringContainsString('H5P Author', $h5p->jsoncontent);
        $this->assertStringContainsString('Add metadata information', $h5p->jsoncontent);
    }

    /**
     * Test the behaviour of save_h5p() when the .h5p file is invalid.
     * @runInSeparateProcess
     */
    public function test_save_h5p_invalid_file(): void {
        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Prepare an invalid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'h5ptest.zip');
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);
        $factory->get_framework()->set_file($file);
        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        // When saving an invalid .h5p file, an error should be raised.
        $h5pid = helper::save_h5p($factory, $file, $config);
        $this->assertFalse($h5pid);
        $errors = $factory->get_framework()->getMessages('error');
        $this->assertCount(2, $errors);

        $expectederrorcodes = ['invalid-content-folder', 'invalid-h5p-json-file'];
        foreach ($errors as $error) {
            $this->assertContains($error->code, $expectederrorcodes);
        }
    }

    /**
     * Test the behaviour of can_deploy_package().
     */
    public function test_can_deploy_package(): void {
        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $admin = get_admin();

        // Prepare a valid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'greeting-card.h5p');

        // Files created by users can't be deployed.
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);
        $factory->get_framework()->set_file($file);
        $candeploy = helper::can_deploy_package($file);
        $this->assertFalse($candeploy);

        // Files created by admins can be deployed, even when the current user is not the admin.
        $this->setUser($user);
        $file = helper::create_fake_stored_file_from_path($path, (int)$admin->id);
        $factory->get_framework()->set_file($file);
        $candeploy = helper::can_deploy_package($file);
        $this->assertTrue($candeploy);

        $usertobedeleted = $this->getDataGenerator()->create_user();
        $this->setUser($usertobedeleted);
        $file = helper::create_fake_stored_file_from_path($path, (int)$usertobedeleted->id);
        $factory->get_framework()->set_file($file);
        // Then we delete this user.
        $this->setAdminUser();
        delete_user($usertobedeleted);
        $candeploy = helper::can_deploy_package($file);
        $this->assertTrue($candeploy); // We can update as admin.
    }

    /**
     * Test the behaviour of can_update_library().
     */
    public function test_can_update_library(): void {
        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $admin = get_admin();

        // Prepare a valid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'greeting-card.h5p');

        // Libraries can't be updated when the file has been created by users.
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);
        $factory->get_framework()->set_file($file);
        $candeploy = helper::can_update_library($file);
        $this->assertFalse($candeploy);

        // Libraries can be updated when the file has been created by admin, even when the current user is not the admin.
        $this->setUser($user);
        $file = helper::create_fake_stored_file_from_path($path, (int)$admin->id);
        $factory->get_framework()->set_file($file);
        $candeploy = helper::can_update_library($file);
        $this->assertTrue($candeploy);

        $usertobedeleted = $this->getDataGenerator()->create_user();
        $this->setUser($usertobedeleted);
        $file = helper::create_fake_stored_file_from_path($path, (int)$usertobedeleted->id);
        $factory->get_framework()->set_file($file);
        // Then we delete this user.
        $this->setAdminUser();
        delete_user($usertobedeleted);
        $canupdate = helper::can_update_library($file);
        $this->assertTrue($canupdate); // We can update as admin.
    }

    /**
     * Test the behaviour of get_messages().
     */
    public function test_get_messages(): void {
        $this->resetAfterTest();

        $factory = new \core_h5p\factory();
        $messages = new \stdClass();

        helper::get_messages($messages, $factory);
        $this->assertTrue(empty($messages->error));
        $this->assertTrue(empty($messages->info));

        // Add an some messages manually and check they are still there.
        $messages->error = [];
        $messages->error['error1'] = 'Testing ERROR message';
        $messages->info = [];
        $messages->info['info1'] = 'Testing INFO message';
        $messages->info['info2'] = 'Testing INFO message';
        helper::get_messages($messages, $factory);
        $this->assertCount(1, $messages->error);
        $this->assertCount(2, $messages->info);

        // When saving an invalid .h5p file, 6 errors should be raised.
        $path = self::get_fixture_path(__NAMESPACE__, 'h5ptest.zip');
        $file = helper::create_fake_stored_file_from_path($path);
        $factory->get_framework()->set_file($file);
        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];
        $h5pid = helper::save_h5p($factory, $file, $config);
        $this->assertFalse($h5pid);
        helper::get_messages($messages, $factory);
        $this->assertCount(7, $messages->error);
        $this->assertCount(2, $messages->info);
    }

    /**
     * Test the behaviour of get_export_info().
     */
    public function test_get_export_info(): void {
         $this->resetAfterTest();

        $filename = 'guess-the-answer.h5p';
        $syscontext = \context_system::instance();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $deployedfile = $generator->create_export_file($filename,
            $syscontext->id,
            file_storage::COMPONENT,
            file_storage::EXPORT_FILEAREA);

        // Test scenario 1: Get export information from correct filename.
        $helperfile = helper::get_export_info($deployedfile['filename']);
        $this->assertEquals($deployedfile['filename'], $helperfile['filename']);
        $this->assertEquals($deployedfile['filepath'], $helperfile['filepath']);
        $this->assertEquals($deployedfile['filesize'], $helperfile['filesize']);
        $this->assertEquals($deployedfile['timemodified'], $helperfile['timemodified']);
        $this->assertEquals($deployedfile['fileurl'], $helperfile['fileurl']);

        // Test scenario 2: Get export information from correct filename and url.
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            file_storage::COMPONENT,
            'unittest',
            0,
            '/',
            $deployedfile['filename'],
            false,
            true
        );
        $helperfile = helper::get_export_info($deployedfile['filename'], $url);
        $this->assertEquals($url, $helperfile['fileurl']);

        // Test scenario 3: Get export information from correct filename and factory.
        $factory = new \core_h5p\factory();
        $helperfile = helper::get_export_info($deployedfile['filename'], null, $factory);
        $this->assertEquals($deployedfile['filename'], $helperfile['filename']);
        $this->assertEquals($deployedfile['filepath'], $helperfile['filepath']);
        $this->assertEquals($deployedfile['filesize'], $helperfile['filesize']);
        $this->assertEquals($deployedfile['timemodified'], $helperfile['timemodified']);
        $this->assertEquals($deployedfile['fileurl'], $helperfile['fileurl']);

        // Test scenario 4: Get export information from wrong filename.
        $helperfile = helper::get_export_info('nofileexist.h5p', $url);
        $this->assertNull($helperfile);
    }

    /**
     * Test the parse_js_array function with a range of content.
     *
     * @dataProvider parse_js_array_provider
     * @param string $content
     * @param array $expected
     */
    public function test_parse_js_array(string $content, array $expected): void {
        $this->assertEquals($expected, helper::parse_js_array($content));
    }

    /**
     * Data provider for test_parse_js_array().
     *
     * @return array
     */
    public static function parse_js_array_provider(): array {
        $lines = [
            "{",
            "  missingTranslation: '[Missing translation :key]',",
            "  loading: 'Loading, please wait...',",
            "  selectLibrary: 'Select the library you wish to use for your content.',",
            "}",
        ];
        $expected = [
            'missingTranslation' => '[Missing translation :key]',
            'loading' => 'Loading, please wait...',
            'selectLibrary' => 'Select the library you wish to use for your content.',
        ];
        return [
            'Strings with \n' => [
                implode("\n", $lines),
                $expected,
            ],
            'Strings with \r\n' => [
                implode("\r\n", $lines),
                $expected,
            ],
            'Strings with \r' => [
                implode("\r", $lines),
                $expected,
            ],
        ];
    }
}
