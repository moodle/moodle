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

namespace Google\Service\GKEOnPrem;

class OperationMetadata extends \Google\Model
{
  /**
   * Not set.
   */
  public const TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * The resource is being created.
   */
  public const TYPE_CREATE = 'CREATE';
  /**
   * The resource is being deleted.
   */
  public const TYPE_DELETE = 'DELETE';
  /**
   * The resource is being updated.
   */
  public const TYPE_UPDATE = 'UPDATE';
  /**
   * The resource is being upgraded.
   */
  public const TYPE_UPGRADE = 'UPGRADE';
  /**
   * The platform is being upgraded.
   */
  public const TYPE_PLATFORM_UPGRADE = 'PLATFORM_UPGRADE';
  /**
   * Output only. API version used to start the operation.
   *
   * @var string
   */
  public $apiVersion;
  /**
   * Output only. Denotes if the local managing cluster's control plane is
   * currently disconnected. This is expected to occur temporarily during self-
   * managed cluster upgrades.
   *
   * @var bool
   */
  public $controlPlaneDisconnected;
  /**
   * Output only. The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time the operation finished running.
   *
   * @var string
   */
  public $endTime;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';
  /**
   * Output only. Identifies whether the user has requested cancellation of the
   * operation. Operations that have successfully been cancelled have
   * [Operation.error] value with a [google.rpc.Status.code] of 1, corresponding
   * to `Code.CANCELLED`.
   *
   * @var bool
   */
  public $requestedCancellation;
  /**
   * Output only. Human-readable status of the operation, if any.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * Output only. Server-defined resource path for the target of the operation.
   *
   * @var string
   */
  public $target;
  /**
   * Output only. Type of operation being executed.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Name of the verb executed by the operation.
   *
   * @var string
   */
  public $verb;

  /**
   * Output only. API version used to start the operation.
   *
   * @param string $apiVersion
   */
  public function setApiVersion($apiVersion)
  {
    $this->apiVersion = $apiVersion;
  }
  /**
   * @return string
   */
  public function getApiVersion()
  {
    return $this->apiVersion;
  }
  /**
   * Output only. Denotes if the local managing cluster's control plane is
   * currently disconnected. This is expected to occur temporarily during self-
   * managed cluster upgrades.
   *
   * @param bool $controlPlaneDisconnected
   */
  public function setControlPlaneDisconnected($controlPlaneDisconnected)
  {
    $this->controlPlaneDisconnected = $controlPlaneDisconnected;
  }
  /**
   * @return bool
   */
  public function getControlPlaneDisconnected()
  {
    return $this->controlPlaneDisconnected;
  }
  /**
   * Output only. The time the operation was created.
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
   * Output only. The time the operation finished running.
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
   * Output only. Detailed progress information for the operation.
   *
   * @param OperationProgress $progress
   */
  public function setProgress(OperationProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return OperationProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * Output only. Identifies whether the user has requested cancellation of the
   * operation. Operations that have successfully been cancelled have
   * [Operation.error] value with a [google.rpc.Status.code] of 1, corresponding
   * to `Code.CANCELLED`.
   *
   * @param bool $requestedCancellation
   */
  public function setRequestedCancellation($requestedCancellation)
  {
    $this->requestedCancellation = $requestedCancellation;
  }
  /**
   * @return bool
   */
  public function getRequestedCancellation()
  {
    return $this->requestedCancellation;
  }
  /**
   * Output only. Human-readable status of the operation, if any.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. Server-defined resource path for the target of the operation.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Output only. Type of operation being executed.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, CREATE, DELETE, UPDATE,
   * UPGRADE, PLATFORM_UPGRADE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Name of the verb executed by the operation.
   *
   * @param string $verb
   */
  public function setVerb($verb)
  {
    $this->verb = $verb;
  }
  /**
   * @return string
   */
  public function getVerb()
  {
    return $this->verb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationMetadata::class, 'Google_Service_GKEOnPrem_OperationMetadata');
