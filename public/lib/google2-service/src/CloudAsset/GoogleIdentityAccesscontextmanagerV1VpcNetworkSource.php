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

namespace Google\Service\CloudAsset;

class GoogleIdentityAccesscontextmanagerV1VpcNetworkSource extends \Google\Model
{
  protected $vpcSubnetworkType = GoogleIdentityAccesscontextmanagerV1VpcSubNetwork::class;
  protected $vpcSubnetworkDataType = '';

  /**
   * Sub-segment ranges of a VPC network.
   *
   * @param GoogleIdentityAccesscontextmanagerV1VpcSubNetwork $vpcSubnetwork
   */
  public function setVpcSubnetwork(GoogleIdentityAccesscontextmanagerV1VpcSubNetwork $vpcSubnetwork)
  {
    $this->vpcSubnetwork = $vpcSubnetwork;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1VpcSubNetwork
   */
  public function getVpcSubnetwork()
  {
    return $this->vpcSubnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1VpcNetworkSource::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1VpcNetworkSource');
