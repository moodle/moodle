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

namespace mod_bigbluebuttonbn\local\bigbluebutton\recordings;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\local\helpers\roles;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\output\recording_description_editable;
use mod_bigbluebuttonbn\output\recording_name_editable;
use mod_bigbluebuttonbn\output\recording_row_actionbar;
use mod_bigbluebuttonbn\output\recording_row_playback;
use mod_bigbluebuttonbn\output\recording_row_preview;
use mod_bigbluebuttonbn\recording;
use stdClass;

/**
 * The recordings_data.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent.david [at] call-learning [dt] fr)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class recording_data {

    /**
     * Get the full recording table
     *
     * @param array $recordings
     * @param array $tools
     * @param instance|null $instance
     * @param int $courseid
     * @return array
     */
    public static function get_recording_table(array $recordings, array $tools, ?instance $instance = null,
        int $courseid = 0): array {
        $typeprofiles = bigbluebutton_proxy::get_instance_type_profiles();
        $typeprofile = empty($instance) ? $typeprofiles[0] : $typeprofiles[$instance->get_type()];
        $lang = get_string('locale', 'core_langconfig');
        $locale = substr($lang, 0, strpos($lang, '.'));
        $tabledata = [
            'activity' => empty($instance) ? '' : bigbluebutton_proxy::view_get_activity_status($instance),
            'ping_interval' => (int) config::get('waitformoderator_ping_interval') * 1000,
            'locale' => substr($locale, 0, strpos($locale, '_')),
            'profile_features' => $typeprofile['features'],
            'columns' => [],
            'data' => '',
        ];
        $hascapabilityincourse = empty($instance) && roles::has_capability_in_course($courseid,
                'mod/bigbluebuttonbn:managerecordings');

        $data = [];

        // Build table content.
        foreach ($recordings as $recording) {
            $rowtools = $tools;
            // Protected recordings may be enabled or disabled from UI through configuration.
            if (!(boolean) config::get('recording_protect_editable')) {
                $rowtools = array_diff($rowtools, ['protect', 'unprotect']);
            }
            // Protected recordings is not a standard feature, remove actions when protected flag is not present.
            if (in_array('protect', $rowtools) && $recording->get('protected') === null) {
                $rowtools = array_diff($rowtools, ['protect', 'unprotect']);
            }
            $rowdata = self::row($instance, $recording, $rowtools);
            if (!empty($rowdata)) {
                $data[] = $rowdata;
            }
        }

        $columns = [
            [
                'key' => 'playback',
                'label' => get_string('view_recording_playback', 'bigbluebuttonbn'),
                'width' => '125px',
                'type' => 'html',
                'allowHTML' => true,
            ],
            [
                'key' => 'recording',
                'label' => get_string('view_recording_name', 'bigbluebuttonbn'),
                'width' => '125px',
                'type' => 'html',
                'allowHTML' => true,
            ],
            [
                'key' => 'description',
                'label' => get_string('view_recording_description', 'bigbluebuttonbn'),
                'sortable' => true,
                'width' => '250px',
                'type' => 'html',
                'allowHTML' => true,
            ],
        ];

        // Initialize table headers.
        $ispreviewenabled = !empty($instance) && self::preview_enabled($instance);
        $ispreviewenabled = $ispreviewenabled || $hascapabilityincourse;
        if ($ispreviewenabled) {
            $columns[] = [
                'key' => 'preview',
                'label' => get_string('view_recording_preview', 'bigbluebuttonbn'),
                'width' => '250px',
                'type' => 'html',
                'allowHTML' => true,
            ];
        }

        $columns[] = [
            'key' => 'date',
            'label' => get_string('view_recording_date', 'bigbluebuttonbn'),
            'sortable' => true,
            'width' => '225px',
            'type' => 'html',
            'formatter' => 'customDate',
        ];
        $columns[] = [
            'key' => 'duration',
            'label' => get_string('view_recording_duration', 'bigbluebuttonbn'),
            'width' => '50px',
            'allowHTML' => false,
            'sortable' => true,
        ];
        // Either instance is empty and we must show the toolbar (with restricted content) or we check
        // specific rights related to the instance.
        $canmanagerecordings = !empty($instance) && $instance->can_manage_recordings();
        $canmanagerecordings = $canmanagerecordings || $hascapabilityincourse;
        if ($canmanagerecordings) {
            $columns[] = [
                'key' => 'actionbar',
                'label' => get_string('view_recording_actionbar', 'bigbluebuttonbn'),
                'width' => '120px',
                'type' => 'html',
                'allowHTML' => true,
            ];
        }

        $tabledata['columns'] = $columns;
        $tabledata['data'] = json_encode($data);

        return $tabledata;
    }

    /**
     * Helper function builds a row for the data used by the recording table.
     *
     * TODO: replace this with templates whenever possible so we just
     * return the data via the API.
     *
     * @param instance|null $instance $instance
     * @param recording $rec a recording row
     * @param array|null $tools
     * @param int|null $courseid
     * @return stdClass|null
     */
    public static function row(?instance $instance, recording $rec, ?array $tools = null, ?int $courseid = 0): ?stdClass {
        global $PAGE;

        $hascapabilityincourse = empty($instance) && roles::has_capability_in_course($courseid,
                'mod/bigbluebuttonbn:managerecordings');
        $renderer = $PAGE->get_renderer('mod_bigbluebuttonbn');
        foreach ($tools as $key => $tool) {
            if ((!empty($instance) && !$instance->can_perform_on_recordings($tool))
                || (empty($instance) && !$hascapabilityincourse)) {
                unset($tools[$key]);
            }
        }
        if (!self::include_recording_table_row($instance, $rec)) {
            return null;
        }
        $rowdata = new stdClass();

        // Set recording_playback.
        $recordingplayback = new recording_row_playback($rec, $instance);
        $rowdata->playback = $renderer->render($recordingplayback);

        if (empty($instance)) {
            // Set activity name.
            $rowdata->recording = $rec->get('name');

            // Set activity description.
            $rowdata->description = $rec->get('description');
        } else {
            // Set activity name.
            $recordingname = new recording_name_editable($rec, $instance);
            $rowdata->recording = $renderer->render_inplace_editable($recordingname);
            // Set activity description.
            $recordingdescription = new recording_description_editable($rec, $instance);
            $rowdata->description = $renderer->render_inplace_editable($recordingdescription);
        }

        if ((!empty($instance) && self::preview_enabled($instance)) || $hascapabilityincourse) {
            // Set recording_preview.
            $rowdata->preview = '';
            if ($rec->get('playbacks')) {
                $rowpreview = new recording_row_preview($rec);
                $rowdata->preview = $renderer->render($rowpreview);
            }
        }
        // Set date.
        $starttime = $rec->get('starttime');
        $rowdata->date = !is_null($starttime) ? floatval($starttime) : 0;
        // Set duration.
        $rowdata->duration = self::row_duration($rec);
        // Set actionbar, if user is allowed to manage recordings.
        if ((!empty($instance) && $instance->can_manage_recordings()) || $hascapabilityincourse) {
            $actionbar = new recording_row_actionbar($rec, $tools);
            $rowdata->actionbar = $renderer->render($actionbar);
        }
        return $rowdata;
    }

    /**
     * Helper function evaluates if recording preview should be included.
     *
     * @param instance $instance
     * @return bool
     */
    public static function preview_enabled(instance $instance): bool {
        return $instance->get_instance_var('recordings_preview') == '1';
    }

    /**
     * Helper function converts recording duration used in row for the data used by the recording table.
     *
     * @param recording $recording
     * @return int
     */
    protected static function row_duration(recording $recording): int {
        $playbacks = $recording->get('playbacks');
        if (empty($playbacks)) {
            return 0;
        }
        foreach ($playbacks as $playback) {
            // Ignore restricted playbacks.
            if (array_key_exists('restricted', $playback) && strtolower($playback['restricted']) == 'true') {
                continue;
            }

            // Take the length form the fist playback with an actual value.
            if (!empty($playback['length'])) {
                return intval($playback['length']);
            }
        }
        return 0;
    }

    /**
     * Helper function to handle yet unknown recording types
     *
     * @param string $playbacktype : for now presentation, video, statistics, capture, notes, podcast
     * @return string the matching language string or a capitalised version of the provided string
     */
    public static function type_text(string $playbacktype): string {
        // Check first if string exists, and if it does not, just default to the capitalised version of the string.
        $text = ucwords($playbacktype);
        $typestringid = 'view_recording_format_' . $playbacktype;
        if (get_string_manager()->string_exists($typestringid, 'bigbluebuttonbn')) {
            $text = get_string($typestringid, 'bigbluebuttonbn');
        }
        return $text;
    }

    /**
     * Helper function evaluates if recording row should be included in the table.
     *
     * @param instance|null $instance
     * @param recording $rec a bigbluebuttonbn_recordings row
     * @return bool
     */
    protected static function include_recording_table_row(?instance $instance, recording $rec): bool {
        if (empty($instance)) {
            return roles::has_capability_in_course($rec->get('courseid'), 'mod/bigbluebuttonbn:managerecordings');
        }
        // Exclude unpublished recordings, only if user has no rights to manage them.
        if (!$rec->get('published') && !$instance->can_manage_recordings()) {
            return false;
        }
        // Imported recordings are always shown as long as they are published.
        if ($rec->get('imported')) {
            return true;
        }
        // When show imported recordings only is enabled, exclude all other recordings.
        if ($instance->get_recordings_imported() && !$rec->get('imported')) {
            return false;
        }
        // Administrators and moderators are always allowed.
        if ($instance->is_admin() || $instance->is_moderator()) {
            return true;
        }
        // When groups are enabled, exclude those to which the user doesn't have access to.
        if ($instance->uses_groups() && !$instance->can_manage_recordings()) {
            if (groups_get_activity_groupmode($instance->get_cm()) == VISIBLEGROUPS) {
                // In case we are in visible group mode, we show all recordings.
                return true;
            }
            // Else we check if the Recording group is the same as the instance. Instance group
            // being the group chosen for this instance.
            return intval($rec->get('groupid')) === $instance->get_group_id();
        }
        return true;
    }
}
