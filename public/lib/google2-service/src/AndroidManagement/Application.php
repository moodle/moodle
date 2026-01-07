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

namespace Google\Service\AndroidManagement;

class Application extends \Google\Collection
{
  /**
   * Unknown pricing, used to denote an approved app that is not generally
   * available.
   */
  public const APP_PRICING_APP_PRICING_UNSPECIFIED = 'APP_PRICING_UNSPECIFIED';
  /**
   * The app is free.
   */
  public const APP_PRICING_FREE = 'FREE';
  /**
   * The app is free, but offers in-app purchases.
   */
  public const APP_PRICING_FREE_WITH_IN_APP_PURCHASE = 'FREE_WITH_IN_APP_PURCHASE';
  /**
   * The app is paid.
   */
  public const APP_PRICING_PAID = 'PAID';
  /**
   * Unspecified.
   */
  public const CONTENT_RATING_CONTENT_RATING_UNSPECIFIED = 'CONTENT_RATING_UNSPECIFIED';
  /**
   * Content suitable for ages 3 and above only.
   */
  public const CONTENT_RATING_THREE_YEARS = 'THREE_YEARS';
  /**
   * Content suitable for ages 7 and above only.
   */
  public const CONTENT_RATING_SEVEN_YEARS = 'SEVEN_YEARS';
  /**
   * Content suitable for ages 12 and above only.
   */
  public const CONTENT_RATING_TWELVE_YEARS = 'TWELVE_YEARS';
  /**
   * Content suitable for ages 16 and above only.
   */
  public const CONTENT_RATING_SIXTEEN_YEARS = 'SIXTEEN_YEARS';
  /**
   * Content suitable for ages 18 and above only.
   */
  public const CONTENT_RATING_EIGHTEEN_YEARS = 'EIGHTEEN_YEARS';
  /**
   * Unspecified.
   */
  public const DISTRIBUTION_CHANNEL_DISTRIBUTION_CHANNEL_UNSPECIFIED = 'DISTRIBUTION_CHANNEL_UNSPECIFIED';
  /**
   * Package is available through the Play store and not restricted to a
   * specific enterprise.
   */
  public const DISTRIBUTION_CHANNEL_PUBLIC_GOOGLE_HOSTED = 'PUBLIC_GOOGLE_HOSTED';
  /**
   * Package is a private app (restricted to an enterprise) but hosted by
   * Google.
   */
  public const DISTRIBUTION_CHANNEL_PRIVATE_GOOGLE_HOSTED = 'PRIVATE_GOOGLE_HOSTED';
  /**
   * Private app (restricted to an enterprise) and is privately hosted.
   */
  public const DISTRIBUTION_CHANNEL_PRIVATE_SELF_HOSTED = 'PRIVATE_SELF_HOSTED';
  protected $collection_key = 'screenshotUrls';
  /**
   * Whether this app is free, free with in-app purchases, or paid. If the
   * pricing is unspecified, this means the app is not generally available
   * anymore (even though it might still be available to people who own it).
   *
   * @var string
   */
  public $appPricing;
  protected $appTracksType = AppTrackInfo::class;
  protected $appTracksDataType = 'array';
  protected $appVersionsType = AppVersion::class;
  protected $appVersionsDataType = 'array';
  /**
   * The name of the author of the apps (for example, the app developer).
   *
   * @var string
   */
  public $author;
  /**
   * The countries which this app is available in as per ISO 3166-1 alpha-2.
   *
   * @var string[]
   */
  public $availableCountries;
  /**
   * The app category (e.g. RACING, SOCIAL, etc.)
   *
   * @var string
   */
  public $category;
  /**
   * The content rating for this app.
   *
   * @var string
   */
  public $contentRating;
  /**
   * The localized promotional description, if available.
   *
   * @var string
   */
  public $description;
  /**
   * How and to whom the package is made available.
   *
   * @var string
   */
  public $distributionChannel;
  /**
   * Noteworthy features (if any) of this app.
   *
   * @var string[]
   */
  public $features;
  /**
   * Full app description, if available.
   *
   * @var string
   */
  public $fullDescription;
  /**
   * A link to an image that can be used as an icon for the app. This image is
   * suitable for use up to a pixel size of 512 x 512.
   *
   * @var string
   */
  public $iconUrl;
  protected $managedPropertiesType = ManagedProperty::class;
  protected $managedPropertiesDataType = 'array';
  /**
   * The minimum Android SDK necessary to run the app.
   *
   * @var int
   */
  public $minAndroidSdkVersion;
  /**
   * The name of the app in the form
   * enterprises/{enterprise}/applications/{package_name}.
   *
   * @var string
   */
  public $name;
  protected $permissionsType = ApplicationPermission::class;
  protected $permissionsDataType = 'array';
  /**
   * A link to the (consumer) Google Play details page for the app.
   *
   * @var string
   */
  public $playStoreUrl;
  /**
   * A localised description of the recent changes made to the app.
   *
   * @var string
   */
  public $recentChanges;
  /**
   * A list of screenshot links representing the app.
   *
   * @var string[]
   */
  public $screenshotUrls;
  /**
   * A link to a smaller image that can be used as an icon for the app. This
   * image is suitable for use up to a pixel size of 128 x 128.
   *
   * @var string
   */
  public $smallIconUrl;
  /**
   * The title of the app. Localized.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. The approximate time (within 7 days) the app was last
   * published.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Whether this app is free, free with in-app purchases, or paid. If the
   * pricing is unspecified, this means the app is not generally available
   * anymore (even though it might still be available to people who own it).
   *
   * Accepted values: APP_PRICING_UNSPECIFIED, FREE, FREE_WITH_IN_APP_PURCHASE,
   * PAID
   *
   * @param self::APP_PRICING_* $appPricing
   */
  public function setAppPricing($appPricing)
  {
    $this->appPricing = $appPricing;
  }
  /**
   * @return self::APP_PRICING_*
   */
  public function getAppPricing()
  {
    return $this->appPricing;
  }
  /**
   * Application tracks visible to the enterprise.
   *
   * @param AppTrackInfo[] $appTracks
   */
  public function setAppTracks($appTracks)
  {
    $this->appTracks = $appTracks;
  }
  /**
   * @return AppTrackInfo[]
   */
  public function getAppTracks()
  {
    return $this->appTracks;
  }
  /**
   * Versions currently available for this app.
   *
   * @param AppVersion[] $appVersions
   */
  public function setAppVersions($appVersions)
  {
    $this->appVersions = $appVersions;
  }
  /**
   * @return AppVersion[]
   */
  public function getAppVersions()
  {
    return $this->appVersions;
  }
  /**
   * The name of the author of the apps (for example, the app developer).
   *
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }
  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * The countries which this app is available in as per ISO 3166-1 alpha-2.
   *
   * @param string[] $availableCountries
   */
  public function setAvailableCountries($availableCountries)
  {
    $this->availableCountries = $availableCountries;
  }
  /**
   * @return string[]
   */
  public function getAvailableCountries()
  {
    return $this->availableCountries;
  }
  /**
   * The app category (e.g. RACING, SOCIAL, etc.)
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The content rating for this app.
   *
   * Accepted values: CONTENT_RATING_UNSPECIFIED, THREE_YEARS, SEVEN_YEARS,
   * TWELVE_YEARS, SIXTEEN_YEARS, EIGHTEEN_YEARS
   *
   * @param self::CONTENT_RATING_* $contentRating
   */
  public function setContentRating($contentRating)
  {
    $this->contentRating = $contentRating;
  }
  /**
   * @return self::CONTENT_RATING_*
   */
  public function getContentRating()
  {
    return $this->contentRating;
  }
  /**
   * The localized promotional description, if available.
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
   * How and to whom the package is made available.
   *
   * Accepted values: DISTRIBUTION_CHANNEL_UNSPECIFIED, PUBLIC_GOOGLE_HOSTED,
   * PRIVATE_GOOGLE_HOSTED, PRIVATE_SELF_HOSTED
   *
   * @param self::DISTRIBUTION_CHANNEL_* $distributionChannel
   */
  public function setDistributionChannel($distributionChannel)
  {
    $this->distributionChannel = $distributionChannel;
  }
  /**
   * @return self::DISTRIBUTION_CHANNEL_*
   */
  public function getDistributionChannel()
  {
    return $this->distributionChannel;
  }
  /**
   * Noteworthy features (if any) of this app.
   *
   * @param string[] $features
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return string[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * Full app description, if available.
   *
   * @param string $fullDescription
   */
  public function setFullDescription($fullDescription)
  {
    $this->fullDescription = $fullDescription;
  }
  /**
   * @return string
   */
  public function getFullDescription()
  {
    return $this->fullDescription;
  }
  /**
   * A link to an image that can be used as an icon for the app. This image is
   * suitable for use up to a pixel size of 512 x 512.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * The set of managed properties available to be pre-configured for the app.
   *
   * @param ManagedProperty[] $managedProperties
   */
  public function setManagedProperties($managedProperties)
  {
    $this->managedProperties = $managedProperties;
  }
  /**
   * @return ManagedProperty[]
   */
  public function getManagedProperties()
  {
    return $this->managedProperties;
  }
  /**
   * The minimum Android SDK necessary to run the app.
   *
   * @param int $minAndroidSdkVersion
   */
  public function setMinAndroidSdkVersion($minAndroidSdkVersion)
  {
    $this->minAndroidSdkVersion = $minAndroidSdkVersion;
  }
  /**
   * @return int
   */
  public function getMinAndroidSdkVersion()
  {
    return $this->minAndroidSdkVersion;
  }
  /**
   * The name of the app in the form
   * enterprises/{enterprise}/applications/{package_name}.
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
   * The permissions required by the app.
   *
   * @param ApplicationPermission[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return ApplicationPermission[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * A link to the (consumer) Google Play details page for the app.
   *
   * @param string $playStoreUrl
   */
  public function setPlayStoreUrl($playStoreUrl)
  {
    $this->playStoreUrl = $playStoreUrl;
  }
  /**
   * @return string
   */
  public function getPlayStoreUrl()
  {
    return $this->playStoreUrl;
  }
  /**
   * A localised description of the recent changes made to the app.
   *
   * @param string $recentChanges
   */
  public function setRecentChanges($recentChanges)
  {
    $this->recentChanges = $recentChanges;
  }
  /**
   * @return string
   */
  public function getRecentChanges()
  {
    return $this->recentChanges;
  }
  /**
   * A list of screenshot links representing the app.
   *
   * @param string[] $screenshotUrls
   */
  public function setScreenshotUrls($screenshotUrls)
  {
    $this->screenshotUrls = $screenshotUrls;
  }
  /**
   * @return string[]
   */
  public function getScreenshotUrls()
  {
    return $this->screenshotUrls;
  }
  /**
   * A link to a smaller image that can be used as an icon for the app. This
   * image is suitable for use up to a pixel size of 128 x 128.
   *
   * @param string $smallIconUrl
   */
  public function setSmallIconUrl($smallIconUrl)
  {
    $this->smallIconUrl = $smallIconUrl;
  }
  /**
   * @return string
   */
  public function getSmallIconUrl()
  {
    return $this->smallIconUrl;
  }
  /**
   * The title of the app. Localized.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Output only. The approximate time (within 7 days) the app was last
   * published.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Application::class, 'Google_Service_AndroidManagement_Application');
