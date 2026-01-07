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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1DeviceId extends \Google\Model
{
  /**
   * Value of Build.BRAND.
   *
   * @var string
   */
  public $buildBrand;
  /**
   * Value of Build.DEVICE.
   *
   * @var string
   */
  public $buildDevice;

  /**
   * Value of Build.BRAND.
   *
   * @param string $buildBrand
   */
  public function setBuildBrand($buildBrand)
  {
    $this->buildBrand = $buildBrand;
  }
  /**
   * @return string
   */
  public function getBuildBrand()
  {
    return $this->buildBrand;
  }
  /**
   * Value of Build.DEVICE.
   *
   * @param string $buildDevice
   */
  public function setBuildDevice($buildDevice)
  {
    $this->buildDevice = $buildDevice;
  }
  /**
   * @return string
   */
  public function getBuildDevice()
  {
    return $this->buildDevice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1DeviceId::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1DeviceId');
