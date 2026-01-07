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

namespace Google\Service\Compute;

class InterconnectRemoteLocationConstraints extends \Google\Model
{
  /**
   * If PORT_PAIR_MATCHING_REMOTE_LOCATION, the remote cloud provider allocates
   * ports in pairs, and the user should choose the same remote location for
   * both ports.
   */
  public const PORT_PAIR_REMOTE_LOCATION_PORT_PAIR_MATCHING_REMOTE_LOCATION = 'PORT_PAIR_MATCHING_REMOTE_LOCATION';
  /**
   * If PORT_PAIR_UNCONSTRAINED_REMOTE_LOCATION, a user may opt to provision a
   * redundant pair of Cross-Cloud Interconnects using two different remote
   * locations in the same city.
   */
  public const PORT_PAIR_REMOTE_LOCATION_PORT_PAIR_UNCONSTRAINED_REMOTE_LOCATION = 'PORT_PAIR_UNCONSTRAINED_REMOTE_LOCATION';
  /**
   * If PORT_PAIR_MATCHING_VLAN, the Interconnect for this attachment is part of
   * a pair of ports that should have matching VLAN allocations. This occurs
   * with Cross-Cloud Interconnect to Azure remote locations. While GCP's API
   * does not explicitly group pairs of ports, the UI uses this field to ensure
   * matching VLAN ids when configuring a redundant VLAN pair.
   */
  public const PORT_PAIR_VLAN_PORT_PAIR_MATCHING_VLAN = 'PORT_PAIR_MATCHING_VLAN';
  /**
   * PORT_PAIR_UNCONSTRAINED_VLAN means there is no constraint.
   */
  public const PORT_PAIR_VLAN_PORT_PAIR_UNCONSTRAINED_VLAN = 'PORT_PAIR_UNCONSTRAINED_VLAN';
  /**
   * Output only. [Output Only] Port pair remote location constraints, which can
   * take one of the following values: PORT_PAIR_UNCONSTRAINED_REMOTE_LOCATION,
   * PORT_PAIR_MATCHING_REMOTE_LOCATION.
   *
   * Google Cloud API refers only to individual ports, but the UI uses this
   * field when ordering a pair of ports, to prevent users from accidentally
   * ordering something that is incompatible with their cloud provider.
   * Specifically, when ordering a redundant pair of Cross-Cloud Interconnect
   * ports, and one of them uses a remote location with
   * portPairMatchingRemoteLocation set to matching, the UI requires that both
   * ports use the same remote location.
   *
   * @var string
   */
  public $portPairRemoteLocation;
  /**
   * Output only. [Output Only] Port pair VLAN constraints, which can take one
   * of the following values: PORT_PAIR_UNCONSTRAINED_VLAN,
   * PORT_PAIR_MATCHING_VLAN
   *
   * @var string
   */
  public $portPairVlan;
  protected $subnetLengthRangeType = InterconnectRemoteLocationConstraintsSubnetLengthRange::class;
  protected $subnetLengthRangeDataType = '';

  /**
   * Output only. [Output Only] Port pair remote location constraints, which can
   * take one of the following values: PORT_PAIR_UNCONSTRAINED_REMOTE_LOCATION,
   * PORT_PAIR_MATCHING_REMOTE_LOCATION.
   *
   * Google Cloud API refers only to individual ports, but the UI uses this
   * field when ordering a pair of ports, to prevent users from accidentally
   * ordering something that is incompatible with their cloud provider.
   * Specifically, when ordering a redundant pair of Cross-Cloud Interconnect
   * ports, and one of them uses a remote location with
   * portPairMatchingRemoteLocation set to matching, the UI requires that both
   * ports use the same remote location.
   *
   * Accepted values: PORT_PAIR_MATCHING_REMOTE_LOCATION,
   * PORT_PAIR_UNCONSTRAINED_REMOTE_LOCATION
   *
   * @param self::PORT_PAIR_REMOTE_LOCATION_* $portPairRemoteLocation
   */
  public function setPortPairRemoteLocation($portPairRemoteLocation)
  {
    $this->portPairRemoteLocation = $portPairRemoteLocation;
  }
  /**
   * @return self::PORT_PAIR_REMOTE_LOCATION_*
   */
  public function getPortPairRemoteLocation()
  {
    return $this->portPairRemoteLocation;
  }
  /**
   * Output only. [Output Only] Port pair VLAN constraints, which can take one
   * of the following values: PORT_PAIR_UNCONSTRAINED_VLAN,
   * PORT_PAIR_MATCHING_VLAN
   *
   * Accepted values: PORT_PAIR_MATCHING_VLAN, PORT_PAIR_UNCONSTRAINED_VLAN
   *
   * @param self::PORT_PAIR_VLAN_* $portPairVlan
   */
  public function setPortPairVlan($portPairVlan)
  {
    $this->portPairVlan = $portPairVlan;
  }
  /**
   * @return self::PORT_PAIR_VLAN_*
   */
  public function getPortPairVlan()
  {
    return $this->portPairVlan;
  }
  /**
   * Output only. [Output Only]
   *
   * [min-length, max-length]
   *
   * The minimum and maximum value (inclusive) for the IPv4 subnet length.
   *
   *  For example, an  interconnectRemoteLocation for Azure has {min: 30, max:
   * 30} because Azure requires /30 subnets.
   *
   * This range specifies the values supported by both cloud providers.
   * Interconnect currently supports /29 and /30 IPv4 subnet lengths. If a
   * remote cloud has no constraint on IPv4 subnet length, the range would thus
   * be {min: 29, max: 30}.
   *
   * @param InterconnectRemoteLocationConstraintsSubnetLengthRange $subnetLengthRange
   */
  public function setSubnetLengthRange(InterconnectRemoteLocationConstraintsSubnetLengthRange $subnetLengthRange)
  {
    $this->subnetLengthRange = $subnetLengthRange;
  }
  /**
   * @return InterconnectRemoteLocationConstraintsSubnetLengthRange
   */
  public function getSubnetLengthRange()
  {
    return $this->subnetLengthRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectRemoteLocationConstraints::class, 'Google_Service_Compute_InterconnectRemoteLocationConstraints');
