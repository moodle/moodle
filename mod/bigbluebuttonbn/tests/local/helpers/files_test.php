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
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

namespace mod_bigbluebuttonbn\local\helpers;

use context_course;
use context_module;
use context_system;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use stdClass;
use stored_file;

/**
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\local\helpers\files
 * @coversDefaultClass \mod_bigbluebuttonbn\local\helpers\files
 */
final class files_test extends \advanced_testcase {
    use testcase_helper_trait;

    /** @var string Filename used for the presentation */
    const PRESENTATION_FILENAME = 'bbpresentation.pptx';

    /** @var string Filename used for the presentation */
    const PRESENTATION_FILEPATH = '/mod/bigbluebuttonbn/tests/fixtures/bbpresentation.pptx';

    /**
     * Plugin valid test case
     */
    public function test_pluginfile_valid(): void {
        $this->resetAfterTest();
        $this->assertFalse(files::pluginfile_valid(context_course::instance($this->get_course()->id), 'presentation'));
        $this->assertTrue(files::pluginfile_valid(context_system::instance(), 'presentation'));
        $this->assertFalse(files::pluginfile_valid(context_system::instance(), 'otherfilearea'));
    }

    /**
     * Plugin file test case
     */
    public function test_pluginfile_file(): void {
        global $CFG;
        $this->resetAfterTest();

        list($user, $bbactivity) = $this->create_user_and_activity($CFG->dirroot . self::PRESENTATION_FILEPATH);
        $this->setUser($user);
        $instance = instance::get_from_instanceid($bbactivity->id);
        $cm = $instance->get_cm();
        $cmrecord = $cm->get_course_module_record();
        /** @var stored_file $mediafile */
        $mediafile =
            files::pluginfile_file($this->get_course(), $cmrecord, $instance->get_context(),
                'presentation', [self::PRESENTATION_FILENAME]);
        $this->assertEquals(self::PRESENTATION_FILENAME, $mediafile->get_filename());
    }

    /**
     * Get presentation file
     */
    public function test_default_presentation_get_file(): void {
        $this->resetAfterTest();

        list($user, $bbactivity) = $this->create_user_and_activity();
        $this->setUser($user);

        $instance = instance::get_from_instanceid($bbactivity->id);
        $cm = $instance->get_cm();
        $cmrecord = $cm->get_course_module_record();
        $mediafilename =
            files::get_plugin_filename($this->get_course(), $cmrecord, $instance->get_context(), ['presentation.pptx']);
        $this->assertEquals('presentation.pptx', $mediafilename);
    }

    /**
     * Test that file is accessible only once.
     */
    public function test_presentation_file_accessible_twice(): void {
        global $CFG;
        $this->resetAfterTest();

        list($user, $bbactivity) = $this->create_user_and_activity($CFG->dirroot . self::PRESENTATION_FILEPATH);
        $this->setUser($user);
        $CFG->bigbluebuttonbn_preuploadpresentation_editable = true;
        $instance = instance::get_from_instanceid($bbactivity->id);
        $presentation = $instance->get_presentation_for_bigbluebutton_upload();
        $fulldirset = explode('/', $presentation['url']);
        $filename = array_pop($fulldirset);
        $nonce = array_pop($fulldirset);
        $cm = $instance->get_cm();
        $cmrecord = $cm->get_course_module_record();
        // The link should be valid twice.
        for ($i = 0; $i < 2; $i++) {
            $mediafile = files::pluginfile_file($this->get_course(), $cmrecord, $instance->get_context(), 'presentation',
                [$nonce, $filename]);
            $this->assertEquals($filename, $mediafile->get_filename());
        }
        // Third time is a charm, this should be false.
        $mediafile = files::pluginfile_file($this->get_course(), $cmrecord, $instance->get_context(), 'presentation',
            [$nonce, $filename]);
        $this->assertFalse($mediafile);
    }

    /**
     * Test that file is accessible only once.
     */
    public function test_presentation_file_not_accessible_externally(): void {
        global $CFG;
        $this->resetAfterTest();

        list($user, $bbactivity) = $this->create_user_and_activity($CFG->dirroot . self::PRESENTATION_FILEPATH);
        $this->setUser($user);
        $CFG->bigbluebuttonbn_preuploadpresentation_editable = true;
        $instance = instance::get_from_instanceid($bbactivity->id);
        $presentation = $instance->get_presentation();
        $fulldirset = explode('/', $presentation['url']);
        $filename = array_pop($fulldirset);
        $this->setGuestUser();
        $this->expectException(\require_login_exception::class);
        $cm = $instance->get_cm();
        $cmrecord = $cm->get_course_module_record();
        files::pluginfile_file($this->get_course(), $cmrecord, $instance->get_context(), 'presentation', [$filename]);

        $this->setUser($user);
        $mediafile = files::pluginfile_file($this->get_course(), $cmrecord, $instance->get_context(), 'presentation', [$filename]);
        $this->assertNotNull($mediafile);
    }

    /**
     * Get filename test
     */
    public function test_pluginfile_filename(): void {
        global $CFG;
        $this->resetAfterTest();

        list($user, $bbactivity, $bbactivitycm, $bbactivitycontext) = $this->create_user_and_activity();
        $this->setUser($user);
        $this->create_sample_file(self::PRESENTATION_FILENAME, $bbactivitycontext->id);
        $CFG->bigbluebuttonbn_preuploadpresentation_editable = true;
        $presentationdef = files::get_presentation($bbactivitycontext, self::PRESENTATION_FILENAME, $bbactivity->id, true);
        $pathparts = explode('/', $presentationdef['url']);
        $filename = array_pop($pathparts);
        $salt = array_pop($pathparts);
        $filename = files::get_plugin_filename($this->get_course(), $bbactivitycm->get_course_module_record(), $bbactivitycontext,
            [$salt, $filename]);
        $this->assertEquals(self::PRESENTATION_FILENAME, $filename);
    }

    /**
     * Get media files
     */
    public function test_get_media_file(): void {
        $this->resetAfterTest();

        list($user, $bbactivity) = $this->create_user_and_activity();
        $this->setUser($user);

        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        $mediafilepath = files::save_media_file($bbformdata);
        $this->assertEmpty($mediafilepath);

        // From test_delete_original_file_from_draft (lib/test/filelib_test.php)
        // Create a bbb private file.
        $this->create_sample_file(self::PRESENTATION_FILENAME, context_module::instance($bbformdata->coursemodule)->id);
        file_prepare_draft_area($bbformdata->presentation,
            context_module::instance($bbformdata->coursemodule)->id,
            'mod_bigbluebuttonbn',
            'presentation', 0);

        $mediafilepath = files::save_media_file($bbformdata);
        $this->assertEquals('/' . self::PRESENTATION_FILENAME, $mediafilepath);
    }

    /**
     * Create a user and an activity
     *
     * @param string|null $presentationpath
     * @param bool $closed
     * @return array
     */
    protected function create_user_and_activity($presentationpath = null, $closed = false): array {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setAdminUser();
        $activitydata = ['closingtime' => time() + ($closed ? -3600 : +3600)];
        if (!empty($presentationpath)) {
            $activitydata['presentation'] = $presentationpath;
        }
        list($bbactivitycontext, $bbactivitycm, $bbactivity) =
            $this->create_instance(null, $activitydata);
        $generator->enrol_user($user->id, $this->get_course()->id, 'editingteacher');
        return [$user, $bbactivity, $bbactivitycm, $bbactivitycontext];
    }

    /**
     * Helper to create sample file for tests
     *
     * @param string $filename
     * @param int $contextid
     * @return stored_file
     */
    protected function create_sample_file($filename, $contextid) {
        $bbbfilerecord = new stdClass;
        $bbbfilerecord->contextid = $contextid;
        $bbbfilerecord->component = 'mod_bigbluebuttonbn';
        $bbbfilerecord->filearea = 'presentation';
        $bbbfilerecord->itemid = 0;
        $bbbfilerecord->filepath = '/';
        $bbbfilerecord->filename = $filename;
        $bbbfilerecord->source = 'test';
        $fs = get_file_storage();
        return $fs->create_file_from_string($bbbfilerecord, 'Presentation file content');
    }
}
