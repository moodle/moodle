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

class GoogleChromeManagementV1AppDetails extends \Google\Model
{
  /**
   * App type unspecified.
   */
  public const TYPE_APP_ITEM_TYPE_UNSPECIFIED = 'APP_ITEM_TYPE_UNSPECIFIED';
  /**
   * Chrome app.
   */
  public const TYPE_CHROME = 'CHROME';
  /**
   * ARC++ app.
   */
  public const TYPE_ANDROID = 'ANDROID';
  /**
   * Web app.
   */
  public const TYPE_WEB = 'WEB';
  protected $androidAppInfoType = GoogleChromeManagementV1AndroidAppInfo::class;
  protected $androidAppInfoDataType = '';
  /**
   * Output only. Unique store identifier for the item. Examples:
   * "gmbmikajjgmnabiglmofipeabaddhgne" for the Save to Google Drive Chrome
   * extension, "com.google.android.apps.docs" for the Google Drive Android app.
   *
   * @var string
   */
  public $appId;
  protected $chromeAppInfoType = GoogleChromeManagementV1ChromeAppInfo::class;
  protected $chromeAppInfoDataType = '';
  /**
   * Output only. App's description.
   *
   * @var string
   */
  public $description;
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
   * Output only. First published time.
   *
   * @var string
   */
  public $firstPublishTime;
  /**
   * Output only. Home page or Website uri.
   *
   * @var string
   */
  public $homepageUri;
  /**
   * Output only. A link to an image that can be used as an icon for the
   * product.
   *
   * @var string
   */
  public $iconUri;
  /**
   * Output only. Indicates if the app has to be paid for OR has paid content.
   *
   * @var bool
   */
  public $isPaidApp;
  /**
   * Output only. Latest published time.
   *
   * @var string
   */
  public $latestPublishTime;
  /**
   * Output only. Format:
   * name=customers/{customer_id}/apps/{chrome|android|web}/{app_id}@{version}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The URI pointing to the privacy policy of the app, if it was
   * provided by the developer. Version-specific field that will only be set
   * when the requested app version is found.
   *
   * @var string
   */
  public $privacyPolicyUri;
  /**
   * Output only. The publisher of the item.
   *
   * @var string
   */
  public $publisher;
  /**
   * Output only. Number of reviews received. Chrome Web Store review
   * information will always be for the latest version of an app.
   *
   * @var string
   */
  public $reviewNumber;
  /**
   * Output only. The rating of the app (on 5 stars). Chrome Web Store review
   * information will always be for the latest version of an app.
   *
   * @var float
   */
  public $reviewRating;
  /**
   * Output only. App version. A new revision is committed whenever a new
   * version of the app is published.
   *
   * @var string
   */
  public $revisionId;
  protected $serviceErrorType = GoogleRpcStatus::class;
  protected $serviceErrorDataType = '';
  /**
   * Output only. App type.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Android app information.
   *
   * @param GoogleChromeManagementV1AndroidAppInfo $androidAppInfo
   */
  public function setAndroidAppInfo(GoogleChromeManagementV1AndroidAppInfo $androidAppInfo)
  {
    $this->androidAppInfo = $androidAppInfo;
  }
  /**
   * @return GoogleChromeManagementV1AndroidAppInfo
   */
  public function getAndroidAppInfo()
  {
    return $this->androidAppInfo;
  }
  /**
   * Output only. Unique store identifier for the item. Examples:
   * "gmbmikajjgmnabiglmofipeabaddhgne" for the Save to Google Drive Chrome
   * extension, "com.google.android.apps.docs" for the Google Drive Android app.
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
   * Output only. Chrome Web Store app information.
   *
   * @param GoogleChromeManagementV1ChromeAppInfo $chromeAppInfo
   */
  public function setChromeAppInfo(GoogleChromeManagementV1ChromeAppInfo $chromeAppInfo)
  {
    $this->chromeAppInfo = $chromeAppInfo;
  }
  /**
   * @return GoogleChromeManagementV1ChromeAppInfo
   */
  public function getChromeAppInfo()
  {
    return $this->chromeAppInfo;
  }
  /**
   * Output only. App's description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
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
   * Output only. First published time.
   *
   * @param string $firstPublishTime
   */
  public function setFirstPublishTime($firstPublishTime)
  {
    $this->firstPublishTime = $firstPublishTime;
  }
  /**
   * @return string
   */
  public function getFirstPublishTime()
  {
    return $this->firstPublishTime;
  }
  /**
   * Output only. Home page or Website uri.
   *
   * @param string $homepageUri
   */
  public function setHomepageUri($homepageUri)
  {
    $this->homepageUri = $homepageUri;
  }
  /**
   * @return string
   */
  public function getHomepageUri()
  {
    return $this->homepageUri;
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
   * Output only. Indicates if the app has to be paid for OR has paid content.
   *
   * @param bool $isPaidApp
   */
  public function setIsPaidApp($isPaidApp)
  {
    $this->isPaidApp = $isPaidApp;
  }
  /**
   * @return bool
   */
  public function getIsPaidApp()
  {
    return $this->isPaidApp;
  }
  /**
   * Output only. Latest published time.
   *
   * @param string $latestPublishTime
   */
  public function setLatestPublishTime($latestPublishTime)
  {
    $this->latestPublishTime = $latestPublishTime;
  }
  /**
   * @return string
   */
  public function getLatestPublishTime()
  {
    return $this->latestPublishTime;
  }
  /**
   * Output only. Format:
   * name=customers/{customer_id}/apps/{chrome|android|web}/{app_id}@{version}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The URI pointing to the privacy policy of the app, if it was
   * provided by the developer. Version-specific field that will only be set
   * when the requested app version is found.
   *
   * @param string $privacyPolicyUri
   */
  public function setPrivacyPolicyUri($privacyPolicyUri)
  {
    $this->privacyPolicyUri = $privacyPolicyUri;
  }
  /**
   * @return string
   */
  public function getPrivacyPolicyUri()
  {
    return $this->privacyPolicyUri;
  }
  /**
   * Output only. The publisher of the item.
   *
   * @param string $publisher
   */
  public function setPublisher($publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return string
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
  /**
   * Output only. Number of reviews received. Chrome Web Store review
   * information will always be for the latest version of an app.
   *
   * @param string $reviewNumber
   */
  public function setReviewNumber($reviewNumber)
  {
    $this->reviewNumber = $reviewNumber;
  }
  /**
   * @return string
   */
  public function getReviewNumber()
  {
    return $this->reviewNumber;
  }
  /**
   * Output only. The rating of the app (on 5 stars). Chrome Web Store review
   * information will always be for the latest version of an app.
   *
   * @param float $reviewRating
   */
  public function setReviewRating($reviewRating)
  {
    $this->reviewRating = $reviewRating;
  }
  /**
   * @return float
   */
  public function getReviewRating()
  {
    return $this->reviewRating;
  }
  /**
   * Output only. App version. A new revision is committed whenever a new
   * version of the app is published.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. Information about a partial service error if applicable.
   *
   * @param GoogleRpcStatus $serviceError
   */
  public function setServiceError(GoogleRpcStatus $serviceError)
  {
    $this->serviceError = $serviceError;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getServiceError()
  {
    return $this->serviceError;
  }
  /**
   * Output only. App type.
   *
   * Accepted values: APP_ITEM_TYPE_UNSPECIFIED, CHROME, ANDROID, WEB
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1AppDetails::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1AppDetails');
