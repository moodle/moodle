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
    public function get_areas_user_accesses($limitcourseids = false) {
        return parent::get_areas_user_accesses($limitcourseids);
    }

    /**
     * Adds an enabled search component to the search areas list.
     *
     * @param string $areaid
     * @param \core_search\area\base $searcharea
     * @return void
     */
    public function add_search_area($areaid, \core_search\area\base $searcharea) {
       self::$enabledsearchareas[$areaid] = $searcharea;
       self::$allsearchareas[$areaid] = $searcharea;
    }
}
