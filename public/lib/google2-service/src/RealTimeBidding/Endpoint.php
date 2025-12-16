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

namespace Google\Service\RealTimeBidding;

class Endpoint extends \Google\Model
{
  /**
   * Placeholder for undefined bid protocol. This value should not be used.
   */
  public const BID_PROTOCOL_BID_PROTOCOL_UNSPECIFIED = 'BID_PROTOCOL_UNSPECIFIED';
  /**
   * Google RTB protocol / Protobuf encoding.
   *
   * @deprecated
   */
  public const BID_PROTOCOL_GOOGLE_RTB = 'GOOGLE_RTB';
  /**
   * OpenRTB / JSON encoding (unversioned/latest).
   */
  public const BID_PROTOCOL_OPENRTB_JSON = 'OPENRTB_JSON';
  /**
   * OpenRTB / Protobuf encoding (unversioned/latest).
   */
  public const BID_PROTOCOL_OPENRTB_PROTOBUF = 'OPENRTB_PROTOBUF';
  /**
   * A placeholder for an undefined trading region. This value should not be
   * used.
   */
  public const TRADING_LOCATION_TRADING_LOCATION_UNSPECIFIED = 'TRADING_LOCATION_UNSPECIFIED';
  /**
   * The Western US trading location.
   */
  public const TRADING_LOCATION_US_WEST = 'US_WEST';
  /**
   * The Eastern US trading location.
   */
  public const TRADING_LOCATION_US_EAST = 'US_EAST';
  /**
   * The European trading location.
   */
  public const TRADING_LOCATION_EUROPE = 'EUROPE';
  /**
   * The Asia trading location.
   */
  public const TRADING_LOCATION_ASIA = 'ASIA';
  /**
   * The protocol that the bidder endpoint is using.
   *
   * @var string
   */
  public $bidProtocol;
  /**
   * The maximum number of queries per second allowed to be sent to this server.
   *
   * @var string
   */
  public $maximumQps;
  /**
   * Output only. Name of the endpoint resource that must follow the pattern
   * `bidders/{bidderAccountId}/endpoints/{endpointId}`, where {bidderAccountId}
   * is the account ID of the bidder who operates this endpoint, and
   * {endpointId} is a unique ID assigned by the server.
   *
   * @var string
   */
  public $name;
  /**
   * The trading location that bid requests should be sent from. See
   * https://developers.google.com/authorized-buyers/rtb/peer-guide#trading-
   * locations for further information.
   *
   * @var string
   */
  public $tradingLocation;
  /**
   * Output only. The URL that bid requests should be sent to.
   *
   * @var string
   */
  public $url;

  /**
   * The protocol that the bidder endpoint is using.
   *
   * Accepted values: BID_PROTOCOL_UNSPECIFIED, GOOGLE_RTB, OPENRTB_JSON,
   * OPENRTB_PROTOBUF
   *
   * @param self::BID_PROTOCOL_* $bidProtocol
   */
  public function setBidProtocol($bidProtocol)
  {
    $this->bidProtocol = $bidProtocol;
  }
  /**
   * @return self::BID_PROTOCOL_*
   */
  public function getBidProtocol()
  {
    return $this->bidProtocol;
  }
  /**
   * The maximum number of queries per second allowed to be sent to this server.
   *
   * @param string $maximumQps
   */
  public function setMaximumQps($maximumQps)
  {
    $this->maximumQps = $maximumQps;
  }
  /**
   * @return string
   */
  public function getMaximumQps()
  {
    return $this->maximumQps;
  }
  /**
   * Output only. Name of the endpoint resource that must follow the pattern
   * `bidders/{bidderAccountId}/endpoints/{endpointId}`, where {bidderAccountId}
   * is the account ID of the bidder who operates this endpoint, and
   * {endpointId} is a unique ID assigned by the server.
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
   * The trading location that bid requests should be sent from. See
   * https://developers.google.com/authorized-buyers/rtb/peer-guide#trading-
   * locations for further information.
   *
   * Accepted values: TRADING_LOCATION_UNSPECIFIED, US_WEST, US_EAST, EUROPE,
   * ASIA
   *
   * @param self::TRADING_LOCATION_* $tradingLocation
   */
  public function setTradingLocation($tradingLocation)
  {
    $this->tradingLocation = $tradingLocation;
  }
  /**
   * @return self::TRADING_LOCATION_*
   */
  public function getTradingLocation()
  {
    return $this->tradingLocation;
  }
  /**
   * Output only. The URL that bid requests should be sent to.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Endpoint::class, 'Google_Service_RealTimeBidding_Endpoint');
