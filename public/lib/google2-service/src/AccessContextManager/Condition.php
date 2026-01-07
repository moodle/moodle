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

namespace Google\Service\AccessContextManager;

class Condition extends \Google\Collection
{
  protected $collection_key = 'vpcNetworkSources';
  protected $devicePolicyType = DevicePolicy::class;
  protected $devicePolicyDataType = '';
  /**
   * CIDR block IP subnetwork specification. May be IPv4 or IPv6. Note that for
   * a CIDR IP address block, the specified IP address portion must be properly
   * truncated (i.e. all the host bits must be zero) or the input is considered
   * malformed. For example, "192.0.2.0/24" is accepted but "192.0.2.1/24" is
   * not. Similarly, for IPv6, "2001:db8::/32" is accepted whereas
   * "2001:db8::1/32" is not. The originating IP of a request must be in one of
   * the listed subnets in order for this Condition to be true. If empty, all IP
   * addresses are allowed.
   *
   * @var string[]
   */
  public $ipSubnetworks;
  /**
   * The request must be made by one of the provided user or service accounts.
   * Groups are not supported. Syntax: `user:{emailid}`
   * `serviceAccount:{emailid}` If not specified, a request may come from any
   * user.
   *
   * @var string[]
   */
  public $members;
  /**
   * Whether to negate the Condition. If true, the Condition becomes a NAND over
   * its non-empty fields. Any non-empty field criteria evaluating to false will
   * result in the Condition to be satisfied. Defaults to false.
   *
   * @var bool
   */
  public $negate;
  /**
   * The request must originate from one of the provided countries/regions. Must
   * be valid ISO 3166-1 alpha-2 codes.
   *
   * @var string[]
   */
  public $regions;
  /**
   * A list of other access levels defined in the same `Policy`, referenced by
   * resource name. Referencing an `AccessLevel` which does not exist is an
   * error. All access levels listed must be granted for the Condition to be
   * true. Example: "`accessPolicies/MY_POLICY/accessLevels/LEVEL_NAME"`
   *
   * @var string[]
   */
  public $requiredAccessLevels;
  protected $vpcNetworkSourcesType = VpcNetworkSource::class;
  protected $vpcNetworkSourcesDataType = 'array';

  /**
   * Device specific restrictions, all restrictions must hold for the Condition
   * to be true. If not specified, all devices are allowed.
   *
   * @param DevicePolicy $devicePolicy
   */
  public function setDevicePolicy(DevicePolicy $devicePolicy)
  {
    $this->devicePolicy = $devicePolicy;
  }
  /**
   * @return DevicePolicy
   */
  public function getDevicePolicy()
  {
    return $this->devicePolicy;
  }
  /**
   * CIDR block IP subnetwork specification. May be IPv4 or IPv6. Note that for
   * a CIDR IP address block, the specified IP address portion must be properly
   * truncated (i.e. all the host bits must be zero) or the input is considered
   * malformed. For example, "192.0.2.0/24" is accepted but "192.0.2.1/24" is
   * not. Similarly, for IPv6, "2001:db8::/32" is accepted whereas
   * "2001:db8::1/32" is not. The originating IP of a request must be in one of
   * the listed subnets in order for this Condition to be true. If empty, all IP
   * addresses are allowed.
   *
   * @param string[] $ipSubnetworks
   */
  public function setIpSubnetworks($ipSubnetworks)
  {
    $this->ipSubnetworks = $ipSubnetworks;
  }
  /**
   * @return string[]
   */
  public function getIpSubnetworks()
  {
    return $this->ipSubnetworks;
  }
  /**
   * The request must be made by one of the provided user or service accounts.
   * Groups are not supported. Syntax: `user:{emailid}`
   * `serviceAccount:{emailid}` If not specified, a request may come from any
   * user.
   *
   * @param string[] $members
   */
  public function setMembers($members)
  {
    $this->members = $members;
  }
  /**
   * @return string[]
   */
  public function getMembers()
  {
    return $this->members;
  }
  /**
   * Whether to negate the Condition. If true, the Condition becomes a NAND over
   * its non-empty fields. Any non-empty field criteria evaluating to false will
   * result in the Condition to be satisfied. Defaults to false.
   *
   * @param bool $negate
   */
  public function setNegate($negate)
  {
    $this->negate = $negate;
  }
  /**
   * @return bool
   */
  public function getNegate()
  {
    return $this->negate;
  }
  /**
   * The request must originate from one of the provided countries/regions. Must
   * be valid ISO 3166-1 alpha-2 codes.
   *
   * @param string[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return string[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
  /**
   * A list of other access levels defined in the same `Policy`, referenced by
   * resource name. Referencing an `AccessLevel` which does not exist is an
   * error. All access levels listed must be granted for the Condition to be
   * true. Example: "`accessPolicies/MY_POLICY/accessLevels/LEVEL_NAME"`
   *
   * @param string[] $requiredAccessLevels
   */
  public function setRequiredAccessLevels($requiredAccessLevels)
  {
    $this->requiredAccessLevels = $requiredAccessLevels;
  }
  /**
   * @return string[]
   */
  public function getRequiredAccessLevels()
  {
    return $this->requiredAccessLevels;
  }
  /**
   * The request must originate from one of the provided VPC networks in Google
   * Cloud. Cannot specify this field together with `ip_subnetworks`.
   *
   * @param VpcNetworkSource[] $vpcNetworkSources
   */
  public function setVpcNetworkSources($vpcNetworkSources)
  {
    $this->vpcNetworkSources = $vpcNetworkSources;
  }
  /**
   * @return VpcNetworkSource[]
   */
  public function getVpcNetworkSources()
  {
    return $this->vpcNetworkSources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Condition::class, 'Google_Service_AccessContextManager_Condition');
