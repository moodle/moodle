<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   group
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

/**
 * Unit tests for (some of) user/external.php.
 * WARNING: DO NOT RUN THIS TEST ON A PRODUCTION SITE
 * => DO NOT UNCOMMENT THESE TEST FUNCTIONS EXCEPT IF YOU ARE DEVELOPER
 * => NONE OF THESE TEST FUNCTIONS SHOULD BE UNCOMMENT BY DEFAULT
 * => THESE TEST FUNCTIONS ARE DEPENDENT BETWEEEN EACH OTHER
 * => THE FUNCTION ORDER MUST NOT BE CHANGED
 *
 *
 * THIS TEST NEEDS TO BE RUN AS ADMIN!!!
 * @author Jerome Mouneyrac
 */

require_once($CFG->dirroot . '/course/external.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/user/lib.php');

class course_external_test extends UnitTestCase {
/*
    var $realDB;
    var $group;
    var $group2;
    var $userid1;
    var $userid2;
    var $userid3;
    var $userid4;
    var $userid5;
    var $course;
    var $categoryid;
    var $roleid;
    var $context;
    public  static $includecoverage = array('user/lib.php');

    function setUp() {
        global $DB;

        /// create a category
        $tempcat = new object();
        $tempcat->name = 'categoryForTestCourse';
        $this->categoryid = $DB->insert_record('course_categories', $tempcat);

        /// create a course
        $course->category =  $this->categoryid;
        $course->summary = 'Test course for Course';
        $course->format = 'weeks';
        $course->numsections = '10';
        $course->startdate = mktime();
        $course->name = "Test course for Course";
        $course->fullname = "Test course for Course";
        $course->shortname = "TestCourseForCourse";
        $course->idnumber = 123456789;
        $course = create_course($course);
        $this->course = $course;

    }

    function tearDown() {
        global $DB;

        /// delete the course
        delete_course($this->course, false);

        /// delete the category
        $DB->delete_records('course_categories',array('id' =>  $this->categoryid));
    }

    function test_create_courses() {
        $params = array();
        /// create a course
        $course = array();
        $course['category'] =  $this->categoryid;
        $course['summary'] = 'Test course for Course12';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['name'] = "Test course for Group12";
        $course['fullname'] = "Test course for Group12";
        $course['shortname'] = "TestCourseForGroup12";
        $params[] = $course;
        $course = array();
        $course['category'] =  $this->categoryid;
        $course['summary'] = 'Test course for Course02';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['name'] = "Test course for Group02";
        $course['fullname'] = "Test course for Group02";
        $course['shortname'] = "TestCourseForGroup02";
        $params[] = $course;
        $courses = course_external::create_courses($params);
        $this->assertEqual(sizeof($courses), 2);
        $this->assertIsA($courses[key($courses)], "stdClass");
        $this->assertNotNull($courses[key($courses)]);

        /// delete the course
        delete_course($courses[key($courses)], false);
        next($courses);
        delete_course($courses[key($courses)], false);

        /// create a course with a not existing category id
        $params = array();
        $course['category'] = 4568468468;
        $course['summary'] = 'Test course for Course2';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['name'] = "Test course for Group2";
        $course['fullname'] = "Test course for Group2";
        $course['shortname'] = "TestCourseForGroup2";
        $params[] = $course;
        $this->expectException(new moodle_exception('noexistingcategory'));
        $groupids = course_external::create_courses($params);

    }

    function test_create_courses2() {
        //shortname already exit
        $params = array();
        $course['category'] = $this->categoryid;
        $course['summary'] = 'Test course for Course2';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['name'] = "Test course for Group2";
        $course['fullname'] = "Test course for Group2";
        $course['shortname'] = "TestCourseForCourse";
        $params[] = $course;
        $this->expectException(new moodle_exception('shortnametaken'));
        $groupids = course_external::create_courses($params);
    }

    function test_create_courses3() {
        //3. idnumber already exist
        $params = array();
        $course['category'] = $this->categoryid;
        $course['summary'] = 'Test course for Course3';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['name'] = "Test course for Group3";
        $course['fullname'] = "Test course for Group3";
        $course['shortname'] = "TestCourseForGroup3";
        $course['idnumber'] = 123456789;
        $params[] = $course;
        $this->expectException(new moodle_exception('idnumbertaken'));
        $groupids = course_external::create_courses($params);
    }

    function test_get_courses() {
        //get by id, shortname and idnumber
        $params = array();
        $course = array();
        $course['id'] = $this->course->id;
        $params[] = $course;
        $course = array();
        $course['shortname'] = $this->course->shortname;
        $params[] = $course;
        $course = array();
        $course['idnumber'] = $this->course->idnumber;
        $params[] = $course;
        $courses = course_external::get_courses($params);
        $this->assertEqual(sizeof($courses), 3);
        $coursetotest = $courses[key($courses)];
        $this->assertEqual($coursetotest->id, $this->course->id);
        $this->assertEqual($coursetotest->idnumber, $this->course->idnumber);
        $this->assertEqual($coursetotest->shortname, $this->course->shortname);
        $this->assertEqual($coursetotest->summary, $this->course->summary);
        $this->assertEqual($coursetotest->format, $this->course->format);
        $this->assertEqual($coursetotest->fullname, $this->course->fullname);
        $this->assertEqual($coursetotest->numsections, $this->course->numsections);
        $this->assertEqual($coursetotest->startdate, $this->course->startdate);
        $this->assertEqual($coursetotest->category, $this->course->category);
        next($courses);
        $coursetotest = $courses[key($courses)];
        $this->assertEqual($coursetotest->id, $this->course->id);
        $this->assertEqual($coursetotest->idnumber, $this->course->idnumber);
        $this->assertEqual($coursetotest->shortname, $this->course->shortname);
        $this->assertEqual($coursetotest->summary, $this->course->summary);
        $this->assertEqual($coursetotest->format, $this->course->format);
        $this->assertEqual($coursetotest->fullname, $this->course->fullname);
        $this->assertEqual($coursetotest->numsections, $this->course->numsections);
        $this->assertEqual($coursetotest->startdate, $this->course->startdate);
        $this->assertEqual($coursetotest->category, $this->course->category);
        next($courses);
        $coursetotest = $courses[key($courses)];
        $this->assertEqual($coursetotest->id, $this->course->id);
        $this->assertEqual($coursetotest->idnumber, $this->course->idnumber);
        $this->assertEqual($coursetotest->shortname, $this->course->shortname);
        $this->assertEqual($coursetotest->summary, $this->course->summary);
        $this->assertEqual($coursetotest->format, $this->course->format);
        $this->assertEqual($coursetotest->fullname, $this->course->fullname);
        $this->assertEqual($coursetotest->numsections, $this->course->numsections);
        $this->assertEqual($coursetotest->startdate, $this->course->startdate);
        $this->assertEqual($coursetotest->category, $this->course->category);
    }

    function test_delete_courses() {
        $params = array();
        /// create a course
        $course->category =  $this->categoryid;
        $course->summary = 'Test course for Course 2 delete';
        $course->format = 'weeks';
        $course->numsections = '10';
        $course->startdate = mktime();
        $course->name = "Test course for Course 2 delete";
        $course->fullname = "Test course for Course 2 delete";
        $course->shortname = "TestCourseForCourse 2 delete";
        $course->idnumber = 0123456789;
        $course = create_course($course);
        $params[] = (array) $course;
        $result = course_external::delete_courses($params);
        $this->assertEqual($result, true);
    }

    function test_update_courses() {
        $params = array();
        $course['id'] = $this->course->id;
        $course['category'] =  $this->categoryid;
        $course['summary'] = 'Test course for Course 2 update';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['fullname'] = "Test course for Course 2 update";
        $course['shortname'] = "TestCourseForCourse2update";
        $course['idnumber'] = 8005008;
        $params[] = $course;
        $result = course_external::update_courses($params);
        $this->assertEqual($result, true);
        $dbcourse = get_course_by_id($course['id']);
        $this->assertEqual($dbcourse->idnumber, $course['idnumber']);
        $this->assertEqual($dbcourse->shortname, $course['shortname']);
        $this->assertEqual($dbcourse->summary, $course['summary']);
        $this->assertEqual($dbcourse->format, $course['format']);
        $this->assertEqual($dbcourse->fullname, $course['fullname']);
        $this->assertEqual($dbcourse->numsections, $course['numsections']);
        $this->assertEqual($dbcourse->startdate, $course['startdate']);
        $this->assertEqual($dbcourse->category, $course['category']);

        //if id doesn't exist catch exception
        $params = array();
        $course['id'] = 6546544;
        $course['category'] =  $this->categoryid;
        $course['summary'] = 'Test course for Course 2 update';
        $course['format'] = 'weeks';
        $course['numsections'] = '10';
        $course['startdate'] = mktime();
        $course['fullname'] = "Test course for Course 2 update";
        $course['shortname'] = "TestCourseForCourse2update";
        $course['idnumber'] = 8005007;
        $params[] = $course;
        $this->expectException(new moodle_exception('courseidnotfound'));
        $result = course_external::update_courses($params);

    }

    function test_get_course_modules() {
        global $DB;


        //add two chat activities to the course
        $chat = new stdClass();
        $chat->course = $this->course->id;
        $chat->name = "chat to delete";
        $chat->intro = "introduction";
        $chat->keepdays = 0;
        $chat->studentlogs = 0;
        $chat->chattime = 0;
        $chat->schedule = 0;
        $chat->timemodified = 0 ;
        $chatid = chat_add_instance($chat);
        $chat->name = "chat to delete 2";
        $chat->intro = "introduction 2";
        $chatid2 = chat_add_instance($chat);

        $coursemoduleid = $DB->insert_record('course_modules',array('course' => $this->course->id,
                                                  'module' => 2,
                                                  'instance' => $chatid,
                                                  'section' =>0));
        $coursemoduleid2 = $DB->insert_record('course_modules',array('course' => $this->course->id,
                                                  'module' => 2,
                                                  'instance' => $chatid2,
                                                  'section' =>0));
        $section = $DB->get_record('course_sections',array('course' => $this->course->id));
        $section->sequence = $coursemoduleid.",".$coursemoduleid2;
        $DB->update_record('course_sections', $section);


        $params = array();
        $course = array();
        $course["id"] = $this->course->id;
        $params[] = $course;
        $activities = course_external::get_course_modules($params);

        $activities = course_external::get_course_activities($params);
        varlog($activities);
        chat_delete_instance($chatid);
        chat_delete_instance($chatid2);
        $DB->delete_records('course_modules',array('course' => $this->course->id,
                                                  'module' => 2,
                                                  'instance' => $chatid,
                                                  'section' =>0));
        $DB->delete_records('course_modules',array('course' => $this->course->id,
                                                  'module' => 2,
                                                  'instance' => $chatid2,
                                                  'section' =>0));

    }
*/
}

