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

namespace core_course\cache;

use cache_data_source;
use cache_definition;
use moodle_url;
use core_course_list_element;

/**
 * Class to describe cache data source for course image.
 *
 * @package    core
 * @subpackage course
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_image implements cache_data_source {

    /** @var course_image */
    protected static $instance = null;

    /**
     * Returns an instance of the data source class that the cache can use for loading data using the other methods
     * specified by this interface.
     *
     * @param cache_definition $definition
     * @return \core_course\cache\course_image
     */
    public static function get_instance_for_cache(cache_definition $definition): course_image {
        if (is_null(self::$instance)) {
            self::$instance = new course_image();
        }
        return self::$instance;
    }

    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * @param string|int $key The key to load.
     * @return string|bool Returns course image url as a string or false if the image is not exist
     */
    public function load_for_cache($key) {
        // We should use get_course() instead of get_fast_modinfo() for better performance.
        $course = get_course($key);
        return $this->get_image_url_from_overview_files($course);
    }

    /**
     * Returns image URL from course overview files.
     *
     * @param \stdClass $course Course object.
     * @return null|string Image URL or null if it's not exists.
     */
    protected function get_image_url_from_overview_files(\stdClass $course): ?string {
        $courseinlist = new core_course_list_element($course);
        foreach ($courseinlist->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                return moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                )->out();
            }
        }

        // Returning null if no image found to let it be cached
        // as false is what cache API returns then a data is not found in cache.
        return null;
    }

    /**
     * Loads several keys for the cache.
     *
     * @param array $keys An array of keys each of which will be string|int.
     * @return array An array of matching data items.
     */
    public function load_many_for_cache(array $keys): array {
        $records = [];
        foreach ($keys as $key) {
            $records[$key] = $this->load_for_cache($key);
        }
        return $records;
    }
}
