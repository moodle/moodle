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

namespace tool_brickfield\output\checktyperesults;

use core\chart_pie as chart_pie;
use core\chart_series as chart_series;
use tool_brickfield\accessibility;
use tool_brickfield\local\tool\filter;
use tool_brickfield\manager;

/**
 * tool_brickfield/checktyperesults renderer
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \tool_brickfield\output\renderer {
    /**
     * Render the page containing the Checktype results report.
     * @param \stdClass $data
     * @param filter $filter
     * @return bool|string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function display(\stdClass $data, filter $filter): string {
        $templatedata = new \stdClass();

        // Set up the page information for the external renderer.
        $templatedata->title = accessibility::get_title($filter, $data->countdata);
        $templatedata->chartdesc = get_string('pagedesc:checktype', manager::PLUGINNAME);
        $templatedata->chartdesctitle = get_string('pagedesctitle:checktype', manager::PLUGINNAME);

        // Set up a table of data for the template.
        $noerrorsfound = true;
        $grouperrors = [];
        $labels = [];
        $count = 0;
        for ($i = 1; (($count < $data->data[0]->groupcount) && ($i < 10)); $i++) {
            if (isset($data->data[0]->{'componentlabel' . $i})) {
                $grouperrors[] = $data->data[0]->{'errorsvalue' . $i};
                if ($data->data[0]->{'errorsvalue' . $i} > 0) {
                    $noerrorsfound = false;
                }
                $labels[] = $data->data[0]->{'componentlabel' . $i};
                $count++;
            }
        }

        if ($noerrorsfound) {
            $templatedata->pagetitle = $templatedata->title;
            $templatedata->noerrorsfound = get_string('noerrorsfound', manager::PLUGINNAME);
            return $this->render_from_template(manager::PLUGINNAME . '/norecords', $templatedata);
        }

        $chart = new chart_pie();
        $chart->set_doughnut(true);
        $series1 = new chart_series(get_string('totalerrors', manager::PLUGINNAME), $grouperrors);
        $chart->add_series($series1);
        $chart->set_labels($labels);
        $chart->set_title(get_string('totalgrouperrors', manager::PLUGINNAME));

        $templatedata->chart = $this->render($chart);
        return $this->render_from_template(manager::PLUGINNAME . '/chartsingle', $templatedata);
    }
}
