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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesAdGroupCriterion extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const ENGINE_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ENGINE_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Deprecated. Do not use.
   *
   * @deprecated
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_ELIGIBLE = 'AD_GROUP_CRITERION_ELIGIBLE';
  /**
   * Baidu: Bid or quality too low to be displayed.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_INAPPROPRIATE_FOR_CAMPAIGN = 'AD_GROUP_CRITERION_INAPPROPRIATE_FOR_CAMPAIGN';
  /**
   * Baidu: Bid or quality too low for mobile, but eligible to display for
   * desktop.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_INVALID_MOBILE_SEARCH = 'AD_GROUP_CRITERION_INVALID_MOBILE_SEARCH';
  /**
   * Baidu: Bid or quality too low for desktop, but eligible to display for
   * mobile.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_INVALID_PC_SEARCH = 'AD_GROUP_CRITERION_INVALID_PC_SEARCH';
  /**
   * Baidu: Bid or quality too low to be displayed.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_INVALID_SEARCH = 'AD_GROUP_CRITERION_INVALID_SEARCH';
  /**
   * Baidu: Paused by Baidu due to low search volume.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_LOW_SEARCH_VOLUME = 'AD_GROUP_CRITERION_LOW_SEARCH_VOLUME';
  /**
   * Baidu: Mobile URL in process to be reviewed.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_MOBILE_URL_UNDER_REVIEW = 'AD_GROUP_CRITERION_MOBILE_URL_UNDER_REVIEW';
  /**
   * Baidu: The landing page for one device is invalid, while the landing page
   * for the other device is valid.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_PARTIALLY_INVALID = 'AD_GROUP_CRITERION_PARTIALLY_INVALID';
  /**
   * Baidu: Keyword has been created and paused by Baidu account management, and
   * is now ready for you to activate it.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_TO_BE_ACTIVATED = 'AD_GROUP_CRITERION_TO_BE_ACTIVATED';
  /**
   * Baidu: In process to be reviewed by Baidu. Gemini: Criterion under review.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_UNDER_REVIEW = 'AD_GROUP_CRITERION_UNDER_REVIEW';
  /**
   * Baidu: Criterion to be reviewed.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_NOT_REVIEWED = 'AD_GROUP_CRITERION_NOT_REVIEWED';
  /**
   * Deprecated. Do not use. Previously used by Gemini
   *
   * @deprecated
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_ON_HOLD = 'AD_GROUP_CRITERION_ON_HOLD';
  /**
   * Y!J : Criterion pending review
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_PENDING_REVIEW = 'AD_GROUP_CRITERION_PENDING_REVIEW';
  /**
   * Criterion has been paused.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_PAUSED = 'AD_GROUP_CRITERION_PAUSED';
  /**
   * Criterion has been removed.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_REMOVED = 'AD_GROUP_CRITERION_REMOVED';
  /**
   * Criterion has been approved.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_APPROVED = 'AD_GROUP_CRITERION_APPROVED';
  /**
   * Criterion has been disapproved.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_DISAPPROVED = 'AD_GROUP_CRITERION_DISAPPROVED';
  /**
   * Criterion is active and serving.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_SERVING = 'AD_GROUP_CRITERION_SERVING';
  /**
   * Criterion has been paused since the account is paused.
   */
  public const ENGINE_STATUS_AD_GROUP_CRITERION_ACCOUNT_PAUSED = 'AD_GROUP_CRITERION_ACCOUNT_PAUSED';
  /**
   * No value has been specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The ad group criterion is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The ad group criterion is paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * The ad group criterion is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * Not specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Keyword, for example, 'mars cruise'.
   */
  public const TYPE_KEYWORD = 'KEYWORD';
  /**
   * Placement, also known as Website, for example, 'www.flowers4sale.com'
   */
  public const TYPE_PLACEMENT = 'PLACEMENT';
  /**
   * Mobile application categories to target.
   */
  public const TYPE_MOBILE_APP_CATEGORY = 'MOBILE_APP_CATEGORY';
  /**
   * Mobile applications to target.
   */
  public const TYPE_MOBILE_APPLICATION = 'MOBILE_APPLICATION';
  /**
   * Devices to target.
   */
  public const TYPE_DEVICE = 'DEVICE';
  /**
   * Locations to target.
   */
  public const TYPE_LOCATION = 'LOCATION';
  /**
   * Listing groups to target.
   */
  public const TYPE_LISTING_GROUP = 'LISTING_GROUP';
  /**
   * Ad Schedule.
   */
  public const TYPE_AD_SCHEDULE = 'AD_SCHEDULE';
  /**
   * Age range.
   */
  public const TYPE_AGE_RANGE = 'AGE_RANGE';
  /**
   * Gender.
   */
  public const TYPE_GENDER = 'GENDER';
  /**
   * Income Range.
   */
  public const TYPE_INCOME_RANGE = 'INCOME_RANGE';
  /**
   * Parental status.
   */
  public const TYPE_PARENTAL_STATUS = 'PARENTAL_STATUS';
  /**
   * YouTube Video.
   */
  public const TYPE_YOUTUBE_VIDEO = 'YOUTUBE_VIDEO';
  /**
   * YouTube Channel.
   */
  public const TYPE_YOUTUBE_CHANNEL = 'YOUTUBE_CHANNEL';
  /**
   * User list.
   */
  public const TYPE_USER_LIST = 'USER_LIST';
  /**
   * Proximity.
   */
  public const TYPE_PROXIMITY = 'PROXIMITY';
  /**
   * A topic target on the display network (for example, "Pets & Animals").
   */
  public const TYPE_TOPIC = 'TOPIC';
  /**
   * Listing scope to target.
   */
  public const TYPE_LISTING_SCOPE = 'LISTING_SCOPE';
  /**
   * Language.
   */
  public const TYPE_LANGUAGE = 'LANGUAGE';
  /**
   * IpBlock.
   */
  public const TYPE_IP_BLOCK = 'IP_BLOCK';
  /**
   * Content Label for category exclusion.
   */
  public const TYPE_CONTENT_LABEL = 'CONTENT_LABEL';
  /**
   * Carrier.
   */
  public const TYPE_CARRIER = 'CARRIER';
  /**
   * A category the user is interested in.
   */
  public const TYPE_USER_INTEREST = 'USER_INTEREST';
  /**
   * Webpage criterion for dynamic search ads.
   */
  public const TYPE_WEBPAGE = 'WEBPAGE';
  /**
   * Operating system version.
   */
  public const TYPE_OPERATING_SYSTEM_VERSION = 'OPERATING_SYSTEM_VERSION';
  /**
   * App payment model.
   */
  public const TYPE_APP_PAYMENT_MODEL = 'APP_PAYMENT_MODEL';
  /**
   * Mobile device.
   */
  public const TYPE_MOBILE_DEVICE = 'MOBILE_DEVICE';
  /**
   * Custom affinity.
   */
  public const TYPE_CUSTOM_AFFINITY = 'CUSTOM_AFFINITY';
  /**
   * Custom intent.
   */
  public const TYPE_CUSTOM_INTENT = 'CUSTOM_INTENT';
  /**
   * Location group.
   */
  public const TYPE_LOCATION_GROUP = 'LOCATION_GROUP';
  /**
   * Custom audience
   */
  public const TYPE_CUSTOM_AUDIENCE = 'CUSTOM_AUDIENCE';
  /**
   * Combined audience
   */
  public const TYPE_COMBINED_AUDIENCE = 'COMBINED_AUDIENCE';
  /**
   * Smart Campaign keyword theme
   */
  public const TYPE_KEYWORD_THEME = 'KEYWORD_THEME';
  /**
   * Audience
   */
  public const TYPE_AUDIENCE = 'AUDIENCE';
  /**
   * Local Services Ads Service ID.
   */
  public const TYPE_LOCAL_SERVICE_ID = 'LOCAL_SERVICE_ID';
  /**
   * Brand
   */
  public const TYPE_BRAND = 'BRAND';
  /**
   * Brand List
   */
  public const TYPE_BRAND_LIST = 'BRAND_LIST';
  /**
   * Life Event
   */
  public const TYPE_LIFE_EVENT = 'LIFE_EVENT';
  protected $collection_key = 'urlCustomParameters';
  /**
   * Immutable. The ad group to which the criterion belongs.
   *
   * @var string
   */
  public $adGroup;
  protected $ageRangeType = GoogleAdsSearchads360V0CommonAgeRangeInfo::class;
  protected $ageRangeDataType = '';
  /**
   * The modifier for the bid when the criterion matches. The modifier must be
   * in the range: 0.1 - 10.0. Most targetable criteria types support modifiers.
   *
   * @var 
   */
  public $bidModifier;
  /**
   * The CPC (cost-per-click) bid.
   *
   * @var string
   */
  public $cpcBidMicros;
  /**
   * Output only. The timestamp when this ad group criterion was created. The
   * timestamp is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss"
   * format.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. The ID of the criterion.
   *
   * @var string
   */
  public $criterionId;
  /**
   * Output only. The effective CPC (cost-per-click) bid.
   *
   * @var string
   */
  public $effectiveCpcBidMicros;
  /**
   * Output only. The resource names of effective labels attached to this ad
   * group criterion. An effective label is a label inherited or directly
   * assigned to this ad group criterion.
   *
   * @var string[]
   */
  public $effectiveLabels;
  /**
   * Output only. ID of the ad group criterion in the external engine account.
   * This field is for non-Google Ads account only, for example, Yahoo Japan,
   * Microsoft, Baidu etc. For Google Ads entity, use
   * "ad_group_criterion.criterion_id" instead.
   *
   * @var string
   */
  public $engineId;
  /**
   * Output only. The Engine Status for ad group criterion.
   *
   * @var string
   */
  public $engineStatus;
  /**
   * The list of possible final mobile URLs after all cross-domain redirects.
   *
   * @var string[]
   */
  public $finalMobileUrls;
  /**
   * URL template for appending params to final URL.
   *
   * @var string
   */
  public $finalUrlSuffix;
  /**
   * The list of possible final URLs after all cross-domain redirects for the
   * ad.
   *
   * @var string[]
   */
  public $finalUrls;
  protected $genderType = GoogleAdsSearchads360V0CommonGenderInfo::class;
  protected $genderDataType = '';
  protected $keywordType = GoogleAdsSearchads360V0CommonKeywordInfo::class;
  protected $keywordDataType = '';
  /**
   * Output only. The resource names of labels attached to this ad group
   * criterion.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The datetime when this ad group criterion was last modified.
   * The datetime is in the customer's time zone and in "yyyy-MM-dd
   * HH:mm:ss.ssssss" format.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $listingGroupType = GoogleAdsSearchads360V0CommonListingGroupInfo::class;
  protected $listingGroupDataType = '';
  protected $locationType = GoogleAdsSearchads360V0CommonLocationInfo::class;
  protected $locationDataType = '';
  /**
   * Immutable. Whether to target (`false`) or exclude (`true`) the criterion.
   * This field is immutable. To switch a criterion from positive to negative,
   * remove then re-add it.
   *
   * @var bool
   */
  public $negative;
  protected $positionEstimatesType = GoogleAdsSearchads360V0ResourcesAdGroupCriterionPositionEstimates::class;
  protected $positionEstimatesDataType = '';
  protected $qualityInfoType = GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo::class;
  protected $qualityInfoDataType = '';
  /**
   * Immutable. The resource name of the ad group criterion. Ad group criterion
   * resource names have the form:
   * `customers/{customer_id}/adGroupCriteria/{ad_group_id}~{criterion_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * The status of the criterion. This is the status of the ad group criterion
   * entity, set by the client. Note: UI reports may incorporate additional
   * information that affects whether a criterion is eligible to run. In some
   * cases a criterion that's REMOVED in the API can still show as enabled in
   * the UI. For example, campaigns by default show to users of all age ranges
   * unless excluded. The UI will show each age range as "enabled", since
   * they're eligible to see the ads; but AdGroupCriterion.status will show
   * "removed", since no positive criterion was added.
   *
   * @var string
   */
  public $status;
  /**
   * The URL template for constructing a tracking URL.
   *
   * @var string
   */
  public $trackingUrlTemplate;
  /**
   * Output only. The type of the criterion.
   *
   * @var string
   */
  public $type;
  protected $urlCustomParametersType = GoogleAdsSearchads360V0CommonCustomParameter::class;
  protected $urlCustomParametersDataType = 'array';
  protected $userListType = GoogleAdsSearchads360V0CommonUserListInfo::class;
  protected $userListDataType = '';
  protected $webpageType = GoogleAdsSearchads360V0CommonWebpageInfo::class;
  protected $webpageDataType = '';

  /**
   * Immutable. The ad group to which the criterion belongs.
   *
   * @param string $adGroup
   */
  public function setAdGroup($adGroup)
  {
    $this->adGroup = $adGroup;
  }
  /**
   * @return string
   */
  public function getAdGroup()
  {
    return $this->adGroup;
  }
  /**
   * Immutable. Age range.
   *
   * @param GoogleAdsSearchads360V0CommonAgeRangeInfo $ageRange
   */
  public function setAgeRange(GoogleAdsSearchads360V0CommonAgeRangeInfo $ageRange)
  {
    $this->ageRange = $ageRange;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonAgeRangeInfo
   */
  public function getAgeRange()
  {
    return $this->ageRange;
  }
  public function setBidModifier($bidModifier)
  {
    $this->bidModifier = $bidModifier;
  }
  public function getBidModifier()
  {
    return $this->bidModifier;
  }
  /**
   * The CPC (cost-per-click) bid.
   *
   * @param string $cpcBidMicros
   */
  public function setCpcBidMicros($cpcBidMicros)
  {
    $this->cpcBidMicros = $cpcBidMicros;
  }
  /**
   * @return string
   */
  public function getCpcBidMicros()
  {
    return $this->cpcBidMicros;
  }
  /**
   * Output only. The timestamp when this ad group criterion was created. The
   * timestamp is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss"
   * format.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. The ID of the criterion.
   *
   * @param string $criterionId
   */
  public function setCriterionId($criterionId)
  {
    $this->criterionId = $criterionId;
  }
  /**
   * @return string
   */
  public function getCriterionId()
  {
    return $this->criterionId;
  }
  /**
   * Output only. The effective CPC (cost-per-click) bid.
   *
   * @param string $effectiveCpcBidMicros
   */
  public function setEffectiveCpcBidMicros($effectiveCpcBidMicros)
  {
    $this->effectiveCpcBidMicros = $effectiveCpcBidMicros;
  }
  /**
   * @return string
   */
  public function getEffectiveCpcBidMicros()
  {
    return $this->effectiveCpcBidMicros;
  }
  /**
   * Output only. The resource names of effective labels attached to this ad
   * group criterion. An effective label is a label inherited or directly
   * assigned to this ad group criterion.
   *
   * @param string[] $effectiveLabels
   */
  public function setEffectiveLabels($effectiveLabels)
  {
    $this->effectiveLabels = $effectiveLabels;
  }
  /**
   * @return string[]
   */
  public function getEffectiveLabels()
  {
    return $this->effectiveLabels;
  }
  /**
   * Output only. ID of the ad group criterion in the external engine account.
   * This field is for non-Google Ads account only, for example, Yahoo Japan,
   * Microsoft, Baidu etc. For Google Ads entity, use
   * "ad_group_criterion.criterion_id" instead.
   *
   * @param string $engineId
   */
  public function setEngineId($engineId)
  {
    $this->engineId = $engineId;
  }
  /**
   * @return string
   */
  public function getEngineId()
  {
    return $this->engineId;
  }
  /**
   * Output only. The Engine Status for ad group criterion.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, AD_GROUP_CRITERION_ELIGIBLE,
   * AD_GROUP_CRITERION_INAPPROPRIATE_FOR_CAMPAIGN,
   * AD_GROUP_CRITERION_INVALID_MOBILE_SEARCH,
   * AD_GROUP_CRITERION_INVALID_PC_SEARCH, AD_GROUP_CRITERION_INVALID_SEARCH,
   * AD_GROUP_CRITERION_LOW_SEARCH_VOLUME,
   * AD_GROUP_CRITERION_MOBILE_URL_UNDER_REVIEW,
   * AD_GROUP_CRITERION_PARTIALLY_INVALID, AD_GROUP_CRITERION_TO_BE_ACTIVATED,
   * AD_GROUP_CRITERION_UNDER_REVIEW, AD_GROUP_CRITERION_NOT_REVIEWED,
   * AD_GROUP_CRITERION_ON_HOLD, AD_GROUP_CRITERION_PENDING_REVIEW,
   * AD_GROUP_CRITERION_PAUSED, AD_GROUP_CRITERION_REMOVED,
   * AD_GROUP_CRITERION_APPROVED, AD_GROUP_CRITERION_DISAPPROVED,
   * AD_GROUP_CRITERION_SERVING, AD_GROUP_CRITERION_ACCOUNT_PAUSED
   *
   * @param self::ENGINE_STATUS_* $engineStatus
   */
  public function setEngineStatus($engineStatus)
  {
    $this->engineStatus = $engineStatus;
  }
  /**
   * @return self::ENGINE_STATUS_*
   */
  public function getEngineStatus()
  {
    return $this->engineStatus;
  }
  /**
   * The list of possible final mobile URLs after all cross-domain redirects.
   *
   * @param string[] $finalMobileUrls
   */
  public function setFinalMobileUrls($finalMobileUrls)
  {
    $this->finalMobileUrls = $finalMobileUrls;
  }
  /**
   * @return string[]
   */
  public function getFinalMobileUrls()
  {
    return $this->finalMobileUrls;
  }
  /**
   * URL template for appending params to final URL.
   *
   * @param string $finalUrlSuffix
   */
  public function setFinalUrlSuffix($finalUrlSuffix)
  {
    $this->finalUrlSuffix = $finalUrlSuffix;
  }
  /**
   * @return string
   */
  public function getFinalUrlSuffix()
  {
    return $this->finalUrlSuffix;
  }
  /**
   * The list of possible final URLs after all cross-domain redirects for the
   * ad.
   *
   * @param string[] $finalUrls
   */
  public function setFinalUrls($finalUrls)
  {
    $this->finalUrls = $finalUrls;
  }
  /**
   * @return string[]
   */
  public function getFinalUrls()
  {
    return $this->finalUrls;
  }
  /**
   * Immutable. Gender.
   *
   * @param GoogleAdsSearchads360V0CommonGenderInfo $gender
   */
  public function setGender(GoogleAdsSearchads360V0CommonGenderInfo $gender)
  {
    $this->gender = $gender;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonGenderInfo
   */
  public function getGender()
  {
    return $this->gender;
  }
  /**
   * Immutable. Keyword.
   *
   * @param GoogleAdsSearchads360V0CommonKeywordInfo $keyword
   */
  public function setKeyword(GoogleAdsSearchads360V0CommonKeywordInfo $keyword)
  {
    $this->keyword = $keyword;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonKeywordInfo
   */
  public function getKeyword()
  {
    return $this->keyword;
  }
  /**
   * Output only. The resource names of labels attached to this ad group
   * criterion.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The datetime when this ad group criterion was last modified.
   * The datetime is in the customer's time zone and in "yyyy-MM-dd
   * HH:mm:ss.ssssss" format.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Immutable. Listing group.
   *
   * @param GoogleAdsSearchads360V0CommonListingGroupInfo $listingGroup
   */
  public function setListingGroup(GoogleAdsSearchads360V0CommonListingGroupInfo $listingGroup)
  {
    $this->listingGroup = $listingGroup;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonListingGroupInfo
   */
  public function getListingGroup()
  {
    return $this->listingGroup;
  }
  /**
   * Immutable. Location.
   *
   * @param GoogleAdsSearchads360V0CommonLocationInfo $location
   */
  public function setLocation(GoogleAdsSearchads360V0CommonLocationInfo $location)
  {
    $this->location = $location;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonLocationInfo
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Immutable. Whether to target (`false`) or exclude (`true`) the criterion.
   * This field is immutable. To switch a criterion from positive to negative,
   * remove then re-add it.
   *
   * @param bool $negative
   */
  public function setNegative($negative)
  {
    $this->negative = $negative;
  }
  /**
   * @return bool
   */
  public function getNegative()
  {
    return $this->negative;
  }
  /**
   * Output only. Estimates for criterion bids at various positions.
   *
   * @param GoogleAdsSearchads360V0ResourcesAdGroupCriterionPositionEstimates $positionEstimates
   */
  public function setPositionEstimates(GoogleAdsSearchads360V0ResourcesAdGroupCriterionPositionEstimates $positionEstimates)
  {
    $this->positionEstimates = $positionEstimates;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAdGroupCriterionPositionEstimates
   */
  public function getPositionEstimates()
  {
    return $this->positionEstimates;
  }
  /**
   * Output only. Information regarding the quality of the criterion.
   *
   * @param GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo $qualityInfo
   */
  public function setQualityInfo(GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo $qualityInfo)
  {
    $this->qualityInfo = $qualityInfo;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo
   */
  public function getQualityInfo()
  {
    return $this->qualityInfo;
  }
  /**
   * Immutable. The resource name of the ad group criterion. Ad group criterion
   * resource names have the form:
   * `customers/{customer_id}/adGroupCriteria/{ad_group_id}~{criterion_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The status of the criterion. This is the status of the ad group criterion
   * entity, set by the client. Note: UI reports may incorporate additional
   * information that affects whether a criterion is eligible to run. In some
   * cases a criterion that's REMOVED in the API can still show as enabled in
   * the UI. For example, campaigns by default show to users of all age ranges
   * unless excluded. The UI will show each age range as "enabled", since
   * they're eligible to see the ads; but AdGroupCriterion.status will show
   * "removed", since no positive criterion was added.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, PAUSED, REMOVED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The URL template for constructing a tracking URL.
   *
   * @param string $trackingUrlTemplate
   */
  public function setTrackingUrlTemplate($trackingUrlTemplate)
  {
    $this->trackingUrlTemplate = $trackingUrlTemplate;
  }
  /**
   * @return string
   */
  public function getTrackingUrlTemplate()
  {
    return $this->trackingUrlTemplate;
  }
  /**
   * Output only. The type of the criterion.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, KEYWORD, PLACEMENT,
   * MOBILE_APP_CATEGORY, MOBILE_APPLICATION, DEVICE, LOCATION, LISTING_GROUP,
   * AD_SCHEDULE, AGE_RANGE, GENDER, INCOME_RANGE, PARENTAL_STATUS,
   * YOUTUBE_VIDEO, YOUTUBE_CHANNEL, USER_LIST, PROXIMITY, TOPIC, LISTING_SCOPE,
   * LANGUAGE, IP_BLOCK, CONTENT_LABEL, CARRIER, USER_INTEREST, WEBPAGE,
   * OPERATING_SYSTEM_VERSION, APP_PAYMENT_MODEL, MOBILE_DEVICE,
   * CUSTOM_AFFINITY, CUSTOM_INTENT, LOCATION_GROUP, CUSTOM_AUDIENCE,
   * COMBINED_AUDIENCE, KEYWORD_THEME, AUDIENCE, LOCAL_SERVICE_ID, BRAND,
   * BRAND_LIST, LIFE_EVENT
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
  /**
   * The list of mappings used to substitute custom parameter tags in a
   * `tracking_url_template`, `final_urls`, or `mobile_final_urls`.
   *
   * @param GoogleAdsSearchads360V0CommonCustomParameter[] $urlCustomParameters
   */
  public function setUrlCustomParameters($urlCustomParameters)
  {
    $this->urlCustomParameters = $urlCustomParameters;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonCustomParameter[]
   */
  public function getUrlCustomParameters()
  {
    return $this->urlCustomParameters;
  }
  /**
   * Immutable. User List.
   *
   * @param GoogleAdsSearchads360V0CommonUserListInfo $userList
   */
  public function setUserList(GoogleAdsSearchads360V0CommonUserListInfo $userList)
  {
    $this->userList = $userList;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonUserListInfo
   */
  public function getUserList()
  {
    return $this->userList;
  }
  /**
   * Immutable. Webpage
   *
   * @param GoogleAdsSearchads360V0CommonWebpageInfo $webpage
   */
  public function setWebpage(GoogleAdsSearchads360V0CommonWebpageInfo $webpage)
  {
    $this->webpage = $webpage;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonWebpageInfo
   */
  public function getWebpage()
  {
    return $this->webpage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAdGroupCriterion::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroupCriterion');
