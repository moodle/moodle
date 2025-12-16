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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1OperationMetadata extends \Google\Model
{
  /**
   * Unspecified operation type.
   */
  public const OPERATION_TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * An operation to create a new version.
   */
  public const OPERATION_TYPE_CREATE_VERSION = 'CREATE_VERSION';
  /**
   * An operation to delete an existing version.
   */
  public const OPERATION_TYPE_DELETE_VERSION = 'DELETE_VERSION';
  /**
   * An operation to delete an existing model.
   */
  public const OPERATION_TYPE_DELETE_MODEL = 'DELETE_MODEL';
  /**
   * An operation to update an existing model.
   */
  public const OPERATION_TYPE_UPDATE_MODEL = 'UPDATE_MODEL';
  /**
   * An operation to update an existing version.
   */
  public const OPERATION_TYPE_UPDATE_VERSION = 'UPDATE_VERSION';
  /**
   * An operation to update project configuration.
   */
  public const OPERATION_TYPE_UPDATE_CONFIG = 'UPDATE_CONFIG';
  /**
   * The time the operation was submitted.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time operation processing completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * Indicates whether a request to cancel this operation has been made.
   *
   * @var bool
   */
  public $isCancellationRequested;
  /**
   * The user labels, inherited from the model or the model version being
   * operated on.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Contains the name of the model associated with the operation.
   *
   * @var string
   */
  public $modelName;
  /**
   * The operation type.
   *
   * @var string
   */
  public $operationType;
  /**
   * Contains the project number associated with the operation.
   *
   * @var string
   */
  public $projectNumber;
  /**
   * The time operation processing started.
   *
   * @var string
   */
  public $startTime;
  protected $versionType = GoogleCloudMlV1Version::class;
  protected $versionDataType = '';

  /**
   * The time the operation was submitted.
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
   * The time operation processing completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Indicates whether a request to cancel this operation has been made.
   *
   * @param bool $isCancellationRequested
   */
  public function setIsCancellationRequested($isCancellationRequested)
  {
    $this->isCancellationRequested = $isCancellationRequested;
  }
  /**
   * @return bool
   */
  public function getIsCancellationRequested()
  {
    return $this->isCancellationRequested;
  }
  /**
   * The user labels, inherited from the model or the model version being
   * operated on.
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
   * Contains the name of the model associated with the operation.
   *
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }
  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }
  /**
   * The operation type.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, CREATE_VERSION,
   * DELETE_VERSION, DELETE_MODEL, UPDATE_MODEL, UPDATE_VERSION, UPDATE_CONFIG
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
   * Contains the project number associated with the operation.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * The time operation processing started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Contains the version associated with the operation.
   *
   * @param GoogleCloudMlV1Version $version
   */
  public function setVersion(GoogleCloudMlV1Version $version)
  {
    $this->version = $version;
  }
  /**
   * @return GoogleCloudMlV1Version
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1OperationMetadata::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1OperationMetadata');
