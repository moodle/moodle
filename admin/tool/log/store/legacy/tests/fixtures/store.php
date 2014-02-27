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
 * Fixtures for legacy logging testing.
 *
 * @package    logstore_legacy
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_legacy\test;

defined('MOODLE_INTERNAL') || die();

class unittest_logstore_legacy extends \logstore_legacy\log\store {

    /**
     * Wrapper to make protected method accessible during testing.
     *
     * @param array $match matched string for the passed pattern
     *
     * @return string The sql string to use instead of original
     */
    public static function replace_crud($match) {
        return parent::replace_crud($match);
    }

    /**
     * Wrapper to make protected method accessible during testing.
     *
     * @param string $select sql predicate.
     * @param array $params sql params.
     *
     * @return array returns array of sql predicate and params.
     */
    public static function replace_sql_hack($select, array $params) {
        return parent::replace_sql_hacks($select, $params);
    }
}
