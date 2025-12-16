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

class EnterpriseCrmEventbusProtoEventExecutionDetails extends \Google\Collection
{
  public const EVENT_EXECUTION_STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Event is received and waiting for the execution. This happens when firing
   * the event via "postToQueue" or "schedule".
   */
  public const EVENT_EXECUTION_STATE_ON_HOLD = 'ON_HOLD';
  /**
   * Event is under processing.
   */
  public const EVENT_EXECUTION_STATE_IN_PROCESS = 'IN_PROCESS';
  /**
   * Event execution successfully finished. There's no more change after this
   * state.
   */
  public const EVENT_EXECUTION_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Event execution failed. There's no more change after this state.
   */
  public const EVENT_EXECUTION_STATE_FAILED = 'FAILED';
  /**
   * Event execution canceled by user. There's no more change after this state.
   */
  public const EVENT_EXECUTION_STATE_CANCELED = 'CANCELED';
  /**
   * Event execution failed and waiting for retry.
   */
  public const EVENT_EXECUTION_STATE_RETRY_ON_HOLD = 'RETRY_ON_HOLD';
  /**
   * Event execution suspended and waiting for manual intervention.
   */
  public const EVENT_EXECUTION_STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'eventExecutionSnapshot';
  /**
   * If the execution is manually canceled, this field will contain the reason
   * for cancellation.
   *
   * @var string
   */
  public $cancelReason;
  protected $eventAttemptStatsType = EnterpriseCrmEventbusProtoEventExecutionDetailsEventAttemptStats::class;
  protected $eventAttemptStatsDataType = 'array';
  protected $eventExecutionSnapshotType = EnterpriseCrmEventbusProtoEventExecutionSnapshot::class;
  protected $eventExecutionSnapshotDataType = 'array';
  /**
   * Total size of all event_execution_snapshots for an execution
   *
   * @var string
   */
  public $eventExecutionSnapshotsSize;
  /**
   * @var string
   */
  public $eventExecutionState;
  /**
   * Indicates the number of times the execution has restarted from the
   * beginning.
   *
   * @var int
   */
  public $eventRetriesFromBeginningCount;
  /**
   * The log file path (aka. cns address) for this event.
   *
   * @var string
   */
  public $logFilePath;
  /**
   * The network address (aka. bns address) that indicates where the event
   * executor is running.
   *
   * @var string
   */
  public $networkAddress;
  /**
   * Next scheduled execution time in case the execution status was
   * RETRY_ON_HOLD.
   *
   * @var string
   */
  public $nextExecutionTime;
  /**
   * Used internally and shouldn't be exposed to users. A counter for the cron
   * job to record how many times this event is in in_process state but don't
   * have a lock consecutively/
   *
   * @var int
   */
  public $ryeLockUnheldCount;

  /**
   * If the execution is manually canceled, this field will contain the reason
   * for cancellation.
   *
   * @param string $cancelReason
   */
  public function setCancelReason($cancelReason)
  {
    $this->cancelReason = $cancelReason;
  }
  /**
   * @return string
   */
  public function getCancelReason()
  {
    return $this->cancelReason;
  }
  /**
   * @param EnterpriseCrmEventbusProtoEventExecutionDetailsEventAttemptStats[] $eventAttemptStats
   */
  public function setEventAttemptStats($eventAttemptStats)
  {
    $this->eventAttemptStats = $eventAttemptStats;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventExecutionDetailsEventAttemptStats[]
   */
  public function getEventAttemptStats()
  {
    return $this->eventAttemptStats;
  }
  /**
   * @param EnterpriseCrmEventbusProtoEventExecutionSnapshot[] $eventExecutionSnapshot
   */
  public function setEventExecutionSnapshot($eventExecutionSnapshot)
  {
    $this->eventExecutionSnapshot = $eventExecutionSnapshot;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventExecutionSnapshot[]
   */
  public function getEventExecutionSnapshot()
  {
    return $this->eventExecutionSnapshot;
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
   * @param self::EVENT_EXECUTION_STATE_* $eventExecutionState
   */
  public function setEventExecutionState($eventExecutionState)
  {
    $this->eventExecutionState = $eventExecutionState;
  }
  /**
   * @return self::EVENT_EXECUTION_STATE_*
   */
  public function getEventExecutionState()
  {
    return $this->eventExecutionState;
  }
  /**
   * Indicates the number of times the execution has restarted from the
   * beginning.
   *
   * @param int $eventRetriesFromBeginningCount
   */
  public function setEventRetriesFromBeginningCount($eventRetriesFromBeginningCount)
  {
    $this->eventRetriesFromBeginningCount = $eventRetriesFromBeginningCount;
  }
  /**
   * @return int
   */
  public function getEventRetriesFromBeginningCount()
  {
    return $this->eventRetriesFromBeginningCount;
  }
  /**
   * The log file path (aka. cns address) for this event.
   *
   * @param string $logFilePath
   */
  public function setLogFilePath($logFilePath)
  {
    $this->logFilePath = $logFilePath;
  }
  /**
   * @return string
   */
  public function getLogFilePath()
  {
    return $this->logFilePath;
  }
  /**
   * The network address (aka. bns address) that indicates where the event
   * executor is running.
   *
   * @param string $networkAddress
   */
  public function setNetworkAddress($networkAddress)
  {
    $this->networkAddress = $networkAddress;
  }
  /**
   * @return string
   */
  public function getNetworkAddress()
  {
    return $this->networkAddress;
  }
  /**
   * Next scheduled execution time in case the execution status was
   * RETRY_ON_HOLD.
   *
   * @param string $nextExecutionTime
   */
  public function setNextExecutionTime($nextExecutionTime)
  {
    $this->nextExecutionTime = $nextExecutionTime;
  }
  /**
   * @return string
   */
  public function getNextExecutionTime()
  {
    return $this->nextExecutionTime;
  }
  /**
   * Used internally and shouldn't be exposed to users. A counter for the cron
   * job to record how many times this event is in in_process state but don't
   * have a lock consecutively/
   *
   * @param int $ryeLockUnheldCount
   */
  public function setRyeLockUnheldCount($ryeLockUnheldCount)
  {
    $this->ryeLockUnheldCount = $ryeLockUnheldCount;
  }
  /**
   * @return int
   */
  public function getRyeLockUnheldCount()
  {
    return $this->ryeLockUnheldCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoEventExecutionDetails::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoEventExecutionDetails');
