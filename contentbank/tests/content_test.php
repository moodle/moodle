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
 * Test for content bank contenttype class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

use stdClass;
use context_system;
use contenttype_testable\contenttype as contenttype;

/**
 * Test for content bank contenttype class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\content
 *
 */
class core_contenttype_content_testcase extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
    }

    /**
     * Tests for behaviour of get_name().
     *
     * @covers ::get_name
     */
    public function test_get_name() {
        $this->resetAfterTest();

        // Create content.
        $record = new stdClass();
        $record->name = 'Test content';
        $record->configdata = '';

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);
        $this->assertEquals($record->name, $content->get_name());
    }

    /**
     * Data provider for test_set_name.
     *
     * @return  array
     */
    public function set_name_provider() {
        return [
            'Standard name' => ['New name', 'New name'],
            'Name with digits' => ['Today is 17/04/2017', 'Today is 17/04/2017'],
            'Name with symbols' => ['Follow us: @moodle', 'Follow us: @moodle'],
            'Name with tags' => ['This is <b>bold</b>', 'This is bold'],
            'Long name' => [str_repeat('a', 100), str_repeat('a', 100)],
            'Too long name' => [str_repeat('a', 300), str_repeat('a', 255)]
        ];
    }

    /**
     * Tests for 'set_name' behaviour.
     *
     * @dataProvider    set_name_provider
     * @param   string  $newname    The name to set
     * @param   string   $expected   The name result
     *
     * @covers ::set_name
     */
    public function test_set_name(string $newname, string $expected) {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $oldname = "Old name";
        $context = context_system::instance();

        // Create content.
        $record = new stdClass();
        $record->name = $oldname;

        $contenttype = new contenttype($context);
        $content = $contenttype->create_content($record);
        $this->assertEquals($oldname, $content->get_name());

        $content->set_name($newname);
        $this->assertEquals($expected, $content->get_name());

        $record = $DB->get_record('contentbank_content', ['id' => $content->get_id()]);
        $this->assertEquals($expected, $record->name);
    }

    /**
     * Tests for behaviour of get_content_type().
     *
     * @covers ::get_content_type
     */
    public function test_get_content_type() {
        $this->resetAfterTest();

        // Create content.
        $record = new stdClass();
        $record->name = 'Test content';
        $record->configdata = '';

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);
        $this->assertEquals('contenttype_testable', $content->get_content_type());
    }

    /**
     * Tests for 'configdata' behaviour.
     *
     * @covers ::set_configdata
     */
    public function test_configdata_changes() {
        $this->resetAfterTest();

        $configdata = "{img: 'icon.svg'}";

        // Create content.
        $record = new stdClass();
        $record->configdata = $configdata;

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);
        $this->assertEquals($configdata, $content->get_configdata());

        $configdata = "{alt: 'Name'}";
        $content->set_configdata($configdata);
        $this->assertEquals($configdata, $content->get_configdata());
    }

    /**
     * Tests for 'set_contextid' behaviour.
     *
     * @covers ::set_contextid
     */
    public function test_set_contextid() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $newcontext = \context_course::instance($course->id);

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 3, 0, $context);
        $content = reset($contents);

        $oldcontextid = $content->get_contextid();

        $file = $content->get_file();
        $this->assertEquals($oldcontextid, $file->get_contextid());
        $this->assertEquals($context->id, $oldcontextid);
        $this->assertNotEquals($newcontext->id, $oldcontextid);

        $content->set_contextid($newcontext->id);
        $file = $content->get_file();

        $this->assertEquals($newcontext->id, $content->get_contextid());
        $this->assertEquals($newcontext->id, $file->get_contextid());
    }

    /**
     * Tests for 'import_file' behaviour when replacing a file.
     *
     * @covers ::import_file
     */
    public function test_import_file_replace(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_system::instance();

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 3, 0, $context);
        $content = reset($contents);

        $originalfile = $content->get_file();

        // Create a dummy file.
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'contentbank',
            'filearea' => 'draft',
            'itemid' => $content->get_id(),
            'filepath' => '/',
            'filename' => 'example.txt'
        );
        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, 'Dummy content ');

        $importedfile = $content->import_file($file);

        $this->assertEquals($originalfile->get_filename(), $importedfile->get_filename());
        $this->assertEquals($originalfile->get_filearea(), $importedfile->get_filearea());
        $this->assertEquals($originalfile->get_filepath(), $importedfile->get_filepath());
        $this->assertEquals($originalfile->get_mimetype(), $importedfile->get_mimetype());

        $this->assertEquals($file->get_userid(), $importedfile->get_userid());
        $this->assertEquals($file->get_contenthash(), $importedfile->get_contenthash());
    }

    /**
     * Tests for 'import_file' behaviour when uploading a new file.
     *
     * @covers ::import_file
     */
    public function test_import_file_upload(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_system::instance();

        $type = new contenttype($context);
        $record = (object)[
            'name' => 'content name',
            'usercreated' => $USER->id,
        ];
        $content = $type->create_content($record);

        // Create a dummy file.
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'contentbank',
            'filearea' => 'draft',
            'itemid' => $content->get_id(),
            'filepath' => '/',
            'filename' => 'example.txt'
        );
        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, 'Dummy content ');

        $importedfile = $content->import_file($file);

        $this->assertEquals($file->get_filename(), $importedfile->get_filename());
        $this->assertEquals($file->get_userid(), $importedfile->get_userid());
        $this->assertEquals($file->get_mimetype(), $importedfile->get_mimetype());
        $this->assertEquals($file->get_contenthash(), $importedfile->get_contenthash());
        $this->assertEquals('public', $importedfile->get_filearea());
        $this->assertEquals('/', $importedfile->get_filepath());

        $contentfile = $content->get_file($file);
        $this->assertEquals($importedfile->get_id(), $contentfile->get_id());
    }
}
