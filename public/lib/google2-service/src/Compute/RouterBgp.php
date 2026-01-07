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

class RouterBgp extends \Google\Collection
{
  public const ADVERTISE_MODE_CUSTOM = 'CUSTOM';
  public const ADVERTISE_MODE_DEFAULT = 'DEFAULT';
  protected $collection_key = 'advertisedIpRanges';
  /**
   * User-specified flag to indicate which mode to use for advertisement. The
   * options are DEFAULT or CUSTOM.
   *
   * @var string
   */
  public $advertiseMode;
  /**
   * User-specified list of prefix groups to advertise in custom mode. This
   * field can only be populated if advertise_mode is CUSTOM and is advertised
   * to all peers of the router. These groups will be advertised in addition to
   * any specified prefixes. Leave this field blank to advertise no custom
   * groups.
   *
   * @var string[]
   */
  public $advertisedGroups;
  protected $advertisedIpRangesType = RouterAdvertisedIpRange::class;
  protected $advertisedIpRangesDataType = 'array';
  /**
   * Local BGP Autonomous System Number (ASN). Must be anRFC6996 private ASN,
   * either 16-bit or 32-bit. The value will be fixed for this router resource.
   * All VPN tunnels that link to this router will have the same local ASN.
   *
   * @var string
   */
  public $asn;
  /**
   * Explicitly specifies a range of valid BGP Identifiers for this Router. It
   * is provided as a link-local IPv4 range (from 169.254.0.0/16), of size at
   * least /30, even if the BGP sessions are over IPv6. It must not overlap with
   * any IPv4 BGP session ranges.
   *
   * Other vendors commonly call this "router ID".
   *
   * @var string
   */
  public $identifierRange;
  /**
   * The interval in seconds between BGP keepalive messages that are sent to the
   * peer.
   *
   * Hold time is three times the interval at which keepalive messages are sent,
   * and the hold time is the maximum number of seconds allowed to elapse
   * between successive keepalive messages that BGP receives from a peer.
   *
   * BGP will use the smaller of either the local hold time value or the peer's
   * hold time value as the hold time for the BGP connection between the two
   * peers.
   *
   * If set, this value must be between 20 and 60. The default is 20.
   *
   * @var string
   */
  public $keepaliveInterval;

  /**
   * User-specified flag to indicate which mode to use for advertisement. The
   * options are DEFAULT or CUSTOM.
   *
   * Accepted values: CUSTOM, DEFAULT
   *
   * @param self::ADVERTISE_MODE_* $advertiseMode
   */
  public function setAdvertiseMode($advertiseMode)
  {
    $this->advertiseMode = $advertiseMode;
  }
  /**
   * @return self::ADVERTISE_MODE_*
   */
  public function getAdvertiseMode()
  {
    return $this->advertiseMode;
  }
  /**
   * User-specified list of prefix groups to advertise in custom mode. This
   * field can only be populated if advertise_mode is CUSTOM and is advertised
   * to all peers of the router. These groups will be advertised in addition to
   * any specified prefixes. Leave this field blank to advertise no custom
   * groups.
   *
   * @param string[] $advertisedGroups
   */
  public function setAdvertisedGroups($advertisedGroups)
  {
    $this->advertisedGroups = $advertisedGroups;
  }
  /**
   * @return string[]
   */
  public function getAdvertisedGroups()
  {
    return $this->advertisedGroups;
  }
  /**
   * User-specified list of individual IP ranges to advertise in custom mode.
   * This field can only be populated if advertise_mode is CUSTOM and is
   * advertised to all peers of the router. These IP ranges will be advertised
   * in addition to any specified groups. Leave this field blank to advertise no
   * custom IP ranges.
   *
   * @param RouterAdvertisedIpRange[] $advertisedIpRanges
   */
  public function setAdvertisedIpRanges($advertisedIpRanges)
  {
    $this->advertisedIpRanges = $advertisedIpRanges;
  }
  /**
   * @return RouterAdvertisedIpRange[]
   */
  public function getAdvertisedIpRanges()
  {
    return $this->advertisedIpRanges;
  }
  /**
   * Local BGP Autonomous System Number (ASN). Must be anRFC6996 private ASN,
   * either 16-bit or 32-bit. The value will be fixed for this router resource.
   * All VPN tunnels that link to this router will have the same local ASN.
   *
   * @param string $asn
   */
  public function setAsn($asn)
  {
    $this->asn = $asn;
  }
  /**
   * @return string
   */
  public function getAsn()
  {
    return $this->asn;
  }
  /**
   * Explicitly specifies a range of valid BGP Identifiers for this Router. It
   * is provided as a link-local IPv4 range (from 169.254.0.0/16), of size at
   * least /30, even if the BGP sessions are over IPv6. It must not overlap with
   * any IPv4 BGP session ranges.
   *
   * Other vendors commonly call this "router ID".
   *
   * @param string $identifierRange
   */
  public function setIdentifierRange($identifierRange)
  {
    $this->identifierRange = $identifierRange;
  }
  /**
   * @return string
   */
  public function getIdentifierRange()
  {
    return $this->identifierRange;
  }
  /**
   * The interval in seconds between BGP keepalive messages that are sent to the
   * peer.
   *
   * Hold time is three times the interval at which keepalive messages are sent,
   * and the hold time is the maximum number of seconds allowed to elapse
   * between successive keepalive messages that BGP receives from a peer.
   *
   * BGP will use the smaller of either the local hold time value or the peer's
   * hold time value as the hold time for the BGP connection between the two
   * peers.
   *
   * If set, this value must be between 20 and 60. The default is 20.
   *
   * @param string $keepaliveInterval
   */
  public function setKeepaliveInterval($keepaliveInterval)
  {
    $this->keepaliveInterval = $keepaliveInterval;
  }
  /**
   * @return string
   */
  public function getKeepaliveInterval()
  {
    return $this->keepaliveInterval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterBgp::class, 'Google_Service_Compute_RouterBgp');
