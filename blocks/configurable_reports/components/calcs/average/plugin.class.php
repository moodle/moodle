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

/**
 * Class plugin_average
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_average extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = true;
        $this->unique = false;
        $this->fullname = get_string('average', 'block_configurable_reports');
        $this->reporttypes = ['courses', 'users', 'sql', 'timeline', 'categories'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        global $CFG;

        if ($this->report->type !== 'sql') {
            $components = cr_unserialize($this->report->components);
            if (!is_array($components) || empty($components['columns']['elements'])) {
                throw new moodle_exception('nocolumns');
            }

            $columns = $components['columns']['elements'];
            $i = 0;
            foreach ($columns as $c) {
                if ($i == $data->column) {
                    return $c['summary'];
                }
                $i++;
            }
        } else {

            require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
            require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $this->report->type . '/report.class.php');

            $reportclassname = 'report_' . $this->report->type;
            $reportclass = new $reportclassname($this->report);

            $components = cr_unserialize($this->report->components);
            $config = $components['customsql']['config'] ?? new stdclass;

            if (isset($config->querysql)) {

                $sql = $config->querysql;
                $sql = $reportclass->prepare_sql($sql);
                if ($rs = $reportclass->execute_query($sql)) {
                    foreach ($rs as $row) {
                        $i = 0;
                        foreach ($row as $colname => $value) {
                            if ($i == $data->column) {
                                return str_replace('_', ' ', $colname);
                            }
                            $i++;
                        }
                        break;
                    }
                    $rs->close();
                }
            }
        }

        return '';
    }

    /**
     * Execute
     *
     * @param array $rows
     * @return float
     */
    public function execute($rows): float {
        $result = 0;
        $els = 0;
        foreach ($rows as $r) {
            $result += (is_numeric($r)) ? $r : 0;
            $els++;
        }

        if (empty($els)) {
            $els = 1;
        }

        return round($result / $els, 2);
    }

}
