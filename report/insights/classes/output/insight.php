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
 * Single insight view page.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_insights\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Single insight view page.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class insight implements \renderable, \templatable {

    /**
     * @var \core_analytics\model
     */
    protected $model;

    /**
     * @var \core_analytics\prediction
     */
    protected $prediction;

    /**
     * @var bool
     */
    protected $includedetailsaction = false;

    /**
     * Constructor
     *
     * @param \core_analytics\prediction $prediction
     * @param \core_analytics\model $model
     * @param bool $includedetailsaction
     * @return void
     */
    public function __construct(\core_analytics\prediction $prediction, \core_analytics\model $model, $includedetailsaction = false) {
        $this->prediction = $prediction;
        $this->model = $model;
        $this->includedetailsaction = $includedetailsaction;
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
        list($data->sampledescription, $samplerenderable) = $this->model->prediction_sample_description($this->prediction);

        // Sampleimage is a renderable we should pass it to HTML.
        if ($samplerenderable) {
            $data->samplelink = $output->render($samplerenderable);
        }

        // Prediction info.
        $predictedvalue = $this->prediction->get_prediction_data()->prediction;
        $predictionid = $this->prediction->get_prediction_data()->id;
        $data->predictiondisplayvalue = $this->model->get_target()->get_display_value($predictedvalue);
        $data->predictionstyle = $this->get_calculation_style($this->model->get_target(), $predictedvalue);

        $actions = $this->model->get_target()->prediction_actions($this->prediction, $this->includedetailsaction);
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
            $obj->name = call_user_func(array($calculation->indicator, 'get_name'));
            $obj->displayvalue = $calculation->indicator->get_display_value($calculation->value, $calculation->subtype);
            $obj->style = $this->get_calculation_style($calculation->indicator, $calculation->value, $calculation->subtype);

            $data->calculations[] = $obj;
        }

        return $data;
    }

    /**
     * Returns a CSS class from the calculated value outcome.
     *
     * @param \core_analytics\calculable $calculable
     * @param float $value
     * @param string|false $subtype
     * @return string
     */
    protected function get_calculation_style(\core_analytics\calculable $calculable, $value, $subtype = false) {
        $outcome = $calculable->get_calculation_outcome($value, $subtype);
        switch ($outcome) {
            case \core_analytics\calculable::OUTCOME_NEUTRAL:
                $style = '';
                break;
            case \core_analytics\calculable::OUTCOME_VERY_POSITIVE:
                $style = 'alert alert-success';
                break;
            case \core_analytics\calculable::OUTCOME_OK:
                $style = 'alert alert-info';
                break;
            case \core_analytics\calculable::OUTCOME_NEGATIVE:
                $style = 'alert alert-warning';
                break;
            case \core_analytics\calculable::OUTCOME_VERY_NEGATIVE:
                $style = 'alert alert-danger';
                break;
            default:
                throw new \coding_exception('The outcome returned by ' . get_class($calculable) . '::get_calculation_outcome is ' .
                    'not one of the accepted values. Please use \core_analytics\calculable::OUTCOME_VERY_POSITIVE, ' .
                    '\core_analytics\calculable::OUTCOME_OK, \core_analytics\calculable::OUTCOME_NEGATIVE, ' .
                    '\core_analytics\calculable::OUTCOME_VERY_NEGATIVE or \core_analytics\calculable::OUTCOME_NEUTRAL');
        }
        return $style;
    }
}
