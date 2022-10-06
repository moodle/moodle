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

namespace mod_data;

use mod_data\local\importer\preset_existing_importer;
use mod_data\local\importer\preset_upload_importer;

/**
 * Preset importer tests class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\local\importer\preset_importer
 */
class preset_importer_test extends \advanced_testcase {

    /**
     * Test for needs_mapping method.
     *
     * @covers ::needs_mapping
     */
    public function test_needs_mapping() {
        global $CFG, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $manager = manager::create_from_instance($activity);

        // Create presets and importers.
        $pluginname = 'imagegallery';
        $plugin = preset::create_from_plugin(null, $pluginname);
        $pluginimporter = new preset_existing_importer($manager, '/' . $pluginname);

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($activity, $record);
        $savedimporter = new preset_existing_importer($manager, $USER->id . '/Testing preset name');

        $fixturepath = $CFG->dirroot . '/mod/data/tests/fixtures/image_gallery_preset.zip';

        // Create a storage file.
        $draftid = file_get_unused_draft_itemid();
        $filerecord = [
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => \context_user::instance($USER->id)->id,
            'itemid' => $draftid,
            'filename' => 'image_gallery_preset.zip',
            'filepath' => '/'
        ];
        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname($filerecord, $fixturepath);
        $uploadedimporter = new preset_upload_importer($manager, $file->get_filepath());

        // Needs mapping returns false for empty databases.
        $this->assertFalse($pluginimporter->needs_mapping());
        $this->assertFalse($savedimporter->needs_mapping());
        $this->assertFalse($uploadedimporter->needs_mapping());

        // Add a field to the database.
        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field1';
        $fieldrecord->type = 'text';
        $plugingenerator->create_field($fieldrecord, $activity);

        // Needs mapping returns true for non-empty databases.
        $this->assertTrue($pluginimporter->needs_mapping());
        $this->assertTrue($savedimporter->needs_mapping());
        $this->assertTrue($uploadedimporter->needs_mapping());
    }
}
