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

class SecurityPolicyRuleNetworkMatcher extends \Google\Collection
{
  protected $collection_key = 'userDefinedFields';
  /**
   * Destination IPv4/IPv6 addresses or CIDR prefixes, in standard text format.
   *
   * @var string[]
   */
  public $destIpRanges;
  /**
   * Destination port numbers for TCP/UDP/SCTP. Each element can be a 16-bit
   * unsigned decimal number (e.g. "80") or range (e.g. "0-1023").
   *
   * @var string[]
   */
  public $destPorts;
  /**
   * IPv4 protocol / IPv6 next header (after extension headers). Each element
   * can be an 8-bit unsigned decimal number (e.g. "6"), range (e.g. "253-254"),
   * or one of the following protocol names: "tcp", "udp", "icmp", "esp", "ah",
   * "ipip", or "sctp".
   *
   * @var string[]
   */
  public $ipProtocols;
  /**
   * BGP Autonomous System Number associated with the source IP address.
   *
   * @var string[]
   */
  public $srcAsns;
  /**
   * Source IPv4/IPv6 addresses or CIDR prefixes, in standard text format.
   *
   * @var string[]
   */
  public $srcIpRanges;
  /**
   * Source port numbers for TCP/UDP/SCTP. Each element can be a 16-bit unsigned
   * decimal number (e.g. "80") or range (e.g. "0-1023").
   *
   * @var string[]
   */
  public $srcPorts;
  /**
   * Two-letter ISO 3166-1 alpha-2 country code associated with the source IP
   * address.
   *
   * @var string[]
   */
  public $srcRegionCodes;
  protected $userDefinedFieldsType = SecurityPolicyRuleNetworkMatcherUserDefinedFieldMatch::class;
  protected $userDefinedFieldsDataType = 'array';

  /**
   * Destination IPv4/IPv6 addresses or CIDR prefixes, in standard text format.
   *
   * @param string[] $destIpRanges
   */
  public function setDestIpRanges($destIpRanges)
  {
    $this->destIpRanges = $destIpRanges;
  }
  /**
   * @return string[]
   */
  public function getDestIpRanges()
  {
    return $this->destIpRanges;
  }
  /**
   * Destination port numbers for TCP/UDP/SCTP. Each element can be a 16-bit
   * unsigned decimal number (e.g. "80") or range (e.g. "0-1023").
   *
   * @param string[] $destPorts
   */
  public function setDestPorts($destPorts)
  {
    $this->destPorts = $destPorts;
  }
  /**
   * @return string[]
   */
  public function getDestPorts()
  {
    return $this->destPorts;
  }
  /**
   * IPv4 protocol / IPv6 next header (after extension headers). Each element
   * can be an 8-bit unsigned decimal number (e.g. "6"), range (e.g. "253-254"),
   * or one of the following protocol names: "tcp", "udp", "icmp", "esp", "ah",
   * "ipip", or "sctp".
   *
   * @param string[] $ipProtocols
   */
  public function setIpProtocols($ipProtocols)
  {
    $this->ipProtocols = $ipProtocols;
  }
  /**
   * @return string[]
   */
  public function getIpProtocols()
  {
    return $this->ipProtocols;
  }
  /**
   * BGP Autonomous System Number associated with the source IP address.
   *
   * @param string[] $srcAsns
   */
  public function setSrcAsns($srcAsns)
  {
    $this->srcAsns = $srcAsns;
  }
  /**
   * @return string[]
   */
  public function getSrcAsns()
  {
    return $this->srcAsns;
  }
  /**
   * Source IPv4/IPv6 addresses or CIDR prefixes, in standard text format.
   *
   * @param string[] $srcIpRanges
   */
  public function setSrcIpRanges($srcIpRanges)
  {
    $this->srcIpRanges = $srcIpRanges;
  }
  /**
   * @return string[]
   */
  public function getSrcIpRanges()
  {
    return $this->srcIpRanges;
  }
  /**
   * Source port numbers for TCP/UDP/SCTP. Each element can be a 16-bit unsigned
   * decimal number (e.g. "80") or range (e.g. "0-1023").
   *
   * @param string[] $srcPorts
   */
  public function setSrcPorts($srcPorts)
  {
    $this->srcPorts = $srcPorts;
  }
  /**
   * @return string[]
   */
  public function getSrcPorts()
  {
    return $this->srcPorts;
  }
  /**
   * Two-letter ISO 3166-1 alpha-2 country code associated with the source IP
   * address.
   *
   * @param string[] $srcRegionCodes
   */
  public function setSrcRegionCodes($srcRegionCodes)
  {
    $this->srcRegionCodes = $srcRegionCodes;
  }
  /**
   * @return string[]
   */
  public function getSrcRegionCodes()
  {
    return $this->srcRegionCodes;
  }
  /**
   * User-defined fields. Each element names a defined field and lists the
   * matching values for that field.
   *
   * @param SecurityPolicyRuleNetworkMatcherUserDefinedFieldMatch[] $userDefinedFields
   */
  public function setUserDefinedFields($userDefinedFields)
  {
    $this->userDefinedFields = $userDefinedFields;
  }
  /**
   * @return SecurityPolicyRuleNetworkMatcherUserDefinedFieldMatch[]
   */
  public function getUserDefinedFields()
  {
    return $this->userDefinedFields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleNetworkMatcher::class, 'Google_Service_Compute_SecurityPolicyRuleNetworkMatcher');
