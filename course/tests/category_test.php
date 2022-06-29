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

namespace core_course;

use core_course_category;

/**
 * Tests for class core_course_category
 *
 * @package    core_course
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_test extends \advanced_testcase {

    protected $roles;

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
    }

    protected function get_roleid($context = null) {
        global $USER;
        if ($context === null) {
            $context = \context_system::instance();
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
            $contextid = \context_system::instance();
        }
        if (is_object($contextid)) {
            $contextid = $contextid->id;
        }
        assign_capability($capability, $permission, $this->get_roleid($contextid), $contextid, true);
        accesslib_clear_all_caches_for_unit_testing();
    }

    public function test_create_coursecat() {
        // Create the category.
        $data = new \stdClass();
        $data->name = 'aaa';
        $data->description = 'aaa';
        $data->idnumber = '';

        $category1 = core_course_category::create($data);

        // Initially confirm that base data was inserted correctly.
        $this->assertSame($data->name, $category1->name);
        $this->assertSame($data->description, $category1->description);
        $this->assertSame($data->idnumber, $category1->idnumber);

        $this->assertGreaterThanOrEqual(1, $category1->sortorder);

        // Create two more categories and test the sortorder worked correctly.
        $data->name = 'ccc';
        $category2 = core_course_category::create($data);

        $data->name = 'bbb';
        $category3 = core_course_category::create($data);

        $this->assertGreaterThan($category1->sortorder, $category2->sortorder);
        $this->assertGreaterThan($category2->sortorder, $category3->sortorder);
    }

    public function test_name_idnumber_exceptions() {
        try {
            core_course_category::create(array('name' => ''));
            $this->fail('Missing category name exception expected in core_course_category::create');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        $cat1 = core_course_category::create(array('name' => 'Cat1', 'idnumber' => '1'));
        try {
            $cat1->update(array('name' => ''));
            $this->fail('Missing category name exception expected in core_course_category::update');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        try {
            core_course_category::create(array('name' => 'Cat2', 'idnumber' => '1'));
            $this->fail('Duplicate idnumber exception expected in core_course_category::create');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        $cat2 = core_course_category::create(array('name' => 'Cat2', 'idnumber' => '2'));
        try {
            $cat2->update(array('idnumber' => '1'));
            $this->fail('Duplicate idnumber exception expected in core_course_category::update');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        // Test that duplicates with an idnumber of 0 cannot be created.
        core_course_category::create(array('name' => 'Cat3', 'idnumber' => '0'));
        try {
            core_course_category::create(array('name' => 'Cat4', 'idnumber' => '0'));
            $this->fail('Duplicate idnumber "0" exception expected in core_course_category::create');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
        // Test an update cannot make a duplicate idnumber of 0.
        try {
            $cat2->update(array('idnumber' => '0'));
            $this->fail('Duplicate idnumber "0" exception expected in core_course_category::update');
        } catch (\Exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }
    }

    public function test_visibility() {
        $this->assign_capability('moodle/category:viewhiddencategories');
        $this->assign_capability('moodle/category:manage');

        // Create category 1 initially hidden.
        $category1 = core_course_category::create(array('name' => 'Cat1', 'visible' => 0));
        $this->assertEquals(0, $category1->visible);
        $this->assertEquals(0, $category1->visibleold);

        // Create category 2 initially hidden as a child of hidden category 1.
        $category2 = core_course_category::create(array('name' => 'Cat2', 'visible' => 0, 'parent' => $category1->id));
        $this->assertEquals(0, $category2->visible);
        $this->assertEquals(0, $category2->visibleold);

        // Create category 3 initially visible as a child of hidden category 1.
        $category3 = core_course_category::create(array('name' => 'Cat3', 'visible' => 1, 'parent' => $category1->id));
        $this->assertEquals(0, $category3->visible);
        $this->assertEquals(1, $category3->visibleold);

        // Show category 1 and make sure that category 2 is hidden and category 3 is visible.
        $category1->show();
        $this->assertEquals(1, core_course_category::get($category1->id)->visible);
        $this->assertEquals(0, core_course_category::get($category2->id)->visible);
        $this->assertEquals(1, core_course_category::get($category3->id)->visible);

        // Create visible category 4.
        $category4 = core_course_category::create(array('name' => 'Cat4'));
        $this->assertEquals(1, $category4->visible);
        $this->assertEquals(1, $category4->visibleold);

        // Create visible category 5 as a child of visible category 4.
        $category5 = core_course_category::create(array('name' => 'Cat5', 'parent' => $category4->id));
        $this->assertEquals(1, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // Hide category 4 and make sure category 5 is hidden too.
        $category4->hide();
        $this->assertEquals(0, $category4->visible);
        $this->assertEquals(0, $category4->visibleold);
        $category5 = core_course_category::get($category5->id); // We have to re-read from DB.
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // Show category 4 and make sure category 5 is visible too.
        $category4->show();
        $this->assertEquals(1, $category4->visible);
        $this->assertEquals(1, $category4->visibleold);
        $category5 = core_course_category::get($category5->id); // We have to re-read from DB.
        $this->assertEquals(1, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // Move category 5 under hidden category 2 and make sure it became hidden.
        $category5->change_parent($category2->id);
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // Re-read object for category 5 from DB and check again.
        $category5 = core_course_category::get($category5->id);
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);

        // Rricky one! Move hidden category 5 under visible category ("Top") and make sure it is still hidden-
        // WHY? Well, different people may expect different behaviour here. So better keep it hidden.
        $category5->change_parent(0);
        $this->assertEquals(0, $category5->visible);
        $this->assertEquals(1, $category5->visibleold);
    }

    public function test_hierarchy() {
        $this->assign_capability('moodle/category:viewhiddencategories');
        $this->assign_capability('moodle/category:manage');

        $category1 = core_course_category::create(array('name' => 'Cat1'));
        $category2 = core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = core_course_category::create(array('name' => 'Cat3', 'parent' => $category1->id));
        $category4 = core_course_category::create(array('name' => 'Cat4', 'parent' => $category2->id));

        // Check function get_children().
        $this->assertEquals(array($category2->id, $category3->id), array_keys($category1->get_children()));
        // Check function get_parents().
        $this->assertEquals(array($category1->id, $category2->id), $category4->get_parents());

        // Can not move category to itself or to it's children.
        $this->assertFalse($category1->can_change_parent($category2->id));
        $this->assertFalse($category2->can_change_parent($category2->id));
        // Can move category to grandparent.
        $this->assertTrue($category4->can_change_parent($category1->id));

        try {
            $category2->change_parent($category4->id);
            $this->fail('Exception expected - can not move category');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }

        $category4->change_parent(0);
        $this->assertEquals(array(), $category4->get_parents());
        $this->assertEquals(array($category2->id, $category3->id), array_keys($category1->get_children()));
        $this->assertEquals(array(), array_keys($category2->get_children()));
    }

    public function test_update() {
        $category1 = core_course_category::create(array('name' => 'Cat1'));
        $timecreated = $category1->timemodified;
        $this->assertSame('Cat1', $category1->name);
        $this->assertTrue(empty($category1->description));
        $this->waitForSecond();
        $testdescription = 'This is cat 1 а также русский текст';
        $category1->update(array('description' => $testdescription));
        $this->assertSame($testdescription, $category1->description);
        $category1 = core_course_category::get($category1->id);
        $this->assertSame($testdescription, $category1->description);
        \cache_helper::purge_by_event('changesincoursecat');
        $category1 = core_course_category::get($category1->id);
        $this->assertSame($testdescription, $category1->description);

        $this->assertGreaterThan($timecreated, $category1->timemodified);
    }

    public function test_delete() {
        global $DB;

        $this->assign_capability('moodle/category:manage');
        $this->assign_capability('moodle/course:create');

        $initialcatid = $DB->get_field_sql('SELECT max(id) from {course_categories}');

        $category1 = core_course_category::create(array('name' => 'Cat1'));
        $category2 = core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = core_course_category::create(array('name' => 'Cat3'));
        $category4 = core_course_category::create(array('name' => 'Cat4', 'parent' => $category2->id));

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
        // structure.

        // Login as another user to test course:delete capability (user who created course can delete it within 24h even without cap).
        $this->setUser($this->getDataGenerator()->create_user());

        // Delete category 2 and move content to category 3.
        $this->assertFalse($category2->can_move_content_to($category3->id)); // No luck!
        // Add necessary capabilities.
        $this->assign_capability('moodle/course:create', CAP_ALLOW, \context_coursecat::instance($category3->id));
        $this->assign_capability('moodle/category:manage');
        $this->assertTrue($category2->can_move_content_to($category3->id)); // Hurray!
        $category2->delete_move($category3->id);

        // Make sure we have:
        // $category1
        //   $course4
        // $category3
        //    $category4
        //      $course2
        //      $course3
        //    $course1
        // structure.

        $this->assertNull(core_course_category::get($category2->id, IGNORE_MISSING, true));
        $this->assertEquals(array(), $category1->get_children());
        $this->assertEquals(array($category4->id), array_keys($category3->get_children()));
        $this->assertEquals($category4->id, $DB->get_field('course', 'category', array('id' => $course2->id)));
        $this->assertEquals($category4->id, $DB->get_field('course', 'category', array('id' => $course3->id)));
        $this->assertEquals($category3->id, $DB->get_field('course', 'category', array('id' => $course1->id)));

        // Delete category 3 completely.
        $this->assertFalse($category3->can_delete_full()); // No luck!
        // Add necessary capabilities.
        $this->assign_capability('moodle/course:delete', CAP_ALLOW, \context_coursecat::instance($category3->id));
        $this->assertTrue($category3->can_delete_full()); // Hurray!
        $category3->delete_full();

        // Make sure we have:
        // $category1
        //   $course4
        // structure.

        // Note that we also have default course category and default 'site' course.
        $this->assertEquals(1, $DB->get_field_sql('SELECT count(*) FROM {course_categories} WHERE id > ?', array($initialcatid)));
        $this->assertEquals($category1->id, $DB->get_field_sql('SELECT max(id) FROM {course_categories}'));
        $this->assertEquals(1, $DB->get_field_sql('SELECT count(*) FROM {course} WHERE id <> ?', array(SITEID)));
        $this->assertEquals(array('id' => $course4->id, 'category' => $category1->id),
                (array)$DB->get_record_sql('SELECT id, category from {course} where id <> ?', array(SITEID)));
    }

    public function test_get_children() {
        $category1 = core_course_category::create(array('name' => 'Cat1'));
        $category2 = core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = core_course_category::create(array('name' => 'Cat3', 'parent' => $category1->id, 'visible' => 0));
        $category4 = core_course_category::create(array('name' => 'Cat4', 'idnumber' => '12', 'parent' => $category1->id));
        $category5 = core_course_category::create(array('name' => 'Cat5', 'idnumber' => '11',
            'parent' => $category1->id, 'visible' => 0));
        $category6 = core_course_category::create(array('name' => 'Cat6', 'idnumber' => '10', 'parent' => $category1->id));
        $category7 = core_course_category::create(array('name' => 'Cat0', 'parent' => $category1->id));

        $children = $category1->get_children();
        // User does not have the capability to view hidden categories, so the list should be
        // 2, 4, 6, 7.
        $this->assertEquals(array($category2->id, $category4->id, $category6->id, $category7->id), array_keys($children));
        $this->assertEquals(4, $category1->get_children_count());

        $children = $category1->get_children(array('offset' => 2));
        $this->assertEquals(array($category6->id, $category7->id), array_keys($children));
        $this->assertEquals(4, $category1->get_children_count());

        $children = $category1->get_children(array('limit' => 2));
        $this->assertEquals(array($category2->id, $category4->id), array_keys($children));

        $children = $category1->get_children(array('offset' => 1, 'limit' => 2));
        $this->assertEquals(array($category4->id, $category6->id), array_keys($children));

        $children = $category1->get_children(array('sort' => array('name' => 1)));
        // Must be 7, 2, 4, 6.
        $this->assertEquals(array($category7->id, $category2->id, $category4->id, $category6->id), array_keys($children));

        $children = $category1->get_children(array('sort' => array('idnumber' => 1, 'name' => -1)));
        // Must be 2, 7, 6, 4.
        $this->assertEquals(array($category2->id, $category7->id, $category6->id, $category4->id), array_keys($children));

        // Check that everything is all right after purging the caches.
        \cache_helper::purge_by_event('changesincoursecat');
        $children = $category1->get_children();
        $this->assertEquals(array($category2->id, $category4->id, $category6->id, $category7->id), array_keys($children));
        $this->assertEquals(4, $category1->get_children_count());
    }

    /**
     * Test the get_all_children_ids function.
     */
    public function test_get_all_children_ids() {
        $category1 = core_course_category::create(array('name' => 'Cat1'));
        $category2 = core_course_category::create(array('name' => 'Cat2'));
        $category11 = core_course_category::create(array('name' => 'Cat11', 'parent' => $category1->id));
        $category12 = core_course_category::create(array('name' => 'Cat12', 'parent' => $category1->id));
        $category13 = core_course_category::create(array('name' => 'Cat13', 'parent' => $category1->id));
        $category111 = core_course_category::create(array('name' => 'Cat111', 'parent' => $category11->id));
        $category112 = core_course_category::create(array('name' => 'Cat112', 'parent' => $category11->id));
        $category1121 = core_course_category::create(array('name' => 'Cat1121', 'parent' => $category112->id));

        $this->assertCount(0, $category2->get_all_children_ids());
        $this->assertCount(6, $category1->get_all_children_ids());

        $cmpchildrencat1 = array($category11->id, $category12->id, $category13->id, $category111->id, $category112->id,
                $category1121->id);
        $childrencat1 = $category1->get_all_children_ids();
        // Order of values does not matter. Compare sorted arrays.
        sort($cmpchildrencat1);
        sort($childrencat1);
        $this->assertEquals($cmpchildrencat1, $childrencat1);

        $this->assertCount(3, $category11->get_all_children_ids());
        $this->assertCount(0, $category111->get_all_children_ids());
        $this->assertCount(1, $category112->get_all_children_ids());

        $this->assertEquals(array($category1121->id), $category112->get_all_children_ids());
    }

    /**
     * Test the countall function
     */
    public function test_count_all() {
        global $DB;
        // Dont assume there is just one. An add-on might create a category as part of the install.
        $numcategories = $DB->count_records('course_categories');
        $this->assertEquals($numcategories, core_course_category::count_all());
        $this->assertDebuggingCalled('Method core_course_category::count_all() is deprecated. Please use ' .
            'core_course_category::is_simple_site()', DEBUG_DEVELOPER);
        $category1 = core_course_category::create(array('name' => 'Cat1'));
        $category2 = core_course_category::create(array('name' => 'Cat2', 'parent' => $category1->id));
        $category3 = core_course_category::create(array('name' => 'Cat3', 'parent' => $category2->id, 'visible' => 0));
        // Now we've got three more.
        $this->assertEquals($numcategories + 3, core_course_category::count_all());
        $this->assertDebuggingCalled('Method core_course_category::count_all() is deprecated. Please use ' .
            'core_course_category::is_simple_site()', DEBUG_DEVELOPER);
        \cache_helper::purge_by_event('changesincoursecat');
        // We should still have 4.
        $this->assertEquals($numcategories + 3, core_course_category::count_all());
        $this->assertDebuggingCalled('Method core_course_category::count_all() is deprecated. Please use ' .
            'core_course_category::is_simple_site()', DEBUG_DEVELOPER);
    }

    /**
     * Test the is_simple_site function
     */
    public function test_is_simple_site() {
        // By default site has one category and is considered simple.
        $this->assertEquals(true, core_course_category::is_simple_site());
        $default = core_course_category::get_default();
        // When there is only one category but it is hidden, it is not a simple site.
        $default->update(['visible' => 0]);
        $this->assertEquals(false, core_course_category::is_simple_site());
        $default->update(['visible' => 1]);
        $this->assertEquals(true, core_course_category::is_simple_site());
        // As soon as there is more than one category, site is not simple any more.
        core_course_category::create(array('name' => 'Cat1'));
        $this->assertEquals(false, core_course_category::is_simple_site());
    }

    /**
     * Test a categories ability to resort courses.
     */
    public function test_resort_courses() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course1 = $generator->create_course(array(
            'category' => $category->id,
            'idnumber' => '006-01',
            'shortname' => 'Biome Study',
            'fullname' => '<span lang="ar" class="multilang">'.'دراسة منطقة إحيائية'.'</span><span lang="en" class="multilang">Biome Study</span>',
            'timecreated' => '1000000001'
        ));
        $course2 = $generator->create_course(array(
            'category' => $category->id,
            'idnumber' => '007-02',
            'shortname' => 'Chemistry Revision',
            'fullname' => 'Chemistry Revision',
            'timecreated' => '1000000002'
        ));
        $course3 = $generator->create_course(array(
            'category' => $category->id,
            'idnumber' => '007-03',
            'shortname' => 'Swiss Rolls and Sunflowers',
            'fullname' => 'Aarkvarks guide to Swiss Rolls and Sunflowers',
            'timecreated' => '1000000003'
        ));
        $course4 = $generator->create_course(array(
            'category' => $category->id,
            'idnumber' => '006-04',
            'shortname' => 'Scratch',
            'fullname' => '<a href="test.php">Basic Scratch</a>',
            'timecreated' => '1000000004'
        ));
        $c1 = (int)$course1->id;
        $c2 = (int)$course2->id;
        $c3 = (int)$course3->id;
        $c4 = (int)$course4->id;

        $coursecat = core_course_category::get($category->id);
        $this->assertTrue($coursecat->resort_courses('idnumber'));
        $this->assertSame(array($c1, $c4, $c2, $c3), array_keys($coursecat->get_courses()));

        $this->assertTrue($coursecat->resort_courses('shortname'));
        $this->assertSame(array($c1, $c2, $c4, $c3), array_keys($coursecat->get_courses()));

        $this->assertTrue($coursecat->resort_courses('timecreated'));
        $this->assertSame(array($c1, $c2, $c3, $c4), array_keys($coursecat->get_courses()));

        try {
            // Enable the multilang filter and set it to apply to headings and content.
            \filter_manager::reset_caches();
            filter_set_global_state('multilang', TEXTFILTER_ON);
            filter_set_applies_to_strings('multilang', true);
            $expected = array($c3, $c4, $c1, $c2);
        } catch (\coding_exception $ex) {
            $expected = array($c3, $c4, $c2, $c1);
        }
        $this->assertTrue($coursecat->resort_courses('fullname'));
        $this->assertSame($expected, array_keys($coursecat->get_courses()));
    }

    public function test_get_search_courses() {
        $cat1 = core_course_category::create(array('name' => 'Cat1'));
        $cat2 = core_course_category::create(array('name' => 'Cat2', 'parent' => $cat1->id));
        $c1 = $this->getDataGenerator()->create_course(array('category' => $cat1->id, 'fullname' => 'Test 3', 'summary' => ' ', 'idnumber' => 'ID3'));
        $c2 = $this->getDataGenerator()->create_course(array('category' => $cat1->id, 'fullname' => 'Test 1', 'summary' => ' ', 'visible' => 0));
        $c3 = $this->getDataGenerator()->create_course(array('category' => $cat1->id, 'fullname' => 'Математика', 'summary' => ' Test '));
        $c4 = $this->getDataGenerator()->create_course(array('category' => $cat1->id, 'fullname' => 'Test 4', 'summary' => ' ', 'idnumber' => 'ID4'));

        $c5 = $this->getDataGenerator()->create_course(array('category' => $cat2->id, 'fullname' => 'Test 5', 'summary' => ' '));
        $c6 = $this->getDataGenerator()->create_course(array('category' => $cat2->id, 'fullname' => 'Дискретная Математика', 'summary' => ' '));
        $c7 = $this->getDataGenerator()->create_course(array('category' => $cat2->id, 'fullname' => 'Test 7', 'summary' => ' ', 'visible' => 0));
        $c8 = $this->getDataGenerator()->create_course(array('category' => $cat2->id, 'fullname' => 'Test 8', 'summary' => ' '));

        // Get courses in category 1 (returned visible only because user is not enrolled).
        $res = $cat1->get_courses(array('sortorder' => 1));
        $this->assertEquals(array($c4->id, $c3->id, $c1->id), array_keys($res)); // Courses are added in reverse order.
        $this->assertEquals(3, $cat1->get_courses_count());

        // Get courses in category 1 recursively (returned visible only because user is not enrolled).
        $res = $cat1->get_courses(array('recursive' => 1));
        $this->assertEquals(array($c4->id, $c3->id, $c1->id, $c8->id, $c6->id, $c5->id), array_keys($res));
        $this->assertEquals(6, $cat1->get_courses_count(array('recursive' => 1)));

        // Get courses sorted by fullname.
        $res = $cat1->get_courses(array('sort' => array('fullname' => 1)));
        $this->assertEquals(array($c1->id, $c4->id, $c3->id), array_keys($res));
        $this->assertEquals(3, $cat1->get_courses_count(array('sort' => array('fullname' => 1))));

        // Get courses sorted by fullname recursively.
        $res = $cat1->get_courses(array('recursive' => 1, 'sort' => array('fullname' => 1)));
        $this->assertEquals(array($c1->id, $c4->id, $c5->id, $c8->id, $c6->id, $c3->id), array_keys($res));
        $this->assertEquals(6, $cat1->get_courses_count(array('recursive' => 1, 'sort' => array('fullname' => 1))));

        // Get courses sorted by fullname recursively, use offset and limit.
        $res = $cat1->get_courses(array('recursive' => 1, 'offset' => 1, 'limit' => 2, 'sort' => array('fullname' => -1)));
        $this->assertEquals(array($c6->id, $c8->id), array_keys($res));
        // Offset and limit do not affect get_courses_count().
        $this->assertEquals(6, $cat1->get_courses_count(array('recursive' => 1, 'offset' => 1, 'limit' => 2, 'sort' => array('fullname' => 1))));

        // Calling get_courses_count without prior call to get_courses().
        $this->assertEquals(3, $cat2->get_courses_count(array('recursive' => 1, 'sort' => array('idnumber' => 1))));

        // Search courses.

        // Search by text.
        $res = core_course_category::search_courses(array('search' => 'Test'));
        $this->assertEquals(array($c4->id, $c3->id, $c1->id, $c8->id, $c5->id), array_keys($res));
        $this->assertEquals(5, core_course_category::search_courses_count(array('search' => 'Test')));

        // Search by text with specified offset and limit.
        $options = array('sort' => array('fullname' => 1), 'offset' => 1, 'limit' => 2);
        $res = core_course_category::search_courses(array('search' => 'Test'), $options);
        $this->assertEquals(array($c4->id, $c5->id), array_keys($res));
        $this->assertEquals(5, core_course_category::search_courses_count(array('search' => 'Test'), $options));

        // IMPORTANT: the tests below may fail on some databases
        // case-insensitive search.
        $res = core_course_category::search_courses(array('search' => 'test'));
        $this->assertEquals(array($c4->id, $c3->id, $c1->id, $c8->id, $c5->id), array_keys($res));
        $this->assertEquals(5, core_course_category::search_courses_count(array('search' => 'test')));

        // Non-latin language search.
        $res = core_course_category::search_courses(array('search' => 'Математика'));
        $this->assertEquals(array($c3->id, $c6->id), array_keys($res));
        $this->assertEquals(2, core_course_category::search_courses_count(array('search' => 'Математика'), array()));

        $this->setUser($this->getDataGenerator()->create_user());

        // Add necessary capabilities.
        $this->assign_capability('moodle/course:create', CAP_ALLOW, \context_coursecat::instance($cat2->id));
        // Do another search with restricted capabilities.
        $reqcaps = array('moodle/course:create');
        $res = core_course_category::search_courses(array('search' => 'test'), array(), $reqcaps);
        $this->assertEquals(array($c8->id, $c5->id), array_keys($res));
        $this->assertEquals(2, core_course_category::search_courses_count(array('search' => 'test'), array(), $reqcaps));
    }

    public function test_course_contacts() {
        global $DB, $CFG;

        set_config('coursecontactduplicates', false);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $oldcoursecontact = $CFG->coursecontact;

        $CFG->coursecontact = $managerrole->id. ','. $teacherrole->id;

        /*
         * User is listed in course contacts for the course if he has one of the
         * "course contact" roles ($CFG->coursecontact) AND is enrolled in the course.
         * If the user has several roles only the highest is displayed.
         */

        // Test case:
        //
        // == Cat1 (user2 has teacher role)
        //   == Cat2
        //     -- course21 (user2 is enrolled as manager) | [Expected] Manager: F2 L2
        //     -- course22 (user2 is enrolled as student) | [Expected] Teacher: F2 L2
        //     == Cat4 (user2 has manager role)
        //       -- course41 (user4 is enrolled as teacher, user5 is enrolled as manager) | [Expected] Manager: F5 L5, Teacher: F4 L4
        //       -- course42 (user2 is enrolled as teacher) | [Expected] Manager: F2 L2
        //   == Cat3 (user3 has manager role)
        //     -- course31 (user3 is enrolled as student) | [Expected] Manager: F3 L3
        //     -- course32                                | [Expected]
        //   -- course11 (user1 is enrolled as teacher)   | [Expected] Teacher: F1 L1
        //   -- course12 (user1 has teacher role)         | [Expected]
        //                also user4 is enrolled as teacher but enrolment is not active
        $category = $course = $enrol = $user = array();
        $category[1] = core_course_category::create(array('name' => 'Cat1'))->id;
        $category[2] = core_course_category::create(array('name' => 'Cat2', 'parent' => $category[1]))->id;
        $category[3] = core_course_category::create(array('name' => 'Cat3', 'parent' => $category[1]))->id;
        $category[4] = core_course_category::create(array('name' => 'Cat4', 'parent' => $category[2]))->id;
        foreach (array(1, 2, 3, 4) as $catid) {
            foreach (array(1, 2) as $courseid) {
                $course[$catid][$courseid] = $this->getDataGenerator()->create_course(array('idnumber' => 'id'.$catid.$courseid,
                    'category' => $category[$catid]))->id;
                $enrol[$catid][$courseid] = $DB->get_record('enrol', array('courseid'=>$course[$catid][$courseid], 'enrol'=>'manual'), '*', MUST_EXIST);
            }
        }
        foreach (array(1, 2, 3, 4, 5) as $userid) {
            $user[$userid] = $this->getDataGenerator()->create_user(array('firstname' => 'F'.$userid, 'lastname' => 'L'.$userid))->id;
        }

        $manual = enrol_get_plugin('manual');

        // Nobody is enrolled now and course contacts are empty.
        $allcourses = core_course_category::get(0)->get_courses(
            array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));
        foreach ($allcourses as $onecourse) {
            $this->assertEmpty($onecourse->get_course_contacts());
        }

        // Cat1 (user2 has teacher role)
        role_assign($teacherrole->id, $user[2], \context_coursecat::instance($category[1]));
        // course21 (user2 is enrolled as manager)
        $manual->enrol_user($enrol[2][1], $user[2], $managerrole->id);
        // course22 (user2 is enrolled as student)
        $manual->enrol_user($enrol[2][2], $user[2], $studentrole->id);
        // Cat4 (user2 has manager role)
        role_assign($managerrole->id, $user[2], \context_coursecat::instance($category[4]));
        // course41 (user4 is enrolled as teacher, user5 is enrolled as manager)
        $manual->enrol_user($enrol[4][1], $user[4], $teacherrole->id);
        $manual->enrol_user($enrol[4][1], $user[5], $managerrole->id);
        // course42 (user2 is enrolled as teacher)
        $manual->enrol_user($enrol[4][2], $user[2], $teacherrole->id);
        // Cat3 (user3 has manager role)
        role_assign($managerrole->id, $user[3], \context_coursecat::instance($category[3]));
        // course31 (user3 is enrolled as student)
        $manual->enrol_user($enrol[3][1], $user[3], $studentrole->id);
        // course11 (user1 is enrolled as teacher)
        $manual->enrol_user($enrol[1][1], $user[1], $teacherrole->id);
        // -- course12 (user1 has teacher role)
        //                also user4 is enrolled as teacher but enrolment is not active
        role_assign($teacherrole->id, $user[1], \context_course::instance($course[1][2]));
        $manual->enrol_user($enrol[1][2], $user[4], $teacherrole->id, 0, 0, ENROL_USER_SUSPENDED);

        $allcourses = core_course_category::get(0)->get_courses(
            array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));
        // Simplify the list of contacts for each course (similar as renderer would do).
        $contacts = array();
        foreach (array(1, 2, 3, 4) as $catid) {
            foreach (array(1, 2) as $courseid) {
                $tmp = array();
                foreach ($allcourses[$course[$catid][$courseid]]->get_course_contacts() as $contact) {
                    $tmp[] = $contact['rolename']. ': '. $contact['username'];
                }
                $contacts[$catid][$courseid] = join(', ', $tmp);
            }
        }

        // Assert:
        //     -- course21 (user2 is enrolled as manager) | Manager: F2 L2
        $this->assertSame('Manager: F2 L2', $contacts[2][1]);
        //     -- course22 (user2 is enrolled as student) | Teacher: F2 L2
        $this->assertSame('Teacher: F2 L2', $contacts[2][2]);
        //       -- course41 (user4 is enrolled as teacher, user5 is enrolled as manager) | Manager: F5 L5, Teacher: F4 L4
        $this->assertSame('Manager: F5 L5, Teacher: F4 L4', $contacts[4][1]);
        //       -- course42 (user2 is enrolled as teacher) | [Expected] Manager: F2 L2
        $this->assertSame('Manager: F2 L2', $contacts[4][2]);
        //     -- course31 (user3 is enrolled as student) | Manager: F3 L3
        $this->assertSame('Manager: F3 L3', $contacts[3][1]);
        //     -- course32                                |
        $this->assertSame('', $contacts[3][2]);
        //   -- course11 (user1 is enrolled as teacher)   | Teacher: F1 L1
        $this->assertSame('Teacher: F1 L1', $contacts[1][1]);
        //   -- course12 (user1 has teacher role)         |
        $this->assertSame('', $contacts[1][2]);

        // Suspend user 4 and make sure he is no longer in contacts of course 1 in category 4.
        $manual->enrol_user($enrol[4][1], $user[4], $teacherrole->id, 0, 0, ENROL_USER_SUSPENDED);
        $allcourses = core_course_category::get(0)->get_courses(array(
                'recursive' => true,
                'coursecontacts' => true,
                'sort' => array('idnumber' => 1))
        );
        $contacts = $allcourses[$course[4][1]]->get_course_contacts();
        $this->assertCount(1, $contacts);
        $contact = reset($contacts);
        $this->assertEquals('F5 L5', $contact['username']);

        $CFG->coursecontact = $oldcoursecontact;
    }

    public function test_course_contacts_with_duplicates() {
        global $DB, $CFG;

        set_config('coursecontactduplicates', true);

        $displayall = get_config('core', 'coursecontactduplicates');
        $this->assertEquals(true, $displayall);

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $oldcoursecontact = $CFG->coursecontact;

        $CFG->coursecontact = $managerrole->id. ','. $teacherrole->id;

        /*
        * User is listed in course contacts for the course if he has one of the
        * "course contact" roles ($CFG->coursecontact) AND is enrolled in the course.
        * If the user has several roles all roles are displayed, but each role only once per user.
        */

        /*
        * Test case:
        *
        * == Cat1 (user2 has teacher role)
        *    == Cat2
        *      -- course21 (user2 is enrolled as manager) | [Expected] Manager: F2 L2
        *      -- course22 (user2 is enrolled as student) | [Expected] Teacher: F2 L2
        *      == Cat4 (user2 has manager role)
        *        -- course41 (user4 is enrolled as teacher, user5 is enrolled as manager)
        *                                                 | [Expected] Manager: F5 L5, Teacher: F4 L4
        *        -- course42 (user2 is enrolled as teacher) | [Expected] Manager: F2 L2
        *    == Cat3 (user3 has manager role)
        *      -- course31 (user3 is enrolled as student) | [Expected] Manager: F3 L3
        *      -- course32                                | [Expected]
        *    -- course11 (user1 is enrolled as teacher)   | [Expected] Teacher: F1 L1
        *    -- course12 (user1 has teacher role)         | [Expected]
        *                 also user4 is enrolled as teacher but enrolment is not active
        */
        $category = $course = $enrol = $user = array();
        $category[1] = core_course_category::create(array('name' => 'Cat1'))->id;
        $category[2] = core_course_category::create(array('name' => 'Cat2', 'parent' => $category[1]))->id;
        $category[3] = core_course_category::create(array('name' => 'Cat3', 'parent' => $category[1]))->id;
        $category[4] = core_course_category::create(array('name' => 'Cat4', 'parent' => $category[2]))->id;
        foreach (array(1, 2, 3, 4) as $catid) {
            foreach (array(1, 2) as $courseid) {
                $course[$catid][$courseid] = $this->getDataGenerator()->create_course(array(
                        'idnumber' => 'id'.$catid.$courseid,
                        'category' => $category[$catid])
                )->id;
                $enrol[$catid][$courseid] = $DB->get_record(
                        'enrol',
                        array('courseid' => $course[$catid][$courseid], 'enrol' => 'manual'),
                        '*',
                        MUST_EXIST
                );
            }
        }
        foreach (array(1, 2, 3, 4, 5) as $userid) {
            $user[$userid] = $this->getDataGenerator()->create_user(array(
                            'firstname' => 'F'.$userid,
                            'lastname' => 'L'.$userid)
            )->id;
        }

        $manual = enrol_get_plugin('manual');

        // Nobody is enrolled now and course contacts are empty.
        $allcourses = core_course_category::get(0)->get_courses(array(
                'recursive' => true,
                'coursecontacts' => true,
                'sort' => array('idnumber' => 1))
        );
        foreach ($allcourses as $onecourse) {
            $this->assertEmpty($onecourse->get_course_contacts());
        }

        // Cat1: user2 has teacher role.
        role_assign($teacherrole->id, $user[2], \context_coursecat::instance($category[1]));
        // Course21: user2 is enrolled as manager.
        $manual->enrol_user($enrol[2][1], $user[2], $managerrole->id);
        // Course22: user2 is enrolled as student.
        $manual->enrol_user($enrol[2][2], $user[2], $studentrole->id);
        // Cat4: user2 has manager role.
        role_assign($managerrole->id, $user[2], \context_coursecat::instance($category[4]));
        // Course41: user4 is enrolled as teacher, user5 is enrolled as manager.
        $manual->enrol_user($enrol[4][1], $user[4], $teacherrole->id);
        $manual->enrol_user($enrol[4][1], $user[5], $managerrole->id);
        // Course42: user2 is enrolled as teacher.
        $manual->enrol_user($enrol[4][2], $user[2], $teacherrole->id);
        // Cat3: user3 has manager role.
        role_assign($managerrole->id, $user[3], \context_coursecat::instance($category[3]));
        // Course31: user3 is enrolled as student.
        $manual->enrol_user($enrol[3][1], $user[3], $studentrole->id);
        // Course11: user1 is enrolled as teacher and user4 is enrolled as teacher and has manager role.
        $manual->enrol_user($enrol[1][1], $user[1], $teacherrole->id);
        $manual->enrol_user($enrol[1][1], $user[4], $teacherrole->id);
        role_assign($managerrole->id, $user[4], \context_course::instance($course[1][1]));
        // Course12: user1 has teacher role, but is not enrolled, as well as user4 is enrolled as teacher, but user4's enrolment is
        // not active.
        role_assign($teacherrole->id, $user[1], \context_course::instance($course[1][2]));
        $manual->enrol_user($enrol[1][2], $user[4], $teacherrole->id, 0, 0, ENROL_USER_SUSPENDED);

        $allcourses = core_course_category::get(0)->get_courses(
                array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));
        // Simplify the list of contacts for each course (similar as renderer would do).
        $contacts = array();
        foreach (array(1, 2, 3, 4) as $catid) {
            foreach (array(1, 2) as $courseid) {
                $tmp = array();
                foreach ($allcourses[$course[$catid][$courseid]]->get_course_contacts() as $contact) {
                    $rolenames = array_map(function ($role) {
                        return $role->displayname;
                    }, $contact['roles']);
                    $tmp[] = implode(", ", $rolenames). ': '.
                            $contact['username'];
                }
                $contacts[$catid][$courseid] = join(', ', $tmp);
            }
        }

        // Assert:
        // Course21: user2 is enrolled as manager. [Expected] Manager: F2 L2, Teacher: F2 L2.
        $this->assertSame('Manager, Teacher: F2 L2', $contacts[2][1]);
        // Course22: user2 is enrolled as student. [Expected] Teacher: F2 L2.
        $this->assertSame('Teacher: F2 L2', $contacts[2][2]);
        // Course41: user4 is enrolled as teacher, user5 is enrolled as manager. [Expected] Manager: F5 L5, Teacher: F4 L4.
        $this->assertSame('Manager: F5 L5, Teacher: F4 L4', $contacts[4][1]);
        // Course42: user2 is enrolled as teacher. [Expected] Manager: F2 L2, Teacher: F2 L2.
        $this->assertSame('Manager, Teacher: F2 L2', $contacts[4][2]);
        // Course31: user3 is enrolled as student. [Expected] Manager: F3 L3.
        $this->assertSame('Manager: F3 L3', $contacts[3][1]);
        // Course32: nobody is enrolled. [Expected] (nothing).
        $this->assertSame('', $contacts[3][2]);
        // Course11: user1 is enrolled as teacher and user4 is enrolled as teacher and has manager role. [Expected] Manager: F4 L4,
        // Teacher: F1 L1, Teacher: F4 L4.
        $this->assertSame('Manager, Teacher: F4 L4, Teacher: F1 L1', $contacts[1][1]);
        // Course12: user1 has teacher role, but is not enrolled, as well as user4 is enrolled as teacher, but user4's enrolment is
        // not active. [Expected] (nothing).
        $this->assertSame('', $contacts[1][2]);

        // Suspend user 4 and make sure he is no longer in contacts of course 1 in category 4.
        $manual->enrol_user($enrol[4][1], $user[4], $teacherrole->id, 0, 0, ENROL_USER_SUSPENDED);
        $allcourses = core_course_category::get(0)->get_courses(array(
                'recursive' => true,
                'coursecontacts' => true,
                'sort' => array('idnumber' => 1)
        ));
        $contacts = $allcourses[$course[4][1]]->get_course_contacts();
        $this->assertCount(1, $contacts);
        $contact = reset($contacts);
        $this->assertEquals('F5 L5', $contact['username']);

        $CFG->coursecontact = $oldcoursecontact;
    }

    public function test_overview_files() {
        global $CFG;
        $this->setAdminUser();
        $cat1 = core_course_category::create(array('name' => 'Cat1'));

        // Create course c1 with one image file.
        $dratid1 = $this->fill_draft_area(array('filename.jpg' => 'Test file contents1'));
        $c1 = $this->getDataGenerator()->create_course(array('category' => $cat1->id,
            'fullname' => 'Test 1', 'overviewfiles_filemanager' => $dratid1));
        // Create course c2 with two image files (only one file will be added because of settings).
        $dratid2 = $this->fill_draft_area(array('filename21.jpg' => 'Test file contents21', 'filename22.jpg' => 'Test file contents22'));
        $c2 = $this->getDataGenerator()->create_course(array('category' => $cat1->id,
            'fullname' => 'Test 2', 'overviewfiles_filemanager' => $dratid2));
        // Create course c3 without files.
        $c3 = $this->getDataGenerator()->create_course(array('category' => $cat1->id, 'fullname' => 'Test 3'));

        // Change the settings to allow multiple files of any types.
        $CFG->courseoverviewfileslimit = 3;
        $CFG->courseoverviewfilesext = '*';
        // Create course c5 with two image files.
        $dratid4 = $this->fill_draft_area(array('filename41.jpg' => 'Test file contents41', 'filename42.jpg' => 'Test file contents42'));
        $c4 = $this->getDataGenerator()->create_course(array('category' => $cat1->id,
            'fullname' => 'Test 4', 'overviewfiles_filemanager' => $dratid4));
        // Create course c6 with non-image file.
        $dratid5 = $this->fill_draft_area(array('filename51.zip' => 'Test file contents51'));
        $c5 = $this->getDataGenerator()->create_course(array('category' => $cat1->id,
            'fullname' => 'Test 5', 'overviewfiles_filemanager' => $dratid5));

        // Reset default settings.
        $CFG->courseoverviewfileslimit = 1;
        $CFG->courseoverviewfilesext = 'web_image';

        $courses = $cat1->get_courses();
        $this->assertTrue($courses[$c1->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c2->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c3->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c4->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c5->id]->has_course_overviewfiles()); // Does not validate the filetypes.

        $this->assertEquals(1, count($courses[$c1->id]->get_course_overviewfiles()));
        $this->assertEquals(1, count($courses[$c2->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c3->id]->get_course_overviewfiles()));
        $this->assertEquals(1, count($courses[$c4->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c5->id]->get_course_overviewfiles())); // Validate the filetypes.

        // Overview files are not allowed, all functions return empty values.
        $CFG->courseoverviewfileslimit = 0;

        $this->assertFalse($courses[$c1->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c2->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c3->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c4->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c5->id]->has_course_overviewfiles());

        $this->assertEquals(0, count($courses[$c1->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c2->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c3->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c4->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c5->id]->get_course_overviewfiles()));

        // Multiple overview files are allowed but still limited to images.
        $CFG->courseoverviewfileslimit = 3;

        $this->assertTrue($courses[$c1->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c2->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c3->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c4->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c5->id]->has_course_overviewfiles()); // Still does not validate the filetypes.

        $this->assertEquals(1, count($courses[$c1->id]->get_course_overviewfiles()));
        $this->assertEquals(1, count($courses[$c2->id]->get_course_overviewfiles())); // Only 1 file was actually added.
        $this->assertEquals(0, count($courses[$c3->id]->get_course_overviewfiles()));
        $this->assertEquals(2, count($courses[$c4->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c5->id]->get_course_overviewfiles()));

        // Multiple overview files of any type are allowed.
        $CFG->courseoverviewfilesext = '*';

        $this->assertTrue($courses[$c1->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c2->id]->has_course_overviewfiles());
        $this->assertFalse($courses[$c3->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c4->id]->has_course_overviewfiles());
        $this->assertTrue($courses[$c5->id]->has_course_overviewfiles());

        $this->assertEquals(1, count($courses[$c1->id]->get_course_overviewfiles()));
        $this->assertEquals(1, count($courses[$c2->id]->get_course_overviewfiles()));
        $this->assertEquals(0, count($courses[$c3->id]->get_course_overviewfiles()));
        $this->assertEquals(2, count($courses[$c4->id]->get_course_overviewfiles()));
        $this->assertEquals(1, count($courses[$c5->id]->get_course_overviewfiles()));
    }

    public function test_get_nested_name() {
        $cat1name = 'Cat1';
        $cat2name = 'Cat2';
        $cat3name = 'Cat3';
        $cat4name = 'Cat4';
        $category1 = core_course_category::create(array('name' => $cat1name));
        $category2 = core_course_category::create(array('name' => $cat2name, 'parent' => $category1->id));
        $category3 = core_course_category::create(array('name' => $cat3name, 'parent' => $category2->id));
        $category4 = core_course_category::create(array('name' => $cat4name, 'parent' => $category2->id));

        $this->assertEquals($cat1name, $category1->get_nested_name(false));
        $this->assertEquals("{$cat1name} / {$cat2name}", $category2->get_nested_name(false));
        $this->assertEquals("{$cat1name} / {$cat2name} / {$cat3name}", $category3->get_nested_name(false));
        $this->assertEquals("{$cat1name} / {$cat2name} / {$cat4name}", $category4->get_nested_name(false));
    }

    public function test_coursecat_is_uservisible() {
        global $USER;

        // Create category 1 as visible.
        $category1 = core_course_category::create(array('name' => 'Cat1', 'visible' => 1));
        // Create category 2 as hidden.
        $category2 = core_course_category::create(array('name' => 'Cat2', 'visible' => 0));

        $this->assertTrue($category1->is_uservisible());
        $this->assertFalse($category2->is_uservisible());

        $this->assign_capability('moodle/category:viewhiddencategories');

        $this->assertTrue($category1->is_uservisible());
        $this->assertTrue($category2->is_uservisible());

        // First, store current user's id, then login as another user.
        $userid = $USER->id;
        $this->setUser($this->getDataGenerator()->create_user());

        // User $user should still have the moodle/category:viewhiddencategories capability.
        $this->assertTrue($category1->is_uservisible($userid));
        $this->assertTrue($category2->is_uservisible($userid));

        $this->assign_capability('moodle/category:viewhiddencategories', CAP_INHERIT);

        $this->assertTrue($category1->is_uservisible());
        $this->assertFalse($category2->is_uservisible());
    }

    public function test_current_user_coursecat_get() {
        $this->assign_capability('moodle/category:viewhiddencategories');

        // Create category 1 as visible.
        $category1 = core_course_category::create(array('name' => 'Cat1', 'visible' => 1));
        // Create category 2 as hidden.
        $category2 = core_course_category::create(array('name' => 'Cat2', 'visible' => 0));

        $this->assertEquals($category1->id, core_course_category::get($category1->id)->id);
        $this->assertEquals($category2->id, core_course_category::get($category2->id)->id);

        // Login as another user to test core_course_category::get.
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertEquals($category1->id, core_course_category::get($category1->id)->id);

        // Expecting to get an exception as this new user does not have the moodle/category:viewhiddencategories capability.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('cannotviewcategory', 'error'));
        core_course_category::get($category2->id);
    }

    public function test_another_user_coursecat_get() {
        global $USER;

        $this->assign_capability('moodle/category:viewhiddencategories');

        // Create category 1 as visible.
        $category1 = core_course_category::create(array('name' => 'Cat1', 'visible' => 1));
        // Create category 2 as hidden.
        $category2 = core_course_category::create(array('name' => 'Cat2', 'visible' => 0));

        // First, store current user's object, then login as another user.
        $user1 = $USER;
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->assertEquals($category1->id, core_course_category::get($category1->id, MUST_EXIST, false, $user1)->id);
        $this->assertEquals($category2->id, core_course_category::get($category2->id, MUST_EXIST, false, $user1)->id);

        $this->setUser($user1);

        $this->assertEquals($category1->id, core_course_category::get($category1->id, MUST_EXIST, false, $user2)->id);
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('cannotviewcategory', 'error'));
        core_course_category::get($category2->id, MUST_EXIST, false, $user2);
    }

    /**
     * Creates a draft area for current user and fills it with fake files
     *
     * @param array $files array of files that need to be added to filearea, filename => filecontents
     * @return int draftid for the filearea
     */
    protected function fill_draft_area(array $files) {
        global $USER;
        $usercontext = \context_user::instance($USER->id);
        $draftid = file_get_unused_draft_itemid();
        foreach ($files as $filename => $filecontents) {
            // Add actual file there.
            $filerecord = array('component' => 'user', 'filearea' => 'draft',
                    'contextid' => $usercontext->id, 'itemid' => $draftid,
                    'filename' => $filename, 'filepath' => '/');
            $fs = get_file_storage();
            $fs->create_file_from_string($filerecord, $filecontents);
        }
        return $draftid;
    }

    /**
     * This test ensures that is the list of courses in a category can be retrieved while a course is being deleted.
     */
    public function test_get_courses_during_delete() {
        global $DB;
        $category = self::getDataGenerator()->create_category();
        $course = self::getDataGenerator()->create_course(['category' => $category->id]);
        $othercourse = self::getDataGenerator()->create_course(['category' => $category->id]);
        $coursecategory = core_course_category::get($category->id);
        // Get a list of courses before deletion to populate the cache.
        $originalcourses = $coursecategory->get_courses();
        $this->assertCount(2, $originalcourses);
        $this->assertArrayHasKey($course->id, $originalcourses);
        $this->assertArrayHasKey($othercourse->id, $originalcourses);
        // Simulate the course deletion process being part way though.
        $DB->delete_records('course', ['id' => $course->id]);
        // Get the list of courses while a deletion is in progress.
        $courses = $coursecategory->get_courses();
        $this->assertCount(1, $courses);
        $this->assertArrayHasKey($othercourse->id, $courses);
    }

    /**
     * Test get_nearest_editable_subcategory() method.
     *
     * @covers \core_course_category::get_nearest_editable_subcategory
     */
    public function test_get_nearest_editable_subcategory(): void {
        global $DB;

        $coursecreatorrole = $DB->get_record('role', ['shortname' => 'coursecreator']);
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);

        // Create categories.
        $category1 = core_course_category::create(['name' => 'Cat1']);
        $category2 = core_course_category::create(['name' => 'Cat2']);
        $category3 = core_course_category::create(['name' => 'Cat3']);
        // Get the category contexts.
        $category1context = $category1->get_context();
        $category2context = $category2->get_context();
        $category3context = $category3->get_context();
        // Create user.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        // Assign the user1 to 'Course creator' role for Cat1.
        role_assign($coursecreatorrole->id, $user1->id, $category1context->id);
        // Assign the user2 to 'Manager' role for Cat3.
        role_assign($managerrole->id, $user2->id, $category3context->id);

        // Start scenario 1.
        // user3 has no permission to create course or manage category.
        $this->setUser($user3);
        $coursecat = core_course_category::user_top();
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:create']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/category:manage']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create', 'manage']));
        // End scenario 1.

        // Start scenario 2.
        // user1 has permission to create course but has no permission to manage category.
        $this->setUser($user1);
        $coursecat = core_course_category::user_top();
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:create']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/category:manage']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create', 'manage']));
        // The get_nearest_editable_subcategory should return Cat1.
        $this->assertEquals($category1->id, core_course_category::get_nearest_editable_subcategory($coursecat, ['create'])->id);
        $this->assertEquals($category1->id,
            core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:create'])->id);
        // Assign the user1 to 'Course creator' role for Cat2.
        role_assign($coursecreatorrole->id, $user1->id, $category2context->id);
        // The get_nearest_editable_subcategory should still return Cat1 (First creatable subcategory) for create course capability.
        $this->assertEquals($category1->id, core_course_category::get_nearest_editable_subcategory($coursecat, ['create'])->id);
        $this->assertEquals($category1->id,
            core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:create'])->id);
        // End scenario 2.

        // Start scenario 3.
        // user2 has no permission to create course but has permission to manage category.
        $this->setUser($user2);
        // Remove the moodle/course:create capability for the manager role.
        unassign_capability('moodle/course:create', $managerrole->id);
        $coursecat = core_course_category::user_top();
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:create']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/category:manage']));
        $this->assertEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create', 'manage']));
        // The get_nearest_editable_subcategory should return Cat3.
        $this->assertEquals($category3->id, core_course_category::get_nearest_editable_subcategory($coursecat, ['manage'])->id);
        $this->assertEquals($category3->id,
            core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/category:manage'])->id);
        // End scenario 3.

        // Start scenario 4.
        // user2 has both permission to create course and manage category.
        // Add the moodle/course:create capability back again for the manager role.
        assign_capability('moodle/course:create', CAP_ALLOW, $managerrole->id, $category3context->id);
        $this->setUser($user2);
        $coursecat = core_course_category::user_top();
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:create']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/category:manage']));
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, ['create', 'manage']));
        // The get_nearest_editable_subcategory should return Cat3.
        $this->assertEquals($category3->id,
            core_course_category::get_nearest_editable_subcategory($coursecat, ['create', 'manage'])->id);
        $this->assertEquals($category3->id, core_course_category::get_nearest_editable_subcategory($coursecat,
            ['moodle/course:create', 'moodle/category:manage'])->id);
        // End scenario 4.

        // Start scenario 5.
        // Exception will be thrown if $permissionstocheck is empty.
        $this->setUser($user1);
        $coursecat = core_course_category::user_top();
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid permissionstocheck parameter');
        $this->assertNotEmpty(core_course_category::get_nearest_editable_subcategory($coursecat, []));
        // End scenario 5.
    }

    /**
     * Test get_nearest_editable_subcategory() method with hidden categories.
     *
     * @param int $visible  Whether the category is visible or not.
     * @param bool $child   Whether the category is child of main category or not.
     * @param string $role  The role the user must have.
     * @param array $permissions An array of permissions we must check.
     * @param bool $result Whether the result should be the category or null.
     *
     * @dataProvider get_nearest_editable_subcategory_provider
     * @covers \core_course_category::get_nearest_editable_subcategory
     */
    public function test_get_nearest_editable_subcategory_with_hidden_categories(
        int $visible = 0,
        bool $child = false,
        string $role = 'manager',
        array $permissions = [],
        bool $result = false
    ): void {
        global $DB;

        $userrole = $DB->get_record('role', ['shortname' => $role]);
        $maincat = core_course_category::create(['name' => 'Main cat']);

        $catparams = new \stdClass();
        $catparams->name = 'Test category';
        $catparams->visible = $visible;
        if ($child) {
            $catparams->parent = $maincat->id;
        }
        $category = core_course_category::create($catparams);
        $catcontext = $category->get_context();
        $user = $this->getDataGenerator()->create_user();
        role_assign($userrole->id, $user->id, $catcontext->id);
        $this->setUser($user);

        $nearestcat = core_course_category::get_nearest_editable_subcategory(core_course_category::user_top(), $permissions);

        if ($result) {
            $this->assertEquals($category->id, $nearestcat->id);
        } else {
            $this->assertEmpty($nearestcat);
        }
    }

    /**
     * Data provider for test_get_nearest_editable_subcategory_with_hidden_categories().
     *
     * @return array
     */
    public function get_nearest_editable_subcategory_provider(): array {
        return [
            'Hidden main category for manager. Checking create and manage' => [
                0,
                false,
                'manager',
                ['create', 'manage'],
                true,
            ],
            'Hidden main category for course creator. Checking create and manage' => [
                0,
                false,
                'coursecreator',
                ['create', 'manage'],
                false,
            ],
            'Hidden main category for student. Checking create and manage' => [
                0,
                false,
                'student',
                ['create', 'manage'],
                false,
            ],
            'Hidden main category for manager. Checking create' => [
                0,
                false,
                'manager',
                ['create'],
                true,
            ],
            'Hidden main category for course creator. Checking create' => [
                0,
                false,
                'coursecreator',
                ['create'],
                true,
            ],
            'Hidden main category for student. Checking create' => [
                0,
                false,
                'student',
                ['create'],
                false,
            ],
            'Hidden subcategory for manager. Checking create and manage' => [
                0,
                true,
                'manager',
                ['create', 'manage'],
                true,
            ],
            'Hidden subcategory for course creator. Checking create and manage' => [
                0,
                true,
                'coursecreator',
                ['create', 'manage'],
                false,
            ],
            'Hidden subcategory for student. Checking create and manage' => [
                0,
                true,
                'student',
                ['create', 'manage'],
                false,
            ],
            'Hidden subcategory for manager. Checking create' => [
                0,
                true,
                'manager',
                ['create'],
                true,
            ],
            'Hidden subcategory for course creator. Checking create' => [
                0,
                true,
                'coursecreator',
                ['create'],
                true,
            ],
            'Hidden subcategory for student. Checking create' => [
                0,
                true,
                'student',
                ['create'],
                false,
            ],
        ];
    }
}
