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
 * Effectiveness report renderable.
 *
 * @package    tool_analytics
 * @copyright  2019 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Effectiveness report renderable.
 *
 * @package    tool_analytics
 * @copyright  2019 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class effectiveness_report implements \renderable, \templatable {

    /**
     * @var \core_analytics\model
     */
    private $model = null;

    /**
     * @var \context
     */
    private $context = null;

    /**
     * Inits the effectiveness report renderable.
     *
     * @param \core_analytics\model $model
     * @param int|null $contextid
     * @return null
     */
    public function __construct(\core_analytics\model $model, ?int $contextid = null) {
        $this->model = $model;
        if ($contextid) {
            $this->context = \context::instance_by_id($contextid);
        }
    }

    /**
     * Export the data.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output): \stdClass {

        // Prepare the context object.
        $data = new \stdClass();
        $data->modelname = $this->model->get_name();

        $data->charts = [];

        $predictionactionrecords = $this->model->get_prediction_actions($this->context);

        // Context selector.
        $predictioncontexts = $this->model->get_predictions_contexts(false);
        if ($predictioncontexts && count($predictioncontexts) > 1) {
            $url = new \moodle_url('/admin/tool/analytics/model.php', ['id' => $this->model->get_id(),
                'action' => 'effectivenessreport']);

            if ($this->context) {
                $selected = $this->context->id;
            } else {
                // This is the 'all' option.
                $selected = 0;
            }
            $data->contextselect = \tool_analytics\output\helper::prediction_context_selector($predictioncontexts,
                $url, $output, $selected, true, false);
        }

        if ($predictionactionrecords->valid()) {

            foreach ($predictionactionrecords as $record) {

                // Using this unusual execution flow to init the chart data because $predictionactionrecords
                // is a \moodle_recordset.
                if (empty($actionlabels)) {
                    list($actionlabels, $actionvalues) = $this->init_action_labels($record);
                }

                // One value for each action.
                $actionvalues['separated'][$record->actionname]++;

                // Data grouped in three boxes.
                if ($record->actionname == 'notuseful') {
                    $actionvalues['grouped']['negative']++;
                } else if ($record->actionname == 'predictiondetails') {
                    $actionvalues['grouped']['neutral']++;
                } else {
                    $actionvalues['grouped']['positive']++;
                }
            }
            $predictionactionrecords->close();

            // Actions doughtnut.
            $chart = new \core\chart_pie();
            $chart->set_doughnut(true);
            $chart->set_title(get_string('actionsexecutedbyusers', 'tool_analytics'));
            $series = new \core\chart_series(get_string('actions', 'tool_analytics'),
                array_values($actionvalues['separated']));
            $chart->add_series($series);
            $chart->set_labels(array_values($actionlabels['separated']));
            $data->separatedchart = $output->render($chart);

            // Positive/negative/neutral bar chart.
            $chart = new \core\chart_bar();
            $chart->set_title(get_string('actionexecutedgroupedusefulness', 'tool_analytics'));
            $series = new \core\chart_series(get_string('actions', 'tool_analytics'),
                array_values($actionvalues['grouped']));
            $chart->add_series($series);
            $chart->set_labels(array_values($actionlabels['grouped']));
            $data->groupedchart = $output->render($chart);

        } else {
            $predictionactionrecords->close();
            $data->noactions = [
                'message' => get_string('noactionsfound', 'tool_analytics'),
                'announce' => true,
            ];
        }
        return $data;
    }

    /**
     * Initialises the action labels and values in this model.
     *
     * @param  \stdClass $predictionactionrecord
     * @return array Two-dimensional array with the labels and values initialised to zero.
     */
    private function init_action_labels(\stdClass $predictionactionrecord): array {

        $predictioncontext = \context::instance_by_id($predictionactionrecord->contextid);

        // Just 1 result, we just want to retrieve the prediction action names.
        list ($unused, $predictions) = $this->model->get_predictions($predictioncontext, false, 0, 1);

        // We pass 'true' for $isinsightuser so all the prediction actions available for this target are returning.
        $predictionactions = $this->model->get_target()->prediction_actions(reset($predictions), true, true);

        $actionlabels = [];
        $actionvalues = ['separated' => [], 'grouped' => []];
        foreach ($predictionactions as $action) {
            $actionlabels['separated'][$action->get_action_name()] = $action->get_text();
            $actionvalues['separated'][$action->get_action_name()] = 0;
        }

        $actionlabels['grouped']['positive'] = get_string('useful', 'analytics');
        $actionlabels['grouped']['neutral'] = get_string('neutral', 'analytics');
        $actionlabels['grouped']['negative'] = get_string('notuseful', 'analytics');
        $actionvalues['grouped']['positive'] = 0;
        $actionvalues['grouped']['neutral'] = 0;
        $actionvalues['grouped']['negative'] = 0;

        return [$actionlabels, $actionvalues];
    }
}
