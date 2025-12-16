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

namespace Google\Service\Appengine;

class OperationMetadataV1Beta extends \Google\Collection
{
  protected $collection_key = 'warning';
  protected $createVersionMetadataType = CreateVersionMetadataV1Beta::class;
  protected $createVersionMetadataDataType = '';
  /**
   * Time that this operation completed.@OutputOnly
   *
   * @var string
   */
  public $endTime;
  /**
   * Ephemeral message that may change every time the operation is polled.
   * @OutputOnly
   *
   * @var string
   */
  public $ephemeralMessage;
  /**
   * Time that this operation was created.@OutputOnly
   *
   * @var string
   */
  public $insertTime;
  /**
   * API method that initiated this operation. Example:
   * google.appengine.v1beta.Versions.CreateVersion.@OutputOnly
   *
   * @var string
   */
  public $method;
  /**
   * Name of the resource that this operation is acting on. Example:
   * apps/myapp/services/default.@OutputOnly
   *
   * @var string
   */
  public $target;
  /**
   * User who requested this operation.@OutputOnly
   *
   * @var string
   */
  public $user;
  /**
   * Durable messages that persist on every operation poll. @OutputOnly
   *
   * @var string[]
   */
  public $warning;

  /**
   * @param CreateVersionMetadataV1Beta $createVersionMetadata
   */
  public function setCreateVersionMetadata(CreateVersionMetadataV1Beta $createVersionMetadata)
  {
    $this->createVersionMetadata = $createVersionMetadata;
  }
  /**
   * @return CreateVersionMetadataV1Beta
   */
  public function getCreateVersionMetadata()
  {
    return $this->createVersionMetadata;
  }
  /**
   * Time that this operation completed.@OutputOnly
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
   * Ephemeral message that may change every time the operation is polled.
   * @OutputOnly
   *
   * @param string $ephemeralMessage
   */
  public function setEphemeralMessage($ephemeralMessage)
  {
    $this->ephemeralMessage = $ephemeralMessage;
  }
  /**
   * @return string
   */
  public function getEphemeralMessage()
  {
    return $this->ephemeralMessage;
  }
  /**
   * Time that this operation was created.@OutputOnly
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * API method that initiated this operation. Example:
   * google.appengine.v1beta.Versions.CreateVersion.@OutputOnly
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Name of the resource that this operation is acting on. Example:
   * apps/myapp/services/default.@OutputOnly
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
   * User who requested this operation.@OutputOnly
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
  /**
   * Durable messages that persist on every operation poll. @OutputOnly
   *
   * @param string[] $warning
   */
  public function setWarning($warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return string[]
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationMetadataV1Beta::class, 'Google_Service_Appengine_OperationMetadataV1Beta');
