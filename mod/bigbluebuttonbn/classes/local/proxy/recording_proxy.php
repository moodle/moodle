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

namespace mod_bigbluebuttonbn\local\proxy;

use cache;
use cache_helper;
use SimpleXMLElement;

/**
 * The recording proxy.
 *
 * This class acts as a proxy between Moodle and the BigBlueButton API server,
 * and deals with all requests relating to recordings.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class recording_proxy extends proxy_base {

    /**
     * Invalidate the MUC cache for the specified recording.
     *
     * @param string $recordid
     */
    protected static function invalidate_cache_for_recording(string $recordid): void {
        cache_helper::invalidate_by_event('mod_bigbluebuttonbn/recordingchanged', [$recordid]);
    }

    /**
     * Perform deleteRecordings on BBB.
     *
     * @param string $recordid a recording id
     * @return bool
     */
    public static function delete_recording(string $recordid, ?int $instanceid = null): bool {
        $result = self::fetch_endpoint_xml('deleteRecordings', ['recordID' => $recordid]);
        if (!$result || $result->returncode != 'SUCCESS') {
            return false;
        }
        return true;
    }

    /**
     * Perform publishRecordings on BBB.
     *
     * @param string $recordid
     * @param string $publish
     * @return bool
     */
    public static function publish_recording(string $recordid, string $publish = 'true'): bool {
        $result = self::fetch_endpoint_xml('publishRecordings', [
            'recordID' => $recordid,
            'publish' => $publish,
        ]);

        self::invalidate_cache_for_recording($recordid);

        if (!$result || $result->returncode != 'SUCCESS') {
            return false;
        }

        return true;
    }

    /**
     * Perform publishRecordings on BBB.
     *
     * @param string $recordid
     * @param string $protected
     * @return bool
     */
    public static function protect_recording(string $recordid, string $protected = 'true'): bool {
        global $CFG;

        // Ignore action if recording_protect_editable is set to false.
        if (empty($CFG->bigbluebuttonbn_recording_protect_editable)) {
            return false;
        }

        $result = self::fetch_endpoint_xml('updateRecordings', [
            'recordID' => $recordid,
            'protect' => $protected,
        ]);

        self::invalidate_cache_for_recording($recordid);

        if (!$result || $result->returncode != 'SUCCESS') {
            return false;
        }

        return true;
    }

    /**
     * Perform updateRecordings on BBB.
     *
     * @param string $recordid a single record identifier
     * @param array $params ['key'=>param_key, 'value']
     */
    public static function update_recording(string $recordid, array $params): bool {
        $result = self::fetch_endpoint_xml('updateRecordings', array_merge([
            'recordID' => $recordid
        ], $params));

        self::invalidate_cache_for_recording($recordid);

        return $result ? $result->returncode == 'SUCCESS' : false;
    }

    /**
     * Helper function to fetch a single recording from a BigBlueButton server.
     *
     * @param string $recordingid
     * @return null|array
     */
    public static function fetch_recording(string $recordingid): ?array {
        $data = self::fetch_recordings([$recordingid]);

        if (array_key_exists($recordingid, $data)) {
            return $data[$recordingid];
        }

        return null;
    }

    /**
     * Check whether the current recording is a protected recording and purge the cache if necessary.
     *
     * @param string $recordingid
     */
    public static function purge_protected_recording(string $recordingid): void {
        $cache = cache::make('mod_bigbluebuttonbn', 'recordings');

        $recording = $cache->get($recordingid);
        if (empty($recording)) {
            // This value was not cached to begin with.
            return;
        }

        $currentfetchcache = cache::make('mod_bigbluebuttonbn', 'currentfetch');
        if ($currentfetchcache->has($recordingid)) {
            // This item was fetched in the current request.
            return;
        }

        if (array_key_exists('protected', $recording) && $recording['protected'] === 'true') {
            // This item is protected. Purge it from the cache.
            $cache->delete($recordingid);
            return;
        }
    }

    /**
     * Helper function to fetch recordings from a BigBlueButton server.
     *
     * We use a cache to store recording indexed by keyids/recordingID.
     * @param array $keyids list of recordingids
     * @return array (associative) with recordings indexed by recordID, each recording is a non sequential array
     *  and sorted by {@see recording_proxy::sort_recordings}
     */
    public static function fetch_recordings(array $keyids = []): array {
        $recordings = [];

        // If $ids is empty return array() to prevent a getRecordings with meetingID and recordID set to ''.
        if (empty($keyids)) {
            return $recordings;
        }
        $cache = cache::make('mod_bigbluebuttonbn', 'recordings');
        $currentfetchcache = cache::make('mod_bigbluebuttonbn', 'currentfetch');
        $recordings = array_filter($cache->get_many($keyids));
        $missingkeys = array_diff(array_values($keyids), array_keys($recordings));

        $recordings += self::do_fetch_recordings($missingkeys);
        $cache->set_many($recordings);
        $currentfetchcache->set_many(array_flip(array_keys($recordings)));
        return $recordings;
    }

    /**
     * Helper function to fetch recordings from a BigBlueButton server.
     *
     * @param array $keyids list of meetingids
     * @return array (associative) with recordings indexed by recordID, each recording is a non sequential array
     *  and sorted by {@see recording_proxy::sort_recordings}
     */
    public static function fetch_recording_by_meeting_id(array $keyids = []): array {
        $recordings = [];

        // If $ids is empty return array() to prevent a getRecordings with meetingID and recordID set to ''.
        if (empty($keyids)) {
            return $recordings;
        }
        $recordings = self::do_fetch_recordings($keyids, 'meetingID');
        return $recordings;
    }

    /**
     * Helper function to fetch recordings from a BigBlueButton server.
     *
     * @param array $keyids list of meetingids or recordingids
     * @param string $key the param name used for the BBB request (<recordID>|meetingID)
     * @return array (associative) with recordings indexed by recordID, each recording is a non sequential array.
     *  and sorted {@see recording_proxy::sort_recordings}
     */
    private static function do_fetch_recordings(array $keyids = [], string $key = 'recordID'): array {
        $recordings = [];
        $pagesize = 25;
        while ($ids = array_splice($keyids, 0, $pagesize)) {
            $fetchrecordings = self::fetch_recordings_page($ids, $key);
            $recordings += $fetchrecordings;
        }
        // Sort recordings.
        return self::sort_recordings($recordings);
    }
    /**
     * Helper function to fetch a page of recordings from the remote server.
     *
     * @param array $ids
     * @param string $key
     * @return array
     */
    private static function fetch_recordings_page(array $ids, $key = 'recordID'): array {
        // The getRecordings call is executed using a method GET (supported by all versions of BBB).
        $xml = self::fetch_endpoint_xml('getRecordings', [$key => implode(',', $ids), 'state' => 'any']);

        if (!$xml) {
            return [];
        }

        if ($xml->returncode != 'SUCCESS') {
            return [];
        }

        if (!isset($xml->recordings)) {
            return [];
        }

        $recordings = [];
        // If there were recordings already created.
        foreach ($xml->recordings->recording as $recordingxml) {
            $recording = self::parse_recording($recordingxml);
            $recordings[$recording['recordID']] = $recording;
            // Check if there are any child.
            if (isset($recordingxml->breakoutRooms->breakoutRoom)) {
                $breakoutrooms = [];
                foreach ($recordingxml->breakoutRooms->breakoutRoom as $breakoutroom) {
                    $breakoutrooms[] = trim((string) $breakoutroom);
                }
                if ($breakoutrooms) {
                    $xml = self::fetch_endpoint_xml('getRecordings', ['recordID' => implode(',', $breakoutrooms)]);
                    if ($xml && $xml->returncode == 'SUCCESS' && isset($xml->recordings)) {
                        // If there were already created meetings.
                        foreach ($xml->recordings->recording as $subrecordingxml) {
                            $recording = self::parse_recording($subrecordingxml);
                            $recordings[$recording['recordID']] = $recording;
                        }
                    }
                }
            }
        }

        return $recordings;
    }

    /**
     *  Helper function to sort an array of recordings. It compares the startTime in two recording objects.
     *
     * @param array $recordings
     * @return array
     */
    public static function sort_recordings(array $recordings): array {
        global $CFG;

        uasort($recordings, function($a, $b) {
            if ($a['startTime'] < $b['startTime']) {
                return -1;
            }
            if ($a['startTime'] == $b['startTime']) {
                return 0;
            }
            return 1;
        });

        return $recordings;
    }

    /**
     * Helper function to parse an xml recording object and produce an array in the format used by the plugin.
     *
     * @param SimpleXMLElement $recording
     *
     * @return array
     */
    public static function parse_recording(SimpleXMLElement $recording): array {
        // Add formats.
        $playbackarray = [];
        foreach ($recording->playback->format as $format) {
            $playbackarray[(string) $format->type] = [
                'type' => (string) $format->type,
                'url' => trim((string) $format->url), 'length' => (string) $format->length
            ];
            // Add preview per format when existing.
            if ($format->preview) {
                $playbackarray[(string) $format->type]['preview'] =
                    self::parse_preview_images($format->preview);
            }
        }
        // Add the metadata to the recordings array.
        $metadataarray =
            self::parse_recording_meta(get_object_vars($recording->metadata));
        $recordingarray = [
            'recordID' => (string) $recording->recordID,
            'meetingID' => (string) $recording->meetingID,
            'meetingName' => (string) $recording->name,
            'published' => (string) $recording->published,
            'state' => (string) $recording->state,
            'startTime' => (string) $recording->startTime,
            'endTime' => (string) $recording->endTime,
            'playbacks' => $playbackarray
        ];
        if (isset($recording->protected)) {
            $recordingarray['protected'] = (string) $recording->protected;
        }
        return $recordingarray + $metadataarray;
    }

    /**
     * Helper function to convert an xml recording metadata object to an array in the format used by the plugin.
     *
     * @param array $metadata
     *
     * @return array
     */
    public static function parse_recording_meta(array $metadata): array {
        $metadataarray = [];
        foreach ($metadata as $key => $value) {
            if (is_object($value)) {
                $value = '';
            }
            $metadataarray['meta_' . $key] = $value;
        }
        return $metadataarray;
    }

    /**
     * Helper function to convert an xml recording preview images to an array in the format used by the plugin.
     *
     * @param SimpleXMLElement $preview
     *
     * @return array
     */
    public static function parse_preview_images(SimpleXMLElement $preview): array {
        $imagesarray = [];
        foreach ($preview->images->image as $image) {
            $imagearray = ['url' => trim((string) $image)];
            foreach ($image->attributes() as $attkey => $attvalue) {
                $imagearray[$attkey] = (string) $attvalue;
            }
            array_push($imagesarray, $imagearray);
        }
        return $imagesarray;
    }
}
