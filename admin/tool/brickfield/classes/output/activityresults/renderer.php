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

namespace tool_brickfield\output\activityresults;

use core\chart_bar as chart_bar;
use core\chart_series as chart_series;
use tool_brickfield\accessibility;
use tool_brickfield\local\tool\filter;
use tool_brickfield\manager;

/**
 * tool_brickfield/activityresults renderer
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \tool_brickfield\output\renderer {
    /**
     * Render the page containing the activity results report.
     *
     * @param \stdClass $data Report data.
     * @param filter $filter Display filters.
     * @return String HTML showing charts.
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function display(\stdClass $data, filter $filter): string {
        $templatedata = new \stdClass();

        // Set up the page information for the template.
        $templatedata->title = accessibility::get_title($filter, $data->countdata);
        $templatedata->chartdesc = get_string('pagedesc:pertarget', manager::PLUGINNAME);
        $templatedata->chartdesctitle = get_string('pagedesctitle:pertarget', manager::PLUGINNAME);

        // Set up a table of data for the external renderer.
        $labels = [];
        $failed = [];
        $passed = [];
        foreach ($data->data as $rowdata) {
            $labels[] = $rowdata->componentlabel;
            $passed[] = $rowdata->total - $rowdata->failed;
            $failed[] = $rowdata->failed;
        }

        $chart = new chart_bar();
        $chart->set_stacked(true);
        $series1 = new chart_series(get_string('passed', manager::PLUGINNAME), $passed);
        $series2 = new chart_series(get_string('failed', manager::PLUGINNAME), $failed);
        $chart->add_series($series1);
        $chart->add_series($series2);
        $chart->set_labels($labels);
        $chart->set_title(get_string('targetratio', manager::PLUGINNAME));

        $templatedata->chart = $this->render($chart);
        return $this->render_from_template(manager::PLUGINNAME . '/chartsingle', $templatedata);
    }
}
