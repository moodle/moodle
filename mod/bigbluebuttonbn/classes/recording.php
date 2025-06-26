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
use context;
use context_course;
use context_module;
use core\persistent;
use mod_bigbluebuttonbn\local\proxy\recording_proxy;
use moodle_url;
use stdClass;

/**
 * The recording entity.
 *
 * This is utility class that defines a single recording, and provides methods for their local handling locally, and
 * communication with the bigbluebutton server.
 *
 * @package mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording extends persistent {
    /** The table name. */
    const TABLE = 'bigbluebuttonbn_recordings';

    /** @var int Defines that the activity used to create the recording no longer exists */
    public const RECORDING_HEADLESS = 1;

    /** @var int Defines that the recording is not the original but an imported one */
    public const RECORDING_IMPORTED = 1;

    /** @var int Defines that the list should include imported recordings */
    public const INCLUDE_IMPORTED_RECORDINGS = true;

    /** @var int A meeting set to be recorded still awaits for a recording update */
    public const RECORDING_STATUS_AWAITING = 0;

    /** @var int A meeting set to be recorded was not recorded and dismissed by BBB */
    public const RECORDING_STATUS_DISMISSED = 1;

    /** @var int A meeting set to be recorded has a recording processed */
    public const RECORDING_STATUS_PROCESSED = 2;

    /** @var int A meeting set to be recorded received notification callback from BBB */
    public const RECORDING_STATUS_NOTIFIED = 3;

    /** @var int A meeting set to be recorded was processed and set back to an awaiting state */
    public const RECORDING_STATUS_RESET = 4;

    /** @var int A meeting set to be recorded was deleted from bigbluebutton */
    public const RECORDING_STATUS_DELETED = 5;

    /** @var bool Whether metadata been changed so the remote information needs to be updated ? */
    protected $metadatachanged = false;

    /** @var int A refresh period for recordings, defaults to 300s (5mins) */
    public const RECORDING_REFRESH_DEFAULT_PERIOD = 300;

    /** @var int A time limit for recordings to be dismissed, defaults to 30d (30days) */
    public const RECORDING_TIME_LIMIT_DAYS = 30;

    /** @var array A cached copy of the metadata */
    protected $metadata = null;

    /** @var instance A cached copy of the instance */
    protected $instance;

    /** @var bool imported recording status */
    public $imported;

    /**
     * Create an instance of this class.
     *
     * @param int $id If set, this is the id of an existing record, used to load the data.
     * @param stdClass|null $record If set will be passed to from_record
     * @param null|array $metadata
     */
    public function __construct($id = 0, ?stdClass $record = null, ?array $metadata = null) {
        if ($record) {
            $record->headless = $record->headless ?? false;
            $record->imported = $record->imported ?? false;
            $record->groupid = $record->groupid ?? 0;
            $record->status = $record->status ?? self::RECORDING_STATUS_AWAITING;
        }
        parent::__construct($id, $record);

        if ($metadata) {
            $this->metadata = $metadata;
        }
    }

    /**
     * Helper function to retrieve recordings from the BigBlueButton.
     *
     * @param instance $instance
     * @param bool $includeimported
     * @param bool $onlyimported
     *
     * @return recording[] containing the recordings indexed by recordID, each recording is also a
     * non sequential associative array itself that corresponds to the actual recording in BBB
     */
    public static function get_recordings_for_instance(
        instance $instance,
        bool $includeimported = false,
        bool $onlyimported = false
    ): array {
        [$selects, $params] = self::get_basic_select_from_parameters(false, $includeimported, $onlyimported);
        $selects[] = "bigbluebuttonbnid = :bbbid";
        $params['bbbid'] = $instance->get_instance_id();
        $groupmode = groups_get_activity_groupmode($instance->get_cm());
        $context = $instance->get_context();
        if ($groupmode) {
            [$groupselects, $groupparams] = self::get_select_for_group(
                $groupmode,
                $context,
                $instance->get_course_id(),
                $instance->get_group_id(),
                $instance->get_cm()->groupingid
            );
            if ($groupselects) {
                $selects[] = $groupselects;
                $params = array_merge_recursive($params, $groupparams);
            }
        }

        $recordings = self::fetch_records($selects, $params);
        foreach ($recordings as $recording) {
            $recording->instance = $instance;
        }

        return $recordings;
    }

    /**
     * Helper function to retrieve recordings from a given course.
     *
     * @param int $courseid id for a course record or null
     * @param array $excludedinstanceid exclude recordings from instance ids
     * @param bool $includeimported
     * @param bool $onlyimported
     * @param bool $includedeleted
     * @param bool $onlydeleted
     *
     * @return recording[] containing the recordings indexed by recordID, each recording is also a
     * non sequential associative array itself that corresponds to the actual recording in BBB
     */
    public static function get_recordings_for_course(
        int $courseid,
        array $excludedinstanceid = [],
        bool $includeimported = false,
        bool $onlyimported = false,
        bool $includedeleted = false,
        bool $onlydeleted = false
    ): array {
        global $DB;

        [$selects, $params] = self::get_basic_select_from_parameters(
            $includedeleted,
            $includeimported,
            $onlyimported,
            $onlydeleted
        );
        if ($courseid) {
            $selects[] = "courseid = :courseid";
            $params['courseid'] = $courseid;
            $course = $DB->get_record('course', ['id' => $courseid]);
            $groupmode = groups_get_course_groupmode($course);
            $context = context_course::instance($courseid);
        } else {
            $context = \context_system::instance();
            $groupmode = NOGROUPS;
        }

        if ($groupmode) {
            [$groupselects, $groupparams] = self::get_select_for_group($groupmode, $context, $course->id);
            if ($groupselects) {
                $selects[] = $groupselects;
                $params = array_merge($params, $groupparams);
            }
        }

        if ($excludedinstanceid) {
            [$sqlexcluded, $paramexcluded] = $DB->get_in_or_equal($excludedinstanceid, SQL_PARAMS_NAMED, 'param', false);
            $selects[] = "bigbluebuttonbnid {$sqlexcluded}";
            $params = array_merge($params, $paramexcluded);
        }

        return self::fetch_records($selects, $params);
    }

    /**
     * Get select for given group mode and context
     *
     * @param int $groupmode
     * @param \context $context
     * @param int $courseid
     * @param int $groupid
     * @param int $groupingid
     * @return array
     */
    protected static function get_select_for_group($groupmode, $context, $courseid, $groupid = 0, $groupingid = 0): array {
        global $DB, $USER;

        $selects = [];
        $params = [];
        if ($groupmode) {
            $accessallgroups = has_capability('moodle/site:accessallgroups', $context) || $groupmode == VISIBLEGROUPS;
            if ($accessallgroups) {
                if ($context instanceof context_module) {
                    $allowedgroups = groups_get_all_groups($courseid, 0, $groupingid);
                } else {
                    $allowedgroups = groups_get_all_groups($courseid);
                }
            } else {
                if ($context instanceof context_module) {
                    $allowedgroups = groups_get_all_groups($courseid, $USER->id, $groupingid);
                } else {
                    $allowedgroups = groups_get_all_groups($courseid, $USER->id);
                }
            }
            $allowedgroupsid = array_map(function ($g) {
                return $g->id;
            }, $allowedgroups);
            if ($groupid || empty($allowedgroups)) {
                $selects[] = "groupid = :groupid";
                $params['groupid'] = ($groupid && in_array($groupid, $allowedgroupsid)) ?
                    $groupid : 0;
            } else {
                if ($accessallgroups) {
                    $allowedgroupsid[] = 0;
                }
                list($groupselects, $groupparams) = $DB->get_in_or_equal($allowedgroupsid, SQL_PARAMS_NAMED);
                $selects[] = 'groupid ' . $groupselects;
                $params = array_merge_recursive($params, $groupparams);
            }
        }
        return [
            implode(" AND ", $selects),
            $params,
        ];
    }

    /**
     * Get basic sql select from given parameters
     *
     * @param bool $includedeleted
     * @param bool $includeimported
     * @param bool $onlyimported
     * @param bool $onlydeleted
     * @return array
     */
    protected static function get_basic_select_from_parameters(
        bool $includedeleted = false,
        bool $includeimported = false,
        bool $onlyimported = false,
        bool $onlydeleted = false
    ): array {
        $selects = [];
        $params = [];

        // Start with the filters.
        if ($onlydeleted) {
            // Only headless recordings when only deleted is set.
            $selects[] = "headless = :headless";
            $params['headless'] = self::RECORDING_HEADLESS;
        } else if (!$includedeleted) {
            // Exclude headless recordings unless includedeleted.
            $selects[] = "headless != :headless";
            $params['headless'] = self::RECORDING_HEADLESS;
        }

        if (!$includeimported) {
            // Exclude imported recordings unless includedeleted.
            $selects[] = "imported != :imported";
            $params['imported'] = self::RECORDING_IMPORTED;
        } else if ($onlyimported) {
            // Exclude non-imported recordings.
            $selects[] = "imported = :imported";
            $params['imported'] = self::RECORDING_IMPORTED;
        }

        // Now get only recordings that have been validated by recording ready callback.
        $selects[] = "status IN (:status_processed, :status_notified)";
        $params['status_processed'] = self::RECORDING_STATUS_PROCESSED;
        $params['status_notified'] = self::RECORDING_STATUS_NOTIFIED;
        return [$selects, $params];
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'bigbluebuttonbnid' => [
                'type' => PARAM_INT,
            ],
            'groupid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
            ],
            'recordingid' => [
                'type' => PARAM_RAW,
            ],
            'headless' => [
                'type' => PARAM_BOOL,
            ],
            'imported' => [
                'type' => PARAM_BOOL,
            ],
            'status' => [
                'type' => PARAM_INT,
            ],
            'importeddata' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => ''
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => 0
            ],
            'protected' => [
                'type' => PARAM_BOOL,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
            'starttime' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
            'endtime' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
            'published' => [
                'type' => PARAM_BOOL,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
            'playbacks' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null
            ],
        ];
    }

    /**
     * Get the instance that this recording relates to.
     *
     * @return instance
     */
    public function get_instance(): instance {
        if ($this->instance === null) {
            $this->instance = instance::get_from_instanceid($this->get('bigbluebuttonbnid'));
        }

        return $this->instance;
    }

    /**
     * Before doing the database update, let's check if we need to update metadata
     *
     * @return void
     */
    protected function before_update() {
        // We update if the remote metadata has been changed locally.
        if ($this->metadatachanged && !$this->get('imported')) {
            $metadata = $this->fetch_metadata();
            if ($metadata) {
                recording_proxy::update_recording(
                    $this->get('recordingid'),
                    $metadata
                );
            }
            $this->metadatachanged = false;
        }
    }

    /**
     * Create a new imported recording from current recording
     *
     * @param instance $targetinstance
     * @return recording
     */
    public function create_imported_recording(instance $targetinstance) {
        $recordingrec = $this->to_record();
        $remotedata = $this->fetch_metadata();
        unset($recordingrec->id);
        $recordingrec->bigbluebuttonbnid = $targetinstance->get_instance_id();
        $recordingrec->courseid = $targetinstance->get_course_id();
        $recordingrec->groupid = 0; // The recording is available to everyone.
        $recordingrec->importeddata = json_encode($remotedata);
        $recordingrec->imported = true;
        $recordingrec->headless = false;
        $importedrecording = new self(0, $recordingrec);
        $importedrecording->create();
        return $importedrecording;
    }

    /**
     * Delete the recording in the BBB button
     *
     * @return void
     */
    protected function before_delete() {
        $recordid = $this->get('recordingid');
        if ($recordid && !$this->get('imported')) {
            recording_proxy::delete_recording($recordid);
            // Delete in cache if needed.
            $cachedrecordings = cache::make('mod_bigbluebuttonbn', 'recordings');
            $cachedrecordings->delete($recordid);
        }
    }

    /**
     * Set name
     *
     * @param string $value
     */
    protected function set_name($value) {
        $this->metadata_set('name', trim($value));
    }

    /**
     * Set Description
     *
     * @param string $value
     */
    protected function set_description($value) {
        $this->metadata_set('description', trim($value));
    }

    /**
     * Recording is protected
     *
     * @param bool $value
     */
    protected function set_protected($value) {
        $realvalue = $value ? "true" : "false";
        $this->metadata_set('protected', $realvalue);
        recording_proxy::protect_recording($this->get('recordingid'), $realvalue);
    }

    /**
     * Recording starttime
     *
     * @param int $value
     */
    protected function set_starttime($value) {
        $this->metadata_set('starttime', $value);
    }

    /**
     * Recording endtime
     *
     * @param int $value
     */
    protected function set_endtime($value) {
        $this->metadata_set('endtime', $value);
    }

    /**
     * Recording is published
     *
     * @param bool $value
     */
    protected function set_published($value) {
        $realvalue = $value ? "true" : "false";
        $this->metadata_set('published', $realvalue);
        // Now set this flag onto the remote bbb server.
        recording_proxy::publish_recording($this->get('recordingid'), $realvalue);
    }

    /**
     * Update recording status
     *
     * @param bool $value
     */
    protected function set_status($value) {
        $this->raw_set('status', $value);
        $this->update();
    }

    /**
     * POSSIBLE_REMOTE_META_SOURCE match a field type and its metadataname (historical and current).
     */
    const POSSIBLE_REMOTE_META_SOURCE = [
        'description' => ['meta_bbb-recording-description', 'meta_contextactivitydescription'],
        'name' => ['meta_bbb-recording-name', 'meta_contextactivity', 'meetingName'],
        'playbacks' => ['playbacks'],
        'starttime' => ['startTime'],
        'endtime' => ['endTime'],
        'published' => ['published'],
        'protected' => ['protected'],
        'tags' => ['meta_bbb-recording-tags']
    ];

    /**
     * Get the real metadata name for the possible source.
     *
     * @param string $sourcetype the name of the source we look for (name, description...)
     * @param array $metadata current metadata
     */
    protected function get_possible_meta_name_for_source($sourcetype, $metadata): string {
        $possiblesource = self::POSSIBLE_REMOTE_META_SOURCE[$sourcetype];
        $possiblesourcename = $possiblesource[0];
        foreach ($possiblesource as $possiblesname) {
            if (isset($meta[$possiblesname])) {
                $possiblesourcename = $possiblesname;
            }
        }
        return $possiblesourcename;
    }

    /**
     * Convert string (metadata) to json object
     *
     * @return mixed|null
     */
    protected function remote_meta_convert() {
        $remotemeta = $this->raw_get('importeddata');
        return json_decode($remotemeta, true);
    }

    /**
     * Description is stored in the metadata, so we sometimes needs to do some conversion.
     */
    protected function get_description() {
        return trim($this->metadata_get('description'));
    }

    /**
     * Name is stored in the metadata
     */
    protected function get_name() {
        return trim($this->metadata_get('name'));
    }

    /**
     * List of playbacks for this recording.
     *
     * @return array[]
     */
    protected function get_playbacks() {
        if ($playbacks = $this->metadata_get('playbacks')) {
            return array_map(function (array $playback): array {
                $clone = array_merge([], $playback);
                $clone['url'] = new moodle_url('/mod/bigbluebuttonbn/bbb_view.php', [
                    'action' => 'play',
                    'bn' => $this->raw_get('bigbluebuttonbnid'),
                    'rid' => $this->get('id'),
                    'rtype' => $clone['type'],
                ]);

                return $clone;
            }, $playbacks);
        }

        return [];
    }

    /**
     * Get the playback URL for the specified type.
     *
     * @param string $type
     * @return null|string
     */
    public function get_remote_playback_url(string $type): ?string {
        $this->refresh_metadata_if_required();

        $playbacks = $this->metadata_get('playbacks');
        foreach ($playbacks as $playback) {
            if ($playback['type'] == $type) {
                return $playback['url'];
            }
        }

        return null;
    }

    /**
     * Is protected. Return null if protected is not implemented.
     *
     * @return bool|null
     */
    protected function get_protected() {
        $protectedtext = $this->metadata_get('protected');
        return is_null($protectedtext) ? null : $protectedtext === "true";
    }

    /**
     * Start time
     *
     * @return mixed|null
     */
    protected function get_starttime() {
        return $this->metadata_get('starttime');
    }

    /**
     * Start time
     *
     * @return mixed|null
     */
    protected function get_endtime() {
        return $this->metadata_get('endtime');
    }

    /**
     * Is published
     *
     * @return bool
     */
    protected function get_published() {
        $publishedtext = $this->metadata_get('published');
        return $publishedtext === "true";
    }

    /**
     * Set locally stored metadata from this instance
     *
     * @param string $fieldname
     * @param mixed $value
     */
    protected function metadata_set($fieldname, $value) {
        // Can we can change the metadata on the imported record ?
        if ($this->get('imported')) {
            return;
        }

        $this->metadatachanged = true;

        $metadata = $this->fetch_metadata();
        $possiblesourcename = $this->get_possible_meta_name_for_source($fieldname, $metadata);
        $metadata[$possiblesourcename] = $value;

        $this->metadata = $metadata;
    }

    /**
     * Get information stored in the recording metadata such as description, name and other info
     *
     * @param string $fieldname
     * @return mixed|null
     */
    protected function metadata_get($fieldname) {
        $metadata = $this->fetch_metadata();

        $possiblesourcename = $this->get_possible_meta_name_for_source($fieldname, $metadata);
        return $metadata[$possiblesourcename] ?? null;
    }

    /**
     * @var string Default sort for recordings when fetching from the database.
     */
    const DEFAULT_RECORDING_SORT = 'timecreated ASC';

    /**
     * Fetch all records which match the specified parameters, including all metadata that relates to them.
     *
     * @param array $selects
     * @param array $params
     * @return recording[]
     */
    protected static function fetch_records(array $selects, array $params): array {
        global $DB, $CFG;

        $withindays = time() - (self::RECORDING_TIME_LIMIT_DAYS * DAYSECS);
        // Sort for recordings when fetching from the database.
        $recordingsort = $CFG->bigbluebuttonbn_recordings_asc_sort ? 'timecreated ASC' : 'timecreated DESC';

        // Fetch the local data. Arbitrary sort by id, so we get the same result on different db engines.
        $recordings = $DB->get_records_select(
            static::TABLE,
            implode(" AND ", $selects),
            $params,
            $recordingsort
        );

        // Grab the recording IDs.
        $recordingids = array_values(array_map(function ($recording) {
            return $recording->recordingid;
        }, $recordings));

        // Fetch all metadata for these recordings.
        $metadatas = recording_proxy::fetch_recordings($recordingids);
        $failedids = recording_proxy::fetch_missing_recordings($recordingids);

        // Return the instances.
        return array_filter(array_map(function ($recording) use ($metadatas, $withindays, $failedids) {
            // Filter out if no metadata was fetched.
            if (!array_key_exists($recording->recordingid, $metadatas)) {
                // If the recording was successfully fetched, mark it as dismissed if it is older than 30 days.
                if (!in_array($recording->recordingid, $failedids) && $withindays > $recording->timecreated) {
                    $recording = new self(0, $recording, null);
                    $recording->set_status(self::RECORDING_STATUS_DISMISSED);
                }
                return false;
            }
            $metadata = $metadatas[$recording->recordingid];
            // Filter out and mark it as deleted if it was deleted in BBB.
            if ($metadata['state'] == 'deleted') {
                $recording = new self(0, $recording, null);
                $recording->set_status(self::RECORDING_STATUS_DELETED);
                return false;
            }
            // Include it otherwise.
            return new self(0, $recording, $metadata);
        }, $recordings));
    }

    /**
     * Fetch metadata
     *
     * If metadata has changed locally or if it an imported recording, nothing will be done.
     *
     * @param bool $force
     * @return array
     */
    protected function fetch_metadata(bool $force = false): ?array {
        if ($this->metadata !== null && !$force) {
            // Metadata is already up-to-date.
            return $this->metadata;
        }

        if ($this->get('imported')) {
            $this->metadata = json_decode($this->get('importeddata'), true);
        } else {
            $this->metadata = recording_proxy::fetch_recording($this->get('recordingid'));
        }

        return $this->metadata;
    }

    /**
     * Refresh metadata if required.
     *
     * If this is a protected recording which whose data was not fetched in the current request, then the metadata will
     * be purged and refetched. This ensures that the url is safe for use with a protected recording.
     */
    protected function refresh_metadata_if_required() {
        recording_proxy::purge_protected_recording($this->get('recordingid'));
        $this->fetch_metadata(true);
    }

    /**
     * Synchronise pending recordings from the server.
     *
     * This function should be called by the check_pending_recordings scheduled task.
     *
     * @param bool $dismissedonly fetch dismissed recording only
     */
    public static function sync_pending_recordings_from_server(bool $dismissedonly = false): void {
        global $DB;
        $params = [
            'withindays' => time() - (self::RECORDING_TIME_LIMIT_DAYS * DAYSECS),
        ];
        // Fetch the local data.
        if ($dismissedonly) {
            mtrace("=> Looking for any recording that has been 'dismissed' in the past " . self::RECORDING_TIME_LIMIT_DAYS
                . " days.");
            $select = 'status = :status_dismissed AND timemodified > :withindays';
            $params['status_dismissed'] = self::RECORDING_STATUS_DISMISSED;
        } else {
            mtrace("=> Looking for any recording awaiting processing from the past " . self::RECORDING_TIME_LIMIT_DAYS . " days.");
            $select = '(status = :status_awaiting AND timecreated > :withindays) OR status = :status_reset';
            $params['status_reset'] = self::RECORDING_STATUS_RESET;
            $params['status_awaiting'] = self::RECORDING_STATUS_AWAITING;
        }

        $recordings = $DB->get_records_select(static::TABLE, $select, $params, self::DEFAULT_RECORDING_SORT);
        // Sort by DEFAULT_RECORDING_SORT we get the same result on different db engines.

        $recordingcount = count($recordings);
        mtrace("=> Found {$recordingcount} recordings to query");

        // Grab the recording IDs.
        $recordingids = array_map(function($recording) {
            return $recording->recordingid;
        }, $recordings);

        // Fetch all metadata for these recordings.
        mtrace("=> Fetching recording metadata from server");
        $metadatas = recording_proxy::fetch_recordings($recordingids);

        $foundcount = 0;
        foreach ($metadatas as $recordingid => $metadata) {
            mtrace("==> Found metadata for {$recordingid}.");
            $id = array_search($recordingid, $recordingids);
            if (!$id) {
                // Recording was not found, skip.
                mtrace("===> Skip as fetched recording was not found.");
                continue;
            }
            // Recording was found, update status.
            mtrace("===> Update local cache as fetched recording was found.");
            $recording = new self(0, $recordings[$id], $metadata);
            $recording->set_status(self::RECORDING_STATUS_PROCESSED);
            $foundcount++;

            if (array_key_exists('breakouts', $metadata)) {
                // Iterate breakout recordings (if any) and update status.
                foreach ($metadata['breakouts'] as $breakoutrecordingid => $breakoutmetadata) {
                    $breakoutrecording = self::get_record(['recordingid' => $breakoutrecordingid]);
                    if (!$breakoutrecording) {
                        $breakoutrecording = new recording(0, (object) [
                            'courseid' => $recording->get('courseid'),
                            'bigbluebuttonbnid' => $recording->get('bigbluebuttonbnid'),
                            'groupid' => $recording->get('groupid'),
                            'recordingid' => $breakoutrecordingid
                        ], $breakoutmetadata);
                        $breakoutrecording->create();
                    }
                    $breakoutrecording->set_status(self::RECORDING_STATUS_PROCESSED);
                    $foundcount++;
                }
            }
        }

        mtrace("=> Finished processing recordings. Updated status for {$foundcount} / {$recordingcount} recordings.");
    }
}
