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
 * External course functions unit tests
 *
 * @package    core_course
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External course functions unit tests
 *
 * @package    core_course
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/course/externallib.php');
    }

    /**
     * Test create_categories
     */
    public function test_create_categories() {

        global $DB;

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/category:manage', $contextid);

        // Create base categories.
        $category1 = new stdClass();
        $category1->name = 'Root Test Category 1';
        $category2 = new stdClass();
        $category2->name = 'Root Test Category 2';
        $category2->idnumber = 'rootcattest2';
        $category2->desc = 'Description for root test category 1';
        $category2->theme = 'bootstrapbase';
        $categories = array(
            array('name' => $category1->name, 'parent' => 0),
            array('name' => $category2->name, 'parent' => 0, 'idnumber' => $category2->idnumber,
                'description' => $category2->desc, 'theme' => $category2->theme)
        );

        $createdcats = core_course_external::create_categories($categories);

        // We need to execute the return values cleaning process to simulate the web service server.
        $createdcats = external_api::clean_returnvalue(core_course_external::create_categories_returns(), $createdcats);

        // Initially confirm that base data was inserted correctly.
        $this->assertEquals($category1->name, $createdcats[0]['name']);
        $this->assertEquals($category2->name, $createdcats[1]['name']);

        // Save the ids.
        $category1->id = $createdcats[0]['id'];
        $category2->id = $createdcats[1]['id'];

        // Create on sub category.
        $category3 = new stdClass();
        $category3->name = 'Sub Root Test Category 3';
        $subcategories = array(
            array('name' => $category3->name, 'parent' => $category1->id)
        );

        $createdsubcats = core_course_external::create_categories($subcategories);

        // We need to execute the return values cleaning process to simulate the web service server.
        $createdsubcats = external_api::clean_returnvalue(core_course_external::create_categories_returns(), $createdsubcats);

        // Confirm that sub categories were inserted correctly.
        $this->assertEquals($category3->name, $createdsubcats[0]['name']);

        // Save the ids.
        $category3->id = $createdsubcats[0]['id'];

        // Calling the ws function should provide a new sortorder to give category1,
        // category2, category3. New course categories are ordered by id not name.
        $category1 = $DB->get_record('course_categories', array('id' => $category1->id));
        $category2 = $DB->get_record('course_categories', array('id' => $category2->id));
        $category3 = $DB->get_record('course_categories', array('id' => $category3->id));

        // sortorder sequence (and sortorder) must be:
        // category 1
        //   category 3
        // category 2
        $this->assertGreaterThan($category1->sortorder, $category3->sortorder);
        $this->assertGreaterThan($category3->sortorder, $category2->sortorder);

        // Call without required capability
        $this->unassignUserCapability('moodle/category:manage', $contextid, $roleid);
        $this->expectException('required_capability_exception');
        $createdsubcats = core_course_external::create_categories($subcategories);

    }

    /**
     * Test delete categories
     */
    public function test_delete_categories() {
        global $DB;

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/category:manage', $contextid);

        $category1  = self::getDataGenerator()->create_category();
        $category2  = self::getDataGenerator()->create_category(
                array('parent' => $category1->id));
        $category3  = self::getDataGenerator()->create_category();
        $category4  = self::getDataGenerator()->create_category(
                array('parent' => $category3->id));
        $category5  = self::getDataGenerator()->create_category(
                array('parent' => $category4->id));

        //delete category 1 and 2 + delete category 4, category 5 moved under category 3
        core_course_external::delete_categories(array(
            array('id' => $category1->id, 'recursive' => 1),
            array('id' => $category4->id)
        ));

        //check $category 1 and 2 are deleted
        $notdeletedcount = $DB->count_records_select('course_categories',
            'id IN ( ' . $category1->id . ',' . $category2->id . ',' . $category4->id . ')');
        $this->assertEquals(0, $notdeletedcount);

        //check that $category5 as $category3 for parent
        $dbcategory5 = $DB->get_record('course_categories', array('id' => $category5->id));
        $this->assertEquals($dbcategory5->path, $category3->path . '/' . $category5->id);

         // Call without required capability
        $this->unassignUserCapability('moodle/category:manage', $contextid, $roleid);
        $this->expectException('required_capability_exception');
        $createdsubcats = core_course_external::delete_categories(
                array(array('id' => $category3->id)));
    }

    /**
     * Test get categories
     */
    public function test_get_categories() {
        global $DB;

        $this->resetAfterTest(true);

        $generatedcats = array();
        $category1data['idnumber'] = 'idnumbercat1';
        $category1data['name'] = 'Category 1 for PHPunit test';
        $category1data['description'] = 'Category 1 description';
        $category1data['descriptionformat'] = FORMAT_MOODLE;
        $category1  = self::getDataGenerator()->create_category($category1data);
        $generatedcats[$category1->id] = $category1;
        $category2  = self::getDataGenerator()->create_category(
                array('parent' => $category1->id));
        $generatedcats[$category2->id] = $category2;
        $category6  = self::getDataGenerator()->create_category(
                array('parent' => $category1->id, 'visible' => 0));
        $generatedcats[$category6->id] = $category6;
        $category3  = self::getDataGenerator()->create_category();
        $generatedcats[$category3->id] = $category3;
        $category4  = self::getDataGenerator()->create_category(
                array('parent' => $category3->id));
        $generatedcats[$category4->id] = $category4;
        $category5  = self::getDataGenerator()->create_category(
                array('parent' => $category4->id));
        $generatedcats[$category5->id] = $category5;

        // Set the required capabilities by the external function.
        $context = context_system::instance();
        $roleid = $this->assignUserCapability('moodle/category:manage', $context->id);
        $this->assignUserCapability('moodle/category:viewhiddencategories', $context->id, $roleid);

        // Retrieve category1 + sub-categories except not visible ones
        $categories = core_course_external::get_categories(array(
            array('key' => 'id', 'value' => $category1->id),
            array('key' => 'visible', 'value' => 1)), 1);

        // We need to execute the return values cleaning process to simulate the web service server.
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        // Check we retrieve the good total number of categories.
        $this->assertEquals(2, count($categories));

        // Check the return values
        foreach ($categories as $category) {
            $generatedcat = $generatedcats[$category['id']];
            $this->assertEquals($category['idnumber'], $generatedcat->idnumber);
            $this->assertEquals($category['name'], $generatedcat->name);
            // Description was converted to the HTML format.
            $this->assertEquals($category['description'], format_text($generatedcat->description, FORMAT_MOODLE, array('para' => false)));
            $this->assertEquals($category['descriptionformat'], FORMAT_HTML);
        }

        // Check categories by ids.
        $ids = implode(',', array_keys($generatedcats));
        $categories = core_course_external::get_categories(array(
            array('key' => 'ids', 'value' => $ids)), 0);

        // We need to execute the return values cleaning process to simulate the web service server.
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        // Check we retrieve the good total number of categories.
        $this->assertEquals(6, count($categories));
        // Check ids.
        $returnedids = [];
        foreach ($categories as $category) {
            $returnedids[] = $category['id'];
        }
        // Sort the arrays upon comparision.
        $this->assertEquals(array_keys($generatedcats), $returnedids, '', 0.0, 10, true);

        // Check different params.
        $categories = core_course_external::get_categories(array(
            array('key' => 'id', 'value' => $category1->id),
            array('key' => 'ids', 'value' => $category1->id),
            array('key' => 'idnumber', 'value' => $category1->idnumber),
            array('key' => 'visible', 'value' => 1)), 0);

        // We need to execute the return values cleaning process to simulate the web service server.
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        $this->assertEquals(1, count($categories));

        // Same query, but forcing a parameters clean.
        $categories = core_course_external::get_categories(array(
            array('key' => 'id', 'value' => "$category1->id"),
            array('key' => 'idnumber', 'value' => $category1->idnumber),
            array('key' => 'name', 'value' => $category1->name . "<br/>"),
            array('key' => 'visible', 'value' => '1')), 0);
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        $this->assertEquals(1, count($categories));

        // Retrieve categories from parent.
        $categories = core_course_external::get_categories(array(
            array('key' => 'parent', 'value' => $category3->id)), 1);
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        $this->assertEquals(2, count($categories));

        // Retrieve all categories.
        $categories = core_course_external::get_categories();

        // We need to execute the return values cleaning process to simulate the web service server.
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        $this->assertEquals($DB->count_records('course_categories'), count($categories));

        $this->unassignUserCapability('moodle/category:viewhiddencategories', $context->id, $roleid);

        // Ensure maxdepthcategory is 2 and retrieve all categories without category:viewhiddencategories capability.
        // It should retrieve all visible categories as well.
        set_config('maxcategorydepth', 2);
        $categories = core_course_external::get_categories();

        // We need to execute the return values cleaning process to simulate the web service server.
        $categories = external_api::clean_returnvalue(core_course_external::get_categories_returns(), $categories);

        $this->assertEquals($DB->count_records('course_categories', array('visible' => 1)), count($categories));

        // Call without required capability (it will fail cause of the search on idnumber).
        $this->expectException('moodle_exception');
        $categories = core_course_external::get_categories(array(
            array('key' => 'id', 'value' => $category1->id),
            array('key' => 'idnumber', 'value' => $category1->idnumber),
            array('key' => 'visible', 'value' => 1)), 0);
    }

    /**
     * Test update_categories
     */
    public function test_update_categories() {
        global $DB;

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/category:manage', $contextid);

        // Create base categories.
        $category1data['idnumber'] = 'idnumbercat1';
        $category1data['name'] = 'Category 1 for PHPunit test';
        $category1data['description'] = 'Category 1 description';
        $category1data['descriptionformat'] = FORMAT_MOODLE;
        $category1  = self::getDataGenerator()->create_category($category1data);
        $category2  = self::getDataGenerator()->create_category(
                array('parent' => $category1->id));
        $category3  = self::getDataGenerator()->create_category();
        $category4  = self::getDataGenerator()->create_category(
                array('parent' => $category3->id));
        $category5  = self::getDataGenerator()->create_category(
                array('parent' => $category4->id));

        // We update all category1 attribut.
        // Then we move cat4 and cat5 parent: cat3 => cat1
        $categories = array(
            array('id' => $category1->id,
                'name' => $category1->name . '_updated',
                'idnumber' => $category1->idnumber . '_updated',
                'description' => $category1->description . '_updated',
                'descriptionformat' => FORMAT_HTML,
                'theme' => $category1->theme),
            array('id' => $category4->id, 'parent' => $category1->id));

        core_course_external::update_categories($categories);

        // Check the values were updated.
        $dbcategories = $DB->get_records_select('course_categories',
                'id IN (' . $category1->id . ',' . $category2->id . ',' . $category2->id
                . ',' . $category3->id . ',' . $category4->id . ',' . $category5->id .')');
        $this->assertEquals($category1->name . '_updated',
                $dbcategories[$category1->id]->name);
        $this->assertEquals($category1->idnumber . '_updated',
                $dbcategories[$category1->id]->idnumber);
        $this->assertEquals($category1->description . '_updated',
                $dbcategories[$category1->id]->description);
        $this->assertEquals(FORMAT_HTML, $dbcategories[$category1->id]->descriptionformat);

        // Check that category4 and category5 have been properly moved.
        $this->assertEquals('/' . $category1->id . '/' . $category4->id,
                $dbcategories[$category4->id]->path);
        $this->assertEquals('/' . $category1->id . '/' . $category4->id . '/' . $category5->id,
                $dbcategories[$category5->id]->path);

        // Call without required capability.
        $this->unassignUserCapability('moodle/category:manage', $contextid, $roleid);
        $this->expectException('required_capability_exception');
        core_course_external::update_categories($categories);
    }

    /**
     * Test create_courses numsections
     */
    public function test_create_course_numsections() {
        global $DB;

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function.
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/course:create', $contextid);
        $this->assignUserCapability('moodle/course:visibility', $contextid, $roleid);

        $numsections = 10;
        $category  = self::getDataGenerator()->create_category();

        // Create base categories.
        $course1['fullname'] = 'Test course 1';
        $course1['shortname'] = 'Testcourse1';
        $course1['categoryid'] = $category->id;
        $course1['courseformatoptions'][] = array('name' => 'numsections', 'value' => $numsections);

        $courses = array($course1);

        $createdcourses = core_course_external::create_courses($courses);
        foreach ($createdcourses as $createdcourse) {
            $existingsections = $DB->get_records('course_sections', array('course' => $createdcourse['id']));
            $modinfo = get_fast_modinfo($createdcourse['id']);
            $sections = $modinfo->get_section_info_all();
            $this->assertEquals(count($sections), $numsections + 1); // Includes generic section.
            $this->assertEquals(count($existingsections), $numsections + 1); // Includes generic section.
        }
    }

    /**
     * Test create_courses
     */
    public function test_create_courses() {
        global $DB;

        $this->resetAfterTest(true);

        // Enable course completion.
        set_config('enablecompletion', 1);
        // Enable course themes.
        set_config('allowcoursethemes', 1);

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/course:create', $contextid);
        $this->assignUserCapability('moodle/course:visibility', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:setforcedlanguage', $contextid, $roleid);

        $category  = self::getDataGenerator()->create_category();

        // Create base categories.
        $course1['fullname'] = 'Test course 1';
        $course1['shortname'] = 'Testcourse1';
        $course1['categoryid'] = $category->id;
        $course2['fullname'] = 'Test course 2';
        $course2['shortname'] = 'Testcourse2';
        $course2['categoryid'] = $category->id;
        $course2['idnumber'] = 'testcourse2idnumber';
        $course2['summary'] = 'Description for course 2';
        $course2['summaryformat'] = FORMAT_MOODLE;
        $course2['format'] = 'weeks';
        $course2['showgrades'] = 1;
        $course2['newsitems'] = 3;
        $course2['startdate'] = 1420092000; // 01/01/2015.
        $course2['enddate'] = 1422669600; // 01/31/2015.
        $course2['numsections'] = 4;
        $course2['maxbytes'] = 100000;
        $course2['showreports'] = 1;
        $course2['visible'] = 0;
        $course2['hiddensections'] = 0;
        $course2['groupmode'] = 0;
        $course2['groupmodeforce'] = 0;
        $course2['defaultgroupingid'] = 0;
        $course2['enablecompletion'] = 1;
        $course2['completionnotify'] = 1;
        $course2['lang'] = 'en';
        $course2['forcetheme'] = 'bootstrapbase';
        $course2['courseformatoptions'][] = array('name' => 'automaticenddate', 'value' => 0);
        $course3['fullname'] = 'Test course 3';
        $course3['shortname'] = 'Testcourse3';
        $course3['categoryid'] = $category->id;
        $course3['format'] = 'topics';
        $course3options = array('numsections' => 8,
            'hiddensections' => 1,
            'coursedisplay' => 1);
        $course3['courseformatoptions'] = array();
        foreach ($course3options as $key => $value) {
            $course3['courseformatoptions'][] = array('name' => $key, 'value' => $value);
        }
        $courses = array($course1, $course2, $course3);

        $createdcourses = core_course_external::create_courses($courses);

        // We need to execute the return values cleaning process to simulate the web service server.
        $createdcourses = external_api::clean_returnvalue(core_course_external::create_courses_returns(), $createdcourses);

        // Check that right number of courses were created.
        $this->assertEquals(3, count($createdcourses));

        // Check that the courses were correctly created.
        foreach ($createdcourses as $createdcourse) {
            $courseinfo = course_get_format($createdcourse['id'])->get_course();

            if ($createdcourse['shortname'] == $course2['shortname']) {
                $this->assertEquals($courseinfo->fullname, $course2['fullname']);
                $this->assertEquals($courseinfo->shortname, $course2['shortname']);
                $this->assertEquals($courseinfo->category, $course2['categoryid']);
                $this->assertEquals($courseinfo->idnumber, $course2['idnumber']);
                $this->assertEquals($courseinfo->summary, $course2['summary']);
                $this->assertEquals($courseinfo->summaryformat, $course2['summaryformat']);
                $this->assertEquals($courseinfo->format, $course2['format']);
                $this->assertEquals($courseinfo->showgrades, $course2['showgrades']);
                $this->assertEquals($courseinfo->newsitems, $course2['newsitems']);
                $this->assertEquals($courseinfo->startdate, $course2['startdate']);
                $this->assertEquals($courseinfo->enddate, $course2['enddate']);
                $this->assertEquals(course_get_format($createdcourse['id'])->get_last_section_number(), $course2['numsections']);
                $this->assertEquals($courseinfo->maxbytes, $course2['maxbytes']);
                $this->assertEquals($courseinfo->showreports, $course2['showreports']);
                $this->assertEquals($courseinfo->visible, $course2['visible']);
                $this->assertEquals($courseinfo->hiddensections, $course2['hiddensections']);
                $this->assertEquals($courseinfo->groupmode, $course2['groupmode']);
                $this->assertEquals($courseinfo->groupmodeforce, $course2['groupmodeforce']);
                $this->assertEquals($courseinfo->defaultgroupingid, $course2['defaultgroupingid']);
                $this->assertEquals($courseinfo->completionnotify, $course2['completionnotify']);
                $this->assertEquals($courseinfo->lang, $course2['lang']);
                $this->assertEquals($courseinfo->theme, $course2['forcetheme']);

                // We enabled completion at the beginning of the test.
                $this->assertEquals($courseinfo->enablecompletion, $course2['enablecompletion']);

            } else if ($createdcourse['shortname'] == $course1['shortname']) {
                $courseconfig = get_config('moodlecourse');
                $this->assertEquals($courseinfo->fullname, $course1['fullname']);
                $this->assertEquals($courseinfo->shortname, $course1['shortname']);
                $this->assertEquals($courseinfo->category, $course1['categoryid']);
                $this->assertEquals($courseinfo->summaryformat, FORMAT_HTML);
                $this->assertEquals($courseinfo->format, $courseconfig->format);
                $this->assertEquals($courseinfo->showgrades, $courseconfig->showgrades);
                $this->assertEquals($courseinfo->newsitems, $courseconfig->newsitems);
                $this->assertEquals($courseinfo->maxbytes, $courseconfig->maxbytes);
                $this->assertEquals($courseinfo->showreports, $courseconfig->showreports);
                $this->assertEquals($courseinfo->groupmode, $courseconfig->groupmode);
                $this->assertEquals($courseinfo->groupmodeforce, $courseconfig->groupmodeforce);
                $this->assertEquals($courseinfo->defaultgroupingid, 0);
            } else if ($createdcourse['shortname'] == $course3['shortname']) {
                $this->assertEquals($courseinfo->fullname, $course3['fullname']);
                $this->assertEquals($courseinfo->shortname, $course3['shortname']);
                $this->assertEquals($courseinfo->category, $course3['categoryid']);
                $this->assertEquals($courseinfo->format, $course3['format']);
                $this->assertEquals($courseinfo->hiddensections, $course3options['hiddensections']);
                $this->assertEquals(course_get_format($createdcourse['id'])->get_last_section_number(),
                    $course3options['numsections']);
                $this->assertEquals($courseinfo->coursedisplay, $course3options['coursedisplay']);
            } else {
                throw new moodle_exception('Unexpected shortname');
            }
        }

        // Call without required capability
        $this->unassignUserCapability('moodle/course:create', $contextid, $roleid);
        $this->expectException('required_capability_exception');
        $createdsubcats = core_course_external::create_courses($courses);
    }

    /**
     * Test delete_courses
     */
    public function test_delete_courses() {
        global $DB, $USER;

        $this->resetAfterTest(true);

        // Admin can delete a course.
        $this->setAdminUser();
        // Validate_context() will fail as the email is not set by $this->setAdminUser().
        $USER->email = 'emailtopass@example.com';

        $course1  = self::getDataGenerator()->create_course();
        $course2  = self::getDataGenerator()->create_course();
        $course3  = self::getDataGenerator()->create_course();

        // Delete courses.
        $result = core_course_external::delete_courses(array($course1->id, $course2->id));
        $result = external_api::clean_returnvalue(core_course_external::delete_courses_returns(), $result);
        // Check for 0 warnings.
        $this->assertEquals(0, count($result['warnings']));

        // Check $course 1 and 2 are deleted.
        $notdeletedcount = $DB->count_records_select('course',
            'id IN ( ' . $course1->id . ',' . $course2->id . ')');
        $this->assertEquals(0, $notdeletedcount);

        // Try to delete non-existent course.
        $result = core_course_external::delete_courses(array($course1->id));
        $result = external_api::clean_returnvalue(core_course_external::delete_courses_returns(), $result);
        // Check for 1 warnings.
        $this->assertEquals(1, count($result['warnings']));

        // Try to delete Frontpage course.
        $result = core_course_external::delete_courses(array(0));
        $result = external_api::clean_returnvalue(core_course_external::delete_courses_returns(), $result);
        // Check for 1 warnings.
        $this->assertEquals(1, count($result['warnings']));

         // Fail when the user has access to course (enrolled) but does not have permission or is not admin.
        $student1 = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student1->id,
                                              $course3->id,
                                              $studentrole->id);
        $this->setUser($student1);
        $result = core_course_external::delete_courses(array($course3->id));
        $result = external_api::clean_returnvalue(core_course_external::delete_courses_returns(), $result);
        // Check for 1 warnings.
        $this->assertEquals(1, count($result['warnings']));

         // Fail when the user is not allow to access the course (enrolled) or is not admin.
        $this->setGuestUser();
        $this->expectException('require_login_exception');

        $result = core_course_external::delete_courses(array($course3->id));
        $result = external_api::clean_returnvalue(core_course_external::delete_courses_returns(), $result);
    }

    /**
     * Test get_courses
     */
    public function test_get_courses () {
        global $DB;

        $this->resetAfterTest(true);

        $generatedcourses = array();
        $coursedata['idnumber'] = 'idnumbercourse1';
        // Adding tags here to check that format_string is applied.
        $coursedata['fullname'] = '<b>Course 1 for PHPunit test</b>';
        $coursedata['shortname'] = '<b>Course 1 for PHPunit test</b>';
        $coursedata['summary'] = 'Course 1 description';
        $coursedata['summaryformat'] = FORMAT_MOODLE;
        $course1  = self::getDataGenerator()->create_course($coursedata);

        $generatedcourses[$course1->id] = $course1;
        $course2  = self::getDataGenerator()->create_course();
        $generatedcourses[$course2->id] = $course2;
        $course3  = self::getDataGenerator()->create_course(array('format' => 'topics'));
        $generatedcourses[$course3->id] = $course3;

        // Set the required capabilities by the external function.
        $context = context_system::instance();
        $roleid = $this->assignUserCapability('moodle/course:view', $context->id);
        $this->assignUserCapability('moodle/course:update',
                context_course::instance($course1->id)->id, $roleid);
        $this->assignUserCapability('moodle/course:update',
                context_course::instance($course2->id)->id, $roleid);
        $this->assignUserCapability('moodle/course:update',
                context_course::instance($course3->id)->id, $roleid);

        $courses = core_course_external::get_courses(array('ids' =>
            array($course1->id, $course2->id)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $courses = external_api::clean_returnvalue(core_course_external::get_courses_returns(), $courses);

        // Check we retrieve the good total number of categories.
        $this->assertEquals(2, count($courses));

        foreach ($courses as $course) {
            $coursecontext = context_course::instance($course['id']);
            $dbcourse = $generatedcourses[$course['id']];
            $this->assertEquals($course['idnumber'], $dbcourse->idnumber);
            $this->assertEquals($course['fullname'], external_format_string($dbcourse->fullname, $coursecontext->id));
            $this->assertEquals($course['displayname'], external_format_string(get_course_display_name_for_list($dbcourse),
                $coursecontext->id));
            // Summary was converted to the HTML format.
            $this->assertEquals($course['summary'], format_text($dbcourse->summary, FORMAT_MOODLE, array('para' => false)));
            $this->assertEquals($course['summaryformat'], FORMAT_HTML);
            $this->assertEquals($course['shortname'], external_format_string($dbcourse->shortname, $coursecontext->id));
            $this->assertEquals($course['categoryid'], $dbcourse->category);
            $this->assertEquals($course['format'], $dbcourse->format);
            $this->assertEquals($course['showgrades'], $dbcourse->showgrades);
            $this->assertEquals($course['newsitems'], $dbcourse->newsitems);
            $this->assertEquals($course['startdate'], $dbcourse->startdate);
            $this->assertEquals($course['enddate'], $dbcourse->enddate);
            $this->assertEquals($course['numsections'], course_get_format($dbcourse)->get_last_section_number());
            $this->assertEquals($course['maxbytes'], $dbcourse->maxbytes);
            $this->assertEquals($course['showreports'], $dbcourse->showreports);
            $this->assertEquals($course['visible'], $dbcourse->visible);
            $this->assertEquals($course['hiddensections'], $dbcourse->hiddensections);
            $this->assertEquals($course['groupmode'], $dbcourse->groupmode);
            $this->assertEquals($course['groupmodeforce'], $dbcourse->groupmodeforce);
            $this->assertEquals($course['defaultgroupingid'], $dbcourse->defaultgroupingid);
            $this->assertEquals($course['completionnotify'], $dbcourse->completionnotify);
            $this->assertEquals($course['lang'], $dbcourse->lang);
            $this->assertEquals($course['forcetheme'], $dbcourse->theme);
            $this->assertEquals($course['enablecompletion'], $dbcourse->enablecompletion);
            if ($dbcourse->format === 'topics') {
                $this->assertEquals($course['courseformatoptions'], array(
                    array('name' => 'hiddensections', 'value' => $dbcourse->hiddensections),
                    array('name' => 'coursedisplay', 'value' => $dbcourse->coursedisplay),
                ));
            }
        }

        // Get all courses in the DB
        $courses = core_course_external::get_courses(array());

        // We need to execute the return values cleaning process to simulate the web service server.
        $courses = external_api::clean_returnvalue(core_course_external::get_courses_returns(), $courses);

        $this->assertEquals($DB->count_records('course'), count($courses));
    }

    /**
     * Test get_courses without capability
     */
    public function test_get_courses_without_capability() {
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $this->setUser($this->getDataGenerator()->create_user());

        // No permissions are required to get the site course.
        $courses = core_course_external::get_courses(array('ids' => [SITEID]));
        $courses = external_api::clean_returnvalue(core_course_external::get_courses_returns(), $courses);

        $this->assertEquals(1, count($courses));
        $this->assertEquals('PHPUnit test site', $courses[0]['fullname']);
        $this->assertEquals('site', $courses[0]['format']);

        // Requesting course without being enrolled or capability to view it will throw an exception.
        try {
            core_course_external::get_courses(array('ids' => [$course1->id]));
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertEquals(1, preg_match('/Course or activity not accessible. \(Not enrolled\)/', $e->getMessage()));
        }
    }

    /**
     * Test search_courses
     */
    public function test_search_courses () {

        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generatedcourses = array();
        $coursedata1['fullname'] = 'FIRST COURSE';
        $course1  = self::getDataGenerator()->create_course($coursedata1);

        $page = new moodle_page();
        $page->set_course($course1);
        $page->blocks->add_blocks([BLOCK_POS_LEFT => ['news_items'], BLOCK_POS_RIGHT => []], 'course-view-*');

        $coursedata2['fullname'] = 'SECOND COURSE';
        $course2  = self::getDataGenerator()->create_course($coursedata2);

        $page = new moodle_page();
        $page->set_course($course2);
        $page->blocks->add_blocks([BLOCK_POS_LEFT => ['news_items'], BLOCK_POS_RIGHT => []], 'course-view-*');
        // Search by name.
        $results = core_course_external::search_courses('search', 'FIRST');
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertEquals($coursedata1['fullname'], $results['courses'][0]['fullname']);
        $this->assertCount(1, $results['courses']);

        // Create the forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course2->id;
        // Set Aggregate type = Average of ratings.
        $forum = self::getDataGenerator()->create_module('forum', $record);

        // Search by module.
        $results = core_course_external::search_courses('modulelist', 'forum');
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertEquals(1, $results['total']);

        // Enable coursetag option.
        set_config('block_tags_showcoursetags', true);
        // Add tag 'TAG-LABEL ON SECOND COURSE' to Course2.
        core_tag_tag::set_item_tags('core', 'course', $course2->id, context_course::instance($course2->id),
                array('TAG-LABEL ON SECOND COURSE'));
        $taginstance = $DB->get_record('tag_instance',
                array('itemtype' => 'course', 'itemid' => $course2->id), '*', MUST_EXIST);
        // Search by tagid.
        $results = core_course_external::search_courses('tagid', $taginstance->tagid);
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertEquals($coursedata2['fullname'], $results['courses'][0]['fullname']);

        // Search by block (use news_items default block).
        $blockid = $DB->get_field('block', 'id', array('name' => 'news_items'));
        $results = core_course_external::search_courses('blocklist', $blockid);
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertEquals(2, $results['total']);

        // Now as a normal user.
        $user = self::getDataGenerator()->create_user();

        // Add a 3rd, hidden, course we shouldn't see, even when enrolled as student.
        $coursedata3['fullname'] = 'HIDDEN COURSE';
        $coursedata3['visible'] = 0;
        $course3  = self::getDataGenerator()->create_course($coursedata3);
        $this->getDataGenerator()->enrol_user($user->id, $course3->id, 'student');

        $this->getDataGenerator()->enrol_user($user->id, $course2->id, 'student');
        $this->setUser($user);

        $results = core_course_external::search_courses('search', 'FIRST');
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertCount(1, $results['courses']);
        $this->assertEquals(1, $results['total']);
        $this->assertEquals($coursedata1['fullname'], $results['courses'][0]['fullname']);

        // Check that we can see both without the limit to enrolled setting.
        $results = core_course_external::search_courses('search', 'COURSE', 0, 0, array(), 0);
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertCount(2, $results['courses']);
        $this->assertEquals(2, $results['total']);

        // Check that we only see our enrolled course when limiting.
        $results = core_course_external::search_courses('search', 'COURSE', 0, 0, array(), 1);
        $results = external_api::clean_returnvalue(core_course_external::search_courses_returns(), $results);
        $this->assertCount(1, $results['courses']);
        $this->assertEquals(1, $results['total']);
        $this->assertEquals($coursedata2['fullname'], $results['courses'][0]['fullname']);

        // Search by block (use news_items default block). Should fail (only admins allowed).
        $this->expectException('required_capability_exception');
        $results = core_course_external::search_courses('blocklist', $blockid);

    }

    /**
     * Create a course with contents
     * @return array A list with the course object and course modules objects
     */
    private function prepare_get_course_contents_test() {
        global $DB, $CFG;

        $CFG->allowstealth = 1; // Allow stealth activities.
        $CFG->enablecompletion = true;
        $course  = self::getDataGenerator()->create_course(['numsections' => 4, 'enablecompletion' => 1]);

        $forumdescription = 'This is the forum description';
        $forum = $this->getDataGenerator()->create_module('forum',
            array('course' => $course->id, 'intro' => $forumdescription, 'trackingtype' => 2),
            array('showdescription' => true, 'completion' => COMPLETION_TRACKING_MANUAL));
        $forumcm = get_coursemodule_from_id('forum', $forum->cmid);
        // Add discussions to the tracking forced forum.
        $record = new stdClass();
        $record->course = $course->id;
        $record->userid = 0;
        $record->forum = $forum->id;
        $discussionforce = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
        $data = $this->getDataGenerator()->create_module('data',
            array('assessed' => 1, 'scale' => 100, 'course' => $course->id, 'completion' => 2, 'completionentries' => 3));
        $datacm = get_coursemodule_from_instance('data', $data->id);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $pagecm = get_coursemodule_from_instance('page', $page->id);
        // This is an stealth page (set by visibleoncoursepage).
        $pagestealth = $this->getDataGenerator()->create_module('page', array('course' => $course->id, 'visibleoncoursepage' => 0));
        $labeldescription = 'This is a very long label to test if more than 50 characters are returned.
                So bla bla bla bla <b>bold bold bold</b> bla bla bla bla.';
        $label = $this->getDataGenerator()->create_module('label', array('course' => $course->id,
            'intro' => $labeldescription));
        $labelcm = get_coursemodule_from_instance('label', $label->id);
        $tomorrow = time() + DAYSECS;
        // Module with availability restrictions not met.
        $url = $this->getDataGenerator()->create_module('url',
            array('course' => $course->id, 'name' => 'URL: % & $ ../', 'section' => 2, 'display' => RESOURCELIB_DISPLAY_POPUP,
                'popupwidth' => 100, 'popupheight' => 100),
            array('availability' => '{"op":"&","c":[{"type":"date","d":">=","t":' . $tomorrow . '}],"showc":[true]}'));
        $urlcm = get_coursemodule_from_instance('url', $url->id);
        // Module for the last section.
        $this->getDataGenerator()->create_module('url',
            array('course' => $course->id, 'name' => 'URL for last section', 'section' => 3));
        // Module for section 1 with availability restrictions met.
        $yesterday = time() - DAYSECS;
        $this->getDataGenerator()->create_module('url',
            array('course' => $course->id, 'name' => 'URL restrictions met', 'section' => 1),
            array('availability' => '{"op":"&","c":[{"type":"date","d":">=","t":'. $yesterday .'}],"showc":[true]}'));

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:view', $context->id);
        $this->assignUserCapability('moodle/course:update', $context->id, $roleid);
        $this->assignUserCapability('mod/data:view', $context->id, $roleid);

        $conditions = array('course' => $course->id, 'section' => 2);
        $DB->set_field('course_sections', 'summary', 'Text with iframe <iframe src="https://moodle.org"></iframe>', $conditions);

        // Add date availability condition not met for section 3.
        $availability = '{"op":"&","c":[{"type":"date","d":">=","t":' . $tomorrow . '}],"showc":[true]}';
        $DB->set_field('course_sections', 'availability', $availability,
                array('course' => $course->id, 'section' => 3));

        // Create resource for last section.
        $pageinhiddensection = $this->getDataGenerator()->create_module('page',
            array('course' => $course->id, 'name' => 'Page in hidden section', 'section' => 4));
        // Set not visible last section.
        $DB->set_field('course_sections', 'visible', 0,
                array('course' => $course->id, 'section' => 4));

        rebuild_course_cache($course->id, true);

        return array($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm);
    }

    /**
     * Test get_course_contents
     */
    public function test_get_course_contents() {
        global $CFG;
        $this->resetAfterTest(true);

        $CFG->forum_allowforcedreadtracking = 1;
        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // We first run the test as admin.
        $this->setAdminUser();
        $sections = core_course_external::get_course_contents($course->id, array());
        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $modinfo = get_fast_modinfo($course);
        $testexecuted = 0;
        foreach ($sections[0]['modules'] as $module) {
            if ($module['id'] == $forumcm->id and $module['modname'] == 'forum') {
                $cm = $modinfo->cms[$forumcm->id];
                $formattedtext = format_text($cm->content, FORMAT_HTML,
                    array('noclean' => true, 'para' => false, 'filter' => false));
                $this->assertEquals($formattedtext, $module['description']);
                $this->assertEquals($forumcm->instance, $module['instance']);
                $this->assertContains('1 unread post', $module['afterlink']);
                $testexecuted = $testexecuted + 2;
            } else if ($module['id'] == $labelcm->id and $module['modname'] == 'label') {
                $cm = $modinfo->cms[$labelcm->id];
                $formattedtext = format_text($cm->content, FORMAT_HTML,
                    array('noclean' => true, 'para' => false, 'filter' => false));
                $this->assertEquals($formattedtext, $module['description']);
                $this->assertEquals($labelcm->instance, $module['instance']);
                $testexecuted = $testexecuted + 1;
            } else if ($module['id'] == $datacm->id and $module['modname'] == 'data') {
                $this->assertContains('customcompletionrules', $module['customdata']);
                $testexecuted = $testexecuted + 1;
            }
        }
        foreach ($sections[2]['modules'] as $module) {
            if ($module['id'] == $urlcm->id and $module['modname'] == 'url') {
                $this->assertContains('width=100,height=100', $module['onclick']);
                $testexecuted = $testexecuted + 1;
            }
        }

        $CFG->forum_allowforcedreadtracking = 0;    // Recover original value.
        forum_tp_count_forum_unread_posts($forumcm, $course, true);    // Reset static cache for further tests.

        $this->assertEquals(5, $testexecuted);
        $this->assertEquals(0, $sections[0]['section']);

        $this->assertCount(5, $sections[0]['modules']);
        $this->assertCount(1, $sections[1]['modules']);
        $this->assertCount(1, $sections[2]['modules']);
        $this->assertCount(1, $sections[3]['modules']); // One module for the section with availability restrictions.
        $this->assertCount(1, $sections[4]['modules']); // One module for the hidden section with a visible activity.
        $this->assertNotEmpty($sections[3]['availabilityinfo']);
        $this->assertEquals(1, $sections[1]['section']);
        $this->assertEquals(2, $sections[2]['section']);
        $this->assertEquals(3, $sections[3]['section']);
        $this->assertEquals(4, $sections[4]['section']);
        $this->assertContains('<iframe', $sections[2]['summary']);
        $this->assertContains('</iframe>', $sections[2]['summary']);
        $this->assertNotEmpty($sections[2]['modules'][0]['availabilityinfo']);
        try {
            $sections = core_course_external::get_course_contents($course->id,
                                                                    array(array("name" => "invalid", "value" => 1)));
            $this->fail('Exception expected due to invalid option.');
        } catch (moodle_exception $e) {
            $this->assertEquals('errorinvalidparam', $e->errorcode);
        }
    }


    /**
     * Test get_course_contents as student
     */
    public function test_get_course_contents_student() {
        global $DB;
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $user = self::getDataGenerator()->create_user();
        self::getDataGenerator()->enrol_user($user->id, $course->id, $studentroleid);
        $this->setUser($user);

        $sections = core_course_external::get_course_contents($course->id, array());
        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(4, $sections); // Nothing for the not visible section.
        $this->assertCount(5, $sections[0]['modules']);
        $this->assertCount(1, $sections[1]['modules']);
        $this->assertCount(1, $sections[2]['modules']);
        $this->assertCount(0, $sections[3]['modules']); // No modules for the section with availability restrictions.

        $this->assertNotEmpty($sections[3]['availabilityinfo']);
        $this->assertEquals(1, $sections[1]['section']);
        $this->assertEquals(2, $sections[2]['section']);
        $this->assertEquals(3, $sections[3]['section']);
        // The module with the availability restriction met is returning contents.
        $this->assertNotEmpty($sections[1]['modules'][0]['contents']);
        // The module with the availability restriction not met is not returning contents.
        $this->assertArrayNotHasKey('contents', $sections[2]['modules'][0]);

        // Now include flag for returning stealth information (fake section).
        $sections = core_course_external::get_course_contents($course->id,
            array(array("name" => "includestealthmodules", "value" => 1)));
        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(5, $sections); // Include fake section with stealth activities.
        $this->assertCount(5, $sections[0]['modules']);
        $this->assertCount(1, $sections[1]['modules']);
        $this->assertCount(1, $sections[2]['modules']);
        $this->assertCount(0, $sections[3]['modules']); // No modules for the section with availability restrictions.
        $this->assertCount(1, $sections[4]['modules']); // One stealh module.
        $this->assertEquals(-1, $sections[4]['id']);
    }

    /**
     * Test get_course_contents excluding modules
     */
    public function test_get_course_contents_excluding_modules() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(array("name" => "excludemodules", "value" => 1)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertEmpty($sections[0]['modules']);
        $this->assertEmpty($sections[1]['modules']);
    }

    /**
     * Test get_course_contents excluding contents
     */
    public function test_get_course_contents_excluding_contents() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(array("name" => "excludecontents", "value" => 1)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        foreach ($sections as $section) {
            foreach ($section['modules'] as $module) {
                // Only resources return contents.
                if (isset($module['contents'])) {
                    $this->assertEmpty($module['contents']);
                }
            }
        }
    }

    /**
     * Test get_course_contents filtering by section number
     */
    public function test_get_course_contents_section_number() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(array("name" => "sectionnumber", "value" => 0)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(1, $sections);
        $this->assertCount(5, $sections[0]['modules']);
    }

    /**
     * Test get_course_contents filtering by cmid
     */
    public function test_get_course_contents_cmid() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(array("name" => "cmid", "value" => $forumcm->id)));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(4, $sections);
        $this->assertCount(1, $sections[0]['modules']);
        $this->assertEquals($forumcm->id, $sections[0]['modules'][0]["id"]);
    }


    /**
     * Test get_course_contents filtering by cmid and section
     */
    public function test_get_course_contents_section_cmid() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(
                                                                        array("name" => "cmid", "value" => $forumcm->id),
                                                                        array("name" => "sectionnumber", "value" => 0)
                                                                        ));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(1, $sections);
        $this->assertCount(1, $sections[0]['modules']);
        $this->assertEquals($forumcm->id, $sections[0]['modules'][0]["id"]);
    }

    /**
     * Test get_course_contents filtering by modname
     */
    public function test_get_course_contents_modname() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(array("name" => "modname", "value" => "forum")));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(4, $sections);
        $this->assertCount(1, $sections[0]['modules']);
        $this->assertEquals($forumcm->id, $sections[0]['modules'][0]["id"]);
    }

    /**
     * Test get_course_contents filtering by modname
     */
    public function test_get_course_contents_modid() {
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test exclude modules.
        $sections = core_course_external::get_course_contents($course->id, array(
                                                                            array("name" => "modname", "value" => "page"),
                                                                            array("name" => "modid", "value" => $pagecm->instance),
                                                                            ));

        // We need to execute the return values cleaning process to simulate the web service server.
        $sections = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $sections);

        $this->assertCount(4, $sections);
        $this->assertCount(1, $sections[0]['modules']);
        $this->assertEquals("page", $sections[0]['modules'][0]["modname"]);
        $this->assertEquals($pagecm->instance, $sections[0]['modules'][0]["instance"]);
    }

    /**
     * Test get course contents completion
     */
    public function test_get_course_contents_completion() {
        global $CFG;
        $this->resetAfterTest(true);

        list($course, $forumcm, $datacm, $pagecm, $labelcm, $urlcm) = $this->prepare_get_course_contents_test();

        // Test activity not completed yet.
        $result = core_course_external::get_course_contents($course->id, array(
            array("name" => "modname", "value" => "forum"), array("name" => "modid", "value" => $forumcm->instance)));
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $result);

        $this->assertCount(1, $result[0]['modules']);
        $this->assertEquals("forum", $result[0]['modules'][0]["modname"]);
        $this->assertEquals(COMPLETION_TRACKING_MANUAL, $result[0]['modules'][0]["completion"]);
        $this->assertEquals(0, $result[0]['modules'][0]["completiondata"]['state']);
        $this->assertEquals(0, $result[0]['modules'][0]["completiondata"]['timecompleted']);
        $this->assertEmpty($result[0]['modules'][0]["completiondata"]['overrideby']);

        // Set activity completed.
        core_completion_external::update_activity_completion_status_manually($forumcm->id, true);

        $result = core_course_external::get_course_contents($course->id, array(
            array("name" => "modname", "value" => "forum"), array("name" => "modid", "value" => $forumcm->instance)));
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $result);

        $this->assertEquals(COMPLETION_COMPLETE, $result[0]['modules'][0]["completiondata"]['state']);
        $this->assertNotEmpty($result[0]['modules'][0]["completiondata"]['timecompleted']);
        $this->assertEmpty($result[0]['modules'][0]["completiondata"]['overrideby']);

        // Disable completion.
        $CFG->enablecompletion = 0;
        $result = core_course_external::get_course_contents($course->id, array(
            array("name" => "modname", "value" => "forum"), array("name" => "modid", "value" => $forumcm->instance)));
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_course_external::get_course_contents_returns(), $result);

        $this->assertArrayNotHasKey('completiondata', $result[0]['modules'][0]);
    }

    /**
     * Test duplicate_course
     */
    public function test_duplicate_course() {
        $this->resetAfterTest(true);

        // Create one course with three modules.
        $course  = self::getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $forumcm = get_coursemodule_from_id('forum', $forum->cmid);
        $forumcontext = context_module::instance($forum->cmid);
        $data = $this->getDataGenerator()->create_module('data', array('assessed'=>1, 'scale'=>100, 'course'=>$course->id));
        $datacontext = context_module::instance($data->cmid);
        $datacm = get_coursemodule_from_instance('page', $data->id);
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course->id));
        $pagecontext = context_module::instance($page->cmid);
        $pagecm = get_coursemodule_from_instance('page', $page->id);

        // Set the required capabilities by the external function.
        $coursecontext = context_course::instance($course->id);
        $categorycontext = context_coursecat::instance($course->category);
        $roleid = $this->assignUserCapability('moodle/course:create', $categorycontext->id);
        $this->assignUserCapability('moodle/course:view', $categorycontext->id, $roleid);
        $this->assignUserCapability('moodle/restore:restorecourse', $categorycontext->id, $roleid);
        $this->assignUserCapability('moodle/backup:backupcourse', $coursecontext->id, $roleid);
        $this->assignUserCapability('moodle/backup:configure', $coursecontext->id, $roleid);
        // Optional capabilities to copy user data.
        $this->assignUserCapability('moodle/backup:userinfo', $coursecontext->id, $roleid);
        $this->assignUserCapability('moodle/restore:userinfo', $categorycontext->id, $roleid);

        $newcourse['fullname'] = 'Course duplicate';
        $newcourse['shortname'] = 'courseduplicate';
        $newcourse['categoryid'] = $course->category;
        $newcourse['visible'] = true;
        $newcourse['options'][] = array('name' => 'users', 'value' => true);

        $duplicate = core_course_external::duplicate_course($course->id, $newcourse['fullname'],
                $newcourse['shortname'], $newcourse['categoryid'], $newcourse['visible'], $newcourse['options']);

        // We need to execute the return values cleaning process to simulate the web service server.
        $duplicate = external_api::clean_returnvalue(core_course_external::duplicate_course_returns(), $duplicate);

        // Check that the course has been duplicated.
        $this->assertEquals($newcourse['shortname'], $duplicate['shortname']);
    }

    /**
     * Test update_courses
     */
    public function test_update_courses() {
        global $DB, $CFG, $USER, $COURSE;

        // Get current $COURSE to be able to restore it later (defaults to $SITE). We need this
        // trick because we are both updating and getting (for testing) course information
        // in the same request and core_course_external::update_courses()
        // is overwriting $COURSE all over the time with OLD values, so later
        // use of get_course() fetches those OLD values instead of the updated ones.
        // See MDL-39723 for more info.
        $origcourse = clone($COURSE);

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function.
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/course:update', $contextid);
        $this->assignUserCapability('moodle/course:changecategory', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:changefullname', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:changeshortname', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:changeidnumber', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:changesummary', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:visibility', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:viewhiddencourses', $contextid, $roleid);
        $this->assignUserCapability('moodle/course:setforcedlanguage', $contextid, $roleid);

        // Create category and course.
        $category1  = self::getDataGenerator()->create_category();
        $category2  = self::getDataGenerator()->create_category();
        $originalcourse1 = self::getDataGenerator()->create_course();
        self::getDataGenerator()->enrol_user($USER->id, $originalcourse1->id, $roleid);
        $originalcourse2 = self::getDataGenerator()->create_course();
        self::getDataGenerator()->enrol_user($USER->id, $originalcourse2->id, $roleid);

        // Course values to be updated.
        $course1['id'] = $originalcourse1->id;
        $course1['fullname'] = 'Updated test course 1';
        $course1['shortname'] = 'Udestedtestcourse1';
        $course1['categoryid'] = $category1->id;
        $course2['id'] = $originalcourse2->id;
        $course2['fullname'] = 'Updated test course 2';
        $course2['shortname'] = 'Updestedtestcourse2';
        $course2['categoryid'] = $category2->id;
        $course2['idnumber'] = 'Updatedidnumber2';
        $course2['summary'] = 'Updaated description for course 2';
        $course2['summaryformat'] = FORMAT_HTML;
        $course2['format'] = 'topics';
        $course2['showgrades'] = 1;
        $course2['newsitems'] = 3;
        $course2['startdate'] = 1420092000; // 01/01/2015.
        $course2['enddate'] = 1422669600; // 01/31/2015.
        $course2['maxbytes'] = 100000;
        $course2['showreports'] = 1;
        $course2['visible'] = 0;
        $course2['hiddensections'] = 0;
        $course2['groupmode'] = 0;
        $course2['groupmodeforce'] = 0;
        $course2['defaultgroupingid'] = 0;
        $course2['enablecompletion'] = 1;
        $course2['lang'] = 'en';
        $course2['forcetheme'] = 'bootstrapbase';
        $courses = array($course1, $course2);

        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $COURSE = $origcourse; // Restore $COURSE. Instead of using the OLD one set by the previous line.

        // Check that right number of courses were created.
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));

        // Check that the courses were correctly created.
        foreach ($courses as $course) {
            $courseinfo = course_get_format($course['id'])->get_course();
            if ($course['id'] == $course2['id']) {
                $this->assertEquals($course2['fullname'], $courseinfo->fullname);
                $this->assertEquals($course2['shortname'], $courseinfo->shortname);
                $this->assertEquals($course2['categoryid'], $courseinfo->category);
                $this->assertEquals($course2['idnumber'], $courseinfo->idnumber);
                $this->assertEquals($course2['summary'], $courseinfo->summary);
                $this->assertEquals($course2['summaryformat'], $courseinfo->summaryformat);
                $this->assertEquals($course2['format'], $courseinfo->format);
                $this->assertEquals($course2['showgrades'], $courseinfo->showgrades);
                $this->assertEquals($course2['newsitems'], $courseinfo->newsitems);
                $this->assertEquals($course2['startdate'], $courseinfo->startdate);
                $this->assertEquals($course2['enddate'], $courseinfo->enddate);
                $this->assertEquals($course2['maxbytes'], $courseinfo->maxbytes);
                $this->assertEquals($course2['showreports'], $courseinfo->showreports);
                $this->assertEquals($course2['visible'], $courseinfo->visible);
                $this->assertEquals($course2['hiddensections'], $courseinfo->hiddensections);
                $this->assertEquals($course2['groupmode'], $courseinfo->groupmode);
                $this->assertEquals($course2['groupmodeforce'], $courseinfo->groupmodeforce);
                $this->assertEquals($course2['defaultgroupingid'], $courseinfo->defaultgroupingid);
                $this->assertEquals($course2['lang'], $courseinfo->lang);

                if (!empty($CFG->allowcoursethemes)) {
                    $this->assertEquals($course2['forcetheme'], $courseinfo->theme);
                }

                $this->assertEquals($course2['enablecompletion'], $courseinfo->enablecompletion);
            } else if ($course['id'] == $course1['id']) {
                $this->assertEquals($course1['fullname'], $courseinfo->fullname);
                $this->assertEquals($course1['shortname'], $courseinfo->shortname);
                $this->assertEquals($course1['categoryid'], $courseinfo->category);
                $this->assertEquals(FORMAT_MOODLE, $courseinfo->summaryformat);
                $this->assertEquals('topics', $courseinfo->format);
                $this->assertEquals(5, course_get_format($course['id'])->get_last_section_number());
                $this->assertEquals(0, $courseinfo->newsitems);
                $this->assertEquals(FORMAT_MOODLE, $courseinfo->summaryformat);
            } else {
                throw new moodle_exception('Unexpected shortname');
            }
        }

        $courses = array($course1);
        // Try update course without update capability.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        $this->unassignUserCapability('moodle/course:update', $contextid, $roleid);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course category without capability.
        $this->assignUserCapability('moodle/course:update', $contextid, $roleid);
        $this->unassignUserCapability('moodle/course:changecategory', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $course1['categoryid'] = $category2->id;
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course fullname without capability.
        $this->assignUserCapability('moodle/course:changecategory', $contextid, $roleid);
        $this->unassignUserCapability('moodle/course:changefullname', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));
        $course1['fullname'] = 'Testing fullname without permission';
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course shortname without capability.
        $this->assignUserCapability('moodle/course:changefullname', $contextid, $roleid);
        $this->unassignUserCapability('moodle/course:changeshortname', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));
        $course1['shortname'] = 'Testing shortname without permission';
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course idnumber without capability.
        $this->assignUserCapability('moodle/course:changeshortname', $contextid, $roleid);
        $this->unassignUserCapability('moodle/course:changeidnumber', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));
        $course1['idnumber'] = 'NEWIDNUMBER';
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course summary without capability.
        $this->assignUserCapability('moodle/course:changeidnumber', $contextid, $roleid);
        $this->unassignUserCapability('moodle/course:changesummary', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));
        $course1['summary'] = 'New summary';
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course with invalid summary format.
        $this->assignUserCapability('moodle/course:changesummary', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));
        $course1['summaryformat'] = 10;
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));

        // Try update course visibility without capability.
        $this->unassignUserCapability('moodle/course:visibility', $contextid, $roleid);
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        self::getDataGenerator()->enrol_user($user->id, $course1['id'], $roleid);
        $course1['summaryformat'] = FORMAT_MOODLE;
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(0, count($updatedcoursewarnings['warnings']));
        $course1['visible'] = 0;
        $courses = array($course1);
        $updatedcoursewarnings = core_course_external::update_courses($courses);
        $updatedcoursewarnings = external_api::clean_returnvalue(core_course_external::update_courses_returns(),
                                                                    $updatedcoursewarnings);
        $this->assertEquals(1, count($updatedcoursewarnings['warnings']));
    }

    /**
     * Test delete course_module.
     */
    public function test_delete_modules() {
        global $DB;

        // Ensure we reset the data after this test.
        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user();

        // Set the tests to run as the user.
        self::setUser($user);

        // Create a course to add the modules.
        $course = self::getDataGenerator()->create_course();

        // Create two test modules.
        $record = new stdClass();
        $record->course = $course->id;
        $module1 = self::getDataGenerator()->create_module('forum', $record);
        $module2 = self::getDataGenerator()->create_module('assign', $record);

        // Check the forum was correctly created.
        $this->assertEquals(1, $DB->count_records('forum', array('id' => $module1->id)));

        // Check the assignment was correctly created.
        $this->assertEquals(1, $DB->count_records('assign', array('id' => $module2->id)));

        // Check data exists in the course modules table.
        $this->assertEquals(2, $DB->count_records_select('course_modules', 'id = :module1 OR id = :module2',
                array('module1' => $module1->cmid, 'module2' => $module2->cmid)));

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance, $user->id);

        // Assign capabilities to delete module 1.
        $modcontext = context_module::instance($module1->cmid);
        $this->assignUserCapability('moodle/course:manageactivities', $modcontext->id);

        // Assign capabilities to delete module 2.
        $modcontext = context_module::instance($module2->cmid);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $this->assignUserCapability('moodle/course:manageactivities', $modcontext->id, $newrole);

        // Deleting these module instances.
        core_course_external::delete_modules(array($module1->cmid, $module2->cmid));

        // Check the forum was deleted.
        $this->assertEquals(0, $DB->count_records('forum', array('id' => $module1->id)));

        // Check the assignment was deleted.
        $this->assertEquals(0, $DB->count_records('assign', array('id' => $module2->id)));

        // Check we retrieve no data in the course modules table.
        $this->assertEquals(0, $DB->count_records_select('course_modules', 'id = :module1 OR id = :module2',
                array('module1' => $module1->cmid, 'module2' => $module2->cmid)));

        // Call with non-existent course module id and ensure exception thrown.
        try {
            core_course_external::delete_modules(array('1337'));
            $this->fail('Exception expected due to missing course module.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidcoursemodule', $e->errorcode);
        }

        // Create two modules.
        $module1 = self::getDataGenerator()->create_module('forum', $record);
        $module2 = self::getDataGenerator()->create_module('assign', $record);

        // Since these modules were recreated the user will not have capabilities
        // to delete them, ensure exception is thrown if they try.
        try {
            core_course_external::delete_modules(array($module1->cmid, $module2->cmid));
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Unenrol user from the course.
        $enrol->unenrol_user($instance, $user->id);

        // Try and delete modules from the course the user was unenrolled in, make sure exception thrown.
        try {
            core_course_external::delete_modules(array($module1->cmid, $module2->cmid));
            $this->fail('Exception expected due to being unenrolled from the course.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }
    }

    /**
     * Test import_course into an empty course
     */
    public function test_import_course_empty() {
        global $USER;

        $this->resetAfterTest(true);

        $course1  = self::getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id, 'name' => 'Forum test'));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course1->id, 'name' => 'Page test'));

        $course2  = self::getDataGenerator()->create_course();

        $course1cms = get_fast_modinfo($course1->id)->get_cms();
        $course2cms = get_fast_modinfo($course2->id)->get_cms();

        // Verify the state of the courses before we do the import.
        $this->assertCount(2, $course1cms);
        $this->assertEmpty($course2cms);

        // Setup the user to run the operation (ugly hack because validate_context() will
        // fail as the email is not set by $this->setAdminUser()).
        $this->setAdminUser();
        $USER->email = 'emailtopass@example.com';

        // Import from course1 to course2.
        core_course_external::import_course($course1->id, $course2->id, 0);

        // Verify that now we have two modules in both courses.
        $course1cms = get_fast_modinfo($course1->id)->get_cms();
        $course2cms = get_fast_modinfo($course2->id)->get_cms();
        $this->assertCount(2, $course1cms);
        $this->assertCount(2, $course2cms);

        // Verify that the names transfered across correctly.
        foreach ($course2cms as $cm) {
            if ($cm->modname === 'page') {
                $this->assertEquals($cm->name, $page->name);
            } else if ($cm->modname === 'forum') {
                $this->assertEquals($cm->name, $forum->name);
            } else {
                $this->fail('Unknown CM found.');
            }
        }
    }

    /**
     * Test import_course into an filled course
     */
    public function test_import_course_filled() {
        global $USER;

        $this->resetAfterTest(true);

        // Add forum and page to course1.
        $course1  = self::getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course1->id, 'name' => 'Forum test'));
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course1->id, 'name' => 'Page test'));

        // Add quiz to course 2.
        $course2  = self::getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course'=>$course2->id, 'name' => 'Page test'));

        $course1cms = get_fast_modinfo($course1->id)->get_cms();
        $course2cms = get_fast_modinfo($course2->id)->get_cms();

        // Verify the state of the courses before we do the import.
        $this->assertCount(2, $course1cms);
        $this->assertCount(1, $course2cms);

        // Setup the user to run the operation (ugly hack because validate_context() will
        // fail as the email is not set by $this->setAdminUser()).
        $this->setAdminUser();
        $USER->email = 'emailtopass@example.com';

        // Import from course1 to course2 without deleting content.
        core_course_external::import_course($course1->id, $course2->id, 0);

        $course2cms = get_fast_modinfo($course2->id)->get_cms();

        // Verify that now we have three modules in course2.
        $this->assertCount(3, $course2cms);

        // Verify that the names transfered across correctly.
        foreach ($course2cms as $cm) {
            if ($cm->modname === 'page') {
                $this->assertEquals($cm->name, $page->name);
            } else if ($cm->modname === 'forum') {
                $this->assertEquals($cm->name, $forum->name);
            } else if ($cm->modname === 'quiz') {
                $this->assertEquals($cm->name, $quiz->name);
            } else {
                $this->fail('Unknown CM found.');
            }
        }
    }

    /**
     * Test import_course with only blocks set to backup
     */
    public function test_import_course_blocksonly() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        // Add forum and page to course1.
        $course1  = self::getDataGenerator()->create_course();
        $course1ctx = context_course::instance($course1->id);
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course1->id, 'name' => 'Forum test'));
        $block = $this->getDataGenerator()->create_block('online_users', array('parentcontextid' => $course1ctx->id));

        $course2  = self::getDataGenerator()->create_course();
        $course2ctx = context_course::instance($course2->id);
        $initialblockcount = $DB->count_records('block_instances', array('parentcontextid' => $course2ctx->id));
        $initialcmcount = count(get_fast_modinfo($course2->id)->get_cms());

        // Setup the user to run the operation (ugly hack because validate_context() will
        // fail as the email is not set by $this->setAdminUser()).
        $this->setAdminUser();
        $USER->email = 'emailtopass@example.com';

        // Import from course1 to course2 without deleting content, but excluding
        // activities.
        $options = array(
            array('name' => 'activities', 'value' => 0),
            array('name' => 'blocks', 'value' => 1),
            array('name' => 'filters', 'value' => 0),
        );

        core_course_external::import_course($course1->id, $course2->id, 0, $options);

        $newcmcount = count(get_fast_modinfo($course2->id)->get_cms());
        $newblockcount = $DB->count_records('block_instances', array('parentcontextid' => $course2ctx->id));
        // Check that course modules haven't changed, but that blocks have.
        $this->assertEquals($initialcmcount, $newcmcount);
        $this->assertEquals(($initialblockcount + 1), $newblockcount);
    }

    /**
     * Test import_course into an filled course, deleting content.
     */
    public function test_import_course_deletecontent() {
        global $USER;
        $this->resetAfterTest(true);

        // Add forum and page to course1.
        $course1  = self::getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course1->id, 'name' => 'Forum test'));
        $page = $this->getDataGenerator()->create_module('page', array('course'=>$course1->id, 'name' => 'Page test'));

        // Add quiz to course 2.
        $course2  = self::getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course'=>$course2->id, 'name' => 'Page test'));

        $course1cms = get_fast_modinfo($course1->id)->get_cms();
        $course2cms = get_fast_modinfo($course2->id)->get_cms();

        // Verify the state of the courses before we do the import.
        $this->assertCount(2, $course1cms);
        $this->assertCount(1, $course2cms);

        // Setup the user to run the operation (ugly hack because validate_context() will
        // fail as the email is not set by $this->setAdminUser()).
        $this->setAdminUser();
        $USER->email = 'emailtopass@example.com';

        // Import from course1 to course2,  deleting content.
        core_course_external::import_course($course1->id, $course2->id, 1);

        $course2cms = get_fast_modinfo($course2->id)->get_cms();

        // Verify that now we have two modules in course2.
        $this->assertCount(2, $course2cms);

        // Verify that the course only contains the imported modules.
        foreach ($course2cms as $cm) {
            if ($cm->modname === 'page') {
                $this->assertEquals($cm->name, $page->name);
            } else if ($cm->modname === 'forum') {
                $this->assertEquals($cm->name, $forum->name);
            } else {
                $this->fail('Unknown CM found: '.$cm->name);
            }
        }
    }

    /**
     * Ensure import_course handles incorrect deletecontent option correctly.
     */
    public function test_import_course_invalid_deletecontent_option() {
        $this->resetAfterTest(true);

        $course1  = self::getDataGenerator()->create_course();
        $course2  = self::getDataGenerator()->create_course();

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('invalidextparam', 'webservice', -1));
        // Import from course1 to course2, with invalid option
        core_course_external::import_course($course1->id, $course2->id, -1);;
    }

    /**
     * Test view_course function
     */
    public function test_view_course() {

        $this->resetAfterTest();

        // Course without sections.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5), array('createsections' => true));
        $this->setAdminUser();

        // Redirect events to the sink, so we can recover them later.
        $sink = $this->redirectEvents();

        $result = core_course_external::view_course($course->id, 1);
        $result = external_api::clean_returnvalue(core_course_external::view_course_returns(), $result);
        $events = $sink->get_events();
        $event = reset($events);

        // Check the event details are correct.
        $this->assertInstanceOf('\core\event\course_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals(1, $event->other['coursesectionnumber']);

        $result = core_course_external::view_course($course->id);
        $result = external_api::clean_returnvalue(core_course_external::view_course_returns(), $result);
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        // Check the event details are correct.
        $this->assertInstanceOf('\core\event\course_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEmpty($event->other);

    }

    /**
     * Test get_course_module
     */
    public function test_get_course_module() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $record = array(
            'course' => $course->id,
            'name' => 'First Assignment'
        );
        $options = array(
            'idnumber' => 'ABC',
            'visible' => 0
        );
        // Hidden activity.
        $assign = self::getDataGenerator()->create_module('assign', $record, $options);

        $outcomescale = 'Distinction, Very Good, Good, Pass, Fail';

        // Insert a custom grade scale to be used by an outcome.
        $gradescale = new grade_scale();
        $gradescale->name        = 'gettcoursemodulescale';
        $gradescale->courseid    = $course->id;
        $gradescale->userid      = 0;
        $gradescale->scale       = $outcomescale;
        $gradescale->description = 'This scale is used to mark standard assignments.';
        $gradescale->insert();

        // Insert an outcome.
        $data = new stdClass();
        $data->courseid = $course->id;
        $data->fullname = 'Team work';
        $data->shortname = 'Team work';
        $data->scaleid = $gradescale->id;
        $outcome = new grade_outcome($data, false);
        $outcome->insert();

        $outcomegradeitem = new grade_item();
        $outcomegradeitem->itemname = $outcome->shortname;
        $outcomegradeitem->itemtype = 'mod';
        $outcomegradeitem->itemmodule = 'assign';
        $outcomegradeitem->iteminstance = $assign->id;
        $outcomegradeitem->outcomeid = $outcome->id;
        $outcomegradeitem->cmid = 0;
        $outcomegradeitem->courseid = $course->id;
        $outcomegradeitem->aggregationcoef = 0;
        $outcomegradeitem->itemnumber = 1; // The activity's original grade item will be 0.
        $outcomegradeitem->gradetype = GRADE_TYPE_SCALE;
        $outcomegradeitem->scaleid = $outcome->scaleid;
        $outcomegradeitem->insert();

        $assignmentgradeitem = grade_item::fetch(
            array(
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $assign->id,
                'itemnumber' => 0,
                'courseid' => $course->id
            )
        );
        $outcomegradeitem->set_parent($assignmentgradeitem->categoryid);
        $outcomegradeitem->move_after_sortorder($assignmentgradeitem->sortorder);

        // Test admin user can see the complete hidden activity.
        $result = core_course_external::get_course_module($assign->cmid);
        $result = external_api::clean_returnvalue(core_course_external::get_course_module_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        // Test we retrieve all the fields.
        $this->assertCount(28, $result['cm']);
        $this->assertEquals($record['name'], $result['cm']['name']);
        $this->assertEquals($options['idnumber'], $result['cm']['idnumber']);
        $this->assertEquals(100, $result['cm']['grade']);
        $this->assertEquals(0.0, $result['cm']['gradepass']);
        $this->assertEquals('submissions', $result['cm']['advancedgrading'][0]['area']);
        $this->assertEmpty($result['cm']['advancedgrading'][0]['method']);
        $this->assertEquals($outcomescale, $result['cm']['outcomes'][0]['scale']);

        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        self::getDataGenerator()->enrol_user($student->id,  $course->id, $studentrole->id);
        $this->setUser($student);

        // The user shouldn't be able to see the activity.
        try {
            core_course_external::get_course_module($assign->cmid);
            $this->fail('Exception expected due to invalid permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Make module visible.
        set_coursemodule_visible($assign->cmid, 1);

        // Test student user.
        $result = core_course_external::get_course_module($assign->cmid);
        $result = external_api::clean_returnvalue(core_course_external::get_course_module_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        // Test we retrieve only the few files we can see.
        $this->assertCount(11, $result['cm']);
        $this->assertEquals($assign->cmid, $result['cm']['id']);
        $this->assertEquals($course->id, $result['cm']['course']);
        $this->assertEquals('assign', $result['cm']['modname']);
        $this->assertEquals($assign->id, $result['cm']['instance']);

    }

    /**
     * Test get_course_module_by_instance
     */
    public function test_get_course_module_by_instance() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $record = array(
            'course' => $course->id,
            'name' => 'First quiz',
            'grade' => 90.00
        );
        $options = array(
            'idnumber' => 'ABC',
            'visible' => 0
        );
        // Hidden activity.
        $quiz = self::getDataGenerator()->create_module('quiz', $record, $options);

        // Test admin user can see the complete hidden activity.
        $result = core_course_external::get_course_module_by_instance('quiz', $quiz->id);
        $result = external_api::clean_returnvalue(core_course_external::get_course_module_by_instance_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        // Test we retrieve all the fields.
        $this->assertCount(26, $result['cm']);
        $this->assertEquals($record['name'], $result['cm']['name']);
        $this->assertEquals($record['grade'], $result['cm']['grade']);
        $this->assertEquals($options['idnumber'], $result['cm']['idnumber']);

        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        self::getDataGenerator()->enrol_user($student->id,  $course->id, $studentrole->id);
        $this->setUser($student);

        // The user shouldn't be able to see the activity.
        try {
            core_course_external::get_course_module_by_instance('quiz', $quiz->id);
            $this->fail('Exception expected due to invalid permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Make module visible.
        set_coursemodule_visible($quiz->cmid, 1);

        // Test student user.
        $result = core_course_external::get_course_module_by_instance('quiz', $quiz->id);
        $result = external_api::clean_returnvalue(core_course_external::get_course_module_by_instance_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        // Test we retrieve only the few files we can see.
        $this->assertCount(11, $result['cm']);
        $this->assertEquals($quiz->cmid, $result['cm']['id']);
        $this->assertEquals($course->id, $result['cm']['course']);
        $this->assertEquals('quiz', $result['cm']['modname']);
        $this->assertEquals($quiz->id, $result['cm']['instance']);

        // Try with an invalid module name.
        try {
            core_course_external::get_course_module_by_instance('abc', $quiz->id);
            $this->fail('Exception expected due to invalid module name.');
        } catch (dml_read_exception $e) {
            $this->assertEquals('dmlreadexception', $e->errorcode);
        }

    }

    /**
     * Test get_activities_overview
     */
    public function test_get_activities_overview() {
        global $USER;

        $this->resetAfterTest();
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // Create a viewer user.
        $viewer = self::getDataGenerator()->create_user((object) array('trackforums' => 1));
        $this->getDataGenerator()->enrol_user($viewer->id, $course1->id);
        $this->getDataGenerator()->enrol_user($viewer->id, $course2->id);

        // Create two forums - one in each course.
        $record = new stdClass();
        $record->course = $course1->id;
        $forum1 = self::getDataGenerator()->create_module('forum', (object) array('course' => $course1->id));
        $forum2 = self::getDataGenerator()->create_module('forum', (object) array('course' => $course2->id));

        $this->setAdminUser();
        // A standard post in the forum.
        $record = new stdClass();
        $record->course = $course1->id;
        $record->userid = $USER->id;
        $record->forum = $forum1->id;
        $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $this->setUser($viewer->id);
        $courses = array($course1->id , $course2->id);

        $result = core_course_external::get_activities_overview($courses);
        $this->assertDebuggingCalledCount(8);
        $result = external_api::clean_returnvalue(core_course_external::get_activities_overview_returns(), $result);

        // There should be one entry for course1, and no others.
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);
        // Check expected overview data for the module.
        $this->assertEquals('forum', $result['courses'][0]['overviews'][0]['module']);
        $this->assertContains('1 total unread', $result['courses'][0]['overviews'][0]['overviewtext']);
    }

    /**
     * Test get_user_navigation_options
     */
    public function test_get_user_navigation_options() {
        global $USER;

        $this->resetAfterTest();
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // Create a viewer user.
        $viewer = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($viewer->id, $course1->id);
        $this->getDataGenerator()->enrol_user($viewer->id, $course2->id);

        $this->setUser($viewer->id);
        $courses = array($course1->id , $course2->id, SITEID);

        $result = core_course_external::get_user_navigation_options($courses);
        $result = external_api::clean_returnvalue(core_course_external::get_user_navigation_options_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['courses']);

        foreach ($result['courses'] as $course) {
            $navoptions = new stdClass;
            foreach ($course['options'] as $option) {
                $navoptions->{$option['name']} = $option['available'];
            }
            $this->assertCount(9, $course['options']);
            if ($course['id'] == SITEID) {
                $this->assertTrue($navoptions->blogs);
                $this->assertFalse($navoptions->notes);
                $this->assertFalse($navoptions->participants);
                $this->assertTrue($navoptions->badges);
                $this->assertTrue($navoptions->tags);
                $this->assertFalse($navoptions->grades);
                $this->assertFalse($navoptions->search);
                $this->assertTrue($navoptions->calendar);
                $this->assertTrue($navoptions->competencies);
            } else {
                $this->assertTrue($navoptions->blogs);
                $this->assertFalse($navoptions->notes);
                $this->assertTrue($navoptions->participants);
                $this->assertTrue($navoptions->badges);
                $this->assertFalse($navoptions->tags);
                $this->assertTrue($navoptions->grades);
                $this->assertFalse($navoptions->search);
                $this->assertFalse($navoptions->calendar);
                $this->assertTrue($navoptions->competencies);
            }
        }
    }

    /**
     * Test get_user_administration_options
     */
    public function test_get_user_administration_options() {
        global $USER;

        $this->resetAfterTest();
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // Create a viewer user.
        $viewer = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($viewer->id, $course1->id);
        $this->getDataGenerator()->enrol_user($viewer->id, $course2->id);

        $this->setUser($viewer->id);
        $courses = array($course1->id , $course2->id, SITEID);

        $result = core_course_external::get_user_administration_options($courses);
        $result = external_api::clean_returnvalue(core_course_external::get_user_administration_options_returns(), $result);

        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['courses']);

        foreach ($result['courses'] as $course) {
            $adminoptions = new stdClass;
            foreach ($course['options'] as $option) {
                $adminoptions->{$option['name']} = $option['available'];
            }
            if ($course['id'] == SITEID) {
                $this->assertCount(16, $course['options']);
                $this->assertFalse($adminoptions->update);
                $this->assertFalse($adminoptions->filters);
                $this->assertFalse($adminoptions->reports);
                $this->assertFalse($adminoptions->backup);
                $this->assertFalse($adminoptions->restore);
                $this->assertFalse($adminoptions->files);
                $this->assertFalse(!isset($adminoptions->tags));
                $this->assertFalse($adminoptions->gradebook);
                $this->assertFalse($adminoptions->outcomes);
                $this->assertFalse($adminoptions->badges);
                $this->assertFalse($adminoptions->import);
                $this->assertFalse($adminoptions->publish);
                $this->assertFalse($adminoptions->reset);
                $this->assertFalse($adminoptions->roles);
                $this->assertFalse($adminoptions->editcompletion);
            } else {
                $this->assertCount(15, $course['options']);
                $this->assertFalse($adminoptions->update);
                $this->assertFalse($adminoptions->filters);
                $this->assertFalse($adminoptions->reports);
                $this->assertFalse($adminoptions->backup);
                $this->assertFalse($adminoptions->restore);
                $this->assertFalse($adminoptions->files);
                $this->assertFalse($adminoptions->tags);
                $this->assertFalse($adminoptions->gradebook);
                $this->assertFalse($adminoptions->outcomes);
                $this->assertTrue($adminoptions->badges);
                $this->assertFalse($adminoptions->import);
                $this->assertFalse($adminoptions->publish);
                $this->assertFalse($adminoptions->reset);
                $this->assertFalse($adminoptions->roles);
                $this->assertFalse($adminoptions->editcompletion);
            }
        }
    }

    /**
     * Test get_courses_by_fields
     */
    public function test_get_courses_by_field() {
        global $DB;
        $this->resetAfterTest(true);

        $category1 = self::getDataGenerator()->create_category(array('name' => 'Cat 1'));
        $category2 = self::getDataGenerator()->create_category(array('parent' => $category1->id));
        $course1 = self::getDataGenerator()->create_course(
            array('category' => $category1->id, 'shortname' => 'c1', 'format' => 'topics'));
        $course2 = self::getDataGenerator()->create_course(array('visible' => 0, 'category' => $category2->id, 'idnumber' => 'i2'));

        $student1 = self::getDataGenerator()->create_user();
        $user1 = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        self::getDataGenerator()->enrol_user($student1->id, $course1->id, $studentrole->id);
        self::getDataGenerator()->enrol_user($student1->id, $course2->id, $studentrole->id);

        self::setAdminUser();
        // As admins, we should be able to retrieve everything.
        $result = core_course_external::get_courses_by_field();
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(3, $result['courses']);
        // Expect to receive all the fields.
        $this->assertCount(37, $result['courses'][0]);
        $this->assertCount(38, $result['courses'][1]);  // One more field because is not the site course.
        $this->assertCount(38, $result['courses'][2]);  // One more field because is not the site course.

        $result = core_course_external::get_courses_by_field('id', $course1->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);
        // Expect to receive all the fields.
        $this->assertCount(38, $result['courses'][0]);
        // Check default values for course format topics.
        $this->assertCount(2, $result['courses'][0]['courseformatoptions']);
        foreach ($result['courses'][0]['courseformatoptions'] as $option) {
            if ($option['name'] == 'hiddensections') {
                $this->assertEquals(0, $option['value']);
            } else {
                $this->assertEquals('coursedisplay', $option['name']);
                $this->assertEquals(0, $option['value']);
            }
        }

        $result = core_course_external::get_courses_by_field('id', $course2->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course2->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('ids', "$course1->id,$course2->id");
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(2, $result['courses']);

        // Check default filters.
        $this->assertCount(3, $result['courses'][0]['filters']);
        $this->assertCount(3, $result['courses'][1]['filters']);

        $result = core_course_external::get_courses_by_field('category', $category1->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);
        $this->assertEquals('Cat 1', $result['courses'][0]['categoryname']);

        $result = core_course_external::get_courses_by_field('shortname', 'c1');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('idnumber', 'i2');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course2->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('idnumber', 'x');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);

        // Change filter value.
        filter_set_local_state('mediaplugin', context_course::instance($course1->id)->id, TEXTFILTER_OFF);

        self::setUser($student1);
        // All visible courses  (including front page) for normal student.
        $result = core_course_external::get_courses_by_field();
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(2, $result['courses']);
        $this->assertCount(30, $result['courses'][0]);
        $this->assertCount(31, $result['courses'][1]);  // One field more (course format options), not present in site course.

        $result = core_course_external::get_courses_by_field('id', $course1->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);
        // Expect to receive all the files that a student can see.
        $this->assertCount(31, $result['courses'][0]);

        // Check default filters.
        $filters = $result['courses'][0]['filters'];
        $this->assertCount(3, $filters);
        $found = false;
        foreach ($filters as $filter) {
            if ($filter['filter'] == 'mediaplugin' and $filter['localstate'] == TEXTFILTER_OFF) {
                $found = true;
            }
        }
        $this->assertTrue($found);

        // Course 2 is not visible.
        $result = core_course_external::get_courses_by_field('id', $course2->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);

        $result = core_course_external::get_courses_by_field('ids', "$course1->id,$course2->id");
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);

        $result = core_course_external::get_courses_by_field('category', $category1->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('shortname', 'c1');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('idnumber', 'i2');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);

        $result = core_course_external::get_courses_by_field('idnumber', 'x');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);

        self::setUser($user1);
        // All visible courses (including front page) for authenticated user.
        $result = core_course_external::get_courses_by_field();
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(2, $result['courses']);
        $this->assertCount(30, $result['courses'][0]);  // Site course.
        $this->assertCount(13, $result['courses'][1]);  // Only public information, not enrolled.

        $result = core_course_external::get_courses_by_field('id', $course1->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);
        // Expect to receive all the files that a authenticated can see.
        $this->assertCount(13, $result['courses'][0]);

        // Course 2 is not visible.
        $result = core_course_external::get_courses_by_field('id', $course2->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);

        $result = core_course_external::get_courses_by_field('ids', "$course1->id,$course2->id");
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);

        $result = core_course_external::get_courses_by_field('category', $category1->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('shortname', 'c1');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(1, $result['courses']);
        $this->assertEquals($course1->id, $result['courses'][0]['id']);

        $result = core_course_external::get_courses_by_field('idnumber', 'i2');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);

        $result = core_course_external::get_courses_by_field('idnumber', 'x');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);
    }

    public function test_get_courses_by_field_invalid_field() {
        $this->expectException('invalid_parameter_exception');
        $result = core_course_external::get_courses_by_field('zyx', 'x');
    }

    public function test_get_courses_by_field_invalid_courses() {
        $result = core_course_external::get_courses_by_field('id', '-1');
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertCount(0, $result['courses']);
    }

    /**
     * Test get_courses_by_field_invalid_theme_and_lang
     */
    public function test_get_courses_by_field_invalid_theme_and_lang() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = self::getDataGenerator()->create_course(array('theme' => 'kkt', 'lang' => 'kkl'));
        $result = core_course_external::get_courses_by_field('id', $course->id);
        $result = external_api::clean_returnvalue(core_course_external::get_courses_by_field_returns(), $result);
        $this->assertEmpty($result['courses']['0']['theme']);
        $this->assertEmpty($result['courses']['0']['lang']);
    }


    public function test_check_updates() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create different types of activities.
        $course  = self::getDataGenerator()->create_course();
        $tocreate = array('assign', 'book', 'choice', 'folder', 'forum', 'glossary', 'imscp', 'label', 'lti', 'page', 'quiz',
                            'resource', 'scorm', 'survey', 'url', 'wiki');

        $modules = array();
        foreach ($tocreate as $modname) {
            $modules[$modname]['instance'] = $this->getDataGenerator()->create_module($modname, array('course' => $course->id));
            $modules[$modname]['cm'] = get_coursemodule_from_id(false, $modules[$modname]['instance']->cmid);
            $modules[$modname]['context'] = context_module::instance($modules[$modname]['instance']->cmid);
        }

        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        self::getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        $this->setUser($student);

        $since = time();
        $this->waitForSecond();
        $params = array();
        foreach ($modules as $modname => $data) {
            $params[$data['cm']->id] = array(
                'contextlevel' => 'module',
                'id' => $data['cm']->id,
                'since' => $since
            );
        }

        // Check there is nothing updated because modules are fresh new.
        $result = core_course_external::check_updates($course->id, $params);
        $result = external_api::clean_returnvalue(core_course_external::check_updates_returns(), $result);
        $this->assertCount(0, $result['instances']);
        $this->assertCount(0, $result['warnings']);

        // Test with get_updates_since the same data.
        $result = core_course_external::get_updates_since($course->id, $since);
        $result = external_api::clean_returnvalue(core_course_external::get_updates_since_returns(), $result);
        $this->assertCount(0, $result['instances']);
        $this->assertCount(0, $result['warnings']);

        // Update a module after a second.
        $this->waitForSecond();
        set_coursemodule_name($modules['forum']['cm']->id, 'New forum name');

        $found = false;
        $result = core_course_external::check_updates($course->id, $params);
        $result = external_api::clean_returnvalue(core_course_external::check_updates_returns(), $result);
        $this->assertCount(1, $result['instances']);
        $this->assertCount(0, $result['warnings']);
        foreach ($result['instances'] as $module) {
            foreach ($module['updates'] as $update) {
                if ($module['id'] == $modules['forum']['cm']->id and $update['name'] == 'configuration') {
                    $found = true;
                }
            }
        }
        $this->assertTrue($found);

        // Test with get_updates_since the same data.
        $result = core_course_external::get_updates_since($course->id, $since);
        $result = external_api::clean_returnvalue(core_course_external::get_updates_since_returns(), $result);
        $this->assertCount(1, $result['instances']);
        $this->assertCount(0, $result['warnings']);
        $found = false;
        $this->assertCount(1, $result['instances']);
        $this->assertCount(0, $result['warnings']);
        foreach ($result['instances'] as $module) {
            foreach ($module['updates'] as $update) {
                if ($module['id'] == $modules['forum']['cm']->id and $update['name'] == 'configuration') {
                    $found = true;
                }
            }
        }
        $this->assertTrue($found);

        // Do not retrieve the configuration field.
        $filter = array('files');
        $found = false;
        $result = core_course_external::check_updates($course->id, $params, $filter);
        $result = external_api::clean_returnvalue(core_course_external::check_updates_returns(), $result);
        $this->assertCount(0, $result['instances']);
        $this->assertCount(0, $result['warnings']);
        $this->assertFalse($found);

        // Add invalid cmid.
        $params[] = array(
            'contextlevel' => 'module',
            'id' => -2,
            'since' => $since
        );
        $result = core_course_external::check_updates($course->id, $params);
        $result = external_api::clean_returnvalue(core_course_external::check_updates_returns(), $result);
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals(-2, $result['warnings'][0]['itemid']);
    }

    /**
     * Test cases for the get_enrolled_courses_by_timeline_classification test.
     */
    public function get_get_enrolled_courses_by_timeline_classification_test_cases() {
        $now = time();
        $day = 86400;

        $coursedata = [
            [
                'shortname' => 'apast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'bpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'cpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'dpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'epast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'ainprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'binprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'cinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'dinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'einprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'afuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'bfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'cfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'dfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'efuture',
                'startdate' => $now + $day
            ]
        ];

        // Raw enrolled courses result set should be returned in this order:
        // afuture, ainprogress, apast, bfuture, binprogress, bpast, cfuture, cinprogress, cpast,
        // dfuture, dinprogress, dpast, efuture, einprogress, epast
        //
        // By classification the offset values for each record should be:
        // COURSE_TIMELINE_FUTURE
        // 0 (afuture), 3 (bfuture), 6 (cfuture), 9 (dfuture), 12 (efuture)
        // COURSE_TIMELINE_INPROGRESS
        // 1 (ainprogress), 4 (binprogress), 7 (cinprogress), 10 (dinprogress), 13 (einprogress)
        // COURSE_TIMELINE_PAST
        // 2 (apast), 5 (bpast), 8 (cpast), 11 (dpast), 14 (epast).
        //
        // NOTE: The offset applies to the unfiltered full set of courses before the classification
        // filtering is done.
        // E.g. In our example if an offset of 2 is given then it would mean the first
        // two courses (afuture, ainprogress) are ignored.
        return [
            'empty set' => [
                'coursedata' => [],
                'classification' => 'future',
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => [],
                'expectednextoffset' => 0
            ],
            // COURSE_TIMELINE_FUTURE.
            'future not limit no offset' => [
                'coursedata' => $coursedata,
                'classification' => 'future',
                'limit' => 0,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectednextoffset' => 15
            ],
            'future no offset' => [
                'coursedata' => $coursedata,
                'classification' => 'future',
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture'],
                'expectednextoffset' => 4
            ],
            'future offset' => [
                'coursedata' => $coursedata,
                'classification' => 'future',
                'limit' => 2,
                'offset' => 2,
                'expectedcourses' => ['bfuture', 'cfuture'],
                'expectednextoffset' => 7
            ],
            'future exact limit' => [
                'coursedata' => $coursedata,
                'classification' => 'future',
                'limit' => 5,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectednextoffset' => 13
            ],
            'future limit less results' => [
                'coursedata' => $coursedata,
                'classification' => 'future',
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectednextoffset' => 15
            ],
            'future limit less results with offset' => [
                'coursedata' => $coursedata,
                'classification' => 'future',
                'limit' => 10,
                'offset' => 5,
                'expectedcourses' => ['cfuture', 'dfuture', 'efuture'],
                'expectednextoffset' => 15
            ],
            'all no limit or offset' => [
                'coursedata' => $coursedata,
                'classification' => 'all',
                'limit' => 0,
                'offset' => 0,
                'expectedcourses' => [
                    'afuture',
                    'ainprogress',
                    'apast',
                    'bfuture',
                    'binprogress',
                    'bpast',
                    'cfuture',
                    'cinprogress',
                    'cpast',
                    'dfuture',
                    'dinprogress',
                    'dpast',
                    'efuture',
                    'einprogress',
                    'epast'
                ],
                'expectednextoffset' => 15
            ],
            'all limit no offset' => [
                'coursedata' => $coursedata,
                'classification' => 'all',
                'limit' => 5,
                'offset' => 0,
                'expectedcourses' => [
                    'afuture',
                    'ainprogress',
                    'apast',
                    'bfuture',
                    'binprogress'
                ],
                'expectednextoffset' => 5
            ],
            'all limit and offset' => [
                'coursedata' => $coursedata,
                'classification' => 'all',
                'limit' => 5,
                'offset' => 5,
                'expectedcourses' => [
                    'bpast',
                    'cfuture',
                    'cinprogress',
                    'cpast',
                    'dfuture'
                ],
                'expectednextoffset' => 10
            ],
            'all offset past result set' => [
                'coursedata' => $coursedata,
                'classification' => 'all',
                'limit' => 5,
                'offset' => 50,
                'expectedcourses' => [],
                'expectednextoffset' => 50
            ],
        ];
    }

    /**
     * Test the get_enrolled_courses_by_timeline_classification function.
     *
     * @dataProvider get_get_enrolled_courses_by_timeline_classification_test_cases()
     * @param array $coursedata Courses to create
     * @param string $classification Timeline classification
     * @param int $limit Maximum number of results
     * @param int $offset Offset the unfiltered courses result set by this amount
     * @param array $expectedcourses Expected courses in result
     * @param int $expectednextoffset Expected next offset value in result
     */
    public function test_get_enrolled_courses_by_timeline_classification(
        $coursedata,
        $classification,
        $limit,
        $offset,
        $expectedcourses,
        $expectednextoffset
    ) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursedata);

        $student = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        // NOTE: The offset applies to the unfiltered full set of courses before the classification
        // filtering is done.
        // E.g. In our example if an offset of 2 is given then it would mean the first
        // two courses (afuture, ainprogress) are ignored.
        $result = core_course_external::get_enrolled_courses_by_timeline_classification(
            $classification,
            $limit,
            $offset,
            'shortname ASC'
        );
        $result = external_api::clean_returnvalue(
            core_course_external::get_enrolled_courses_by_timeline_classification_returns(),
            $result
        );

        $actual = array_map(function($course) {
            return $course['shortname'];
        }, $result['courses']);

        $this->assertEquals($expectedcourses, $actual);
        $this->assertEquals($expectednextoffset, $result['nextoffset']);
    }

    /**
     * Test the get_recent_courses function.
     */
    public function test_get_recent_courses() {
        global $USER, $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        set_config('hiddenuserfields', 'lastaccess');

        $courses = array();
        for ($i = 1; $i < 12; $i++) {
            $courses[]  = $generator->create_course();
        };

        $student = $generator->create_user();
        $teacher = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $generator->enrol_user($teacher->id, $courses[0]->id, 'teacher');

        $this->setUser($student);

        $result = core_course_external::get_recent_courses($USER->id);

        // No course accessed.
        $this->assertCount(0, $result);

        foreach ($courses as $course) {
            core_course_external::view_course($course->id);
        }

        // Every course accessed.
        $result = core_course_external::get_recent_courses($USER->id);
        $this->assertCount( 11, $result);

        // Every course accessed, result limited to 10 courses.
        $result = core_course_external::get_recent_courses($USER->id, 10);
        $this->assertCount(10, $result);

        $guestcourse = $generator->create_course(
                (object)array('shortname' => 'guestcourse',
                'enrol_guest_status_0' => ENROL_INSTANCE_ENABLED,
                'enrol_guest_password_0' => ''));
        core_course_external::view_course($guestcourse->id);

        // Every course accessed, even the not enrolled one.
        $result = core_course_external::get_recent_courses($USER->id);
        $this->assertCount(12, $result);

        // Offset 5, return 7 out of 12.
        $result = core_course_external::get_recent_courses($USER->id, 0, 5);
        $this->assertCount(7, $result);

        // Offset 5 and limit 3, return 3 out of 12.
        $result = core_course_external::get_recent_courses($USER->id, 3, 5);
        $this->assertCount(3, $result);

        // Sorted by course id ASC.
        $result = core_course_external::get_recent_courses($USER->id, 0, 0, 'id ASC');
        $this->assertEquals($courses[0]->id, array_shift($result)->id);

        // Sorted by course id DESC.
        $result = core_course_external::get_recent_courses($USER->id, 0, 0, 'id DESC');
        $this->assertEquals($guestcourse->id, array_shift($result)->id);

        // If last access is hidden, only get the courses where has viewhiddenuserfields capability.
        $this->setUser($teacher);
        $teacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $usercontext = context_user::instance($student->id);
        $this->assignUserCapability('moodle/user:viewdetails', $usercontext, $teacherroleid);

        // Sorted by course id DESC.
        $result = core_course_external::get_recent_courses($student->id);
        $this->assertCount(1, $result);
        $this->assertEquals($courses[0]->id, array_shift($result)->id);
    }
}