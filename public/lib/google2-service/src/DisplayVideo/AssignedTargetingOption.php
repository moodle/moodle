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

namespace Google\Service\DisplayVideo;

class AssignedTargetingOption extends \Google\Model
{
  /**
   * The inheritance is unspecified or unknown.
   */
  public const INHERITANCE_INHERITANCE_UNSPECIFIED = 'INHERITANCE_UNSPECIFIED';
  /**
   * The assigned targeting option is not inherited from higher level entity.
   */
  public const INHERITANCE_NOT_INHERITED = 'NOT_INHERITED';
  /**
   * The assigned targeting option is inherited from partner targeting settings.
   */
  public const INHERITANCE_INHERITED_FROM_PARTNER = 'INHERITED_FROM_PARTNER';
  /**
   * The assigned targeting option is inherited from advertiser targeting
   * settings.
   */
  public const INHERITANCE_INHERITED_FROM_ADVERTISER = 'INHERITED_FROM_ADVERTISER';
  /**
   * Default value when type is not specified or is unknown in this version.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_UNSPECIFIED = 'TARGETING_TYPE_UNSPECIFIED';
  /**
   * Target a channel (a custom group of related websites or apps).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CHANNEL = 'TARGETING_TYPE_CHANNEL';
  /**
   * Target an app category (for example, education or puzzle games).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_APP_CATEGORY = 'TARGETING_TYPE_APP_CATEGORY';
  /**
   * Target a specific app (for example, Angry Birds).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_APP = 'TARGETING_TYPE_APP';
  /**
   * Target a specific url (for example, quora.com).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_URL = 'TARGETING_TYPE_URL';
  /**
   * Target ads during a chosen time period on a specific day.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DAY_AND_TIME = 'TARGETING_TYPE_DAY_AND_TIME';
  /**
   * Target ads to a specific age range (for example, 18-24).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AGE_RANGE = 'TARGETING_TYPE_AGE_RANGE';
  /**
   * Target ads to the specified regions on a regional location list.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_REGIONAL_LOCATION_LIST = 'TARGETING_TYPE_REGIONAL_LOCATION_LIST';
  /**
   * Target ads to the specified points of interest on a proximity location
   * list.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_PROXIMITY_LOCATION_LIST = 'TARGETING_TYPE_PROXIMITY_LOCATION_LIST';
  /**
   * Target ads to a specific gender (for example, female or male).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_GENDER = 'TARGETING_TYPE_GENDER';
  /**
   * Target a specific video player size for video ads.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_VIDEO_PLAYER_SIZE = 'TARGETING_TYPE_VIDEO_PLAYER_SIZE';
  /**
   * Target user rewarded content for video ads.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_USER_REWARDED_CONTENT = 'TARGETING_TYPE_USER_REWARDED_CONTENT';
  /**
   * Target ads to a specific parental status (for example, parent or not a
   * parent).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_PARENTAL_STATUS = 'TARGETING_TYPE_PARENTAL_STATUS';
  /**
   * Target video or audio ads in a specific content instream position (for
   * example, pre-roll, mid-roll, or post-roll).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_INSTREAM_POSITION = 'TARGETING_TYPE_CONTENT_INSTREAM_POSITION';
  /**
   * Target ads in a specific content outstream position.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION = 'TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION';
  /**
   * Target ads to a specific device type (for example, tablet or connected TV).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DEVICE_TYPE = 'TARGETING_TYPE_DEVICE_TYPE';
  /**
   * Target ads to an audience or groups of audiences. Singleton field, at most
   * one can exist on a single Lineitem at a time.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AUDIENCE_GROUP = 'TARGETING_TYPE_AUDIENCE_GROUP';
  /**
   * Target ads to specific web browsers (for example, Chrome).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_BROWSER = 'TARGETING_TYPE_BROWSER';
  /**
   * Target ads to a specific household income range (for example, top 10%).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_HOUSEHOLD_INCOME = 'TARGETING_TYPE_HOUSEHOLD_INCOME';
  /**
   * Target ads in a specific on screen position.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_ON_SCREEN_POSITION = 'TARGETING_TYPE_ON_SCREEN_POSITION';
  /**
   * Filter web sites through third party verification (for example, IAS or
   * DoubleVerify).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_THIRD_PARTY_VERIFIER = 'TARGETING_TYPE_THIRD_PARTY_VERIFIER';
  /**
   * Filter web sites by specific digital content label ratings (for example,
   * DL-MA: suitable only for mature audiences).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION = 'TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION';
  /**
   * Filter website content by sensitive categories (for example, adult).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION = 'TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION';
  /**
   * Target ads to a specific environment (for example, web or app).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_ENVIRONMENT = 'TARGETING_TYPE_ENVIRONMENT';
  /**
   * Target ads to a specific network carrier or internet service provider (ISP)
   * (for example, Comcast or Orange).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CARRIER_AND_ISP = 'TARGETING_TYPE_CARRIER_AND_ISP';
  /**
   * Target ads to a specific operating system (for example, macOS).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_OPERATING_SYSTEM = 'TARGETING_TYPE_OPERATING_SYSTEM';
  /**
   * Target ads to a specific device make or model (for example, Roku or
   * Samsung).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_DEVICE_MAKE_MODEL = 'TARGETING_TYPE_DEVICE_MAKE_MODEL';
  /**
   * Target ads to a specific keyword (for example, dog or retriever).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_KEYWORD = 'TARGETING_TYPE_KEYWORD';
  /**
   * Target ads to a specific negative keyword list.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_NEGATIVE_KEYWORD_LIST = 'TARGETING_TYPE_NEGATIVE_KEYWORD_LIST';
  /**
   * Target ads to a specific viewability (for example, 80% viewable).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_VIEWABILITY = 'TARGETING_TYPE_VIEWABILITY';
  /**
   * Target ads to a specific content category (for example, arts &
   * entertainment).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CATEGORY = 'TARGETING_TYPE_CATEGORY';
  /**
   * Purchase impressions from specific deals and auction packages.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_INVENTORY_SOURCE = 'TARGETING_TYPE_INVENTORY_SOURCE';
  /**
   * Target ads to a specific language (for example, English or Japanese).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_LANGUAGE = 'TARGETING_TYPE_LANGUAGE';
  /**
   * Target ads to ads.txt authorized sellers. If no targeting option of this
   * type is assigned, the resource uses the "Authorized Direct Sellers and
   * Resellers" option by default.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AUTHORIZED_SELLER_STATUS = 'TARGETING_TYPE_AUTHORIZED_SELLER_STATUS';
  /**
   * Target ads to a specific regional location (for example, a city or state).
   */
  public const TARGETING_TYPE_TARGETING_TYPE_GEO_REGION = 'TARGETING_TYPE_GEO_REGION';
  /**
   * Purchase impressions from a group of deals and auction packages.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_INVENTORY_SOURCE_GROUP = 'TARGETING_TYPE_INVENTORY_SOURCE_GROUP';
  /**
   * Purchase impressions from specific exchanges.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_EXCHANGE = 'TARGETING_TYPE_EXCHANGE';
  /**
   * Purchase impressions from specific sub-exchanges.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_SUB_EXCHANGE = 'TARGETING_TYPE_SUB_EXCHANGE';
  /**
   * Target ads around a specific point of interest, such as a notable building,
   * a street address, or latitude/longitude coordinates.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_POI = 'TARGETING_TYPE_POI';
  /**
   * Target ads around locations of a business chain within a specific geo
   * region.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_BUSINESS_CHAIN = 'TARGETING_TYPE_BUSINESS_CHAIN';
  /**
   * Target ads to a specific video content duration.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_DURATION = 'TARGETING_TYPE_CONTENT_DURATION';
  /**
   * Target ads to a specific video content stream type.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_STREAM_TYPE = 'TARGETING_TYPE_CONTENT_STREAM_TYPE';
  /**
   * Target ads to a specific native content position.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_NATIVE_CONTENT_POSITION = 'TARGETING_TYPE_NATIVE_CONTENT_POSITION';
  /**
   * Target ads in an Open Measurement enabled inventory.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_OMID = 'TARGETING_TYPE_OMID';
  /**
   * Target ads to a specific audio content type.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_AUDIO_CONTENT_TYPE = 'TARGETING_TYPE_AUDIO_CONTENT_TYPE';
  /**
   * Target ads to a specific content genre.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_GENRE = 'TARGETING_TYPE_CONTENT_GENRE';
  /**
   * Target ads to a specific YouTube video. Targeting of this type cannot be
   * created or updated using the API. Although this targeting is inherited by
   * child resources, **inherited targeting of this type will not be
   * retrieveable**.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_YOUTUBE_VIDEO = 'TARGETING_TYPE_YOUTUBE_VIDEO';
  /**
   * Target ads to a specific YouTube channel. Targeting of this type cannot be
   * created or updated using the API. Although this targeting is inherited by
   * child resources, **inherited targeting of this type will not be
   * retrieveable**.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_YOUTUBE_CHANNEL = 'TARGETING_TYPE_YOUTUBE_CHANNEL';
  /**
   * Target ads to a serve it in a certain position of a session. Only supported
   * for Ad Group resources under YouTube Programmatic Reservation line items.
   * Targeting of this type cannot be created or updated using the API.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_SESSION_POSITION = 'TARGETING_TYPE_SESSION_POSITION';
  /**
   * Filter website content by content themes (for example, religion). Only
   * supported for Advertiser resources. Targeting of this type cannot be
   * created or updated using the API. This targeting is only inherited by child
   * YouTube and Demand Gen line item resources.
   */
  public const TARGETING_TYPE_TARGETING_TYPE_CONTENT_THEME_EXCLUSION = 'TARGETING_TYPE_CONTENT_THEME_EXCLUSION';
  protected $ageRangeDetailsType = AgeRangeAssignedTargetingOptionDetails::class;
  protected $ageRangeDetailsDataType = '';
  protected $appCategoryDetailsType = AppCategoryAssignedTargetingOptionDetails::class;
  protected $appCategoryDetailsDataType = '';
  protected $appDetailsType = AppAssignedTargetingOptionDetails::class;
  protected $appDetailsDataType = '';
  /**
   * Output only. The unique ID of the assigned targeting option. The ID is only
   * unique within a given resource and targeting type. It may be reused in
   * other contexts.
   *
   * @var string
   */
  public $assignedTargetingOptionId;
  /**
   * Output only. An alias for the assigned_targeting_option_id. This value can
   * be used in place of `assignedTargetingOptionId` when retrieving or deleting
   * existing targeting. This field will only be supported for all assigned
   * targeting options of the following targeting types: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_DEVICE_TYPE` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_EXCHANGE` *
   * `TARGETING_TYPE_GENDER` * `TARGETING_TYPE_HOUSEHOLD_INCOME` *
   * `TARGETING_TYPE_NATIVE_CONTENT_POSITION` * `TARGETING_TYPE_OMID` *
   * `TARGETING_TYPE_PARENTAL_STATUS` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` *
   * `TARGETING_TYPE_VIDEO_PLAYER_SIZE` * `TARGETING_TYPE_VIEWABILITY` This
   * field is also supported for line item assigned targeting options of the
   * following targeting types: * `TARGETING_TYPE_CONTENT_INSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION`
   *
   * @var string
   */
  public $assignedTargetingOptionIdAlias;
  protected $audienceGroupDetailsType = AudienceGroupAssignedTargetingOptionDetails::class;
  protected $audienceGroupDetailsDataType = '';
  protected $audioContentTypeDetailsType = AudioContentTypeAssignedTargetingOptionDetails::class;
  protected $audioContentTypeDetailsDataType = '';
  protected $authorizedSellerStatusDetailsType = AuthorizedSellerStatusAssignedTargetingOptionDetails::class;
  protected $authorizedSellerStatusDetailsDataType = '';
  protected $browserDetailsType = BrowserAssignedTargetingOptionDetails::class;
  protected $browserDetailsDataType = '';
  protected $businessChainDetailsType = BusinessChainAssignedTargetingOptionDetails::class;
  protected $businessChainDetailsDataType = '';
  protected $carrierAndIspDetailsType = CarrierAndIspAssignedTargetingOptionDetails::class;
  protected $carrierAndIspDetailsDataType = '';
  protected $categoryDetailsType = CategoryAssignedTargetingOptionDetails::class;
  protected $categoryDetailsDataType = '';
  protected $channelDetailsType = ChannelAssignedTargetingOptionDetails::class;
  protected $channelDetailsDataType = '';
  protected $contentDurationDetailsType = ContentDurationAssignedTargetingOptionDetails::class;
  protected $contentDurationDetailsDataType = '';
  protected $contentGenreDetailsType = ContentGenreAssignedTargetingOptionDetails::class;
  protected $contentGenreDetailsDataType = '';
  protected $contentInstreamPositionDetailsType = ContentInstreamPositionAssignedTargetingOptionDetails::class;
  protected $contentInstreamPositionDetailsDataType = '';
  protected $contentOutstreamPositionDetailsType = ContentOutstreamPositionAssignedTargetingOptionDetails::class;
  protected $contentOutstreamPositionDetailsDataType = '';
  protected $contentStreamTypeDetailsType = ContentStreamTypeAssignedTargetingOptionDetails::class;
  protected $contentStreamTypeDetailsDataType = '';
  protected $contentThemeExclusionDetailsType = ContentThemeAssignedTargetingOptionDetails::class;
  protected $contentThemeExclusionDetailsDataType = '';
  protected $dayAndTimeDetailsType = DayAndTimeAssignedTargetingOptionDetails::class;
  protected $dayAndTimeDetailsDataType = '';
  protected $deviceMakeModelDetailsType = DeviceMakeModelAssignedTargetingOptionDetails::class;
  protected $deviceMakeModelDetailsDataType = '';
  protected $deviceTypeDetailsType = DeviceTypeAssignedTargetingOptionDetails::class;
  protected $deviceTypeDetailsDataType = '';
  protected $digitalContentLabelExclusionDetailsType = DigitalContentLabelAssignedTargetingOptionDetails::class;
  protected $digitalContentLabelExclusionDetailsDataType = '';
  protected $environmentDetailsType = EnvironmentAssignedTargetingOptionDetails::class;
  protected $environmentDetailsDataType = '';
  protected $exchangeDetailsType = ExchangeAssignedTargetingOptionDetails::class;
  protected $exchangeDetailsDataType = '';
  protected $genderDetailsType = GenderAssignedTargetingOptionDetails::class;
  protected $genderDetailsDataType = '';
  protected $geoRegionDetailsType = GeoRegionAssignedTargetingOptionDetails::class;
  protected $geoRegionDetailsDataType = '';
  protected $householdIncomeDetailsType = HouseholdIncomeAssignedTargetingOptionDetails::class;
  protected $householdIncomeDetailsDataType = '';
  /**
   * Output only. The inheritance status of the assigned targeting option.
   *
   * @var string
   */
  public $inheritance;
  protected $inventorySourceDetailsType = InventorySourceAssignedTargetingOptionDetails::class;
  protected $inventorySourceDetailsDataType = '';
  protected $inventorySourceGroupDetailsType = InventorySourceGroupAssignedTargetingOptionDetails::class;
  protected $inventorySourceGroupDetailsDataType = '';
  protected $keywordDetailsType = KeywordAssignedTargetingOptionDetails::class;
  protected $keywordDetailsDataType = '';
  protected $languageDetailsType = LanguageAssignedTargetingOptionDetails::class;
  protected $languageDetailsDataType = '';
  /**
   * Output only. The resource name for this assigned targeting option.
   *
   * @var string
   */
  public $name;
  protected $nativeContentPositionDetailsType = NativeContentPositionAssignedTargetingOptionDetails::class;
  protected $nativeContentPositionDetailsDataType = '';
  protected $negativeKeywordListDetailsType = NegativeKeywordListAssignedTargetingOptionDetails::class;
  protected $negativeKeywordListDetailsDataType = '';
  protected $omidDetailsType = OmidAssignedTargetingOptionDetails::class;
  protected $omidDetailsDataType = '';
  protected $onScreenPositionDetailsType = OnScreenPositionAssignedTargetingOptionDetails::class;
  protected $onScreenPositionDetailsDataType = '';
  protected $operatingSystemDetailsType = OperatingSystemAssignedTargetingOptionDetails::class;
  protected $operatingSystemDetailsDataType = '';
  protected $parentalStatusDetailsType = ParentalStatusAssignedTargetingOptionDetails::class;
  protected $parentalStatusDetailsDataType = '';
  protected $poiDetailsType = PoiAssignedTargetingOptionDetails::class;
  protected $poiDetailsDataType = '';
  protected $proximityLocationListDetailsType = ProximityLocationListAssignedTargetingOptionDetails::class;
  protected $proximityLocationListDetailsDataType = '';
  protected $regionalLocationListDetailsType = RegionalLocationListAssignedTargetingOptionDetails::class;
  protected $regionalLocationListDetailsDataType = '';
  protected $sensitiveCategoryExclusionDetailsType = SensitiveCategoryAssignedTargetingOptionDetails::class;
  protected $sensitiveCategoryExclusionDetailsDataType = '';
  protected $sessionPositionDetailsType = SessionPositionAssignedTargetingOptionDetails::class;
  protected $sessionPositionDetailsDataType = '';
  protected $subExchangeDetailsType = SubExchangeAssignedTargetingOptionDetails::class;
  protected $subExchangeDetailsDataType = '';
  /**
   * Output only. Identifies the type of this assigned targeting option.
   *
   * @var string
   */
  public $targetingType;
  protected $thirdPartyVerifierDetailsType = ThirdPartyVerifierAssignedTargetingOptionDetails::class;
  protected $thirdPartyVerifierDetailsDataType = '';
  protected $urlDetailsType = UrlAssignedTargetingOptionDetails::class;
  protected $urlDetailsDataType = '';
  protected $userRewardedContentDetailsType = UserRewardedContentAssignedTargetingOptionDetails::class;
  protected $userRewardedContentDetailsDataType = '';
  protected $videoPlayerSizeDetailsType = VideoPlayerSizeAssignedTargetingOptionDetails::class;
  protected $videoPlayerSizeDetailsDataType = '';
  protected $viewabilityDetailsType = ViewabilityAssignedTargetingOptionDetails::class;
  protected $viewabilityDetailsDataType = '';
  protected $youtubeChannelDetailsType = YoutubeChannelAssignedTargetingOptionDetails::class;
  protected $youtubeChannelDetailsDataType = '';
  protected $youtubeVideoDetailsType = YoutubeVideoAssignedTargetingOptionDetails::class;
  protected $youtubeVideoDetailsDataType = '';

  /**
   * Age range details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_AGE_RANGE`.
   *
   * @param AgeRangeAssignedTargetingOptionDetails $ageRangeDetails
   */
  public function setAgeRangeDetails(AgeRangeAssignedTargetingOptionDetails $ageRangeDetails)
  {
    $this->ageRangeDetails = $ageRangeDetails;
  }
  /**
   * @return AgeRangeAssignedTargetingOptionDetails
   */
  public function getAgeRangeDetails()
  {
    return $this->ageRangeDetails;
  }
  /**
   * App category details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_APP_CATEGORY`.
   *
   * @param AppCategoryAssignedTargetingOptionDetails $appCategoryDetails
   */
  public function setAppCategoryDetails(AppCategoryAssignedTargetingOptionDetails $appCategoryDetails)
  {
    $this->appCategoryDetails = $appCategoryDetails;
  }
  /**
   * @return AppCategoryAssignedTargetingOptionDetails
   */
  public function getAppCategoryDetails()
  {
    return $this->appCategoryDetails;
  }
  /**
   * App details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_APP`.
   *
   * @param AppAssignedTargetingOptionDetails $appDetails
   */
  public function setAppDetails(AppAssignedTargetingOptionDetails $appDetails)
  {
    $this->appDetails = $appDetails;
  }
  /**
   * @return AppAssignedTargetingOptionDetails
   */
  public function getAppDetails()
  {
    return $this->appDetails;
  }
  /**
   * Output only. The unique ID of the assigned targeting option. The ID is only
   * unique within a given resource and targeting type. It may be reused in
   * other contexts.
   *
   * @param string $assignedTargetingOptionId
   */
  public function setAssignedTargetingOptionId($assignedTargetingOptionId)
  {
    $this->assignedTargetingOptionId = $assignedTargetingOptionId;
  }
  /**
   * @return string
   */
  public function getAssignedTargetingOptionId()
  {
    return $this->assignedTargetingOptionId;
  }
  /**
   * Output only. An alias for the assigned_targeting_option_id. This value can
   * be used in place of `assignedTargetingOptionId` when retrieving or deleting
   * existing targeting. This field will only be supported for all assigned
   * targeting options of the following targeting types: *
   * `TARGETING_TYPE_AGE_RANGE` * `TARGETING_TYPE_DEVICE_TYPE` *
   * `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION` *
   * `TARGETING_TYPE_ENVIRONMENT` * `TARGETING_TYPE_EXCHANGE` *
   * `TARGETING_TYPE_GENDER` * `TARGETING_TYPE_HOUSEHOLD_INCOME` *
   * `TARGETING_TYPE_NATIVE_CONTENT_POSITION` * `TARGETING_TYPE_OMID` *
   * `TARGETING_TYPE_PARENTAL_STATUS` *
   * `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION` *
   * `TARGETING_TYPE_VIDEO_PLAYER_SIZE` * `TARGETING_TYPE_VIEWABILITY` This
   * field is also supported for line item assigned targeting options of the
   * following targeting types: * `TARGETING_TYPE_CONTENT_INSTREAM_POSITION` *
   * `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION`
   *
   * @param string $assignedTargetingOptionIdAlias
   */
  public function setAssignedTargetingOptionIdAlias($assignedTargetingOptionIdAlias)
  {
    $this->assignedTargetingOptionIdAlias = $assignedTargetingOptionIdAlias;
  }
  /**
   * @return string
   */
  public function getAssignedTargetingOptionIdAlias()
  {
    return $this->assignedTargetingOptionIdAlias;
  }
  /**
   * Audience targeting details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_AUDIENCE_GROUP`. You can only target one
   * audience group option per resource.
   *
   * @param AudienceGroupAssignedTargetingOptionDetails $audienceGroupDetails
   */
  public function setAudienceGroupDetails(AudienceGroupAssignedTargetingOptionDetails $audienceGroupDetails)
  {
    $this->audienceGroupDetails = $audienceGroupDetails;
  }
  /**
   * @return AudienceGroupAssignedTargetingOptionDetails
   */
  public function getAudienceGroupDetails()
  {
    return $this->audienceGroupDetails;
  }
  /**
   * Audio content type details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_AUDIO_CONTENT_TYPE`.
   *
   * @param AudioContentTypeAssignedTargetingOptionDetails $audioContentTypeDetails
   */
  public function setAudioContentTypeDetails(AudioContentTypeAssignedTargetingOptionDetails $audioContentTypeDetails)
  {
    $this->audioContentTypeDetails = $audioContentTypeDetails;
  }
  /**
   * @return AudioContentTypeAssignedTargetingOptionDetails
   */
  public function getAudioContentTypeDetails()
  {
    return $this->audioContentTypeDetails;
  }
  /**
   * Authorized seller status details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_AUTHORIZED_SELLER_STATUS`. You can only
   * target one authorized seller status option per resource. If a resource
   * doesn't have an authorized seller status option, all authorized sellers
   * indicated as DIRECT or RESELLER in the ads.txt file are targeted by
   * default.
   *
   * @param AuthorizedSellerStatusAssignedTargetingOptionDetails $authorizedSellerStatusDetails
   */
  public function setAuthorizedSellerStatusDetails(AuthorizedSellerStatusAssignedTargetingOptionDetails $authorizedSellerStatusDetails)
  {
    $this->authorizedSellerStatusDetails = $authorizedSellerStatusDetails;
  }
  /**
   * @return AuthorizedSellerStatusAssignedTargetingOptionDetails
   */
  public function getAuthorizedSellerStatusDetails()
  {
    return $this->authorizedSellerStatusDetails;
  }
  /**
   * Browser details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_BROWSER`.
   *
   * @param BrowserAssignedTargetingOptionDetails $browserDetails
   */
  public function setBrowserDetails(BrowserAssignedTargetingOptionDetails $browserDetails)
  {
    $this->browserDetails = $browserDetails;
  }
  /**
   * @return BrowserAssignedTargetingOptionDetails
   */
  public function getBrowserDetails()
  {
    return $this->browserDetails;
  }
  /**
   * Business chain details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_BUSINESS_CHAIN`.
   *
   * @param BusinessChainAssignedTargetingOptionDetails $businessChainDetails
   */
  public function setBusinessChainDetails(BusinessChainAssignedTargetingOptionDetails $businessChainDetails)
  {
    $this->businessChainDetails = $businessChainDetails;
  }
  /**
   * @return BusinessChainAssignedTargetingOptionDetails
   */
  public function getBusinessChainDetails()
  {
    return $this->businessChainDetails;
  }
  /**
   * Carrier and ISP details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_CARRIER_AND_ISP`.
   *
   * @param CarrierAndIspAssignedTargetingOptionDetails $carrierAndIspDetails
   */
  public function setCarrierAndIspDetails(CarrierAndIspAssignedTargetingOptionDetails $carrierAndIspDetails)
  {
    $this->carrierAndIspDetails = $carrierAndIspDetails;
  }
  /**
   * @return CarrierAndIspAssignedTargetingOptionDetails
   */
  public function getCarrierAndIspDetails()
  {
    return $this->carrierAndIspDetails;
  }
  /**
   * Category details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_CATEGORY`. Targeting a category will also target its
   * subcategories. If a category is excluded from targeting and a subcategory
   * is included, the exclusion will take precedence.
   *
   * @param CategoryAssignedTargetingOptionDetails $categoryDetails
   */
  public function setCategoryDetails(CategoryAssignedTargetingOptionDetails $categoryDetails)
  {
    $this->categoryDetails = $categoryDetails;
  }
  /**
   * @return CategoryAssignedTargetingOptionDetails
   */
  public function getCategoryDetails()
  {
    return $this->categoryDetails;
  }
  /**
   * Channel details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_CHANNEL`.
   *
   * @param ChannelAssignedTargetingOptionDetails $channelDetails
   */
  public function setChannelDetails(ChannelAssignedTargetingOptionDetails $channelDetails)
  {
    $this->channelDetails = $channelDetails;
  }
  /**
   * @return ChannelAssignedTargetingOptionDetails
   */
  public function getChannelDetails()
  {
    return $this->channelDetails;
  }
  /**
   * Content duration details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_CONTENT_DURATION`.
   *
   * @param ContentDurationAssignedTargetingOptionDetails $contentDurationDetails
   */
  public function setContentDurationDetails(ContentDurationAssignedTargetingOptionDetails $contentDurationDetails)
  {
    $this->contentDurationDetails = $contentDurationDetails;
  }
  /**
   * @return ContentDurationAssignedTargetingOptionDetails
   */
  public function getContentDurationDetails()
  {
    return $this->contentDurationDetails;
  }
  /**
   * Content genre details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_CONTENT_GENRE`.
   *
   * @param ContentGenreAssignedTargetingOptionDetails $contentGenreDetails
   */
  public function setContentGenreDetails(ContentGenreAssignedTargetingOptionDetails $contentGenreDetails)
  {
    $this->contentGenreDetails = $contentGenreDetails;
  }
  /**
   * @return ContentGenreAssignedTargetingOptionDetails
   */
  public function getContentGenreDetails()
  {
    return $this->contentGenreDetails;
  }
  /**
   * Content instream position details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_CONTENT_INSTREAM_POSITION`.
   *
   * @param ContentInstreamPositionAssignedTargetingOptionDetails $contentInstreamPositionDetails
   */
  public function setContentInstreamPositionDetails(ContentInstreamPositionAssignedTargetingOptionDetails $contentInstreamPositionDetails)
  {
    $this->contentInstreamPositionDetails = $contentInstreamPositionDetails;
  }
  /**
   * @return ContentInstreamPositionAssignedTargetingOptionDetails
   */
  public function getContentInstreamPositionDetails()
  {
    return $this->contentInstreamPositionDetails;
  }
  /**
   * Content outstream position details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION`.
   *
   * @param ContentOutstreamPositionAssignedTargetingOptionDetails $contentOutstreamPositionDetails
   */
  public function setContentOutstreamPositionDetails(ContentOutstreamPositionAssignedTargetingOptionDetails $contentOutstreamPositionDetails)
  {
    $this->contentOutstreamPositionDetails = $contentOutstreamPositionDetails;
  }
  /**
   * @return ContentOutstreamPositionAssignedTargetingOptionDetails
   */
  public function getContentOutstreamPositionDetails()
  {
    return $this->contentOutstreamPositionDetails;
  }
  /**
   * Content duration details. This field will be populated when the
   * TargetingType is `TARGETING_TYPE_CONTENT_STREAM_TYPE`.
   *
   * @param ContentStreamTypeAssignedTargetingOptionDetails $contentStreamTypeDetails
   */
  public function setContentStreamTypeDetails(ContentStreamTypeAssignedTargetingOptionDetails $contentStreamTypeDetails)
  {
    $this->contentStreamTypeDetails = $contentStreamTypeDetails;
  }
  /**
   * @return ContentStreamTypeAssignedTargetingOptionDetails
   */
  public function getContentStreamTypeDetails()
  {
    return $this->contentStreamTypeDetails;
  }
  /**
   * Content theme details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_CONTENT_THEME_EXCLUSION`. Content theme are targeting
   * exclusions. Advertiser level content theme exclusions, if set, are always
   * applied in serving (even though they aren't visible in resource settings).
   * Resource settings can exclude content theme in addition to advertiser
   * exclusions.
   *
   * @param ContentThemeAssignedTargetingOptionDetails $contentThemeExclusionDetails
   */
  public function setContentThemeExclusionDetails(ContentThemeAssignedTargetingOptionDetails $contentThemeExclusionDetails)
  {
    $this->contentThemeExclusionDetails = $contentThemeExclusionDetails;
  }
  /**
   * @return ContentThemeAssignedTargetingOptionDetails
   */
  public function getContentThemeExclusionDetails()
  {
    return $this->contentThemeExclusionDetails;
  }
  /**
   * Day and time details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_DAY_AND_TIME`.
   *
   * @param DayAndTimeAssignedTargetingOptionDetails $dayAndTimeDetails
   */
  public function setDayAndTimeDetails(DayAndTimeAssignedTargetingOptionDetails $dayAndTimeDetails)
  {
    $this->dayAndTimeDetails = $dayAndTimeDetails;
  }
  /**
   * @return DayAndTimeAssignedTargetingOptionDetails
   */
  public function getDayAndTimeDetails()
  {
    return $this->dayAndTimeDetails;
  }
  /**
   * Device make and model details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_DEVICE_MAKE_MODEL`.
   *
   * @param DeviceMakeModelAssignedTargetingOptionDetails $deviceMakeModelDetails
   */
  public function setDeviceMakeModelDetails(DeviceMakeModelAssignedTargetingOptionDetails $deviceMakeModelDetails)
  {
    $this->deviceMakeModelDetails = $deviceMakeModelDetails;
  }
  /**
   * @return DeviceMakeModelAssignedTargetingOptionDetails
   */
  public function getDeviceMakeModelDetails()
  {
    return $this->deviceMakeModelDetails;
  }
  /**
   * Device Type details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_DEVICE_TYPE`.
   *
   * @param DeviceTypeAssignedTargetingOptionDetails $deviceTypeDetails
   */
  public function setDeviceTypeDetails(DeviceTypeAssignedTargetingOptionDetails $deviceTypeDetails)
  {
    $this->deviceTypeDetails = $deviceTypeDetails;
  }
  /**
   * @return DeviceTypeAssignedTargetingOptionDetails
   */
  public function getDeviceTypeDetails()
  {
    return $this->deviceTypeDetails;
  }
  /**
   * Digital content label details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION`. Digital
   * content labels are targeting exclusions. Advertiser level digital content
   * label exclusions, if set, are always applied in serving (even though they
   * aren't visible in resource settings). Resource settings can exclude content
   * labels in addition to advertiser exclusions, but can't override them. A
   * line item won't serve if all the digital content labels are excluded.
   *
   * @param DigitalContentLabelAssignedTargetingOptionDetails $digitalContentLabelExclusionDetails
   */
  public function setDigitalContentLabelExclusionDetails(DigitalContentLabelAssignedTargetingOptionDetails $digitalContentLabelExclusionDetails)
  {
    $this->digitalContentLabelExclusionDetails = $digitalContentLabelExclusionDetails;
  }
  /**
   * @return DigitalContentLabelAssignedTargetingOptionDetails
   */
  public function getDigitalContentLabelExclusionDetails()
  {
    return $this->digitalContentLabelExclusionDetails;
  }
  /**
   * Environment details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_ENVIRONMENT`.
   *
   * @param EnvironmentAssignedTargetingOptionDetails $environmentDetails
   */
  public function setEnvironmentDetails(EnvironmentAssignedTargetingOptionDetails $environmentDetails)
  {
    $this->environmentDetails = $environmentDetails;
  }
  /**
   * @return EnvironmentAssignedTargetingOptionDetails
   */
  public function getEnvironmentDetails()
  {
    return $this->environmentDetails;
  }
  /**
   * Exchange details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_EXCHANGE`.
   *
   * @param ExchangeAssignedTargetingOptionDetails $exchangeDetails
   */
  public function setExchangeDetails(ExchangeAssignedTargetingOptionDetails $exchangeDetails)
  {
    $this->exchangeDetails = $exchangeDetails;
  }
  /**
   * @return ExchangeAssignedTargetingOptionDetails
   */
  public function getExchangeDetails()
  {
    return $this->exchangeDetails;
  }
  /**
   * Gender details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_GENDER`.
   *
   * @param GenderAssignedTargetingOptionDetails $genderDetails
   */
  public function setGenderDetails(GenderAssignedTargetingOptionDetails $genderDetails)
  {
    $this->genderDetails = $genderDetails;
  }
  /**
   * @return GenderAssignedTargetingOptionDetails
   */
  public function getGenderDetails()
  {
    return $this->genderDetails;
  }
  /**
   * Geographic region details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_GEO_REGION`.
   *
   * @param GeoRegionAssignedTargetingOptionDetails $geoRegionDetails
   */
  public function setGeoRegionDetails(GeoRegionAssignedTargetingOptionDetails $geoRegionDetails)
  {
    $this->geoRegionDetails = $geoRegionDetails;
  }
  /**
   * @return GeoRegionAssignedTargetingOptionDetails
   */
  public function getGeoRegionDetails()
  {
    return $this->geoRegionDetails;
  }
  /**
   * Household income details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_HOUSEHOLD_INCOME`.
   *
   * @param HouseholdIncomeAssignedTargetingOptionDetails $householdIncomeDetails
   */
  public function setHouseholdIncomeDetails(HouseholdIncomeAssignedTargetingOptionDetails $householdIncomeDetails)
  {
    $this->householdIncomeDetails = $householdIncomeDetails;
  }
  /**
   * @return HouseholdIncomeAssignedTargetingOptionDetails
   */
  public function getHouseholdIncomeDetails()
  {
    return $this->householdIncomeDetails;
  }
  /**
   * Output only. The inheritance status of the assigned targeting option.
   *
   * Accepted values: INHERITANCE_UNSPECIFIED, NOT_INHERITED,
   * INHERITED_FROM_PARTNER, INHERITED_FROM_ADVERTISER
   *
   * @param self::INHERITANCE_* $inheritance
   */
  public function setInheritance($inheritance)
  {
    $this->inheritance = $inheritance;
  }
  /**
   * @return self::INHERITANCE_*
   */
  public function getInheritance()
  {
    return $this->inheritance;
  }
  /**
   * Inventory source details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_INVENTORY_SOURCE`.
   *
   * @param InventorySourceAssignedTargetingOptionDetails $inventorySourceDetails
   */
  public function setInventorySourceDetails(InventorySourceAssignedTargetingOptionDetails $inventorySourceDetails)
  {
    $this->inventorySourceDetails = $inventorySourceDetails;
  }
  /**
   * @return InventorySourceAssignedTargetingOptionDetails
   */
  public function getInventorySourceDetails()
  {
    return $this->inventorySourceDetails;
  }
  /**
   * Inventory source group details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_INVENTORY_SOURCE_GROUP`.
   *
   * @param InventorySourceGroupAssignedTargetingOptionDetails $inventorySourceGroupDetails
   */
  public function setInventorySourceGroupDetails(InventorySourceGroupAssignedTargetingOptionDetails $inventorySourceGroupDetails)
  {
    $this->inventorySourceGroupDetails = $inventorySourceGroupDetails;
  }
  /**
   * @return InventorySourceGroupAssignedTargetingOptionDetails
   */
  public function getInventorySourceGroupDetails()
  {
    return $this->inventorySourceGroupDetails;
  }
  /**
   * Keyword details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_KEYWORD`. A maximum of 5000 direct negative keywords can be
   * assigned to a resource. No limit on number of positive keywords that can be
   * assigned.
   *
   * @param KeywordAssignedTargetingOptionDetails $keywordDetails
   */
  public function setKeywordDetails(KeywordAssignedTargetingOptionDetails $keywordDetails)
  {
    $this->keywordDetails = $keywordDetails;
  }
  /**
   * @return KeywordAssignedTargetingOptionDetails
   */
  public function getKeywordDetails()
  {
    return $this->keywordDetails;
  }
  /**
   * Language details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_LANGUAGE`.
   *
   * @param LanguageAssignedTargetingOptionDetails $languageDetails
   */
  public function setLanguageDetails(LanguageAssignedTargetingOptionDetails $languageDetails)
  {
    $this->languageDetails = $languageDetails;
  }
  /**
   * @return LanguageAssignedTargetingOptionDetails
   */
  public function getLanguageDetails()
  {
    return $this->languageDetails;
  }
  /**
   * Output only. The resource name for this assigned targeting option.
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
   * Native content position details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_NATIVE_CONTENT_POSITION`.
   *
   * @param NativeContentPositionAssignedTargetingOptionDetails $nativeContentPositionDetails
   */
  public function setNativeContentPositionDetails(NativeContentPositionAssignedTargetingOptionDetails $nativeContentPositionDetails)
  {
    $this->nativeContentPositionDetails = $nativeContentPositionDetails;
  }
  /**
   * @return NativeContentPositionAssignedTargetingOptionDetails
   */
  public function getNativeContentPositionDetails()
  {
    return $this->nativeContentPositionDetails;
  }
  /**
   * Keyword details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_NEGATIVE_KEYWORD_LIST`. A maximum of 4 negative keyword
   * lists can be assigned to a resource.
   *
   * @param NegativeKeywordListAssignedTargetingOptionDetails $negativeKeywordListDetails
   */
  public function setNegativeKeywordListDetails(NegativeKeywordListAssignedTargetingOptionDetails $negativeKeywordListDetails)
  {
    $this->negativeKeywordListDetails = $negativeKeywordListDetails;
  }
  /**
   * @return NegativeKeywordListAssignedTargetingOptionDetails
   */
  public function getNegativeKeywordListDetails()
  {
    return $this->negativeKeywordListDetails;
  }
  /**
   * Open Measurement enabled inventory details. This field will be populated
   * when the targeting_type is `TARGETING_TYPE_OMID`.
   *
   * @param OmidAssignedTargetingOptionDetails $omidDetails
   */
  public function setOmidDetails(OmidAssignedTargetingOptionDetails $omidDetails)
  {
    $this->omidDetails = $omidDetails;
  }
  /**
   * @return OmidAssignedTargetingOptionDetails
   */
  public function getOmidDetails()
  {
    return $this->omidDetails;
  }
  /**
   * On screen position details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_ON_SCREEN_POSITION`.
   *
   * @param OnScreenPositionAssignedTargetingOptionDetails $onScreenPositionDetails
   */
  public function setOnScreenPositionDetails(OnScreenPositionAssignedTargetingOptionDetails $onScreenPositionDetails)
  {
    $this->onScreenPositionDetails = $onScreenPositionDetails;
  }
  /**
   * @return OnScreenPositionAssignedTargetingOptionDetails
   */
  public function getOnScreenPositionDetails()
  {
    return $this->onScreenPositionDetails;
  }
  /**
   * Operating system details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_OPERATING_SYSTEM`.
   *
   * @param OperatingSystemAssignedTargetingOptionDetails $operatingSystemDetails
   */
  public function setOperatingSystemDetails(OperatingSystemAssignedTargetingOptionDetails $operatingSystemDetails)
  {
    $this->operatingSystemDetails = $operatingSystemDetails;
  }
  /**
   * @return OperatingSystemAssignedTargetingOptionDetails
   */
  public function getOperatingSystemDetails()
  {
    return $this->operatingSystemDetails;
  }
  /**
   * Parental status details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_PARENTAL_STATUS`.
   *
   * @param ParentalStatusAssignedTargetingOptionDetails $parentalStatusDetails
   */
  public function setParentalStatusDetails(ParentalStatusAssignedTargetingOptionDetails $parentalStatusDetails)
  {
    $this->parentalStatusDetails = $parentalStatusDetails;
  }
  /**
   * @return ParentalStatusAssignedTargetingOptionDetails
   */
  public function getParentalStatusDetails()
  {
    return $this->parentalStatusDetails;
  }
  /**
   * POI details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_POI`.
   *
   * @param PoiAssignedTargetingOptionDetails $poiDetails
   */
  public function setPoiDetails(PoiAssignedTargetingOptionDetails $poiDetails)
  {
    $this->poiDetails = $poiDetails;
  }
  /**
   * @return PoiAssignedTargetingOptionDetails
   */
  public function getPoiDetails()
  {
    return $this->poiDetails;
  }
  /**
   * Proximity location list details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_PROXIMITY_LOCATION_LIST`.
   *
   * @param ProximityLocationListAssignedTargetingOptionDetails $proximityLocationListDetails
   */
  public function setProximityLocationListDetails(ProximityLocationListAssignedTargetingOptionDetails $proximityLocationListDetails)
  {
    $this->proximityLocationListDetails = $proximityLocationListDetails;
  }
  /**
   * @return ProximityLocationListAssignedTargetingOptionDetails
   */
  public function getProximityLocationListDetails()
  {
    return $this->proximityLocationListDetails;
  }
  /**
   * Regional location list details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_REGIONAL_LOCATION_LIST`.
   *
   * @param RegionalLocationListAssignedTargetingOptionDetails $regionalLocationListDetails
   */
  public function setRegionalLocationListDetails(RegionalLocationListAssignedTargetingOptionDetails $regionalLocationListDetails)
  {
    $this->regionalLocationListDetails = $regionalLocationListDetails;
  }
  /**
   * @return RegionalLocationListAssignedTargetingOptionDetails
   */
  public function getRegionalLocationListDetails()
  {
    return $this->regionalLocationListDetails;
  }
  /**
   * Sensitive category details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION`. Sensitive
   * categories are targeting exclusions. Advertiser level sensitive category
   * exclusions, if set, are always applied in serving (even though they aren't
   * visible in resource settings). Resource settings can exclude sensitive
   * categories in addition to advertiser exclusions, but can't override them.
   *
   * @param SensitiveCategoryAssignedTargetingOptionDetails $sensitiveCategoryExclusionDetails
   */
  public function setSensitiveCategoryExclusionDetails(SensitiveCategoryAssignedTargetingOptionDetails $sensitiveCategoryExclusionDetails)
  {
    $this->sensitiveCategoryExclusionDetails = $sensitiveCategoryExclusionDetails;
  }
  /**
   * @return SensitiveCategoryAssignedTargetingOptionDetails
   */
  public function getSensitiveCategoryExclusionDetails()
  {
    return $this->sensitiveCategoryExclusionDetails;
  }
  /**
   * Session position details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_SESSION_POSITION`.
   *
   * @param SessionPositionAssignedTargetingOptionDetails $sessionPositionDetails
   */
  public function setSessionPositionDetails(SessionPositionAssignedTargetingOptionDetails $sessionPositionDetails)
  {
    $this->sessionPositionDetails = $sessionPositionDetails;
  }
  /**
   * @return SessionPositionAssignedTargetingOptionDetails
   */
  public function getSessionPositionDetails()
  {
    return $this->sessionPositionDetails;
  }
  /**
   * Sub-exchange details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_SUB_EXCHANGE`.
   *
   * @param SubExchangeAssignedTargetingOptionDetails $subExchangeDetails
   */
  public function setSubExchangeDetails(SubExchangeAssignedTargetingOptionDetails $subExchangeDetails)
  {
    $this->subExchangeDetails = $subExchangeDetails;
  }
  /**
   * @return SubExchangeAssignedTargetingOptionDetails
   */
  public function getSubExchangeDetails()
  {
    return $this->subExchangeDetails;
  }
  /**
   * Output only. Identifies the type of this assigned targeting option.
   *
   * Accepted values: TARGETING_TYPE_UNSPECIFIED, TARGETING_TYPE_CHANNEL,
   * TARGETING_TYPE_APP_CATEGORY, TARGETING_TYPE_APP, TARGETING_TYPE_URL,
   * TARGETING_TYPE_DAY_AND_TIME, TARGETING_TYPE_AGE_RANGE,
   * TARGETING_TYPE_REGIONAL_LOCATION_LIST,
   * TARGETING_TYPE_PROXIMITY_LOCATION_LIST, TARGETING_TYPE_GENDER,
   * TARGETING_TYPE_VIDEO_PLAYER_SIZE, TARGETING_TYPE_USER_REWARDED_CONTENT,
   * TARGETING_TYPE_PARENTAL_STATUS, TARGETING_TYPE_CONTENT_INSTREAM_POSITION,
   * TARGETING_TYPE_CONTENT_OUTSTREAM_POSITION, TARGETING_TYPE_DEVICE_TYPE,
   * TARGETING_TYPE_AUDIENCE_GROUP, TARGETING_TYPE_BROWSER,
   * TARGETING_TYPE_HOUSEHOLD_INCOME, TARGETING_TYPE_ON_SCREEN_POSITION,
   * TARGETING_TYPE_THIRD_PARTY_VERIFIER,
   * TARGETING_TYPE_DIGITAL_CONTENT_LABEL_EXCLUSION,
   * TARGETING_TYPE_SENSITIVE_CATEGORY_EXCLUSION, TARGETING_TYPE_ENVIRONMENT,
   * TARGETING_TYPE_CARRIER_AND_ISP, TARGETING_TYPE_OPERATING_SYSTEM,
   * TARGETING_TYPE_DEVICE_MAKE_MODEL, TARGETING_TYPE_KEYWORD,
   * TARGETING_TYPE_NEGATIVE_KEYWORD_LIST, TARGETING_TYPE_VIEWABILITY,
   * TARGETING_TYPE_CATEGORY, TARGETING_TYPE_INVENTORY_SOURCE,
   * TARGETING_TYPE_LANGUAGE, TARGETING_TYPE_AUTHORIZED_SELLER_STATUS,
   * TARGETING_TYPE_GEO_REGION, TARGETING_TYPE_INVENTORY_SOURCE_GROUP,
   * TARGETING_TYPE_EXCHANGE, TARGETING_TYPE_SUB_EXCHANGE, TARGETING_TYPE_POI,
   * TARGETING_TYPE_BUSINESS_CHAIN, TARGETING_TYPE_CONTENT_DURATION,
   * TARGETING_TYPE_CONTENT_STREAM_TYPE, TARGETING_TYPE_NATIVE_CONTENT_POSITION,
   * TARGETING_TYPE_OMID, TARGETING_TYPE_AUDIO_CONTENT_TYPE,
   * TARGETING_TYPE_CONTENT_GENRE, TARGETING_TYPE_YOUTUBE_VIDEO,
   * TARGETING_TYPE_YOUTUBE_CHANNEL, TARGETING_TYPE_SESSION_POSITION,
   * TARGETING_TYPE_CONTENT_THEME_EXCLUSION
   *
   * @param self::TARGETING_TYPE_* $targetingType
   */
  public function setTargetingType($targetingType)
  {
    $this->targetingType = $targetingType;
  }
  /**
   * @return self::TARGETING_TYPE_*
   */
  public function getTargetingType()
  {
    return $this->targetingType;
  }
  /**
   * Third party verification details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_THIRD_PARTY_VERIFIER`.
   *
   * @param ThirdPartyVerifierAssignedTargetingOptionDetails $thirdPartyVerifierDetails
   */
  public function setThirdPartyVerifierDetails(ThirdPartyVerifierAssignedTargetingOptionDetails $thirdPartyVerifierDetails)
  {
    $this->thirdPartyVerifierDetails = $thirdPartyVerifierDetails;
  }
  /**
   * @return ThirdPartyVerifierAssignedTargetingOptionDetails
   */
  public function getThirdPartyVerifierDetails()
  {
    return $this->thirdPartyVerifierDetails;
  }
  /**
   * URL details. This field will be populated when the targeting_type is
   * `TARGETING_TYPE_URL`.
   *
   * @param UrlAssignedTargetingOptionDetails $urlDetails
   */
  public function setUrlDetails(UrlAssignedTargetingOptionDetails $urlDetails)
  {
    $this->urlDetails = $urlDetails;
  }
  /**
   * @return UrlAssignedTargetingOptionDetails
   */
  public function getUrlDetails()
  {
    return $this->urlDetails;
  }
  /**
   * User rewarded content details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_USER_REWARDED_CONTENT`.
   *
   * @param UserRewardedContentAssignedTargetingOptionDetails $userRewardedContentDetails
   */
  public function setUserRewardedContentDetails(UserRewardedContentAssignedTargetingOptionDetails $userRewardedContentDetails)
  {
    $this->userRewardedContentDetails = $userRewardedContentDetails;
  }
  /**
   * @return UserRewardedContentAssignedTargetingOptionDetails
   */
  public function getUserRewardedContentDetails()
  {
    return $this->userRewardedContentDetails;
  }
  /**
   * Video player size details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_VIDEO_PLAYER_SIZE`.
   *
   * @param VideoPlayerSizeAssignedTargetingOptionDetails $videoPlayerSizeDetails
   */
  public function setVideoPlayerSizeDetails(VideoPlayerSizeAssignedTargetingOptionDetails $videoPlayerSizeDetails)
  {
    $this->videoPlayerSizeDetails = $videoPlayerSizeDetails;
  }
  /**
   * @return VideoPlayerSizeAssignedTargetingOptionDetails
   */
  public function getVideoPlayerSizeDetails()
  {
    return $this->videoPlayerSizeDetails;
  }
  /**
   * Viewability details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_VIEWABILITY`. You can only target one viewability option
   * per resource.
   *
   * @param ViewabilityAssignedTargetingOptionDetails $viewabilityDetails
   */
  public function setViewabilityDetails(ViewabilityAssignedTargetingOptionDetails $viewabilityDetails)
  {
    $this->viewabilityDetails = $viewabilityDetails;
  }
  /**
   * @return ViewabilityAssignedTargetingOptionDetails
   */
  public function getViewabilityDetails()
  {
    return $this->viewabilityDetails;
  }
  /**
   * YouTube channel details. This field will be populated when the
   * targeting_type is `TARGETING_TYPE_YOUTUBE_CHANNEL`.
   *
   * @param YoutubeChannelAssignedTargetingOptionDetails $youtubeChannelDetails
   */
  public function setYoutubeChannelDetails(YoutubeChannelAssignedTargetingOptionDetails $youtubeChannelDetails)
  {
    $this->youtubeChannelDetails = $youtubeChannelDetails;
  }
  /**
   * @return YoutubeChannelAssignedTargetingOptionDetails
   */
  public function getYoutubeChannelDetails()
  {
    return $this->youtubeChannelDetails;
  }
  /**
   * YouTube video details. This field will be populated when the targeting_type
   * is `TARGETING_TYPE_YOUTUBE_VIDEO`.
   *
   * @param YoutubeVideoAssignedTargetingOptionDetails $youtubeVideoDetails
   */
  public function setYoutubeVideoDetails(YoutubeVideoAssignedTargetingOptionDetails $youtubeVideoDetails)
  {
    $this->youtubeVideoDetails = $youtubeVideoDetails;
  }
  /**
   * @return YoutubeVideoAssignedTargetingOptionDetails
   */
  public function getYoutubeVideoDetails()
  {
    return $this->youtubeVideoDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssignedTargetingOption::class, 'Google_Service_DisplayVideo_AssignedTargetingOption');
