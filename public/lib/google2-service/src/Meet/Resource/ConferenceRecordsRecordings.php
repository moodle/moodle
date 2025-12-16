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

use Google\Service\Meet\ListRecordingsResponse;
use Google\Service\Meet\Recording;

/**
 * The "recordings" collection of methods.
 * Typical usage is:
 *  <code>
 *   $meetService = new Google\Service\Meet(...);
 *   $recordings = $meetService->conferenceRecords_recordings;
 *  </code>
 */
class ConferenceRecordsRecordings extends \Google\Service\Resource
{
  /**
   * Gets a recording by recording ID. (recordings.get)
   *
   * @param string $name Required. Resource name of the recording.
   * @param array $optParams Optional parameters.
   * @return Recording
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Recording::class);
  }
  /**
   * Lists the recording resources from the conference record. By default, ordered
   * by start time and in ascending order.
   * (recordings.listConferenceRecordsRecordings)
   *
   * @param string $parent Required. Format:
   * `conferenceRecords/{conference_record}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of recordings to return. The service
   * might return fewer than this value. If unspecified, at most 10 recordings are
   * returned. The maximum value is 100; values above 100 are coerced to 100.
   * Maximum might change in the future.
   * @opt_param string pageToken Page token returned from previous List Call.
   * @return ListRecordingsResponse
   * @throws \Google\Service\Exception
   */
  public function listConferenceRecordsRecordings($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRecordingsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRecordsRecordings::class, 'Google_Service_Meet_Resource_ConferenceRecordsRecordings');
