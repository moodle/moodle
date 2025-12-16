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

namespace Google\Service\AndroidEnterprise;

class Product extends \Google\Collection
{
  public const CONTENT_RATING_ratingUnknown = 'ratingUnknown';
  public const CONTENT_RATING_all = 'all';
  public const CONTENT_RATING_preTeen = 'preTeen';
  public const CONTENT_RATING_teen = 'teen';
  public const CONTENT_RATING_mature = 'mature';
  public const DISTRIBUTION_CHANNEL_publicGoogleHosted = 'publicGoogleHosted';
  public const DISTRIBUTION_CHANNEL_privateGoogleHosted = 'privateGoogleHosted';
  public const DISTRIBUTION_CHANNEL_privateSelfHosted = 'privateSelfHosted';
  /**
   * Unknown pricing, used to denote an approved product that is not generally
   * available.
   */
  public const PRODUCT_PRICING_unknown = 'unknown';
  /**
   * The product is free.
   */
  public const PRODUCT_PRICING_free = 'free';
  /**
   * The product is free, but offers in-app purchases.
   */
  public const PRODUCT_PRICING_freeWithInAppPurchase = 'freeWithInAppPurchase';
  /**
   * The product is paid.
   */
  public const PRODUCT_PRICING_paid = 'paid';
  protected $collection_key = 'screenshotUrls';
  protected $appRestrictionsSchemaType = AppRestrictionsSchema::class;
  protected $appRestrictionsSchemaDataType = '';
  protected $appTracksType = TrackInfo::class;
  protected $appTracksDataType = 'array';
  protected $appVersionType = AppVersion::class;
  protected $appVersionDataType = 'array';
  /**
   * The name of the author of the product (for example, the app developer).
   *
   * @var string
   */
  public $authorName;
  /**
   * The countries which this app is available in.
   *
   * @var string[]
   */
  public $availableCountries;
  /**
   * Deprecated, use appTracks instead.
   *
   * @var string[]
   */
  public $availableTracks;
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
   * A link to the (consumer) Google Play details page for the product.
   *
   * @var string
   */
  public $detailsUrl;
  /**
   * How and to whom the package is made available. The value publicGoogleHosted
   * means that the package is available through the Play store and not
   * restricted to a specific enterprise. The value privateGoogleHosted means
   * that the package is a private app (restricted to an enterprise) but hosted
   * by Google. The value privateSelfHosted means that the package is a private
   * app (restricted to an enterprise) and is privately hosted.
   *
   * @var string
   */
  public $distributionChannel;
  /**
   * Noteworthy features (if any) of this product.
   *
   * @var string[]
   */
  public $features;
  /**
   * The localized full app store description, if available.
   *
   * @var string
   */
  public $fullDescription;
  /**
   * A link to an image that can be used as an icon for the product. This image
   * is suitable for use at up to 512px x 512px.
   *
   * @var string
   */
  public $iconUrl;
  /**
   * The approximate time (within 7 days) the app was last published, expressed
   * in milliseconds since epoch.
   *
   * @var string
   */
  public $lastUpdatedTimestampMillis;
  /**
   * The minimum Android SDK necessary to run the app.
   *
   * @var int
   */
  public $minAndroidSdkVersion;
  protected $permissionsType = ProductPermission::class;
  protected $permissionsDataType = 'array';
  /**
   * A string of the form *app:*. For example, app:com.google.android.gm
   * represents the Gmail app.
   *
   * @var string
   */
  public $productId;
  /**
   * Whether this product is free, free with in-app purchases, or paid. If the
   * pricing is unknown, this means the product is not generally available
   * anymore (even though it might still be available to people who own it).
   *
   * @var string
   */
  public $productPricing;
  /**
   * A description of the recent changes made to the app.
   *
   * @var string
   */
  public $recentChanges;
  /**
   * Deprecated.
   *
   * @var bool
   */
  public $requiresContainerApp;
  /**
   * A list of screenshot links representing the app.
   *
   * @var string[]
   */
  public $screenshotUrls;
  protected $signingCertificateType = ProductSigningCertificate::class;
  protected $signingCertificateDataType = '';
  /**
   * A link to a smaller image that can be used as an icon for the product. This
   * image is suitable for use at up to 128px x 128px.
   *
   * @var string
   */
  public $smallIconUrl;
  /**
   * The name of the product.
   *
   * @var string
   */
  public $title;
  /**
   * A link to the managed Google Play details page for the product, for use by
   * an Enterprise admin.
   *
   * @var string
   */
  public $workDetailsUrl;

  /**
   * The app restriction schema
   *
   * @param AppRestrictionsSchema $appRestrictionsSchema
   */
  public function setAppRestrictionsSchema(AppRestrictionsSchema $appRestrictionsSchema)
  {
    $this->appRestrictionsSchema = $appRestrictionsSchema;
  }
  /**
   * @return AppRestrictionsSchema
   */
  public function getAppRestrictionsSchema()
  {
    return $this->appRestrictionsSchema;
  }
  /**
   * The tracks visible to the enterprise.
   *
   * @param TrackInfo[] $appTracks
   */
  public function setAppTracks($appTracks)
  {
    $this->appTracks = $appTracks;
  }
  /**
   * @return TrackInfo[]
   */
  public function getAppTracks()
  {
    return $this->appTracks;
  }
  /**
   * App versions currently available for this product.
   *
   * @param AppVersion[] $appVersion
   */
  public function setAppVersion($appVersion)
  {
    $this->appVersion = $appVersion;
  }
  /**
   * @return AppVersion[]
   */
  public function getAppVersion()
  {
    return $this->appVersion;
  }
  /**
   * The name of the author of the product (for example, the app developer).
   *
   * @param string $authorName
   */
  public function setAuthorName($authorName)
  {
    $this->authorName = $authorName;
  }
  /**
   * @return string
   */
  public function getAuthorName()
  {
    return $this->authorName;
  }
  /**
   * The countries which this app is available in.
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
   * Deprecated, use appTracks instead.
   *
   * @param string[] $availableTracks
   */
  public function setAvailableTracks($availableTracks)
  {
    $this->availableTracks = $availableTracks;
  }
  /**
   * @return string[]
   */
  public function getAvailableTracks()
  {
    return $this->availableTracks;
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
   * Accepted values: ratingUnknown, all, preTeen, teen, mature
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
   * A link to the (consumer) Google Play details page for the product.
   *
   * @param string $detailsUrl
   */
  public function setDetailsUrl($detailsUrl)
  {
    $this->detailsUrl = $detailsUrl;
  }
  /**
   * @return string
   */
  public function getDetailsUrl()
  {
    return $this->detailsUrl;
  }
  /**
   * How and to whom the package is made available. The value publicGoogleHosted
   * means that the package is available through the Play store and not
   * restricted to a specific enterprise. The value privateGoogleHosted means
   * that the package is a private app (restricted to an enterprise) but hosted
   * by Google. The value privateSelfHosted means that the package is a private
   * app (restricted to an enterprise) and is privately hosted.
   *
   * Accepted values: publicGoogleHosted, privateGoogleHosted, privateSelfHosted
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
   * Noteworthy features (if any) of this product.
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
   * The localized full app store description, if available.
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
   * A link to an image that can be used as an icon for the product. This image
   * is suitable for use at up to 512px x 512px.
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
   * The approximate time (within 7 days) the app was last published, expressed
   * in milliseconds since epoch.
   *
   * @param string $lastUpdatedTimestampMillis
   */
  public function setLastUpdatedTimestampMillis($lastUpdatedTimestampMillis)
  {
    $this->lastUpdatedTimestampMillis = $lastUpdatedTimestampMillis;
  }
  /**
   * @return string
   */
  public function getLastUpdatedTimestampMillis()
  {
    return $this->lastUpdatedTimestampMillis;
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
   * A list of permissions required by the app.
   *
   * @param ProductPermission[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return ProductPermission[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * A string of the form *app:*. For example, app:com.google.android.gm
   * represents the Gmail app.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Whether this product is free, free with in-app purchases, or paid. If the
   * pricing is unknown, this means the product is not generally available
   * anymore (even though it might still be available to people who own it).
   *
   * Accepted values: unknown, free, freeWithInAppPurchase, paid
   *
   * @param self::PRODUCT_PRICING_* $productPricing
   */
  public function setProductPricing($productPricing)
  {
    $this->productPricing = $productPricing;
  }
  /**
   * @return self::PRODUCT_PRICING_*
   */
  public function getProductPricing()
  {
    return $this->productPricing;
  }
  /**
   * A description of the recent changes made to the app.
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
   * Deprecated.
   *
   * @param bool $requiresContainerApp
   */
  public function setRequiresContainerApp($requiresContainerApp)
  {
    $this->requiresContainerApp = $requiresContainerApp;
  }
  /**
   * @return bool
   */
  public function getRequiresContainerApp()
  {
    return $this->requiresContainerApp;
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
   * The certificate used to sign this product.
   *
   * @param ProductSigningCertificate $signingCertificate
   */
  public function setSigningCertificate(ProductSigningCertificate $signingCertificate)
  {
    $this->signingCertificate = $signingCertificate;
  }
  /**
   * @return ProductSigningCertificate
   */
  public function getSigningCertificate()
  {
    return $this->signingCertificate;
  }
  /**
   * A link to a smaller image that can be used as an icon for the product. This
   * image is suitable for use at up to 128px x 128px.
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
   * The name of the product.
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
   * A link to the managed Google Play details page for the product, for use by
   * an Enterprise admin.
   *
   * @param string $workDetailsUrl
   */
  public function setWorkDetailsUrl($workDetailsUrl)
  {
    $this->workDetailsUrl = $workDetailsUrl;
  }
  /**
   * @return string
   */
  public function getWorkDetailsUrl()
  {
    return $this->workDetailsUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Product::class, 'Google_Service_AndroidEnterprise_Product');
