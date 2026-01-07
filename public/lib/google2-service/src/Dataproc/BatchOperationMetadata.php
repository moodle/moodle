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

namespace Google\Service\Dataproc;

class BatchOperationMetadata extends \Google\Collection
{
  /**
   * Batch operation type is unknown.
   */
  public const OPERATION_TYPE_BATCH_OPERATION_TYPE_UNSPECIFIED = 'BATCH_OPERATION_TYPE_UNSPECIFIED';
  /**
   * Batch operation type.
   */
  public const OPERATION_TYPE_BATCH = 'BATCH';
  protected $collection_key = 'warnings';
  /**
   * Name of the batch for the operation.
   *
   * @var string
   */
  public $batch;
  /**
   * Batch UUID for the operation.
   *
   * @var string
   */
  public $batchUuid;
  /**
   * The time when the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Short description of the operation.
   *
   * @var string
   */
  public $description;
  /**
   * The time when the operation finished.
   *
   * @var string
   */
  public $doneTime;
  /**
   * Labels associated with the operation.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The operation type.
   *
   * @var string
   */
  public $operationType;
  /**
   * Warnings encountered during operation execution.
   *
   * @var string[]
   */
  public $warnings;

  /**
   * Name of the batch for the operation.
   *
   * @param string $batch
   */
  public function setBatch($batch)
  {
    $this->batch = $batch;
  }
  /**
   * @return string
   */
  public function getBatch()
  {
    return $this->batch;
  }
  /**
   * Batch UUID for the operation.
   *
   * @param string $batchUuid
   */
  public function setBatchUuid($batchUuid)
  {
    $this->batchUuid = $batchUuid;
  }
  /**
   * @return string
   */
  public function getBatchUuid()
  {
    return $this->batchUuid;
  }
  /**
   * The time when the operation was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Short description of the operation.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The time when the operation finished.
   *
   * @param string $doneTime
   */
  public function setDoneTime($doneTime)
  {
    $this->doneTime = $doneTime;
  }
  /**
   * @return string
   */
  public function getDoneTime()
  {
    return $this->doneTime;
  }
  /**
   * Labels associated with the operation.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The operation type.
   *
   * Accepted values: BATCH_OPERATION_TYPE_UNSPECIFIED, BATCH
   *
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
   * Warnings encountered during operation execution.
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
class_alias(BatchOperationMetadata::class, 'Google_Service_Dataproc_BatchOperationMetadata');
