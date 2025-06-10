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
 * Class plugin_coursefieldorder
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_coursefieldorder extends plugin_base {

    /**
     * @var bool
     */
    public $sql = true;

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('coursefield', 'block_configurable_reports');
        $this->form = true;
        $this->unique = true;
        $this->reporttypes = ['courses'];
        $this->sql = true;
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string($data->column) . ' ' . (strtoupper($data->direction));
    }

    /**
     * Execute
     *
     * @param object $data
     * @return string
     */
    public function execute(object $data) {
        global $DB;

        // Data -> Plugin configuration data.
        if ($data->direction === 'asc' || $data->direction === 'desc') {
            $direction = strtoupper($data->direction);
            $columns = $DB->get_columns('course');

            $coursecolumns = [];
            foreach ($columns as $c) {
                $coursecolumns[$c->name] = $c->name;
            }

            if (isset($coursecolumns[$data->column])) {
                return $data->column . ' ' . $direction;
            }
        }

        return '';
    }

}
