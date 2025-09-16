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
 * Adds settings links to admin tree.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig && \core_analytics\manager::is_analytics_enabled()) {

    $settings = new admin_settingpage('analyticssite', new lang_string('analyticssiteinfo', 'analytics'));
    $ADMIN->add('analytics', $settings);

    if ($ADMIN->fulltree) {
        $modeinstructions = [
            'facetoface' => get_string('modeinstructionfacetoface', 'analytics'),
            'blendedhybrid' => get_string('modeinstructionblendedhybrid', 'analytics'),
            'fullyonline' => get_string('modeinstructionfullyonline', 'analytics'),
        ];
        $settings->add(new admin_setting_configmultiselect('analytics/modeinstruction', get_string('modeinstruction', 'analytics'),
            '', [], $modeinstructions));

        $settings->add(new admin_setting_configtext_with_maxlength('analytics/percentonline',
            get_string('percentonline', 'analytics'),
            get_string('percentonline_help', 'analytics'), '', PARAM_INT, 3, 3));

        $typeinstitutions = [
            'typeinstitutionacademic' => get_string('typeinstitutionacademic', 'analytics'),
            'typeinstitutiontraining' => get_string('typeinstitutiontraining', 'analytics'),
            'typeinstitutionngo' => get_string('typeinstitutionngo', 'analytics'),
        ];
        $settings->add(new admin_setting_configmultiselect('analytics/typeinstitution', get_string('typeinstitution', 'analytics'),
            '', [], $typeinstitutions));

        $levelinstitutions = [
            'levelinstitutionisced0' => get_string('levelinstitutionisced0', 'analytics'),
            'levelinstitutionisced1' => get_string('levelinstitutionisced1', 'analytics'),
            'levelinstitutionisced2' => get_string('levelinstitutionisced2', 'analytics'),
            'levelinstitutionisced3' => get_string('levelinstitutionisced3', 'analytics'),
            'levelinstitutionisced4' => get_string('levelinstitutionisced4', 'analytics'),
            'levelinstitutionisced5' => get_string('levelinstitutionisced5', 'analytics'),
            'levelinstitutionisced6' => get_string('levelinstitutionisced6', 'analytics'),
            'levelinstitutionisced7' => get_string('levelinstitutionisced7', 'analytics'),
            'levelinstitutionisced8' => get_string('levelinstitutionisced8', 'analytics'),
        ];
        $settings->add(new admin_setting_configmultiselect('analytics/levelinstitution',
            get_string('levelinstitution', 'analytics'), '', [], $levelinstitutions));
    }

    $settings = new admin_settingpage('analyticssettings', new lang_string('analyticssettings', 'analytics'));
    $ADMIN->add('analytics', $settings);

    if ($ADMIN->fulltree) {
        // Select the site prediction's processor.
        $predictionprocessors = \core_analytics\manager::get_all_prediction_processors();
        $predictors = [];
        foreach ($predictionprocessors as $fullclassname => $predictor) {
            $pluginname = substr($fullclassname, 1, strpos($fullclassname, '\\', 1) - 1);
            $predictors[$fullclassname] = new lang_string('pluginname', $pluginname);
        }
        $settings->add(
            new \core_analytics\admin_setting_predictor(
                'analytics/predictionsprocessor',
                new lang_string('defaultpredictionsprocessor', 'analytics'),
                new lang_string('predictionsprocessor_help', 'analytics'),
                \core_analytics\manager::default_mlbackend(),
                $predictors,
            )
        );
        // Warn if current processor is not configured.
        // We are avoiding doing this check in write_config because it is likely the default
        // mlbackend_python plugin is not configured and will output warnings during install.
        $currentprocessor = get_config('analytics', 'predictionsprocessor');
        if (!empty($currentprocessor)) {
            $currentprocessor = new $currentprocessor;
            $currentprocessorisready = $currentprocessor->is_ready();
            if ($currentprocessorisready !== true) {
                $settings->add(new admin_setting_description(
                    'processornotready',
                    '',
                    html_writer::tag('div', $currentprocessorisready, ['class' => 'alert alert-danger'])
                ));
            }
        }

        // Log store.
        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers('core\log\sql_reader');
        $options = array();
        $defaultreader = null;
        foreach ($readers as $plugin => $reader) {
            if (!$reader->is_logging()) {
                continue;
            }
            if (!isset($defaultreader)) {
                // The top one as default reader.
                $defaultreader = $plugin;
            }
            $options[$plugin] = $reader->get_name();
        }

        if (empty($defaultreader)) {
            // We fall here during initial site installation because log stores are not
            // enabled until admin/tool/log/db/install.php is executed and get_readers
            // return nothing.

            if ($enabledlogstores = get_config('tool_log', 'enabled_stores')) {
                $enabledlogstores = explode(',', $enabledlogstores);
                $defaultreader = reset($enabledlogstores);

                // No need to set the correct name, just the value, this will not be displayed.
                $options[$defaultreader] = $defaultreader;
            }
        }
        $settings->add(new admin_setting_configselect('analytics/logstore',
            new lang_string('analyticslogstore', 'analytics'), new lang_string('analyticslogstore_help', 'analytics'),
            $defaultreader, $options));

        // Enable/disable time splitting methods.
        $alltimesplittings = \core_analytics\manager::get_time_splitting_methods_for_evaluation(true);

        $timesplittingoptions = array();
        $timesplittingdefaults = array('\core\analytics\time_splitting\quarters_accum',
            '\core\analytics\time_splitting\quarters', '\core\analytics\time_splitting\single_range');
        foreach ($alltimesplittings as $key => $timesplitting) {
            $timesplittingoptions[$key] = $timesplitting->get_name();
        }
        $settings->add(new admin_setting_configmultiselect('analytics/defaulttimesplittingsevaluation',
            new lang_string('defaulttimesplittingmethods', 'analytics'),
            new lang_string('defaulttimesplittingmethods_help', 'analytics'),
            $timesplittingdefaults, $timesplittingoptions)
        );

        // Predictions processor output dir - specify default in setting description (used if left blank).
        $defaultmodeloutputdir = \core_analytics\model::default_output_dir();
        $settings->add(new admin_setting_configdirectory('analytics/modeloutputdir', new lang_string('modeloutputdir', 'analytics'),
            new lang_string('modeloutputdirwithdefaultinfo', 'analytics', $defaultmodeloutputdir), ''));

        // Disable web interface evaluation and get predictions.
        $settings->add(new admin_setting_configcheckbox('analytics/onlycli', new lang_string('onlycli', 'analytics'),
            new lang_string('onlycliinfo', 'analytics'), 1));

        // Training and prediction time limit per model.
        $settings->add(new admin_setting_configduration('analytics/modeltimelimit', new lang_string('modeltimelimit', 'analytics'),
            new lang_string('modeltimelimitinfo', 'analytics'), 20 * MINSECS));

        $options = array(
            0    => new lang_string('neverdelete', 'analytics'),
            1000 => new lang_string('numdays', '', 1000),
            365  => new lang_string('numdays', '', 365),
            180  => new lang_string('numdays', '', 180),
            150  => new lang_string('numdays', '', 150),
            120  => new lang_string('numdays', '', 120),
            90   => new lang_string('numdays', '', 90),
            60   => new lang_string('numdays', '', 60),
            35   => new lang_string('numdays', '', 35));
        $settings->add(new admin_setting_configselect('analytics/calclifetime',
            new lang_string('calclifetime', 'analytics'),
            new lang_string('configlcalclifetime', 'analytics'), 35, $options));


    }
}
