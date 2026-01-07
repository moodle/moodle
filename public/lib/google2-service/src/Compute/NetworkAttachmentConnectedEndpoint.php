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

class NetworkAttachmentConnectedEndpoint extends \Google\Collection
{
  /**
   * The consumer allows traffic from the producer to reach its VPC.
   */
  public const STATUS_ACCEPTED = 'ACCEPTED';
  /**
   * The consumer network attachment no longer exists.
   */
  public const STATUS_CLOSED = 'CLOSED';
  /**
   * The consumer needs to take further action before traffic can be served.
   */
  public const STATUS_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * The consumer neither allows nor prohibits traffic from the producer to
   * reach its VPC.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The consumer prohibits traffic from the producer to reach its VPC.
   */
  public const STATUS_REJECTED = 'REJECTED';
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  protected $collection_key = 'secondaryIpCidrRanges';
  /**
   * The IPv4 address assigned to the producer instance network interface. This
   * value will be a range in case of Serverless.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The IPv6 address assigned to the producer instance network interface. This
   * is only assigned when the stack types of both the instance network
   * interface and the consumer subnet are IPv4_IPv6.
   *
   * @var string
   */
  public $ipv6Address;
  /**
   * The project id or number of the interface to which the IP was assigned.
   *
   * @var string
   */
  public $projectIdOrNum;
  /**
   * Alias IP ranges from the same subnetwork.
   *
   * @var string[]
   */
  public $secondaryIpCidrRanges;
  /**
   * The status of a connected endpoint to this network attachment.
   *
   * @var string
   */
  public $status;
  /**
   * The subnetwork used to assign the IP to the producer instance network
   * interface.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Output only. [Output Only] The CIDR range of the subnet from which the IPv4
   * internal IP was allocated from.
   *
   * @var string
   */
  public $subnetworkCidrRange;

  /**
   * The IPv4 address assigned to the producer instance network interface. This
   * value will be a range in case of Serverless.
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
   * The IPv6 address assigned to the producer instance network interface. This
   * is only assigned when the stack types of both the instance network
   * interface and the consumer subnet are IPv4_IPv6.
   *
   * @param string $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @return string
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
  /**
   * The project id or number of the interface to which the IP was assigned.
   *
   * @param string $projectIdOrNum
   */
  public function setProjectIdOrNum($projectIdOrNum)
  {
    $this->projectIdOrNum = $projectIdOrNum;
  }
  /**
   * @return string
   */
  public function getProjectIdOrNum()
  {
    return $this->projectIdOrNum;
  }
  /**
   * Alias IP ranges from the same subnetwork.
   *
   * @param string[] $secondaryIpCidrRanges
   */
  public function setSecondaryIpCidrRanges($secondaryIpCidrRanges)
  {
    $this->secondaryIpCidrRanges = $secondaryIpCidrRanges;
  }
  /**
   * @return string[]
   */
  public function getSecondaryIpCidrRanges()
  {
    return $this->secondaryIpCidrRanges;
  }
  /**
   * The status of a connected endpoint to this network attachment.
   *
   * Accepted values: ACCEPTED, CLOSED, NEEDS_ATTENTION, PENDING, REJECTED,
   * STATUS_UNSPECIFIED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The subnetwork used to assign the IP to the producer instance network
   * interface.
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
  /**
   * Output only. [Output Only] The CIDR range of the subnet from which the IPv4
   * internal IP was allocated from.
   *
   * @param string $subnetworkCidrRange
   */
  public function setSubnetworkCidrRange($subnetworkCidrRange)
  {
    $this->subnetworkCidrRange = $subnetworkCidrRange;
  }
  /**
   * @return string
   */
  public function getSubnetworkCidrRange()
  {
    return $this->subnetworkCidrRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkAttachmentConnectedEndpoint::class, 'Google_Service_Compute_NetworkAttachmentConnectedEndpoint');
