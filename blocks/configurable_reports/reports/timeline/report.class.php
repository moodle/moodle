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
 * Class report_timeline
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_timeline extends report_base {

    /**
     * @var mixed
     */
    private $timeline;

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->components = [
            'timeline',
            'columns',
            'filters',
            'template',
            'permissions',
            'calcs',
            'plot',
        ];
    }

    /**
     * Get all elements
     *
     * @return array
     */
    public function get_all_elements(): array {
        $elements = [];

        $components = cr_unserialize($this->config->components);

        $config = $components['timeline']['config'] ?? new stdclass();

        if (isset($config->timemode)) {

            $daysecs = 60 * 60 * 24;

            if ($config->timemode === 'previous') {
                $config->starttime = time() - $config->previousstart * $daysecs;
                $config->endtime = time() - $config->previousend * $daysecs;
                if (isset($config->forcemidnight)) {
                    $config->starttime = usergetmidnight($config->starttime);
                    $config->endtime = usergetmidnight($config->endtime) + ($daysecs - 1);
                }
            }

            $filterstarttime = optional_param('filter_starttime', 0, PARAM_RAW);
            $filterendtime = optional_param('filter_endtime', 0, PARAM_RAW);

            if ($filterstarttime && $filterendtime) {
                $filterstarttime = make_timestamp($filterstarttime['year'], $filterstarttime['month'], $filterstarttime['day']);
                $filterendtime = make_timestamp($filterendtime['year'], $filterendtime['month'], $filterendtime['day']);

                $config->starttime = usergetmidnight($filterstarttime);
                $config->endtime = usergetmidnight($filterendtime) + 24 * 60 * 60;
            }

            for ($i = $config->starttime; $i < $config->endtime; $i += $config->interval * $daysecs) {
                $row = new stdclass();
                $row->id = $i;
                $row->starttime = $i;
                $row->endtime = $row->starttime + ($config->interval * $daysecs - 1);
                if ($row->endtime > $config->endtime) {
                    $row->endtime = $config->endtime;
                }
                $this->timeline[$row->starttime] = $row;
                $elements[] = $row->starttime;
            }

            if ($config->ordering === 'desc') {
                rsort($elements);
            }
        }

        return $elements;
    }

    /**
     * get_rows
     *
     * @param array $elements
     * @param string $sqlorder
     * @return array
     */
    public function get_rows(array $elements, string $sqlorder = '') {

        if (!empty($elements)) {
            $finaltimeline = [];
            foreach ($elements as $e) {
                $finaltimeline[] = $this->timeline[$e];
            }

            return $finaltimeline;
        }

        return [];

    }

}
