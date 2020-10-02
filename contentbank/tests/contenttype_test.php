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
use context_user;
use Exception;
use contenttype_testable\contenttype as contenttype;
/**
 * Test for content bank contenttype class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\contenttype
 *
 */
class core_contenttype_contenttype_testcase extends \advanced_testcase {

    /** @var int Identifier for the manager role. */
    protected $managerroleid;

    /** @var stdClass Manager user. */
    protected $manager1;

    /** @var stdClass Manager user. */
    protected $manager2;

    /** @var stdClass User. */
    protected $user;

    /** @var array List of contents created (every user has a key with contents created by her). */
    protected $contents = [];

    /** @var contenttype The contenttype instance. */
    protected $contenttype;

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');
    }

    /**
     * Tests get_contenttype_name result.
     *
     * @covers ::get_contenttype_name
     */
    public function test_get_contenttype_name() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->assertEquals('contenttype_testable', $testable->get_contenttype_name());
    }

    /**
     * Tests get_plugin_name result.
     *
     * @covers ::get_plugin_name
     */
    public function test_get_plugin_name() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->assertEquals('testable', $testable->get_plugin_name());
    }

    /**
     * Tests get_icon result.
     *
     * @covers ::get_icon
     */
    public function test_get_icon() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);
        $record = new stdClass();
        $record->name = 'New content';
        $content = $testable->create_content($record);
        $icon = $testable->get_icon($content);
        $this->assertContains('archive', $icon);
    }

    /**
     * Tests is_feature_supported behavior .
     *
     * @covers ::is_feature_supported
     */
    public function test_is_feature_supported() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->assertTrue($testable->is_feature_supported(contenttype::CAN_TEST));
        $this->assertFalse($testable->is_feature_supported(contenttype::CAN_UPLOAD));
    }

    /**
     * Tests can_upload behavior with no implemented upload feature.
     *
     * @covers ::can_upload
     */
    public function test_no_upload_feature_supported() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->setAdminUser();
        $this->assertFalse($testable->is_feature_supported(contenttype::CAN_UPLOAD));
        $this->assertFalse($testable->can_upload());
    }

    /**
     * Test create_content() with empty data.
     *
     * @covers ::create_content
     */
    public function test_create_empty_content() {
        $this->resetAfterTest();

        // Create empty content.
        $record = new stdClass();

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);

        $this->assertEquals('contenttype_testable', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_testable\\content', $content);
    }

    /**
     * Tests for behaviour of create_content() with data.
     *
     * @covers ::create_content
     */
    public function test_create_content() {
        $this->resetAfterTest();

        // Create content.
        $record = new stdClass();
        $record->name = 'Test content';
        $record->configdata = '';
        $record->contenttype = '';

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);

        $this->assertEquals('contenttype_testable', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_testable\\content', $content);
    }

    /**
     * Tests for behaviour of upload_content() with a file and a record.
     *
     * @dataProvider upload_content_provider
     * @param bool $userecord if a predefined record has to be used.
     *
     * @covers ::upload_content
     */
    public function test_upload_content(bool $userecord): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $dummy = [
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'file.h5p',
            'userid' => $USER->id,
        ];
        $fs = get_file_storage();
        $dummyfile = $fs->create_file_from_string($dummy, 'Dummy content');

        // Create content.
        if ($userecord) {
            $record = new stdClass();
            $record->name = 'Test content';
            $record->configdata = '';
            $record->contenttype = '';
            $checkname = $record->name;
        } else {
            $record = null;
            $checkname = $dummyfile->get_filename();
        }

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->upload_content($dummyfile, $record);

        $this->assertEquals('contenttype_testable', $content->get_content_type());
        $this->assertEquals($checkname, $content->get_name());
        $this->assertInstanceOf('\\contenttype_testable\\content', $content);

        $file = $content->get_file();
        $this->assertEquals($dummyfile->get_filename(), $file->get_filename());
        $this->assertEquals($dummyfile->get_userid(), $file->get_userid());
        $this->assertEquals($dummyfile->get_mimetype(), $file->get_mimetype());
        $this->assertEquals($dummyfile->get_contenthash(), $file->get_contenthash());
        $this->assertEquals('contentbank', $file->get_component());
        $this->assertEquals('public', $file->get_filearea());
        $this->assertEquals('/', $file->get_filepath());
    }

    /**
     * Data provider for test_rename_content.
     *
     * @return  array
     */
    public function upload_content_provider() {
        return [
            'With record' => [true],
            'Without record' => [false],
        ];
    }

    /**
     * Tests for behaviour of upload_content() with a file wrong file.
     *
     * @covers ::upload_content
     */
    public function test_upload_content_exception(): void {
        global $USER, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // The testing contenttype thows exception if filename is "error.*".
        $dummy = [
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'error.txt',
            'userid' => $USER->id,
        ];
        $fs = get_file_storage();
        $dummyfile = $fs->create_file_from_string($dummy, 'Dummy content');

        $contenttype = new contenttype(context_system::instance());
        $cbcontents = $DB->count_records('contentbank_content');

        // We need to capture the exception to check no content is created.
        try {
            $content = $contenttype->upload_content($dummyfile);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertEquals($cbcontents, $DB->count_records('contentbank_content'));
        $this->assertEquals(1, $DB->count_records('files', ['contenthash' => $dummyfile->get_contenthash()]));
    }

    /**
     * Tests for behaviour of replace_content() using a dummy file.
     *
     * @covers ::replace_content
     */
    public function test_replace_content(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_system::instance();

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 3, 0, $context);
        $content = reset($contents);

        $dummy = [
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'file.h5p',
            'userid' => $USER->id,
        ];
        $fs = get_file_storage();
        $dummyfile = $fs->create_file_from_string($dummy, 'Dummy content');

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->replace_content($dummyfile, $content);

        $this->assertEquals('contenttype_testable', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_testable\\content', $content);

        $file = $content->get_file();
        $this->assertEquals($dummyfile->get_userid(), $file->get_userid());
        $this->assertEquals($dummyfile->get_contenthash(), $file->get_contenthash());
        $this->assertEquals('contentbank', $file->get_component());
        $this->assertEquals('public', $file->get_filearea());
        $this->assertEquals('/', $file->get_filepath());
    }

    /**
     * Tests for behaviour of replace_content() using an error file.
     *
     * @covers ::replace_content
     */
    public function test_replace_content_exception(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_system::instance();

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 3, 0, $context);
        $content = reset($contents);

        $dummy = [
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'error.txt',
            'userid' => $USER->id,
        ];
        $fs = get_file_storage();
        $dummyfile = $fs->create_file_from_string($dummy, 'Dummy content');

        $contenttype = new contenttype(context_system::instance());

        $this->expectException(Exception::class);
        $content = $contenttype->replace_content($dummyfile, $content);
    }

    /**
     * Test the behaviour of can_delete().
     */
    public function test_can_delete() {
        global $DB;

        $this->resetAfterTest();
        $this->contenttype_setup_scenario_data();

        $managercontent = array_shift($this->contents[$this->manager1->id]);
        $usercontent = array_shift($this->contents[$this->user->id]);

        // Check the content has been created as expected.
        $records = $DB->count_records('contentbank_content');
        $this->assertEquals(4, $records);

        // Check user can only delete records created by her.
        $this->setUser($this->user);
        $this->assertFalse($this->contenttype->can_delete($managercontent));
        $this->assertTrue($this->contenttype->can_delete($usercontent));

        // Check manager can delete records all the records created.
        $this->setUser($this->manager1);
        $this->assertTrue($this->contenttype->can_delete($managercontent));
        $this->assertTrue($this->contenttype->can_delete($usercontent));

        // Unassign capability to manager role and check not can only delete their own records.
        unassign_capability('moodle/contentbank:deleteanycontent', $this->managerroleid);
        $this->assertTrue($this->contenttype->can_delete($managercontent));
        $this->assertFalse($this->contenttype->can_delete($usercontent));
        $this->setUser($this->manager2);
        $this->assertFalse($this->contenttype->can_delete($managercontent));
        $this->assertFalse($this->contenttype->can_delete($usercontent));
    }

    /**
     * Test the behaviour of delete_content().
     */
    public function test_delete_content() {
        global $DB;

        $this->resetAfterTest();
        $this->contenttype_setup_scenario_data();

        // Check the content has been created as expected.
        $this->assertEquals(4, $DB->count_records('contentbank_content'));

        // Check the content is deleted as expected.
        $this->setUser($this->manager1);
        $content = array_shift($this->contents[$this->manager1->id]);
        $deleted = $this->contenttype->delete_content($content);
        $this->assertTrue($deleted);
        $this->assertEquals(3, $DB->count_records('contentbank_content'));
    }

    /**
     * Helper function to setup 3 users (manager1, manager2 and user) and 4 contents (3 created by manager1 and 1 by user).
     */
    protected function contenttype_setup_scenario_data(string $contenttype = 'contenttype_testable'): void {
        global $DB;
        $systemcontext = context_system::instance();

        // Create users.
        $this->manager1 = $this->getDataGenerator()->create_user();
        $this->manager2 = $this->getDataGenerator()->create_user();
        $this->managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        $this->getDataGenerator()->role_assign($this->managerroleid, $this->manager1->id);
        $this->getDataGenerator()->role_assign($this->managerroleid, $this->manager2->id);
        $editingteacherrolerid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        $this->user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->role_assign($editingteacherrolerid, $this->user->id);

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $this->contents[$this->manager1->id] = $generator->generate_contentbank_data($contenttype, 3, $this->manager1->id);
        $this->contents[$this->user->id] = $generator->generate_contentbank_data($contenttype, 1, $this->user->id);

        $contenttypeclass = "\\$contenttype\\contenttype";
        $this->contenttype = new $contenttypeclass($systemcontext);
    }

    /**
     * Data provider for test_rename_content.
     *
     * @return  array
     */
    public function rename_content_provider() {
        return [
            'Standard name' => ['New name', 'New name', true],
            'Name with digits' => ['Today is 17/04/2017', 'Today is 17/04/2017', true],
            'Name with symbols' => ['Follow us: @moodle', 'Follow us: @moodle', true],
            'Name with tags' => ['This is <b>bold</b>', 'This is bold', true],
            'Long name' => [str_repeat('a', 100), str_repeat('a', 100), true],
            'Too long name' => [str_repeat('a', 300), str_repeat('a', 255), true],
            'Empty name' => ['', 'Test content ', false],
            'Blanks only' => ['  ', 'Test content ', false],
        ];
    }

    /**
     * Test the behaviour of rename_content().
     *
     * @dataProvider    rename_content_provider
     * @param   string  $newname    The name to set
     * @param   string   $expected   The name result
     * @param   bool   $result   The bolean result expected when renaming
     *
     * @covers ::rename_content
     */
    public function test_rename_content(string $newname, string $expected, bool $result) {
        global $DB;

        $this->resetAfterTest();

        // Create course and teacher user.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $coursecontext = \context_course::instance($course->id);
        $contenttype = new contenttype($coursecontext);

        // Add some content to the content bank as teacher.
        $this->setUser($teacher);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 1, $teacher->id);
        $content = array_shift($contents);

        $oldname = $content->get_name();

        // Check the content is renamed as expected by a user with permission.
        $renamed = $contenttype->rename_content($content, $newname);
        $this->assertEquals($result, $renamed);
        $record = $DB->get_record('contentbank_content', ['id' => $content->get_id()]);
        $this->assertEquals($expected, $record->name);
    }

    /**
     * Test the behaviour of move_content().
     */
    public function test_move_content() {
        global $DB;

        $this->resetAfterTest();
        $systemcontext = context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $systemcontents = $generator->generate_contentbank_data('contenttype_testable', 3, 0, $systemcontext);
        $generator->generate_contentbank_data('contenttype_testable', 3, 0, $coursecontext);
        $systemcontent = reset($systemcontents);

        // Check the content has been created as expected.
        $this->assertEquals(6, $DB->count_records('contentbank_content'));
        $this->assertEquals(3, $DB->count_records('contentbank_content', ['contextid' => $systemcontext->id]));
        $this->assertEquals(3, $DB->count_records('contentbank_content', ['contextid' => $coursecontext->id]));

        // Check the content files has been created as expected.
        $this->assertEquals(12, $DB->count_records('files', ['component' => 'contentbank']));
        $this->assertEquals(6, $DB->count_records('files', ['component' => 'contentbank', 'contextid' => $systemcontext->id]));
        $this->assertEquals(6, $DB->count_records('files', ['component' => 'contentbank', 'contextid' => $coursecontext->id]));

        // Check the content is moved as expected.
        $contenttype = new contenttype($systemcontext);
        $this->assertTrue($contenttype->move_content($systemcontent, $coursecontext));
        $this->assertEquals(6, $DB->count_records('contentbank_content'));
        $this->assertEquals(2, $DB->count_records('contentbank_content', ['contextid' => $systemcontext->id]));
        $this->assertEquals(4, $DB->count_records('contentbank_content', ['contextid' => $coursecontext->id]));

        // Check the content files were moved as expected.
        $this->assertEquals(12, $DB->count_records('files', ['component' => 'contentbank']));
        $this->assertEquals(4, $DB->count_records('files', ['component' => 'contentbank', 'contextid' => $systemcontext->id]));
        $this->assertEquals(8, $DB->count_records('files', ['component' => 'contentbank', 'contextid' => $coursecontext->id]));
    }

    /**
     * Test the behaviour of can_manage().
     *
     * @covers ::can_manage
     */
    public function test_can_manage() {
        global $DB, $USER;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');

        // Create course and teacher user.
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $manager = $this->getDataGenerator()->create_and_enrol($course, 'manager');
        $coursecontext = \context_course::instance($course->id);

        $contenttype = new contenttype($coursecontext);

        // Add some content to the content bank as admin.
        $this->setAdminUser();
        $contentsbyadmin = $generator->generate_contentbank_data('contenttype_testable', 1, $USER->id, $coursecontext);
        $contentbyadmin = array_shift($contentsbyadmin);

        // Add some content to the content bank as teacher.
        $contentsbyteacher = $generator->generate_contentbank_data('contenttype_testable', 1, $teacher->id, $coursecontext);
        $contentbyteacher = array_shift($contentsbyteacher);

        // Check the content has been created as expected.
        $records = $DB->count_records('contentbank_content');
        $this->assertEquals(2, $records);

        // Check manager can manage by default all the contents created.
        $this->setUser($manager);
        $this->assertTrue($contenttype->can_manage($contentbyteacher));
        $this->assertTrue($contenttype->can_manage($contentbyadmin));

        // Check teacher can only edit their own content.
        $this->setUser($teacher);
        $this->assertTrue($contenttype->can_manage($contentbyteacher));
        $this->assertFalse($contenttype->can_manage($contentbyadmin));

        // Unassign capability to teacher role and check they not can not edit any content.
        unassign_capability('moodle/contentbank:manageowncontent', $teacherroleid);
        $this->assertFalse($contenttype->can_manage($contentbyteacher));
        $this->assertFalse($contenttype->can_manage($contentbyadmin));
    }

    /**
     * Test the behaviour of can_download().
     *
     * @covers ::can_download
     */
    public function test_can_download() {
        global $DB;

        $this->resetAfterTest();
        $this->contenttype_setup_scenario_data('contenttype_h5p');

        $managercontent = array_shift($this->contents[$this->manager1->id]);
        $usercontent = array_shift($this->contents[$this->user->id]);

        // Check the content has been created as expected.
        $records = $DB->count_records('contentbank_content');
        $this->assertEquals(4, $records);

        // Check user can download content created by anybody.
        $this->setUser($this->user);
        $this->assertTrue($this->contenttype->can_download($usercontent));
        $this->assertTrue($this->contenttype->can_download($managercontent));

        // Check manager can download all the content too.
        $this->setUser($this->manager1);
        $this->assertTrue($this->contenttype->can_download($managercontent));
        $this->assertTrue($this->contenttype->can_download($usercontent));

        // Unassign capability to manager role and check she cannot download content anymore.
        unassign_capability('moodle/contentbank:downloadcontent', $this->managerroleid);
        $this->assertFalse($this->contenttype->can_download($managercontent));
        $this->assertFalse($this->contenttype->can_download($usercontent));
    }

    /**
     * Tests get_download_url result.
     *
     * @covers ::get_download_url
     */
    public function test_get_download_url() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $systemcontext = context_system::instance();

        // Add some content to the content bank.
        $filename = 'filltheblanks.h5p';
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/' . $filename;
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_testable', 1, 0, $systemcontext, true, $filepath);
        $content = array_shift($contents);

        // Check the URL is returned OK for a content with file.
        $contenttype = new contenttype($systemcontext);
        $url = $contenttype->get_download_url($content);
        $this->assertNotEmpty($url);
        $this->assertContains($filename, $url);

        // Check the URL is empty when the content hasn't any file.
        $record = new stdClass();
        $content = $contenttype->create_content($record);
        $url = $contenttype->get_download_url($content);
        $this->assertEmpty($url);
    }
}
