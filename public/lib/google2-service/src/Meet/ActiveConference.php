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

namespace Google\Service\Meet;

class ActiveConference extends \Google\Model
{
  /**
   * Output only. Reference to 'ConferenceRecord' resource. Format:
   * `conferenceRecords/{conference_record}` where `{conference_record}` is a
   * unique ID for each instance of a call within a space.
   *
   * @var string
   */
  public $conferenceRecord;

  /**
   * Output only. Reference to 'ConferenceRecord' resource. Format:
   * `conferenceRecords/{conference_record}` where `{conference_record}` is a
   * unique ID for each instance of a call within a space.
   *
   * @param string $conferenceRecord
   */
  public function setConferenceRecord($conferenceRecord)
  {
    $this->conferenceRecord = $conferenceRecord;
  }
  /**
   * @return string
   */
  public function getConferenceRecord()
  {
    return $this->conferenceRecord;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActiveConference::class, 'Google_Service_Meet_ActiveConference');
