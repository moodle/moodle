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
 * mod_bigbluebuttonbn data generator
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2018 - present, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

use core\plugininfo\mod;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\recording;

/**
 * bigbluebuttonbn module data generator
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2018 - present, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class mod_bigbluebuttonbn_generator extends \testing_module_generator {

    /**
     * Creates an instance of bigbluebuttonbn for testing purposes.
     *
     * @param array|stdClass $record data for module being generated.
     * @param null|array $options general options for course module.
     * @return stdClass record from module-defined table with additional field cmid
     */
    public function create_instance($record = null, ?array $options = null) {
        // Prior to creating the instance, make sure that the BigBlueButton module is enabled.
        $modules = \core_plugin_manager::instance()->get_plugins_of_type('mod');
        if (!$modules['bigbluebuttonbn']->is_enabled()) {
            mod::enable_plugin('bigbluebuttonbn', true);
        }

        $now = time();
        $defaults = [
            "type" => 0,
            "meetingid" => sha1(rand()),
            "record" => true,
            "moderatorpass" => "mp",
            "viewerpass" => "ap",
            "participants" => "{}",
            "timecreated" => $now,
            "timemodified" => $now,
            "presentation" => null,
            "recordings_preview" => 0,
            "grade" => 0,
        ];

        $record = (array) $record;

        $record['participants'] = json_encode($this->get_participants_from_record($record));

        foreach ($defaults as $key => $value) {
            if (!isset($record[$key])) {
                $record[$key] = $value;
            }
        }
        if ($record['presentation']) {
            global $USER;
            // Here we replace the original presentation file with a draft area in which we store this file.
            $draftareaid = file_get_unused_draft_itemid();
            $bbbfilerecord['contextid'] = context_user::instance($USER->id)->id;
            $bbbfilerecord['component'] = 'user';
            $bbbfilerecord['filearea'] = 'draft';
            $bbbfilerecord['itemid'] = $draftareaid;
            $bbbfilerecord['filepath'] = '/';
            $bbbfilerecord['filename'] = basename($record['presentation']);
            $fs = get_file_storage();

            $fs->create_file_from_pathname($bbbfilerecord, $record['presentation']);
            // Now the $record['presentation'] must contain the draftareaid.
            $record['presentation'] = $draftareaid;
        }
        return parent::create_instance((object) $record, (array) $options);
    }

    /**
     * Create the participants field data from create_instance data.
     *
     * @param array $record
     * @return array
     */
    protected function get_participants_from_record(array $record): array {
        $roles = [];
        if (array_key_exists('moderators', $record) && !empty($record['moderators'])) {
            $roles = array_merge(
                $roles,
                $this->get_participant_configuration($record['moderators'], 'moderator')
            );
            unset($record['moderators']);
        }

        if (array_key_exists('viewers', $record) && !empty($record['viewers'])) {
            $roles = array_merge(
                $roles,
                $this->get_participant_configuration($record['viewers'], 'viewer')
            );
            unset($record['viewers']);
        }

        if (!empty($roles)) {
            array_unshift($roles, (object) [
                'selectiontype' => 'all',
                'selectionid' => 'all',
                'role' => 'viewer',
            ]);
        }

        return $roles;
    }

    /**
     * Get the participant configuration for a field and role for use in get_participants_from_record.
     *
     * @param string $field
     * @param string $role
     * @return array
     */
    protected function get_participant_configuration(string $field, string $role): array {
        global $DB;

        $values = explode(',', $field);

        $roles = $DB->get_records_menu('role', [], '', 'shortname, id');

        $configuration = [];
        foreach ($values as $value) {
            if (empty($value)) {
                // Empty value.
                continue;
            }
            [$type, $name] = explode(':', $value);

            $participant = (object) [
                'selectiontype' => $type,
                'role' => $role,
            ];
            switch ($type) {
                case 'role':
                    if (!array_key_exists($name, $roles)) {
                        throw new \coding_exception("Unknown role '{$name}'");
                    }
                    $participant->selectionid = $roles[$name];

                    break;
                case 'user':
                    $participant->selectionid = $DB->get_field('user', 'id', ['username' => $name], MUST_EXIST);
                    break;
                default:
                    throw new \coding_exception("Unknown participant type: '{$type}'");
            }

            $configuration[] = $participant;
        }

        return $configuration;
    }

    /**
     * Create a recording for the given bbb activity.
     *
     * The recording is created both locally, and a recording record is created on the mocked BBB server.
     *
     * @param array $data
     * @param bool $serveronly create it only on the server, not in the database.
     * @return stdClass the recording object
     */
    public function create_recording(array $data, $serveronly = false): stdClass {
        $instance = instance::get_from_instanceid($data['bigbluebuttonbnid']);

        if (isset($data['imported']) && filter_var($data['imported'], FILTER_VALIDATE_BOOLEAN)) {
            if (empty($data['importedid'])) {
                throw new moodle_exception('error');
            }
            $recording = recording::get_record(['recordingid' => $data['importedid']]);
            $recording->imported = true;
        } else {
            $recording = (object) [
                'headless' => false,
                'imported' => false,
                'status' => $data['status'] ?? recording::RECORDING_STATUS_NOTIFIED,
            ];
        }

        if (!empty($data['groupid'])) {
            $instance->set_group_id($data['groupid']);
            $recording->groupid = $data['groupid'];
        }

        $recording->bigbluebuttonbnid = $instance->get_instance_id();
        $recording->courseid = $instance->get_course_id();
        if (isset($options['imported']) && $options['imported']) {
            $precording = $recording->create_imported_recording($instance);
        } else {
            if ($recording->status == recording::RECORDING_STATUS_DISMISSED) {
                $recording->recordingid = sprintf(
                    "%s-%s",
                    md5($instance->get_meeting_id()),
                    time() + rand(1, 100000)
                );
            } else {
                $recording->recordingid = $this->create_mockserver_recording($instance, $recording, $data);
            }
            $precording = new recording(0, $recording);
            if (!$serveronly) {
                $precording->create();
            }
        }
        return $precording->to_record();
    }

    /**
     * Add a recording in the mock server
     *
     * @param instance $instance
     * @param stdClass $recordingdata
     * @param array $data
     * @return string
     */
    protected function create_mockserver_recording(instance $instance, stdClass $recordingdata, array $data): string {
        $now = time();
        $mockdata = array_merge((array) $recordingdata, [
            'sequence' => 1,
            'meta' => [
                'bn-presenter-name' => $data['presentername'] ?? 'Fake presenter',
                'bbb-recording-ready-url' => new moodle_url('/mod/bigbluebuttonbn/bbb_broker.php', [
                    'action' => 'recording_ready',
                    'bigbluebuttonbn' => $instance->get_instance_id()
                ]),
                'bbb-recording-description' => $data['description'] ?? '',
                'bbb-recording-name' => $data['name'] ?? '',
                'bbb-recording-tags' => $data['tags'] ?? '',
            ],
        ]);
        $mockdata['startTime'] = $data['starttime'] ?? $now;
        $mockdata['endTime'] = $data['endtime'] ?? $mockdata['startTime'] + HOURSECS;
        if (!empty($data['playback'])) {
            $mockdata['playback'] = json_encode($data['playback']);
        }
        if (!empty($data['isBreakout'])) {
            // If it is a breakout meeting, we do not have any way to know the real Id of the meeting
            // unless we query the list of submeetings.
            // For now we will just send the parent ID and let the mock server deal with the sequence + parentID
            // to find the meetingID.
            $mockdata['parentMeetingID'] = $instance->get_meeting_id();
        } else {
            $mockdata['meetingID'] = $instance->get_meeting_id();
        }

        $result = $this->send_mock_request('backoffice/createRecording', [], $mockdata);

        return (string) $result->recordID;
    }

    /**
     * Utility to send a request to the mock server
     *
     * @param string $endpoint
     * @param array $params
     * @param array $mockdata
     * @return SimpleXMLElement|bool
     * @throws moodle_exception
     */
    protected function send_mock_request(string $endpoint, array $params = [], array $mockdata = []): SimpleXMLElement {
        $url = $this->get_mocked_server_url($endpoint, $params);

        foreach ($mockdata as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subvalue) {
                    $paramname = "{$key}_{$subkey}";
                    $url->param($paramname, $subvalue);
                }
            } else {
                $url->param($key, $value);
            }
        }

        $curl = new \curl();
        $result = $curl->get($url->out_omit_querystring(), $url->params());

        $retvalue = @simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if ($retvalue === false) {
            throw new moodle_exception('mockserverconnfailed', 'mod_bigbluebutton');
        }
        return $retvalue;
    }

    /**
     * Get a URL for a mocked BBB server endpoint.
     *
     * @param string $endpoint
     * @param array $params
     * @return moodle_url
     */
    protected function get_mocked_server_url(string $endpoint = '', array $params = []): moodle_url {
        return new moodle_url(TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER . '/' . $endpoint, $params);
    }

    /**
     * Mock an in-progress meeting on the remote server.
     *
     * @param array $data
     * @return stdClass
     */
    public function create_meeting(array $data): stdClass {
        $instance = instance::get_from_instanceid($data['instanceid']);

        if (array_key_exists('groupid', $data)) {
            $instance = instance::get_group_instance_from_instance($instance, $data['groupid']);
        }

        $meetingid = $instance->get_meeting_id();

        // Default room configuration.
        $roomconfig = array_merge($data, [
            'meetingName' => $instance->get_meeting_name(),
            'attendeePW' => $instance->get_viewer_password(),
            'moderatorPW' => $instance->get_moderator_password(),
            'voiceBridge' => $instance->get_voice_bridge(),
            'meta' => [
                'bbb-context' => $instance->get_course()->fullname,
                'bbb-context-id' => $instance->get_course()->id,
                'bbb-context-label' => $instance->get_course()->shortname,
                'bbb-context-name' => $instance->get_course()->fullname,
                'bbb-origin' => 'Moodle',
                'bbb-origin-tag' => 'moodle-mod_bigbluebuttonbn (TODO version)',
                'bbb-recording-description' => $instance->get_meeting_description(),
                'bbb-recording-name' => $instance->get_meeting_name(),
            ],
        ]);
        if ((boolean) config::get('recordingready_enabled')) {
            $roomconfig['meta']['bbb-recording-ready-url'] = $instance->get_record_ready_url()->out(false);
        }
        if ((boolean) config::get('meetingevents_enabled')) {
            $roomconfig['meta']['analytics-callback-url'] = $instance->get_meeting_event_notification_url()->out(false);
        }
        if (!empty($roomconfig['isBreakout'])) {
            // If it is a breakout meeting, we do not have any way to know the real Id of the meeting
            // For now we will just send the parent ID and let the mock server deal with the sequence + parentID
            // to find the meetingID.
            $roomconfig['parentMeetingID'] = $instance->get_meeting_id();
        } else {
            $roomconfig['meetingID'] = $meetingid;
        }
        $this->send_mock_request('backoffice/createMeeting', [], $roomconfig);
        return (object) $roomconfig;
    }

    /**
     * Create a log record
     *
     * @param mixed $record
     * @param array|null $options
     */
    public function create_log($record, ?array $options = null) {
        $instance = instance::get_from_instanceid($record['bigbluebuttonbnid']);

        $record = array_merge([
            'meetingid' => $instance->get_meeting_id(),
        ], (array) $record);

        $testlogclass = new class extends logger {
            /**
             * Log test event
             *
             * @param instance $instance
             * @param array $record
             */
            public static function log_test_event(instance $instance, array $record): void {
                self::log(
                    $instance,
                    logger::EVENT_CREATE,
                    $record
                );
            }
        };

        $testlogclass::log_test_event($instance, $record);
    }

    /**
     * Set a value in the Mock server
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws moodle_exception
     */
    public function set_value(string $name, $value): void {
        if (defined('TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER')) {
            $this->send_mock_request('backoffice/set', [], ['name' => $name, 'value' => json_encode($value)]);
        }
    }

    /**
     * Trigger a meeting event on BBB side
     *
     * @param object $user
     * @param instance $instance
     * @param string $eventtype
     * @param string|null $eventdata
     * @return void
     */
    public function add_meeting_event(object $user, instance $instance, string $eventtype, string $eventdata = ''): void {
        $this->send_mock_request('backoffice/addMeetingEvent', [
                'secret' => \mod_bigbluebuttonbn\local\config::DEFAULT_SHARED_SECRET,
                'meetingID' => $instance->get_meeting_id(),
                'attendeeID' => $user->id,
                'attendeeName' => fullname($user),
                'eventType' => $eventtype,
                'eventData' => $eventdata
            ]
        );
    }

    /**
     * Send all previously store events
     *
     * @param instance $instance
     * @return object|null
     */
    public function send_all_events(instance $instance): ?object {
        if (defined('TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER')) {
            return $this->send_mock_request('backoffice/sendAllEvents', [
                'meetingID' => $instance->get_meeting_id(),
                'sendQuery' => false, // We get the result directly here.
                'secret' => \mod_bigbluebuttonbn\local\config::DEFAULT_SHARED_SECRET,
            ]);
        }
        return null;
    }

    /**
     * Reset the mock server
     */
    public function reset_mock(): void {
        if (defined('TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER')) {
            $this->send_mock_request('backoffice/reset');
        }
    }
}
