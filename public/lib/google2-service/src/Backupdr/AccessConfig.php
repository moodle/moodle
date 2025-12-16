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

namespace Google\Service\Backupdr;

class AccessConfig extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const NETWORK_TIER_NETWORK_TIER_UNSPECIFIED = 'NETWORK_TIER_UNSPECIFIED';
  /**
   * High quality, Google-grade network tier, support for all networking
   * products.
   */
  public const NETWORK_TIER_PREMIUM = 'PREMIUM';
  /**
   * Public internet quality, only limited support for other networking
   * products.
   */
  public const NETWORK_TIER_STANDARD = 'STANDARD';
  /**
   * Default value. This value is unused.
   */
  public const TYPE_ACCESS_TYPE_UNSPECIFIED = 'ACCESS_TYPE_UNSPECIFIED';
  /**
   * ONE_TO_ONE_NAT
   */
  public const TYPE_ONE_TO_ONE_NAT = 'ONE_TO_ONE_NAT';
  /**
   * Direct IPv6 access.
   */
  public const TYPE_DIRECT_IPV6 = 'DIRECT_IPV6';
  /**
   * Optional. The external IPv6 address of this access configuration.
   *
   * @var string
   */
  public $externalIpv6;
  /**
   * Optional. The prefix length of the external IPv6 range.
   *
   * @var int
   */
  public $externalIpv6PrefixLength;
  /**
   * Optional. The name of this access configuration.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The external IP address of this access configuration.
   *
   * @var string
   */
  public $natIP;
  /**
   * Optional. This signifies the networking tier used for configuring this
   * access
   *
   * @var string
   */
  public $networkTier;
  /**
   * Optional. The DNS domain name for the public PTR record.
   *
   * @var string
   */
  public $publicPtrDomainName;
  /**
   * Optional. Specifies whether a public DNS 'PTR' record should be created to
   * map the external IP address of the instance to a DNS domain name.
   *
   * @var bool
   */
  public $setPublicPtr;
  /**
   * Optional. In accessConfigs (IPv4), the default and only option is
   * ONE_TO_ONE_NAT. In ipv6AccessConfigs, the default and only option is
   * DIRECT_IPV6.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The external IPv6 address of this access configuration.
   *
   * @param string $externalIpv6
   */
  public function setExternalIpv6($externalIpv6)
  {
    $this->externalIpv6 = $externalIpv6;
  }
  /**
   * @return string
   */
  public function getExternalIpv6()
  {
    return $this->externalIpv6;
  }
  /**
   * Optional. The prefix length of the external IPv6 range.
   *
   * @param int $externalIpv6PrefixLength
   */
  public function setExternalIpv6PrefixLength($externalIpv6PrefixLength)
  {
    $this->externalIpv6PrefixLength = $externalIpv6PrefixLength;
  }
  /**
   * @return int
   */
  public function getExternalIpv6PrefixLength()
  {
    return $this->externalIpv6PrefixLength;
  }
  /**
   * Optional. The name of this access configuration.
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
   * Optional. The external IP address of this access configuration.
   *
   * @param string $natIP
   */
  public function setNatIP($natIP)
  {
    $this->natIP = $natIP;
  }
  /**
   * @return string
   */
  public function getNatIP()
  {
    return $this->natIP;
  }
  /**
   * Optional. This signifies the networking tier used for configuring this
   * access
   *
   * Accepted values: NETWORK_TIER_UNSPECIFIED, PREMIUM, STANDARD
   *
   * @param self::NETWORK_TIER_* $networkTier
   */
  public function setNetworkTier($networkTier)
  {
    $this->networkTier = $networkTier;
  }
  /**
   * @return self::NETWORK_TIER_*
   */
  public function getNetworkTier()
  {
    return $this->networkTier;
  }
  /**
   * Optional. The DNS domain name for the public PTR record.
   *
   * @param string $publicPtrDomainName
   */
  public function setPublicPtrDomainName($publicPtrDomainName)
  {
    $this->publicPtrDomainName = $publicPtrDomainName;
  }
  /**
   * @return string
   */
  public function getPublicPtrDomainName()
  {
    return $this->publicPtrDomainName;
  }
  /**
   * Optional. Specifies whether a public DNS 'PTR' record should be created to
   * map the external IP address of the instance to a DNS domain name.
   *
   * @param bool $setPublicPtr
   */
  public function setSetPublicPtr($setPublicPtr)
  {
    $this->setPublicPtr = $setPublicPtr;
  }
  /**
   * @return bool
   */
  public function getSetPublicPtr()
  {
    return $this->setPublicPtr;
  }
  /**
   * Optional. In accessConfigs (IPv4), the default and only option is
   * ONE_TO_ONE_NAT. In ipv6AccessConfigs, the default and only option is
   * DIRECT_IPV6.
   *
   * Accepted values: ACCESS_TYPE_UNSPECIFIED, ONE_TO_ONE_NAT, DIRECT_IPV6
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessConfig::class, 'Google_Service_Backupdr_AccessConfig');
