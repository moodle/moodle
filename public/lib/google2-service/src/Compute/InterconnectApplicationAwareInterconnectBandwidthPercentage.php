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

class InterconnectApplicationAwareInterconnectBandwidthPercentage extends \Google\Model
{
  /**
   * Traffic Class 1, corresponding to DSCP ranges (0-7) 000xxx.
   */
  public const TRAFFIC_CLASS_TC1 = 'TC1';
  /**
   * Traffic Class 2, corresponding to DSCP ranges (8-15) 001xxx.
   */
  public const TRAFFIC_CLASS_TC2 = 'TC2';
  /**
   * Traffic Class 3, corresponding to DSCP ranges (16-23) 010xxx.
   */
  public const TRAFFIC_CLASS_TC3 = 'TC3';
  /**
   * Traffic Class 4, corresponding to DSCP ranges (24-31) 011xxx.
   */
  public const TRAFFIC_CLASS_TC4 = 'TC4';
  /**
   * Traffic Class 5, corresponding to DSCP ranges (32-47) 10xxxx.
   */
  public const TRAFFIC_CLASS_TC5 = 'TC5';
  /**
   * Traffic Class 6, corresponding to DSCP ranges (48-63) 11xxxx.
   */
  public const TRAFFIC_CLASS_TC6 = 'TC6';
  /**
   * Bandwidth percentage for a specific traffic class.
   *
   * @var string
   */
  public $percentage;
  /**
   * TrafficClass whose bandwidth percentage is being specified.
   *
   * @var string
   */
  public $trafficClass;

  /**
   * Bandwidth percentage for a specific traffic class.
   *
   * @param string $percentage
   */
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  /**
   * @return string
   */
  public function getPercentage()
  {
    return $this->percentage;
  }
  /**
   * TrafficClass whose bandwidth percentage is being specified.
   *
   * Accepted values: TC1, TC2, TC3, TC4, TC5, TC6
   *
   * @param self::TRAFFIC_CLASS_* $trafficClass
   */
  public function setTrafficClass($trafficClass)
  {
    $this->trafficClass = $trafficClass;
  }
  /**
   * @return self::TRAFFIC_CLASS_*
   */
  public function getTrafficClass()
  {
    return $this->trafficClass;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectApplicationAwareInterconnectBandwidthPercentage::class, 'Google_Service_Compute_InterconnectApplicationAwareInterconnectBandwidthPercentage');
