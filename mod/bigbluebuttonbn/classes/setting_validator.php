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

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/adminlib.php');

/**
 * Helper class for validating settings used HTML for settings.php.
 *
 * @package mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_validator {

    /**
     * Validate if general section will be shown.
     *
     * @return bool
     */
    public static function section_general_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['server_url']) ||
                !isset($CFG->bigbluebuttonbn['shared_secret'])
            );
    }

    /**
     * Validate if default messages section will be shown.
     *
     * @return bool
     */
    public static function section_default_messages_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['welcome_default']) ||
                !isset($CFG->bigbluebuttonbn['welcome_editable']));
    }

    /**
     * Validate if record meeting section  will be shown.
     *
     * @return bool
     */
    public static function section_record_meeting_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['recording_default']) ||
                !isset($CFG->bigbluebuttonbn['recording_editable']) ||
                !isset($CFG->bigbluebuttonbn['recording_all_from_start_default']) ||
                !isset($CFG->bigbluebuttonbn['recording_all_from_start_editable']) ||
                !isset($CFG->bigbluebuttonbn['recording_hide_button_default']) ||
                !isset($CFG->bigbluebuttonbn['recording_hide_button_editable'])
            );
    }

    /**
     * Validate if import recording section will be shown.
     *
     * @return bool
     */
    public static function section_import_recordings_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['importrecordings_enabled']) ||
                !isset($CFG->bigbluebuttonbn['importrecordings_from_deleted_enabled']));
    }

    /**
     * Validate if show recording section will be shown.
     *
     * @return bool
     */
    public static function section_show_recordings_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['recordings_deleted_default']) ||
                !isset($CFG->bigbluebuttonbn['recordings_deleted_editable']) ||
                !isset($CFG->bigbluebuttonbn['recordings_imported_default']) ||
                !isset($CFG->bigbluebuttonbn['recordings_imported_editable']) ||
                !isset($CFG->bigbluebuttonbn['recordings_preview_default']) ||
                !isset($CFG->bigbluebuttonbn['recordings_preview_editable']) ||
                !isset($CFG->bigbluebuttonbn['recording_protect_editable'])
              );
    }

    /**
     * Validate if wait moderator section will be shown.
     *
     * @return bool
     */
    public static function section_wait_moderator_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['waitformoderator_default']) ||
                !isset($CFG->bigbluebuttonbn['waitformoderator_editable']) ||
                !isset($CFG->bigbluebuttonbn['waitformoderator_ping_interval']) ||
                !isset($CFG->bigbluebuttonbn['waitformoderator_cache_ttl']));
    }

    /**
     * Validate if static voice bridge section will be shown.
     *
     * @return bool
     */
    public static function section_static_voice_bridge_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['voicebridge_editable']));
    }

    /**
     * Validate if preupload presentation section will be shown.
     *
     * @return bool
     */
    public static function section_preupload_presentation_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['preuploadpresentation_editable']));
    }

    /**
     * Validate if user limit section will be shown.
     *
     * @return bool
     */
    public static function section_user_limit_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['userlimit_default']) ||
                !isset($CFG->bigbluebuttonbn['userlimit_editable']));
    }

    /**
     * Validate if moderator default section will be shown.
     *
     * @return bool
     */
    public static function section_moderator_default_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['participant_moderator_default']));
    }

    /**
     * Validate if settings extended section will be shown.
     *
     * @return bool
     */
    public static function section_settings_extended_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['recordingready_enabled']) ||
                !isset($CFG->bigbluebuttonbn['meetingevents_enabled']));
    }

    /**
     * Validate if muteonstart section will be shown.
     *
     * @return bool
     */
    public static function section_muteonstart_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['muteonstart_default']) ||
            !isset($CFG->bigbluebuttonbn['muteonstart_editable']));
    }

    /**
     * Validate if disablecam section will be shown.
     *
     * @return bool
     */
    public static function section_disablecam_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['disablecam_default']) ||
            !isset($CFG->bigbluebuttonbn['disablecam_editable']));
    }

    /**
     * Validate if disablemic section will be shown.
     *
     * @return bool
     */
    public static function section_disablemic_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['disablemic_default']) ||
            !isset($CFG->bigbluebuttonbn['disablemic_editable']));
    }

    /**
     * Validate if disableprivatechat section will be shown.
     *
     * @return bool
     */
    public static function section_disableprivatechat_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['disableprivatechat_default']) ||
            !isset($CFG->bigbluebuttonbn['disableprivatechat_editable']));
    }

    /**
     * Validate if disablepublicchat section will be shown.
     *
     * @return bool
     */
    public static function section_disablepublicchat_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['disablepublicchat_default']) ||
            !isset($CFG->bigbluebuttonbn['disablepublicchat_editable']));
    }

    /**
     * Validate if disablenote section will be shown.
     *
     * @return bool
     */
    public static function section_disablenote_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['disablenote_default']) ||
            !isset($CFG->bigbluebuttonbn['disablenote_editable']));
    }

    /**
     * Validate if hideuserlist section will be shown.
     *
     * @return bool
     */
    public static function section_hideuserlist_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['hideuserlist_default']) ||
            !isset($CFG->bigbluebuttonbn['hideuserlist_editable']));
    }

    /**
     * Validate if lockonjoin section will be shown.
     *
     * @return bool
     */
    public static function section_lockonjoin_shown() {
        global $CFG;
        return (!isset($CFG->bigbluebuttonbn['lockonjoin_default']) ||
            !isset($CFG->bigbluebuttonbn['lockonjoin_editable']));
    }

    /**
     * Validate that session lock settings is shown or not
     * @return bool
     */
    public static function section_lock_shown() {
        return self::section_disablecam_shown() ||
                self::section_disablemic_shown() ||
                self::section_disablenote_shown() ||
                self::section_disableprivatechat_shown() ||
                self::section_disablepublicchat_shown() ||
                self::section_disablenote_shown() ||
                self::section_hideuserlist_shown() ||
                self::section_lockonjoin_shown();
    }
}
