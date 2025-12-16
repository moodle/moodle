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

namespace Google\Service\DataManager;

class AdIdentifiers extends \Google\Model
{
  /**
   * Optional. The click identifier for clicks associated with app events and
   * originating from iOS devices starting with iOS14.
   *
   * @var string
   */
  public $gbraid;
  /**
   * Optional. The Google click ID (gclid) associated with this event.
   *
   * @var string
   */
  public $gclid;
  protected $landingPageDeviceInfoType = DeviceInfo::class;
  protected $landingPageDeviceInfoDataType = '';
  /**
   * Optional. Session attributes for event attribution and modeling.
   *
   * @var string
   */
  public $sessionAttributes;
  /**
   * Optional. The click identifier for clicks associated with web events and
   * originating from iOS devices starting with iOS14.
   *
   * @var string
   */
  public $wbraid;

  /**
   * Optional. The click identifier for clicks associated with app events and
   * originating from iOS devices starting with iOS14.
   *
   * @param string $gbraid
   */
  public function setGbraid($gbraid)
  {
    $this->gbraid = $gbraid;
  }
  /**
   * @return string
   */
  public function getGbraid()
  {
    return $this->gbraid;
  }
  /**
   * Optional. The Google click ID (gclid) associated with this event.
   *
   * @param string $gclid
   */
  public function setGclid($gclid)
  {
    $this->gclid = $gclid;
  }
  /**
   * @return string
   */
  public function getGclid()
  {
    return $this->gclid;
  }
  /**
   * Optional. Information gathered about the device being used (if any) at the
   * time of landing onto the advertiser’s site after interacting with the ad.
   *
   * @param DeviceInfo $landingPageDeviceInfo
   */
  public function setLandingPageDeviceInfo(DeviceInfo $landingPageDeviceInfo)
  {
    $this->landingPageDeviceInfo = $landingPageDeviceInfo;
  }
  /**
   * @return DeviceInfo
   */
  public function getLandingPageDeviceInfo()
  {
    return $this->landingPageDeviceInfo;
  }
  /**
   * Optional. Session attributes for event attribution and modeling.
   *
   * @param string $sessionAttributes
   */
  public function setSessionAttributes($sessionAttributes)
  {
    $this->sessionAttributes = $sessionAttributes;
  }
  /**
   * @return string
   */
  public function getSessionAttributes()
  {
    return $this->sessionAttributes;
  }
  /**
   * Optional. The click identifier for clicks associated with web events and
   * originating from iOS devices starting with iOS14.
   *
   * @param string $wbraid
   */
  public function setWbraid($wbraid)
  {
    $this->wbraid = $wbraid;
  }
  /**
   * @return string
   */
  public function getWbraid()
  {
    return $this->wbraid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdIdentifiers::class, 'Google_Service_DataManager_AdIdentifiers');
