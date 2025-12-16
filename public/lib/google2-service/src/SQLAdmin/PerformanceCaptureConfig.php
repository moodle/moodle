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

namespace Google\Service\SQLAdmin;

class PerformanceCaptureConfig extends \Google\Model
{
  /**
   * Optional. Enable or disable the Performance Capture feature.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. The minimum number of consecutive readings above threshold that
   * triggers instance state capture.
   *
   * @var int
   */
  public $probeThreshold;
  /**
   * Optional. The time interval in seconds between any two probes.
   *
   * @var int
   */
  public $probingIntervalSeconds;
  /**
   * Optional. The minimum number of server threads running to trigger the
   * capture on primary.
   *
   * @var int
   */
  public $runningThreadsThreshold;
  /**
   * Optional. The minimum number of seconds replica must be lagging behind
   * primary to trigger capture on replica.
   *
   * @var int
   */
  public $secondsBehindSourceThreshold;
  /**
   * Optional. The amount of time in seconds that a transaction needs to have
   * been open before the watcher starts recording it.
   *
   * @var int
   */
  public $transactionDurationThreshold;

  /**
   * Optional. Enable or disable the Performance Capture feature.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. The minimum number of consecutive readings above threshold that
   * triggers instance state capture.
   *
   * @param int $probeThreshold
   */
  public function setProbeThreshold($probeThreshold)
  {
    $this->probeThreshold = $probeThreshold;
  }
  /**
   * @return int
   */
  public function getProbeThreshold()
  {
    return $this->probeThreshold;
  }
  /**
   * Optional. The time interval in seconds between any two probes.
   *
   * @param int $probingIntervalSeconds
   */
  public function setProbingIntervalSeconds($probingIntervalSeconds)
  {
    $this->probingIntervalSeconds = $probingIntervalSeconds;
  }
  /**
   * @return int
   */
  public function getProbingIntervalSeconds()
  {
    return $this->probingIntervalSeconds;
  }
  /**
   * Optional. The minimum number of server threads running to trigger the
   * capture on primary.
   *
   * @param int $runningThreadsThreshold
   */
  public function setRunningThreadsThreshold($runningThreadsThreshold)
  {
    $this->runningThreadsThreshold = $runningThreadsThreshold;
  }
  /**
   * @return int
   */
  public function getRunningThreadsThreshold()
  {
    return $this->runningThreadsThreshold;
  }
  /**
   * Optional. The minimum number of seconds replica must be lagging behind
   * primary to trigger capture on replica.
   *
   * @param int $secondsBehindSourceThreshold
   */
  public function setSecondsBehindSourceThreshold($secondsBehindSourceThreshold)
  {
    $this->secondsBehindSourceThreshold = $secondsBehindSourceThreshold;
  }
  /**
   * @return int
   */
  public function getSecondsBehindSourceThreshold()
  {
    return $this->secondsBehindSourceThreshold;
  }
  /**
   * Optional. The amount of time in seconds that a transaction needs to have
   * been open before the watcher starts recording it.
   *
   * @param int $transactionDurationThreshold
   */
  public function setTransactionDurationThreshold($transactionDurationThreshold)
  {
    $this->transactionDurationThreshold = $transactionDurationThreshold;
  }
  /**
   * @return int
   */
  public function getTransactionDurationThreshold()
  {
    return $this->transactionDurationThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceCaptureConfig::class, 'Google_Service_SQLAdmin_PerformanceCaptureConfig');
