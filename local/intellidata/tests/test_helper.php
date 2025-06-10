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
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata;

/**
 * Test case helper.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class test_helper {

    /**
     * Validate phpunit version.
     *
     * @return bool
     */
    public static function is_new_phpunit() {
        global $CFG;
        return $CFG->version > 2017051509;
    }

    /**
     * Validate user/lib.php version.
     *
     * @return bool
     */
    public static function is_user_generator_with_events() {
        global $CFG;
        return $CFG->version >= 2020061522;
    }

    /**
     * Validate data.
     *
     * @param $data
     * @param $fields
     * @return object
     */
    public static function filter_fields($data, $fields) {
        $keys = array_keys($fields);

        return (object)array_filter((array)$data, function ($key) use ($keys) {
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return correct assertion method.
     *
     * @param $object
     * @return string
     */
    public static function assert_file_does_not_exist_method($object) {
        return method_exists($object, 'assertFileDoesNotExist')
            ? 'assertFileDoesNotExist' : 'assertFileNotExists';
    }

    /**
     * Assert isArray.
     *
     * @param $object
     * @param $array
     * @return string
     */
    public static function assert_is_array($object, $array) {
        return method_exists($object, 'assertIsArray')
            ? $object->assertIsArray($array)
            : $object->assertTrue((bool)count($array));
    }

    /**
     * Validate if get_local_path_from_storedfile is callable.
     *
     * @return bool
     */
    public static function is_get_local_path_from_storedfile_callable() {
        $fs = get_file_storage();
        $filesystem = $fs->get_file_system();

        return is_callable([$filesystem, 'get_local_path_from_storedfile']);
    }
}
