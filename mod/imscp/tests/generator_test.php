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

namespace mod_imscp;

/**
 * Genarator tests class for mod_imscp.
 *
 * @package    mod_imscp
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends \advanced_testcase {

    public function test_create_instance(): void {
        global $DB, $CFG, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse($DB->record_exists('imscp', array('course' => $course->id)));
        $imscp = $this->getDataGenerator()->create_module('imscp', array('course' => $course));
        $records = $DB->get_records('imscp', array('course' => $course->id), 'id');
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($imscp->id, $records));

        $params = array('course' => $course->id, 'name' => 'Another imscp');
        $imscp = $this->getDataGenerator()->create_module('imscp', $params);
        $records = $DB->get_records('imscp', array('course' => $course->id), 'id');
        $this->assertEquals(2, count($records));
        $this->assertEquals('Another imscp', $records[$imscp->id]->name);

        // Examples of specifying the package file (do not validate anything, just check for exceptions).
        // 1. As path to the file in filesystem...
        $params = array(
            'course' => $course->id,
            'packagepath' => $CFG->dirroot.'/mod/imscp/tests/packages/singlescobasic.zip'
        );
        $imscp = $this->getDataGenerator()->create_module('imscp', $params);

        // 2. As file draft area id...
        $fs = get_file_storage();
        $params = array(
            'course' => $course->id,
            'package' => file_get_unused_draft_itemid()
        );
        $usercontext = \context_user::instance($USER->id);
        $filerecord = array('component' => 'user', 'filearea' => 'draft',
                'contextid' => $usercontext->id, 'itemid' => $params['package'],
                'filename' => 'singlescobasic.zip', 'filepath' => '/');
        $fs->create_file_from_pathname($filerecord, $CFG->dirroot.'/mod/imscp/tests/packages/singlescobasic.zip');
        $imscp = $this->getDataGenerator()->create_module('imscp', $params);
    }
}
