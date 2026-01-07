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

class PublicAdvertisedPrefix extends \Google\Collection
{
  /**
   * This public advertised prefix can be used to create both regional and
   * global public delegated prefixes. It usually takes 4 weeks to create or
   * delete a public delegated prefix. The BGP status cannot be changed.
   */
  public const BYOIP_API_VERSION_V1 = 'V1';
  /**
   * This public advertised prefix can only be used to create regional public
   * delegated prefixes. Public delegated prefix creation and deletion takes
   * minutes and the BGP status can be modified.
   */
  public const BYOIP_API_VERSION_V2 = 'V2';
  /**
   * Default IPv6 access type. The prefix will be announced to the internet. All
   * children Public Delegated Prefixes will have IPv6 access type as EXTERNAL.
   */
  public const IPV6_ACCESS_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * The prefix will not be announced to the internet. Prefix will be used
   * privately within Cloud. All children Public Delegated Prefixes will have
   * IPv6 access type as INTERNAL.
   */
  public const IPV6_ACCESS_TYPE_INTERNAL = 'INTERNAL';
  /**
   * The public delegated prefix is global only. The provisioning will take ~4
   * weeks.
   */
  public const PDP_SCOPE_GLOBAL = 'GLOBAL';
  /**
   * The public delegated prefixes is BYOIP V1 legacy prefix. This is output
   * only value and no longer supported in BYOIP V2.
   */
  public const PDP_SCOPE_GLOBAL_AND_REGIONAL = 'GLOBAL_AND_REGIONAL';
  /**
   * The public delegated prefix is regional only. The provisioning will take a
   * few minutes.
   */
  public const PDP_SCOPE_REGIONAL = 'REGIONAL';
  /**
   * The prefix is announced to Internet.
   */
  public const STATUS_ANNOUNCED_TO_INTERNET = 'ANNOUNCED_TO_INTERNET';
  /**
   * RPKI validation is complete.
   */
  public const STATUS_INITIAL = 'INITIAL';
  /**
   * The prefix is fully configured.
   */
  public const STATUS_PREFIX_CONFIGURATION_COMPLETE = 'PREFIX_CONFIGURATION_COMPLETE';
  /**
   * The prefix is being configured.
   */
  public const STATUS_PREFIX_CONFIGURATION_IN_PROGRESS = 'PREFIX_CONFIGURATION_IN_PROGRESS';
  /**
   * The prefix is being removed.
   */
  public const STATUS_PREFIX_REMOVAL_IN_PROGRESS = 'PREFIX_REMOVAL_IN_PROGRESS';
  /**
   * User has configured the PTR.
   */
  public const STATUS_PTR_CONFIGURED = 'PTR_CONFIGURED';
  /**
   * The prefix is currently withdrawn but ready to be announced.
   */
  public const STATUS_READY_TO_ANNOUNCE = 'READY_TO_ANNOUNCE';
  /**
   * Reverse DNS lookup failed.
   */
  public const STATUS_REVERSE_DNS_LOOKUP_FAILED = 'REVERSE_DNS_LOOKUP_FAILED';
  /**
   * Reverse DNS lookup is successful.
   */
  public const STATUS_VALIDATED = 'VALIDATED';
  protected $collection_key = 'publicDelegatedPrefixs';
  /**
   * Output only. [Output Only] The version of BYOIP API.
   *
   * @var string
   */
  public $byoipApiVersion;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * The address to be used for reverse DNS verification.
   *
   * @var string
   */
  public $dnsVerificationIp;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a new PublicAdvertisedPrefix. An up-to-date fingerprint must be
   * provided in order to update thePublicAdvertisedPrefix, otherwise the
   * request will fail with error 412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * PublicAdvertisedPrefix.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * The address range, in CIDR format, represented by this public advertised
   * prefix.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * The internet access type for IPv6 Public Advertised Prefixes.
   *
   * @var string
   */
  public $ipv6AccessType;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#publicAdvertisedPrefix for public advertised prefixes.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * Specifies how child public delegated prefix will be scoped. It could be one
   * of following values:              - `REGIONAL`: The public delegated prefix
   * is regional only. The      provisioning will take a few minutes.      -
   * `GLOBAL`: The public delegated prefix is global only. The      provisioning
   * will take ~4 weeks.      - `GLOBAL_AND_REGIONAL` [output only]: The public
   * delegated prefixes is       BYOIP V1 legacy prefix. This is output only
   * value and no longer       supported in BYOIP V2.
   *
   * @var string
   */
  public $pdpScope;
  protected $publicDelegatedPrefixsType = PublicAdvertisedPrefixPublicDelegatedPrefix::class;
  protected $publicDelegatedPrefixsDataType = 'array';
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] The shared secret to be used for reverse DNS verification.
   *
   * @var string
   */
  public $sharedSecret;
  /**
   * The status of the public advertised prefix. Possible values include:
   * - `INITIAL`: RPKI validation is complete.      - `PTR_CONFIGURED`: User has
   * configured the PTR.      - `VALIDATED`: Reverse DNS lookup is successful.
   * - `REVERSE_DNS_LOOKUP_FAILED`: Reverse DNS lookup failed.      -
   * `PREFIX_CONFIGURATION_IN_PROGRESS`: The prefix is being      configured.
   * - `PREFIX_CONFIGURATION_COMPLETE`: The prefix is fully configured.      -
   * `PREFIX_REMOVAL_IN_PROGRESS`: The prefix is being removed.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. [Output Only] The version of BYOIP API.
   *
   * Accepted values: V1, V2
   *
   * @param self::BYOIP_API_VERSION_* $byoipApiVersion
   */
  public function setByoipApiVersion($byoipApiVersion)
  {
    $this->byoipApiVersion = $byoipApiVersion;
  }
  /**
   * @return self::BYOIP_API_VERSION_*
   */
  public function getByoipApiVersion()
  {
    return $this->byoipApiVersion;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The address to be used for reverse DNS verification.
   *
   * @param string $dnsVerificationIp
   */
  public function setDnsVerificationIp($dnsVerificationIp)
  {
    $this->dnsVerificationIp = $dnsVerificationIp;
  }
  /**
   * @return string
   */
  public function getDnsVerificationIp()
  {
    return $this->dnsVerificationIp;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a new PublicAdvertisedPrefix. An up-to-date fingerprint must be
   * provided in order to update thePublicAdvertisedPrefix, otherwise the
   * request will fail with error 412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * PublicAdvertisedPrefix.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The address range, in CIDR format, represented by this public advertised
   * prefix.
   *
   * @param string $ipCidrRange
   */
  public function setIpCidrRange($ipCidrRange)
  {
    $this->ipCidrRange = $ipCidrRange;
  }
  /**
   * @return string
   */
  public function getIpCidrRange()
  {
    return $this->ipCidrRange;
  }
  /**
   * The internet access type for IPv6 Public Advertised Prefixes.
   *
   * Accepted values: EXTERNAL, INTERNAL
   *
   * @param self::IPV6_ACCESS_TYPE_* $ipv6AccessType
   */
  public function setIpv6AccessType($ipv6AccessType)
  {
    $this->ipv6AccessType = $ipv6AccessType;
  }
  /**
   * @return self::IPV6_ACCESS_TYPE_*
   */
  public function getIpv6AccessType()
  {
    return $this->ipv6AccessType;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#publicAdvertisedPrefix for public advertised prefixes.
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
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Specifies how child public delegated prefix will be scoped. It could be one
   * of following values:              - `REGIONAL`: The public delegated prefix
   * is regional only. The      provisioning will take a few minutes.      -
   * `GLOBAL`: The public delegated prefix is global only. The      provisioning
   * will take ~4 weeks.      - `GLOBAL_AND_REGIONAL` [output only]: The public
   * delegated prefixes is       BYOIP V1 legacy prefix. This is output only
   * value and no longer       supported in BYOIP V2.
   *
   * Accepted values: GLOBAL, GLOBAL_AND_REGIONAL, REGIONAL
   *
   * @param self::PDP_SCOPE_* $pdpScope
   */
  public function setPdpScope($pdpScope)
  {
    $this->pdpScope = $pdpScope;
  }
  /**
   * @return self::PDP_SCOPE_*
   */
  public function getPdpScope()
  {
    return $this->pdpScope;
  }
  /**
   * Output only. [Output Only] The list of public delegated prefixes that exist
   * for this public advertised prefix.
   *
   * @param PublicAdvertisedPrefixPublicDelegatedPrefix[] $publicDelegatedPrefixs
   */
  public function setPublicDelegatedPrefixs($publicDelegatedPrefixs)
  {
    $this->publicDelegatedPrefixs = $publicDelegatedPrefixs;
  }
  /**
   * @return PublicAdvertisedPrefixPublicDelegatedPrefix[]
   */
  public function getPublicDelegatedPrefixs()
  {
    return $this->publicDelegatedPrefixs;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * [Output Only] The shared secret to be used for reverse DNS verification.
   *
   * @param string $sharedSecret
   */
  public function setSharedSecret($sharedSecret)
  {
    $this->sharedSecret = $sharedSecret;
  }
  /**
   * @return string
   */
  public function getSharedSecret()
  {
    return $this->sharedSecret;
  }
  /**
   * The status of the public advertised prefix. Possible values include:
   * - `INITIAL`: RPKI validation is complete.      - `PTR_CONFIGURED`: User has
   * configured the PTR.      - `VALIDATED`: Reverse DNS lookup is successful.
   * - `REVERSE_DNS_LOOKUP_FAILED`: Reverse DNS lookup failed.      -
   * `PREFIX_CONFIGURATION_IN_PROGRESS`: The prefix is being      configured.
   * - `PREFIX_CONFIGURATION_COMPLETE`: The prefix is fully configured.      -
   * `PREFIX_REMOVAL_IN_PROGRESS`: The prefix is being removed.
   *
   * Accepted values: ANNOUNCED_TO_INTERNET, INITIAL,
   * PREFIX_CONFIGURATION_COMPLETE, PREFIX_CONFIGURATION_IN_PROGRESS,
   * PREFIX_REMOVAL_IN_PROGRESS, PTR_CONFIGURED, READY_TO_ANNOUNCE,
   * REVERSE_DNS_LOOKUP_FAILED, VALIDATED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublicAdvertisedPrefix::class, 'Google_Service_Compute_PublicAdvertisedPrefix');
