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

namespace Google\Service\APIManagement;

class GclbObservationSourcePscNetworkConfig extends \Google\Model
{
  /**
   * Required. The VPC network. Format:
   * `projects/{project_id}/global/networks/{network}`
   *
   * @var string
   */
  public $network;
  /**
   * Required. The subnetwork in the source region that will be used to connect
   * to the Cloud Load Balancers via PSC NEGs. Must belong to `network`. Format:
   * projects/{project_id}/regions/{region}/subnetworks/{subnet}
   *
   * @var string
   */
  public $subnetwork;

  /**
   * Required. The VPC network. Format:
   * `projects/{project_id}/global/networks/{network}`
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Required. The subnetwork in the source region that will be used to connect
   * to the Cloud Load Balancers via PSC NEGs. Must belong to `network`. Format:
   * projects/{project_id}/regions/{region}/subnetworks/{subnet}
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GclbObservationSourcePscNetworkConfig::class, 'Google_Service_APIManagement_GclbObservationSourcePscNetworkConfig');
