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
 * Prediction models list page.
 *
 * @package    tool_analytics
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Shows tool_analytics models list.
 *
 * @package    tool_analytics
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class models_list implements \renderable, \templatable {

    /**
     * models
     *
     * @var \core_analytics\model[]
     */
    protected $models = array();

    /**
     * __construct
     *
     * @param \core_analytics\model[] $models
     * @return void
     */
    public function __construct($models) {
        $this->models = $models;
    }

    /**
     * Exports the data.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {

        $data = new \stdClass();

        $data->models = array();
        foreach ($this->models as $model) {
            $modeldata = $model->export();

            // Check if there is a help icon for the target to show.
            $identifier = $modeldata->target->get_identifier();
            $component = $modeldata->target->get_component();
            if (get_string_manager()->string_exists($identifier . '_help', $component)) {
                $helpicon = new \help_icon($identifier, $component);
                $modeldata->targethelp = $helpicon->export_for_template($output);
            } else {
                // We really want to encourage developers to add help to their targets.
                debugging("The target '{$modeldata->target}' should include a '{$identifier}_help' string to
                    describe its purpose.", DEBUG_DEVELOPER);
            }

            // Check if there is a help icon for the indicators to show.
            if (!empty($modeldata->indicators)) {
                $indicators = array();
                foreach ($modeldata->indicators as $ind) {
                    // Create the indicator with the details we want for the context.
                    $indicator = new \stdClass();
                    $indicator->name = $ind->out();
                    $identifier = $ind->get_identifier();
                    $component = $ind->get_component();
                    if (get_string_manager()->string_exists($identifier . '_help', $component)) {
                        $helpicon = new \help_icon($identifier, $component);
                        $indicator->help = $helpicon->export_for_template($output);
                    } else {
                        // We really want to encourage developers to add help to their indicators.
                        debugging("The indicator '{$ind}' should include a '{$identifier}_help' string to
                            describe its purpose.", DEBUG_DEVELOPER);
                    }
                    $indicators[] = $indicator;
                }
                $modeldata->indicators = $indicators;
            }

            // Check if there is a help icon for the time splitting method.
            if (!empty($modeldata->timesplitting)) {
                $identifier = $modeldata->timesplitting->get_identifier();
                $component = $modeldata->timesplitting->get_component();
                if (get_string_manager()->string_exists($identifier . '_help', $component)) {
                    $helpicon = new \help_icon($identifier, $component);
                    $modeldata->timesplittinghelp = $helpicon->export_for_template($output);
                } else {
                    // We really want to encourage developers to add help to their time splitting methods.
                    debugging("The time splitting method '{$modeldata->timesplitting}' should include a '{$identifier}_help'
                        string to describe its purpose.", DEBUG_DEVELOPER);
                }
            }

            // Model predictions list.
            if ($model->uses_insights()) {
                $predictioncontexts = $model->get_predictions_contexts();
                if ($predictioncontexts) {

                    foreach ($predictioncontexts as $contextid => $unused) {
                        // We prepare this to be used as single_select template options.
                        $context = \context::instance_by_id($contextid);

                        // Special name for system level predictions as showing "System is not visually nice".
                        if ($contextid == SYSCONTEXTID) {
                            $contextname = get_string('allpredictions', 'tool_analytics');
                        } else {
                            $contextname = shorten_text($context->get_context_name(true, true), 90);
                        }
                        $predictioncontexts[$contextid] = $contextname;
                    }
                    \core_collator::asort($predictioncontexts);

                    if (!empty($predictioncontexts)) {
                        $url = new \moodle_url('/report/insights/insights.php', array('modelid' => $model->get_id()));
                        $singleselect = new \single_select($url, 'contextid', $predictioncontexts);
                        $modeldata->insights = $singleselect->export_for_template($output);
                    }
                }

                if (empty($modeldata->insights)) {
                    if ($model->any_prediction_obtained()) {
                        $modeldata->noinsights = get_string('noinsights', 'analytics');
                    } else {
                        $modeldata->noinsights = get_string('nopredictionsyet', 'analytics');
                    }
                }

            } else {
                $modeldata->noinsights = get_string('noinsightsmodel', 'analytics');
            }

            // Actions.
            $actionsmenu = new \action_menu();
            $actionsmenu->set_menu_trigger(get_string('actions'));
            $actionsmenu->set_owner_selector('model-actions-' . $model->get_id());
            $actionsmenu->set_alignment(\action_menu::TL, \action_menu::BL);

            // Edit model.
            if (!$model->is_static()) {
                $url = new \moodle_url('model.php', array('action' => 'edit', 'id' => $model->get_id()));
                $icon = new \action_menu_link_secondary($url, new \pix_icon('t/edit', get_string('edit')), get_string('edit'));
                $actionsmenu->add($icon);
            }

            // Enable / disable.
            if ($model->is_enabled()) {
                $action = 'disable';
                $text = get_string('disable');
                $icontype = 't/block';
            } else {
                $action = 'enable';
                $text = get_string('enable');
                $icontype = 'i/checked';
            }
            $url = new \moodle_url('model.php', array('action' => $action, 'id' => $model->get_id()));
            $icon = new \action_menu_link_secondary($url, new \pix_icon($icontype, $text), $text);
            $actionsmenu->add($icon);

            // Evaluate machine-learning-based models.
            if ($model->get_indicators() && !$model->is_static()) {
                $url = new \moodle_url('model.php', array('action' => 'evaluate', 'id' => $model->get_id()));
                $icon = new \action_menu_link_secondary($url, new \pix_icon('i/calc', get_string('evaluate', 'tool_analytics')),
                    get_string('evaluate', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Get predictions.
            if ($modeldata->enabled && !empty($modeldata->timesplitting)) {
                $url = new \moodle_url('model.php', array('action' => 'getpredictions', 'id' => $model->get_id()));
                $icon = new \action_menu_link_secondary($url, new \pix_icon('i/notifications',
                    get_string('getpredictions', 'tool_analytics')), get_string('getpredictions', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Machine-learning-based models evaluation log.
            if (!$model->is_static()) {
                $url = new \moodle_url('model.php', array('action' => 'log', 'id' => $model->get_id()));
                $icon = new \action_menu_link_secondary($url, new \pix_icon('i/report', get_string('viewlog', 'tool_analytics')),
                    get_string('viewlog', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Export training data.
            if (!$model->is_static() && $model->is_trained()) {
                $url = new \moodle_url('model.php', array('action' => 'export', 'id' => $model->get_id()));
                $icon = new \action_menu_link_secondary($url, new \pix_icon('i/export',
                    get_string('exporttrainingdata', 'tool_analytics')), get_string('export', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            $modeldata->actions = $actionsmenu->export_for_template($output);

            $data->models[] = $modeldata;
        }

        $data->warnings = array(
            (object)array('message' => get_string('bettercli', 'tool_analytics'), 'closebutton' => true)
        );

        return $data;
    }
}
