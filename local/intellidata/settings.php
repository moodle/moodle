<?php
// This file is part of the Local plans plugin
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
 * This plugin sends users a plans message after logging in
 * and notify a moderator a new user has been added
 * it has a settings page that allow you to configure the messages
 * send.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\helpers\ParamsHelper;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->locate('localplugins') && $ADMIN->locate('root')) {
    $trackingcache = \cache::make('local_intellidata', 'tracking');
    $eventscache = \cache::make('local_intellidata', 'events');

    $pluginname = local_intellidata\helpers\ParamsHelper::PLUGIN;

    $settings = new admin_settingpage($pluginname, get_string('general', $pluginname));

    $ADMIN->add('localplugins', new admin_category('intellidata', get_string('pluginname', $pluginname)));
    $ADMIN->add('intellidata', $settings);

    // General settings.
    $name = 'enabled';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $options = [
        StorageHelper::FILE_STORAGE => get_string('file', $pluginname),
        StorageHelper::DATABASE_STORAGE => get_string('database', $pluginname),
    ];

    if (method_exists($eventscache, 'store_supports_get_all_keys') && $eventscache->store_supports_get_all_keys()) {
        $options[StorageHelper::CACHE_STORAGE] = get_string('cache', $pluginname);
    }

    $name = 'trackingstorage';
    $setting = new admin_setting_configselect(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        $options
    );
    $settings->add($setting);

    $name = 'encryptionkey';
    $setting = new admin_setting_configpasswordunmask(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    $name = 'clientidentifier';
    $setting = new admin_setting_configpasswordunmask(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    $name = 'cleaner_duration';
    $setting = new admin_setting_configduration(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        86400
    );
    $settings->add($setting);

    $name = 'migrationrecordslimit';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name)
    );
    $settings->add($setting);

    $name = 'migrationwriterecordslimit';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name)
    );
    $settings->add($setting);

    $name = 'exportrecordslimit';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name)
    );
    $settings->add($setting);

    $name = 'exportfilesduringmigration';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'tracklogsdatatypes';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'resetmigrationprogress';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'resetimporttrackingprogress';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'defaultlayout';
    $setting = new admin_setting_configselect(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        SettingsHelper::get_layouts_options()
    );
    $settings->add($setting);

    $name = 'enablescheduledsnapshot';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    // User Tracking.
    $settings->add(new admin_setting_heading(
        $pluginname . 'usertracking', get_string('usertracking', $pluginname), ''
    ));

    $name = 'enabledtracking';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'compresstracking';
    $options = [
        local_intellidata\repositories\tracking\tracking_repository::TYPE_LIVE =>
            new lang_string('do_not_use_compresstracking', $pluginname),
        local_intellidata\repositories\tracking\tracking_repository::TYPE_FILE =>
            new lang_string('file_compresstracking', $pluginname),
    ];
    if (method_exists($trackingcache, 'store_supports_get_all_keys') && $trackingcache->store_supports_get_all_keys()) {
        $options[local_intellidata\repositories\tracking\tracking_repository::TYPE_CACHE] = new lang_string(
            'cache_compresstracking', $pluginname
        );
    }
    $setting = new admin_setting_configselect(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        $options
    );
    $settings->add($setting);

    $name = 'tracklogs';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'trackdetails';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'inactivity';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name)
    );
    $settings->add($setting);

    $name = 'ajaxfrequency';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name)
    );
    $settings->add($setting);

    $name = 'trackadmin';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'trackmedia';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_desc', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    // IB Next LTI.
    $settings->add(new admin_setting_heading(
        $pluginname . '/lti', get_string('lticonfiguration', $pluginname), ''
    ));

    $name = 'ltitoolurl';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    $name = 'lticonsumerkey';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    $name = 'ltisharedsecret';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    $name = 'ltititle';
    $setting = new admin_setting_configtext(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    $name = 'custommenuitem';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'ibnltirole';
    $setting = new admin_setting_configselect(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        SettingsHelper::get_roles_options()
    );
    $settings->add($setting);

    $name = 'ltiassigndefaultmethod';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        get_string($name . '_description', $pluginname),
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'debug';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string('ltidebug', $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        PARAM_TEXT
    );
    $settings->add($setting);

    // Advanced Settings.
    $settings->add(new admin_setting_heading(
        $pluginname . '/advancedsettings', get_string('advancedsettings', $pluginname), ''
    ));

    $name = 'enableprogresscalculation';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'eventstracking';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    if (!empty($CFG->intellidata_force_enable_new_tracking) ||  ParamsHelper::compare_release('3.8.0')) {
        $name = 'newtracking';
        $setting = new admin_setting_configcheckbox(
            $pluginname . '/' . $name,
            get_string($name, $pluginname),
            '',
            SettingsHelper::get_defaut_config_value($name),
            true,
            false
        );
        $settings->add($setting);
    }

    $name = 'divideexportbydatatype';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'dividemigrationtbydatatype';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'exportids';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'exportdeletedrecords';
    $setting = new admin_setting_configselect(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        SettingsHelper::get_exportdeletedrecords_options()
    );
    $settings->add($setting);

    $name = 'cacheconfig';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'forcedisablemigration';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'debugenabled';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'datavalidationenabled';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $name = 'enablecustomdbdriver';
    $setting = new admin_setting_configcheckbox(
        $pluginname . '/' . $name,
        get_string($name, $pluginname),
        '',
        SettingsHelper::get_defaut_config_value($name),
        true,
        false
    );
    $settings->add($setting);

    $ADMIN->add('intellidata',
        new admin_externalpage('intellidatamigrations',
            new lang_string('migrations', $pluginname),
            $CFG->wwwroot.'/local/intellidata/migrations/index.php')
    );

    $ADMIN->add('intellidata',
        new admin_externalpage('intellidataexportfiles',
            new lang_string('exportfiles', $pluginname),
            $CFG->wwwroot.'/local/intellidata/logs/index.php')
    );

    $ADMIN->add('intellidata',
        new admin_externalpage('intellidataexportlogs',
            new lang_string('exportlogs', $pluginname),
            $CFG->wwwroot.'/local/intellidata/logs/exportlogs.php')
    );

    if (!$ADMIN->locate('intellibdata') && $ADMIN->locate('localplugins')) {
        $ADMIN->add('intellidata', new admin_externalpage(
            'intellidatasql',
            new lang_string('sqlreports', $pluginname),
            $CFG->wwwroot.'/local/intellidata/sql_reports/index.php'
            )
        );
    }

    $ADMIN->add('intellidata',
        new admin_externalpage('intellidataconfig',
            new lang_string('configuration', $pluginname),
            $CFG->wwwroot.'/local/intellidata/config/index.php'
        )
    );

    // Page with list of adhoc tasks.
    $ADMIN->add('intellidata',
        new admin_externalpage('intellidataadhoctasks',
            new lang_string('exportadhoctasks', $pluginname),
            $CFG->wwwroot.'/local/intellidata/logs/adhoctasks.php'
        )
    );

    // IntelliBoard.net help page.
    $ADMIN->add('intellidata',
        new admin_externalpage('intelliboardhelp',
            new lang_string('help', $pluginname),
            $CFG->wwwroot.'/local/intellidata/help.php'
        )
    );

}
