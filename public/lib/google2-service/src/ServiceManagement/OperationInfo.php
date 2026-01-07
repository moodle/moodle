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

namespace Google\Service\ServiceManagement;

class OperationInfo extends \Google\Model
{
  /**
   * Required. The message name of the metadata type for this long-running
   * operation. If the response is in a different package from the rpc, a fully-
   * qualified message name must be used (e.g. `google.protobuf.Struct`). Note:
   * Altering this value constitutes a breaking change.
   *
   * @var string
   */
  public $metadataType;
  /**
   * Required. The message name of the primary return type for this long-running
   * operation. This type will be used to deserialize the LRO's response. If the
   * response is in a different package from the rpc, a fully-qualified message
   * name must be used (e.g. `google.protobuf.Struct`). Note: Altering this
   * value constitutes a breaking change.
   *
   * @var string
   */
  public $responseType;

  /**
   * Required. The message name of the metadata type for this long-running
   * operation. If the response is in a different package from the rpc, a fully-
   * qualified message name must be used (e.g. `google.protobuf.Struct`). Note:
   * Altering this value constitutes a breaking change.
   *
   * @param string $metadataType
   */
  public function setMetadataType($metadataType)
  {
    $this->metadataType = $metadataType;
  }
  /**
   * @return string
   */
  public function getMetadataType()
  {
    return $this->metadataType;
  }
  /**
   * Required. The message name of the primary return type for this long-running
   * operation. This type will be used to deserialize the LRO's response. If the
   * response is in a different package from the rpc, a fully-qualified message
   * name must be used (e.g. `google.protobuf.Struct`). Note: Altering this
   * value constitutes a breaking change.
   *
   * @param string $responseType
   */
  public function setResponseType($responseType)
  {
    $this->responseType = $responseType;
  }
  /**
   * @return string
   */
  public function getResponseType()
  {
    return $this->responseType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationInfo::class, 'Google_Service_ServiceManagement_OperationInfo');
