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
        global $PAGE;

        $data = new \stdClass();

        $newmodelmenu = new \action_menu();
        $newmodelmenu->set_menu_trigger(get_string('newmodel', 'tool_analytics'), 'btn btn-secondary');
        $newmodelmenu->set_alignment(\action_menu::TL, \action_menu::BL);

        $newmodelmenu->add(new \action_menu_link(
            new \moodle_url('/admin/tool/analytics/createmodel.php'),
            new \pix_icon('i/edit', ''),
            get_string('createmodel', 'tool_analytics'),
            false
        ));

        $newmodelmenu->add(new \action_menu_link(
            new \moodle_url('/admin/tool/analytics/importmodel.php'),
            new \pix_icon('i/import', ''),
            get_string('importmodel', 'tool_analytics'),
            false
        ));

        $newmodelmenu->add(new \action_menu_link(
            new \moodle_url('/admin/tool/analytics/restoredefault.php'),
            new \pix_icon('i/reload', ''),
            get_string('restoredefault', 'tool_analytics'),
            false
        ));

        $data->newmodelmenu = $newmodelmenu->export_for_template($output);

        $onlycli = get_config('analytics', 'onlycli');
        if ($onlycli === false) {
            // Default applied if no config found.
            $onlycli = 1;
        }

        // Evaluation options.
        $timesplittingsforevaluation = \core_analytics\manager::get_time_splitting_methods_for_evaluation(true);

        $misconfiguredmodels = [];
        $data->models = array();
        foreach ($this->models as $model) {
            $modeldata = $model->export($output);

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

            if ($model->invalid_timesplitting_selected()) {
                $misconfiguredmodels[$model->get_id()] = $model->get_name();
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

            $modeldata->indicatorsnum = count($modeldata->indicators);

            // Check if there is a help icon for the time splitting method.
            if (!empty($modeldata->timesplitting)) {
                $identifier = $modeldata->timesplitting->get_identifier();
                $component = $modeldata->timesplitting->get_component();
                if (get_string_manager()->string_exists($identifier . '_help', $component)) {
                    $helpicon = new \help_icon($identifier, $component);
                    $modeldata->timesplittinghelp = $helpicon->export_for_template($output);
                } else {
                    // We really want to encourage developers to add help to their time splitting methods.
                    debugging("The analysis interval '{$modeldata->timesplitting}' should include a '{$identifier}_help'
                        string to describe its purpose.", DEBUG_DEVELOPER);
                }
            } else {
                $helpicon = new \help_icon('timesplittingnotdefined', 'tool_analytics');
                $modeldata->timesplittinghelp = $helpicon->export_for_template($output);
            }

            // Has this model generated predictions?.
            $predictioncontexts = $model->get_predictions_contexts();
            $anypredictionobtained = $model->any_prediction_obtained();

            // Model predictions list.
            if (!$model->is_enabled()) {
                $modeldata->noinsights = get_string('disabledmodel', 'analytics');
            } else if ($model->uses_insights()) {
                if ($predictioncontexts) {
                    $url = new \moodle_url('/report/insights/insights.php', array('modelid' => $model->get_id()));
                    $modeldata->insights = \tool_analytics\output\helper::prediction_context_selector($predictioncontexts,
                        $url, $output);
                }

                if (empty($modeldata->insights)) {
                    if ($anypredictionobtained) {
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

            $urlparams = ['id' => $model->get_id(), 'sesskey' => sesskey()];

            // Get predictions.
            if (!$onlycli && $modeldata->enabled && !empty($modeldata->timesplitting)) {
                $urlparams['action'] = 'scheduledanalysis';
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $icon = new \action_menu_link_secondary($url,
                    new \pix_icon('i/notifications', get_string('executescheduledanalysis', 'tool_analytics')),
                    get_string('executescheduledanalysis', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Evaluate machine-learning-based models.
            if (!$onlycli && $model->get_indicators() && !$model->is_static()) {

                // Extra is_trained call as trained_locally returns false if the model has not been trained yet.
                $trainedonlyexternally = !$model->trained_locally() && $model->is_trained();

                $actionid = 'evaluate-' . $model->get_id();

                // Evaluation options.
                $modeltimesplittingmethods = $this->timesplittings_options_for_evaluation($model, $timesplittingsforevaluation);

                // Include the current time-splitting method as the default selection method the model already have one.
                if ($model->get_model_obj()->timesplitting) {
                    $currenttimesplitting = ['id' => 'current', 'text' => get_string('currenttimesplitting', 'tool_analytics')];
                    array_unshift($modeltimesplittingmethods, $currenttimesplitting);
                }

                $evaluateparams = [$actionid, $trainedonlyexternally];
                $PAGE->requires->js_call_amd('tool_analytics/model', 'selectEvaluationOptions', $evaluateparams);
                $urlparams['action'] = 'evaluate';
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $icon = new \action_menu_link_secondary($url, new \pix_icon('i/calc', get_string('evaluate', 'tool_analytics')),
                    get_string('evaluate', 'tool_analytics'), ['data-action-id' => $actionid,
                    'data-timesplitting-methods' => json_encode($modeltimesplittingmethods)]);
                $actionsmenu->add($icon);
            }

            // Machine-learning-based models evaluation log.
            if (!$model->is_static() && $model->get_logs()) {
                $urlparams['action'] = 'log';
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $icon = new \action_menu_link_secondary($url, new \pix_icon('i/report', get_string('viewlog', 'tool_analytics')),
                    get_string('viewlog', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Edit model.
            $urlparams['action'] = 'edit';
            $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
            $icon = new \action_menu_link_secondary($url, new \pix_icon('t/edit', get_string('edit')), get_string('edit'));
            $actionsmenu->add($icon);

            // Enable / disable.
            if ($model->is_enabled() || !empty($modeldata->timesplitting)) {
                // If there is no timesplitting method set, the model can not be enabled.
                if ($model->is_enabled()) {
                    $action = 'disable';
                    $text = get_string('disable');
                    $icontype = 't/block';
                } else {
                    $action = 'enable';
                    $text = get_string('enable');
                    $icontype = 'i/checked';
                }
                $urlparams['action'] = $action;
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $icon = new \action_menu_link_secondary($url, new \pix_icon($icontype, $text), $text);
                $actionsmenu->add($icon);
            }

            // Export.
            if (!$model->is_static()) {

                $fullysetup = $model->get_indicators() && !empty($modeldata->timesplitting);
                $istrained = $model->is_trained();

                if ($fullysetup || $istrained) {

                    $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                    // Clear the previous action param from the URL, we will set it in JS.
                    $url->remove_params('action');

                    $actionid = 'export-' . $model->get_id();
                    $PAGE->requires->js_call_amd('tool_analytics/model', 'selectExportOptions',
                        [$actionid, $istrained]);

                    $icon = new \action_menu_link_secondary($url, new \pix_icon('i/export',
                        get_string('export', 'tool_analytics')), get_string('export', 'tool_analytics'),
                        ['data-action-id' => $actionid]);
                    $actionsmenu->add($icon);
                }
            }

            // Insights report.
            if (!empty($anypredictionobtained) && $model->uses_insights()) {
                $urlparams['action'] = 'insightsreport';
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $pix = new \pix_icon('i/report', get_string('insightsreport', 'tool_analytics'));
                $icon = new \action_menu_link_secondary($url, $pix, get_string('insightsreport', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Invalid analysables.
            $analyser = $model->get_analyser(['notimesplitting' => true]);
            if (!$analyser instanceof \core_analytics\local\analyser\sitewide) {
                $urlparams['action'] = 'invalidanalysables';
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $pix = new \pix_icon('i/report', get_string('invalidanalysables', 'tool_analytics'));
                $icon = new \action_menu_link_secondary($url, $pix, get_string('invalidanalysables', 'tool_analytics'));
                $actionsmenu->add($icon);
            }

            // Clear model.
            if (!empty($anypredictionobtained) || $model->is_trained()) {
                $actionid = 'clear-' . $model->get_id();
                $PAGE->requires->js_call_amd('tool_analytics/model', 'confirmAction', [$actionid, 'clear']);
                $urlparams['action'] = 'clear';
                $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
                $icon = new \action_menu_link_secondary($url, new \pix_icon('e/cleanup_messy_code',
                    get_string('clearpredictions', 'tool_analytics')), get_string('clearpredictions', 'tool_analytics'),
                    ['data-action-id' => $actionid]);
                $actionsmenu->add($icon);
            }

            // Delete model.
            $actionid = 'delete-' . $model->get_id();
            $PAGE->requires->js_call_amd('tool_analytics/model', 'confirmAction', [$actionid, 'delete']);
            $urlparams['action'] = 'delete';
            $url = new \moodle_url('/admin/tool/analytics/model.php', $urlparams);
            $icon = new \action_menu_link_secondary($url, new \pix_icon('t/delete',
                get_string('delete', 'tool_analytics')), get_string('delete', 'tool_analytics'),
                ['data-action-id' => $actionid]);
            $actionsmenu->add($icon);

            $modeldata->actions = $actionsmenu->export_for_template($output);

            $data->models[] = $modeldata;
        }

        $data->warnings = [];
        $data->infos = [];
        if (!$onlycli) {
            $data->warnings[] = (object)array('message' => get_string('bettercli', 'tool_analytics'), 'closebutton' => true);
        } else {
            $url = new \moodle_url('/admin/settings.php', array('section' => 'analyticssettings'),
                'id_s_analytics_onlycli');

            $langstrid = 'clievaluationandpredictionsnoadmin';
            if (is_siteadmin()) {
                $langstrid = 'clievaluationandpredictions';
            }
            $data->infos[] = (object)array('message' => get_string($langstrid, 'tool_analytics', $url->out()),
                'closebutton' => true);
        }

        if ($misconfiguredmodels) {
            $warningstr = get_string('invalidtimesplittinginmodels', 'tool_analytics', implode(', ', $misconfiguredmodels));
            $data->warnings[] = (object)array('message' => $warningstr, 'closebutton' => true);
        }

        return $data;
    }

    /**
     * Returns the list of time splitting methods that are available for evaluation.
     *
     * @param  \core_analytics\model $model
     * @param  array                 $timesplittingsforevaluation
     * @return array
     */
    private function timesplittings_options_for_evaluation(\core_analytics\model $model,
            array $timesplittingsforevaluation): array {

        $modeltimesplittingmethods = [
            ['id' => 'all', 'text' => get_string('alltimesplittingmethods', 'tool_analytics')],
        ];
        $potentialtimesplittingmethods = $model->get_potential_timesplittings();
        foreach ($timesplittingsforevaluation as $timesplitting) {
            if (empty($potentialtimesplittingmethods[$timesplitting->get_id()])) {
                // This time-splitting method can not be used for this model.
                continue;
            }
            $modeltimesplittingmethods[] = [
                'id' => \tool_analytics\output\helper::class_to_option($timesplitting->get_id()),
                'text' => $timesplitting->get_name()->out(),
            ];
        }

        return $modeltimesplittingmethods;
    }
}
