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
 * Report table class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_configlog\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Report table class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table extends \table_sql implements \renderable {

    /**
     * Constructor
     *
     */
    public function __construct() {
        parent::__construct('report-configlog-report-table');

        // Define columns.
        $columns = [
            'timemodified' => get_string('timemodified', 'report_configlog'),
            'fullname'     => get_string('name'),
            'plugin'       => get_string('plugin', 'report_configlog'),
            'name'         => get_string('setting', 'report_configlog'),
            'value'        => get_string('valuenew', 'report_configlog'),
            'oldvalue'     => get_string('valueold', 'report_configlog'),
        ];
        $this->define_columns(array_keys($columns));
        $this->define_headers(array_values($columns));

        // Table configuration.
        $this->set_attribute('cellspacing', '0');

        $this->sortable(true, 'timemodified', SORT_DESC);

        $this->initialbars(false);
        $this->collapsible(false);

        $this->useridfield = 'userid';

        // Initialize table SQL properties.
        $this->init_sql();
    }

    /**
     * Initializes table SQL properties
     *
     * @return void
     */
    protected function init_sql() {
        $userfields = get_all_user_name_fields(true, 'u');

        $fields = 'cl.id, cl.timemodified, cl.plugin, cl.name, cl.value, cl.oldvalue, cl.userid, ' . $userfields;

        $from = '{config_log} cl
            JOIN {user} u ON u.id = cl.userid';

        $this->set_sql($fields, $from, '1=1');
        $this->set_count_sql('SELECT COUNT(1) FROM ' . $from);
    }

    /**
     * Cross DB text-compatible sorting for value/oldvalue fields
     *
     * @return string
     */
    public function get_sql_sort() {
        global $DB;

        $sort = preg_replace_callback('/\b(value|oldvalue)\b/', function(array $matches) use ($DB) {
            return $DB->sql_order_by_text($matches[1], 255);
        }, parent::get_sql_sort());

        return $sort;
    }

    /**
     * Format report timemodified field
     *
     * @param stdClass $row
     * @return string
     */
    public function col_timemodified(\stdClass $row) {
        return userdate($row->timemodified);
    }

    /**
     * Format report plugin field
     *
     * @param stdClass $row
     * @return string
     */
    public function col_plugin(\stdClass $row) {
        return $row->plugin ?? 'core';
    }

    /**
     * Format report value field
     *
     * @param stdClass $row
     * @return string
     */
    public function col_value(\stdClass $row) {
        return $this->format_text($row->value, FORMAT_PLAIN);
    }

    /**
     * Format report old value field
     *
     * @param stdClass $row
     * @return string
     */
    public function col_oldvalue(\stdClass $row) {
        return $this->format_text($row->oldvalue, FORMAT_PLAIN);;
    }
}
