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

class EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata extends \Google\Collection
{
  protected $collection_key = 'ancestorTaskNumbers';
  /**
   * Ancestor iteration number for the task(it will only be non-empty if the
   * task is under 'private workflow')
   *
   * @var string[]
   */
  public $ancestorIterationNumbers;
  /**
   * Ancestor task number for the task(it will only be non-empty if the task is
   * under 'private workflow')
   *
   * @var string[]
   */
  public $ancestorTaskNumbers;
  /**
   * the event attempt number this snapshot belongs to.
   *
   * @var int
   */
  public $eventAttemptNum;
  /**
   * The direct integration which the event execution snapshots belongs to
   *
   * @var string
   */
  public $integrationName;
  /**
   * the task attempt number this snapshot belongs to. Could be empty.
   *
   * @var int
   */
  public $taskAttemptNum;
  /**
   * the task label associated with this snapshot. Could be empty.
   *
   * @var string
   */
  public $taskLabel;
  /**
   * the task name associated with this snapshot. Could be empty.
   *
   * @var string
   */
  public $taskName;
  /**
   * The task number associated with this snapshot. Could be empty.
   *
   * @var string
   */
  public $taskNumber;

  /**
   * Ancestor iteration number for the task(it will only be non-empty if the
   * task is under 'private workflow')
   *
   * @param string[] $ancestorIterationNumbers
   */
  public function setAncestorIterationNumbers($ancestorIterationNumbers)
  {
    $this->ancestorIterationNumbers = $ancestorIterationNumbers;
  }
  /**
   * @return string[]
   */
  public function getAncestorIterationNumbers()
  {
    return $this->ancestorIterationNumbers;
  }
  /**
   * Ancestor task number for the task(it will only be non-empty if the task is
   * under 'private workflow')
   *
   * @param string[] $ancestorTaskNumbers
   */
  public function setAncestorTaskNumbers($ancestorTaskNumbers)
  {
    $this->ancestorTaskNumbers = $ancestorTaskNumbers;
  }
  /**
   * @return string[]
   */
  public function getAncestorTaskNumbers()
  {
    return $this->ancestorTaskNumbers;
  }
  /**
   * the event attempt number this snapshot belongs to.
   *
   * @param int $eventAttemptNum
   */
  public function setEventAttemptNum($eventAttemptNum)
  {
    $this->eventAttemptNum = $eventAttemptNum;
  }
  /**
   * @return int
   */
  public function getEventAttemptNum()
  {
    return $this->eventAttemptNum;
  }
  /**
   * The direct integration which the event execution snapshots belongs to
   *
   * @param string $integrationName
   */
  public function setIntegrationName($integrationName)
  {
    $this->integrationName = $integrationName;
  }
  /**
   * @return string
   */
  public function getIntegrationName()
  {
    return $this->integrationName;
  }
  /**
   * the task attempt number this snapshot belongs to. Could be empty.
   *
   * @param int $taskAttemptNum
   */
  public function setTaskAttemptNum($taskAttemptNum)
  {
    $this->taskAttemptNum = $taskAttemptNum;
  }
  /**
   * @return int
   */
  public function getTaskAttemptNum()
  {
    return $this->taskAttemptNum;
  }
  /**
   * the task label associated with this snapshot. Could be empty.
   *
   * @param string $taskLabel
   */
  public function setTaskLabel($taskLabel)
  {
    $this->taskLabel = $taskLabel;
  }
  /**
   * @return string
   */
  public function getTaskLabel()
  {
    return $this->taskLabel;
  }
  /**
   * the task name associated with this snapshot. Could be empty.
   *
   * @param string $taskName
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;
  }
  /**
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }
  /**
   * The task number associated with this snapshot. Could be empty.
   *
   * @param string $taskNumber
   */
  public function setTaskNumber($taskNumber)
  {
    $this->taskNumber = $taskNumber;
  }
  /**
   * @return string
   */
  public function getTaskNumber()
  {
    return $this->taskNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata');
