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
 * Inspire predictions list page.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_insights\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Shows report_insights predictions list.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class predictions_list implements \renderable, \templatable {

    /**
     * @var \core_analytics\model
     */
    protected $model;

    /**
     * @var \context
     */
    protected $context;

    /**
     * @var \core_analytics\model
     */
    protected $othermodels;

    public function __construct(\core_analytics\model $model, \context $context, $othermodels) {
        $this->model = $model;
        $this->context = $context;
        $this->othermodels = $othermodels;
    }

    /**
     * Exports the data.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;

        $data = new \stdClass();

        $predictions = $this->model->get_predictions($this->context);

        $data->predictions = array();
        foreach ($predictions as $prediction) {
            $predictionrenderable = new \report_insights\output\prediction($prediction, $this->model);
            $data->predictions[] = $predictionrenderable->export_for_template($output);
        }

        if (empty($data->predictions)) {
            $notification = new \core\output\notification(get_string('nopredictionsyet', 'analytics'));
            $data->nopredictions = $notification->export_for_template($output);
        }

        if ($this->othermodels) {

            $options = array();
            foreach ($this->othermodels as $model) {
                $options[$model->get_id()] = $model->get_target()->get_name();
            }

            // New moodle_url instance returned by magic_get_url.
            $url = $PAGE->url;
            $url->remove_params('modelid');
            $modelselector = new \single_select($url, 'modelid', $options, '',
                array('' => get_string('selectotherinsights', 'report_insights')));
            $data->modelselector = $modelselector->export_for_template($output);
        }

        return $data;
    }
}
