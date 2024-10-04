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
 * Fixture for mocking callbacks used in \core_course_category
 *
 * @package    core_course
 * @category   test
 * @copyright  2020 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\test {

    /**
     * Class mock_hooks
     *
     * @package    core_course
     * @category   test
     * @copyright  2020 Ruslan Kabalin
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class mock_hooks {
        /** @var bool $cancoursecategorydelete */
        private static $cancoursecategorydelete = true;

        /** @var bool $cancoursecategorydeletemove */
        private static $cancoursecategorydeletemove = true;

        /** @var string $getcoursecategorycontents */
        private static $getcoursecategorycontents = '';

        /** @var array $callingarguments */
        private static $callingarguments = [];

        /**
         * Set calling arguments.
         *
         * This is supposed to be used in the callbacks to store arguments passed to callback.
         *
         * @param array $callingarguments
         */
        public static function set_calling_arguments($callingarguments) {
            self::$callingarguments = $callingarguments;
        }

        /**
         * Get calling arguments.
         *
         * This is supposed to be used in the test to verify arguments passed to callback.
         * This method also reset stored calling arguments.
         *
         * @return array $callingarguments
         */
        public static function get_calling_arguments() {
            $callingarguments = self::$callingarguments;
            self::$callingarguments = [];
            return $callingarguments;
        }

        /**
         * Get can_course_category_delete callback return.
         *
         * @return bool
         */
        public static function get_can_course_category_delete_return(): bool {
            return self::$cancoursecategorydelete;
        }

        /**
         * Sets can_course_category_delete callback return.
         *
         * @param bool $return
         */
        public static function set_can_course_category_delete_return(bool $return) {
            self::$cancoursecategorydelete = $return;
        }

        /**
         * Get can_course_category_delete_move callback return.
         *
         * @return bool
         */
        public static function get_can_course_category_delete_move_return(): bool {
            return self::$cancoursecategorydeletemove;
        }

        /**
         * Sets can_course_category_delete_move callback return.
         *
         * @param bool $return
         */
        public static function set_can_course_category_delete_move_return(bool $return) {
            self::$cancoursecategorydeletemove = $return;
        }

        /**
         * Get get_course_category_contents callback return.
         *
         * @return string
         */
        public static function get_get_course_category_contents_return(): string {
            return self::$getcoursecategorycontents;
        }

        /**
         * Sets get_course_category_contents callback return.
         *
         * @param string $return
         */
        public static function set_get_course_category_contents_return(string $return) {
            self::$getcoursecategorycontents = $return;
        }
    }
}

namespace {

    use core_course\test\mock_hooks;

    /**
     * Test pre_course_category_delete callback.
     *
     * @param object $category
     */
    function tool_unittest_pre_course_category_delete(object $category) {
        mock_hooks::set_calling_arguments(func_get_args());
    }

    /**
     * Test pre_course_category_delete_move callback.
     *
     * @param core_course_category $category
     * @param core_course_category $newcategory
     */
    function tool_unittest_pre_course_category_delete_move(core_course_category $category, core_course_category $newcategory) {
        mock_hooks::set_calling_arguments(func_get_args());
    }

    /**
     * Test can_course_category_delete callback.
     *
     * @param core_course_category $category
     * @return bool
     */
    function tool_unittest_can_course_category_delete(core_course_category $category) {
        mock_hooks::set_calling_arguments(func_get_args());
        return mock_hooks::get_can_course_category_delete_return();
    }

    /**
     * Test can_course_category_delete_move callback.
     *
     * @param core_course_category $category
     * @param core_course_category $newcategory
     * @return bool
     */
    function tool_unittest_can_course_category_delete_move(core_course_category $category, core_course_category $newcategory) {
        mock_hooks::set_calling_arguments(func_get_args());
        return mock_hooks::get_can_course_category_delete_move_return();
    }

    /**
     * Test get_course_category_contents callback.
     *
     * @param core_course_category $category
     * @return string
     */
    function tool_unittest_get_course_category_contents(core_course_category $category) {
        mock_hooks::set_calling_arguments(func_get_args());
        return mock_hooks::get_get_course_category_contents_return();
    }
}
