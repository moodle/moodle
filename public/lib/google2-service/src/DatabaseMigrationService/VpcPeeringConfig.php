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

namespace Google\Service\DatabaseMigrationService;

class VpcPeeringConfig extends \Google\Model
{
  /**
   * Required. A free subnet for peering. (CIDR of /29)
   *
   * @var string
   */
  public $subnet;
  /**
   * Required. Fully qualified name of the VPC that Database Migration Service
   * will peer to.
   *
   * @var string
   */
  public $vpcName;

  /**
   * Required. A free subnet for peering. (CIDR of /29)
   *
   * @param string $subnet
   */
  public function setSubnet($subnet)
  {
    $this->subnet = $subnet;
  }
  /**
   * @return string
   */
  public function getSubnet()
  {
    return $this->subnet;
  }
  /**
   * Required. Fully qualified name of the VPC that Database Migration Service
   * will peer to.
   *
   * @param string $vpcName
   */
  public function setVpcName($vpcName)
  {
    $this->vpcName = $vpcName;
  }
  /**
   * @return string
   */
  public function getVpcName()
  {
    return $this->vpcName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpcPeeringConfig::class, 'Google_Service_DatabaseMigrationService_VpcPeeringConfig');
