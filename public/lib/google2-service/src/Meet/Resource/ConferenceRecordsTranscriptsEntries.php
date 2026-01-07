<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Meet\Resource;

use Google\Service\Meet\ListTranscriptEntriesResponse;
use Google\Service\Meet\TranscriptEntry;

/**
 * The "entries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $meetService = new Google\Service\Meet(...);
 *   $entries = $meetService->conferenceRecords_transcripts_entries;
 *  </code>
 */
class ConferenceRecordsTranscriptsEntries extends \Google\Service\Resource
{
  /**
   * Gets a `TranscriptEntry` resource by entry ID. Note: The transcript entries
   * returned by the Google Meet API might not match the transcription found in
   * the Google Docs transcript file. This can occur when 1) we have interleaved
   * speakers within milliseconds, or 2) the Google Docs transcript file is
   * modified after generation. (entries.get)
   *
   * @param string $name Required. Resource name of the `TranscriptEntry`.
   * @param array $optParams Optional parameters.
   * @return TranscriptEntry
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TranscriptEntry::class);
  }
  /**
   * Lists the structured transcript entries per transcript. By default, ordered
   * by start time and in ascending order. Note: The transcript entries returned
   * by the Google Meet API might not match the transcription found in the Google
   * Docs transcript file. This can occur when 1) we have interleaved speakers
   * within milliseconds, or 2) the Google Docs transcript file is modified after
   * generation. (entries.listConferenceRecordsTranscriptsEntries)
   *
   * @param string $parent Required. Format:
   * `conferenceRecords/{conference_record}/transcripts/{transcript}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of entries to return. The service
   * might return fewer than this value. If unspecified, at most 10 entries are
   * returned. The maximum value is 100; values above 100 are coerced to 100.
   * Maximum might change in the future.
   * @opt_param string pageToken Page token returned from previous List Call.
   * @return ListTranscriptEntriesResponse
   * @throws \Google\Service\Exception
   */
  public function listConferenceRecordsTranscriptsEntries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTranscriptEntriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRecordsTranscriptsEntries::class, 'Google_Service_Meet_Resource_ConferenceRecordsTranscriptsEntries');
