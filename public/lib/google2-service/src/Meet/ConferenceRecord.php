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

class ConferenceRecord extends \Google\Model
{
  /**
   * Output only. Timestamp when the conference ended. Set for past conferences.
   * Unset if the conference is ongoing.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Server enforced expiration time for when this conference
   * record resource is deleted. The resource is deleted 30 days after the
   * conference ends.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Identifier. Resource name of the conference record. Format:
   * `conferenceRecords/{conference_record}` where `{conference_record}` is a
   * unique ID for each instance of a call within a space.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The space where the conference was held.
   *
   * @var string
   */
  public $space;
  /**
   * Output only. Timestamp when the conference started. Always set.
   *
   * @var string
   */
  public $startTime;

  /**
   * Output only. Timestamp when the conference ended. Set for past conferences.
   * Unset if the conference is ongoing.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Server enforced expiration time for when this conference
   * record resource is deleted. The resource is deleted 30 days after the
   * conference ends.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Identifier. Resource name of the conference record. Format:
   * `conferenceRecords/{conference_record}` where `{conference_record}` is a
   * unique ID for each instance of a call within a space.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The space where the conference was held.
   *
   * @param string $space
   */
  public function setSpace($space)
  {
    $this->space = $space;
  }
  /**
   * @return string
   */
  public function getSpace()
  {
    return $this->space;
  }
  /**
   * Output only. Timestamp when the conference started. Always set.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRecord::class, 'Google_Service_Meet_ConferenceRecord');
