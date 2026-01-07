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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2IpRule extends \Google\Collection
{
  protected $collection_key = 'portRanges';
  protected $portRangesType = GoogleCloudSecuritycenterV2PortRange::class;
  protected $portRangesDataType = 'array';
  /**
   * The IP protocol this rule applies to. This value can either be one of the
   * following well known protocol strings (TCP, UDP, ICMP, ESP, AH, IPIP, SCTP)
   * or a string representation of the integer value.
   *
   * @var string
   */
  public $protocol;

  /**
   * Optional. An optional list of ports to which this rule applies. This field
   * is only applicable for the UDP or (S)TCP protocols. Each entry must be
   * either an integer or a range including a min and max port number.
   *
   * @param GoogleCloudSecuritycenterV2PortRange[] $portRanges
   */
  public function setPortRanges($portRanges)
  {
    $this->portRanges = $portRanges;
  }
  /**
   * @return GoogleCloudSecuritycenterV2PortRange[]
   */
  public function getPortRanges()
  {
    return $this->portRanges;
  }
  /**
   * The IP protocol this rule applies to. This value can either be one of the
   * following well known protocol strings (TCP, UDP, ICMP, ESP, AH, IPIP, SCTP)
   * or a string representation of the integer value.
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IpRule::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IpRule');
