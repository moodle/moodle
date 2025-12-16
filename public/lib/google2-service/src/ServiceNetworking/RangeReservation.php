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

namespace Google\Service\ServiceNetworking;

class RangeReservation extends \Google\Collection
{
  protected $collection_key = 'subnetworkCandidates';
  /**
   * Required. The size of the desired subnet. Use usual CIDR range notation.
   * For example, '29' to find unused x.x.x.x/29 CIDR range. The goal is to
   * determine if one of the allocated ranges has enough free space for a subnet
   * of the requested size. GCE disallows subnets with prefix_length > 29
   *
   * @var int
   */
  public $ipPrefixLength;
  /**
   * Optional. The name of one or more allocated IP address ranges associated
   * with this private service access connection. If no range names are provided
   * all ranges associated with this connection will be considered. If a CIDR
   * range with the specified IP prefix length is not available within these
   * ranges the validation fails.
   *
   * @var string[]
   */
  public $requestedRanges;
  /**
   * Optional. The size of the desired secondary ranges for the subnet. Use
   * usual CIDR range notation. For example, '29' to find unused x.x.x.x/29 CIDR
   * range. The goal is to determine that the allocated ranges have enough free
   * space for all the requested secondary ranges. GCE disallows subnets with
   * prefix_length > 29
   *
   * @var int[]
   */
  public $secondaryRangeIpPrefixLengths;
  protected $subnetworkCandidatesType = Subnetwork::class;
  protected $subnetworkCandidatesDataType = 'array';

  /**
   * Required. The size of the desired subnet. Use usual CIDR range notation.
   * For example, '29' to find unused x.x.x.x/29 CIDR range. The goal is to
   * determine if one of the allocated ranges has enough free space for a subnet
   * of the requested size. GCE disallows subnets with prefix_length > 29
   *
   * @param int $ipPrefixLength
   */
  public function setIpPrefixLength($ipPrefixLength)
  {
    $this->ipPrefixLength = $ipPrefixLength;
  }
  /**
   * @return int
   */
  public function getIpPrefixLength()
  {
    return $this->ipPrefixLength;
  }
  /**
   * Optional. The name of one or more allocated IP address ranges associated
   * with this private service access connection. If no range names are provided
   * all ranges associated with this connection will be considered. If a CIDR
   * range with the specified IP prefix length is not available within these
   * ranges the validation fails.
   *
   * @param string[] $requestedRanges
   */
  public function setRequestedRanges($requestedRanges)
  {
    $this->requestedRanges = $requestedRanges;
  }
  /**
   * @return string[]
   */
  public function getRequestedRanges()
  {
    return $this->requestedRanges;
  }
  /**
   * Optional. The size of the desired secondary ranges for the subnet. Use
   * usual CIDR range notation. For example, '29' to find unused x.x.x.x/29 CIDR
   * range. The goal is to determine that the allocated ranges have enough free
   * space for all the requested secondary ranges. GCE disallows subnets with
   * prefix_length > 29
   *
   * @param int[] $secondaryRangeIpPrefixLengths
   */
  public function setSecondaryRangeIpPrefixLengths($secondaryRangeIpPrefixLengths)
  {
    $this->secondaryRangeIpPrefixLengths = $secondaryRangeIpPrefixLengths;
  }
  /**
   * @return int[]
   */
  public function getSecondaryRangeIpPrefixLengths()
  {
    return $this->secondaryRangeIpPrefixLengths;
  }
  /**
   * Optional. List of subnetwork candidates to validate. The required input
   * fields are `name`, `network`, and `region`. Subnetworks from this list
   * which exist will be returned in the response with the `ip_cidr_range`,
   * `secondary_ip_cider_ranges`, and `outside_allocation` fields set.
   *
   * @param Subnetwork[] $subnetworkCandidates
   */
  public function setSubnetworkCandidates($subnetworkCandidates)
  {
    $this->subnetworkCandidates = $subnetworkCandidates;
  }
  /**
   * @return Subnetwork[]
   */
  public function getSubnetworkCandidates()
  {
    return $this->subnetworkCandidates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RangeReservation::class, 'Google_Service_ServiceNetworking_RangeReservation');
