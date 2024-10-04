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

use mod_bigbluebuttonbn\event\events;
use stdClass;

/**
 * Utility class for all logs routines helper.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */
class logger {

    /** @var string The bigbluebuttonbn Add event */
    public const EVENT_ADD = 'Add';

    /** @var string The bigbluebuttonbn Edit event */
    public const EVENT_EDIT = 'Edit';

    /** @var string The bigbluebuttonbn Create event */
    public const EVENT_CREATE = 'Create';

    /** @var string The bigbluebuttonbn Join event */
    public const EVENT_JOIN = 'Join';

    /** @var string The bigbluebuttonbn Playback event */
    public const EVENT_PLAYED = 'Played';

    /** @var string The bigbluebuttonbn Logout event */
    public const EVENT_LOGOUT = 'Logout';

    /** @var string The bigbluebuttonbn Import event */
    public const EVENT_IMPORT = 'Import';

    /** @var string The bigbluebuttonbn Delete event */
    public const EVENT_DELETE = 'Delete';

    /** @var string The bigbluebuttonbn Callback event */
    public const EVENT_CALLBACK = 'Callback';

    /** @var string The bigbluebuttonbn Summary event */
    public const EVENT_SUMMARY = 'Summary';

    /** @var string This is a specific log to mark this log as upgraded: used only in the upgrade process from 2.4
     *
     * Note: Migrated event name change: once a log has been migrated we mark
     * it as migrated by changing its log name. This will help to recover
     * manually if we have an issue in the migration process.
     */
    public const EVENT_IMPORT_MIGRATED = 'import-migrated';

    /** @var string This is a specific log to mark this log as upgraded: used only in the upgrade process from 2.4 */
    public const EVENT_CREATE_MIGRATED = 'create-migrated';

    /** @var string The bigbluebuttonbn meeting_start event */
    public const EVENT_MEETING_START = 'meeting_start';

    /** @var int The user accessed the session from activity page */
    public const ORIGIN_BASE = 0;

    /** @var int The user accessed the session from Timeline */
    public const ORIGIN_TIMELINE = 1;

    /** @var int The user accessed the session from Index */
    public const ORIGIN_INDEX = 2;

    /**
     * Get the user event logs related to completion, for the specified user in the named instance.
     *
     * @param instance $instance
     * @param int|null $userid
     * @param array|null $filters
     * @param int|null $timestart
     * @return array
     */
    public static function get_user_completion_logs(
        instance $instance,
        ?int $userid,
        ?array $filters,
        ?int $timestart = 0
    ): array {
        global $DB;
        $filters = $filters ?? [self::EVENT_JOIN, self::EVENT_PLAYED, self::EVENT_SUMMARY];
        [$wheresql, $params] = static::get_user_completion_sql_params($instance, $userid, $filters, $timestart);
        return $DB->get_records_select('bigbluebuttonbn_logs', $wheresql, $params);
    }

    /**
     * Get the user event logs related to completion, for the specified user in the named instance.
     *
     * @param instance $instance
     * @param int|null $userid
     * @param array|null $filters
     * @param int|null $timestart
     * @return array
     */
    public static function get_user_completion_logs_with_userfields(
        instance $instance,
        ?int $userid,
        ?array $filters,
        ?int $timestart = 0
    ): array {
        global $DB;
        $filters = $filters ?? [self::EVENT_JOIN, self::EVENT_PLAYED, self::EVENT_SUMMARY];
        [$wheresql, $params] = static::get_user_completion_sql_params($instance, $userid, $filters, $timestart, 'l');
        $userfieldsapi = \core_user\fields::for_userpic();
        $userfields = $userfieldsapi->get_sql('u', false, '', 'userid', false)->selects;
        $logtable = new \core\dml\table('bigbluebuttonbn_logs', 'l', '');
        $logtableselect = $logtable->get_field_select();
        $logtablefrom = $logtable->get_from_sql();
        $usertable = new \core\dml\table('user', 'u', '');
        $usertablefrom = $usertable->get_from_sql();
        $sql = <<<EOF
            SELECT {$logtableselect}, {$userfields}
              FROM {$logtablefrom}
        INNER JOIN {$usertablefrom} ON u.id = l.userid
             WHERE $wheresql
EOF;
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get the latest timestamp for any event logs related to completion, for the specified user in the named instance.
     *
     * @param instance $instance
     * @param int|null $userid
     * @param array|null $filters
     * @param int|null $timestart
     * @return int
     */
    public static function get_user_completion_logs_max_timestamp(
        instance $instance,
        ?int $userid,
        ?array $filters,
        ?int $timestart = 0
    ): int {
        global $DB;

        [$wheresql, $params] = static::get_user_completion_sql_params($instance, $userid, $filters, $timestart);
        $select = "SELECT MAX(timecreated) ";
        $lastlogtime = $DB->get_field_sql($select . ' FROM {bigbluebuttonbn_logs} WHERE ' . $wheresql, $params);
        return $lastlogtime ?? 0;
    }

    /**
     * Helper method to get the right SQL query for completion
     *
     * @param instance $instance
     * @param int|null $userid
     * @param array|null $filters
     * @param int|null $timestart
     * @param string|null $logtablealias
     * @return array
     */
    protected static function get_user_completion_sql_params(instance $instance, ?int $userid, ?array $filters, ?int $timestart,
        ?string $logtablealias = null) {
        global $DB;
        $filters = $filters ?? [self::EVENT_JOIN, self::EVENT_PLAYED, self::EVENT_SUMMARY];
        [$insql, $params] = $DB->get_in_or_equal($filters, SQL_PARAMS_NAMED);
        $wheres = [];
        $wheres['bigbluebuttonbnid'] = '= :instanceid';
        $wheres['courseid'] = '= :courseid'; // This speeds up the requests masively as courseid is an index.
        if ($timestart) {
            $wheres['timecreated'] = ' > :timestart';
            $params['timestart'] = $timestart;
        }
        if ($userid) {
            $wheres['userid'] = ' = :userid';
            $params['userid'] = $userid;
        }
        $params['instanceid'] = $instance->get_instance_id();
        $params['courseid'] = $instance->get_course_id();
        $wheres['log'] = " $insql";
        $wheresqls = [];
        foreach ($wheres as $key => $val) {
            $prefix = !empty($logtablealias) ? "$logtablealias." : "";
            $wheresqls[] = "$prefix$key $val";
        }
        return [join(' AND ', $wheresqls), $params];
    }

    /**
     * Log that an instance was created.
     *
     * Note: This event cannot take the instance class as it is typically called before the cm has been configured.
     *
     * @param stdClass $instancedata
     */
    public static function log_instance_created(stdClass $instancedata): void {
        self::raw_log(
            self::EVENT_ADD,
            $instancedata->id,
            $instancedata->course,
            $instancedata->meetingid
        );
    }

    /**
     * Log that an instance was updated.
     *
     * @param instance $instance
     */
    public static function log_instance_updated(instance $instance): void {
        self::log($instance, self::EVENT_EDIT);
    }

    /**
     * Log an instance deleted event.
     *
     * @param instance $instance
     */
    public static function log_instance_deleted(instance $instance): void {
        global $DB;

        $wheresql = 'bigbluebuttonbnid = :instanceid AND log = :logtype AND ' . $DB->sql_compare_text('meta') . ' = :meta';
        $logs = $DB->get_records_select('bigbluebuttonbn_logs', $wheresql, [
            'instanceid' => $instance->get_instance_id(),
            'logtype' => self::EVENT_CREATE,
            'meta' => "{\"record\":true}"
        ]);

        $meta = "{\"has_recordings\":" . empty($logs) ? "true" : "false" . "}";
        self::log($instance, self::EVENT_DELETE, [], $meta);
    }

    /**
     * Log an event callback.
     *
     * @param instance $instance
     * @param array $overrides
     * @param array $meta
     * @return int The new count of callback events
     */
    public static function log_event_callback(instance $instance, array $overrides, array $meta): int {
        self::log(
            $instance,
            self::EVENT_CALLBACK,
            $overrides,
            json_encode($meta)
        );

        return self::count_callback_events($meta['internalmeetingid'], 'meeting_events');
    }

    /**
     * Log an event summary event.
     *
     * @param instance $instance
     * @param array $overrides
     * @param array $meta
     */
    public static function log_event_summary(instance $instance, array $overrides = [], array $meta = []): void {
        self::log(
            $instance,
            self::EVENT_SUMMARY,
            $overrides,
            json_encode($meta)
        );
    }

    /**
     * Log that an instance was viewed.
     *
     * @param instance $instance
     */
    public static function log_instance_viewed(instance $instance): void {
        self::log_moodle_event($instance, events::$events['view']);
    }

    /**
     * Log the events for when a meeting was ended.
     *
     * @param instance $instance
     */
    public static function log_meeting_ended_event(instance $instance): void {
        // Moodle event logger: Create an event for meeting ended.
        self::log_moodle_event($instance, events::$events['meeting_end']);

    }

    /**
     * Log the relevant events for when a meeting was joined.
     *
     * @param instance $instance
     * @param int $origin
     */
    public static function log_meeting_joined_event(instance $instance, int $origin): void {
        // Moodle event logger: Create an event for meeting joined.
        self::log_moodle_event($instance, events::$events['meeting_join']);

        // Internal logger: Instert a record with the meeting created.
        self::log(
            $instance,
            self::EVENT_JOIN,
            ['meetingid' => $instance->get_meeting_id()],
            json_encode((object) ['origin' => $origin])
        );
    }

    /**
     * Log the relevant events for when a user left a meeting.
     *
     * @param instance $instance
     */
    public static function log_meeting_left_event(instance $instance): void {
        // Moodle event logger: Create an event for meeting left.
        self::log_moodle_event($instance, events::$events['meeting_left']);
    }

    /**
     * Log the relevant events for when a recording has been played.
     *
     * @param instance $instance
     * @param int $rid RecordID
     */
    public static function log_recording_played_event(instance $instance, int $rid): void {
        // Moodle event logger: Create an event for recording played.
        self::log_moodle_event($instance, events::$events['recording_play'], ['other' => $rid]);

        // Internal logger: Insert a record with the playback played.
        self::log(
            $instance,
            self::EVENT_PLAYED,
            [
                'meetingid' => $instance->get_meeting_id(),
            ],
            json_encode(['recordingid' => $rid])
        );
    }

    /**
     * Register a bigbluebuttonbn event from an instance.
     *
     * @param instance $instance
     * @param string $event
     * @param array $overrides
     * @param string|null $meta
     * @return bool
     */
    protected static function log(instance $instance, string $event, array $overrides = [], ?string $meta = null): bool {
        return self::raw_log(
            $event,
            $instance->get_instance_id(),
            $instance->get_course_id(),
            $instance->get_meeting_id(),
            $overrides,
            $meta
        );
    }

    /**
     * Register a bigbluebuttonbn event from raw data.
     *
     * @param string $event
     * @param int $instanceid
     * @param int $courseid
     * @param string $meetingid
     * @param array $overrides
     * @param string|null $meta
     * @return bool
     */
    protected static function raw_log(
        string $event,
        int $instanceid,
        int $courseid,
        string $meetingid,
        array $overrides = [],
        ?string $meta = null
    ): bool {
        global $DB, $USER;

        $log = (object) array_merge([
            // Default values.
            'courseid' => $courseid,
            'bigbluebuttonbnid' => $instanceid,
            'userid' => $USER->id,
            'meetingid' => $meetingid,
            'timecreated' => time(),
            'log' => $event,
            'meta' => $meta,
        ], $overrides);

        return !!$DB->insert_record('bigbluebuttonbn_logs', $log);
    }

    /**
     * Helper register a bigbluebuttonbn event.
     *
     * @param instance $instance
     * @param string $type
     * @param array $options [timecreated, userid, other]
     */
    protected static function log_moodle_event(instance $instance, string $type, array $options = []): void {
        if (!in_array($type, \mod_bigbluebuttonbn\event\events::$events)) {
            // No log will be created.
            return;
        }

        $params = [
            'context' => $instance->get_context(),
            'objectid' => $instance->get_instance_id(),
        ];

        if (array_key_exists('timecreated', $options)) {
            $params['timecreated'] = $options['timecreated'];
        }

        if (array_key_exists('userid', $options)) {
            $params['userid'] = $options['userid'];
        }

        if (array_key_exists('other', $options)) {
            $params['other'] = $options['other'];
        }

        $event = call_user_func_array("\\mod_bigbluebuttonbn\\event\\{$type}::create", [$params]);
        $event->add_record_snapshot('course_modules', $instance->get_cm());
        $event->add_record_snapshot('course', $instance->get_course());
        $event->add_record_snapshot('bigbluebuttonbn', $instance->get_instance_data());
        $event->trigger();
    }

    /**
     * Helper function to count the number of callback logs matching the supplied specifications.
     *
     * @param string $id
     * @param string $callbacktype
     * @return int
     */
    protected static function count_callback_events(string $id, string $callbacktype = 'recording_ready'): int {
        global $DB;
        // Look for a log record that is of "Callback" type and is related to the given event.
        $conditions = [
            "log = :logtype",
            $DB->sql_like('meta', ':cbtypelike')
        ];

        $params = [
            'logtype' => self::EVENT_CALLBACK,
            'cbtypelike' => "%meeting_events%" // All callbacks are meeting events, even recording events.
        ];

        $basesql = 'SELECT COUNT(DISTINCT id) FROM {bigbluebuttonbn_logs}';
        switch ($callbacktype) {
            case 'recording_ready':
                $conditions[] = $DB->sql_like('meta', ':isrecordid');
                $params['isrecordid'] = '%recordid%'; // The recordid field in the meta field (json encoded).
                break;
            case 'meeting_events':
                $conditions[] = $DB->sql_like('meta', ':idlike');
                $params['idlike'] = "%$id%"; // The unique id of the meeting is the meta field (json encoded).
                break;
        }
        $wheresql = join(' AND ', $conditions);
        return $DB->count_records_sql($basesql . ' WHERE ' . $wheresql, $params);
    }

    /**
     * Log event to string that can be internationalised via get_string.
     */
    const LOG_TO_STRING = [
        self::EVENT_JOIN => 'event_meeting_joined',
        self::EVENT_PLAYED => 'event_recording_viewed',
        self::EVENT_IMPORT => 'event_recording_imported',
        self::EVENT_ADD => 'event_activity_created',
        self::EVENT_DELETE => 'event_activity_deleted',
        self::EVENT_EDIT => 'event_activity_updated',
        self::EVENT_SUMMARY => 'event_meeting_summary',
        self::EVENT_LOGOUT => 'event_meeting_left',
        self::EVENT_MEETING_START => 'event_meeting_joined',
    ];

    /**
     * Get the event name (human friendly version)
     *
     * @param object $log object as returned by get_user_completion_logs_with_userfields
     */
    public static function get_printable_event_name(object $log) {
        $logstringname = self::LOG_TO_STRING[$log->log] ?? 'event_unknown';
        return get_string($logstringname, 'mod_bigbluebuttonbn');
    }
}
