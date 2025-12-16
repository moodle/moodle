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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1AssetDiscoveryStatus extends \Google\Model
{
  /**
   * State is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Discovery for the asset is scheduled.
   */
  public const STATE_SCHEDULED = 'SCHEDULED';
  /**
   * Discovery for the asset is running.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Discovery for the asset is currently paused (e.g. due to a lack of
   * available resources). It will be automatically resumed.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * Discovery for the asset is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The duration of the last discovery run.
   *
   * @var string
   */
  public $lastRunDuration;
  /**
   * The start time of the last discovery run.
   *
   * @var string
   */
  public $lastRunTime;
  /**
   * Additional information about the current state.
   *
   * @var string
   */
  public $message;
  /**
   * The current status of the discovery feature.
   *
   * @var string
   */
  public $state;
  protected $statsType = GoogleCloudDataplexV1AssetDiscoveryStatusStats::class;
  protected $statsDataType = '';
  /**
   * Last update time of the status.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The duration of the last discovery run.
   *
   * @param string $lastRunDuration
   */
  public function setLastRunDuration($lastRunDuration)
  {
    $this->lastRunDuration = $lastRunDuration;
  }
  /**
   * @return string
   */
  public function getLastRunDuration()
  {
    return $this->lastRunDuration;
  }
  /**
   * The start time of the last discovery run.
   *
   * @param string $lastRunTime
   */
  public function setLastRunTime($lastRunTime)
  {
    $this->lastRunTime = $lastRunTime;
  }
  /**
   * @return string
   */
  public function getLastRunTime()
  {
    return $this->lastRunTime;
  }
  /**
   * Additional information about the current state.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The current status of the discovery feature.
   *
   * Accepted values: STATE_UNSPECIFIED, SCHEDULED, IN_PROGRESS, PAUSED,
   * DISABLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Data Stats of the asset reported by discovery.
   *
   * @param GoogleCloudDataplexV1AssetDiscoveryStatusStats $stats
   */
  public function setStats(GoogleCloudDataplexV1AssetDiscoveryStatusStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return GoogleCloudDataplexV1AssetDiscoveryStatusStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * Last update time of the status.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1AssetDiscoveryStatus::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AssetDiscoveryStatus');
