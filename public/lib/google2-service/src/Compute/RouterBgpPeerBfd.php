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

class RouterBgpPeerBfd extends \Google\Model
{
  public const SESSION_INITIALIZATION_MODE_ACTIVE = 'ACTIVE';
  public const SESSION_INITIALIZATION_MODE_DISABLED = 'DISABLED';
  public const SESSION_INITIALIZATION_MODE_PASSIVE = 'PASSIVE';
  /**
   * The minimum interval, in milliseconds, between BFD control packets received
   * from the peer router. The actual value is negotiated between the two
   * routers and is equal to the greater of this value and the transmit interval
   * of the other router.
   *
   * If set, this value must be between 1000 and 30000.
   *
   * The default is 1000.
   *
   * @var string
   */
  public $minReceiveInterval;
  /**
   * The minimum interval, in milliseconds, between BFD control packets
   * transmitted to the peer router. The actual value is negotiated between the
   * two routers and is equal to the greater of this value and the corresponding
   * receive interval of the other router.
   *
   * If set, this value must be between 1000 and 30000.
   *
   * The default is 1000.
   *
   * @var string
   */
  public $minTransmitInterval;
  /**
   * The number of consecutive BFD packets that must be missed before BFD
   * declares that a peer is unavailable.
   *
   * If set, the value must be a value between 5 and 16.
   *
   * The default is 5.
   *
   * @var string
   */
  public $multiplier;
  /**
   * The BFD session initialization mode for this BGP peer.
   *
   * If set to ACTIVE, the Cloud Router will initiate the BFD session for this
   * BGP peer. If set to PASSIVE, the Cloud Router will wait for the peer router
   * to initiate the BFD session for this BGP peer. If set to DISABLED, BFD is
   * disabled for this BGP peer. The default is DISABLED.
   *
   * @var string
   */
  public $sessionInitializationMode;

  /**
   * The minimum interval, in milliseconds, between BFD control packets received
   * from the peer router. The actual value is negotiated between the two
   * routers and is equal to the greater of this value and the transmit interval
   * of the other router.
   *
   * If set, this value must be between 1000 and 30000.
   *
   * The default is 1000.
   *
   * @param string $minReceiveInterval
   */
  public function setMinReceiveInterval($minReceiveInterval)
  {
    $this->minReceiveInterval = $minReceiveInterval;
  }
  /**
   * @return string
   */
  public function getMinReceiveInterval()
  {
    return $this->minReceiveInterval;
  }
  /**
   * The minimum interval, in milliseconds, between BFD control packets
   * transmitted to the peer router. The actual value is negotiated between the
   * two routers and is equal to the greater of this value and the corresponding
   * receive interval of the other router.
   *
   * If set, this value must be between 1000 and 30000.
   *
   * The default is 1000.
   *
   * @param string $minTransmitInterval
   */
  public function setMinTransmitInterval($minTransmitInterval)
  {
    $this->minTransmitInterval = $minTransmitInterval;
  }
  /**
   * @return string
   */
  public function getMinTransmitInterval()
  {
    return $this->minTransmitInterval;
  }
  /**
   * The number of consecutive BFD packets that must be missed before BFD
   * declares that a peer is unavailable.
   *
   * If set, the value must be a value between 5 and 16.
   *
   * The default is 5.
   *
   * @param string $multiplier
   */
  public function setMultiplier($multiplier)
  {
    $this->multiplier = $multiplier;
  }
  /**
   * @return string
   */
  public function getMultiplier()
  {
    return $this->multiplier;
  }
  /**
   * The BFD session initialization mode for this BGP peer.
   *
   * If set to ACTIVE, the Cloud Router will initiate the BFD session for this
   * BGP peer. If set to PASSIVE, the Cloud Router will wait for the peer router
   * to initiate the BFD session for this BGP peer. If set to DISABLED, BFD is
   * disabled for this BGP peer. The default is DISABLED.
   *
   * Accepted values: ACTIVE, DISABLED, PASSIVE
   *
   * @param self::SESSION_INITIALIZATION_MODE_* $sessionInitializationMode
   */
  public function setSessionInitializationMode($sessionInitializationMode)
  {
    $this->sessionInitializationMode = $sessionInitializationMode;
  }
  /**
   * @return self::SESSION_INITIALIZATION_MODE_*
   */
  public function getSessionInitializationMode()
  {
    return $this->sessionInitializationMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterBgpPeerBfd::class, 'Google_Service_Compute_RouterBgpPeerBfd');
