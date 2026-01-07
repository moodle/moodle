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

use Google\Service\Meet\ConferenceRecord;
use Google\Service\Meet\ListConferenceRecordsResponse;

/**
 * The "conferenceRecords" collection of methods.
 * Typical usage is:
 *  <code>
 *   $meetService = new Google\Service\Meet(...);
 *   $conferenceRecords = $meetService->conferenceRecords;
 *  </code>
 */
class ConferenceRecords extends \Google\Service\Resource
{
  /**
   * Gets a conference record by conference ID. (conferenceRecords.get)
   *
   * @param string $name Required. Resource name of the conference.
   * @param array $optParams Optional parameters.
   * @return ConferenceRecord
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ConferenceRecord::class);
  }
  /**
   * Lists the conference records. By default, ordered by start time and in
   * descending order. (conferenceRecords.listConferenceRecords)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. User specified filtering condition in
   * [EBNF
   * format](https://en.wikipedia.org/wiki/Extended_Backus%E2%80%93Naur_form). The
   * following are the filterable fields: * `space.meeting_code` * `space.name` *
   * `start_time` * `end_time` For example, consider the following filters: *
   * `space.name = "spaces/NAME"` * `space.meeting_code = "abc-mnop-xyz"` *
   * `start_time>="2024-01-01T00:00:00.000Z" AND
   * start_time<="2024-01-02T00:00:00.000Z"` * `end_time IS NULL`
   * @opt_param int pageSize Optional. Maximum number of conference records to
   * return. The service might return fewer than this value. If unspecified, at
   * most 25 conference records are returned. The maximum value is 100; values
   * above 100 are coerced to 100. Maximum might change in the future.
   * @opt_param string pageToken Optional. Page token returned from previous List
   * Call.
   * @return ListConferenceRecordsResponse
   * @throws \Google\Service\Exception
   */
  public function listConferenceRecords($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConferenceRecordsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRecords::class, 'Google_Service_Meet_Resource_ConferenceRecords');
