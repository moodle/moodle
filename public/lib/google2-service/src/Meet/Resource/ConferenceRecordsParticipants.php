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

use Google\Service\Meet\ListParticipantsResponse;
use Google\Service\Meet\Participant;

/**
 * The "participants" collection of methods.
 * Typical usage is:
 *  <code>
 *   $meetService = new Google\Service\Meet(...);
 *   $participants = $meetService->conferenceRecords_participants;
 *  </code>
 */
class ConferenceRecordsParticipants extends \Google\Service\Resource
{
  /**
   * Gets a participant by participant ID. (participants.get)
   *
   * @param string $name Required. Resource name of the participant.
   * @param array $optParams Optional parameters.
   * @return Participant
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Participant::class);
  }
  /**
   * Lists the participants in a conference record. By default, ordered by join
   * time and in descending order. This API supports `fields` as standard
   * parameters like every other API. However, when the `fields` request parameter
   * is omitted, this API defaults to `'participants, next_page_token'`.
   * (participants.listConferenceRecordsParticipants)
   *
   * @param string $parent Required. Format:
   * `conferenceRecords/{conference_record}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. User specified filtering condition in
   * [EBNF
   * format](https://en.wikipedia.org/wiki/Extended_Backus%E2%80%93Naur_form). The
   * following are the filterable fields: * `earliest_start_time` *
   * `latest_end_time` For example, `latest_end_time IS NULL` returns active
   * participants in the conference.
   * @opt_param int pageSize Maximum number of participants to return. The service
   * might return fewer than this value. If unspecified, at most 100 participants
   * are returned. The maximum value is 250; values above 250 are coerced to 250.
   * Maximum might change in the future.
   * @opt_param string pageToken Page token returned from previous List Call.
   * @return ListParticipantsResponse
   * @throws \Google\Service\Exception
   */
  public function listConferenceRecordsParticipants($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListParticipantsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRecordsParticipants::class, 'Google_Service_Meet_Resource_ConferenceRecordsParticipants');
