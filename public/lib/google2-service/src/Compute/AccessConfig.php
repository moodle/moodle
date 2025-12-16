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

class AccessConfig extends \Google\Model
{
  /**
   * Public internet quality with fixed bandwidth.
   */
  public const NETWORK_TIER_FIXED_STANDARD = 'FIXED_STANDARD';
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
   * (Output only) Temporary tier for FIXED_STANDARD when fixed standard tier is
   * expired or not configured.
   */
  public const NETWORK_TIER_STANDARD_OVERRIDES_FIXED_STANDARD = 'STANDARD_OVERRIDES_FIXED_STANDARD';
  public const TYPE_DIRECT_IPV6 = 'DIRECT_IPV6';
  public const TYPE_ONE_TO_ONE_NAT = 'ONE_TO_ONE_NAT';
  /**
   * Applies to ipv6AccessConfigs only. The first IPv6 address of the external
   * IPv6 range associated with this instance, prefix length is stored
   * inexternalIpv6PrefixLength in ipv6AccessConfig. To use a static external IP
   * address, it must be unused and in the same region as the instance's zone.
   * If not specified, Google Cloud will automatically assign an external IPv6
   * address from the instance's subnetwork.
   *
   * @var string
   */
  public $externalIpv6;
  /**
   * Applies to ipv6AccessConfigs only. The prefix length of the external IPv6
   * range.
   *
   * @var int
   */
  public $externalIpv6PrefixLength;
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#accessConfig
   * for access configs.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of this access configuration. In accessConfigs (IPv4), the default
   * and recommended name is External NAT, but you can use any arbitrary string,
   * such as My external IP orNetwork Access. In ipv6AccessConfigs, the
   * recommend name is External IPv6.
   *
   * @var string
   */
  public $name;
  /**
   * Applies to accessConfigs (IPv4) only. Anexternal IP address associated with
   * this instance. Specify an unused static external IP address available to
   * the project or leave this field undefined to use an IP from a shared
   * ephemeral IP address pool. If you specify a static external IP address, it
   * must live in the same region as the zone of the instance.
   *
   * @var string
   */
  public $natIP;
  /**
   * This signifies the networking tier used for configuring this access
   * configuration and can only take the following values: PREMIUM,STANDARD.
   *
   * If an AccessConfig is specified without a valid external IP address, an
   * ephemeral IP will be created with this networkTier.
   *
   * If an AccessConfig with a valid external IP address is specified, it must
   * match that of the networkTier associated with the Address resource owning
   * that IP.
   *
   * @var string
   */
  public $networkTier;
  /**
   * The DNS domain name for the public PTR record.
   *
   * You can set this field only if the `setPublicPtr` field is enabled
   * inaccessConfig. If this field is unspecified inipv6AccessConfig, a default
   * PTR record will be created for first IP in associated external IPv6 range.
   *
   * @var string
   */
  public $publicPtrDomainName;
  /**
   * The resource URL for the security policy associated with this access
   * config.
   *
   * @var string
   */
  public $securityPolicy;
  /**
   * Specifies whether a public DNS 'PTR' record should be created to map the
   * external IP address of the instance to a DNS domain name.
   *
   * This field is not used in ipv6AccessConfig. A default PTR record will be
   * created if the VM has external IPv6 range associated.
   *
   * @var bool
   */
  public $setPublicPtr;
  /**
   * The type of configuration. In accessConfigs (IPv4), the default and only
   * option is ONE_TO_ONE_NAT. Inipv6AccessConfigs, the default and only option
   * isDIRECT_IPV6.
   *
   * @var string
   */
  public $type;

  /**
   * Applies to ipv6AccessConfigs only. The first IPv6 address of the external
   * IPv6 range associated with this instance, prefix length is stored
   * inexternalIpv6PrefixLength in ipv6AccessConfig. To use a static external IP
   * address, it must be unused and in the same region as the instance's zone.
   * If not specified, Google Cloud will automatically assign an external IPv6
   * address from the instance's subnetwork.
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
   * Applies to ipv6AccessConfigs only. The prefix length of the external IPv6
   * range.
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
   * Output only. [Output Only] Type of the resource. Alwayscompute#accessConfig
   * for access configs.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of this access configuration. In accessConfigs (IPv4), the default
   * and recommended name is External NAT, but you can use any arbitrary string,
   * such as My external IP orNetwork Access. In ipv6AccessConfigs, the
   * recommend name is External IPv6.
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
   * Applies to accessConfigs (IPv4) only. Anexternal IP address associated with
   * this instance. Specify an unused static external IP address available to
   * the project or leave this field undefined to use an IP from a shared
   * ephemeral IP address pool. If you specify a static external IP address, it
   * must live in the same region as the zone of the instance.
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
   * This signifies the networking tier used for configuring this access
   * configuration and can only take the following values: PREMIUM,STANDARD.
   *
   * If an AccessConfig is specified without a valid external IP address, an
   * ephemeral IP will be created with this networkTier.
   *
   * If an AccessConfig with a valid external IP address is specified, it must
   * match that of the networkTier associated with the Address resource owning
   * that IP.
   *
   * Accepted values: FIXED_STANDARD, PREMIUM, STANDARD,
   * STANDARD_OVERRIDES_FIXED_STANDARD
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
   * The DNS domain name for the public PTR record.
   *
   * You can set this field only if the `setPublicPtr` field is enabled
   * inaccessConfig. If this field is unspecified inipv6AccessConfig, a default
   * PTR record will be created for first IP in associated external IPv6 range.
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
   * The resource URL for the security policy associated with this access
   * config.
   *
   * @param string $securityPolicy
   */
  public function setSecurityPolicy($securityPolicy)
  {
    $this->securityPolicy = $securityPolicy;
  }
  /**
   * @return string
   */
  public function getSecurityPolicy()
  {
    return $this->securityPolicy;
  }
  /**
   * Specifies whether a public DNS 'PTR' record should be created to map the
   * external IP address of the instance to a DNS domain name.
   *
   * This field is not used in ipv6AccessConfig. A default PTR record will be
   * created if the VM has external IPv6 range associated.
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
   * The type of configuration. In accessConfigs (IPv4), the default and only
   * option is ONE_TO_ONE_NAT. Inipv6AccessConfigs, the default and only option
   * isDIRECT_IPV6.
   *
   * Accepted values: DIRECT_IPV6, ONE_TO_ONE_NAT
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
class_alias(AccessConfig::class, 'Google_Service_Compute_AccessConfig');
