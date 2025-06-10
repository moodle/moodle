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
 * Sql reports table.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\output\tables;
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/tablelib.php');


/**
 * Sql reports table.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sql_reports_table extends \table_sql {

    /**
     * Sql reports table construct.
     *
     * @param $uniqueid
     * @throws \coding_exception
     */
    public function __construct($uniqueid) {
        global $PAGE;

        parent::__construct($uniqueid);

        $this->define_headers([
            get_string('sql_report_name', 'local_intellidata'),
            get_string('sql_report_status', 'local_intellidata'),
            get_string('sql_report_date', 'local_intellidata'),
            get_string('sql_report_actions', 'local_intellidata'),
        ]);
        $this->define_columns(['name', 'status', 'timecreated', 'actions']);

        $fields = "lir.*, '' AS actions";
        $from = "{local_intellidata_reports} lir";

        $this->set_sql($fields, $from, 'lir.id > 0', []);
        $this->define_baseurl($PAGE->url);
    }

    /**
     * Column time created.
     *
     * @param $values
     * @return false|string
     */
    public function col_timecreated($values) {
        return date('m/d/Y', $values->timecreated);
    }

    /**
     * Column status.
     *
     * @param $values
     * @return mixed
     * @throws \coding_exception
     */
    public function col_status($values) {
        return [
            get_string('sql_report_inactive', 'local_intellidata'),
            get_string('sql_report_active', 'local_intellidata'),
        ][$values->status];
    }

    /**
     * Column actions.
     *
     * @param $values
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_actions($values) {
        global $CFG;

        $buttons = [];
        $urlparams = ['id' => $values->id];

        $buttons[] = \html_writer::link(
            new \moodle_url($CFG->wwwroot.'/local/intellidata/sql_reports/report.php', $urlparams),
            get_string('edit'),
            ['title' => get_string('edit')]
        );

        $buttons[] = \html_writer::link(
            new \moodle_url($CFG->wwwroot.'/local/intellidata/sql_reports/report.php', $urlparams + ['delete' => 1]),
            get_string('delete'),
            ['title' => get_string('delete')]
        );

        return implode(' | ', $buttons);
    }

}
