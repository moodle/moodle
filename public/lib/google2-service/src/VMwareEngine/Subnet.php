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

namespace Google\Service\VMwareEngine;

class Subnet extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The subnet is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The subnet is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The subnet is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The subnet is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Changes requested in the last operation are being propagated.
   */
  public const STATE_RECONCILING = 'RECONCILING';
  /**
   * Last operation on the subnet did not succeed. Subnet's payload is reverted
   * back to its most recent working state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The IP address of the gateway of this subnet. Must fall within the IP
   * prefix defined above.
   *
   * @var string
   */
  public $gatewayIp;
  /**
   * The IP address range of the subnet in CIDR format '10.0.0.0/24'.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Output only. Identifier. The resource name of this subnet. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/subnets/my-subnet`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the resource.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The type of the subnet. For example "management" or
   * "userDefined".
   *
   * @var string
   */
  public $type;
  /**
   * Output only. VLAN ID of the VLAN on which the subnet is configured
   *
   * @var int
   */
  public $vlanId;

  /**
   * The IP address of the gateway of this subnet. Must fall within the IP
   * prefix defined above.
   *
   * @param string $gatewayIp
   */
  public function setGatewayIp($gatewayIp)
  {
    $this->gatewayIp = $gatewayIp;
  }
  /**
   * @return string
   */
  public function getGatewayIp()
  {
    return $this->gatewayIp;
  }
  /**
   * The IP address range of the subnet in CIDR format '10.0.0.0/24'.
   *
   * @param string $ipCidrRange
   */
  public function setIpCidrRange($ipCidrRange)
  {
    $this->ipCidrRange = $ipCidrRange;
  }
  /**
   * @return string
   */
  public function getIpCidrRange()
  {
    return $this->ipCidrRange;
  }
  /**
   * Output only. Identifier. The resource name of this subnet. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/subnets/my-subnet`
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
   * Output only. The state of the resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, UPDATING, DELETING,
   * RECONCILING, FAILED
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
  /**
   * Output only. The type of the subnet. For example "management" or
   * "userDefined".
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. VLAN ID of the VLAN on which the subnet is configured
   *
   * @param int $vlanId
   */
  public function setVlanId($vlanId)
  {
    $this->vlanId = $vlanId;
  }
  /**
   * @return int
   */
  public function getVlanId()
  {
    return $this->vlanId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subnet::class, 'Google_Service_VMwareEngine_Subnet');
