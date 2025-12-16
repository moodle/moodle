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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ProbeGrpcAction extends \Google\Model
{
  /**
   * Port number of the gRPC service. Number must be in the range 1 to 65535.
   *
   * @var int
   */
  public $port;
  /**
   * Service is the name of the service to place in the gRPC HealthCheckRequest.
   * See https://github.com/grpc/grpc/blob/master/doc/health-checking.md. If
   * this is not specified, the default behavior is defined by gRPC.
   *
   * @var string
   */
  public $service;

  /**
   * Port number of the gRPC service. Number must be in the range 1 to 65535.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Service is the name of the service to place in the gRPC HealthCheckRequest.
   * See https://github.com/grpc/grpc/blob/master/doc/health-checking.md. If
   * this is not specified, the default behavior is defined by gRPC.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ProbeGrpcAction::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ProbeGrpcAction');
