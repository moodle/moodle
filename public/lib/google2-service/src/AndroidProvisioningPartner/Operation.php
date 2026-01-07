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

namespace Google\Service\AndroidProvisioningPartner;

class Operation extends \Google\Model
{
  /**
   * If the value is `false`, it means the operation is still in progress. If
   * `true`, the operation is completed, and either `error` or `response` is
   * available.
   *
   * @var bool
   */
  public $done;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * This field will contain a `DevicesLongRunningOperationMetadata` object if
   * the operation is created by `claimAsync`, `unclaimAsync`, or
   * `updateMetadataAsync`.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * The server-assigned name, which is only unique within the same service that
   * originally returns it. If you use the default HTTP mapping, the `name`
   * should be a resource name ending with `operations/{unique_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * This field will contain a `DevicesLongRunningOperationResponse` object if
   * the operation is created by `claimAsync`, `unclaimAsync`, or
   * `updateMetadataAsync`.
   *
   * @var array[]
   */
  public $response;

  /**
   * If the value is `false`, it means the operation is still in progress. If
   * `true`, the operation is completed, and either `error` or `response` is
   * available.
   *
   * @param bool $done
   */
  public function setDone($done)
  {
    $this->done = $done;
  }
  /**
   * @return bool
   */
  public function getDone()
  {
    return $this->done;
  }
  /**
   * This field will always be not set if the operation is created by
   * `claimAsync`, `unclaimAsync`, or `updateMetadataAsync`. In this case, error
   * information for each device is set in
   * `response.perDeviceStatus.result.status`.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * This field will contain a `DevicesLongRunningOperationMetadata` object if
   * the operation is created by `claimAsync`, `unclaimAsync`, or
   * `updateMetadataAsync`.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The server-assigned name, which is only unique within the same service that
   * originally returns it. If you use the default HTTP mapping, the `name`
   * should be a resource name ending with `operations/{unique_id}`.
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
   * This field will contain a `DevicesLongRunningOperationResponse` object if
   * the operation is created by `claimAsync`, `unclaimAsync`, or
   * `updateMetadataAsync`.
   *
   * @param array[] $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
  }
  /**
   * @return array[]
   */
  public function getResponse()
  {
    return $this->response;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operation::class, 'Google_Service_AndroidProvisioningPartner_Operation');
