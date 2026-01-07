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

namespace Google\Service\SecurityCommandCenter;

class Attack extends \Google\Model
{
  /**
   * Type of attack, for example, 'SYN-flood', 'NTP-udp', or 'CHARGEN-udp'.
   *
   * @var string
   */
  public $classification;
  /**
   * Total BPS (bytes per second) volume of attack. Deprecated - refer to
   * volume_bps_long instead.
   *
   * @deprecated
   * @var int
   */
  public $volumeBps;
  /**
   * Total BPS (bytes per second) volume of attack.
   *
   * @var string
   */
  public $volumeBpsLong;
  /**
   * Total PPS (packets per second) volume of attack. Deprecated - refer to
   * volume_pps_long instead.
   *
   * @deprecated
   * @var int
   */
  public $volumePps;
  /**
   * Total PPS (packets per second) volume of attack.
   *
   * @var string
   */
  public $volumePpsLong;

  /**
   * Type of attack, for example, 'SYN-flood', 'NTP-udp', or 'CHARGEN-udp'.
   *
   * @param string $classification
   */
  public function setClassification($classification)
  {
    $this->classification = $classification;
  }
  /**
   * @return string
   */
  public function getClassification()
  {
    return $this->classification;
  }
  /**
   * Total BPS (bytes per second) volume of attack. Deprecated - refer to
   * volume_bps_long instead.
   *
   * @deprecated
   * @param int $volumeBps
   */
  public function setVolumeBps($volumeBps)
  {
    $this->volumeBps = $volumeBps;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getVolumeBps()
  {
    return $this->volumeBps;
  }
  /**
   * Total BPS (bytes per second) volume of attack.
   *
   * @param string $volumeBpsLong
   */
  public function setVolumeBpsLong($volumeBpsLong)
  {
    $this->volumeBpsLong = $volumeBpsLong;
  }
  /**
   * @return string
   */
  public function getVolumeBpsLong()
  {
    return $this->volumeBpsLong;
  }
  /**
   * Total PPS (packets per second) volume of attack. Deprecated - refer to
   * volume_pps_long instead.
   *
   * @deprecated
   * @param int $volumePps
   */
  public function setVolumePps($volumePps)
  {
    $this->volumePps = $volumePps;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getVolumePps()
  {
    return $this->volumePps;
  }
  /**
   * Total PPS (packets per second) volume of attack.
   *
   * @param string $volumePpsLong
   */
  public function setVolumePpsLong($volumePpsLong)
  {
    $this->volumePpsLong = $volumePpsLong;
  }
  /**
   * @return string
   */
  public function getVolumePpsLong()
  {
    return $this->volumePpsLong;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attack::class, 'Google_Service_SecurityCommandCenter_Attack');
