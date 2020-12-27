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
        $this->assertEquals(1, $glossaries['glossaries'][0]['canaddentry']);
        $this->assertEquals(1, $glossaries['glossaries'][1]['canaddentry']);

        // Check results with specific course IDs.
        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($c1->id, $c2->id));
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);

        $this->assertCount(2, $glossaries['glossaries']);
        $this->assertEquals('First Glossary', $glossaries['glossaries'][0]['name']);
        $this->assertEquals('Second Glossary', $glossaries['glossaries'][1]['name']);

        $this->assertEquals('course', $glossaries['warnings'][0]['item']);
        $this->assertEquals($c2->id, $glossaries['warnings'][0]['itemid']);
        $this->assertEquals('1', $glossaries['warnings'][0]['warningcode']);
        $this->assertEquals(1, $glossaries['glossaries'][0]['canaddentry']);

        // Now as admin.
        $this->setAdminUser();

        $glossaries = mod_glossary_external::get_glossaries_by_courses(array($c2->id));
        $glossaries = external_api::clean_returnvalue(mod_glossary_external::get_glossaries_by_courses_returns(), $glossaries);

        $this->assertCount(1, $glossaries['glossaries']);
        $this->assertEquals('Third Glossary', $glossaries['glossaries'][0]['name']);
        $this->assertEquals(1, $glossaries['glossaries'][0]['canaddentry']);
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
        $this->expectException(require_login_exception::class);
        $this->expectExceptionMessage('Activity is hidden');
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
            // All good.
        }

        // Test non-readable entry.
        $this->expectException(require_login_exception::class);
        $this->expectExceptionMessage('Activity is hidden');
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

        $e1a = $gg->create_content($g1, array('approved' => 0, 'concept' => 'Bob', 'userid' => 2, 'tags' => array('Cats', 'Dogs')));
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
        $this->assertEquals('Cats', $return['entries'][1]['tags'][0]['rawname']);
        $this->assertEquals('Dogs', $return['entries'][1]['tags'][1]['rawname']);
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
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'b', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e1b->id, $return['entries'][0]['id']);

        // Requesting special letters.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'SPECIAL', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);

        // Requesting with limit.
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_letter_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);
        $return = mod_glossary_external::get_entries_by_letter($g1->id, 'ALL', 1, 2, array());
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
            'timecreated' => 1, 'timemodified' => $now + 3600, 'tags' => array('Cats', 'Dogs')));
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
        $this->assertEquals('Cats', $return['entries'][0]['tags'][0]['rawname']);
        $this->assertEquals('Dogs', $return['entries'][0]['tags'][1]['rawname']);
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

    public function test_get_entries_by_category() {
        $this->resetAfterTest(true);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);

        $e1a1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id, 'tags' => array('Cats', 'Dogs')));
        $e1a2 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1a3 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1b1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1b2 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u1->id));
        $e1x1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1x2 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u1->id));
        $e2a1 = $gg->create_content($g2, array('approved' => 1, 'userid' => $u1->id));
        $e2a2 = $gg->create_content($g2, array('approved' => 1, 'userid' => $u1->id));

        $cat1a = $gg->create_category($g1, array('name' => 'Fish'), array($e1a1, $e1a2, $e1a3));
        $cat1b = $gg->create_category($g1, array('name' => 'Cat'), array($e1b1, $e1b2));
        $cat1c = $gg->create_category($g1, array('name' => 'Zebra'), array($e1b1));   // Entry $e1b1 is in two categories.
        $cat2a = $gg->create_category($g2, array(), array($e2a1, $e2a2));

        $this->setAdminUser();

        // Browse one category.
        $return = mod_glossary_external::get_entries_by_category($g1->id, $cat1a->id, 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_category_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a1->id, $return['entries'][0]['id']);
        $this->assertEquals('Cats', $return['entries'][0]['tags'][0]['rawname']);
        $this->assertEquals('Dogs', $return['entries'][0]['tags'][1]['rawname']);
        $this->assertEquals($e1a2->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][2]['id']);

        // Browse all categories.
        $return = mod_glossary_external::get_entries_by_category($g1->id, GLOSSARY_SHOW_ALL_CATEGORIES, 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_category_returns(), $return);
        $this->assertCount(5, $return['entries']);
        $this->assertEquals(5, $return['count']);
        $this->assertEquals($e1b1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][3]['id']);
        $this->assertEquals($e1b1->id, $return['entries'][4]['id']);

        // Browse uncategorised.
        $return = mod_glossary_external::get_entries_by_category($g1->id, GLOSSARY_SHOW_NOT_CATEGORISED, 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_category_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e1x1->id, $return['entries'][0]['id']);

        // Including to approve.
        $return = mod_glossary_external::get_entries_by_category($g1->id, $cat1b->id, 0, 20,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_category_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($e1b1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1b2->id, $return['entries'][1]['id']);

        // Using limit.
        $return = mod_glossary_external::get_entries_by_category($g1->id, GLOSSARY_SHOW_ALL_CATEGORIES, 0, 3,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_category_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1b1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1b2->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][2]['id']);
        $return = mod_glossary_external::get_entries_by_category($g1->id, GLOSSARY_SHOW_ALL_CATEGORIES, 3, 2,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_category_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1a2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][1]['id']);
    }

    public function test_get_authors() {
        $this->resetAfterTest(true);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));

        $u1 = $this->getDataGenerator()->create_user(array('lastname' => 'Upsilon'));
        $u2 = $this->getDataGenerator()->create_user(array('lastname' => 'Alpha'));
        $u3 = $this->getDataGenerator()->create_user(array('lastname' => 'Omega'));

        $ctx = context_module::instance($g1->cmid);

        $e1a = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 1));
        $e1b = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 1));
        $e1c = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 1));
        $e2a = $gg->create_content($g1, array('userid' => $u2->id, 'approved' => 1));
        $e3a = $gg->create_content($g1, array('userid' => $u3->id, 'approved' => 0));

        $this->setAdminUser();

        // Simple request.
        $return = mod_glossary_external::get_authors($g1->id, 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_authors_returns(), $return);
        $this->assertCount(2, $return['authors']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($u2->id, $return['authors'][0]['id']);
        $this->assertEquals($u1->id, $return['authors'][1]['id']);

        // Include users with entries pending approval.
        $return = mod_glossary_external::get_authors($g1->id, 0, 20, array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_authors_returns(), $return);
        $this->assertCount(3, $return['authors']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($u2->id, $return['authors'][0]['id']);
        $this->assertEquals($u3->id, $return['authors'][1]['id']);
        $this->assertEquals($u1->id, $return['authors'][2]['id']);

        // Pagination.
        $return = mod_glossary_external::get_authors($g1->id, 1, 1, array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_authors_returns(), $return);
        $this->assertCount(1, $return['authors']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($u3->id, $return['authors'][0]['id']);
    }

    public function test_get_entries_by_author() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $u1 = $this->getDataGenerator()->create_user(array('lastname' => 'Upsilon', 'firstname' => 'Zac'));
        $u2 = $this->getDataGenerator()->create_user(array('lastname' => 'Ultra', 'firstname' => '1337'));
        $u3 = $this->getDataGenerator()->create_user(array('lastname' => 'Alpha', 'firstname' => 'Omega'));
        $u4 = $this->getDataGenerator()->create_user(array('lastname' => '0-day', 'firstname' => 'Zoe'));
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $e1a1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1a2 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1a3 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id));
        $e1b1 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u2->id));
        $e1b2 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u2->id, 'tags' => array('Cats', 'Dogs')));
        $e1c1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u3->id));
        $e1d1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u4->id));
        $e2a = $gg->create_content($g2, array('approved' => 1, 'userid' => $u1->id));

        $this->setUser($u1);

        // Requesting a single letter.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'u', 'LASTNAME', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1b2->id, $return['entries'][0]['id']);
        $this->assertEquals('Cats', $return['entries'][0]['tags'][0]['rawname']);
        $this->assertEquals('Dogs', $return['entries'][0]['tags'][1]['rawname']);
        $this->assertEquals($e1a1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][3]['id']);

        // Requesting special letters.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'SPECIAL', 'LASTNAME', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e1d1->id, $return['entries'][0]['id']);

        // Requesting with limit.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'ALL', 'LASTNAME', 'ASC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1d1->id, $return['entries'][0]['id']);
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'ALL', 'LASTNAME', 'ASC', 1, 2, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1c1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1b2->id, $return['entries'][1]['id']);

        // Including non-approved.
        $this->setAdminUser();
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'ALL', 'LASTNAME', 'ASC', 0, 20,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(7, $return['entries']);
        $this->assertEquals(7, $return['count']);
        $this->assertEquals($e1d1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1b1->id, $return['entries'][2]['id']);
        $this->assertEquals($e1b2->id, $return['entries'][3]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][4]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][5]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][6]['id']);

        // Changing order.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'ALL', 'LASTNAME', 'DESC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1a1->id, $return['entries'][0]['id']);

        // Sorting by firstname.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'ALL', 'FIRSTNAME', 'ASC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1b2->id, $return['entries'][0]['id']);

        // Sorting by firstname descending.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'ALL', 'FIRSTNAME', 'DESC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(6, $return['count']);
        $this->assertEquals($e1d1->id, $return['entries'][0]['id']);

        // Filtering by firstname descending.
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'z', 'FIRSTNAME', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1d1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][3]['id']);

        // Test with a deleted user.
        delete_user($u2);
        $return = mod_glossary_external::get_entries_by_author($g1->id, 'u', 'LASTNAME', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1b2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][3]['id']);
    }

    public function test_get_entries_by_author_id() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id, 'displayformat' => 'entrylist'));
        $u1 = $this->getDataGenerator()->create_user(array('lastname' => 'Upsilon', 'firstname' => 'Zac'));
        $u2 = $this->getDataGenerator()->create_user(array('lastname' => 'Ultra', 'firstname' => '1337'));
        $u3 = $this->getDataGenerator()->create_user(array('lastname' => 'Alpha', 'firstname' => 'Omega'));
        $u4 = $this->getDataGenerator()->create_user(array('lastname' => '0-day', 'firstname' => 'Zoe'));
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $e1a1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id, 'concept' => 'Zoom',
            'timecreated' => 3600, 'timemodified' => time() - 3600));
        $e1a2 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id, 'concept' => 'Alpha'));
        $e1a3 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id, 'concept' => 'Dog',
            'timecreated' => 1, 'timemodified' => time() - 1800));
        $e1a4 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u1->id, 'concept' => 'Bird'));
        $e1b1 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u2->id));
        $e2a = $gg->create_content($g2, array('approved' => 1, 'userid' => $u1->id));

        $this->setAdminUser();

        // Standard request.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CONCEPT', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][2]['id']);

        // Standard request descending.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CONCEPT', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);

        // Requesting ordering by time created.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CREATION', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a3->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);

        // Requesting ordering by time created descending.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CREATION', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][2]['id']);

        // Requesting ordering by time modified.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'UPDATE', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a1->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a2->id, $return['entries'][2]['id']);

        // Requesting ordering by time modified descending.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'UPDATE', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1a2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][2]['id']);

        // Including non approved.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CONCEPT', 'ASC', 0, 20,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1a2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a4->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][2]['id']);
        $this->assertEquals($e1a1->id, $return['entries'][3]['id']);

        // Pagination.
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CONCEPT', 'ASC', 0, 2,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1a2->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a4->id, $return['entries'][1]['id']);
        $return = mod_glossary_external::get_entries_by_author_id($g1->id, $u1->id, 'CONCEPT', 'ASC', 1, 2,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_author_id_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1a4->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a3->id, $return['entries'][1]['id']);
    }

    public function test_get_entries_by_search() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->setUser($u1);

        $e1 = $gg->create_content($g1, array('approved' => 1, 'concept' => 'House', 'timecreated' => time() + 3600));
        $e2 = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Mouse', 'timemodified' => 1));
        $e3 = $gg->create_content($g1, array('approved' => 1, 'concept' => 'Hero', 'tags' => array('Cats', 'Dogs')));
        $e4 = $gg->create_content($g1, array('approved' => 0, 'concept' => 'Toulouse'));
        $e5 = $gg->create_content($g1, array('approved' => 1, 'definition' => 'Heroes', 'concept' => 'Abcd'));
        $e6 = $gg->create_content($g1, array('approved' => 0, 'definition' => 'When used for Heroes'));
        $e7 = $gg->create_content($g1, array('approved' => 1, 'timecreated' => 1, 'timemodified' => time() + 3600,
            'concept' => 'Z'), array('Couscous'));
        $e8 = $gg->create_content($g1, array('approved' => 0), array('Heroes'));
        $e9 = $gg->create_content($g2, array('approved' => 0));

        $this->setAdminUser();

        // Test simple query.
        $query = 'hero';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'CONCEPT', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e3->id, $return['entries'][0]['id']);
        $this->assertEquals('Cats', $return['entries'][0]['tags'][0]['rawname']);
        $this->assertEquals('Dogs', $return['entries'][0]['tags'][1]['rawname']);

        // Enabling full search.
        $query = 'hero';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, true, 'CONCEPT', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($e5->id, $return['entries'][0]['id']);
        $this->assertEquals($e3->id, $return['entries'][1]['id']);

        // Concept descending.
        $query = 'hero';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, true, 'CONCEPT', 'DESC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($e3->id, $return['entries'][0]['id']);
        $this->assertEquals($e5->id, $return['entries'][1]['id']);

        // Search on alias.
        $query = 'couscous';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'CONCEPT', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e7->id, $return['entries'][0]['id']);
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, true, 'CONCEPT', 'ASC', 0, 20, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e7->id, $return['entries'][0]['id']);

        // Pagination and ordering on created date.
        $query = 'ou';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'CREATION', 'ASC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e7->id, $return['entries'][0]['id']);
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'CREATION', 'DESC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e1->id, $return['entries'][0]['id']);

        // Ordering on updated date.
        $query = 'ou';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'UPDATE', 'ASC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e2->id, $return['entries'][0]['id']);
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'UPDATE', 'DESC', 0, 1, array());
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
        $this->assertEquals($e7->id, $return['entries'][0]['id']);

        // Including not approved.
        $query = 'ou';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, false, 'CONCEPT', 'ASC', 0, 20,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1->id, $return['entries'][0]['id']);
        $this->assertEquals($e2->id, $return['entries'][1]['id']);
        $this->assertEquals($e4->id, $return['entries'][2]['id']);
        $this->assertEquals($e7->id, $return['entries'][3]['id']);

        // Advanced query string.
        $query = '+Heroes -Abcd';
        $return = mod_glossary_external::get_entries_by_search($g1->id, $query, true, 'CONCEPT', 'ASC', 0, 20,
            array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_search_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        $this->assertEquals($e6->id, $return['entries'][0]['id']);
        $this->assertEquals($e8->id, $return['entries'][1]['id']);
    }

    public function test_get_entries_by_term() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $this->setAdminUser();

        $e1 = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 1, 'concept' => 'cat',
            'tags' => array('Cats', 'Dogs')));
        $e2 = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 1), array('cat', 'dog'));
        $e3 = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 1), array('dog'));
        $e4 = $gg->create_content($g1, array('userid' => $u1->id, 'approved' => 0, 'concept' => 'dog'));
        $e5 = $gg->create_content($g2, array('userid' => $u1->id, 'approved' => 1, 'concept' => 'dog'), array('cat'));

        // Search concept + alias.
        $return = mod_glossary_external::get_entries_by_term($g1->id, 'cat', 0, 20, array('includenotapproved' => false));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_term_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        // Compare ids, ignore ordering of array, using canonicalize parameter of assertEquals.
        $expected = array($e1->id, $e2->id);
        $actual = array($return['entries'][0]['id'], $return['entries'][1]['id']);
        $this->assertEqualsCanonicalizing($expected, $actual);
        // Compare rawnames of all expected tags, ignore ordering of array, using canonicalize parameter of assertEquals.
        $expected = array('Cats', 'Dogs'); // Only $e1 has 2 tags.
        $actual = array(); // Accumulate all tags returned.
        foreach ($return['entries'] as $entry) {
            foreach ($entry['tags'] as $tag) {
                $actual[] = $tag['rawname'];
            }
        }
        $this->assertEqualsCanonicalizing($expected, $actual);

        // Search alias.
        $return = mod_glossary_external::get_entries_by_term($g1->id, 'dog', 0, 20, array('includenotapproved' => false));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_term_returns(), $return);

        $this->assertCount(2, $return['entries']);
        $this->assertEquals(2, $return['count']);
        // Compare ids, ignore ordering of array, using canonicalize parameter of assertEquals.
        $expected = array($e2->id, $e3->id);
        $actual = array($return['entries'][0]['id'], $return['entries'][1]['id']);
        $this->assertEqualsCanonicalizing($expected, $actual);

        // Search including not approved.
        $return = mod_glossary_external::get_entries_by_term($g1->id, 'dog', 0, 20, array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_term_returns(), $return);
        $this->assertCount(3, $return['entries']);
        $this->assertEquals(3, $return['count']);
        // Compare ids, ignore ordering of array, using canonicalize parameter of assertEquals.
        $expected = array($e4->id, $e2->id, $e3->id);
        $actual = array($return['entries'][0]['id'], $return['entries'][1]['id'], $return['entries'][2]['id']);
        $this->assertEqualsCanonicalizing($expected, $actual);

        // Pagination.
        $return = mod_glossary_external::get_entries_by_term($g1->id, 'dog', 0, 1, array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_term_returns(), $return);
        $this->assertCount(1, $return['entries']);
        // We don't compare the returned entry id because it may be different depending on the DBMS,
        // for example, Postgres does a random sorting in this case.
        $this->assertEquals(3, $return['count']);
        $return = mod_glossary_external::get_entries_by_term($g1->id, 'dog', 1, 1, array('includenotapproved' => true));
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_by_term_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(3, $return['count']);
    }

    public function test_get_entries_to_approve() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        $e1a = $gg->create_content($g1, array('approved' => 0, 'concept' => 'Bob', 'userid' => $u1->id,
            'timecreated' => time() + 3600));
        $e1b = $gg->create_content($g1, array('approved' => 0, 'concept' => 'Jane', 'userid' => $u1->id, 'timecreated' => 1));
        $e1c = $gg->create_content($g1, array('approved' => 0, 'concept' => 'Alice', 'userid' => $u1->id, 'timemodified' => 1));
        $e1d = $gg->create_content($g1, array('approved' => 0, 'concept' => '0-day', 'userid' => $u1->id,
            'timemodified' => time() + 3600));
        $e1e = $gg->create_content($g1, array('approved' => 1, 'concept' => '1-day', 'userid' => $u1->id));
        $e2a = $gg->create_content($g2);

        $this->setAdminUser(true);

        // Simple listing.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CONCEPT', 'ASC', 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $this->assertEquals($e1a->id, $return['entries'][2]['id']);
        $this->assertEquals($e1b->id, $return['entries'][3]['id']);

        // Revert ordering of concept.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CONCEPT', 'DESC', 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(4, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1b->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a->id, $return['entries'][1]['id']);
        $this->assertEquals($e1c->id, $return['entries'][2]['id']);
        $this->assertEquals($e1d->id, $return['entries'][3]['id']);

        // Filtering by letter.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'a', 'CONCEPT', 'ASC', 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e1c->id, $return['entries'][0]['id']);

        // Filtering by special.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'SPECIAL', 'CONCEPT', 'ASC', 0, 20);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(1, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);

        // Pagination.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CONCEPT', 'ASC', 0, 2);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);
        $this->assertEquals($e1c->id, $return['entries'][1]['id']);
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CONCEPT', 'ASC', 1, 2);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(2, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1c->id, $return['entries'][0]['id']);
        $this->assertEquals($e1a->id, $return['entries'][1]['id']);

        // Ordering by creation date.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CREATION', 'ASC', 0, 1);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1b->id, $return['entries'][0]['id']);

        // Ordering by creation date desc.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CREATION', 'DESC', 0, 1);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1a->id, $return['entries'][0]['id']);

        // Ordering by update date.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'UPDATE', 'ASC', 0, 1);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1c->id, $return['entries'][0]['id']);

        // Ordering by update date desc.
        $return = mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'UPDATE', 'DESC', 0, 1);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entries_to_approve_returns(), $return);
        $this->assertCount(1, $return['entries']);
        $this->assertEquals(4, $return['count']);
        $this->assertEquals($e1d->id, $return['entries'][0]['id']);

        // Permissions are checked.
        $this->setUser($u1);
        $this->expectException('required_capability_exception');
        mod_glossary_external::get_entries_to_approve($g1->id, 'ALL', 'CONCEPT', 'ASC', 0, 1);
        $this->fail('Do not test anything else after this.');
    }

    public function test_get_entry_by_id() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $g2 = $this->getDataGenerator()->create_module('glossary', array('course' => $c2->id, 'visible' => 0));
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u2->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u3->id, $c1->id);

        $e1 = $gg->create_content($g1, array('approved' => 1, 'userid' => $u1->id, 'tags' => array('Cats', 'Dogs')));
        // Add a fake inline image to the entry.
        $filename = 'shouldbeanimage.jpg';
        $filerecordinline = array(
            'contextid' => $ctx->id,
            'component' => 'mod_glossary',
            'filearea'  => 'entry',
            'itemid'    => $e1->id,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        $e2 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u1->id));
        $e3 = $gg->create_content($g1, array('approved' => 0, 'userid' => $u2->id));
        $e4 = $gg->create_content($g2, array('approved' => 1));

        $this->setUser($u1);
        $return = mod_glossary_external::get_entry_by_id($e1->id);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entry_by_id_returns(), $return);
        $this->assertEquals($e1->id, $return['entry']['id']);
        $this->assertEquals('Cats', $return['entry']['tags'][0]['rawname']);
        $this->assertEquals('Dogs', $return['entry']['tags'][1]['rawname']);
        $this->assertEquals($filename, $return['entry']['definitioninlinefiles'][0]['filename']);
        $this->assertTrue($return['permissions']['candelete']);

        $return = mod_glossary_external::get_entry_by_id($e2->id);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entry_by_id_returns(), $return);
        $this->assertEquals($e2->id, $return['entry']['id']);
        $this->assertTrue($return['permissions']['candelete']);

        try {
            $return = mod_glossary_external::get_entry_by_id($e3->id);
            $this->fail('Cannot view unapproved entries of others.');
        } catch (invalid_parameter_exception $e) {
            // All good.
        }

        try {
            $return = mod_glossary_external::get_entry_by_id($e4->id);
            $this->fail('Cannot view entries from another course.');
        } catch (require_login_exception $e) {
            // All good.
        }

        // An admin can see other's entries to be approved.
        $this->setAdminUser();
        $return = mod_glossary_external::get_entry_by_id($e3->id);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entry_by_id_returns(), $return);
        $this->assertEquals($e3->id, $return['entry']['id']);
        $this->assertTrue($return['permissions']['candelete']);

        // Students can see other students approved entries but they will not be able to delete them.
        $this->setUser($u3);
        $return = mod_glossary_external::get_entry_by_id($e1->id);
        $return = external_api::clean_returnvalue(mod_glossary_external::get_entry_by_id_returns(), $return);
        $this->assertEquals($e1->id, $return['entry']['id']);
        $this->assertFalse($return['permissions']['candelete']);
    }

    public function test_add_entry_without_optional_settings() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = '<p>A definition</p>';
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);

        // Get entry from DB.
        $entry = $DB->get_record('glossary_entries', array('id' => $return['entryid']));

        $this->assertEquals($concept, $entry->concept);
        $this->assertEquals($definition, $entry->definition);
        $this->assertEquals($CFG->glossary_linkentries, $entry->usedynalink);
        $this->assertEquals($CFG->glossary_casesensitive, $entry->casesensitive);
        $this->assertEquals($CFG->glossary_fullmatch, $entry->fullmatch);
        $this->assertEmpty($DB->get_records('glossary_alias', array('entryid' => $return['entryid'])));
        $this->assertEmpty($DB->get_records('glossary_entries_categories', array('entryid' => $return['entryid'])));
    }

    public function test_add_entry_with_aliases() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = 'A definition';
        $paramaliases = 'abc, def, gez';
        $options = array(
            array(
                'name' => 'aliases',
                'value' => $paramaliases,
            )
        );
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);

        $aliases = $DB->get_records('glossary_alias', array('entryid' => $return['entryid']));
        $this->assertCount(3, $aliases);
        foreach ($aliases as $alias) {
            $this->assertStringContainsString($alias->alias, $paramaliases);
        }
    }

    public function test_add_entry_in_categories() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $cat1 = $gg->create_category($glossary);
        $cat2 = $gg->create_category($glossary);

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = 'A definition';
        $paramcategories = "$cat1->id, $cat2->id";
        $options = array(
            array(
                'name' => 'categories',
                'value' => $paramcategories,
            )
        );
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);

        $categories = $DB->get_records('glossary_entries_categories', array('entryid' => $return['entryid']));
        $this->assertCount(2, $categories);
        foreach ($categories as $category) {
            $this->assertStringContainsString($category->categoryid, $paramcategories);
        }
    }

    public function test_add_entry_with_attachments() {
        global $DB, $USER;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));
        $context = context_module::instance($glossary->cmid);

        $this->setAdminUser();
        $concept = 'A concept';
        $definition = 'A definition';

        // Draft files.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $draftidattach = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($USER->id);
        $filerecordinline = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => 'shouldbeanimage.txt',
        );
        $fs = get_file_storage();

        // Create a file in a draft area for regular attachments.
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $options = array(
            array(
                'name' => 'inlineattachmentsid',
                'value' => $draftidinlineattach,
            ),
            array(
                'name' => 'attachmentsid',
                'value' => $draftidattach,
            )
        );
        $return = mod_glossary_external::add_entry($glossary->id, $concept, $definition, FORMAT_HTML, $options);
        $return = external_api::clean_returnvalue(mod_glossary_external::add_entry_returns(), $return);

        $editorfiles = external_util::get_area_files($context->id, 'mod_glossary', 'entry', $return['entryid']);
        $attachmentfiles = external_util::get_area_files($context->id, 'mod_glossary', 'attachment', $return['entryid']);

        $this->assertCount(1, $editorfiles);
        $this->assertCount(1, $attachmentfiles);

        $this->assertEquals('shouldbeanimage.txt', $editorfiles[0]['filename']);
        $this->assertEquals('attachment.txt', $attachmentfiles[0]['filename']);
    }

    /**
     *   Test get entry including rating information.
     */
    public function test_get_entry_rating_information() {
        $this->resetAfterTest(true);

        global $DB, $CFG;
        require_once($CFG->dirroot . '/rating/lib.php');

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Create course to add the module.
        $course = self::getDataGenerator()->create_course();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create the glossary and contents.
        $record = new stdClass();
        $record->course = $course->id;
        $record->assessed = RATING_AGGREGATE_AVERAGE;
        $scale = $this->getDataGenerator()->create_scale(array('scale' => 'A,B,C,D'));
        $record->scale = "-$scale->id";
        $glossary = $this->getDataGenerator()->create_module('glossary', $record);
        $context = context_module::instance($glossary->cmid);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry = $gg->create_content($glossary, array('approved' => 1, 'userid' => $user1->id));

        // Rate the entry as user2.
        $rating1 = new stdClass();
        $rating1->contextid = $context->id;
        $rating1->component = 'mod_glossary';
        $rating1->ratingarea = 'entry';
        $rating1->itemid = $entry->id;
        $rating1->rating = 1; // 1 is A.
        $rating1->scaleid = "-$scale->id";
        $rating1->userid = $user2->id;
        $rating1->timecreated = time();
        $rating1->timemodified = time();
        $rating1->id = $DB->insert_record('rating', $rating1);

        // Rate the entry as user3.
        $rating2 = new stdClass();
        $rating2->contextid = $context->id;
        $rating2->component = 'mod_glossary';
        $rating2->ratingarea = 'entry';
        $rating2->itemid = $entry->id;
        $rating2->rating = 3; // 3 is C.
        $rating2->scaleid = "-$scale->id";
        $rating2->userid = $user3->id;
        $rating2->timecreated = time() + 1;
        $rating2->timemodified = time() + 1;
        $rating2->id = $DB->insert_record('rating', $rating2);

        // As student, retrieve ratings information.
        $this->setUser($user1);
        $result = mod_glossary_external::get_entry_by_id($entry->id);
        $result = external_api::clean_returnvalue(mod_glossary_external::get_entry_by_id_returns(), $result);
        $this->assertCount(1, $result['ratinginfo']['ratings']);
        $this->assertFalse($result['ratinginfo']['ratings'][0]['canviewaggregate']);
        $this->assertFalse($result['ratinginfo']['canviewall']);
        $this->assertFalse($result['ratinginfo']['ratings'][0]['canrate']);
        $this->assertTrue(!isset($result['ratinginfo']['ratings'][0]['count']));

        // Now, as teacher, I should see the info correctly.
        $this->setUser($teacher);
        $result = mod_glossary_external::get_entry_by_id($entry->id);
        $result = external_api::clean_returnvalue(mod_glossary_external::get_entry_by_id_returns(), $result);
        $this->assertCount(1, $result['ratinginfo']['ratings']);
        $this->assertTrue($result['ratinginfo']['ratings'][0]['canviewaggregate']);
        $this->assertTrue($result['ratinginfo']['canviewall']);
        $this->assertTrue($result['ratinginfo']['ratings'][0]['canrate']);
        $this->assertEquals(2, $result['ratinginfo']['ratings'][0]['count']);
        $this->assertEquals(2, $result['ratinginfo']['ratings'][0]['aggregate']);   // 2 is B, that is the average of A + C.
    }
}
