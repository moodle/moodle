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

class GoogleCloudApigeeV1NatAddress extends \Google\Model
{
  /**
   * The resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The NAT address is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The NAT address is reserved but not yet used for Internet egress.
   */
  public const STATE_RESERVED = 'RESERVED';
  /**
   * The NAT address is active and used for Internet egress.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The NAT address is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. The static IPV4 address.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Required. Resource ID of the NAT address.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the nat address.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The static IPV4 address.
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
   * Required. Resource ID of the NAT address.
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
   * Output only. State of the nat address.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, RESERVED, ACTIVE, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1NatAddress::class, 'Google_Service_Apigee_GoogleCloudApigeeV1NatAddress');
