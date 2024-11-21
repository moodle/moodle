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
 * External.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use block_xp\external\external_api;
use block_xp\external\external_function_parameters;
use block_xp\external\external_value;

/**
 * External class.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Level Up XP 3.15, use block_xp\external classes instead.
 */
class external extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function search_courses_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Search courses.
     *
     * @param string $query The query.
     * @return array
     */
    public static function search_courses($query) {
        throw new \coding_exception('Method deprecated, use block_xp\external\search_courses instead.');
    }

    /**
     * External function return values.
     *
     * @return external_value
     */
    public static function search_courses_returns() {
        return new external_value(PARAM_BOOL);
    }

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function search_modules_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Search modules.
     *
     * @param int $courseid The course ID.
     * @param string $query The query.
     * @return array
     */
    public static function search_modules($courseid, $query) {
        throw new \coding_exception('Method deprecated, use block_xp\external\search_modules instead.');
    }

    /**
     * External function return values.
     *
     * @return external_value
     */
    public static function search_modules_returns() {
        return new external_value(PARAM_BOOL);
    }

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function set_default_levels_info_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Allow AJAX use.
     *
     * @return true
     */
    public static function set_default_levels_info_is_allowed_from_ajax() {
        return true;
    }

    /**
     * External function.
     *
     * @param array $levels The levels.
     * @param array $algo The algo.
     * @return object
     */
    public static function set_default_levels_info($levels, $algo) {
        throw new \coding_exception('Method deprecated, use block_xp\external\set_default_levels_info instead.');
    }

    /**
     * External function return definition.
     *
     * @return external_description
     */
    public static function set_default_levels_info_returns() {
        return new external_value(PARAM_BOOL);
    }

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function set_levels_info_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Allow AJAX use.
     *
     * @return true
     */
    public static function set_levels_info_is_allowed_from_ajax() {
        return true;
    }

    /**
     * External function.
     *
     * @param int $courseid The course ID.
     * @param array $levels The levels.
     * @param array $algo The algo.
     * @return object
     */
    public static function set_levels_info($courseid, $levels, $algo) {
        throw new \coding_exception('Method deprecated, use block_xp\external\set_levels_info instead.');
    }

    /**
     * External function return definition.
     *
     * @return external_description
     */
    public static function set_levels_info_returns() {
        return new external_value(PARAM_BOOL);
    }

}
