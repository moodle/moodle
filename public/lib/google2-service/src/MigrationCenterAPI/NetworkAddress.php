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

namespace Google\Service\MigrationCenterAPI;

class NetworkAddress extends \Google\Model
{
  /**
   * Unknown (default value).
   */
  public const ASSIGNMENT_ADDRESS_ASSIGNMENT_UNSPECIFIED = 'ADDRESS_ASSIGNMENT_UNSPECIFIED';
  /**
   * Statically assigned IP.
   */
  public const ASSIGNMENT_ADDRESS_ASSIGNMENT_STATIC = 'ADDRESS_ASSIGNMENT_STATIC';
  /**
   * Dynamically assigned IP (DHCP).
   */
  public const ASSIGNMENT_ADDRESS_ASSIGNMENT_DHCP = 'ADDRESS_ASSIGNMENT_DHCP';
  /**
   * Whether DHCP is used to assign addresses.
   *
   * @var string
   */
  public $assignment;
  /**
   * Broadcast address.
   *
   * @var string
   */
  public $bcast;
  /**
   * Fully qualified domain name.
   *
   * @var string
   */
  public $fqdn;
  /**
   * Assigned or configured IP Address.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Subnet mask.
   *
   * @var string
   */
  public $subnetMask;

  /**
   * Whether DHCP is used to assign addresses.
   *
   * Accepted values: ADDRESS_ASSIGNMENT_UNSPECIFIED, ADDRESS_ASSIGNMENT_STATIC,
   * ADDRESS_ASSIGNMENT_DHCP
   *
   * @param self::ASSIGNMENT_* $assignment
   */
  public function setAssignment($assignment)
  {
    $this->assignment = $assignment;
  }
  /**
   * @return self::ASSIGNMENT_*
   */
  public function getAssignment()
  {
    return $this->assignment;
  }
  /**
   * Broadcast address.
   *
   * @param string $bcast
   */
  public function setBcast($bcast)
  {
    $this->bcast = $bcast;
  }
  /**
   * @return string
   */
  public function getBcast()
  {
    return $this->bcast;
  }
  /**
   * Fully qualified domain name.
   *
   * @param string $fqdn
   */
  public function setFqdn($fqdn)
  {
    $this->fqdn = $fqdn;
  }
  /**
   * @return string
   */
  public function getFqdn()
  {
    return $this->fqdn;
  }
  /**
   * Assigned or configured IP Address.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Subnet mask.
   *
   * @param string $subnetMask
   */
  public function setSubnetMask($subnetMask)
  {
    $this->subnetMask = $subnetMask;
  }
  /**
   * @return string
   */
  public function getSubnetMask()
  {
    return $this->subnetMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkAddress::class, 'Google_Service_MigrationCenterAPI_NetworkAddress');
