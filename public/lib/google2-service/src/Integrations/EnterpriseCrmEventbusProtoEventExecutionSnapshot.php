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

class EnterpriseCrmEventbusProtoEventExecutionSnapshot extends \Google\Collection
{
  protected $collection_key = 'taskExecutionDetails';
  /**
   * Indicates "right after which checkpoint task's execution" this snapshot is
   * taken.
   *
   * @var string
   */
  public $checkpointTaskNumber;
  /**
   * Client that the execution snapshot is associated to.
   *
   * @var string
   */
  public $clientId;
  protected $conditionResultsType = EnterpriseCrmEventbusProtoConditionResult::class;
  protected $conditionResultsDataType = 'array';
  protected $diffParamsType = EnterpriseCrmEventbusProtoEventParameters::class;
  protected $diffParamsDataType = '';
  /**
   * Points to the event execution info this snapshot belongs to.
   *
   * @var string
   */
  public $eventExecutionInfoId;
  /**
   * Auto-generated. Used as primary key for EventExecutionSnapshots table.
   *
   * @var string
   */
  public $eventExecutionSnapshotId;
  protected $eventExecutionSnapshotMetadataType = EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata::class;
  protected $eventExecutionSnapshotMetadataDataType = '';
  protected $eventParamsType = EnterpriseCrmEventbusProtoEventParameters::class;
  protected $eventParamsDataType = '';
  /**
   * indicate whether snapshot exceeded maximum size before clean up
   *
   * @var bool
   */
  public $exceedMaxSize;
  /**
   * Indicates when this snapshot is taken.
   *
   * @var string
   */
  public $snapshotTime;
  protected $taskExecutionDetailsType = EnterpriseCrmEventbusProtoTaskExecutionDetails::class;
  protected $taskExecutionDetailsDataType = 'array';
  /**
   * The task name associated with this snapshot. Could be empty.
   *
   * @deprecated
   * @var string
   */
  public $taskName;
  /**
   * Name of the workflow this event execution snapshot belongs to.
   *
   * @var string
   */
  public $workflowName;

  /**
   * Indicates "right after which checkpoint task's execution" this snapshot is
   * taken.
   *
   * @param string $checkpointTaskNumber
   */
  public function setCheckpointTaskNumber($checkpointTaskNumber)
  {
    $this->checkpointTaskNumber = $checkpointTaskNumber;
  }
  /**
   * @return string
   */
  public function getCheckpointTaskNumber()
  {
    return $this->checkpointTaskNumber;
  }
  /**
   * Client that the execution snapshot is associated to.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * All of the computed conditions that been calculated.
   *
   * @param EnterpriseCrmEventbusProtoConditionResult[] $conditionResults
   */
  public function setConditionResults($conditionResults)
  {
    $this->conditionResults = $conditionResults;
  }
  /**
   * @return EnterpriseCrmEventbusProtoConditionResult[]
   */
  public function getConditionResults()
  {
    return $this->conditionResults;
  }
  /**
   * The parameters in Event object that differs from last snapshot.
   *
   * @param EnterpriseCrmEventbusProtoEventParameters $diffParams
   */
  public function setDiffParams(EnterpriseCrmEventbusProtoEventParameters $diffParams)
  {
    $this->diffParams = $diffParams;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventParameters
   */
  public function getDiffParams()
  {
    return $this->diffParams;
  }
  /**
   * Points to the event execution info this snapshot belongs to.
   *
   * @param string $eventExecutionInfoId
   */
  public function setEventExecutionInfoId($eventExecutionInfoId)
  {
    $this->eventExecutionInfoId = $eventExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getEventExecutionInfoId()
  {
    return $this->eventExecutionInfoId;
  }
  /**
   * Auto-generated. Used as primary key for EventExecutionSnapshots table.
   *
   * @param string $eventExecutionSnapshotId
   */
  public function setEventExecutionSnapshotId($eventExecutionSnapshotId)
  {
    $this->eventExecutionSnapshotId = $eventExecutionSnapshotId;
  }
  /**
   * @return string
   */
  public function getEventExecutionSnapshotId()
  {
    return $this->eventExecutionSnapshotId;
  }
  /**
   * @param EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata $eventExecutionSnapshotMetadata
   */
  public function setEventExecutionSnapshotMetadata(EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata $eventExecutionSnapshotMetadata)
  {
    $this->eventExecutionSnapshotMetadata = $eventExecutionSnapshotMetadata;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventExecutionSnapshotEventExecutionSnapshotMetadata
   */
  public function getEventExecutionSnapshotMetadata()
  {
    return $this->eventExecutionSnapshotMetadata;
  }
  /**
   * The parameters in Event object.
   *
   * @param EnterpriseCrmEventbusProtoEventParameters $eventParams
   */
  public function setEventParams(EnterpriseCrmEventbusProtoEventParameters $eventParams)
  {
    $this->eventParams = $eventParams;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventParameters
   */
  public function getEventParams()
  {
    return $this->eventParams;
  }
  /**
   * indicate whether snapshot exceeded maximum size before clean up
   *
   * @param bool $exceedMaxSize
   */
  public function setExceedMaxSize($exceedMaxSize)
  {
    $this->exceedMaxSize = $exceedMaxSize;
  }
  /**
   * @return bool
   */
  public function getExceedMaxSize()
  {
    return $this->exceedMaxSize;
  }
  /**
   * Indicates when this snapshot is taken.
   *
   * @param string $snapshotTime
   */
  public function setSnapshotTime($snapshotTime)
  {
    $this->snapshotTime = $snapshotTime;
  }
  /**
   * @return string
   */
  public function getSnapshotTime()
  {
    return $this->snapshotTime;
  }
  /**
   * All of the task execution details at the given point of time.
   *
   * @param EnterpriseCrmEventbusProtoTaskExecutionDetails[] $taskExecutionDetails
   */
  public function setTaskExecutionDetails($taskExecutionDetails)
  {
    $this->taskExecutionDetails = $taskExecutionDetails;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTaskExecutionDetails[]
   */
  public function getTaskExecutionDetails()
  {
    return $this->taskExecutionDetails;
  }
  /**
   * The task name associated with this snapshot. Could be empty.
   *
   * @deprecated
   * @param string $taskName
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }
  /**
   * Name of the workflow this event execution snapshot belongs to.
   *
   * @param string $workflowName
   */
  public function setWorkflowName($workflowName)
  {
    $this->workflowName = $workflowName;
  }
  /**
   * @return string
   */
  public function getWorkflowName()
  {
    return $this->workflowName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoEventExecutionSnapshot::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoEventExecutionSnapshot');
