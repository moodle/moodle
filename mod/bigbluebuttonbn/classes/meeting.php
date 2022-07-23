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

use cache;
use cache_store;
use context_course;
use core_tag_tag;
use Exception;
use Firebase\JWT\Key;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\local\exceptions\bigbluebutton_exception;
use mod_bigbluebuttonbn\local\exceptions\meeting_join_exception;
use mod_bigbluebuttonbn\local\helpers\roles;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use stdClass;

/**
 * Class to describe a BBB Meeting.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class meeting {

    /** @var instance The bbb instance */
    protected $instance;

    /** @var stdClass Info about the meeting */
    protected $meetinginfo = null;

    /**
     * Constructor for the meeting object.
     *
     * @param instance $instance
     */
    public function __construct(instance $instance) {
        $this->instance = $instance;
    }

    /**
     * Helper to join a meeting.
     *
     *
     * It will create the meeting if not already created.
     *
     * @param instance $instance
     * @param int $origin
     * @return string
     * @throws meeting_join_exception this is sent if we cannot join (meeting full, user needs to wait...)
     */
    public static function join_meeting(instance $instance, $origin = logger::ORIGIN_BASE): string {
        // See if the session is in progress.
        $meeting = new meeting($instance);
        // As the meeting doesn't exist, try to create it.
        if (empty($meeting->get_meeting_info(true)->createtime)) {
            $meeting->create_meeting();
        }
        return $meeting->join($origin);
    }

    /**
     * Get currently stored meeting info
     *
     * @return mixed|stdClass
     */
    public function get_meeting_info() {
        if (!$this->meetinginfo) {
            $this->meetinginfo = $this->do_get_meeting_info();
        }
        return $this->meetinginfo;
    }

    /**
     * Return meeting information for the specified instance.
     *
     * @param instance $instance
     * @param bool $updatecache Whether to update the cache when fetching the information
     * @return stdClass
     */
    public static function get_meeting_info_for_instance(instance $instance, bool $updatecache = false): stdClass {
        $meeting = new self($instance);
        return $meeting->do_get_meeting_info($updatecache);
    }

    /**
     * Helper function returns a sha1 encoded string that is unique and will be used as a seed for meetingid.
     *
     * @return string
     */
    public static function get_unique_meetingid_seed() {
        global $DB;
        do {
            $encodedseed = sha1(plugin::random_password(12));
            $meetingid = (string) $DB->get_field('bigbluebuttonbn', 'meetingid', ['meetingid' => $encodedseed]);
        } while ($meetingid == $encodedseed);
        return $encodedseed;
    }

    /**
     * Is meeting running ?
     *
     * @return bool
     */
    public function is_running() {
        return $this->get_meeting_info()->statusrunning ?? false;
    }

    /**
     * Force update the meeting in cache.
     */
    public function update_cache() {
        $this->meetinginfo = $this->do_get_meeting_info(true);
    }

    /**
     * Get meeting attendees
     *
     * @return mixed
     */
    public function get_attendees() {
        return $this->get_meeting_info()->attendees ?? [];
    }

    /**
     * Can the meeting be joined ?
     *
     * @return bool
     */
    public function can_join() {
        return $this->get_meeting_info()->canjoin;
    }

    /**
     * Number of participants
     *
     * @return int
     */
    public function get_participant_count() {
        return $this->get_meeting_info()->participantcount;
    }

    /**
     * Creates a bigbluebutton meeting, send the message to BBB and returns the response in an array.
     *
     * @return array
     */
    public function create_meeting() {
        $data = $this->create_meeting_data();
        $metadata = $this->create_meeting_metadata();
        $presentation = $this->instance->get_presentation_for_bigbluebutton_upload(); // The URL must contain nonce.
        $presentationname = $presentation['name'] ?? null;
        $presentationurl = $presentation['url'] ?? null;
        $response = bigbluebutton_proxy::create_meeting($data, $metadata, $presentationname, $presentationurl);
        // New recording management: Insert a recordingID that corresponds to the meeting created.
        if ($this->instance->is_recorded()) {
            $recording = new recording(0, (object) [
                'courseid' => $this->instance->get_course_id(),
                'bigbluebuttonbnid' => $this->instance->get_instance_id(),
                'recordingid' => $response['internalMeetingID'],
                'groupid' => $this->instance->get_group_id()]
            );
            $recording->create();
        }
        return $response;
    }

    /**
     * Send an end meeting message to BBB server
     */
    public function end_meeting() {
        bigbluebutton_proxy::end_meeting($this->instance->get_meeting_id(), $this->instance->get_moderator_password());
    }

    /**
     * Get meeting join URL
     *
     * @return string
     */
    public function get_join_url() {
        return bigbluebutton_proxy::get_join_url(
            $this->instance->get_meeting_id(),
            $this->instance->get_user_fullname(),
            $this->instance->get_current_user_password(),
            $this->instance->get_logout_url()->out(false),
            null,
            $this->instance->get_user_id(),
            $this->get_meeting_info()->createtime
        );
    }

    /**
     * Return meeting information for this meeting.
     *
     * @param bool $updatecache Whether to update the cache when fetching the information
     * @return stdClass
     */
    protected function do_get_meeting_info(bool $updatecache = false): stdClass {
        $instance = $this->instance;
        $meetinginfo = (object) [
            'instanceid' => $instance->get_instance_id(),
            'bigbluebuttonbnid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'meetingid' => $instance->get_meeting_id(),
            'cmid' => $instance->get_cm_id(),
            'ismoderator' => $instance->is_moderator(),
            'joinurl' => $instance->get_join_url()->out(),
            'userlimit' => $instance->get_user_limit(),
            'presentations' => [],
        ];
        if ($instance->get_instance_var('openingtime')) {
            $meetinginfo->openingtime = intval($instance->get_instance_var('openingtime'));
        }
        if ($instance->get_instance_var('closingtime')) {
            $meetinginfo->closingtime = intval($instance->get_instance_var('closingtime'));
        }
        $activitystatus = bigbluebutton_proxy::view_get_activity_status($instance);
        // This might raise an exception if info cannot be retrieved.
        // But this might be totally fine as the meeting is maybe not yet created on BBB side.
        $participantcount = 0;
        // This is the default value for any meeting that has not been created.
        $meetinginfo->statusrunning = false;
        $meetinginfo->createtime = null;

        $info = self::retrieve_cached_meeting_info($this->instance->get_meeting_id(), $updatecache);
        if (!empty($info)) {
            $meetinginfo->statusrunning = $info['running'] === 'true';
            $meetinginfo->createtime = $info['createTime'] ?? null;
            $participantcount = isset($info['participantCount']) ? $info['participantCount'] : 0;
        }

        $meetinginfo->statusclosed = $activitystatus === 'ended';
        $meetinginfo->statusopen = !$meetinginfo->statusrunning && $activitystatus === 'open';
        $meetinginfo->participantcount = $participantcount;

        $canjoin = !$instance->user_must_wait_to_join() || $meetinginfo->statusrunning;
        // Limit has not been reached or user does not count toward limit.
        $canjoin = $canjoin && (
            !$instance->has_user_limit_been_reached($participantcount)
            || !$instance->does_current_user_count_towards_user_limit()
            );
        $canjoin = $canjoin && ($instance->is_currently_open() || $instance->user_can_force_join());
        // Double check that the user has the capabilities to join.
        $canjoin = $canjoin && $instance->can_join();
        $meetinginfo->canjoin = $canjoin;

        // If user is administrator, moderator or if is viewer and no waiting is required, join allowed.
        if ($meetinginfo->statusrunning) {
            $meetinginfo->startedat = floor(intval($info['startTime']) / 1000); // Milliseconds.
            $meetinginfo->moderatorcount = $info['moderatorCount'];
            $meetinginfo->moderatorplural = $info['moderatorCount'] > 1;
            $meetinginfo->participantcount = $participantcount - $meetinginfo->moderatorcount;
            $meetinginfo->participantplural = $meetinginfo->participantcount > 1;
        }
        $meetinginfo->statusmessage = $this->get_status_message($meetinginfo, $instance);

        $presentation = $instance->get_presentation(); // This is for internal use.
        if (!empty($presentation)) {
            $meetinginfo->presentations[] = $presentation;
        }
        $meetinginfo->attendees = [];
        if (!empty($info['attendees'])) {
            // Make sure attendees is an array of object, not a simpleXML object.
            foreach ($info['attendees'] as $attendee) {
                $meetinginfo->attendees[] = (array) $attendee;
            }
        }
        return $meetinginfo;
    }

    /**
     * Deduce status message from the current meeting info and the instance
     *
     * Returns the human-readable message depending on if the user must wait to join, the meeting has not
     * yet started ...
     * @param object $meetinginfo
     * @param instance $instance
     * @return string
     */
    protected function get_status_message(object $meetinginfo, instance $instance): string {
        if ($meetinginfo->statusrunning) {
            return get_string('view_message_conference_in_progress', 'bigbluebuttonbn');
        }
        if ($instance->user_must_wait_to_join() && !$instance->user_can_force_join()) {
            return get_string('view_message_conference_wait_for_moderator', 'bigbluebuttonbn');
        }
        if ($instance->before_start_time()) {
            return get_string('view_message_conference_not_started', 'bigbluebuttonbn');
        }
        if ($instance->has_ended()) {
            return get_string('view_message_conference_has_ended', 'bigbluebuttonbn');
        }
        return get_string('view_message_conference_room_ready', 'bigbluebuttonbn');
    }

    /**
     * Gets a meeting info object cached or fetched from the live session.
     *
     * @param string $meetingid
     * @param bool $updatecache
     *
     * @return array
     */
    protected static function retrieve_cached_meeting_info($meetingid, $updatecache = false) {
        $cachettl = (int) config::get('waitformoderator_cache_ttl');
        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'mod_bigbluebuttonbn', 'meetings_cache');
        $result = $cache->get($meetingid);
        $now = time();
        if (!$updatecache && !empty($result) && $now < ($result['creation_time'] + $cachettl)) {
            // Use the value in the cache.
            return (array) json_decode($result['meeting_info']);
        }
        // We set the cache to an empty value so then if get_meeting_info raises an exception we still have the
        // info about the last creation_time, so we don't ask the server again for a bit.
        $defaultcacheinfo = ['creation_time' => time(), 'meeting_info' => '[]'];
        // Pings again and refreshes the cache.
        try {
            $meetinginfo = bigbluebutton_proxy::get_meeting_info($meetingid);
            $cache->set($meetingid, ['creation_time' => time(), 'meeting_info' => json_encode($meetinginfo)]);
        } catch (bigbluebutton_exception $e) {
            // The meeting is not created on BBB side, so we set the value in the cache so we don't poll again
            // and return an empty array.
            $cache->set($meetingid, $defaultcacheinfo);
            return [];
        }
        return $meetinginfo;
    }

    /**
     * Conversion between form settings and lockSettings as set in BBB API.
     */
    const LOCK_SETTINGS_MEETING_DATA = [
        'disablecam' => 'lockSettingsDisableCam',
        'disablemic' => 'lockSettingsDisableMic',
        'disableprivatechat' => 'lockSettingsDisablePrivateChat',
        'disablepublicchat' => 'lockSettingsDisablePublicChat',
        'disablenote' => 'lockSettingsDisableNote',
        'lockonjoin' => 'lockSettingsLockOnJoin',
        'hideuserlist' => 'lockSettingsHideUserList'
    ];
    /**
     * Helper to prepare data used for create meeting.
     *
     * @return array
     */
    protected function create_meeting_data() {
        $data = ['meetingID' => $this->instance->get_meeting_id(),
            'name' => \mod_bigbluebuttonbn\plugin::html2text($this->instance->get_meeting_name(), 64),
            'attendeePW' => $this->instance->get_viewer_password(),
            'moderatorPW' => $this->instance->get_moderator_password(),
            'logoutURL' => $this->instance->get_logout_url()->out(false),
        ];
        $data['record'] = $this->instance->should_record() ? 'true' : 'false';
        // Check if auto_start_record is enable.
        if ($data['record'] == 'true' && $this->instance->should_record_from_start()) {
            $data['autoStartRecording'] = 'true';
        }
        // Check if hide_record_button is enable.
        if (!$this->instance->should_show_recording_button()) {
            $data['allowStartStopRecording'] = 'false';
        }
        $data['welcome'] = trim($this->instance->get_welcome_message());
        $voicebridge = intval($this->instance->get_voice_bridge());
        if ($voicebridge > 0 && $voicebridge < 79999) {
            $data['voiceBridge'] = $voicebridge;
        }
        $maxparticipants = intval($this->instance->get_user_limit());
        if ($maxparticipants > 0) {
            $data['maxParticipants'] = $maxparticipants;
        }
        if ($this->instance->get_mute_on_start()) {
            $data['muteOnStart'] = 'true';
        }
        // Locks settings.
        foreach (self::LOCK_SETTINGS_MEETING_DATA as $instancevarname => $lockname) {
            $instancevar = $this->instance->get_instance_var($instancevarname);
            if (!is_null($instancevar)) {
                $data[$lockname] = $instancevar ? 'true' : 'false';
            }
        }
        return $data;
    }

    /**
     * Helper for preparing metadata used while creating the meeting.
     *
     * @return array
     */
    protected function create_meeting_metadata() {
        global $USER;
        // Create standard metadata.
        $origindata = $this->instance->get_origin_data();
        $metadata = [
            'bbb-origin' => $origindata->origin,
            'bbb-origin-version' => $origindata->originVersion,
            'bbb-origin-server-name' => $origindata->originServerName,
            'bbb-origin-server-common-name' => $origindata->originServerCommonName,
            'bbb-origin-tag' => $origindata->originTag,
            'bbb-context' => $this->instance->get_course()->fullname,
            'bbb-context-id' => $this->instance->get_course_id(),
            'bbb-context-name' => trim(html_to_text($this->instance->get_course()->fullname, 0)),
            'bbb-context-label' => trim(html_to_text($this->instance->get_course()->shortname, 0)),
            'bbb-recording-name' => plugin::html2text($this->instance->get_meeting_name(), 64),
            'bbb-recording-description' => plugin::html2text($this->instance->get_meeting_description(),
                64),
            'bbb-recording-tags' =>
                implode(',', core_tag_tag::get_item_tags_array('core',
                    'course_modules', $this->instance->get_cm_id())), // Same as $id.
            'bbb-meeting-size-hint' =>
                count_enrolled_users(context_course::instance($this->instance->get_course_id()),
                    '', $this->instance->get_group_id()),
        ];
        // Special metadata for recording processing.
        if ((boolean) config::get('recordingstatus_enabled')) {
            $metadata["bn-recording-status"] = json_encode(
                [
                    'email' => ['"' . fullname($USER) . '" <' . $USER->email . '>'],
                    'context' => $this->instance->get_view_url(),
                ]
            );
        }
        if ((boolean) config::get('recordingready_enabled')) {
            $metadata['bn-recording-ready-url'] = $this->instance->get_record_ready_url()->out(false);
        }
        if ((boolean) config::get('meetingevents_enabled')) {
            $metadata['analytics-callback-url'] = $this->instance->get_meeting_event_notification_url()->out(false);
        }
        return $metadata;
    }

    /**
     * Helper for responding when storing live meeting events is requested.
     *
     * The callback with a POST request includes:
     *  - Authentication: Bearer <A JWT token containing {"exp":<TIMESTAMP>} encoded with HS512>
     *  - Content Type: application/json
     *  - Body: <A JSON Object>
     *
     * @param instance $instance
     * @return void
     */
    public static function meeting_events(instance $instance) {
        $bigbluebuttonbn = $instance->get_instance_data();
        // Decodes the received JWT string.
        try {
            // Get the HTTP headers (getallheaders is a PHP function that may only work with Apache).
            $headers = getallheaders();

            // Pull the Bearer from the headers.
            if (!array_key_exists('Authorization', $headers)) {
                $msg = 'Authorization failed';
                header('HTTP/1.0 400 Bad Request. ' . $msg);
                return;
            }
            $authorization = explode(" ", $headers['Authorization']);

            // Verify the authenticity of the request.
            $token = \Firebase\JWT\JWT::decode(
                $authorization[1],
                new Key(config::get('shared_secret'), 'HS512')
            );

            // Get JSON string from the body.
            $jsonstr = file_get_contents('php://input');

            // Convert JSON string to a JSON object.
            $jsonobj = json_decode($jsonstr);
        } catch (Exception $e) {
            $msg = 'Caught exception: ' . $e->getMessage();
            header('HTTP/1.0 400 Bad Request. ' . $msg);
            return;
        }

        // Validate that the bigbluebuttonbn activity corresponds to the meeting_id received.
        $meetingidelements = explode('[', $jsonobj->{'meeting_id'});
        $meetingidelements = explode('-', $meetingidelements[0]);
        if (!isset($bigbluebuttonbn) || $bigbluebuttonbn->meetingid != $meetingidelements[0]) {
            $msg = 'The activity may have been deleted';
            header('HTTP/1.0 410 Gone. ' . $msg);
            return;
        }

        // We make sure events are processed only once.
        $overrides = ['meetingid' => $jsonobj->{'meeting_id'}];
        $meta['recordid'] = $jsonobj->{'internal_meeting_id'};
        $meta['callback'] = 'meeting_events';

        $eventcount = logger::log_event_callback($instance, $overrides, $meta);
        if ($eventcount === 1) {
            // Process the events.
            self::process_meeting_events($instance, $jsonobj);
            header('HTTP/1.0 200 Accepted. Enqueued.');
        } else {
            header('HTTP/1.0 202 Accepted. Already processed.');
        }
    }

    /**
     * Helper function enqueues list of meeting events to be stored and processed as for completion.
     *
     * @param instance $instance
     * @param stdClass $jsonobj
     */
    protected static function process_meeting_events(instance $instance, stdClass $jsonobj) {
        $meetingid = $jsonobj->{'meeting_id'};
        $recordid = $jsonobj->{'internal_meeting_id'};
        $attendees = $jsonobj->{'data'}->{'attendees'};
        foreach ($attendees as $attendee) {
            $userid = $attendee->{'ext_user_id'};
            $overrides['meetingid'] = $meetingid;
            $overrides['userid'] = $userid;
            $meta['recordid'] = $recordid;
            $meta['data'] = $attendee;

            // Stores the log.
            logger::log_event_summary($instance, $overrides, $meta);

            // Enqueue a task for processing the completion.
            bigbluebutton_proxy::enqueue_completion_event($instance->get_instance_data(), $userid);
        }
    }

    /**
     * Join a meeting.
     *
     * @param int $origin The spec
     * @return string The URL to redirect to
     * @throws meeting_join_exception
     */
    public function join(int $origin): string {
        $this->do_get_meeting_info(true);
        if ($this->is_running()) {
            if (
                $this->instance->has_user_limit_been_reached($this->get_participant_count())
                && $this->instance->does_current_user_count_towards_user_limit()
            ) {
                throw new meeting_join_exception('userlimitreached');
            }
        } else if ($this->instance->user_must_wait_to_join()) {
            // If user is not administrator nor moderator (user is student) and waiting is required.
            throw new meeting_join_exception('waitformoderator');
        }

        // Moodle event logger: Create an event for meeting joined.
        logger::log_meeting_joined_event($this->instance, $origin);

        // Before executing the redirect, increment the number of participants.
        roles::participant_joined($this->instance->get_meeting_id(), $this->instance->is_moderator());
        return $this->get_join_url();
    }
}
