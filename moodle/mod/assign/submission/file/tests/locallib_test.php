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
 * Tests for mod/assign/submission/file/locallib.php
 *
 * @package   assignsubmission_file
 * @copyright 2016 Cameron Ball
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Unit tests for mod/assign/submission/file/locallib.php
 *
 * @copyright  2016 Cameron Ball
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignsubmission_file_locallib_testcase extends advanced_testcase {

    /** @var stdClass $user A user to submit an assignment. */
    protected $user;

    /** @var stdClass $course New course created to hold the assignment activity. */
    protected $course;

    /** @var stdClass $cm A context module object. */
    protected $cm;

    /** @var stdClass $context Context of the assignment activity. */
    protected $context;

    /** @var stdClass $assign The assignment object. */
    protected $assign;

    /**
     * Setup all the various parts of an assignment activity including creating an onlinetext submission.
     */
    protected function setUp() {
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params = [
            'course' => $this->course->id,
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 12,
            'assignsubmission_file_maxsizebytes' => 10,
        ];
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);
        $this->assign = new testable_assign($this->context, $this->cm, $this->course);
        $this->setUser($this->user->id);
    }

    /**
     * Test submission_is_empty
     *
     * @dataProvider submission_is_empty_testcases
     * @param string $data The file submission data
     * @param bool $expected The expected return value
     */
    public function test_submission_is_empty($data, $expected) {
        $this->resetAfterTest();

        $itemid = file_get_unused_draft_itemid();
        $submission = (object)['files_filemanager' => $itemid];
        $plugin = $this->assign->get_submission_plugin_by_type('file');

        if ($data) {
            $data += ['contextid' => context_user::instance($this->user->id)->id, 'itemid' => $itemid];
            $fs = get_file_storage();
            $fs->create_file_from_string((object)$data, 'Content of ' . $data['filename']);
        }

        $result = $plugin->submission_is_empty($submission);
        $this->assertTrue($result === $expected);
    }

    /**
     * Test new_submission_empty
     *
     * @dataProvider submission_is_empty_testcases
     * @param string $data The file submission data
     * @param bool $expected The expected return value
     */
    public function test_new_submission_empty($data, $expected) {
        $this->resetAfterTest();

        $itemid = file_get_unused_draft_itemid();
        $submission = (object)['files_filemanager' => $itemid];

        if ($data) {
            $data += ['contextid' => context_user::instance($this->user->id)->id, 'itemid' => $itemid];
            $fs = get_file_storage();
            $fs->create_file_from_string((object)$data, 'Content of ' . $data['filename']);
        }

        $result = $this->assign->new_submission_empty($submission);
        $this->assertTrue($result === $expected);
    }

    /**
     * Dataprovider for the test_submission_is_empty testcase
     *
     * @return array of testcases
     */
    public function submission_is_empty_testcases() {
        return [
            'With file' => [
                [
                    'component' => 'user',
                    'filearea' => 'draft',
                    'filepath' => '/',
                    'filename' => 'not_a_virus.exe'
                ],
                false
            ],
            'Without file' => [null, true]
        ];
    }

    /**
     * Data provider for testing test_get_nonexistent_file_types.
     *
     * @return array
     */
    public function get_nonexistent_file_types_provider() {
        return [
            'Nonexistent extensions are not allowed' => [
                'filetypes' => '.rat',
                'expected' => ['.rat']
            ],
            'Multiple nonexistent extensions are not allowed' => [
                'filetypes' => '.ricefield .rat',
                'expected' => ['.ricefield', '.rat']
            ],
            'Existent extension is allowed' => [
                'filetypes' => '.xml',
                'expected' => []
            ],
            'Existent group is allowed' => [
                'filetypes' => 'web_file',
                'expected' => []
            ],
            'Nonexistent group is not allowed' => [
                'filetypes' => '©ç√√ß∂å√©åß©√',
                'expected' => ['©ç√√ß∂å√©åß©√']
            ],
            'Existent mimetype is allowed' => [
                'filetypes' => 'application/xml',
                'expected' => []
            ],
            'Nonexistent mimetype is not allowed' => [
                'filetypes' => 'ricefield/rat',
                'expected' => ['ricefield/rat']
            ],
            'Multiple nonexistent mimetypes are not allowed' => [
                'filetypes' => 'ricefield/rat cam/ball',
                'expected' => ['ricefield/rat', 'cam/ball']
            ],
            'Missing dot in extension is not allowed' => [
                'filetypes' => 'png',
                'expected' => ['png']
            ],
            'Some existent some not' => [
                'filetypes' => '.txt application/xml web_file ©ç√√ß∂å√©åß©√ .png ricefield/rat document png',
                'expected' => ['©ç√√ß∂å√©åß©√', 'ricefield/rat', 'png']
            ]
        ];
    }

    /**
     * Test get_nonexistent_file_types().
     * @dataProvider get_nonexistent_file_types_provider
     * @param string $filetypes The filetypes to check
     * @param array $expected The expected result. The list of non existent file types.
     */
    public function test_get_nonexistent_file_types($filetypes, $expected) {
        $this->resetAfterTest();
        $method = new ReflectionMethod(assign_submission_file::class, 'get_nonexistent_file_types');
        $method->setAccessible(true);
        $plugin = $this->assign->get_submission_plugin_by_type('file');
        $nonexistentfiletypes = $method->invokeArgs($plugin, [$filetypes]);
        $this->assertSame($expected, $nonexistentfiletypes);
    }

}
