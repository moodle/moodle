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

class PacketMirroringFilter extends \Google\Collection
{
  /**
   * Default, both directions are mirrored.
   */
  public const DIRECTION_BOTH = 'BOTH';
  /**
   * Only egress traffic is mirrored.
   */
  public const DIRECTION_EGRESS = 'EGRESS';
  /**
   * Only ingress traffic is mirrored.
   */
  public const DIRECTION_INGRESS = 'INGRESS';
  protected $collection_key = 'cidrRanges';
  protected $internal_gapi_mappings = [
        "iPProtocols" => "IPProtocols",
  ];
  /**
   * Protocols that apply as filter on mirrored traffic. If no protocols are
   * specified, all traffic that matches the specified CIDR ranges is mirrored.
   * If neither cidrRanges nor IPProtocols is specified, all IPv4 traffic is
   * mirrored.
   *
   * @var string[]
   */
  public $iPProtocols;
  /**
   * One or more IPv4 or IPv6 CIDR ranges that apply as filters on the source
   * (ingress) or destination (egress) IP in the IP header. If no ranges are
   * specified, all IPv4 traffic that matches the specified IPProtocols is
   * mirrored. If neither cidrRanges nor IPProtocols is specified, all IPv4
   * traffic is mirrored. To mirror all IPv4 and IPv6 traffic, use
   * "0.0.0.0/0,::/0".
   *
   * @var string[]
   */
  public $cidrRanges;
  /**
   * Direction of traffic to mirror, either INGRESS, EGRESS, or BOTH. The
   * default is BOTH.
   *
   * @var string
   */
  public $direction;

  /**
   * Protocols that apply as filter on mirrored traffic. If no protocols are
   * specified, all traffic that matches the specified CIDR ranges is mirrored.
   * If neither cidrRanges nor IPProtocols is specified, all IPv4 traffic is
   * mirrored.
   *
   * @param string[] $iPProtocols
   */
  public function setIPProtocols($iPProtocols)
  {
    $this->iPProtocols = $iPProtocols;
  }
  /**
   * @return string[]
   */
  public function getIPProtocols()
  {
    return $this->iPProtocols;
  }
  /**
   * One or more IPv4 or IPv6 CIDR ranges that apply as filters on the source
   * (ingress) or destination (egress) IP in the IP header. If no ranges are
   * specified, all IPv4 traffic that matches the specified IPProtocols is
   * mirrored. If neither cidrRanges nor IPProtocols is specified, all IPv4
   * traffic is mirrored. To mirror all IPv4 and IPv6 traffic, use
   * "0.0.0.0/0,::/0".
   *
   * @param string[] $cidrRanges
   */
  public function setCidrRanges($cidrRanges)
  {
    $this->cidrRanges = $cidrRanges;
  }
  /**
   * @return string[]
   */
  public function getCidrRanges()
  {
    return $this->cidrRanges;
  }
  /**
   * Direction of traffic to mirror, either INGRESS, EGRESS, or BOTH. The
   * default is BOTH.
   *
   * Accepted values: BOTH, EGRESS, INGRESS
   *
   * @param self::DIRECTION_* $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }
  /**
   * @return self::DIRECTION_*
   */
  public function getDirection()
  {
    return $this->direction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PacketMirroringFilter::class, 'Google_Service_Compute_PacketMirroringFilter');
