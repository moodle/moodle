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
    $ADMIN->add('appearance', $settings);

    if ($ADMIN->fulltree) {
        // Select the site prediction's processor.
        $predictionprocessors = \core_analytics\manager::get_all_prediction_processors();
        $predictors = array();
        foreach ($predictionprocessors as $fullclassname => $predictor) {
            $pluginname = substr($fullclassname, 1, strpos($fullclassname, '\\', 1) - 1);
            $predictors[$fullclassname] = new lang_string('pluginname', $pluginname);
        }
        $settings->add(new \core_analytics\admin_setting_predictor('analytics/predictionsprocessor',
            new lang_string('predictionsprocessor', 'analytics'), new lang_string('predictionsprocessor_help', 'analytics'),
            '\mlbackend_php\processor', $predictors)
        );

        // Enable/disable time splitting methods.
        $alltimesplittings = \core_analytics\manager::get_all_time_splittings();

        $timesplittingoptions = array();
        $timesplittingdefaults = array('\\core_analytics\\local\\time_splitting\\quarters_accum',
            '\\core_analytics\\local\\time_splitting\\quarters');
        foreach ($alltimesplittings as $key => $timesplitting) {
            $timesplittingoptions[$key] = $timesplitting->get_name();
        }
        $settings->add(new admin_setting_configmultiselect('analytics/timesplittings',
            new lang_string('enabledtimesplittings', 'analytics'), new lang_string('enabledtimesplittings_help', 'analytics'),
            $timesplittingdefaults, $timesplittingoptions)
        );

        // Predictions processor output dir.
        $defaultmodeloutputdir = rtrim($CFG->dataroot, '/') . DIRECTORY_SEPARATOR . 'models';
        $settings->add(new admin_setting_configdirectory('analytics/modeloutputdir', new lang_string('modeloutputdir', 'analytics'),
            new lang_string('modeloutputdirinfo', 'analytics'), $defaultmodeloutputdir));
        $studentdefaultroles = [];
        $teacherdefaultroles = [];

        // Student and teacher roles.
        $allroles = role_fix_names(get_all_roles());
        $rolechoices = [];
        foreach ($allroles as $role) {
            $rolechoices[$role->id] = $role->localname;

            if ($role->shortname == 'student') {
                $studentdefaultroles[] = $role->id;
            } else if ($role->shortname == 'teacher') {
                $teacherdefaultroles[] = $role->id;
            } else if ($role->shortname == 'editingteacher') {
                $teacherdefaultroles[] = $role->id;
            }
        }

        $settings->add(new admin_setting_configmultiselect('analytics/teacherroles', new lang_string('teacherroles', 'analytics'),
           '', $teacherdefaultroles, $rolechoices));

        $settings->add(new admin_setting_configmultiselect('analytics/studentroles', new lang_string('studentroles', 'analytics'),
           '', $studentdefaultroles, $rolechoices));

    }
}
