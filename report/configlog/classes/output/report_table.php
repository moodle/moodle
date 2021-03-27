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

require_once($CFG->libdir . '/searchlib.php');
require_once($CFG->libdir . '/tablelib.php');

/**
 * Report table class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table extends \table_sql implements \renderable {

    /** @var string $search */
    protected $search;

    /**
     * Constructor
     *
     * @param string $search
     */
    public function __construct(string $search) {
        parent::__construct('report-configlog-report-table');

        $this->search = trim($search);

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
        $this->set_attribute('id', $this->uniqueid);
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
        global $DB;

        $userfieldsapi = \core_user\fields::for_name();
        $userfields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $fields = 'cl.id, cl.timemodified, cl.plugin, cl.name, cl.value, cl.oldvalue, cl.userid, ' . $userfields;

        $from = '{config_log} cl
            LEFT JOIN {user} u ON u.id = cl.userid';

        // Report search.
        $where = '1=1';
        $params = [];

        if (!empty($this->search)) {
            // Clean quotes, allow search by 'setting:' prefix.
            $searchstring = str_replace(["\\\"", 'setting:'], ["\"", 'subject:'], $this->search);

            $parser = new \search_parser();
            $lexer = new \search_lexer($parser);

            if ($lexer->parse($searchstring)) {
                $parsearray = $parser->get_parsed_array();

                // Data fields should contain both value/oldvalue.
                $datafields = $DB->sql_concat_join("':'", ['cl.value', 'cl.oldvalue']);

                list($where, $params) = search_generate_SQL($parsearray, $datafields, 'cl.name', 'cl.userid', 'u.id',
                    'u.firstname', 'u.lastname', 'cl.timemodified', 'cl.id');
            }
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql('SELECT COUNT(1) FROM ' . $from . ' WHERE ' . $where, $params);
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
     * Format fullname field
     *
     * @param stdClass $row
     * @return string
     */
    public function col_fullname($row) {

        $userid = $row->{$this->useridfield};
        if (empty($userid)) {
            // If the user id is empty it must have been set via the
            // admin/cli/cfg.php script or during the initial install.
            return get_string('usernone', 'report_configlog');
        } else {
            return parent::col_fullname($row);
        }
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
