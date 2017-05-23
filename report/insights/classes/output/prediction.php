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
 * Prediction view page.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_insights\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Prediction view page.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prediction implements \renderable, \templatable {

    /**
     * @var \core_analytics\model
     */
    protected $model;

    /**
     * @var \core_analytics\prediction
     */
    protected $prediction;

    public function __construct(\core_analytics\prediction $prediction, \core_analytics\model $model) {
        $this->prediction = $prediction;
        $this->model = $model;
    }

    /**
     * Exports the data.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {

        $data = new \stdClass();

        // Sample info (determined by the analyser).
        list($data->sampledescription, $sampleimage) = $this->model->prediction_sample_description($this->prediction);

        // Sampleimage is a renderable we should pass it to HTML.
        if ($sampleimage) {
            $data->sampleimage = $output->render($sampleimage);
        }

        // Prediction info.
        $predictedvalue = $this->prediction->get_prediction_data()->prediction;
        $predictionid = $this->prediction->get_prediction_data()->id;
        $data->predictiondisplayvalue = $this->model->get_target()->get_display_value($predictedvalue);
        $data->predictionstyle = $this->model->get_target()->get_value_style($predictedvalue);

        $actions = $this->model->get_target()->prediction_actions($this->prediction);
        if ($actions) {
            $actionsmenu = new \action_menu();
            $actionsmenu->set_menu_trigger(get_string('actions'));
            $actionsmenu->set_owner_selector('prediction-actions-' . $predictionid);
            $actionsmenu->set_alignment(\action_menu::TL, \action_menu::BL);

            // Add all actions defined by the target.
            foreach ($actions as $action) {
                $actionsmenu->add($action->get_action_link());
            }
            $data->actions = $actionsmenu->export_for_template($output);
        } else {
            $data->actions = false;
        }

        // Calculated indicators values.
        $data->calculations = array();
        $calculations = $this->prediction->get_calculations();
        foreach ($calculations as $calculation) {

            // Hook for indicators with extra features that should not be displayed (e.g. discrete indicators).
            if (!$calculation->indicator->should_be_displayed($calculation->value, $calculation->subtype)) {
                continue;
            }

            if ($calculation->value === null) {
                // We don't show values that could not be calculated.
                continue;
            }

            $obj = new \stdClass();
            $obj->name = forward_static_call(array($calculation->indicator, 'get_name'), $calculation->subtype);
            $obj->displayvalue = $calculation->indicator->get_display_value($calculation->value, $calculation->subtype);
            $obj->style = $calculation->indicator->get_value_style($calculation->value, $calculation->subtype);

            $data->calculations[] = $obj;
        }

        return $data;
    }
}
