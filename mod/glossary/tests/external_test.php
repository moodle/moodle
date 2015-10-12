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

    public function test_view_glossary() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $sink = $this->redirectEvents();
        $this->setUser($u1);
        $return = mod_glossary_external::view_glossary($g1->id, 'letter');
        $return = external_api::clean_returnvalue(mod_glossary_external::view_glossary_returns(), $return);
        $events = $sink->get_events();

        // Assertion.
        $this->assertTrue($return['status']);
        $this->assertEmpty($return['warnings']);
        $this->assertCount(1, $events);
        $this->assertEquals('\mod_glossary\event\course_module_viewed', $events[0]->eventname);
        $sink->close();
    }

    public function test_view_glossary_without_permission() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $ctx = context_module::instance($g1->cmid);

        // Revoke permission.
        $roles = get_archetype_roles('user');
        $role = array_shift($roles);
        assign_capability('mod/glossary:view', CAP_PROHIBIT, $role->id, $ctx, true);
        accesslib_clear_all_caches_for_unit_testing();

        // Assertion.
        $this->setUser($u1);
        $this->setExpectedException('require_login_exception', 'Activity is hidden');
        mod_glossary_external::view_glossary($g1->id, 'letter');
    }

    public function test_view_entry() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'visible' => false));
        $u1 = $this->getDataGenerator()->create_user();
        $e1 = $gg->create_content($g1, array('approved' => 1));
        $e2 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u1->id));
        $e3 = $gg->create_content($g1, array('approved' => 0, 'userid' => -1));
        $e4 = $gg->create_content($g2, array('approved' => 1));
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->setUser($u1);

        // Test readable entry.
        $sink = $this->redirectEvents();
        $return = mod_glossary_external::view_entry($e1->id);
        $return = external_api::clean_returnvalue(mod_glossary_external::view_entry_returns(), $return);
        $events = $sink->get_events();
        $this->assertTrue($return['status']);
        $this->assertEmpty($return['warnings']);
        $this->assertCount(1, $events);
        $this->assertEquals('\mod_glossary\event\entry_viewed', $events[0]->eventname);
        $sink->close();

        // Test non-approved of self.
        $return = mod_glossary_external::view_entry($e2->id);
        $return = external_api::clean_returnvalue(mod_glossary_external::view_entry_returns(), $return);
        $events = $sink->get_events();
        $this->assertTrue($return['status']);
        $this->assertEmpty($return['warnings']);
        $this->assertCount(1, $events);
        $this->assertEquals('\mod_glossary\event\entry_viewed', $events[0]->eventname);
        $sink->close();

        // Test non-approved of other.
        try {
            mod_glossary_external::view_entry($e3->id);
            $this->fail('Cannot view non-approved entries of others.');
        } catch (invalid_parameter_exception $e) {
        }

        // Test non-readable entry.
        $this->setExpectedException('require_login_exception', 'Activity is hidden');
        mod_glossary_external::view_entry($e4->id);
    }

    public function test_get_entries_by_letter() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $e1a = $gg->create_content($g1, array('approved' => 0, 'concept' => 'Bob', 'userid' => 2));
        $e1b = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Jane', 'userid' => 2));
        $e1c = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Alice', 'userid' => $u1->id));
        $e1d = $gg->create_content($g1, array('approved' => 0, 'concept' => '0-day', 'userid' => $u1->id));
        $e2a = $gg->create_content($g2);

        $this->setAdminUser();

        // Just a normal request from admin user.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1c->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a->id, $return['entries'][1]['id']);
        $this->assertEquals($e1b->id, $return['entries'][2]['id']);

        // An admin user requesting all the entries.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 0, 20, array('includenotapproved' => 1));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a->id, $return['entries'][2]['id']);
        $this->assertEquals($e1b->id, $return['entries'][3]['id']);

        // A normal user.
        $this->setUser($u1);
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1b->id, $return['entries'][2]['id']);

        // A normal user requesting to view all non approved entries.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 0, 20, array('includenotapproved' => 1));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1b->id, $return['entries'][2]['id']);
    }

    public function test_get_entries_by_letter_with_parameters() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $e1a = $gg->create_content($g1, array('approved' => 1, 'concept' => '0-day', 'userid' => $u1->id));
        $e1b = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Bob', 'userid' => 2));
        $e1c = $gg->create_content($g1, array('approved' => 1, 'concept' => '1-dayb', 'userid' => $u1->id));

        $this->setUser($u1);

        // Requesting a single letter.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'b', 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e1b->id, $return['entries'][0]['id']);

        // Requesting special letters.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'SPECIAL', 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);

        // Requesting with limit.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 0, 1);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 1, 2);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1c->id, $return['entries'][0]['id']);
        $this->assertEquals($e1b->id, $return['entries'][1]['id']);
    }

    public function test_get_entries_by_date() {
        global $DB;
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $now = time();
        $e1a = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Bob', 'userid' => $u1->id,
            'timecreated' => 1, 'timemodified' => $now + 3600));
        $e1b = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Jane', 'userid' => $u1->id,
            'timecreated' => $now + 3600, 'timemodified' => 1));
        $e1c = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Alice', 'userid' => $u1->id,
            'timecreated' => $now + 1, 'timemodified' => $now + 1));
        $e1d = $gg->create_content($g1, array('approved' => 0, 'concept' => '0-day', 'userid' => $u1->id,
            'timecreated' => $now + 2, 'timemodified' => $now + 2));
        $e2a = $gg->create_content($g2);

        $this->setAdminUser($u1);

        // Ordering by time modified descending.
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'UPDATE', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1b->id, $return['entries'][2]['id']);

        // Ordering by time modified ascending.
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'UPDATE', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1b->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a->id, $return['entries'][2]['id']);

        // Ordering by time created asc.
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'CREATION', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1b->id, $return['entries'][2]['id']);

        // Ordering by time created descending.
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'CREATION', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1b->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a->id, $return['entries'][2]['id']);

        // Ordering including to approve.
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'CREATION', 'ASC', 0, 20,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1d->id, $return['entries'][2]['id']);
        $this->assertEquals($e1b->id, $return['entries'][3]['id']);

        // Ordering including to approve and pagination.
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'CREATION', 'ASC', 0, 2,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $return = mod_glossary_external::get_entries_by_date($g1->id, 'CREATION', 'ASC', 2, 2,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_date_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);
        $this->assertEquals($e1b->id, $return['entries'][1]['id']);
    }

    public function test_get_categories() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $cat1a = $gg->create_category($g1);
        $cat1b = $gg->create_category($g1);
        $cat1c = $gg->create_category($g1);
        $cat2a = $gg->create_category($g2);

        $return = mod_glossary_external::get_categories($g1->id, 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_categories_returns(), $return);
        $this->assertCount(3, $return['categories']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($cat1a->id, $return['categories'][0]['id']);
        $this->assertEquals($cat1b->id, $return['categories'][1]['id']);
        $this->assertEquals($cat1c->id, $return['categories'][2]['id']);

        $return = mod_glossary_external::get_categories($g1->id, 1, 2);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_categories_returns(), $return);
        $this->assertCount(2, $return['categories']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($cat1b->id, $return['categories'][0]['id']);
        $this->assertEquals($cat1c->id, $return['categories'][1]['id']);
    }

}
