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

class Batch extends \Google\Collection
{
  /**
   * The batch state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The batch is created before running.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The batch is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The batch is cancelling.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The batch cancellation was successful.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The batch completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The batch is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'stateHistory';
  /**
   * Output only. The time when the batch was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The email address of the user who created the batch.
   *
   * @var string
   */
  public $creator;
  protected $environmentConfigType = EnvironmentConfig::class;
  protected $environmentConfigDataType = '';
  /**
   * Optional. The labels to associate with this batch. Label keys must contain
   * 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a batch.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the batch.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the operation associated with this batch.
   *
   * @var string
   */
  public $operation;
  protected $pysparkBatchType = PySparkBatch::class;
  protected $pysparkBatchDataType = '';
  protected $runtimeConfigType = RuntimeConfig::class;
  protected $runtimeConfigDataType = '';
  protected $runtimeInfoType = RuntimeInfo::class;
  protected $runtimeInfoDataType = '';
  protected $sparkBatchType = SparkBatch::class;
  protected $sparkBatchDataType = '';
  protected $sparkRBatchType = SparkRBatch::class;
  protected $sparkRBatchDataType = '';
  protected $sparkSqlBatchType = SparkSqlBatch::class;
  protected $sparkSqlBatchDataType = '';
  /**
   * Output only. The state of the batch.
   *
   * @var string
   */
  public $state;
  protected $stateHistoryType = StateHistory::class;
  protected $stateHistoryDataType = 'array';
  /**
   * Output only. Batch state details, such as a failure description if the
   * state is FAILED.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Output only. The time when the batch entered a current state.
   *
   * @var string
   */
  public $stateTime;
  /**
   * Output only. A batch UUID (Unique Universal Identifier). The service
   * generates this value when it creates the batch.
   *
   * @var string
   */
  public $uuid;

  /**
   * Output only. The time when the batch was created.
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
   * Output only. The email address of the user who created the batch.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Optional. Environment configuration for the batch execution.
   *
   * @param EnvironmentConfig $environmentConfig
   */
  public function setEnvironmentConfig(EnvironmentConfig $environmentConfig)
  {
    $this->environmentConfig = $environmentConfig;
  }
  /**
   * @return EnvironmentConfig
   */
  public function getEnvironmentConfig()
  {
    return $this->environmentConfig;
  }
  /**
   * Optional. The labels to associate with this batch. Label keys must contain
   * 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). Label values may be empty, but, if
   * present, must contain 1 to 63 characters, and must conform to RFC 1035
   * (https://www.ietf.org/rfc/rfc1035.txt). No more than 32 labels can be
   * associated with a batch.
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
   * Output only. The resource name of the batch.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The resource name of the operation associated with this batch.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Optional. PySpark batch config.
   *
   * @param PySparkBatch $pysparkBatch
   */
  public function setPysparkBatch(PySparkBatch $pysparkBatch)
  {
    $this->pysparkBatch = $pysparkBatch;
  }
  /**
   * @return PySparkBatch
   */
  public function getPysparkBatch()
  {
    return $this->pysparkBatch;
  }
  /**
   * Optional. Runtime configuration for the batch execution.
   *
   * @param RuntimeConfig $runtimeConfig
   */
  public function setRuntimeConfig(RuntimeConfig $runtimeConfig)
  {
    $this->runtimeConfig = $runtimeConfig;
  }
  /**
   * @return RuntimeConfig
   */
  public function getRuntimeConfig()
  {
    return $this->runtimeConfig;
  }
  /**
   * Output only. Runtime information about batch execution.
   *
   * @param RuntimeInfo $runtimeInfo
   */
  public function setRuntimeInfo(RuntimeInfo $runtimeInfo)
  {
    $this->runtimeInfo = $runtimeInfo;
  }
  /**
   * @return RuntimeInfo
   */
  public function getRuntimeInfo()
  {
    return $this->runtimeInfo;
  }
  /**
   * Optional. Spark batch config.
   *
   * @param SparkBatch $sparkBatch
   */
  public function setSparkBatch(SparkBatch $sparkBatch)
  {
    $this->sparkBatch = $sparkBatch;
  }
  /**
   * @return SparkBatch
   */
  public function getSparkBatch()
  {
    return $this->sparkBatch;
  }
  /**
   * Optional. SparkR batch config.
   *
   * @param SparkRBatch $sparkRBatch
   */
  public function setSparkRBatch(SparkRBatch $sparkRBatch)
  {
    $this->sparkRBatch = $sparkRBatch;
  }
  /**
   * @return SparkRBatch
   */
  public function getSparkRBatch()
  {
    return $this->sparkRBatch;
  }
  /**
   * Optional. SparkSql batch config.
   *
   * @param SparkSqlBatch $sparkSqlBatch
   */
  public function setSparkSqlBatch(SparkSqlBatch $sparkSqlBatch)
  {
    $this->sparkSqlBatch = $sparkSqlBatch;
  }
  /**
   * @return SparkSqlBatch
   */
  public function getSparkSqlBatch()
  {
    return $this->sparkSqlBatch;
  }
  /**
   * Output only. The state of the batch.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, CANCELLING,
   * CANCELLED, SUCCEEDED, FAILED
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
   * Output only. Historical state information for the batch.
   *
   * @param StateHistory[] $stateHistory
   */
  public function setStateHistory($stateHistory)
  {
    $this->stateHistory = $stateHistory;
  }
  /**
   * @return StateHistory[]
   */
  public function getStateHistory()
  {
    return $this->stateHistory;
  }
  /**
   * Output only. Batch state details, such as a failure description if the
   * state is FAILED.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * Output only. The time when the batch entered a current state.
   *
   * @param string $stateTime
   */
  public function setStateTime($stateTime)
  {
    $this->stateTime = $stateTime;
  }
  /**
   * @return string
   */
  public function getStateTime()
  {
    return $this->stateTime;
  }
  /**
   * Output only. A batch UUID (Unique Universal Identifier). The service
   * generates this value when it creates the batch.
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Batch::class, 'Google_Service_Dataproc_Batch');
