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

use Google\Service\Meet\ListTranscriptsResponse;
use Google\Service\Meet\Transcript;

/**
 * The "transcripts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $meetService = new Google\Service\Meet(...);
 *   $transcripts = $meetService->conferenceRecords_transcripts;
 *  </code>
 */
class ConferenceRecordsTranscripts extends \Google\Service\Resource
{
  /**
   * Gets a transcript by transcript ID. (transcripts.get)
   *
   * @param string $name Required. Resource name of the transcript.
   * @param array $optParams Optional parameters.
   * @return Transcript
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Transcript::class);
  }
  /**
   * Lists the set of transcripts from the conference record. By default, ordered
   * by start time and in ascending order.
   * (transcripts.listConferenceRecordsTranscripts)
   *
   * @param string $parent Required. Format:
   * `conferenceRecords/{conference_record}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of transcripts to return. The service
   * might return fewer than this value. If unspecified, at most 10 transcripts
   * are returned. The maximum value is 100; values above 100 are coerced to 100.
   * Maximum might change in the future.
   * @opt_param string pageToken Page token returned from previous List Call.
   * @return ListTranscriptsResponse
   * @throws \Google\Service\Exception
   */
  public function listConferenceRecordsTranscripts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTranscriptsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRecordsTranscripts::class, 'Google_Service_Meet_Resource_ConferenceRecordsTranscripts');
