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

use core\chart_line;
use core\chart_series;
use mod_adaptivequiz\local\report\attempt_report_helper;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * An object to render as a report on question administration for the given attempt.
 *
 * @package    mod_adaptivequiz
 * @copyright  2024 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_administration_report implements renderable, templatable {

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
        $data = attempt_report_helper::prepare_administration_data($this->attemptid);

        $chart = new chart_line();
        $chart->set_labels(array_keys($data));

        $xaxis = $chart->get_xaxis(0, true);
        $xaxis->set_label(get_string('questionnumber', 'adaptivequiz'));

        $yaxis = $chart->get_yaxis(0, true);
        $yaxis->set_label(get_string('attemptquestion_ability', 'adaptivequiz'));
        $yaxis->set_stepsize(1);

        $targetdiffseries = new chart_series(get_string('reportattemptadmcharttargetdifflabel', 'adaptivequiz'),
            array_values(array_map(fn (stdClass $dataitem): int => $dataitem->targetdifficulty, $data)));
        $targetdiffseries->set_color('#a1caf1');
        $chart->add_series($targetdiffseries);

        $admdiffseries = new chart_series(get_string('reportattemptadmchartadmdifflabel', 'adaptivequiz'),
            array_values(array_map(fn (stdClass $dataitem): int => $dataitem->administereddifficulty, $data)));
        $admdiffseries->set_color('#875692');
        $chart->add_series($admdiffseries);

        $chart->add_series(new chart_series(get_string('attemptquestion_rightwrong', 'adaptivequiz'),
            array_values(array_map(
                fn (stdClass $dataitem): string => $dataitem->answeredcorrectly
                    ? get_string('reportattemptadmanswerright', 'adaptivequiz')
                    : get_string('reportattemptadmanswerwrong', 'adaptivequiz'),
                $data
            ))
        ));

        $abilityseries = new chart_series(get_string('attemptquestion_ability', 'adaptivequiz'),
            array_values(array_map(fn (stdClass $dataitem): float => round($dataitem->abilitymeasure, 2), $data)));
        $abilityseries->set_color('#7f180d');
        $chart->add_series($abilityseries);

        // We don't care about the label here, as it's supposed to be not displayed.
        $standarderrormaxseries = new chart_series('standarderrormax', array_values(array_map(
            fn (stdClass $dataitem): float => round($dataitem->standarderrormax, 2), $data
        )));
        $standarderrormaxseries->set_fill('-1');
        $standarderrormaxseries->set_color('rgba(255, 26, 104, 0.2)');
        $chart->add_series($standarderrormaxseries);

        // Same for the label as above.
        $standarderrorminseries = new chart_series('standarderrormin', array_values(array_map(
            fn (stdClass $dataitem): float => round($dataitem->standarderrormin, 2), $data
        )));
        $standarderrorminseries->set_fill('-2');
        $standarderrorminseries->set_color('rgba(255, 26, 104, 0.2)');
        $chart->add_series($standarderrorminseries);

        $chart->add_series(new chart_series(get_string('graphlegend_error', 'adaptivequiz'),
            array_values(array_map(
                fn (stdClass $dataitem): string => '+/- ' . format_float($dataitem->standarderror * 100, 2)  . '%', $data
            ))
        ));

        return [
            'chartdata' => json_encode($chart),
            'withtable' => true,
        ];
    }
}
