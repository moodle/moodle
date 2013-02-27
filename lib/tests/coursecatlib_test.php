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
 * Tests for class coursecat from lib/coursecatlib.php
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/coursecatlib.php');

/**
 * Functional test for accesslib.php
 *
 * Note: execution may take many minutes especially on slower servers.
 */
class coursecatlib_testcase extends advanced_testcase {

    var $roles;

    public function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
    }

    protected function get_roleid($context = null) {
        global $USER;
        if ($context === null) {
            $context = context_system::instance();
        }
        if (is_object($context)) {
            $context = $context->id;
        }
        if (empty($this->roles)) {
            $this->roles = array();
        }
        if (empty($this->roles[$USER->id])) {
            $this->roles[$USER->id] = array();
        }
        if (empty($this->roles[$USER->id][$context])) {
            $this->roles[$USER->id][$context] = create_role('Role for '.$USER->id.' in '.$context, 'role'.$USER->id.'-'.$context, '-');
            role_assign($this->roles[$USER->id][$context], $USER->id, $context);
        }
        return $this->roles[$USER->id][$context];
    }

    protected function assign_capability($capability, $permission = CAP_ALLOW, $contextid = null) {
        if ($contextid === null) {
            $contextid = context_system::instance();
        }
        if (is_object($contextid)) {
            $contextid = $contextid->id;
        }
        assign_capability($capability, $permission, $this->get_roleid($contextid), $contextid, true);
        accesslib_clear_all_caches_for_unit_testing();
    }

    public function test_create_coursecat() {
        // Create the category
        $data = new stdClass();
        $data->name = 'aaa';
        $data->description = 'aaa';
        $data->idnumber = '';

        $category1 = coursecat::create($data);

        // Initially confirm that base data was inserted correctly
        $this->assertEquals($data->name, $category1->name);
        $this->assertEquals($data->description, $category1->description);
        $this->assertEquals($data->idnumber, $category1->idnumber);

        $this->assertGreaterThanOrEqual(1, $category1->sortorder);

        // Create two more categories and test the sortorder worked correctly
        $data->name = 'ccc';
        $category2 = coursecat::create($data);

        $data->name = 'bbb';
        $category3 = coursecat::create($data);

        $this->assertGreaterThan($category1->sortorder, $category2->sortorder);
        $this->assertGreaterThan($category2->sortorder, $category3->sortorder);
    }

    public function test_name_idnumber_exceptions() {
        try {
            coursecat::create(array('name' => ''));
            $this->fail('Missing category name exception expected in coursecat::create');
        } catch (moodle_exception $e) {
        }
        $cat1 = coursecat::create(array('name' => 'Cat1', 'idnumber' => '1'));
        try {
            $cat1->update(array('name' => ''));
            $this->fail('Missing category name exception expected in coursecat::update');
        } catch (moodle_exception $e) {
        }
        try {
            coursecat::create(array('name' => 'Cat2', 'idnumber' => '1'));
            $this->fail('Duplicate idnumber exception expected in coursecat::create');
        } catch (moodle_exception $e) {
        }
        $cat2 = coursecat::create(array('name' => 'Cat2', 'idnumber' => '2'));
        try {
            $cat2->update(array('idnumber' => '1'));
            $this->fail('Duplicate idnumber exception expected in coursecat::update');
        } catch (moodle_exception $e) {
        }
    }

    public function test_visibility() {
        $this->assign_capability('moodle/category:viewhiddencategories');
        $this->assign_capability('moodle/category:manage');

        // create category 1 initially hidden
        $category1 = coursecat::create(array('name' => 'Cat1', 'visible' => 0));
        $this->assertEquals(0, $category1->visible);
        $this->assertEquals(0, $category1->visibleold);

        // create category 2 initially hidden as a child of hidden category 1
        $category2 = coursecat::create(array('name' => 'Cat2', 'visible' => 0, 'parent' => $category1->id));
        $this->assertEquals(0, $category2->visible);
        $this->assertEquals(0, $category2->visibleold);

        // create category 3 initially visible as a child of hidden category 1
        $category3 = coursecat::create(array('name' => 'Cat3', 'visible' => 1, 'parent' => $category1->id));
        $this->assertEquals(0, $category3->visible);
        $this->assertEquals(1, $category3->visibleold);

        // show category 1 and make sure that category 2 is hidden and category 3 is visible
        $category1->show();
        $this->assertEquals(1, coursecat::get($category1->id)->visible);
        $this->assertEquals(0, coursecat::get($category2->id)->visible);
        $this->assertEquals(1, coursecat::get($category3->id)->visible);

        // create visible category 4
        $category4 = coursecat::create(array('name' => 'Cat4'));
        $this->assertEquals(1, $category4->visible);
        $this->assertEquals(1, $category4->visibleold);

        // create visible category 5 as a child of visible category 4
        $category5 = coursecat::create(array('name' => 'Cat5', 'parent' => $category4->id));
        $this->assertEquals(1, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // hide category 4 and make sure category 5 is hidden too
        $category4->hide();
        $this->assertEquals(0, $category4->visible);
        $this->assertEquals(0, $category4->visibleold);
        $category5 = coursecat::get($category5->id); // we have to re-read from DB
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // show category 4 and make sure category 5 is visible too
        $category4->show();
        $this->assertEquals(1, $category4->visible);
        $this->assertEquals(1, $category4->visibleold);
        $category5 = coursecat::get($category5->id); // we have to re-read from DB
        $this->assertEquals(1, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // move category 5 under hidden category 2 and make sure it became hidden
        $category5->change_parent($category2->id);
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // re-read object for category 5 from DB and check again
        $category5 = coursecat::get($category5->id);
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // tricky one! Move hidden category 5 under visible category ("Top") and make sure it is still hidden
        // WHY? Well, different people may expect different behaviour here. So better keep it hidden
        $category5->change_parent(0);
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);
    }

    public function test_hierarchy() {
        $this->assign_capability('moodle/category:viewhiddencategories');
        $this->assign_capability('moodle/category:manage');

        $category1 = coursecat::create(array('name' => 'Cat1'));
        $category2 = coursecat::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = coursecat::create(array('name' => 'Cat3', 'parent' => $category1->id));
        $category4 = coursecat::create(array('name' => 'Cat4', 'parent' => $category2->id));

        // check function get_children()
        $this->assertEquals(array($category2->id, $category3->id), array_keys($category1->get_children()));
        // check function get_parents()
        $this->assertEquals(array($category1->id, $category2->id), $category4->get_parents());

        // can not move category to itself or to it's children
        $this->assertFalse($category1->can_change_parent($category2->id));
        $this->assertFalse($category2->can_change_parent($category2->id));
        // can move category to grandparent
        $this->assertTrue($category4->can_change_parent($category1->id));

        try {
            $category2->change_parent($category4->id);
            $this->fail('Exception expected - can not move category');
        } catch (moodle_exception $e) {
        }

        $category4->change_parent(0);
        $this->assertEquals(array(), $category4->get_parents());
        $this->assertEquals(array($category2->id, $category3->id), array_keys($category1->get_children()));
        $this->assertEquals(array(), array_keys($category2->get_children()));
    }

    public function test_update() {
        $category1 = coursecat::create(array('name' => 'Cat1'));
        $timecreated = $category1->timemodified;
        $this->assertEquals('Cat1', $category1->name);
        $this->assertTrue(empty($category1->description));
        sleep(2);
        $testdescription = 'This is cat 1 а также русский текст';
        $category1->update(array('description' => $testdescription));
        $this->assertEquals($testdescription, $category1->description);
        $category1 = coursecat::get($category1->id);
        $this->assertEquals($testdescription, $category1->description);
        coursecat::purge_cache();
        $category1 = coursecat::get($category1->id);
        $this->assertEquals($testdescription, $category1->description);

        $this->assertGreaterThan($timecreated, $category1->timemodified);
    }

    public function test_delete() {
        global $DB;

        $this->assign_capability('moodle/category:manage');
        $this->assign_capability('moodle/course:create');

        $initialcatid = $DB->get_field_sql('SELECT max(id) from {course_categories}');

        $category1 = coursecat::create(array('name' => 'Cat1'));
        $category2 = coursecat::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = coursecat::create(array('name' => 'Cat3'));
        $category4 = coursecat::create(array('name' => 'Cat4', 'parent' => $category2->id));

        $course1 = $this->getDataGenerator()->create_course(array('category' => $category2->id));
        $course2 = $this->getDataGenerator()->create_course(array('category' => $category4->id));
        $course3 = $this->getDataGenerator()->create_course(array('category' => $category4->id));
        $course4 = $this->getDataGenerator()->create_course(array('category' => $category1->id));

        // Now we have
        // $category1
        //   $category2
        //      $category4
        //        $course2
        //        $course3
        //      $course1
        //   $course4
        // $category3

        // Login as another user to test course:delete capability (user who created course can delete it within 24h even without cap)
        $this->setUser($this->getDataGenerator()->create_user());

        // Delete category 2 and move content to category 3
        $this->assertFalse($category2->can_move_content_to($category3->id)); // no luck!
        // add necessary capabilities
        $this->assign_capability('moodle/course:create', CAP_ALLOW, context_coursecat::instance($category3->id));
        $this->assign_capability('moodle/category:manage');
        $this->assertTrue($category2->can_move_content_to($category3->id)); // hurray!
        $category2->delete_move($category3->id);

        // Make sure we have:
        // $category1
        //   $course4
        // $category3
        //    $category4
        //      $course2
        //      $course3
        //    $course1

        $this->assertNull(coursecat::get($category2->id, IGNORE_MISSING, true));
        $this->assertEquals(array(), $category1->get_children());
        $this->assertEquals(array($category4->id), array_keys($category3->get_children()));
        $this->assertEquals($category4->id, $DB->get_field('course', 'category', array('id' => $course2->id)));
        $this->assertEquals($category4->id, $DB->get_field('course', 'category', array('id' => $course3->id)));
        $this->assertEquals($category3->id, $DB->get_field('course', 'category', array('id' => $course1->id)));

        // Delete category 3 completely
        $this->assertFalse($category3->can_delete_full()); // no luck!
        // add necessary capabilities
        $this->assign_capability('moodle/course:delete', CAP_ALLOW, context_coursecat::instance($category3->id));
        $this->assertTrue($category3->can_delete_full()); // hurray!
        $category3->delete_full();

        // Make sure we have:
        // $category1
        //   $course4

        // Note that we also have default 'Miscellaneous' category and default 'site' course
        $this->assertEquals(1, $DB->get_field_sql('SELECT count(*) FROM {course_categories} WHERE id > ?', array($initialcatid)));
        $this->assertEquals($category1->id, $DB->get_field_sql('SELECT max(id) FROM {course_categories}'));
        $this->assertEquals(1, $DB->get_field_sql('SELECT count(*) FROM {course} WHERE id <> ?', array(SITEID)));
        $this->assertEquals(array('id' => $course4->id, 'category' => $category1->id),
                (array)$DB->get_record_sql('SELECT id, category from {course} where id <> ?', array(SITEID)));
    }
}