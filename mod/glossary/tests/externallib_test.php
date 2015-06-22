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
 * External glossary functions unit tests
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
/**
 * External glossary functions unit tests
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_glossary_externallib_testcase extends externallib_advanced_testcase {
    /**
     * Test get_glossaries_by_courses
     */
    public function test_get_glossaries_by_courses() {
        global $DB, $USER;
        $this->resetAfterTest(true);
        // As admin.
        $this->setAdminUser();
        $course1 = self::getDataGenerator()->create_course();
        $glossaryoptions1 = array(
                              'course' => $course1->id,
                              'name' => 'First Glossary'
                             );
        $glossary1 = self::getDataGenerator()->create_module('glossary', $glossaryoptions1);
        $course2 = self::getDataGenerator()->create_course();
        $glossaryoptions2 = array(
                              'course' => $course2->id,
                              'name' => 'Second Glossary'
                             );
        $glossary2 = self::getDataGenerator()->create_module('glossary', $glossaryoptions2);
        $student1 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        // Enroll Student1 in Course1.
        self::getDataGenerator()->enrol_user($student1->id,  $course1->id, $studentrole->id);
        $this->setUser($student1);
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array());
        // We need to execute the return values cleaning process to simulate the web service server.
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);
        $this->assertCount(1, $glossaries['glossaries']);
        $this->assertEquals('First Glossary', $glossaries['glossaries'][0]['name']);
        // As Student you cannot see some glossary properties like 'showunanswered'.
        $this->assertFalse(isset($glossaries['glossaries'][0]['section']));
        // Student1 is not enrolled in this Course.
        // The webservice will give a warning!
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($course2->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);
        $this->assertCount(0, $glossaries['glossaries']);
        $this->assertEquals(1, $glossaries['warnings'][0]['warningcode']);
        // Now as admin.
        $this->setAdminUser();
        // As Admin we can see this glossary.
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($course2->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);
        $this->assertCount(1, $glossaries['glossaries']);
        $this->assertEquals('Second Glossary', $glossaries['glossaries'][0]['name']);
        // As an Admin you can see some glossary properties like 'section'.
        $this->assertEquals(0, $glossaries['glossaries'][0]['section']);
        $this->setUser($student1);
        // Prohibit capability = mod:glossary:view on Course1 for students.
        $contextcourse1 = context_course::instance($course1->id);
        assign_capability('mod/glossary:view', CAP_PROHIBIT, $studentrole->id, $contextcourse1->id);
        accesslib_clear_all_caches_for_unit_testing();
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($course1->id));
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);
        $this->assertEquals(2, $glossaries['warnings'][0]['warningcode']);
    }
}
