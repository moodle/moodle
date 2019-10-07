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
 * External comment functions unit tests
 *
 * @package    core_comment
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External comment functions unit tests
 *
 * @package    core_comment
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_comment_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;

        require_once($CFG->dirroot . '/comment/lib.php');
    }

    /**
     * Test get_comments
     */
    public function test_get_comments() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $CFG->usecomments = true;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecomment' => 1));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $record = new stdClass();
        $record->course = $course->id;
        $record->name = "Mod data  test";
        $record->intro = "Some intro of some sort";
        $record->comments = 1;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $field = data_get_field_new('text', $module);

        $fielddetail = new stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        $context = context_module::instance($module->cmid);

        $this->setUser($user);

        // We need to add the comments manually, the comment API uses the global OUTPUT and this is going to make the WS to fail.
        $newcmt = new stdClass;
        $timecreated = time();
        $newcmt->contextid    = $context->id;
        $newcmt->commentarea  = 'database_entry';
        $newcmt->itemid       = $recordid;
        $newcmt->content      = 'New comment';
        $newcmt->format       = 0;
        $newcmt->userid       = $user->id;
        $newcmt->timecreated  = $timecreated;
        $cmtid1 = $DB->insert_record('comments', $newcmt);

        $newcmt->content  = 'New comment 2';
        $newcmt->timecreated  = $timecreated;
        $cmtid2 = $DB->insert_record('comments', $newcmt);

        $contextlevel = 'module';
        $instanceid = $cm->id;
        $component = 'mod_data';
        $itemid = $recordid;
        $area = 'database_entry';
        $page = 0;

        $result = core_comment_external::get_comments($contextlevel, $instanceid, $component, $itemid, $area, $page);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_comment_external::get_comments_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['comments']);

        $this->assertEquals($user->id, $result['comments'][0]['userid']);
        $this->assertEquals($user->id, $result['comments'][1]['userid']);

        $this->assertEquals($cmtid2, $result['comments'][0]['id']);
        $this->assertEquals($cmtid1, $result['comments'][1]['id']);
    }
}
