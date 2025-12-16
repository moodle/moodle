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

class GooglePlayDeveloperReportingV1beta1DeviceModelSummary extends \Google\Model
{
  protected $deviceIdType = GooglePlayDeveloperReportingV1beta1DeviceId::class;
  protected $deviceIdDataType = '';
  /**
   * Link to the device in Play Device Catalog.
   *
   * @var string
   */
  public $deviceUri;
  /**
   * Display name of the device.
   *
   * @var string
   */
  public $marketingName;

  /**
   * Identifier of the device.
   *
   * @param GooglePlayDeveloperReportingV1beta1DeviceId $deviceId
   */
  public function setDeviceId(GooglePlayDeveloperReportingV1beta1DeviceId $deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return GooglePlayDeveloperReportingV1beta1DeviceId
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Link to the device in Play Device Catalog.
   *
   * @param string $deviceUri
   */
  public function setDeviceUri($deviceUri)
  {
    $this->deviceUri = $deviceUri;
  }
  /**
   * @return string
   */
  public function getDeviceUri()
  {
    return $this->deviceUri;
  }
  /**
   * Display name of the device.
   *
   * @param string $marketingName
   */
  public function setMarketingName($marketingName)
  {
    $this->marketingName = $marketingName;
  }
  /**
   * @return string
   */
  public function getMarketingName()
  {
    return $this->marketingName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1DeviceModelSummary::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1DeviceModelSummary');
