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

namespace Google\Service\CloudComposer;

class OperationMetadata extends \Google\Model
{
  /**
   * Unused.
   */
  public const OPERATION_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A resource creation operation.
   */
  public const OPERATION_TYPE_CREATE = 'CREATE';
  /**
   * A resource deletion operation.
   */
  public const OPERATION_TYPE_DELETE = 'DELETE';
  /**
   * A resource update operation.
   */
  public const OPERATION_TYPE_UPDATE = 'UPDATE';
  /**
   * A resource check operation.
   */
  public const OPERATION_TYPE_CHECK = 'CHECK';
  /**
   * Saves snapshot of the resource operation.
   */
  public const OPERATION_TYPE_SAVE_SNAPSHOT = 'SAVE_SNAPSHOT';
  /**
   * Loads snapshot of the resource operation.
   */
  public const OPERATION_TYPE_LOAD_SNAPSHOT = 'LOAD_SNAPSHOT';
  /**
   * Triggers failover of environment's Cloud SQL instance (only for highly
   * resilient environments).
   */
  public const OPERATION_TYPE_DATABASE_FAILOVER = 'DATABASE_FAILOVER';
  /**
   * Migrates resource to a new major version.
   */
  public const OPERATION_TYPE_MIGRATE = 'MIGRATE';
  /**
   * Unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The operation has been created but is not yet started.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The operation is underway.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The operation completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  public const STATE_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * The operation is no longer running but did not succeed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The time the operation was submitted to the server.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the operation terminated, regardless of its
   * success. This field is unset if the operation is still ongoing.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. The type of operation being performed.
   *
   * @var string
   */
  public $operationType;
  /**
   * Output only. The resource being operated on, as a [relative resource name](
   * /apis/design/resource_names#relative_resource_name).
   *
   * @var string
   */
  public $resource;
  /**
   * Output only. The UUID of the resource being operated on.
   *
   * @var string
   */
  public $resourceUuid;
  /**
   * Output only. The current operation state.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The time the operation was submitted to the server.
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
   * Output only. The time when the operation terminated, regardless of its
   * success. This field is unset if the operation is still ongoing.
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
   * Output only. The type of operation being performed.
   *
   * Accepted values: TYPE_UNSPECIFIED, CREATE, DELETE, UPDATE, CHECK,
   * SAVE_SNAPSHOT, LOAD_SNAPSHOT, DATABASE_FAILOVER, MIGRATE
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
   * Output only. The resource being operated on, as a [relative resource name](
   * /apis/design/resource_names#relative_resource_name).
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Output only. The UUID of the resource being operated on.
   *
   * @param string $resourceUuid
   */
  public function setResourceUuid($resourceUuid)
  {
    $this->resourceUuid = $resourceUuid;
  }
  /**
   * @return string
   */
  public function getResourceUuid()
  {
    return $this->resourceUuid;
  }
  /**
   * Output only. The current operation state.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED,
   * SUCCESSFUL, FAILED
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
class_alias(OperationMetadata::class, 'Google_Service_CloudComposer_OperationMetadata');
