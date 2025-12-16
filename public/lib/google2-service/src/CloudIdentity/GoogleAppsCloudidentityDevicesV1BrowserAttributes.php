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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1BrowserAttributes extends \Google\Model
{
  protected $chromeBrowserInfoType = GoogleAppsCloudidentityDevicesV1BrowserInfo::class;
  protected $chromeBrowserInfoDataType = '';
  /**
   * Chrome profile ID that is exposed by the Chrome API. It is unique for each
   * device.
   *
   * @var string
   */
  public $chromeProfileId;
  /**
   * Timestamp in milliseconds since the Unix epoch when the profile/gcm id was
   * last synced.
   *
   * @var string
   */
  public $lastProfileSyncTime;

  /**
   * Represents the current state of the [Chrome browser
   * attributes](https://cloud.google.com/access-context-manager/docs/browser-
   * attributes) sent by the [Endpoint Verification
   * extension](https://chromewebstore.google.com/detail/endpoint-
   * verification/callobklhcbilhphinckomhgkigmfocg?pli=1).
   *
   * @param GoogleAppsCloudidentityDevicesV1BrowserInfo $chromeBrowserInfo
   */
  public function setChromeBrowserInfo(GoogleAppsCloudidentityDevicesV1BrowserInfo $chromeBrowserInfo)
  {
    $this->chromeBrowserInfo = $chromeBrowserInfo;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1BrowserInfo
   */
  public function getChromeBrowserInfo()
  {
    return $this->chromeBrowserInfo;
  }
  /**
   * Chrome profile ID that is exposed by the Chrome API. It is unique for each
   * device.
   *
   * @param string $chromeProfileId
   */
  public function setChromeProfileId($chromeProfileId)
  {
    $this->chromeProfileId = $chromeProfileId;
  }
  /**
   * @return string
   */
  public function getChromeProfileId()
  {
    return $this->chromeProfileId;
  }
  /**
   * Timestamp in milliseconds since the Unix epoch when the profile/gcm id was
   * last synced.
   *
   * @param string $lastProfileSyncTime
   */
  public function setLastProfileSyncTime($lastProfileSyncTime)
  {
    $this->lastProfileSyncTime = $lastProfileSyncTime;
  }
  /**
   * @return string
   */
  public function getLastProfileSyncTime()
  {
    return $this->lastProfileSyncTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1BrowserAttributes::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1BrowserAttributes');
