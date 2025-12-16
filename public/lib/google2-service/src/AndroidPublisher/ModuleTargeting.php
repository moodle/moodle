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

class ModuleTargeting extends \Google\Collection
{
  protected $collection_key = 'deviceFeatureTargeting';
  protected $deviceFeatureTargetingType = DeviceFeatureTargeting::class;
  protected $deviceFeatureTargetingDataType = 'array';
  protected $sdkVersionTargetingType = SdkVersionTargeting::class;
  protected $sdkVersionTargetingDataType = '';
  protected $userCountriesTargetingType = UserCountriesTargeting::class;
  protected $userCountriesTargetingDataType = '';

  /**
   * Targeting for device features.
   *
   * @param DeviceFeatureTargeting[] $deviceFeatureTargeting
   */
  public function setDeviceFeatureTargeting($deviceFeatureTargeting)
  {
    $this->deviceFeatureTargeting = $deviceFeatureTargeting;
  }
  /**
   * @return DeviceFeatureTargeting[]
   */
  public function getDeviceFeatureTargeting()
  {
    return $this->deviceFeatureTargeting;
  }
  /**
   * The sdk version that the variant targets
   *
   * @param SdkVersionTargeting $sdkVersionTargeting
   */
  public function setSdkVersionTargeting(SdkVersionTargeting $sdkVersionTargeting)
  {
    $this->sdkVersionTargeting = $sdkVersionTargeting;
  }
  /**
   * @return SdkVersionTargeting
   */
  public function getSdkVersionTargeting()
  {
    return $this->sdkVersionTargeting;
  }
  /**
   * Countries-level targeting
   *
   * @param UserCountriesTargeting $userCountriesTargeting
   */
  public function setUserCountriesTargeting(UserCountriesTargeting $userCountriesTargeting)
  {
    $this->userCountriesTargeting = $userCountriesTargeting;
  }
  /**
   * @return UserCountriesTargeting
   */
  public function getUserCountriesTargeting()
  {
    return $this->userCountriesTargeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModuleTargeting::class, 'Google_Service_AndroidPublisher_ModuleTargeting');
