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
 * Predict system models with new data available.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Predict system models with new data available.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class predict_models extends \core\task\scheduled_task {

    /**
     * get_name
     *
     * @return string
     */
    public function get_name() {
        return get_string('predictmodels', 'tool_analytics');
    }

    /**
     * Executes the prediction task.
     *
     * @return void
     */
    public function execute() {
        global $OUTPUT, $PAGE;

        $models = \core_analytics\manager::get_all_models(true, true);
        if (!$models) {
            mtrace(get_string('errornoenabledandtrainedmodels', 'tool_analytics'));
            return;
        }

        foreach ($models as $model) {
            $result = $model->predict();

            // Reset the page as some indicators may call external functions that overwrite the page context.
            \tool_analytics\output\helper::reset_page();

            if ($result) {
                echo $OUTPUT->heading(get_string('modelresults', 'tool_analytics', $model->get_name()));
                $renderer = $PAGE->get_renderer('tool_analytics');
                echo $renderer->render_get_predictions_results(false, array(), $result, $model->get_analyser()->get_logs());
            }
        }

    }
}
