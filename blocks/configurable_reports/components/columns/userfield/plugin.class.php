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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_userfield
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_userfield extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('userfield', 'block_configurable_reports');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = ['users'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return format_string($data->columname);
    }

    /**
     * Execute
     *
     * @param object $data
     * @param object $row
     * @return string
     */
    public function execute($data, $row) {
        global $DB;

        // Data -> Plugin configuration data.
        // Row -> Complet user row c->id, c->fullname, etc...

        if (strpos($data->column, 'profile_') === 0) {
            $sql = "SELECT d.*, f.shortname, f.datatype
                      FROM {user_info_data} d ,{user_info_field} f
                     WHERE f.id = d.fieldid AND d.userid = ?";
            if ($profiledata = $DB->get_records_sql($sql, [$row->id])) {
                foreach ($profiledata as $p) {
                    if ($p->datatype == 'checkbox') {
                        $p->data = ($p->data) ? get_string('yes') : get_string('no');
                    }
                    if ($p->datatype == 'datetime') {
                        $p->data = userdate($p->data);
                    }
                    $row->{'profile_' . $p->shortname} = $p->data;
                }
            }
        }

        $row->fullname = fullname($row);

        if (isset($row->{$data->column})) {
            switch ($data->column) {
                case 'firstaccess':
                case 'lastaccess':
                case 'currentlogin':
                case 'timemodified':
                case 'lastlogin':
                    $row->{$data->column} = ($row->{$data->column}) ? userdate($row->{$data->column}) : '--';
                    break;
                case 'confirmed':
                case 'policyagreed':
                case 'maildigest':
                case 'ajax':
                case 'autosubscribe':
                case 'trackforums':
                case 'screenreader':
                case 'emailstop':
                    $row->{$data->column} = ($row->{$data->column}) ? get_string('yes') : get_string('no');
                    break;
            }
        }

        return $row->{$data->column} ?? '';
    }

}
