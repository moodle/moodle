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

namespace mod_bigbluebuttonbn\local;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\recording;

/**
 * Handles the global configuration based on config.php.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class config {

    /** @var string Default bigbluebutton server url */
    public const DEFAULT_SERVER_URL = 'https://test-moodle.blindsidenetworks.com/bigbluebutton/';

    /** @var string Default bigbluebutton server shared secret */
    public const DEFAULT_SHARED_SECRET = '0b21fcaf34673a8c3ec8ed877d76ae34';

    /** @var string Default bigbluebutton data processing agreement url */
    public const DEFAULT_DPA_URL = 'https://blindsidenetworks.com/dpa-moodle-free-tier';

    /** @var string the default bigbluebutton checksum algorithm */
    public const DEFAULT_CHECKSUM_ALGORITHM = 'SHA1';

    /** @var array list of supported bigbluebutton checksum algorithm */
    const CHECKSUM_ALGORITHMS = [
        self::DEFAULT_CHECKSUM_ALGORITHM,
        'SHA256',
        'SHA512'
    ];

    /**
     * Returns moodle version.
     *
     * @return string
     */
    protected static function get_moodle_version_major(): string {
        global $CFG;
        $versionarray = explode('.', $CFG->version);
        return $versionarray[0];
    }

    /**
     * Returns configuration default values.
     *
     * @return array
     */
    protected static function defaultvalues(): array {
        return [
            'server_url' => self::DEFAULT_SERVER_URL,
            'shared_secret' => self::DEFAULT_SHARED_SECRET,
            'voicebridge_editable' => false,
            'importrecordings_enabled' => false,
            'importrecordings_from_deleted_enabled' => false,
            'waitformoderator_default' => false,
            'waitformoderator_editable' => true,
            'waitformoderator_ping_interval' => '10',
            'waitformoderator_cache_ttl' => '60',
            'userlimit_default' => '0',
            'userlimit_editable' => false,
            'preuploadpresentation_editable' => false,
            'recordingready_enabled' => false,
            'recordingstatus_enabled' => false,
            'meetingevents_enabled' => false,
            'participant_moderator_default' => '0',
            'profile_picture_enabled' => false,
            'scheduled_pre_opening' => '10',
            'recordings_enabled' => true,
            'recordings_deleted_default' => false,
            'recordings_deleted_editable' => false,
            'recordings_imported_default' => false,
            'recordings_imported_editable' => false,
            'recordings_preview_default' => true,
            'recordings_preview_editable' => false,
            'recording_default' => true,
            'recording_editable' => true,
            'recording_refresh_period' => recording::RECORDING_REFRESH_DEFAULT_PERIOD,
            'recording_all_from_start_default' => false,
            'recording_all_from_start_editable' => false,
            'recording_hide_button_default' => false,
            'recording_hide_button_editable' => false,
            'recording_protect_editable' => true,
            'general_warning_message' => '',
            'general_warning_roles' => 'editingteacher,teacher',
            'general_warning_box_type' => 'info',
            'general_warning_button_text' => '',
            'general_warning_button_href' => '',
            'general_warning_button_class' => '',
            'muteonstart_default' => false,
            'muteonstart_editable' => false,
            'breakoutrooms_default' => false,
            'breakoutrooms_editable' => false,
            'captions_default' => false,
            'captions_editable' => false,
            'chat_default' => false,
            'chat_editable' => false,
            'downloadpresentationwithannotations_default' => false,
            'downloadpresentationwithannotations_editable' => false,
            'snapshotofcurrentslide_default' => false,
            'snapshotofcurrentslide_editable' => false,
            'externalvideos_default' => false,
            'externalvideos_editable' => false,
            'importpresentationwithannotationsfrombreakoutrooms_default' => false,
            'importpresentationwithannotationsfrombreakoutrooms_editable' => false,
            'importsharednotesfrombreakoutrooms_default' => false,
            'importsharednotesfrombreakoutrooms_editable' => false,
            'layouts_default' => false,
            'layouts_editable' => false,
            'learningdashboard_default' => false,
            'learningdashboard_editable' => false,
            'polls_default' => false,
            'polls_editable' => false,
            'screenshare_default' => false,
            'screenshare_editable' => false,
            'sharednotes_default' => false,
            'sharednotes_editable' => false,
            'virtualbackgrounds_default' => false,
            'virtualbackgrounds_editable' => false,
            'customvirtualbackgrounds_default' => false,
            'customvirtualbackgrounds_editable' => false,
            'presentationdf_default' => false,
            'presentationdf_editable' => false,
            'cameraascontent_default' => false,
            'cameraascontent_editable' => false,
            'disablecam_default' => false,
            'disablecam_editable' => true,
            'disablemic_default' => false,
            'disablemic_editable' => true,
            'disableprivatechat_default' => false,
            'disableprivatechat_editable' => true,
            'disablepublicchat_default' => false,
            'disablepublicchat_editable' => true,
            'disablenote_default' => false,
            'disablenote_editable' => true,
            'hideuserlist_default' => false,
            'hideuserlist_editable' => true,
            'welcome_default' => '',
            'welcome_editable' => true,
            'default_dpa_accepted' => false,
            'poll_interval' => bigbluebutton_proxy::DEFAULT_POLL_INTERVAL,
            'checksum_algorithm' => self::DEFAULT_CHECKSUM_ALGORITHM,
        ];
    }

    /**
     * Returns default value for an specific setting.
     *
     * @param string $setting
     * @return string|null
     */
    public static function defaultvalue(string $setting): ?string {
        $defaultvalues = self::defaultvalues();
        if (!array_key_exists($setting, $defaultvalues)) {
            return null;
        }
        return $defaultvalues[$setting];
    }

    /**
     * Returns value for an specific setting.
     *
     * @param string $setting
     * @return string
     */
    public static function get(string $setting): string {
        global $CFG;
        if (isset($CFG->bigbluebuttonbn[$setting])) {
            return (string) $CFG->bigbluebuttonbn[$setting];
        }
        if (isset($CFG->{'bigbluebuttonbn_' . $setting})) {
            return (string) $CFG->{'bigbluebuttonbn_' . $setting};
        }
        return (string) self::defaultvalue($setting);
    }

    /**
     * Validates if recording settings are enabled.
     *
     * @return bool
     */
    public static function recordings_enabled(): bool {
        return (boolean) self::get('recordings_enabled');
    }

    /**
     * Validates if imported recording settings are enabled.
     *
     * @return bool
     */
    public static function importrecordings_enabled(): bool {
        return (boolean) self::get('importrecordings_enabled');
    }

    /**
     * Wraps current settings in an array.
     *
     * @return array
     */
    public static function get_options(): array {
        return [
            'version_major' => self::get_moodle_version_major(),
            'voicebridge_editable' => self::get('voicebridge_editable'),
            'importrecordings_enabled' => self::get('importrecordings_enabled'),
            'importrecordings_from_deleted_enabled' => self::get('importrecordings_from_deleted_enabled'),
            'waitformoderator_default' => self::get('waitformoderator_default'),
            'waitformoderator_editable' => self::get('waitformoderator_editable'),
            'userlimit_default' => self::get('userlimit_default'),
            'userlimit_editable' => self::get('userlimit_editable'),
            'preuploadpresentation_editable' => self::get('preuploadpresentation_editable'),
            'recordings_enabled' => self::get('recordings_enabled'),
            'meetingevents_enabled' => self::get('meetingevents_enabled'),
            'recordings_deleted_default' => self::get('recordings_deleted_default'),
            'recordings_deleted_editable' => self::get('recordings_deleted_editable'),
            'recordings_imported_default' => self::get('recordings_imported_default'),
            'recordings_imported_editable' => self::get('recordings_imported_editable'),
            'recordings_preview_default' => self::get('recordings_preview_default'),
            'recordings_preview_editable' => self::get('recordings_preview_editable'),
            'recording_default' => self::get('recording_default'),
            'recording_editable' => self::get('recording_editable'),
            'recording_refresh_period' => self::get('recording_refresh_period'),
            'recording_all_from_start_default' => self::get('recording_all_from_start_default'),
            'recording_all_from_start_editable' => self::get('recording_all_from_start_editable'),
            'recording_hide_button_default' => self::get('recording_hide_button_default'),
            'recording_hide_button_editable' => self::get('recording_hide_button_editable'),
            'recording_protect_editable' => self::get('recording_protect_editable'),
            'general_warning_message' => self::get('general_warning_message'),
            'general_warning_box_type' => self::get('general_warning_box_type'),
            'general_warning_button_text' => self::get('general_warning_button_text'),
            'general_warning_button_href' => self::get('general_warning_button_href'),
            'general_warning_button_class' => self::get('general_warning_button_class'),
            'muteonstart_editable' => self::get('muteonstart_editable'),
            'muteonstart_default' => self::get('muteonstart_default'),
            'breakoutrooms_editable' => self::get('breakoutrooms_editable'),
            'breakoutrooms_default' => self::get('breakoutrooms_default'),
            'captions_editable' => self::get('captions_editable'),
            'captions_default' => self::get('captions_default'),
            'chat_editable' => self::get('chat_editable'),
            'chat_default' => self::get('chat_default'),
            'downloadpresentationwithannotations_editable' => self::get('downloadpresentationwithannotations_editable'),
            'downloadpresentationwithannotations_default' => self::get('downloadpresentationwithannotations_default'),
            'snapshotofcurrentslide_editable' => self::get('snapshotofcurrentslide_editable'),
            'snapshotofcurrentslide_default' => self::get('snapshotofcurrentslide_default'),
            'externalvideos_editable' => self::get('externalvideos_editable'),
            'externalvideos_default' => self::get('externalvideos_default'),
            'importpresentationwithannotationsfrombreakoutrooms_editable' => self::get('importpresentationwithannotationsfrombreakoutrooms_editable'),
            'importpresentationwithannotationsfrombreakoutrooms_default' => self::get('importpresentationwithannotationsfrombreakoutrooms_default'),
            'importsharednotesfrombreakoutrooms_editable' => self::get('importsharednotesfrombreakoutrooms_editable'),
            'importsharednotesfrombreakoutrooms_default' => self::get('importsharednotesfrombreakoutrooms_default'),
            'layouts_editable' => self::get('layouts_editable'),
            'layouts_default' => self::get('layouts_default'),
            'learningdashboard_editable' => self::get('learningdashboard_editable'),
            'learningdashboard_default' => self::get('learningdashboard_default'),
            'polls_editable' => self::get('polls_editable'),
            'polls_default' => self::get('polls_default'),
            'screenshare_editable' => self::get('screenshare_editable'),
            'screenshare_default' => self::get('screenshare_default'),
            'sharednotes_editable' => self::get('sharednotes_editable'),
            'sharednotes_default' => self::get('sharednotes_default'),
            'virtualbackgrounds_editable' => self::get('virtualbackgrounds_editable'),
            'virtualbackgrounds_default' => self::get('virtualbackgrounds_default'),
            'customvirtualbackgrounds_editable' => self::get('customvirtualbackgrounds_editable'),
            'customvirtualbackgrounds_default' => self::get('customvirtualbackgrounds_default'),
            'presentationdf_editable' => self::get('presentationdf_editable'),
            'presentationdf_default' => self::get('presentationdf_default'),
            'cameraascontent_editable' => self::get('cameraascontent_editable'),
            'cameraascontent_default' => self::get('cameraascontent_default'),
            'timer_editable' => self::get('timer_editable'),
            'timer_default' => self::get('timer_default'),
            'disablecam_editable' => self::get('disablecam_editable'),
            'disablecam_default' => self::get('disablecam_default'),
            'disablemic_editable' => self::get('disablemic_editable'),
            'disablemic_default' => self::get('disablemic_default'),
            'disableprivatechat_editable' => self::get('disableprivatechat_editable'),
            'disableprivatechat_default' => self::get('disableprivatechat_default'),
            'disablepublicchat_editable' => self::get('disablepublicchat_editable'),
            'disablepublicchat_default' => self::get('disablepublicchat_default'),
            'disablenote_editable' => self::get('disablenote_editable'),
            'disablenote_default' => self::get('disablenote_default'),
            'hideuserlist_editable' => self::get('hideuserlist_editable'),
            'hideuserlist_default' => self::get('hideuserlist_default'),
            'welcome_default' => self::get('welcome_default'),
            'welcome_editable' => self::get('welcome_editable'),
            'poll_interval' => self::get('poll_interval'),
            'guestaccess_enabled' => self::get('guestaccess_enabled'),
        ];
    }

    /**
     * Helper function returns an array with enabled features for an specific profile type.
     *
     * @param array $typeprofiles
     * @param string|null $type
     *
     * @return array
     */
    public static function get_enabled_features(array $typeprofiles, ?string $type = null): array {
        $enabledfeatures = [];
        $features = $typeprofiles[instance::TYPE_ALL]['features'];
        if (!is_null($type) && key_exists($type, $typeprofiles)) {
            $features = $typeprofiles[$type]['features'];
        }
        $enabledfeatures['showroom'] = (in_array('all', $features) || in_array('showroom', $features));
        // Evaluates if recordings are enabled for the Moodle site.
        $enabledfeatures['showrecordings'] = false;
        if (self::recordings_enabled()) {
            $enabledfeatures['showrecordings'] = (in_array('all', $features) || in_array('showrecordings', $features));
        }
        $enabledfeatures['importrecordings'] = false;
        if (self::importrecordings_enabled()) {
            $enabledfeatures['importrecordings'] = (in_array('all', $features) || in_array('importrecordings', $features));
        }
        return $enabledfeatures;
    }
}
