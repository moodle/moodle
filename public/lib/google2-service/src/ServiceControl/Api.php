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

namespace Google\Service\ServiceControl;

class Api extends \Google\Model
{
  /**
   * The API operation name. For gRPC requests, it is the fully qualified API
   * method name, such as "google.pubsub.v1.Publisher.Publish". For OpenAPI
   * requests, it is the `operationId`, such as "getPet".
   *
   * @var string
   */
  public $operation;
  /**
   * The API protocol used for sending the request, such as "http", "https",
   * "grpc", or "internal".
   *
   * @var string
   */
  public $protocol;
  /**
   * The API service name. It is a logical identifier for a networked API, such
   * as "pubsub.googleapis.com". The naming syntax depends on the API management
   * system being used for handling the request.
   *
   * @var string
   */
  public $service;
  /**
   * The API version associated with the API operation above, such as "v1" or
   * "v1alpha1".
   *
   * @var string
   */
  public $version;

  /**
   * The API operation name. For gRPC requests, it is the fully qualified API
   * method name, such as "google.pubsub.v1.Publisher.Publish". For OpenAPI
   * requests, it is the `operationId`, such as "getPet".
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
   * The API protocol used for sending the request, such as "http", "https",
   * "grpc", or "internal".
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * The API service name. It is a logical identifier for a networked API, such
   * as "pubsub.googleapis.com". The naming syntax depends on the API management
   * system being used for handling the request.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * The API version associated with the API operation above, such as "v1" or
   * "v1alpha1".
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Api::class, 'Google_Service_ServiceControl_Api');
