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

class DeviceSelector extends \Google\Collection
{
  protected $collection_key = 'systemOnChips';
  protected $deviceRamType = DeviceRam::class;
  protected $deviceRamDataType = '';
  protected $excludedDeviceIdsType = DeviceId::class;
  protected $excludedDeviceIdsDataType = 'array';
  protected $forbiddenSystemFeaturesType = SystemFeature::class;
  protected $forbiddenSystemFeaturesDataType = 'array';
  protected $includedDeviceIdsType = DeviceId::class;
  protected $includedDeviceIdsDataType = 'array';
  protected $requiredSystemFeaturesType = SystemFeature::class;
  protected $requiredSystemFeaturesDataType = 'array';
  protected $systemOnChipsType = SystemOnChip::class;
  protected $systemOnChipsDataType = 'array';

  /**
   * Conditions on the device's RAM.
   *
   * @param DeviceRam $deviceRam
   */
  public function setDeviceRam(DeviceRam $deviceRam)
  {
    $this->deviceRam = $deviceRam;
  }
  /**
   * @return DeviceRam
   */
  public function getDeviceRam()
  {
    return $this->deviceRam;
  }
  /**
   * Device models excluded by this selector, even if they match all other
   * conditions.
   *
   * @param DeviceId[] $excludedDeviceIds
   */
  public function setExcludedDeviceIds($excludedDeviceIds)
  {
    $this->excludedDeviceIds = $excludedDeviceIds;
  }
  /**
   * @return DeviceId[]
   */
  public function getExcludedDeviceIds()
  {
    return $this->excludedDeviceIds;
  }
  /**
   * A device that has any of these system features is excluded by this
   * selector, even if it matches all other conditions.
   *
   * @param SystemFeature[] $forbiddenSystemFeatures
   */
  public function setForbiddenSystemFeatures($forbiddenSystemFeatures)
  {
    $this->forbiddenSystemFeatures = $forbiddenSystemFeatures;
  }
  /**
   * @return SystemFeature[]
   */
  public function getForbiddenSystemFeatures()
  {
    return $this->forbiddenSystemFeatures;
  }
  /**
   * Device models included by this selector.
   *
   * @param DeviceId[] $includedDeviceIds
   */
  public function setIncludedDeviceIds($includedDeviceIds)
  {
    $this->includedDeviceIds = $includedDeviceIds;
  }
  /**
   * @return DeviceId[]
   */
  public function getIncludedDeviceIds()
  {
    return $this->includedDeviceIds;
  }
  /**
   * A device needs to have all these system features to be included by the
   * selector.
   *
   * @param SystemFeature[] $requiredSystemFeatures
   */
  public function setRequiredSystemFeatures($requiredSystemFeatures)
  {
    $this->requiredSystemFeatures = $requiredSystemFeatures;
  }
  /**
   * @return SystemFeature[]
   */
  public function getRequiredSystemFeatures()
  {
    return $this->requiredSystemFeatures;
  }
  /**
   * Optional. The SoCs included by this selector. Only works for Android S+
   * devices.
   *
   * @param SystemOnChip[] $systemOnChips
   */
  public function setSystemOnChips($systemOnChips)
  {
    $this->systemOnChips = $systemOnChips;
  }
  /**
   * @return SystemOnChip[]
   */
  public function getSystemOnChips()
  {
    return $this->systemOnChips;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceSelector::class, 'Google_Service_AndroidPublisher_DeviceSelector');
