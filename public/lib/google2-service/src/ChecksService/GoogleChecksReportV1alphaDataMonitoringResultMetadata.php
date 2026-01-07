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

class GoogleChecksReportV1alphaDataMonitoringResultMetadata extends \Google\Collection
{
  protected $collection_key = 'badges';
  /**
   * Badges that apply to this result.
   *
   * @var string[]
   */
  public $badges;
  /**
   * The timestamp when this result was first detected within the last 8 weeks.
   * If not set, it wasn't detected within the last 8 weeks.
   *
   * @var string
   */
  public $firstDetectedTime;
  /**
   * Your app's version name when this result was last detected within the last
   * 8 weeks. If not set, it wasn't detected within the last 8 weeks.
   *
   * @var string
   */
  public $lastDetectedAppVersion;
  /**
   * The timestamp when this result was last detected within the last 8 weeks.
   * If not set, it wasn't detected within the last 8 weeks.
   *
   * @var string
   */
  public $lastDetectedTime;

  /**
   * Badges that apply to this result.
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
   * The timestamp when this result was first detected within the last 8 weeks.
   * If not set, it wasn't detected within the last 8 weeks.
   *
   * @param string $firstDetectedTime
   */
  public function setFirstDetectedTime($firstDetectedTime)
  {
    $this->firstDetectedTime = $firstDetectedTime;
  }
  /**
   * @return string
   */
  public function getFirstDetectedTime()
  {
    return $this->firstDetectedTime;
  }
  /**
   * Your app's version name when this result was last detected within the last
   * 8 weeks. If not set, it wasn't detected within the last 8 weeks.
   *
   * @param string $lastDetectedAppVersion
   */
  public function setLastDetectedAppVersion($lastDetectedAppVersion)
  {
    $this->lastDetectedAppVersion = $lastDetectedAppVersion;
  }
  /**
   * @return string
   */
  public function getLastDetectedAppVersion()
  {
    return $this->lastDetectedAppVersion;
  }
  /**
   * The timestamp when this result was last detected within the last 8 weeks.
   * If not set, it wasn't detected within the last 8 weeks.
   *
   * @param string $lastDetectedTime
   */
  public function setLastDetectedTime($lastDetectedTime)
  {
    $this->lastDetectedTime = $lastDetectedTime;
  }
  /**
   * @return string
   */
  public function getLastDetectedTime()
  {
    return $this->lastDetectedTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaDataMonitoringResultMetadata::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaDataMonitoringResultMetadata');
