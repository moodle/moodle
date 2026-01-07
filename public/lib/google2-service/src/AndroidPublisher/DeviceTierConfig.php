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

class DeviceTierConfig extends \Google\Collection
{
  protected $collection_key = 'userCountrySets';
  protected $deviceGroupsType = DeviceGroup::class;
  protected $deviceGroupsDataType = 'array';
  /**
   * Output only. The device tier config ID.
   *
   * @var string
   */
  public $deviceTierConfigId;
  protected $deviceTierSetType = DeviceTierSet::class;
  protected $deviceTierSetDataType = '';
  protected $userCountrySetsType = UserCountrySet::class;
  protected $userCountrySetsDataType = 'array';

  /**
   * Definition of device groups for the app.
   *
   * @param DeviceGroup[] $deviceGroups
   */
  public function setDeviceGroups($deviceGroups)
  {
    $this->deviceGroups = $deviceGroups;
  }
  /**
   * @return DeviceGroup[]
   */
  public function getDeviceGroups()
  {
    return $this->deviceGroups;
  }
  /**
   * Output only. The device tier config ID.
   *
   * @param string $deviceTierConfigId
   */
  public function setDeviceTierConfigId($deviceTierConfigId)
  {
    $this->deviceTierConfigId = $deviceTierConfigId;
  }
  /**
   * @return string
   */
  public function getDeviceTierConfigId()
  {
    return $this->deviceTierConfigId;
  }
  /**
   * Definition of the set of device tiers for the app.
   *
   * @param DeviceTierSet $deviceTierSet
   */
  public function setDeviceTierSet(DeviceTierSet $deviceTierSet)
  {
    $this->deviceTierSet = $deviceTierSet;
  }
  /**
   * @return DeviceTierSet
   */
  public function getDeviceTierSet()
  {
    return $this->deviceTierSet;
  }
  /**
   * Definition of user country sets for the app.
   *
   * @param UserCountrySet[] $userCountrySets
   */
  public function setUserCountrySets($userCountrySets)
  {
    $this->userCountrySets = $userCountrySets;
  }
  /**
   * @return UserCountrySet[]
   */
  public function getUserCountrySets()
  {
    return $this->userCountrySets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceTierConfig::class, 'Google_Service_AndroidPublisher_DeviceTierConfig');
