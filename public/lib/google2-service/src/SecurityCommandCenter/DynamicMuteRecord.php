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

namespace Google\Service\SecurityCommandCenter;

class DynamicMuteRecord extends \Google\Model
{
  /**
   * When the dynamic mute rule first matched the finding.
   *
   * @var string
   */
  public $matchTime;
  /**
   * The relative resource name of the mute rule, represented by a mute config,
   * that created this record, for example
   * `organizations/123/muteConfigs/mymuteconfig` or
   * `organizations/123/locations/global/muteConfigs/mymuteconfig`.
   *
   * @var string
   */
  public $muteConfig;

  /**
   * When the dynamic mute rule first matched the finding.
   *
   * @param string $matchTime
   */
  public function setMatchTime($matchTime)
  {
    $this->matchTime = $matchTime;
  }
  /**
   * @return string
   */
  public function getMatchTime()
  {
    return $this->matchTime;
  }
  /**
   * The relative resource name of the mute rule, represented by a mute config,
   * that created this record, for example
   * `organizations/123/muteConfigs/mymuteconfig` or
   * `organizations/123/locations/global/muteConfigs/mymuteconfig`.
   *
   * @param string $muteConfig
   */
  public function setMuteConfig($muteConfig)
  {
    $this->muteConfig = $muteConfig;
  }
  /**
   * @return string
   */
  public function getMuteConfig()
  {
    return $this->muteConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicMuteRecord::class, 'Google_Service_SecurityCommandCenter_DynamicMuteRecord');
