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

class FirewallPolicyRuleMatcher extends \Google\Collection
{
  public const DEST_NETWORK_TYPE_INTERNET = 'INTERNET';
  public const DEST_NETWORK_TYPE_INTRA_VPC = 'INTRA_VPC';
  public const DEST_NETWORK_TYPE_NON_INTERNET = 'NON_INTERNET';
  public const DEST_NETWORK_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  public const DEST_NETWORK_TYPE_VPC_NETWORKS = 'VPC_NETWORKS';
  public const SRC_NETWORK_TYPE_INTERNET = 'INTERNET';
  public const SRC_NETWORK_TYPE_INTRA_VPC = 'INTRA_VPC';
  public const SRC_NETWORK_TYPE_NON_INTERNET = 'NON_INTERNET';
  public const SRC_NETWORK_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  public const SRC_NETWORK_TYPE_VPC_NETWORKS = 'VPC_NETWORKS';
  protected $collection_key = 'srcThreatIntelligences';
  /**
   * Address groups which should be matched against the traffic destination.
   * Maximum number of destination address groups is 10.
   *
   * @var string[]
   */
  public $destAddressGroups;
  /**
   * Fully Qualified Domain Name (FQDN) which should be matched against traffic
   * destination. Maximum number of destination fqdn allowed is 100.
   *
   * @var string[]
   */
  public $destFqdns;
  /**
   * CIDR IP address range. Maximum number of destination CIDR IP ranges allowed
   * is 5000.
   *
   * @var string[]
   */
  public $destIpRanges;
  /**
   * Network type of the traffic destination. Allowed values are:              -
   * UNSPECIFIED      - INTERNET      - NON_INTERNET
   *
   * @var string
   */
  public $destNetworkType;
  /**
   * Region codes whose IP addresses will be used to match for destination of
   * traffic. Should be specified as 2 letter country code defined as per ISO
   * 3166 alpha-2 country codes. ex."US" Maximum number of dest region codes
   * allowed is 5000.
   *
   * @var string[]
   */
  public $destRegionCodes;
  /**
   * Names of Network Threat Intelligence lists. The IPs in these lists will be
   * matched against traffic destination.
   *
   * @var string[]
   */
  public $destThreatIntelligences;
  protected $layer4ConfigsType = FirewallPolicyRuleMatcherLayer4Config::class;
  protected $layer4ConfigsDataType = 'array';
  /**
   * Address groups which should be matched against the traffic source. Maximum
   * number of source address groups is 10.
   *
   * @var string[]
   */
  public $srcAddressGroups;
  /**
   * Fully Qualified Domain Name (FQDN) which should be matched against traffic
   * source. Maximum number of source fqdn allowed is 100.
   *
   * @var string[]
   */
  public $srcFqdns;
  /**
   * CIDR IP address range. Maximum number of source CIDR IP ranges allowed is
   * 5000.
   *
   * @var string[]
   */
  public $srcIpRanges;
  /**
   * Network type of the traffic source. Allowed values are:              -
   * UNSPECIFIED      - INTERNET      - INTRA_VPC      - NON_INTERNET      -
   * VPC_NETWORKS
   *
   * @var string
   */
  public $srcNetworkType;
  /**
   * Networks of the traffic source. It can be either a full or partial url.
   *
   * @var string[]
   */
  public $srcNetworks;
  /**
   * Region codes whose IP addresses will be used to match for source of
   * traffic. Should be specified as 2 letter country code defined as per ISO
   * 3166 alpha-2 country codes. ex."US" Maximum number of source region codes
   * allowed is 5000.
   *
   * @var string[]
   */
  public $srcRegionCodes;
  protected $srcSecureTagsType = FirewallPolicyRuleSecureTag::class;
  protected $srcSecureTagsDataType = 'array';
  /**
   * Names of Network Threat Intelligence lists. The IPs in these lists will be
   * matched against traffic source.
   *
   * @var string[]
   */
  public $srcThreatIntelligences;

  /**
   * Address groups which should be matched against the traffic destination.
   * Maximum number of destination address groups is 10.
   *
   * @param string[] $destAddressGroups
   */
  public function setDestAddressGroups($destAddressGroups)
  {
    $this->destAddressGroups = $destAddressGroups;
  }
  /**
   * @return string[]
   */
  public function getDestAddressGroups()
  {
    return $this->destAddressGroups;
  }
  /**
   * Fully Qualified Domain Name (FQDN) which should be matched against traffic
   * destination. Maximum number of destination fqdn allowed is 100.
   *
   * @param string[] $destFqdns
   */
  public function setDestFqdns($destFqdns)
  {
    $this->destFqdns = $destFqdns;
  }
  /**
   * @return string[]
   */
  public function getDestFqdns()
  {
    return $this->destFqdns;
  }
  /**
   * CIDR IP address range. Maximum number of destination CIDR IP ranges allowed
   * is 5000.
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
   * Network type of the traffic destination. Allowed values are:              -
   * UNSPECIFIED      - INTERNET      - NON_INTERNET
   *
   * Accepted values: INTERNET, INTRA_VPC, NON_INTERNET, UNSPECIFIED,
   * VPC_NETWORKS
   *
   * @param self::DEST_NETWORK_TYPE_* $destNetworkType
   */
  public function setDestNetworkType($destNetworkType)
  {
    $this->destNetworkType = $destNetworkType;
  }
  /**
   * @return self::DEST_NETWORK_TYPE_*
   */
  public function getDestNetworkType()
  {
    return $this->destNetworkType;
  }
  /**
   * Region codes whose IP addresses will be used to match for destination of
   * traffic. Should be specified as 2 letter country code defined as per ISO
   * 3166 alpha-2 country codes. ex."US" Maximum number of dest region codes
   * allowed is 5000.
   *
   * @param string[] $destRegionCodes
   */
  public function setDestRegionCodes($destRegionCodes)
  {
    $this->destRegionCodes = $destRegionCodes;
  }
  /**
   * @return string[]
   */
  public function getDestRegionCodes()
  {
    return $this->destRegionCodes;
  }
  /**
   * Names of Network Threat Intelligence lists. The IPs in these lists will be
   * matched against traffic destination.
   *
   * @param string[] $destThreatIntelligences
   */
  public function setDestThreatIntelligences($destThreatIntelligences)
  {
    $this->destThreatIntelligences = $destThreatIntelligences;
  }
  /**
   * @return string[]
   */
  public function getDestThreatIntelligences()
  {
    return $this->destThreatIntelligences;
  }
  /**
   * Pairs of IP protocols and ports that the rule should match.
   *
   * @param FirewallPolicyRuleMatcherLayer4Config[] $layer4Configs
   */
  public function setLayer4Configs($layer4Configs)
  {
    $this->layer4Configs = $layer4Configs;
  }
  /**
   * @return FirewallPolicyRuleMatcherLayer4Config[]
   */
  public function getLayer4Configs()
  {
    return $this->layer4Configs;
  }
  /**
   * Address groups which should be matched against the traffic source. Maximum
   * number of source address groups is 10.
   *
   * @param string[] $srcAddressGroups
   */
  public function setSrcAddressGroups($srcAddressGroups)
  {
    $this->srcAddressGroups = $srcAddressGroups;
  }
  /**
   * @return string[]
   */
  public function getSrcAddressGroups()
  {
    return $this->srcAddressGroups;
  }
  /**
   * Fully Qualified Domain Name (FQDN) which should be matched against traffic
   * source. Maximum number of source fqdn allowed is 100.
   *
   * @param string[] $srcFqdns
   */
  public function setSrcFqdns($srcFqdns)
  {
    $this->srcFqdns = $srcFqdns;
  }
  /**
   * @return string[]
   */
  public function getSrcFqdns()
  {
    return $this->srcFqdns;
  }
  /**
   * CIDR IP address range. Maximum number of source CIDR IP ranges allowed is
   * 5000.
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
   * Network type of the traffic source. Allowed values are:              -
   * UNSPECIFIED      - INTERNET      - INTRA_VPC      - NON_INTERNET      -
   * VPC_NETWORKS
   *
   * Accepted values: INTERNET, INTRA_VPC, NON_INTERNET, UNSPECIFIED,
   * VPC_NETWORKS
   *
   * @param self::SRC_NETWORK_TYPE_* $srcNetworkType
   */
  public function setSrcNetworkType($srcNetworkType)
  {
    $this->srcNetworkType = $srcNetworkType;
  }
  /**
   * @return self::SRC_NETWORK_TYPE_*
   */
  public function getSrcNetworkType()
  {
    return $this->srcNetworkType;
  }
  /**
   * Networks of the traffic source. It can be either a full or partial url.
   *
   * @param string[] $srcNetworks
   */
  public function setSrcNetworks($srcNetworks)
  {
    $this->srcNetworks = $srcNetworks;
  }
  /**
   * @return string[]
   */
  public function getSrcNetworks()
  {
    return $this->srcNetworks;
  }
  /**
   * Region codes whose IP addresses will be used to match for source of
   * traffic. Should be specified as 2 letter country code defined as per ISO
   * 3166 alpha-2 country codes. ex."US" Maximum number of source region codes
   * allowed is 5000.
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
   * List of secure tag values, which should be matched at the source of the
   * traffic. For INGRESS rule, if all the srcSecureTag are INEFFECTIVE, and
   * there is no srcIpRange, this rule will be ignored. Maximum number of source
   * tag values allowed is 256.
   *
   * @param FirewallPolicyRuleSecureTag[] $srcSecureTags
   */
  public function setSrcSecureTags($srcSecureTags)
  {
    $this->srcSecureTags = $srcSecureTags;
  }
  /**
   * @return FirewallPolicyRuleSecureTag[]
   */
  public function getSrcSecureTags()
  {
    return $this->srcSecureTags;
  }
  /**
   * Names of Network Threat Intelligence lists. The IPs in these lists will be
   * matched against traffic source.
   *
   * @param string[] $srcThreatIntelligences
   */
  public function setSrcThreatIntelligences($srcThreatIntelligences)
  {
    $this->srcThreatIntelligences = $srcThreatIntelligences;
  }
  /**
   * @return string[]
   */
  public function getSrcThreatIntelligences()
  {
    return $this->srcThreatIntelligences;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPolicyRuleMatcher::class, 'Google_Service_Compute_FirewallPolicyRuleMatcher');
