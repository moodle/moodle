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

class GoogleAdsSearchads360V0ResourcesCampaignCriterion extends \Google\Model
{
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
   * The campaign criterion is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The campaign criterion is paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * The campaign criterion is removed.
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
  protected $ageRangeType = GoogleAdsSearchads360V0CommonAgeRangeInfo::class;
  protected $ageRangeDataType = '';
  /**
   * The modifier for the bids when the criterion matches. The modifier must be
   * in the range: 0.1 - 10.0. Most targetable criteria types support modifiers.
   * Use 0 to opt out of a Device type.
   *
   * @var float
   */
  public $bidModifier;
  /**
   * Output only. The ID of the criterion. This field is ignored during mutate.
   *
   * @var string
   */
  public $criterionId;
  protected $deviceType = GoogleAdsSearchads360V0CommonDeviceInfo::class;
  protected $deviceDataType = '';
  /**
   * Output only. The display name of the criterion. This field is ignored for
   * mutates.
   *
   * @var string
   */
  public $displayName;
  protected $genderType = GoogleAdsSearchads360V0CommonGenderInfo::class;
  protected $genderDataType = '';
  protected $keywordType = GoogleAdsSearchads360V0CommonKeywordInfo::class;
  protected $keywordDataType = '';
  protected $languageType = GoogleAdsSearchads360V0CommonLanguageInfo::class;
  protected $languageDataType = '';
  /**
   * Output only. The datetime when this campaign criterion was last modified.
   * The datetime is in the customer's time zone and in "yyyy-MM-dd
   * HH:mm:ss.ssssss" format.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $locationType = GoogleAdsSearchads360V0CommonLocationInfo::class;
  protected $locationDataType = '';
  protected $locationGroupType = GoogleAdsSearchads360V0CommonLocationGroupInfo::class;
  protected $locationGroupDataType = '';
  /**
   * Immutable. Whether to target (`false`) or exclude (`true`) the criterion.
   *
   * @var bool
   */
  public $negative;
  /**
   * Immutable. The resource name of the campaign criterion. Campaign criterion
   * resource names have the form:
   * `customers/{customer_id}/campaignCriteria/{campaign_id}~{criterion_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * The status of the criterion.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. The type of the criterion.
   *
   * @var string
   */
  public $type;
  protected $userListType = GoogleAdsSearchads360V0CommonUserListInfo::class;
  protected $userListDataType = '';
  protected $webpageType = GoogleAdsSearchads360V0CommonWebpageInfo::class;
  protected $webpageDataType = '';

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
  /**
   * The modifier for the bids when the criterion matches. The modifier must be
   * in the range: 0.1 - 10.0. Most targetable criteria types support modifiers.
   * Use 0 to opt out of a Device type.
   *
   * @param float $bidModifier
   */
  public function setBidModifier($bidModifier)
  {
    $this->bidModifier = $bidModifier;
  }
  /**
   * @return float
   */
  public function getBidModifier()
  {
    return $this->bidModifier;
  }
  /**
   * Output only. The ID of the criterion. This field is ignored during mutate.
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
   * Immutable. Device.
   *
   * @param GoogleAdsSearchads360V0CommonDeviceInfo $device
   */
  public function setDevice(GoogleAdsSearchads360V0CommonDeviceInfo $device)
  {
    $this->device = $device;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonDeviceInfo
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * Output only. The display name of the criterion. This field is ignored for
   * mutates.
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
   * Immutable. Language.
   *
   * @param GoogleAdsSearchads360V0CommonLanguageInfo $language
   */
  public function setLanguage(GoogleAdsSearchads360V0CommonLanguageInfo $language)
  {
    $this->language = $language;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonLanguageInfo
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Output only. The datetime when this campaign criterion was last modified.
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
   * Immutable. Location Group
   *
   * @param GoogleAdsSearchads360V0CommonLocationGroupInfo $locationGroup
   */
  public function setLocationGroup(GoogleAdsSearchads360V0CommonLocationGroupInfo $locationGroup)
  {
    $this->locationGroup = $locationGroup;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonLocationGroupInfo
   */
  public function getLocationGroup()
  {
    return $this->locationGroup;
  }
  /**
   * Immutable. Whether to target (`false`) or exclude (`true`) the criterion.
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
   * Immutable. The resource name of the campaign criterion. Campaign criterion
   * resource names have the form:
   * `customers/{customer_id}/campaignCriteria/{campaign_id}~{criterion_id}`
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
   * The status of the criterion.
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
   * Immutable. Webpage.
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
class_alias(GoogleAdsSearchads360V0ResourcesCampaignCriterion::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCampaignCriterion');
