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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaCheckStateMetadata extends \Google\Collection
{
  protected $collection_key = 'badges';
  /**
   * Indicators related to the check state.
   *
   * @var string[]
   */
  public $badges;
  /**
   * The time when the check first started failing.
   *
   * @var string
   */
  public $firstFailingTime;
  /**
   * The last time the check failed.
   *
   * @var string
   */
  public $lastFailingTime;

  /**
   * Indicators related to the check state.
   *
   * @param string[] $badges
   */
  public function setBadges($badges)
  {
    $this->badges = $badges;
  }
  /**
   * @return string[]
   */
  public function getBadges()
  {
    return $this->badges;
  }
  /**
   * The time when the check first started failing.
   *
   * @param string $firstFailingTime
   */
  public function setFirstFailingTime($firstFailingTime)
  {
    $this->firstFailingTime = $firstFailingTime;
  }
  /**
   * @return string
   */
  public function getFirstFailingTime()
  {
    return $this->firstFailingTime;
  }
  /**
   * The last time the check failed.
   *
   * @param string $lastFailingTime
   */
  public function setLastFailingTime($lastFailingTime)
  {
    $this->lastFailingTime = $lastFailingTime;
  }
  /**
   * @return string
   */
  public function getLastFailingTime()
  {
    return $this->lastFailingTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaCheckStateMetadata::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaCheckStateMetadata');
