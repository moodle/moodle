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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1OperationMetadata extends \Google\Collection
{
  public const OPERATION_TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  public const OPERATION_TYPE_INSERT = 'INSERT';
  public const OPERATION_TYPE_DELETE = 'DELETE';
  public const OPERATION_TYPE_UPDATE = 'UPDATE';
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  public const STATE_FINISHED = 'FINISHED';
  protected $collection_key = 'warnings';
  /**
   * @var string
   */
  public $operationType;
  protected $progressType = GoogleCloudApigeeV1OperationMetadataProgress::class;
  protected $progressDataType = '';
  /**
   * @var string
   */
  public $state;
  /**
   * Name of the resource for which the operation is operating on.
   *
   * @var string
   */
  public $targetResourceName;
  /**
   * Warnings encountered while executing the operation.
   *
   * @var string[]
   */
  public $warnings;

  /**
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * Progress of the operation.
   *
   * @param GoogleCloudApigeeV1OperationMetadataProgress $progress
   */
  public function setProgress(GoogleCloudApigeeV1OperationMetadataProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return GoogleCloudApigeeV1OperationMetadataProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
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
   * Name of the resource for which the operation is operating on.
   *
   * @param string $targetResourceName
   */
  public function setTargetResourceName($targetResourceName)
  {
    $this->targetResourceName = $targetResourceName;
  }
  /**
   * @return string
   */
  public function getTargetResourceName()
  {
    return $this->targetResourceName;
  }
  /**
   * Warnings encountered while executing the operation.
   *
   * @param string[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return string[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1OperationMetadata::class, 'Google_Service_Apigee_GoogleCloudApigeeV1OperationMetadata');
