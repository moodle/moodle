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

class SecondaryIpRangeSpec extends \Google\Model
{
  /**
   * Required. The prefix length of the secondary IP range. Use CIDR range
   * notation, such as `30` to provision a secondary IP range with an
   * `x.x.x.x/30` CIDR range. The IP address range is drawn from a pool of
   * available ranges in the service consumer's allocated range.
   *
   * @var int
   */
  public $ipPrefixLength;
  /**
   * Optional. Enable outside allocation using public IP addresses. Any public
   * IP range may be specified. If this field is provided, we will not use
   * customer reserved ranges for this secondary IP range.
   *
   * @var string
   */
  public $outsideAllocationPublicIpRange;
  /**
   * Required. A name for the secondary IP range. The name must be 1-63
   * characters long, and comply with RFC1035. The name must be unique within
   * the subnetwork.
   *
   * @var string
   */
  public $rangeName;
  /**
   * Optional. The starting address of a range. The address must be a valid IPv4
   * address in the x.x.x.x format. This value combined with the IP prefix range
   * is the CIDR range for the secondary IP range. The range must be within the
   * allocated range that is assigned to the private connection. If the CIDR
   * range isn't available, the call fails.
   *
   * @var string
   */
  public $requestedAddress;

  /**
   * Required. The prefix length of the secondary IP range. Use CIDR range
   * notation, such as `30` to provision a secondary IP range with an
   * `x.x.x.x/30` CIDR range. The IP address range is drawn from a pool of
   * available ranges in the service consumer's allocated range.
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
   * Optional. Enable outside allocation using public IP addresses. Any public
   * IP range may be specified. If this field is provided, we will not use
   * customer reserved ranges for this secondary IP range.
   *
   * @param string $outsideAllocationPublicIpRange
   */
  public function setOutsideAllocationPublicIpRange($outsideAllocationPublicIpRange)
  {
    $this->outsideAllocationPublicIpRange = $outsideAllocationPublicIpRange;
  }
  /**
   * @return string
   */
  public function getOutsideAllocationPublicIpRange()
  {
    return $this->outsideAllocationPublicIpRange;
  }
  /**
   * Required. A name for the secondary IP range. The name must be 1-63
   * characters long, and comply with RFC1035. The name must be unique within
   * the subnetwork.
   *
   * @param string $rangeName
   */
  public function setRangeName($rangeName)
  {
    $this->rangeName = $rangeName;
  }
  /**
   * @return string
   */
  public function getRangeName()
  {
    return $this->rangeName;
  }
  /**
   * Optional. The starting address of a range. The address must be a valid IPv4
   * address in the x.x.x.x format. This value combined with the IP prefix range
   * is the CIDR range for the secondary IP range. The range must be within the
   * allocated range that is assigned to the private connection. If the CIDR
   * range isn't available, the call fails.
   *
   * @param string $requestedAddress
   */
  public function setRequestedAddress($requestedAddress)
  {
    $this->requestedAddress = $requestedAddress;
  }
  /**
   * @return string
   */
  public function getRequestedAddress()
  {
    return $this->requestedAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecondaryIpRangeSpec::class, 'Google_Service_ServiceNetworking_SecondaryIpRangeSpec');
