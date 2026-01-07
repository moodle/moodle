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

class DeviceInfo extends \Google\Model
{
  /**
   * Optional. The IP address of the device for the given context. **Note:**
   * Google Ads does not support IP address matching for end users in the
   * European Economic Area (EEA), United Kingdom (UK), or Switzerland (CH). Add
   * logic to conditionally exclude sharing IP addresses from users from these
   * regions and ensure that you provide users with clear and comprehensive
   * information about the data you collect on your sites, apps, and other
   * properties and get consent where required by law or any applicable Google
   * policies. See the [About offline conversion
   * imports](https://support.google.com/google-ads/answer/2998031) page for
   * more details.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Optional. The user-agent string of the device for the given context.
   *
   * @var string
   */
  public $userAgent;

  /**
   * Optional. The IP address of the device for the given context. **Note:**
   * Google Ads does not support IP address matching for end users in the
   * European Economic Area (EEA), United Kingdom (UK), or Switzerland (CH). Add
   * logic to conditionally exclude sharing IP addresses from users from these
   * regions and ensure that you provide users with clear and comprehensive
   * information about the data you collect on your sites, apps, and other
   * properties and get consent where required by law or any applicable Google
   * policies. See the [About offline conversion
   * imports](https://support.google.com/google-ads/answer/2998031) page for
   * more details.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Optional. The user-agent string of the device for the given context.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceInfo::class, 'Google_Service_DataManager_DeviceInfo');
