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
class core_course_external_testcase extends externallib_advanced_testcase {

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
        $category2->theme = 'base';
        $categories = array(
            array('name' => $category1->name, 'parent' => 0),
            array('name' => $category2->name, 'parent' => 0, 'idnumber' => $category2->idnumber,
                'description' => $category2->desc, 'theme' => $category2->theme)
        );

        $createdcats = core_course_external::create_categories($categories);

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

        // Confirm that sub categories were inserted correctly.
        $this->assertEquals($category3->name, $createdsubcats[0]['name']);

        // Save the ids.
        $category3->id = $createdsubcats[0]['id'];

        // Calling the ws function should provide a new sortorder to give category1,
        // category2, category3. New course categories are ordered by id not name.
        $category1 = $DB->get_record('course_categories', array('id' => $category1->id));
        $category2 = $DB->get_record('course_categories', array('id' => $category2->id));
        $category3 = $DB->get_record('course_categories', array('id' => $category3->id));

        $this->assertGreaterThanOrEqual($category1->sortorder, $category3->sortorder);
        $this->assertGreaterThanOrEqual($category2->sortorder, $category3->sortorder);

        // Call without required capability
        $this->unassignUserCapability('moodle/category:manage', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
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
        $this->setExpectedException('required_capability_exception');
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

        // Retrieve category1 + sub-categories except not visible ones
        $categories = core_course_external::get_categories(array(
            array('key' => 'id', 'value' => $category1->id),
            array('key' => 'visible', 'value' => 1)), 1);

        // Check we retrieve the good total number of categories.
        $this->assertEquals(2, count($categories));

        // Check the return values
        foreach ($categories as $category) {
            $generatedcat = $generatedcats[$category['id']];
            $this->assertEquals($category['idnumber'], $generatedcat->idnumber);
            $this->assertEquals($category['name'], $generatedcat->name);
            $this->assertEquals($category['description'], $generatedcat->description);
            $this->assertEquals($category['descriptionformat'], FORMAT_HTML);
        }

        // Check different params.
        $categories = core_course_external::get_categories(array(
            array('key' => 'id', 'value' => $category1->id),
            array('key' => 'idnumber', 'value' => $category1->idnumber),
            array('key' => 'visible', 'value' => 1)), 0);
        $this->assertEquals(1, count($categories));

        // Retrieve categories from parent.
        $categories = core_course_external::get_categories(array(
            array('key' => 'parent', 'value' => $category3->id)), 1);
        $this->assertEquals(2, count($categories));

        // Retrieve all categories.
        $categories = core_course_external::get_categories();
        $this->assertEquals($DB->count_records('course_categories'), count($categories));

        // Call without required capability (it will fail cause of the search on idnumber).
        $this->unassignUserCapability('moodle/category:manage', $context->id, $roleid);
        $this->setExpectedException('moodle_exception');
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
        $this->setExpectedException('required_capability_exception');
        core_course_external::update_categories($categories);
    }

    /**
     * Test create_courses
     */
    public function test_create_courses() {
        global $DB;

        $this->resetAfterTest(true);

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/course:create', $contextid);
        $this->assignUserCapability('moodle/course:visibility', $contextid, $roleid);

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
        $course2['startdate'] = 1420092000; // 01/01/2015
        $course2['numsections'] = 4;
        $course2['maxbytes'] = 100000;
        $course2['showreports'] = 1;
        $course2['visible'] = 0;
        $course2['hiddensections'] = 0;
        $course2['groupmode'] = 0;
        $course2['groupmodeforce'] = 0;
        $course2['defaultgroupingid'] = 0;
        $course2['enablecompletion'] = 1;
        $course2['completionstartonenrol'] = 1;
        $course2['completionnotify'] = 1;
        $course2['lang'] = 'en';
        $course2['forcetheme'] = 'base';
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
        $courses = array($course1, $course2);

        $createdcourses = core_course_external::create_courses($courses);

        // Check that right number of courses were created.
        $this->assertEquals(2, count($createdcourses));

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
                $this->assertEquals($courseinfo->numsections, $course2['numsections']);
                $this->assertEquals($courseinfo->maxbytes, $course2['maxbytes']);
                $this->assertEquals($courseinfo->showreports, $course2['showreports']);
                $this->assertEquals($courseinfo->visible, $course2['visible']);
                $this->assertEquals($courseinfo->hiddensections, $course2['hiddensections']);
                $this->assertEquals($courseinfo->groupmode, $course2['groupmode']);
                $this->assertEquals($courseinfo->groupmodeforce, $course2['groupmodeforce']);
                $this->assertEquals($courseinfo->defaultgroupingid, $course2['defaultgroupingid']);
                $this->assertEquals($courseinfo->completionnotify, $course2['completionnotify']);
                $this->assertEquals($courseinfo->lang, $course2['lang']);

                if (!empty($CFG->allowcoursethemes)) {
                    $this->assertEquals($courseinfo->theme, $course2['forcetheme']);
                }

                if (completion_info::is_enabled_for_site()) {
                    $this->assertEquals($courseinfo->enablecompletion, $course2['enabledcompletion']);
                    $this->assertEquals($courseinfo->completionstartonenrol, $course2['completionstartonenrol']);
                } else {
                    $this->assertEquals($courseinfo->enablecompletion, 0);
                    $this->assertEquals($courseinfo->completionstartonenrol, 0);
                }

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
                $this->assertEquals($courseinfo->numsections, $course3options['numsections']);
                $this->assertEquals($courseinfo->coursedisplay, $course3options['coursedisplay']);
            } else {
                throw moodle_exception('Unexpected shortname');
            }
        }

        // Call without required capability
        $this->unassignUserCapability('moodle/course:create', $contextid, $roleid);
        $this->setExpectedException('required_capability_exception');
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
        $USER->email = 'emailtopass@contextvalidation.me';

        $course1  = self::getDataGenerator()->create_course();
        $course2  = self::getDataGenerator()->create_course();
        $course3  = self::getDataGenerator()->create_course();

        // Delete courses.
        core_course_external::delete_courses(array($course1->id, $course2->id));

        // Check $course 1 and 2 are deleted.
        $notdeletedcount = $DB->count_records_select('course',
            'id IN ( ' . $course1->id . ',' . $course2->id . ')');
        $this->assertEquals(0, $notdeletedcount);

         // Fail when the user is not allow to access the course (enrolled) or is not admin.
        $this->setGuestUser();
        $this->setExpectedException('require_login_exception');
        $createdsubcats = core_course_external::delete_courses(array($course3->id));
    }

    /**
     * Test get_courses
     */
    public function test_get_courses () {
        global $DB;

        $this->resetAfterTest(true);

        $generatedcourses = array();
        $coursedata['idnumber'] = 'idnumbercourse1';
        $coursedata['fullname'] = 'Course 1 for PHPunit test';
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

        // Check we retrieve the good total number of categories.
        $this->assertEquals(2, count($courses));

        foreach ($courses as $course) {
            $dbcourse = $generatedcourses[$course['id']];
            $this->assertEquals($course['idnumber'], $dbcourse->idnumber);
            $this->assertEquals($course['fullname'], $dbcourse->fullname);
            $this->assertEquals($course['summary'], $dbcourse->summary);
            $this->assertEquals($course['summaryformat'], FORMAT_HTML);
            $this->assertEquals($course['shortname'], $dbcourse->shortname);
            $this->assertEquals($course['categoryid'], $dbcourse->category);
            $this->assertEquals($course['format'], $dbcourse->format);
            $this->assertEquals($course['showgrades'], $dbcourse->showgrades);
            $this->assertEquals($course['newsitems'], $dbcourse->newsitems);
            $this->assertEquals($course['startdate'], $dbcourse->startdate);
            $this->assertEquals($course['numsections'], $dbcourse->numsections);
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
            $this->assertEquals($course['completionstartonenrol'], $dbcourse->completionstartonenrol);
            $this->assertEquals($course['enablecompletion'], $dbcourse->enablecompletion);
            $this->assertEquals($course['completionstartonenrol'], $dbcourse->completionstartonenrol);
            if ($dbcourse->format === 'topics') {
                $this->assertEquals($course['courseformatoptions'], array(
                    array('name' => 'numsections', 'value' => $dbcourse->numsections),
                    array('name' => 'hiddensections', 'value' => $dbcourse->hiddensections),
                    array('name' => 'coursedisplay', 'value' => $dbcourse->coursedisplay),
                ));
            }
        }

        // Get all courses in the DB
        $courses = core_course_external::get_courses(array());
        $this->assertEquals($DB->count_records('course'), count($courses));
    }

    /**
     * Test get_course_contents
     */
    public function test_get_course_contents() {
        $this->resetAfterTest(true);

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
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:view', $context->id);
        $this->assignUserCapability('moodle/course:update', $context->id, $roleid);

        $courses = core_course_external::get_course_contents($course->id, array());

        // Check that the course has the 3 created modules
        $this->assertEquals(3, count($courses[0]['modules']));
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

        // Check that the course has been duplicated.
        $this->assertEquals($newcourse['shortname'], $duplicate['shortname']);
    }
}
