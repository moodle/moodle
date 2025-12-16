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

namespace Google\Service\Storage;

class GoogleLongrunningOperation extends \Google\Model
{
  /**
   * If the value is "false", it means the operation is still in progress. If
   * "true", the operation is completed, and either "error" or "response" is
   * available.
   *
   * @var bool
   */
  public $done;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * The kind of item this is. For operations, this is always storage#operation.
   *
   * @var string
   */
  public $kind;
  /**
   * Service-specific metadata associated with the operation. It typically
   * contains progress information and common metadata such as create time. Some
   * services might not provide such metadata. Any method that returns a long-
   * running operation should document the metadata type, if any.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * The server-assigned name, which is only unique within the same service that
   * originally returns it. If you use the default HTTP mapping, the "name"
   * should be a resource name ending with "operations/{operationId}".
   *
   * @var string
   */
  public $name;
  /**
   * The normal response of the operation in case of success. If the original
   * method returns no data on success, such as "Delete", the response is
   * google.protobuf.Empty. If the original method is standard
   * Get/Create/Update, the response should be the resource. For other methods,
   * the response should have the type "XxxResponse", where "Xxx" is the
   * original method name. For example, if the original method name is
   * "TakeSnapshot()", the inferred response type is "TakeSnapshotResponse".
   *
   * @var array[]
   */
  public $response;
  /**
   * The link to this long running operation.
   *
   * @var string
   */
  public $selfLink;

  /**
   * If the value is "false", it means the operation is still in progress. If
   * "true", the operation is completed, and either "error" or "response" is
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
   * The error result of the operation in case of failure or cancellation.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The kind of item this is. For operations, this is always storage#operation.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Service-specific metadata associated with the operation. It typically
   * contains progress information and common metadata such as create time. Some
   * services might not provide such metadata. Any method that returns a long-
   * running operation should document the metadata type, if any.
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
   * originally returns it. If you use the default HTTP mapping, the "name"
   * should be a resource name ending with "operations/{operationId}".
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
   * The normal response of the operation in case of success. If the original
   * method returns no data on success, such as "Delete", the response is
   * google.protobuf.Empty. If the original method is standard
   * Get/Create/Update, the response should be the resource. For other methods,
   * the response should have the type "XxxResponse", where "Xxx" is the
   * original method name. For example, if the original method name is
   * "TakeSnapshot()", the inferred response type is "TakeSnapshotResponse".
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
  /**
   * The link to this long running operation.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleLongrunningOperation::class, 'Google_Service_Storage_GoogleLongrunningOperation');
