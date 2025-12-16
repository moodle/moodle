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

class InterconnectLocationRegionInfo extends \Google\Model
{
  /**
   * This region is not in any common network presence with this
   * InterconnectLocation.
   */
  public const LOCATION_PRESENCE_GLOBAL = 'GLOBAL';
  /**
   * This region shares the same regional network presence as this
   * InterconnectLocation.
   */
  public const LOCATION_PRESENCE_LOCAL_REGION = 'LOCAL_REGION';
  /**
   * [Deprecated] This region is not in any common network presence with this
   * InterconnectLocation.
   */
  public const LOCATION_PRESENCE_LP_GLOBAL = 'LP_GLOBAL';
  /**
   * [Deprecated] This region shares the same regional network presence as this
   * InterconnectLocation.
   */
  public const LOCATION_PRESENCE_LP_LOCAL_REGION = 'LP_LOCAL_REGION';
  /**
   * Output only. Expected round-trip time in milliseconds, from this
   * InterconnectLocation to a VM in this region.
   *
   * @var string
   */
  public $expectedRttMs;
  /**
   * Output only. Identifies whether L2 Interconnect Attachments can be created
   * in this region for interconnects that are in this location.
   *
   * @var bool
   */
  public $l2ForwardingEnabled;
  /**
   * Output only. Identifies the network presence of this location.
   *
   * @var string
   */
  public $locationPresence;
  /**
   * Output only. URL for the region of this location.
   *
   * @var string
   */
  public $region;

  /**
   * Output only. Expected round-trip time in milliseconds, from this
   * InterconnectLocation to a VM in this region.
   *
   * @param string $expectedRttMs
   */
  public function setExpectedRttMs($expectedRttMs)
  {
    $this->expectedRttMs = $expectedRttMs;
  }
  /**
   * @return string
   */
  public function getExpectedRttMs()
  {
    return $this->expectedRttMs;
  }
  /**
   * Output only. Identifies whether L2 Interconnect Attachments can be created
   * in this region for interconnects that are in this location.
   *
   * @param bool $l2ForwardingEnabled
   */
  public function setL2ForwardingEnabled($l2ForwardingEnabled)
  {
    $this->l2ForwardingEnabled = $l2ForwardingEnabled;
  }
  /**
   * @return bool
   */
  public function getL2ForwardingEnabled()
  {
    return $this->l2ForwardingEnabled;
  }
  /**
   * Output only. Identifies the network presence of this location.
   *
   * Accepted values: GLOBAL, LOCAL_REGION, LP_GLOBAL, LP_LOCAL_REGION
   *
   * @param self::LOCATION_PRESENCE_* $locationPresence
   */
  public function setLocationPresence($locationPresence)
  {
    $this->locationPresence = $locationPresence;
  }
  /**
   * @return self::LOCATION_PRESENCE_*
   */
  public function getLocationPresence()
  {
    return $this->locationPresence;
  }
  /**
   * Output only. URL for the region of this location.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectLocationRegionInfo::class, 'Google_Service_Compute_InterconnectLocationRegionInfo');
