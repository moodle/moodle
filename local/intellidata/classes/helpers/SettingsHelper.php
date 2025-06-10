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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

use local_intellidata\repositories\tracking\tracking_repository;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class SettingsHelper {

    /**
     * Default Values.
     */
    const DEFAULT_VALUES = [
        // General settings.
        'enabled' => 1,
        'ispluginsetup' => 0,
        'migrationcallbackurl' => '',
        'trackingstorage' => StorageHelper::FILE_STORAGE,
        'encryptionkey' => '',
        'clientidentifier' => '',
        'cleaner_duration' => DAYSECS * 14,
        'migrationrecordslimit' => '1000000',
        'migrationwriterecordslimit' => '10000',
        'exportrecordslimit' => '100000',
        'exportfilesduringmigration' => 1,
        'resetmigrationprogress' => 0,
        'resetimporttrackingprogress' => 0,
        'tracklogsdatatypes' => 0,
        'exportdataformat' => 'csv',
        'defaultlayout' => 'base',
        // User Tracking.
        'enabledtracking' => 1,
        'compresstracking' => tracking_repository::TYPE_CACHE,
        'tracklogs' => 1,
        'trackdetails' => 1,
        'inactivity' => '60',
        'ajaxfrequency' => '30',
        'trackadmin' => 0,
        'trackmedia' => 0,
        // IB Next LTI.
        'ltitoolurl' => '',
        'lticonsumerkey' => '',
        'ltisharedsecret' => '',
        'ltititle' => '',
        'custommenuitem' => 0,
        'ltiassigndefaultmethod' => 0,
        'ibnltirole' => '',
        'debug' => 0,
        // Internal settings.
        'lastmigrationdate' => 0,
        'migrationstart' => 0,
        'migrationdatatype' => '',
        'lastexportdate' => 0,
        'exportdatatype' => '',
        'exportstart' => 0,
        // Advanced Settings.
        'enabledatavalidation' => 0,
        'enabledatacleaning' => 0,
        'enableprogresscalculation' => 0,
        'divideexportbydatatype' => 0,
        'dividemigrationtbydatatype' => 1,
        'enablescheduledsnapshot' => 0,
        'eventstracking' => 1,
        'newtracking' => 0,
        'exportids' => 1,
        'exportdeletedrecords' => self::EXPORTDELETED_TRACKEVENTS,
        'debugenabled' => 0,
        'datavalidationenabled' => 1,
        'intelliboardcopydatatype' => null,
        'intelliboardcopyprocessedlimit' => 0,
        'cacheconfig' => 1,
        'forcedisablemigration' => 0,
        'enablecustomdbdriver' => 0,
    ];

    /**
     * Not updatable settings.
     */
    const NOTUPDATABLE_SETTINGS = [
        'encryptionkey',
        'clientidentifier',
    ];

    /**
     * @var int Export deleted records disabled.
     */
    const EXPORTDELETED_DISABLED = 0;
    /**
     * @var int Export deleted records with moodle events tracking.
     */
    const EXPORTDELETED_TRACKEVENTS = 1;

    /**
     * Get config for export format.
     *
     * @param $datatype
     * @return database_storage_repository|file_storage_repository
     * @throws \dml_exception
     */
    public static function get_export_dataformat() {
        return self::get_setting('exportdataformat');
    }

    /**
     * Get default value for config.
     *
     * @param $configname
     * @return mixed|string
     */
    public static function get_defaut_config_value($configname) {
        $defaultvalues = self::DEFAULT_VALUES;
        $defaultvalues['ltititle'] = get_string('ltititlefield', 'local_intellidata');
        return isset($defaultvalues[$configname]) ? $defaultvalues[$configname] : '';
    }

    /**
     * Get config value.
     *
     * @param $configname
     * @return false|mixed|object|string
     * @throws \dml_exception
     */
    public static function get_setting($configname) {
        $config = get_config(ParamsHelper::PLUGIN, $configname);

        // Config did not set or doesn't exist.
        if ($config === null || $config === false) {
            return self::get_defaut_config_value($configname);
        }

        return $config;
    }

    /**
     * Set config value.
     *
     * @param $configname
     * @param $configvalue
     * @return void
     */
    public static function set_setting($configname, $configvalue) {
        set_config($configname, $configvalue, ParamsHelper::PLUGIN);
    }

    /**
     * Get lti title.
     *
     * @return false|\lang_string|mixed|object|string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_lti_title() {
        if ($config = self::get_setting('ltititle')) {
            return $config;
        }

        return get_string('ltimenutitle', ParamsHelper::PLUGIN);
    }

    /**
     * Get layouts options.
     *
     * @return string[]
     */
    public static function get_layouts_options() {
        global $PAGE;

        $options = ['standard' => 'standard'];

        if (!empty($PAGE->theme->layouts)) {
            foreach (array_keys($PAGE->theme->layouts) as $layout) {
                $options[$layout] = $layout;
            }
        }

        return $options;
    }

    /**
     * Get roles for options.
     *
     * @return array
     */
    public static function get_roles_options() {
        $options = ['' => get_string('notselected', ParamsHelper::PLUGIN)];

        if ($roles = get_roles_with_capability('local/intellidata:viewlti', CAP_ALLOW)) {
            foreach ($roles as $role) {
                $options[$role->id] = !empty($role->name) ? $role->name : ucfirst($role->shortname);
            }
        }

        return $options;
    }

    /**
     * Return options for export deleted records.
     *
     * @return array
     * @throws \coding_exception
     */
    public static function get_exportdeletedrecords_options() {
        return [
            self::EXPORTDELETED_DISABLED => get_string('disabled', ParamsHelper::PLUGIN),
            self::EXPORTDELETED_TRACKEVENTS => get_string('trackevents', ParamsHelper::PLUGIN),
        ];
    }

    /**
     * Get page layout.
     *
     * @return string
     * @throws \dml_exception
     */
    public static function get_page_layout() {

        $defaultlayout = self::get_setting('defaultlayout');

        $layoutoptions = self::get_layouts_options();

        if (isset($layoutoptions[$defaultlayout])) {
            return $layoutoptions[$defaultlayout];
        }

        return 'standard';
    }

    /**
     * Validate if plugin setup correctly with IB keys.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_plugin_setup() {

        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            return true;
        }

        return (!empty(self::get_setting('encryptionkey')) &&
                !empty(self::get_setting('clientidentifier')) &&
                !empty(self::get_setting('ispluginsetup'))) ? true : false;
    }

    /**
     * Get plugin settings list with values.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_plugin_settings($pluginame = 'local_intellidata', $pluginsetting = 'local_intellidata') {
        global $CFG, $DB;
        require_once($CFG->libdir.'/adminlib.php');

        $result = [];
        $settingsvalues = $DB->get_records_menu(
            'config_plugins', ['plugin' => $pluginame], '', 'name, value'
        );

        // Validate if settings exists.
        if (!count($settingsvalues)) {
            return $result;
        }

        $adminroot = admin_get_root();
        $settingspage = $adminroot->locate($pluginsetting, true);

        if (isset($settingspage->children)) {
            foreach ($settingspage->children as $childpage) {
                $result[] = self::prepare_settings_page($childpage, $settingsvalues);
            }
        } else if ($settingspage) {
            $result[] = self::prepare_settings_page($settingspage, $settingsvalues);
        }

        return $result;
    }

    /**
     * Return setting title.
     *
     * @param $setting
     * @return mixed|string
     */
    public static function get_setting_langname($visiblename) {
        return ($visiblename instanceof \lang_string)
            ? $visiblename->out()
            : $visiblename;
    }

    /**
     * Prepare readable settings list.
     *
     * @param $settingpage
     * @param $settingsvalues
     * @return array
     */
    public static function prepare_settings_page($settingpage, $settingsvalues) {
        global $CFG;
        require_once($CFG->libdir.'/adminlib.php');

        // Ignore if not accessable.
        if ($settingpage->is_hidden() || !$settingpage->check_access()) {
            return [];
        }

        $page = [
            'title' => self::get_setting_langname($settingpage->visiblename),
            'items' => [],
        ];

        if ($settingpage instanceof \admin_settingpage) {

            if (!empty($settingpage->settings)) {
                $group = [];

                foreach ($settingpage->settings as $setting) {

                    $title = self::get_setting_langname($setting->visiblename);

                    if ($setting instanceof \admin_setting_heading) {
                        if ($group) {
                            $page['items'][] = $group;
                        }
                        $group = [
                            'grouptitle' => $title,
                            'items' => [],
                        ];
                    } else {
                        if ($setting instanceof \admin_setting_configmultiselect) {
                            $selected = explode(
                                ',', $settingsvalues[$setting->name]
                            );

                            $selected = array_filter(
                                $setting->choices, function($key) use ($selected) {
                                    return in_array($key, $selected);
                                }, ARRAY_FILTER_USE_KEY
                            );

                            foreach ($selected as &$item) {
                                if ($item instanceof \lang_string) {
                                    $item = $item->out();
                                }
                            }
                            $value = implode(', ', $selected);
                            $subtype = 'multiselect';
                        } else if ($setting instanceof \admin_setting_configselect) {
                            $value = $setting->choices[$settingsvalues[$setting->name]];
                            $subtype = 'select';
                            if ($value instanceof \lang_string) {
                                $value = $value->out();
                            }
                        } else if ($setting instanceof \admin_setting_configcheckbox) {
                            $value = ($settingsvalues[$setting->name]) ? true : false;
                            $subtype = 'checkbox';
                        } else {
                            $subtype = 'other';
                            $value = $setting->get_setting();
                        }

                        $group['items'][$setting->name] = [
                            'type' => 'setting',
                            'subtype' => $subtype,
                            'title' => $title,
                            'name' => $setting->name,
                            'value' => $value,
                        ];
                    }
                }

                if ($group) {
                    $page['items'][] = $group;
                }
            }
        }

        return $page;
    }

    /**
     * Set last export date.
     *
     * @param $time
     * @return void
     */
    public static function set_lastexportdate($time = null) {
        $value = ($time === null) ? time() : $time;
        set_config('lastexportdate', $value, ParamsHelper::PLUGIN);
    }

    /**
     * Set last migration date.
     *
     * @param $time
     * @return void
     */
    public static function set_lastmigrationdate($time = null) {
        $value = ($time === null) ? time() : $time;
        set_config('lastmigrationdate', $value, ParamsHelper::PLUGIN);
    }

    /**
     * Validate if setting is updatable.
     *
     * @param $settingname
     * @return bool
     */
    public static function is_setting_updatable($settingname) {

        if (!isset(self::DEFAULT_VALUES[$settingname])) {
            return false;
        }

        if (in_array($settingname, self::NOTUPDATABLE_SETTINGS)) {
            return false;
        }

        return true;
    }
}
