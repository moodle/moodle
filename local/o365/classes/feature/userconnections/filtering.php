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
 * User filtering class.
 *
 * @package local_o365
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 onwards Remote-Learner Inc (http://www.remote-learner.net)
 */

namespace local_o365\feature\userconnections;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/user/filters/lib.php');

/**
 * User filtering class.
 */
class filtering extends \user_filtering {

    /**
     * Creates known user filter if present.
     *
     * @param string $fieldname
     * @param boolean $advanced
     * @return object filter
     */
    public function get_field($fieldname, $advanced) {
        global $DB, $USER;
        switch ($fieldname) {

            case 'username':
                $label = get_string('acp_userconnections_filtering_musername', 'local_o365');
                return new \user_filter_text('username', $label, $advanced, 'u.username');

            case 'o365username':
                $label = get_string('acp_userconnections_filtering_o365username', 'local_o365');
                return new \user_filter_text('o365username', $label, $advanced, 'o365username');

            case 'idnumber':
                return new \user_filter_text('idnumber', get_string('idnumber'), $advanced, 'u.idnumber');

            case 'realname':
                $label = get_string('acp_userconnections_filtering_muserfullname', 'local_o365');
                $filteron = $DB->sql_fullname();
                return new \user_filter_text('realname', $label, $advanced, $filteron);

            case 'lastname':
                return new \user_filter_text('lastname', get_string('lastname'), $advanced, 'u.lastname');

            case 'firstname':
                return new \user_filter_text('firstname', get_string('firstname'), $advanced, 'u.firstname');

            case 'email':
                return new \user_filter_text('email', get_string('email'), $advanced, 'u.email');

            default:
                return null;
        }
    }

    /**
     * Returns sql where statement based on active user filters.
     *
     * @param string $extra sql
     * @param array|null $params named params (recommended prefix ex)
     * @return array sql string and $params
     */
    public function get_sql_filter($extra='', ?array $params=null) {
        global $SESSION;

        $sqls = [];
        if ($extra != '') {
            $sqls[] = $extra;
        }
        $params = (array)$params;

        if (!empty($SESSION->user_filtering)) {
            foreach ($SESSION->user_filtering as $fname => $datas) {
                if (!array_key_exists($fname, $this->_fields)) {
                    continue; // Filter not used.
                }
                if ($fname == 'o365username') {
                    continue;
                }
                $field = $this->_fields[$fname];
                foreach ($datas as $i => $data) {
                    [$s, $p] = $field->get_sql_filter($data);
                    $sqls[] = $s;
                    $params = $params + $p;
                }
            }
        }

        if (empty($sqls)) {
            return ['', []];
        } else {
            $sqls = implode(' AND ', $sqls);
            return [$sqls, $params];
        }
    }

    /**
     * Get the filter value for the "o365username" filter.
     *
     * @return array List of filter SQLs and parameters for the o365username filter.
     */
    public function get_filter_o365username() {
        global $SESSION;
        $sqls = [];
        $params = [];
        $fname = 'o365username';
        if (isset($SESSION->user_filtering[$fname])) {
            $datas = $SESSION->user_filtering[$fname];
            $field = $this->_fields[$fname];
            foreach ($datas as $i => $data) {
                [$s, $p] = $field->get_sql_filter($data);
                $sqls[] = $s;
                $params = $params + $p;
            }
        }

        if (empty($sqls)) {
            return ['', []];
        } else {
            $sqls = implode(' AND ', $sqls);
            return [$sqls, $params];
        }
    }
}
