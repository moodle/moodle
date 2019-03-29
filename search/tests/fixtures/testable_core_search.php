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
 * Core search class adapted to unit test.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/mock_search_engine.php');

/**
 * Core search class adapted to unit test.
 *
 * Note that by default all core search areas are returned when calling get_search_areas_list,
 * if you want to use the mock search area you can use testable_core_search::add_search_area
 * although if you want to add mock search areas on top of the core ones you should call
 * testable_core_search::add_core_search_areas before calling testable_core_search::add_search_area.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_core_search extends \core_search\manager {

    /**
     * Attaches the mock engine to search.
     *
     * Auto enables global search.
     *
     * @param  \core_search\engine|bool $searchengine
     * @return testable_core_search
     */
    public static function instance($searchengine = false) {

        // One per request, this should be purged during testing.
        if (self::$instance !== null) {
            return self::$instance;
        }

        set_config('enableglobalsearch', true);

        // Default to the mock one.
        if ($searchengine === false) {
            $searchengine = new \mock_search\engine();
        }

        self::$instance = new testable_core_search($searchengine);

        return self::$instance;
    }

    /**
     * Changes visibility.
     *
     * @return array
     */
    public function get_areas_user_accesses($limitcourseids = false, $limitcontextids = false) {
        return parent::get_areas_user_accesses($limitcourseids, $limitcontextids);
    }

    /**
     * Adds an enabled search component to the search areas list.
     *
     * @param string $areaid
     * @param \core_search\base $searcharea
     * @return void
     */
    public function add_search_area($areaid, \core_search\base $searcharea) {
       self::$enabledsearchareas[$areaid] = $searcharea;
       self::$allsearchareas[$areaid] = $searcharea;
    }

    /**
     * Loads all core search areas.
     *
     * @return void
     */
    public function add_core_search_areas() {
        self::get_search_areas_list(false);
        self::get_search_areas_list(true);
    }

    /**
     * Changes visibility.
     *
     * @param string $classname
     * @return bool
     */
    public static function is_search_area($classname) {
        return parent::is_search_area($classname);
    }

    /**
     * Fakes the current time for PHPunit. Turns off faking time if called with default parameter.
     *
     * Note: This should be replaced with core functionality once possible (see MDL-60644).
     *
     * @param float $faketime Current time
     */
    public static function fake_current_time($faketime = 0.0) {
        static::$phpunitfaketime = $faketime;
    }

    /**
     * Makes build_limitcourseids method public for testing.
     *
     * @param \stdClass $formdata Submitted search form data.
     *
     * @return array|bool
     */
    public function build_limitcourseids(\stdClass $formdata) {
        $limitcourseids = parent::build_limitcourseids($formdata);

        return $limitcourseids;
    }
}
