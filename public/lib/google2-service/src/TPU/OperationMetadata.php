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

namespace Google\Service\TPU;

class OperationMetadata extends \Google\Model
{
  /**
   * API version.
   *
   * @var string
   */
  public $apiVersion;
  /**
   * Specifies if cancellation was requested for the operation.
   *
   * @var bool
   */
  public $cancelRequested;
  /**
   * The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time the operation finished running.
   *
   * @var string
   */
  public $endTime;
  /**
   * Human-readable status of the operation, if any.
   *
   * @var string
   */
  public $statusDetail;
  /**
   * Target of the operation - for example
   * projects/project-1/connectivityTests/test-1
   *
   * @var string
   */
  public $target;
  /**
   * Name of the verb executed by the operation.
   *
   * @var string
   */
  public $verb;

  /**
   * API version.
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
   * Specifies if cancellation was requested for the operation.
   *
   * @param bool $cancelRequested
   */
  public function setCancelRequested($cancelRequested)
  {
    $this->cancelRequested = $cancelRequested;
  }
  /**
   * @return bool
   */
  public function getCancelRequested()
  {
    return $this->cancelRequested;
  }
  /**
   * The time the operation was created.
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
   * The time the operation finished running.
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
   * Human-readable status of the operation, if any.
   *
   * @param string $statusDetail
   */
  public function setStatusDetail($statusDetail)
  {
    $this->statusDetail = $statusDetail;
  }
  /**
   * @return string
   */
  public function getStatusDetail()
  {
    return $this->statusDetail;
  }
  /**
   * Target of the operation - for example
   * projects/project-1/connectivityTests/test-1
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
   * Name of the verb executed by the operation.
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
class_alias(OperationMetadata::class, 'Google_Service_TPU_OperationMetadata');
