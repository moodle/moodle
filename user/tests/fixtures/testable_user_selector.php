<?php
// This file is part of Moodle - https://moodle.org/
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
 * Provides {@link testable_user_selector} class.
 *
 * @package     core_user
 * @subpackage  fixtures
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Testable subclass of the user selector base class.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_user_selector extends user_selector_base {

    /**
     * Basic implementation of the users finder.
     *
     * @param string $search
     * @return array of (string)optgroupname => array of users
     */
    public function find_users($search) {
        global $DB;

        list($wherecondition, $whereparams) = $this->search_sql($search, 'u');
        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $params = array_merge($whereparams, $sortparams);
        $fields = $this->required_fields_sql('u');

        $sql = "SELECT $fields
                  FROM {user} u
                 WHERE $wherecondition
              ORDER BY $sort";

        $found = $DB->get_records_sql($sql, $params);

        if (empty($found)) {
            return [];
        }

        return [get_string('potusers', 'core_role') => $found];
    }

}
