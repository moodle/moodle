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

use stdClass;

/**
 * PHPUnit data generator testcase.
 *
 * @package    mod_data
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data_generator
 */
class generator_test extends \advanced_testcase {
    /**
     * @covers ::create_instance
     */
    public function test_generator() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('data'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $this->assertInstanceOf('mod_data_generator', $generator);
        $this->assertEquals('data', $generator->get_modulename());

        $generator->create_instance(['course' => $course->id]);
        $generator->create_instance(['course' => $course->id]);
        $data = $generator->create_instance(['course' => $course->id]);
        $this->assertEquals(3, $DB->count_records('data'));

        $cm = get_coursemodule_from_instance('data', $data->id);
        $this->assertEquals($data->id, $cm->instance);
        $this->assertEquals('data', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($data->cmid, $context->instanceid);

        // Test gradebook integration using low level DB access - DO NOT USE IN PLUGIN CODE!
        $data = $generator->create_instance(['course' => $course->id, 'assessed' => 1, 'scale' => 100]);
        $gitem = $DB->get_record('grade_items', [
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'data',
            'iteminstance' => $data->id,
        ]);
        $this->assertNotEmpty($gitem);
        $this->assertEquals(100, $gitem->grademax);
        $this->assertEquals(0, $gitem->grademin);
        $this->assertEquals(GRADE_TYPE_VALUE, $gitem->gradetype);
    }

    /**
     * @covers ::create_field
     */
    public function test_create_field() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        $this->assertEquals(0, $DB->count_records('data'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $this->assertInstanceOf('mod_data_generator', $generator);
        $this->assertEquals('data', $generator->get_modulename());

        $data = $generator->create_instance(['course' => $course->id]);
        $this->assertEquals(1, $DB->count_records('data'));

        $cm = get_coursemodule_from_instance('data', $data->id);
        $this->assertEquals($data->id, $cm->instance);
        $this->assertEquals('data', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($data->cmid, $context->instanceid);

        $fieldtypes = ['checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url'];

        $count = 1;

        // Creating test Fields with default parameter values.
        foreach ($fieldtypes as $fieldtype) {
            // Creating variables dynamically.
            $fieldname = 'field-' . $count;
            $record = new \stdClass();
            $record->name = $fieldname;
            $record->type = $fieldtype;

            ${$fieldname} = $this->getDataGenerator()->get_plugin_generator('mod_data')->create_field($record, $data);

            $this->assertInstanceOf('data_field_' . $fieldtype, ${$fieldname});
            $count++;
        }

        $this->assertEquals(count($fieldtypes), $DB->count_records('data_fields', ['dataid' => $data->id]));
    }

    /**
     * @covers ::create_entry
     */
    public function test_create_entry() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        $this->assertEquals(0, $DB->count_records('data'));

        $user1 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');

        $groupa = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'groupA']);
        $this->getDataGenerator()->create_group_member(['userid' => $user1->id, 'groupid' => $groupa->id]);

        /** @var mod_data_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $this->assertInstanceOf('mod_data_generator', $generator);
        $this->assertEquals('data', $generator->get_modulename());

        $data = $generator->create_instance(['course' => $course->id]);
        $this->assertEquals(1, $DB->count_records('data'));

        $cm = get_coursemodule_from_instance('data', $data->id);
        $this->assertEquals($data->id, $cm->instance);
        $this->assertEquals('data', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = \context_module::instance($cm->id);
        $this->assertEquals($data->cmid, $context->instanceid);

        $fieldtypes = ['checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url',
            'latlong', 'file', 'picture',
        ];

        $count = 1;

        // Creating test Fields with default parameter values.
        foreach ($fieldtypes as $fieldtype) {
            // Creating variables dynamically.
            $fieldname = 'field-' . $count;
            $record = new \stdClass();
            $record->name = $fieldname;
            $record->type = $fieldtype;
            $record->required = 1;

            $this->getDataGenerator()->get_plugin_generator('mod_data')->create_field($record, $data);
            $count++;
        }

        $fields = $DB->get_records('data_fields', ['dataid' => $data->id], 'id');

        $contents = [];
        $contents[] = ['opt1', 'opt2', 'opt3', 'opt4'];
        $contents[] = '01-01-2037'; // It should be lower than 2038, to avoid failing on 32-bit windows.
        $contents[] = 'menu1';
        $contents[] = ['multimenu1', 'multimenu2', 'multimenu3', 'multimenu4'];
        $contents[] = '12345';
        $contents[] = 'radioopt1';
        $contents[] = 'text for testing';
        $contents[] = '<p>text area testing<br /></p>';
        $contents[] = ['example.url', 'sampleurl'];
        $contents[] = [-31.9489873, 115.8382036]; // Latlong.
        $contents[] = 'Filename.pdf'; // File - filename.
        $contents[] = ['Cat1234.jpg', 'Cat']; // Picture - filename with alt text.
        $count = 0;
        $fieldcontents = [];
        foreach ($fields as $fieldrecord) {
            $fieldcontents[$fieldrecord->id] = $contents[$count++];
        }

        $tags = ['Cats', 'mice'];

        $this->setUser($user1);
        $datarecordid = $this->getDataGenerator()->get_plugin_generator('mod_data')->create_entry(
            $data,
            $fieldcontents,
            $groupa->id,
            $tags
        );

        $this->assertEquals(1, $DB->count_records('data_records', ['dataid' => $data->id]));
        $this->assertEquals(count($contents), $DB->count_records('data_content', ['recordid' => $datarecordid]));

        $entry = $DB->get_record('data_records', ['id' => $datarecordid]);
        $this->assertEquals($entry->groupid, $groupa->id);

        $contents = $DB->get_records('data_content', ['recordid' => $datarecordid], 'id');

        $contentstartid = 0;
        $flag = 0;
        foreach ($contents as $key => $content) {
            if (!$flag++) {
                $contentstartid = $key;
            }
            $this->assertFalse($content->content == null);
        }

        $this->assertEquals($contents[$contentstartid]->content, 'opt1##opt2##opt3##opt4');
        $this->assertEquals($contents[++$contentstartid]->content, '2114380800');
        $this->assertEquals($contents[++$contentstartid]->content, 'menu1');
        $this->assertEquals($contents[++$contentstartid]->content, 'multimenu1##multimenu2##multimenu3##multimenu4');
        $this->assertEquals($contents[++$contentstartid]->content, '12345');
        $this->assertEquals($contents[++$contentstartid]->content, 'radioopt1');
        $this->assertEquals($contents[++$contentstartid]->content, 'text for testing');
        $this->assertEquals($contents[++$contentstartid]->content, '<p>text area testing<br /></p>');
        $this->assertEquals($contents[$contentstartid]->content1, '1');
        $this->assertEquals($contents[++$contentstartid]->content, 'http://example.url');
        $this->assertEquals($contents[$contentstartid]->content1, 'sampleurl');
        $this->assertEquals(
            ['Cats', 'mice'],
            array_values(\core_tag_tag::get_item_tags_array('mod_data', 'data_records', $datarecordid))
        );
    }

    /**
     * Test for create_preset().
     *
     * @dataProvider create_preset_provider
     * @covers ::create_preset
     * @param stdClass|null $record data for the preset that will be created (like name or description)
     */
    public function test_create_preset(?stdClass $record) {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        $cm = get_coursemodule_from_id(manager::MODULE, $activity->cmid, 0, false, MUST_EXIST);
        if (!is_null($record) && property_exists($record, 'user')) {
            $user = $this->getDataGenerator()->create_and_enrol($course, 'teacher', (object) ['username' => $record->user]);
            $record->userid = $user->id;
            unset($record->user);
        }

        // Check initially there are no saved presets.
        $manager = manager::create_from_coursemodule($cm);
        $savedpresets = $manager->get_available_saved_presets();
        $this->assertEmpty($savedpresets);

        // Create one preset with the configuration in $record.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $preset = $plugingenerator->create_preset($activity, $record);
        // Check the preset has been saved.
        $savedpresets = $manager->get_available_saved_presets();
        $this->assertCount(1, $savedpresets);
        // Check the preset name has the expected value.
        if (is_null($record) || !property_exists($record, 'name')) {
            $this->assertStringStartsWith('New preset', $preset->name);
        } else {
            $this->assertEquals($record->name, $preset->name);
        }
        // Check the preset description has the expected value.
        if (is_null($record) || !property_exists($record, 'description')) {
            $this->assertEmpty($preset->description);
        } else {
            $this->assertEquals($record->description, $preset->description);
        }
        // Check the preset author has the expected value.
        if (is_null($record) || !property_exists($record, 'userid')) {
            $this->assertEquals($USER->id, $preset->get_userid());
        } else {
            $this->assertEquals($record->userid, $preset->get_userid());
        }
        // Check the file has been updated properly.
        $this->assertNotNull($preset->storedfile);
    }

    /**
     * Data provider for test_create_preset().
     *
     * @return array
     */
    public function create_preset_provider(): array {
        return [
            'Create using the default configuration' => [
                'record' => null,
            ],
            'Create with a given name but no description' => [
                'record' => (object) [
                    'name' => 'World recipes preset',
                ],
            ],
            'Create with a given description but no name' => [
                'record' => (object) [
                    'description' => 'This is a preset to collect the most popular world recipes.',
                ],
            ],
            'Create with a given name and description' => [
                'record' => (object) [
                    'name' => 'World recipes preset',
                    'description' => 'This is a preset to collect the most popular world recipes.',
                ],
            ],
            'Create with a given user but no description or name' => [
                'record' => (object) [
                    'user' => 'teacher1',
                ],
            ],
            'Create with a given name and user but no description' => [
                'record' => (object) [
                    'name' => 'World recipes preset',
                    'user' => 'teacher1',
                ],
            ],
            'Create with a given description and user but no name' => [
                'record' => (object) [
                    'description' => 'This is a preset to collect the most popular world recipes.',
                    'user' => 'teacher1',
                ],
            ],
            'Create with a given name, description and user' => [
                'record' => (object) [
                    'name' => 'World recipes preset',
                    'description' => 'This is a preset to collect the most popular world recipes.',
                    'user' => 'teacher1',
                ],
            ],
        ];
    }
}
