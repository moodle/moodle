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

namespace mod_adaptivequiz\output\report;

use core\chart_bar;
use core\chart_series;
use mod_adaptivequiz\local\report\attempt_report_helper;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * An object to render as a report on answers distribution for the given attempt.
 *
 * @package    mod_adaptivequiz
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_answers_distribution_report implements renderable, templatable {

    /**
     * @var int $attemptid
     */
    private int $attemptid;

    /**
     * The constructor.
     *
     * @param int $attemptid
     */
    public function __construct(int $attemptid) {
        $this->attemptid = $attemptid;
    }

    /**
     * Implementation of the interface.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $DB;

        $attemptrecord = $DB->get_record('adaptivequiz_attempt', ['id' => $this->attemptid], '*', MUST_EXIST);
        $adaptivequizid = $attemptrecord->instance;

        $showchartstacked = true;
        if ($chartsettingsjson = get_user_preferences("mod_adaptivequiz_answers_distribution_chart_settings_$adaptivequizid")) {
            $chartsettings = json_decode($chartsettingsjson);
            $showchartstacked = $chartsettings->showstacked;
        }

        $data = attempt_report_helper::prepare_answers_distribution_data($this->attemptid);
        // Display only those levels where question were actually administered.
        $data = array_filter($data, fn (stdClass $dataitem): bool => $dataitem->numcorrect > 0 || $dataitem->numwrong > 0);

        $chart = new chart_bar();
        $chart->set_stacked($showchartstacked);
        $chart->set_labels(array_keys($data));

        $xaxis = $chart->get_xaxis(0, true);
        $xaxis->set_label(get_string('reportanswersdistributionchartxaxislabel', 'adaptivequiz'));

        $yaxis = $chart->get_yaxis(0, true);
        $yaxis->set_label(get_string('reportanswersdistributionchartyaxislabel', 'adaptivequiz'));
        $yaxis->set_stepsize(1);

        $chart->add_series(new chart_series(get_string('reportanswersdistributionchartnumrightlabel', 'adaptivequiz'),
            array_values(array_map(fn (stdClass $dataitem): int => $dataitem->numcorrect, $data))));

        $chart->add_series(new chart_series(get_string('reportanswersdistributionchartnumwronglabel', 'adaptivequiz'),
            array_values(array_map(fn (stdClass $dataitem): int => $dataitem->numwrong, $data))));

        return [
            'showchartstacked' => $showchartstacked,
            'userid' => $USER->id,
            'adaptivequizid' => $adaptivequizid,
            'chartdata' => json_encode($chart),
            'withtable' => true,
        ];
    }
}
