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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1ChromeAppRequest extends \Google\Model
{
  /**
   * Output only. Format:
   * app_details=customers/{customer_id}/apps/chrome/{app_id}
   *
   * @var string
   */
  public $appDetails;
  /**
   * Output only. Unique store identifier for the app. Example:
   * "gmbmikajjgmnabiglmofipeabaddhgne" for the Save to Google Drive Chrome
   * extension.
   *
   * @var string
   */
  public $appId;
  /**
   * Output only. The uri for the detail page of the item.
   *
   * @var string
   */
  public $detailUri;
  /**
   * Output only. App's display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A link to an image that can be used as an icon for the
   * product.
   *
   * @var string
   */
  public $iconUri;
  /**
   * Output only. The timestamp of the most recently made request for this app.
   *
   * @var string
   */
  public $latestRequestTime;
  /**
   * Output only. Total count of requests for this app.
   *
   * @var string
   */
  public $requestCount;

  /**
   * Output only. Format:
   * app_details=customers/{customer_id}/apps/chrome/{app_id}
   *
   * @param string $appDetails
   */
  public function setAppDetails($appDetails)
  {
    $this->appDetails = $appDetails;
  }
  /**
   * @return string
   */
  public function getAppDetails()
  {
    return $this->appDetails;
  }
  /**
   * Output only. Unique store identifier for the app. Example:
   * "gmbmikajjgmnabiglmofipeabaddhgne" for the Save to Google Drive Chrome
   * extension.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Output only. The uri for the detail page of the item.
   *
   * @param string $detailUri
   */
  public function setDetailUri($detailUri)
  {
    $this->detailUri = $detailUri;
  }
  /**
   * @return string
   */
  public function getDetailUri()
  {
    return $this->detailUri;
  }
  /**
   * Output only. App's display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. A link to an image that can be used as an icon for the
   * product.
   *
   * @param string $iconUri
   */
  public function setIconUri($iconUri)
  {
    $this->iconUri = $iconUri;
  }
  /**
   * @return string
   */
  public function getIconUri()
  {
    return $this->iconUri;
  }
  /**
   * Output only. The timestamp of the most recently made request for this app.
   *
   * @param string $latestRequestTime
   */
  public function setLatestRequestTime($latestRequestTime)
  {
    $this->latestRequestTime = $latestRequestTime;
  }
  /**
   * @return string
   */
  public function getLatestRequestTime()
  {
    return $this->latestRequestTime;
  }
  /**
   * Output only. Total count of requests for this app.
   *
   * @param string $requestCount
   */
  public function setRequestCount($requestCount)
  {
    $this->requestCount = $requestCount;
  }
  /**
   * @return string
   */
  public function getRequestCount()
  {
    return $this->requestCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1ChromeAppRequest::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1ChromeAppRequest');
