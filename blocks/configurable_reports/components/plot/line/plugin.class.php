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
 * Class plugin_line
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_line extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('line', 'block_configurable_reports');
        $this->form = true;
        $this->ordering = true;
        $this->reporttypes = ['timeline', 'sql', 'timeline'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('linesummary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param int $id
     * @param object $data
     * @param array $finalreport
     * @return string
     */
    public function execute($id, $data, $finalreport) {
        global $CFG;

        $series = [];
        $data->xaxis--;
        $data->yaxis--;
        $data->serieid--;
        $minvalue = 0;
        $maxvalue = 0;

        if ($finalreport) {
            foreach ($finalreport as $r) {
                $hash = md5(strtolower($r[$data->serieid] ?? ''));
                $sname[$hash] = $r[$data->serieid] ?? null;
                $val = (isset($r[$data->yaxis]) && is_numeric($r[$data->yaxis])) ? $r[$data->yaxis] : 0;
                $series[$hash][] = $val;
                $minvalue = ($val < $minvalue) ? $val : $minvalue;
                $maxvalue = ($val > $maxvalue) ? $val : $maxvalue;
            }
        }

        $params = '';

        $i = 0;
        foreach ($series as $h => $s) {
            $params .= "&amp;serie$i=" . base64_encode($sname[$h] . '||' . implode(',', $s));
            $i++;
        }

        return $CFG->wwwroot . '/blocks/configurable_reports/components/plot/line/graph.php?reportid=' . $this->report->id .
            '&id=' . $id . $params . '&amp;min=' . $minvalue . '&amp;max=' . $maxvalue  . '&courseid='.$this->report->courseid;
    }

    /**
     * Get series
     *
     * @return array
     */
    public function get_series(): array {
        $series = [];

        // TODO don't use $_GET.
        foreach ($_GET as $key => $val) {
            if (strpos($key, 'serie') !== false) {
                $id = (int) str_replace('serie', '', $key);
                [$name, $values] = explode('||', base64_decode($val));
                $series[$id] = ['serie' => explode(',', $values), 'name' => $name];
            }
        }

        return $series;
    }

}
