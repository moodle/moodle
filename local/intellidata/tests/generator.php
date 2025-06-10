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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata;

use phpunit_util;

/**
 * PHPUnit data generator testcase.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class generator {

    /**
     * Data generator.
     *
     * @return \testing_data_generator|null
     */
    private static function data_generator() {
        if (test_helper::is_new_phpunit()) {
            return \advanced_testcase::getDataGenerator();
        }

        return phpunit_util::get_data_generator();
    }

    /**
     * Generate a user.
     *
     * @param array $data
     * @return \stdClass
     * @throws \moodle_exception
     */
    public static function create_user(array $data = []) {
        global $CFG;

        $user = self::data_generator()->create_user($data);

        if (!test_helper::is_user_generator_with_events()) {
            \core\event\user_created::create_from_userid($user->id)->trigger();
        }

        return $user;
    }

    /**
     * Generate users.
     *
     * @param int $num
     * @return \stdClass
     * @throws \moodle_exception
     */
    public static function create_users(int $num = 0) {

        $users = [];

        for ($i = 1; $i <= $num; $i++) {
            $newuser = self::create_user();
            $users[$newuser->id] = $newuser;
        }

        return $users;
    }

    /**
     * Generate a cohort.
     *
     * @param array $data
     * @return \stdClass
     */
    public static function create_cohort(array $data = []) {
        return self::data_generator()->create_cohort($data);
    }

    /**
     * Generate cohorts.
     *
     * @param int $num
     * @return \stdClass
     * @throws \moodle_exception
     */
    public static function create_cohorts(int $num = 0) {

        $cohorts = [];

        for ($i = 1; $i <= $num; $i++) {
            $newcohort = self::create_cohort();
            $cohorts[$newcohort->id] = $newcohort;
        }

        return $cohorts;
    }

    /**
     * Generate a category.
     *
     * @param array $data
     * @return \core_course_category
     */
    public static function create_category(array $data = []) {
        return self::data_generator()->create_category($data);
    }

    /**
     * Generate categories.
     *
     * @param int $num
     * @return \stdClass
     * @throws \moodle_exception
     */
    public static function create_categories(int $num = 0) {

        $categories = [];

        for ($i = 1; $i <= $num; $i++) {
            $newcategory = self::create_category();
            $categories[$newcategory->id] = $newcategory;
        }

        return $categories;
    }

    /**
     * Get category.
     *
     * @param int $id
     * @return \core_course_category|false|null
     * @throws \moodle_exception
     */
    public static function get_category(int $id) {
        if (test_helper::is_new_phpunit() && class_exists('core_course_category')) {
            return \core_course_category::get($id);
        }

        return \coursecat::get($id);
    }

    /**
     * Generate a group.
     *
     * @param array $data
     * @return \stdClass
     * @throws \coding_exception
     */
    public static function create_group(array $data) {
        return self::data_generator()->create_group($data);
    }

    /**
     * Generate group member.
     *
     * @param array $data
     */
    public static function create_group_member(array $data) {
        return self::data_generator()->create_group_member($data);
    }

    /**
     * Generate a course.
     *
     * @param array $data
     * @return \stdClass
     */
    public static function create_course(array $data = []) {
        return self::data_generator()->create_course($data);
    }

    /**
     * Generate courses.
     *
     * @param int $num
     * @return \stdClass
     * @throws \moodle_exception
     */
    public static function create_courses(int $num = 0) {

        $courses = [];

        for ($i = 1; $i <= $num; $i++) {
            $newcourse = self::create_course();
            $courses[$newcourse->id] = $newcourse;
        }

        return $courses;
    }

    /**
     * Enrol user.
     *
     * @param array $data
     * @return bool
     */
    public static function enrol_user(array $data) {
        return self::data_generator()->enrol_user($data['userid'], $data['courseid']);
    }

    /**
     * Enrol user.
     *
     * @param array $data
     * @param bool $withevent
     * @return bool
     */
    public static function create_profile_field_category(array $data, bool $withevent = false) {
        global $DB;

        $categoryid = $DB->insert_record('user_info_category', $data);
        $category = $DB->get_record('user_info_category', ['id' => $categoryid]);
        if ($withevent) {
            \core\event\user_info_category_created::create_from_category($category)->trigger();
        }

        return $category;
    }

    /**
     * Enrol users.
     *
     * @param array $data
     * @return bool
     */
    public static function enrol_users(int $courseid, array $users) {

        $enrols = [];

        foreach ($users as $user) {
            $enrols = self::data_generator()->enrol_user($user->id, $courseid);
        }

        return $enrols;
    }

    /**
     * Get generator.
     *
     * @param $component
     * @return \component_generator_base|\default_block_generator
     */
    public static function get_plugin_generator($component) {
        return self::data_generator()->get_plugin_generator($component);
    }

    /**
     * Generate a role.
     *
     * @param array $data
     * @return int
     * @throws \coding_exception
     */
    public static function create_role(array $data) {
        return self::data_generator()->create_role($data);
    }

    /**
     * Generate a module.
     *
     * @param string $modulename
     * @param array $data
     * @return \stdClass
     */
    public static function create_module(string $modulename, array $data) {
        return self::data_generator()->create_module($modulename, $data);
    }

    /**
     * Data plugin generator.
     *
     * @return \testing_data_generator|null
     */
    public static function data_plugin_generator() {
        if (test_helper::is_new_phpunit()) {
            return \advanced_testcase::getDataGenerator()->get_plugin_generator('local_intellidata');
        }

        return phpunit_util::get_data_generator()->get_plugin_generator('local_intellidata');
    }

    /**
     * Generate tracking record.
     *
     * @param string $modulename
     * @param array $data
     * @return \stdClass
     */
    public static function create_tracking(array $data) {
        return self::data_plugin_generator()->create_tracking($data);
    }
}
