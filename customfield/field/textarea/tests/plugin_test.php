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

namespace customfield_textarea;

use core_customfield_generator;
use core_customfield_test_instance_form;
use context_user;
use context_course;
use context_system;

/**
 * Functional test for customfield_textarea
 *
 * @package    customfield_textarea
 * @copyright  2019 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \customfield_textarea\field_controller
 * @covers     \customfield_textarea\data_controller
 */
class plugin_test extends \advanced_testcase {

    /** @var \stdClass[] */
    private $courses = [];
    /** @var \core_customfield\category_controller */
    private $cfcat;
    /** @var \core_customfield\field_controller[] */
    private $cfields;
    /** @var \core_customfield\data_controller[] */
    private $cfdata;

    /**
     * Tests set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();

        $this->cfcat = $this->get_generator()->create_category();

        $this->cfields[1] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcat->get('id'), 'shortname' => 'myfield1', 'type' => 'textarea']);
        $this->cfields[2] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcat->get('id'), 'shortname' => 'myfield2', 'type' => 'textarea',
                'configdata' => ['required' => 1]]);
        $this->cfields[3] = $this->get_generator()->create_field(
            ['categoryid' => $this->cfcat->get('id'), 'shortname' => 'myfield3', 'type' => 'textarea',
                'configdata' => ['defaultvalue' => 'Value3', 'defaultvalueformat' => FORMAT_MOODLE]]);

        $this->courses[1] = $this->getDataGenerator()->create_course();
        $this->courses[2] = $this->getDataGenerator()->create_course();
        $this->courses[3] = $this->getDataGenerator()->create_course();

        $this->cfdata[1] = $this->get_generator()->add_instance_data($this->cfields[1], $this->courses[1]->id,
            ['text' => 'Value1', 'format' => FORMAT_MOODLE]);
        $this->cfdata[2] = $this->get_generator()->add_instance_data($this->cfields[1], $this->courses[2]->id,
            ['text' => '<br />', 'format' => FORMAT_MOODLE]);

        $this->setUser($this->getDataGenerator()->create_user());
    }

    /**
     * Get generator
     * @return core_customfield_generator
     */
    protected function get_generator(): core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Test for initialising field and data controllers
     */
    public function test_initialise() {
        $f = \core_customfield\field_controller::create($this->cfields[1]->get('id'));
        $this->assertTrue($f instanceof field_controller);

        $f = \core_customfield\field_controller::create(0, (object)['type' => 'textarea'], $this->cfcat);
        $this->assertTrue($f instanceof field_controller);

        $d = \core_customfield\data_controller::create($this->cfdata[1]->get('id'));
        $this->assertTrue($d instanceof data_controller);

        $d = \core_customfield\data_controller::create(0, null, $this->cfields[1]);
        $this->assertTrue($d instanceof data_controller);
    }

    /**
     * Test for configuration form functions
     *
     * Create a configuration form and submit it with the same values as in the field
     */
    public function test_config_form() {
        $this->setAdminUser();
        $submitdata = (array)$this->cfields[3]->to_record();
        $submitdata['configdata'] = $this->cfields[3]->get('configdata');

        $submitdata = \core_customfield\field_config_form::mock_ajax_submit($submitdata);
        $form = new \core_customfield\field_config_form(null, null, 'post', '', null, true,
            $submitdata, true);
        $form->set_data_for_dynamic_submission();
        $this->assertTrue($form->is_validated());
        $form->process_dynamic_submission();
    }

    /**
     * Test for instance form functions
     */
    public function test_instance_form() {
        global $CFG;
        require_once($CFG->dirroot . '/customfield/tests/fixtures/test_instance_form.php');
        $this->setAdminUser();
        $handler = $this->cfcat->get_handler();

        // First try to submit without required field.
        $submitdata = (array)$this->courses[1];
        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('POST',
            ['handler' => $handler, 'instance' => $this->courses[1]]);
        $this->assertFalse($form->is_validated());

        // Now with required field.
        $submitdata['customfield_myfield2_editor'] = ['text' => 'Some text', 'format' => FORMAT_HTML];
        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('POST',
            ['handler' => $handler, 'instance' => $this->courses[1]]);
        $this->assertTrue($form->is_validated());

        $data = $form->get_data();
        $this->assertNotEmpty($data->customfield_myfield1_editor);
        $this->assertNotEmpty($data->customfield_myfield2_editor);
        $handler->instance_form_save($data);
    }

    /**
     * Test that instance form save empties the field content for blank values
     */
    public function test_instance_form_save_clear(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/customfield/tests/fixtures/test_instance_form.php");

        $this->setAdminUser();

        $handler = $this->cfcat->get_handler();

        // Set our custom field to a known value.
        $submitdata = (array) $this->courses[1] + [
            'customfield_myfield1_editor' => ['text' => 'I can see it in your eyes', 'format' => FORMAT_HTML],
            'customfield_myfield2_editor' => ['text' => 'I can see it in your smile', 'format' => FORMAT_HTML],
        ];

        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('post', ['handler' => $handler, 'instance' => $this->courses[1]]);
        $handler->instance_form_save($form->get_data());

        $this->assertEquals($submitdata['customfield_myfield1_editor']['text'],
            \core_customfield\data_controller::create($this->cfdata[1]->get('id'))->export_value());

        // Now empty our non-required field.
        $submitdata['customfield_myfield1_editor']['text'] = '';

        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('post', ['handler' => $handler, 'instance' => $this->courses[1]]);
        $handler->instance_form_save($form->get_data());

        $this->assertNull(\core_customfield\data_controller::create($this->cfdata[1]->get('id'))->export_value());
    }

    /**
     * Test for data_controller::get_value and export_value
     */
    public function test_get_export_value() {
        $this->assertEquals('Value1', $this->cfdata[1]->get_value());
        $this->assertEquals('<div class="text_to_html">Value1</div>', $this->cfdata[1]->export_value());

        // Field with empty data.
        $this->assertNull($this->cfdata[2]->export_value());

        // Field without data but with a default value.
        $d = \core_customfield\data_controller::create(0, null, $this->cfields[3]);
        $this->assertEquals('Value3', $d->get_value());
        $this->assertEquals('<div class="text_to_html">Value3</div>', $d->export_value());
    }

    /**
     * Deleting fields and data
     */
    public function test_delete() {
        $this->cfcat->get_handler()->delete_all();
    }

    /**
     * Test embedded file backup and restore.
     *
     * @covers \customfield_textarea\data_controller::backup_define_structure
     * @covers \customfield_textarea\data_controller::backup_restore_structure
     */
    public function test_embedded_file_backup_and_restore(): void {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . '/customfield/tests/fixtures/test_instance_form.php');
        $this->setAdminUser();
        $handler = $this->cfcat->get_handler();

        // Create a file.
        $fs = get_file_storage();
        $filerecord = [
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => 'mytextfile.txt',
        ];
        $fs->create_file_from_string($filerecord, 'Some text contents');

        // Add the file to the custom field.
        $submitdata = (array) $this->courses[1];
        $submitdata['customfield_myfield1_editor'] = [
            'text' => 'Here is a file: @@PLUGINFILE@@/mytextfile.txt',
            'format' => FORMAT_HTML,
            'itemid' => $filerecord['itemid'],
        ];

        // Set the required field and submit.
        $submitdata['customfield_myfield2_editor'] = ['text' => 'Some text', 'format' => FORMAT_HTML];
        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('POST',
            ['handler' => $handler, 'instance' => $this->courses[1]]);
        $this->assertTrue($form->is_validated());

        $data = $form->get_data();
        $this->assertNotEmpty($data->customfield_myfield1_editor);
        $this->assertNotEmpty($data->customfield_myfield2_editor);
        $handler->instance_form_save($data);

        // Check if the draft file exists.
        $context = context_course::instance($this->courses[1]->id);
        $file = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'], $filerecord['itemid'],
            $filerecord['filepath'], $filerecord['filename']);
        $this->assertNotEmpty($file);

        // Check if the permanent file exists.
        $file = $fs->get_file($context->id, 'customfield_textarea', 'value', $this->cfdata[1]->get('id'), '/', 'mytextfile.txt');
        $this->assertNotEmpty($file);

        // Backup and restore the course.
        $backupid = $this->backup($this->courses[1]);
        $newcourseid = $this->restore($backupid, $this->courses[1], '_copy');

        $newcontext = context_course::instance($newcourseid);

        $newcfdata = $DB->get_record('customfield_data', ['instanceid' => $newcourseid, 'fieldid' => $this->cfields[1]->get('id')]);

        // Check if the permanent file exists in the new course after restore.
        $file = $fs->get_file($newcontext->id, 'customfield_textarea', 'value', $newcfdata->id, '/', 'mytextfile.txt');
        $this->assertNotEmpty($file);
    }

    /**
     * Backs a course up to temp directory.
     *
     * @param \stdClass $course Course object to backup
     * @return string ID of backup
     */
    protected function backup($course): string {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id,
            \backup::FORMAT_MOODLE, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
            $USER->id);
        $bc->get_plan()->get_setting('users')->set_status(\backup_setting::NOT_LOCKED);
        $bc->get_plan()->get_setting('users')->set_value(true);
        $bc->get_plan()->get_setting('logs')->set_value(true);
        $backupid = $bc->get_backupid();

        $bc->execute_plan();
        $bc->destroy();
        return $backupid;
    }

    /**
     * Restores a course from temp directory.
     *
     * @param string $backupid Backup id
     * @param \stdClass $course Original course object
     * @param string $suffix Suffix to add after original course shortname and fullname
     * @return int New course id
     * @throws \restore_controller_exception
     */
    protected function restore(string $backupid, $course, string $suffix): int {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        // Do restore to new course with default settings.
        $newcourseid = \restore_dbops::create_new_course(
            $course->fullname . $suffix, $course->shortname . $suffix, $course->category);
        $rc = new \restore_controller($backupid, $newcourseid,
            \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id,
            \backup::TARGET_NEW_COURSE);
        $rc->get_plan()->get_setting('logs')->set_value(true);
        $rc->get_plan()->get_setting('users')->set_value(true);

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }
}
