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

namespace mod_bigbluebuttonbn;

use admin_category;
use admin_setting;
use admin_setting_configcheckbox;
use admin_setting_configmultiselect;
use admin_setting_configpasswordunmask;
use admin_setting_configselect;
use admin_setting_configstoredfile;
use admin_setting_configtext;
use admin_setting_configtextarea;
use admin_setting_heading;
use admin_settingpage;
use cache_helper;
use lang_string;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\local\helpers\roles;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;

/**
 * The mod_bigbluebuttonbn settings helper
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */
class settings {

    /** @var admin_category shared value */
    private $admin;

    /** @var bool Module is enabled */
    private $moduleenabled;

    /** @var string The name of the section */
    private $section;

    /** @var string The parent name */
    private $parent = "modbigbluebuttonbnfolder";

    /** @var string The section name prefix */
    private $sectionnameprefix = "mod_bigbluebuttonbn";

    /**
     * Constructor for the bigbluebuttonbn settings.
     *
     * @param admin_category $admin
     * @param \core\plugininfo\mod $module
     * @param string $categoryname for the plugin setting (main setting page)
     */
    public function __construct(admin_category $admin, \core\plugininfo\mod $module, string $categoryname) {
        $this->moduleenabled = $module->is_enabled() === true;
        $this->admin = $admin;
        $this->section = $categoryname;

        $modbigbluebuttobnfolder = new admin_category(
            $this->parent,
            new lang_string('pluginname', 'mod_bigbluebuttonbn'),
            $module->is_enabled() === false
        );

        $admin->add('modsettings', $modbigbluebuttobnfolder);

        $mainsettings = $this->add_general_settings();
        $admin->add($this->parent, $mainsettings);
    }

    /**
     * Add all settings.
     */
    public function add_all_settings(): void {
        // Renders settings for welcome messages.
        $this->add_defaultmessages_settings();
        // Evaluates if recordings are enabled for the Moodle site.
        // Renders settings for record feature.
        $this->add_record_settings();
        // Renders settings for import recordings.
        $this->add_importrecordings_settings();
        // Renders settings for showing recordings.
        $this->add_showrecordings_settings();

        // Renders settings for meetings.
        $this->add_waitmoderator_settings();
        $this->add_voicebridge_settings();
        $this->add_preupload_settings();
        $this->add_userlimit_settings();
        $this->add_participants_settings();
        $this->add_muteonstart_settings();
        $this->add_lock_settings();
        // Renders settings for extended capabilities.
        $this->add_extended_settings();
        // Renders settings for experimental features.
        $this->add_experimental_settings();
    }

    /**
     * Add the setting and lock it conditionally.
     *
     * @param string $name
     * @param admin_setting $item
     * @param admin_settingpage $settings
     */
    protected function add_conditional_element(string $name, admin_setting $item, admin_settingpage $settings): void {
        global $CFG;
        if (isset($CFG->bigbluebuttonbn) && isset($CFG->bigbluebuttonbn[$name])) {
            if ($item->config_read($item->name)) {
                // A value has been set, we can safely omit the setting and it won't interfere with installation
                // process.
                // The idea behind it is to hide values from end-users in case we use multitenancy for example.
                return;
            }
        }
        $settings->add($item);
    }

    /**
     * Helper function renders general settings if the feature is enabled.
     *
     * @return admin_settingpage
     * @throws \coding_exception
     */
    protected function add_general_settings(): admin_settingpage {
        global $CFG;
        $settingsgeneral = new admin_settingpage(
            $this->section,
            get_string('config_general', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_general_shown()) && ($this->moduleenabled)
        );
        if ($this->admin->fulltree) {
            // Configuration for BigBlueButton.
            $item = new admin_setting_heading('bigbluebuttonbn_config_general',
                '',
                get_string('config_general_description', 'bigbluebuttonbn')
            );
            $settingsgeneral->add($item);

            if (empty($CFG->bigbluebuttonbn_default_dpa_accepted)) {
                $settingsgeneral->add(new admin_setting_configcheckbox(
                    'bigbluebuttonbn_default_dpa_accepted',
                    get_string('acceptdpa', 'mod_bigbluebuttonbn'),
                    get_string('enablingbigbluebuttondpainfo', 'mod_bigbluebuttonbn', config::DEFAULT_DPA_URL),
                    0
                ));
            }

            $item = new admin_setting_configtext(
                'bigbluebuttonbn_server_url',
                get_string('config_server_url', 'bigbluebuttonbn'),
                get_string('config_server_url_description', 'bigbluebuttonbn'),
                config::DEFAULT_SERVER_URL,
                PARAM_RAW
            );
            $item->set_updatedcallback(
                function() {
                    $this->reset_cache();
                    $task = new \mod_bigbluebuttonbn\task\reset_recordings();
                    \core\task\manager::queue_adhoc_task($task);
                }
            );
            $this->add_conditional_element(
                'server_url',
                $item,
                $settingsgeneral
            );
            $item = new admin_setting_configpasswordunmask(
                'bigbluebuttonbn_shared_secret',
                get_string('config_shared_secret', 'bigbluebuttonbn'),
                get_string('config_shared_secret_description', 'bigbluebuttonbn'),
                config::DEFAULT_SHARED_SECRET
            );
            $this->add_conditional_element(
                'shared_secret',
                $item,
                $settingsgeneral
            );

            $item = new admin_setting_configselect(
                'bigbluebuttonbn_checksum_algorithm',
                get_string('config_checksum_algorithm', 'bigbluebuttonbn'),
                get_string('config_checksum_algorithm_description', 'bigbluebuttonbn'),
                config::DEFAULT_CHECKSUM_ALGORITHM,
                array_combine(config::CHECKSUM_ALGORITHMS, config::CHECKSUM_ALGORITHMS)
            );
            $this->add_conditional_element(
                'checksum_algorithm',
                $item,
                $settingsgeneral
            );

            $item = new \admin_setting_description(
                'bigbluebuttonbn_dpa_info',
                '',
                get_string('config_dpa_note', 'bigbluebuttonbn', config::DEFAULT_DPA_URL),
            );
            $this->add_conditional_element(
                'dpa_info',
                $item,
                $settingsgeneral
            );
            $item = new admin_setting_configtext(
                'bigbluebuttonbn_poll_interval',
                get_string('config_poll_interval', 'bigbluebuttonbn'),
                get_string('config_poll_interval_description', 'bigbluebuttonbn'),
                bigbluebutton_proxy::DEFAULT_POLL_INTERVAL,
                PARAM_INT
            );
            $this->add_conditional_element(
                'poll_interval',
                $item,
                $settingsgeneral
            );
        }
        return $settingsgeneral;
    }

    /**
     * Helper function renders default messages settings if the feature is enabled.
     */
    protected function add_defaultmessages_settings(): void {
        // Configuration for 'default messages' feature.
        $defaultmessagessetting = new admin_settingpage(
            "{$this->sectionnameprefix}_default_messages",
            get_string('config_default_messages', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_default_messages_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_default_messages',
                '',
                get_string('config_default_messages_description', 'bigbluebuttonbn')
            );
            $defaultmessagessetting->add($item);
            $item = new admin_setting_configtextarea(
                'bigbluebuttonbn_welcome_default',
                get_string('config_welcome_default', 'bigbluebuttonbn'),
                get_string('config_welcome_default_description', 'bigbluebuttonbn'),
                '',
                PARAM_TEXT
            );
            $this->add_conditional_element(
                'welcome_default',
                $item,
                $defaultmessagessetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_welcome_editable',
                get_string('config_welcome_editable', 'bigbluebuttonbn'),
                get_string('config_welcome_editable_description', 'bigbluebuttonbn'),
                1,
            );
            $this->add_conditional_element(
                'welcome_editable',
                $item,
                $defaultmessagessetting
            );
        }
        $this->admin->add($this->parent, $defaultmessagessetting);

    }

    /**
     * Helper function renders record settings if the feature is enabled.
     */
    protected function add_record_settings(): void {
        // Configuration for 'recording' feature.
        $recordingsetting = new admin_settingpage(
            "{$this->sectionnameprefix}_recording",
            get_string('config_recording', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_record_meeting_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_recording',
                '',
                get_string('config_recording_description', 'bigbluebuttonbn')
            );
            $recordingsetting->add($item);
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_default',
                get_string('config_recording_default', 'bigbluebuttonbn'),
                get_string('config_recording_default_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'recording_default',
                $item,
                $recordingsetting
            );
            $item = new admin_setting_configtext(
                'bigbluebuttonbn_recording_refresh_period',
                get_string('config_recording_refresh_period', 'bigbluebuttonbn'),
                get_string('config_recording_refresh_period_description', 'bigbluebuttonbn'),
                recording::RECORDING_REFRESH_DEFAULT_PERIOD,
                PARAM_INT
            );
            $this->add_conditional_element(
                'recording_refresh_period',
                $item,
                $recordingsetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_editable',
                get_string('config_recording_editable', 'bigbluebuttonbn'),
                get_string('config_recording_editable_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'recording_editable',
                $item,
                $recordingsetting
            );

            // Add recording start to load and allow/hide stop/pause.
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_all_from_start_default',
                get_string('config_recording_all_from_start_default', 'bigbluebuttonbn'),
                get_string('config_recording_all_from_start_default_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recording_all_from_start_default',
                $item,
                $recordingsetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_all_from_start_editable',
                get_string('config_recording_all_from_start_editable', 'bigbluebuttonbn'),
                get_string('config_recording_all_from_start_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recording_all_from_start_editable',
                $item,
                $recordingsetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_hide_button_default',
                get_string('config_recording_hide_button_default', 'bigbluebuttonbn'),
                get_string('config_recording_hide_button_default_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recording_hide_button_default',
                $item,
                $recordingsetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_hide_button_editable',
                get_string('config_recording_hide_button_editable', 'bigbluebuttonbn'),
                get_string('config_recording_hide_button_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recording_hide_button_editable',
                $item,
                $recordingsetting
            );
            $recordingsafeformat = [
                'notes' => get_string('view_recording_format_notes', 'mod_bigbluebuttonbn'),
                'podcast' => get_string('view_recording_format_podcast', 'mod_bigbluebuttonbn'),
                'presentation' => get_string('view_recording_format_presentation', 'mod_bigbluebuttonbn'),
                'screenshare' => get_string('view_recording_format_screenshare', 'mod_bigbluebuttonbn'),
                'statistics' => get_string('view_recording_format_statistics', 'mod_bigbluebuttonbn'),
                'video' => get_string('view_recording_format_video', 'mod_bigbluebuttonbn'),
            ];
            $item = new admin_setting_configmultiselect(
                'bigbluebuttonbn_recording_safe_formats',
                get_string('config_recording_safe_formats', 'mod_bigbluebuttonbn'),
                get_string('config_recording_safe_formats_description', 'mod_bigbluebuttonbn'),
                ['video', 'presentation'],
                $recordingsafeformat
            );
            $this->add_conditional_element(
                'recording_hide_button_editable',
                $item,
                $recordingsetting
            );
        }
        $this->admin->add($this->parent, $recordingsetting);
    }

    /**
     * Helper function renders import recording settings if the feature is enabled.
     */
    protected function add_importrecordings_settings(): void {
        // Configuration for 'import recordings' feature.
        $importrecordingsettings = new admin_settingpage(
            "{$this->sectionnameprefix}_importrecording",
            get_string('config_importrecordings', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_import_recordings_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_importrecordings',
                '',
                get_string('config_importrecordings_description', 'bigbluebuttonbn')
            );
            $importrecordingsettings->add($item);
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_importrecordings_enabled',
                get_string('config_importrecordings_enabled', 'bigbluebuttonbn'),
                get_string('config_importrecordings_enabled_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'importrecordings_enabled',
                $item,
                $importrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_importrecordings_from_deleted_enabled',
                get_string('config_importrecordings_from_deleted_enabled', 'bigbluebuttonbn'),
                get_string('config_importrecordings_from_deleted_enabled_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'importrecordings_from_deleted_enabled',
                $item,
                $importrecordingsettings
            );
        }
        $this->admin->add($this->parent, $importrecordingsettings);
    }

    /**
     * Helper function renders show recording settings if the feature is enabled.
     */
    protected function add_showrecordings_settings(): void {
        // Configuration for 'show recordings' feature.
        $showrecordingsettings = new admin_settingpage(
            "{$this->sectionnameprefix}_showrecordings",
            get_string('config_recordings', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_show_recordings_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_recordings',
                '',
                get_string('config_recordings_description', 'bigbluebuttonbn')
            );
            $showrecordingsettings->add($item);
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_deleted_default',
                get_string('config_recordings_deleted_default', 'bigbluebuttonbn'),
                get_string('config_recordings_deleted_default_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'recordings_deleted_default',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_deleted_editable',
                get_string('config_recordings_deleted_editable', 'bigbluebuttonbn'),
                get_string('config_recordings_deleted_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recordings_deleted_editable',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_imported_default',
                get_string('config_recordings_imported_default', 'bigbluebuttonbn'),
                get_string('config_recordings_imported_default_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recordings_imported_default',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_imported_editable',
                get_string('config_recordings_imported_editable', 'bigbluebuttonbn'),
                get_string('config_recordings_imported_editable_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'recordings_imported_editable',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_preview_default',
                get_string('config_recordings_preview_default', 'bigbluebuttonbn'),
                get_string('config_recordings_preview_default_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'recordings_preview_default',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_preview_editable',
                get_string('config_recordings_preview_editable', 'bigbluebuttonbn'),
                get_string('config_recordings_preview_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recordings_preview_editable',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordings_asc_sort',
                get_string('config_recordings_asc_sort', 'bigbluebuttonbn'),
                get_string('config_recordings_asc_sort_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recordings_asc_sort',
                $item,
                $showrecordingsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recording_protect_editable',
                get_string('config_recording_protect_editable', 'bigbluebuttonbn'),
                get_string('config_recording_protect_editable_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'recording_protect_editable',
                $item,
                $showrecordingsettings
            );
        }
        $this->admin->add($this->parent, $showrecordingsettings);
    }

    /**
     * Helper function renders wait for moderator settings if the feature is enabled.
     */
    protected function add_waitmoderator_settings(): void {
        // Configuration for wait for moderator feature.
        $waitmoderatorsettings = new admin_settingpage(
            "{$this->sectionnameprefix}_waitformoderator",
            get_string('config_waitformoderator', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_wait_moderator_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_waitformoderator',
                '',
                get_string('config_waitformoderator_description', 'bigbluebuttonbn')
            );
            $waitmoderatorsettings->add($item);
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_waitformoderator_default',
                get_string('config_waitformoderator_default', 'bigbluebuttonbn'),
                get_string('config_waitformoderator_default_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'waitformoderator_default',
                $item,
                $waitmoderatorsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_waitformoderator_editable',
                get_string('config_waitformoderator_editable', 'bigbluebuttonbn'),
                get_string('config_waitformoderator_editable_description', 'bigbluebuttonbn'),
                1
            );
            $this->add_conditional_element(
                'waitformoderator_editable',
                $item,
                $waitmoderatorsettings
            );
            $item = new admin_setting_configtext(
                'bigbluebuttonbn_waitformoderator_ping_interval',
                get_string('config_waitformoderator_ping_interval', 'bigbluebuttonbn'),
                get_string('config_waitformoderator_ping_interval_description', 'bigbluebuttonbn'),
                10,
                PARAM_INT
            );
            $this->add_conditional_element(
                'waitformoderator_ping_interval',
                $item,
                $waitmoderatorsettings
            );
            $item = new admin_setting_configtext(
                'bigbluebuttonbn_waitformoderator_cache_ttl',
                get_string('config_waitformoderator_cache_ttl', 'bigbluebuttonbn'),
                get_string('config_waitformoderator_cache_ttl_description', 'bigbluebuttonbn'),
                60,
                PARAM_INT
            );
            $this->add_conditional_element(
                'waitformoderator_cache_ttl',
                $item,
                $waitmoderatorsettings
            );
        }
        $this->admin->add($this->parent, $waitmoderatorsettings);
    }

    /**
     * Helper function renders static voice bridge settings if the feature is enabled.
     */
    protected function add_voicebridge_settings(): void {
        // Configuration for "static voice bridge" feature.
        $voicebridgesettings = new admin_settingpage(
            "{$this->sectionnameprefix}_voicebridge",
            get_string('config_voicebridge', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_static_voice_bridge_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_voicebridge',
                '',
                get_string('config_voicebridge_description', 'bigbluebuttonbn')
            );
            $voicebridgesettings->add($item);
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_voicebridge_editable',
                get_string('config_voicebridge_editable', 'bigbluebuttonbn'),
                get_string('config_voicebridge_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'voicebridge_editable',
                $item,
                $voicebridgesettings
            );
        }
        $this->admin->add($this->parent, $voicebridgesettings);
    }

    /**
     * Helper function renders preuploaded presentation settings if the feature is enabled.
     */
    protected function add_preupload_settings(): void {
        // Configuration for "preupload presentation" feature.
        $preuploadsettings = new admin_settingpage(
            "{$this->sectionnameprefix}_preupload",
            get_string('config_preuploadpresentation', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_preupload_presentation_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            // This feature only works if curl is installed (but it is as now required by Moodle). The checks have been removed.
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_preuploadpresentation',
                '',
                get_string('config_preuploadpresentation_description', 'bigbluebuttonbn')
            );
            $preuploadsettings->add($item);

            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_preuploadpresentation_editable',
                get_string('config_preuploadpresentation_editable', 'bigbluebuttonbn'),
                get_string('config_preuploadpresentation_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'preuploadpresentation_editable',
                $item,
                $preuploadsettings
            );
            // Note: checks on curl library have been removed as it is a requirement from Moodle.
            $filemanageroptions = [];
            $filemanageroptions['accepted_types'] = '*';
            $filemanageroptions['maxbytes'] = 0;
            $filemanageroptions['subdirs'] = 0;
            $filemanageroptions['maxfiles'] = 1;
            $filemanageroptions['mainfile'] = true;

            $filemanager = new admin_setting_configstoredfile(
                'mod_bigbluebuttonbn/presentationdefault',
                get_string('config_presentation_default', 'bigbluebuttonbn'),
                get_string('config_presentation_default_description', 'bigbluebuttonbn'),
                'presentationdefault',
                0,
                $filemanageroptions
            );

            $preuploadsettings->add($filemanager);
        }
        $this->admin->add($this->parent, $preuploadsettings);
    }

    /**
     * Helper function renders userlimit settings if the feature is enabled.
     */
    protected function add_userlimit_settings(): void {
        $userlimitsettings = new admin_settingpage(
            "{$this->sectionnameprefix}_userlimit",
            get_string('config_userlimit', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_user_limit_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            // Configuration for "user limit" feature.
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_userlimit',
                '',
                get_string('config_userlimit_description', 'bigbluebuttonbn')
            );
            $userlimitsettings->add($item);
            $item = new admin_setting_configtext(
                'bigbluebuttonbn_userlimit_default',
                get_string('config_userlimit_default', 'bigbluebuttonbn'),
                get_string('config_userlimit_default_description', 'bigbluebuttonbn'),
                0,
                PARAM_INT
            );
            $this->add_conditional_element(
                'userlimit_default',
                $item,
                $userlimitsettings
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_userlimit_editable',
                get_string('config_userlimit_editable', 'bigbluebuttonbn'),
                get_string('config_userlimit_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'userlimit_editable',
                $item,
                $userlimitsettings
            );
        }
        $this->admin->add($this->parent, $userlimitsettings);
    }

    /**
     * Helper function renders participant settings if the feature is enabled.
     */
    protected function add_participants_settings(): void {
        // Configuration for defining the default role/user that will be moderator on new activities.
        $participantsettings = new admin_settingpage(
            "{$this->sectionnameprefix}_participant",
            get_string('config_participant', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_moderator_default_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_participant',
                '',
                get_string('config_participant_description', 'bigbluebuttonbn')
            );
            $participantsettings->add($item);

            // UI for 'participants' feature.
            $roles = roles::get_roles(null, false);
            $owner = [
                '0' => get_string('mod_form_field_participant_list_type_owner', 'bigbluebuttonbn')
            ];
            $item = new admin_setting_configmultiselect(
                'bigbluebuttonbn_participant_moderator_default',
                get_string('config_participant_moderator_default', 'bigbluebuttonbn'),
                get_string('config_participant_moderator_default_description', 'bigbluebuttonbn'),
                array_keys($owner),
                $owner + $roles
            );
            $this->add_conditional_element(
                'participant_moderator_default',
                $item,
                $participantsettings
            );
        }
        $this->admin->add($this->parent, $participantsettings);
    }

    /**
     * Helper function renders general settings if the feature is enabled.
     */
    protected function add_muteonstart_settings(): void {
        // Configuration for BigBlueButton.
        $muteonstartsetting = new admin_settingpage(
            "{$this->sectionnameprefix}_muteonstart",
            get_string('config_muteonstart', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_muteonstart_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_muteonstart',
                '',
                get_string('config_muteonstart_description', 'bigbluebuttonbn')
            );
            $muteonstartsetting->add($item);
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_muteonstart_default',
                get_string('config_muteonstart_default', 'bigbluebuttonbn'),
                get_string('config_muteonstart_default_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'muteonstart_default',
                $item,
                $muteonstartsetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_muteonstart_editable',
                get_string('config_muteonstart_editable', 'bigbluebuttonbn'),
                get_string('config_muteonstart_editable_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'muteonstart_editable',
                $item,
                $muteonstartsetting
            );
        }
        $this->admin->add($this->parent, $muteonstartsetting);
    }

    /**
     * Helper function to render lock settings.
     */
    protected function add_lock_settings(): void {
        $lockingsetting = new admin_settingpage(
            "{$this->sectionnameprefix}_locksettings",
            get_string('config_locksettings', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_lock_shown()) && ($this->moduleenabled)
        );
        // Configuration for various lock settings for meetings.
        if ($this->admin->fulltree) {
            $this->add_lock_setting_from_name('disablecam', $lockingsetting);
            $this->add_lock_setting_from_name('disablemic', $lockingsetting);
            $this->add_lock_setting_from_name('disableprivatechat', $lockingsetting);
            $this->add_lock_setting_from_name('disablepublicchat', $lockingsetting);
            $this->add_lock_setting_from_name('disablenote', $lockingsetting);
            $this->add_lock_setting_from_name('hideuserlist', $lockingsetting);
        }
        $this->admin->add($this->parent, $lockingsetting);
    }

    /**
     * Helper function renders setting if the feature is enabled.
     *
     * @param string $settingname
     * @param admin_settingpage $lockingsetting The parent settingpage to add to
     */
    protected function add_lock_setting_from_name(string $settingname, admin_settingpage $lockingsetting): void {
        $validatorname = "section_{$settingname}_shown";
        if ((boolean) setting_validator::$validatorname()) {
            // Configuration for BigBlueButton.
            $item = new admin_setting_configcheckbox(
                    'bigbluebuttonbn_' . $settingname . '_default',
                    get_string('config_' . $settingname . '_default', 'bigbluebuttonbn'),
                    get_string('config_' . $settingname . '_default_description', 'bigbluebuttonbn'),
                    config::defaultvalue($settingname . '_default')
            );
            $this->add_conditional_element(
                    $settingname . '_default',
                    $item,
                    $lockingsetting
            );
            $item = new admin_setting_configcheckbox(
                    'bigbluebuttonbn_' . $settingname . '_editable',
                    get_string('config_' . $settingname . '_editable', 'bigbluebuttonbn'),
                    get_string('config_' . $settingname . '_editable_description', 'bigbluebuttonbn'),
                    config::defaultvalue($settingname . '_editable')
            );
            $this->add_conditional_element(
                    $settingname . '_editable',
                    $item,
                    $lockingsetting
            );
        }
    }

    /**
     * Helper function renders extended settings if any of the features there is enabled.
     */
    protected function add_extended_settings(): void {
        // Configuration for extended capabilities.
        $extendedcapabilitiessetting = new admin_settingpage(
            "{$this->sectionnameprefix}_extendedcapabilities",
            get_string('config_extended_capabilities', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_settings_extended_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_extended_capabilities',
                '',
                get_string('config_extended_capabilities_description', 'bigbluebuttonbn')
            );
            $extendedcapabilitiessetting->add($item);
            // UI for 'notify users when recording ready' feature.
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_recordingready_enabled',
                get_string('config_recordingready_enabled', 'bigbluebuttonbn'),
                get_string('config_recordingready_enabled_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'recordingready_enabled',
                $item,
                $extendedcapabilitiessetting
            );
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_profile_picture_enabled',
                get_string('config_profile_picture_enabled', 'bigbluebuttonbn'),
                get_string('config_profile_picture_enabled_description', 'bigbluebuttonbn'),
                false
            );
            $this->add_conditional_element(
                'profile_picture_enabled',
                $item,
                $extendedcapabilitiessetting
            );
        }
        $this->admin->add($this->parent, $extendedcapabilitiessetting);
        // Configuration for extended BN capabilities should go here.
    }

    /**
     * Helper function renders experimental settings if any of the features there is enabled.
     */
    protected function add_experimental_settings(): void {
        // Configuration for experimental features should go here.
        $experimentalfeaturessetting = new admin_settingpage(
            "{$this->sectionnameprefix}_experimentalfeatures",
            get_string('config_experimental_features', 'bigbluebuttonbn'),
            'moodle/site:config',
            !((boolean) setting_validator::section_settings_extended_shown()) && ($this->moduleenabled)
        );

        if ($this->admin->fulltree) {
            $item = new admin_setting_heading(
                'bigbluebuttonbn_config_experimental_features',
                '',
                get_string('config_experimental_features_description', 'bigbluebuttonbn')
            );
            $experimentalfeaturessetting->add($item);
            // UI for 'register meeting events' feature.
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_meetingevents_enabled',
                get_string('config_meetingevents_enabled', 'bigbluebuttonbn'),
                get_string('config_meetingevents_enabled_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'meetingevents_enabled',
                $item,
                $experimentalfeaturessetting
            );
            // UI for 'register meeting events' feature.
            $item = new admin_setting_configcheckbox(
                'bigbluebuttonbn_guestaccess_enabled',
                get_string('config_guestaccess_enabled', 'bigbluebuttonbn'),
                get_string('config_guestaccess_enabled_description', 'bigbluebuttonbn'),
                0
            );
            $this->add_conditional_element(
                'guestaccess_enabled',
                $item,
                $experimentalfeaturessetting
            );
        }
        $this->admin->add($this->parent, $experimentalfeaturessetting);
    }

    /**
     * Process reset cache.
     */
    protected function reset_cache() {
        // Reset serverinfo cache.
        cache_helper::purge_by_event('mod_bigbluebuttonbn/serversettingschanged');
    }
}
