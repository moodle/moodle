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

if ($hassiteconfig) {
    $settings = new admin_settingpage('analyticssettings', new lang_string('analyticssettings', 'analytics'));
    $ADMIN->add('analytics', $settings);

    if ($ADMIN->fulltree) {
        // Select the site prediction's processor.
        $predictionprocessors = \core_analytics\manager::get_all_prediction_processors();
        $predictors = array();
        foreach ($predictionprocessors as $fullclassname => $predictor) {
            $pluginname = substr($fullclassname, 1, strpos($fullclassname, '\\', 1) - 1);
            $predictors[$fullclassname] = new lang_string('pluginname', $pluginname);
        }
        $settings->add(new \core_analytics\admin_setting_predictor('analytics/predictionsprocessor',
            new lang_string('defaultpredictionsprocessor', 'analytics'), new lang_string('predictionsprocessor_help', 'analytics'),
            \core_analytics\manager::default_mlbackend(), $predictors)
        );

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
        $alltimesplittings = \core_analytics\manager::get_all_time_splittings();

        $timesplittingoptions = array();
        $timesplittingdefaults = array('\core\analytics\time_splitting\quarters_accum',
            '\core\analytics\time_splitting\quarters', '\core\analytics\time_splitting\single_range');
        foreach ($alltimesplittings as $key => $timesplitting) {
            $timesplittingoptions[$key] = $timesplitting->get_name();
        }
        $settings->add(new admin_setting_configmultiselect('analytics/timesplittings',
            new lang_string('enabledtimesplittings', 'analytics'), new lang_string('timesplittingmethod_help', 'analytics'),
            $timesplittingdefaults, $timesplittingoptions)
        );

        // Predictions processor output dir.
        $defaultmodeloutputdir = rtrim($CFG->dataroot, '/') . DIRECTORY_SEPARATOR . 'models';
        if (empty(get_config('analytics', 'modeloutputdir')) && !file_exists($defaultmodeloutputdir) &&
                is_writable($defaultmodeloutputdir)) {
            // Automatically create the dir for them so users don't see the invalid value red cross.
            mkdir($defaultmodeloutputdir, $CFG->directorypermissions, true);
        }
        $settings->add(new admin_setting_configdirectory('analytics/modeloutputdir', new lang_string('modeloutputdir', 'analytics'),
            new lang_string('modeloutputdirinfo', 'analytics'), $defaultmodeloutputdir));

        // Disable web interface evaluation and get predictions.
        $settings->add(new admin_setting_configcheckbox('analytics/onlycli', new lang_string('onlycli', 'analytics'),
            new lang_string('onlycliinfo', 'analytics'), 1));

        // Training and prediction time limit per model.
        $settings->add(new admin_setting_configduration('analytics/modeltimelimit', new lang_string('modeltimelimit', 'analytics'),
            new lang_string('modeltimelimitinfo', 'analytics'), 20 * MINSECS));

    }
}
