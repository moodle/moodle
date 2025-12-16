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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaExecutionDetails extends \Google\Collection
{
  /**
   * Default.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Execution is scheduled and awaiting to be triggered.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Execution is processing.
   */
  public const STATE_PROCESSING = 'PROCESSING';
  /**
   * Execution successfully finished. There's no more change after this state.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Execution failed. There's no more change after this state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Execution canceled by user. There's no more change after this state.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Execution failed and waiting for retry.
   */
  public const STATE_RETRY_ON_HOLD = 'RETRY_ON_HOLD';
  /**
   * Execution suspended and waiting for manual intervention.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'executionSnapshots';
  protected $attemptStatsType = GoogleCloudIntegrationsV1alphaAttemptStats::class;
  protected $attemptStatsDataType = 'array';
  /**
   * Total size of all event_execution_snapshots for an execution
   *
   * @var string
   */
  public $eventExecutionSnapshotsSize;
  protected $executionSnapshotsType = GoogleCloudIntegrationsV1alphaExecutionSnapshot::class;
  protected $executionSnapshotsDataType = 'array';
  /**
   * Status of the execution.
   *
   * @var string
   */
  public $state;

  /**
   * List of Start and end time of the execution attempts.
   *
   * @param GoogleCloudIntegrationsV1alphaAttemptStats[] $attemptStats
   */
  public function setAttemptStats($attemptStats)
  {
    $this->attemptStats = $attemptStats;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaAttemptStats[]
   */
  public function getAttemptStats()
  {
    return $this->attemptStats;
  }
  /**
   * Total size of all event_execution_snapshots for an execution
   *
   * @param string $eventExecutionSnapshotsSize
   */
  public function setEventExecutionSnapshotsSize($eventExecutionSnapshotsSize)
  {
    $this->eventExecutionSnapshotsSize = $eventExecutionSnapshotsSize;
  }
  /**
   * @return string
   */
  public function getEventExecutionSnapshotsSize()
  {
    return $this->eventExecutionSnapshotsSize;
  }
  /**
   * List of snapshots taken during the execution.
   *
   * @param GoogleCloudIntegrationsV1alphaExecutionSnapshot[] $executionSnapshots
   */
  public function setExecutionSnapshots($executionSnapshots)
  {
    $this->executionSnapshots = $executionSnapshots;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaExecutionSnapshot[]
   */
  public function getExecutionSnapshots()
  {
    return $this->executionSnapshots;
  }
  /**
   * Status of the execution.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, PROCESSING, SUCCEEDED, FAILED,
   * CANCELLED, RETRY_ON_HOLD, SUSPENDED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaExecutionDetails::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaExecutionDetails');
