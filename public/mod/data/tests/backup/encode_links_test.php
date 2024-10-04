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

namespace mod_data\backup;

/**
 * Tests for Database
 *
 * @package    mod_data
 * @category   test
 * @copyright  2025 ISB Bayern
 * @author     Stefan Hanauska <stefan.hanauska@csg-in.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class encode_links_test extends \advanced_testcase {
    /**
     * Test that links are encoded correctly.
     *
     * @return void
     *
     * @covers       \backup_data_activity_task::encode_content_links
     * @covers       \restore_data_activity_task::define_decode_rules
     */
    public function test_encode_links(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Make a test course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $newcourse = $generator->create_course();
        $data = $this->getDataGenerator()->create_module('data', ['course' => $course->id]);
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $field = $datagenerator->create_field(
            (object) ['name' => 'field', 'type' => 'text'],
            $data
        );

        $entry = [$field->field->id => 'test'];
        $datagenerator->create_entry($data, $entry);

        $data->intro = $CFG->wwwroot . '/mod/data/view.php?id=' . $data->cmid . '|';
        $data->intro .= urlencode($CFG->wwwroot . '/mod/data/view.php?id='. $data->cmid) . '|';
        $data->intro .= $CFG->wwwroot . '/mod/data/view.php?d=' . $data->id . '|';
        $data->intro .= urlencode($CFG->wwwroot . '/mod/data/view.php?d='. $data->id) . '|';
        $data->intro .= $CFG->wwwroot . '/mod/data/index.php?id=' . $data->course . '|';
        $data->intro .= urlencode($CFG->wwwroot . '/mod/data/index.php?id=' . $data->course) . '|';
        $data->intro .= $CFG->wwwroot . '/mod/data/edit.php?id=' . $data->cmid . '|';
        $data->intro .= urlencode($CFG->wwwroot . '/mod/data/edit.php?id='. $data->cmid) . '|';
        $data->intro .= $CFG->wwwroot . '/mod/data/edit.php?d=' . $data->id . '|';
        $data->intro .= urlencode($CFG->wwwroot . '/mod/data/edit.php?d=' . $data->id) . '|';

        $DB->update_record('data', $data);

        // Duplicate the data module with the type.
        $newcm = duplicate_module($course, get_fast_modinfo($course)->get_cm($data->cmid));

        $newdata = $DB->get_record('data', ['id' => $newcm->instance]);

        $expected = $CFG->wwwroot . '/mod/data/view.php?id=' . $newcm->id . '|';
        $expected .= urlencode($CFG->wwwroot . '/mod/data/view.php?id=' . $newcm->id) . '|';
        $expected .= $CFG->wwwroot . '/mod/data/view.php?d=' . $newdata->id . '|';
        $expected .= urlencode($CFG->wwwroot . '/mod/data/view.php?d=' . $newdata->id) . '|';
        $expected .= $CFG->wwwroot . '/mod/data/index.php?id=' . $newcm->course . '|';
        $expected .= urlencode($CFG->wwwroot . '/mod/data/index.php?id=' . $newcm->course) . '|';
        $expected .= $CFG->wwwroot . '/mod/data/edit.php?id=' . $newcm->id . '|';
        $expected .= urlencode($CFG->wwwroot . '/mod/data/edit.php?id='. $newcm->id) . '|';
        $expected .= $CFG->wwwroot . '/mod/data/edit.php?d=' . $newdata->id . '|';
        $expected .= urlencode($CFG->wwwroot . '/mod/data/edit.php?d=' . $newdata->id) . '|';

        $this->assertEquals($expected, $newdata->intro);
    }
}
