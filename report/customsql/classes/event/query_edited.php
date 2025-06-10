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
 * The customsql query edited event.
 *
 * @package    report_customsql
 * @copyright  2014 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_customsql\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event generated when a query is edited.
 *
 * @package report_customsql
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query_edited extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'report_customsql_queries';
    }

    public static function get_name() {
        return get_string('query_edited', 'report_customsql');
    }

    public function get_description() {
        return "User {$this->userid} has edited the SQL query with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/report/customsql/view.php', array('id' => $this->objectid));
    }

    public function get_legacy_logdata() {
        $url = '../report/customsql/view.php?id=' . $this->objectid;
        return array(0, "report_customsql", 'edit query', $url, $this->objectid, $this->contextinstanceid);
    }
}
