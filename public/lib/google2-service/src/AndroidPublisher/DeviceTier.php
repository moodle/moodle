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

namespace Google\Service\AndroidPublisher;

class DeviceTier extends \Google\Collection
{
  protected $collection_key = 'deviceGroupNames';
  /**
   * Groups of devices included in this tier. These groups must be defined
   * explicitly under device_groups in this configuration.
   *
   * @var string[]
   */
  public $deviceGroupNames;
  /**
   * The priority level of the tier. Tiers are evaluated in descending order of
   * level: the highest level tier has the highest priority. The highest tier
   * matching a given device is selected for that device. You should use a
   * contiguous range of levels for your tiers in a tier set; tier levels in a
   * tier set must be unique. For instance, if your tier set has 4 tiers
   * (including the global fallback), you should define tiers 1, 2 and 3 in this
   * configuration. Note: tier 0 is implicitly defined as a global fallback and
   * selected for devices that don't match any of the tiers explicitly defined
   * here. You mustn't define level 0 explicitly in this configuration.
   *
   * @var int
   */
  public $level;

  /**
   * Groups of devices included in this tier. These groups must be defined
   * explicitly under device_groups in this configuration.
   *
   * @param string[] $deviceGroupNames
   */
  public function setDeviceGroupNames($deviceGroupNames)
  {
    $this->deviceGroupNames = $deviceGroupNames;
  }
  /**
   * @return string[]
   */
  public function getDeviceGroupNames()
  {
    return $this->deviceGroupNames;
  }
  /**
   * The priority level of the tier. Tiers are evaluated in descending order of
   * level: the highest level tier has the highest priority. The highest tier
   * matching a given device is selected for that device. You should use a
   * contiguous range of levels for your tiers in a tier set; tier levels in a
   * tier set must be unique. For instance, if your tier set has 4 tiers
   * (including the global fallback), you should define tiers 1, 2 and 3 in this
   * configuration. Note: tier 0 is implicitly defined as a global fallback and
   * selected for devices that don't match any of the tiers explicitly defined
   * here. You mustn't define level 0 explicitly in this configuration.
   *
   * @param int $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return int
   */
  public function getLevel()
  {
    return $this->level;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceTier::class, 'Google_Service_AndroidPublisher_DeviceTier');
