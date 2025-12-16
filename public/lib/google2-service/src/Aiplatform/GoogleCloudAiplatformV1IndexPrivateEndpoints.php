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

class GoogleCloudAiplatformV1IndexPrivateEndpoints extends \Google\Collection
{
  protected $collection_key = 'pscAutomatedEndpoints';
  /**
   * Output only. The ip address used to send match gRPC requests.
   *
   * @var string
   */
  public $matchGrpcAddress;
  protected $pscAutomatedEndpointsType = GoogleCloudAiplatformV1PscAutomatedEndpoints::class;
  protected $pscAutomatedEndpointsDataType = 'array';
  /**
   * Output only. The name of the service attachment resource. Populated if
   * private service connect is enabled.
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Output only. The ip address used to send match gRPC requests.
   *
   * @param string $matchGrpcAddress
   */
  public function setMatchGrpcAddress($matchGrpcAddress)
  {
    $this->matchGrpcAddress = $matchGrpcAddress;
  }
  /**
   * @return string
   */
  public function getMatchGrpcAddress()
  {
    return $this->matchGrpcAddress;
  }
  /**
   * Output only. PscAutomatedEndpoints is populated if private service connect
   * is enabled if PscAutomatedConfig is set.
   *
   * @param GoogleCloudAiplatformV1PscAutomatedEndpoints[] $pscAutomatedEndpoints
   */
  public function setPscAutomatedEndpoints($pscAutomatedEndpoints)
  {
    $this->pscAutomatedEndpoints = $pscAutomatedEndpoints;
  }
  /**
   * @return GoogleCloudAiplatformV1PscAutomatedEndpoints[]
   */
  public function getPscAutomatedEndpoints()
  {
    return $this->pscAutomatedEndpoints;
  }
  /**
   * Output only. The name of the service attachment resource. Populated if
   * private service connect is enabled.
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1IndexPrivateEndpoints::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IndexPrivateEndpoints');
