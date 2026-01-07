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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DnsZonePeeringConfig extends \Google\Model
{
  /**
   * Required. The VPC network where the records for that private DNS zone's
   * namespace are available. Apigee will be performing DNS peering with this
   * VPC network.
   *
   * @var string
   */
  public $targetNetworkId;
  /**
   * Required. The ID of the project that contains the producer VPC network.
   *
   * @var string
   */
  public $targetProjectId;

  /**
   * Required. The VPC network where the records for that private DNS zone's
   * namespace are available. Apigee will be performing DNS peering with this
   * VPC network.
   *
   * @param string $targetNetworkId
   */
  public function setTargetNetworkId($targetNetworkId)
  {
    $this->targetNetworkId = $targetNetworkId;
  }
  /**
   * @return string
   */
  public function getTargetNetworkId()
  {
    return $this->targetNetworkId;
  }
  /**
   * Required. The ID of the project that contains the producer VPC network.
   *
   * @param string $targetProjectId
   */
  public function setTargetProjectId($targetProjectId)
  {
    $this->targetProjectId = $targetProjectId;
  }
  /**
   * @return string
   */
  public function getTargetProjectId()
  {
    return $this->targetProjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DnsZonePeeringConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DnsZonePeeringConfig');
