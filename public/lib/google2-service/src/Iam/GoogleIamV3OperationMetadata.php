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

namespace Google\Service\Iam;

class GoogleIamV3OperationMetadata extends \Google\Model
{
  /**
   * Output only. API version used to start the operation.
   *
   * @var string
   */
  public $apiVersion;
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
  /**
   * Output only. Identifies whether the user has requested cancellation of the
   * operation. Operations that have successfully been cancelled have
   * Operation.error value with a google.rpc.Status.code of 1, corresponding to
   * `Code.CANCELLED`.
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
   * Output only. Server-defined resource path for the target of the
   *
   * @var string
   */
  public $target;
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
   * Output only. Identifies whether the user has requested cancellation of the
   * operation. Operations that have successfully been cancelled have
   * Operation.error value with a google.rpc.Status.code of 1, corresponding to
   * `Code.CANCELLED`.
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
   * Output only. Server-defined resource path for the target of the
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
class_alias(GoogleIamV3OperationMetadata::class, 'Google_Service_Iam_GoogleIamV3OperationMetadata');
