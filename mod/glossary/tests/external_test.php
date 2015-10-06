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
class mod_glossary_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_glossaries_by_courses
     */
    public function test_get_glossaries_by_courses() {
        $this->resetAfterTest(true);

        // As admin.
        $this->setAdminUser();
        $c1 = self::getDataGenerator()->create_course();
        $c2 = self::getDataGenerator()->create_course();
        $g1 = self::getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'name' => 'First Glossary'));
        $g2 = self::getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'name' => 'Second Glossary'));
        $g3 = self::getDataGenerator()->create_module('glossary', array('course' => $c2->id, 'name' => 'Third Glossary'));

        $s1 = $this->getDataGenerator()->create_user();
        self::getDataGenerator()->enrol_user($s1->id,  $c1->id);

        // Check results where student is enrolled.
        $this->setUser($s1);
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array());
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);

        $this->assertCount(2, $glossaries['glossaries']);
        $this->assertEquals('First Glossary', $glossaries['glossaries'][0]['name']);
        $this->assertEquals('Second Glossary', $glossaries['glossaries'][1]['name']);

        // Check results with specific course IDs.
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($c1->id, $c2->id));
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);

        $this->assertCount(2, $glossaries['glossaries']);
        $this->assertEquals('First Glossary', $glossaries['glossaries'][0]['name']);
        $this->assertEquals('Second Glossary', $glossaries['glossaries'][1]['name']);

        $this->assertEquals('course', $glossaries['warnings'][0]['item']);
        $this->assertEquals($c2->id, $glossaries['warnings'][0]['itemid']);
        $this->assertEquals('1', $glossaries['warnings'][0]['warningcode']);

        // Now as admin.
        $this->setAdminUser();

        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($c2->id));
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);

        $this->assertCount(1, $glossaries['glossaries']);
        $this->assertEquals('Third Glossary', $glossaries['glossaries'][0]['name']);
    }

}
