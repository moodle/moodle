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

namespace Google\Service\Dfareporting;

class Ad extends \Google\Collection
{
  public const COMPATIBILITY_DISPLAY = 'DISPLAY';
  public const COMPATIBILITY_DISPLAY_INTERSTITIAL = 'DISPLAY_INTERSTITIAL';
  /**
   * Deprecated enum value. No longer supported.
   */
  public const COMPATIBILITY_APP = 'APP';
  /**
   * Deprecated enum value. No longer supported.
   */
  public const COMPATIBILITY_APP_INTERSTITIAL = 'APP_INTERSTITIAL';
  public const COMPATIBILITY_IN_STREAM_VIDEO = 'IN_STREAM_VIDEO';
  public const COMPATIBILITY_IN_STREAM_AUDIO = 'IN_STREAM_AUDIO';
  public const TYPE_AD_SERVING_STANDARD_AD = 'AD_SERVING_STANDARD_AD';
  public const TYPE_AD_SERVING_DEFAULT_AD = 'AD_SERVING_DEFAULT_AD';
  public const TYPE_AD_SERVING_CLICK_TRACKER = 'AD_SERVING_CLICK_TRACKER';
  public const TYPE_AD_SERVING_TRACKING = 'AD_SERVING_TRACKING';
  public const TYPE_AD_SERVING_BRAND_SAFE_AD = 'AD_SERVING_BRAND_SAFE_AD';
  protected $collection_key = 'placementAssignments';
  /**
   * Account ID of this ad. This is a read-only field that can be left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether this ad is active. When true, archived must be false.
   *
   * @var bool
   */
  public $active;
  /**
   * Advertiser ID of this ad. This is a required field on insertion.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Whether this ad is archived. When true, active must be false.
   *
   * @var bool
   */
  public $archived;
  /**
   * Audience segment ID that is being targeted for this ad. Applicable when
   * type is AD_SERVING_STANDARD_AD.
   *
   * @var string
   */
  public $audienceSegmentId;
  /**
   * Campaign ID of this ad. This is a required field on insertion.
   *
   * @var string
   */
  public $campaignId;
  protected $campaignIdDimensionValueType = DimensionValue::class;
  protected $campaignIdDimensionValueDataType = '';
  protected $clickThroughUrlType = ClickThroughUrl::class;
  protected $clickThroughUrlDataType = '';
  protected $clickThroughUrlSuffixPropertiesType = ClickThroughUrlSuffixProperties::class;
  protected $clickThroughUrlSuffixPropertiesDataType = '';
  /**
   * Comments for this ad.
   *
   * @var string
   */
  public $comments;
  /**
   * Compatibility of this ad. Applicable when type is AD_SERVING_DEFAULT_AD.
   * DISPLAY and DISPLAY_INTERSTITIAL refer to either rendering on desktop or on
   * mobile devices or in mobile apps for regular or interstitial ads,
   * respectively. APP and APP_INTERSTITIAL are only used for existing default
   * ads. New mobile placements must be assigned DISPLAY or DISPLAY_INTERSTITIAL
   * and default ads created for those placements will be limited to those
   * compatibility types. IN_STREAM_VIDEO refers to rendering in-stream video
   * ads developed with the VAST standard.
   *
   * @var string
   */
  public $compatibility;
  protected $contextualKeywordTargetingType = ContextualKeywordTargeting::class;
  protected $contextualKeywordTargetingDataType = '';
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  protected $creativeGroupAssignmentsType = CreativeGroupAssignment::class;
  protected $creativeGroupAssignmentsDataType = 'array';
  protected $creativeRotationType = CreativeRotation::class;
  protected $creativeRotationDataType = '';
  protected $dayPartTargetingType = DayPartTargeting::class;
  protected $dayPartTargetingDataType = '';
  protected $defaultClickThroughEventTagPropertiesType = DefaultClickThroughEventTagProperties::class;
  protected $defaultClickThroughEventTagPropertiesDataType = '';
  protected $deliveryScheduleType = DeliverySchedule::class;
  protected $deliveryScheduleDataType = '';
  /**
   * Whether this ad is a dynamic click tracker. Applicable when type is
   * AD_SERVING_CLICK_TRACKER. This is a required field on insert, and is read-
   * only after insert.
   *
   * @var bool
   */
  public $dynamicClickTracker;
  /**
   * @var string
   */
  public $endTime;
  protected $eventTagOverridesType = EventTagOverride::class;
  protected $eventTagOverridesDataType = 'array';
  protected $geoTargetingType = GeoTargeting::class;
  protected $geoTargetingDataType = '';
  /**
   * ID of this ad. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  protected $keyValueTargetingExpressionType = KeyValueTargetingExpression::class;
  protected $keyValueTargetingExpressionDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#ad".
   *
   * @var string
   */
  public $kind;
  protected $languageTargetingType = LanguageTargeting::class;
  protected $languageTargetingDataType = '';
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Name of this ad. This is a required field and must be less than 256
   * characters long.
   *
   * @var string
   */
  public $name;
  protected $placementAssignmentsType = PlacementAssignment::class;
  protected $placementAssignmentsDataType = 'array';
  protected $remarketingListExpressionType = ListTargetingExpression::class;
  protected $remarketingListExpressionDataType = '';
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  /**
   * Whether this ad is ssl compliant. This is a read-only field that is auto-
   * generated when the ad is inserted or updated.
   *
   * @var bool
   */
  public $sslCompliant;
  /**
   * Whether this ad requires ssl. This is a read-only field that is auto-
   * generated when the ad is inserted or updated.
   *
   * @var bool
   */
  public $sslRequired;
  /**
   * @var string
   */
  public $startTime;
  /**
   * Subaccount ID of this ad. This is a read-only field that can be left blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Targeting template ID, used to apply preconfigured targeting information to
   * this ad. This cannot be set while any of dayPartTargeting, geoTargeting,
   * keyValueTargetingExpression, languageTargeting, remarketingListExpression,
   * or technologyTargeting are set. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @var string
   */
  public $targetingTemplateId;
  protected $technologyTargetingType = TechnologyTargeting::class;
  protected $technologyTargetingDataType = '';
  /**
   * Type of ad. This is a required field on insertion. Note that default ads (
   * AD_SERVING_DEFAULT_AD) cannot be created directly (see Creative resource).
   *
   * @var string
   */
  public $type;

  /**
   * Account ID of this ad. This is a read-only field that can be left blank.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Whether this ad is active. When true, archived must be false.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Advertiser ID of this ad. This is a required field on insertion.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Dimension value for the ID of the advertiser. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $advertiserIdDimensionValue
   */
  public function setAdvertiserIdDimensionValue(DimensionValue $advertiserIdDimensionValue)
  {
    $this->advertiserIdDimensionValue = $advertiserIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getAdvertiserIdDimensionValue()
  {
    return $this->advertiserIdDimensionValue;
  }
  /**
   * Whether this ad is archived. When true, active must be false.
   *
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * Audience segment ID that is being targeted for this ad. Applicable when
   * type is AD_SERVING_STANDARD_AD.
   *
   * @param string $audienceSegmentId
   */
  public function setAudienceSegmentId($audienceSegmentId)
  {
    $this->audienceSegmentId = $audienceSegmentId;
  }
  /**
   * @return string
   */
  public function getAudienceSegmentId()
  {
    return $this->audienceSegmentId;
  }
  /**
   * Campaign ID of this ad. This is a required field on insertion.
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * Dimension value for the ID of the campaign. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $campaignIdDimensionValue
   */
  public function setCampaignIdDimensionValue(DimensionValue $campaignIdDimensionValue)
  {
    $this->campaignIdDimensionValue = $campaignIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getCampaignIdDimensionValue()
  {
    return $this->campaignIdDimensionValue;
  }
  /**
   * Click-through URL for this ad. This is a required field on insertion.
   * Applicable when type is AD_SERVING_CLICK_TRACKER.
   *
   * @param ClickThroughUrl $clickThroughUrl
   */
  public function setClickThroughUrl(ClickThroughUrl $clickThroughUrl)
  {
    $this->clickThroughUrl = $clickThroughUrl;
  }
  /**
   * @return ClickThroughUrl
   */
  public function getClickThroughUrl()
  {
    return $this->clickThroughUrl;
  }
  /**
   * Click-through URL suffix properties for this ad. Applies to the URL in the
   * ad or (if overriding ad properties) the URL in the creative.
   *
   * @param ClickThroughUrlSuffixProperties $clickThroughUrlSuffixProperties
   */
  public function setClickThroughUrlSuffixProperties(ClickThroughUrlSuffixProperties $clickThroughUrlSuffixProperties)
  {
    $this->clickThroughUrlSuffixProperties = $clickThroughUrlSuffixProperties;
  }
  /**
   * @return ClickThroughUrlSuffixProperties
   */
  public function getClickThroughUrlSuffixProperties()
  {
    return $this->clickThroughUrlSuffixProperties;
  }
  /**
   * Comments for this ad.
   *
   * @param string $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }
  /**
   * @return string
   */
  public function getComments()
  {
    return $this->comments;
  }
  /**
   * Compatibility of this ad. Applicable when type is AD_SERVING_DEFAULT_AD.
   * DISPLAY and DISPLAY_INTERSTITIAL refer to either rendering on desktop or on
   * mobile devices or in mobile apps for regular or interstitial ads,
   * respectively. APP and APP_INTERSTITIAL are only used for existing default
   * ads. New mobile placements must be assigned DISPLAY or DISPLAY_INTERSTITIAL
   * and default ads created for those placements will be limited to those
   * compatibility types. IN_STREAM_VIDEO refers to rendering in-stream video
   * ads developed with the VAST standard.
   *
   * Accepted values: DISPLAY, DISPLAY_INTERSTITIAL, APP, APP_INTERSTITIAL,
   * IN_STREAM_VIDEO, IN_STREAM_AUDIO
   *
   * @param self::COMPATIBILITY_* $compatibility
   */
  public function setCompatibility($compatibility)
  {
    $this->compatibility = $compatibility;
  }
  /**
   * @return self::COMPATIBILITY_*
   */
  public function getCompatibility()
  {
    return $this->compatibility;
  }
  /**
   * Optional. Contextual keyword targeting information for this ad.
   *
   * @param ContextualKeywordTargeting $contextualKeywordTargeting
   */
  public function setContextualKeywordTargeting(ContextualKeywordTargeting $contextualKeywordTargeting)
  {
    $this->contextualKeywordTargeting = $contextualKeywordTargeting;
  }
  /**
   * @return ContextualKeywordTargeting
   */
  public function getContextualKeywordTargeting()
  {
    return $this->contextualKeywordTargeting;
  }
  /**
   * Information about the creation of this ad. This is a read-only field.
   *
   * @param LastModifiedInfo $createInfo
   */
  public function setCreateInfo(LastModifiedInfo $createInfo)
  {
    $this->createInfo = $createInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreateInfo()
  {
    return $this->createInfo;
  }
  /**
   * Creative group assignments for this ad. Applicable when type is
   * AD_SERVING_CLICK_TRACKER. Only one assignment per creative group number is
   * allowed for a maximum of two assignments.
   *
   * @param CreativeGroupAssignment[] $creativeGroupAssignments
   */
  public function setCreativeGroupAssignments($creativeGroupAssignments)
  {
    $this->creativeGroupAssignments = $creativeGroupAssignments;
  }
  /**
   * @return CreativeGroupAssignment[]
   */
  public function getCreativeGroupAssignments()
  {
    return $this->creativeGroupAssignments;
  }
  /**
   * Creative rotation for this ad. Applicable when type is
   * AD_SERVING_DEFAULT_AD, AD_SERVING_STANDARD_AD, or AD_SERVING_TRACKING. When
   * type is AD_SERVING_DEFAULT_AD, this field should have exactly one
   * creativeAssignment .
   *
   * @param CreativeRotation $creativeRotation
   */
  public function setCreativeRotation(CreativeRotation $creativeRotation)
  {
    $this->creativeRotation = $creativeRotation;
  }
  /**
   * @return CreativeRotation
   */
  public function getCreativeRotation()
  {
    return $this->creativeRotation;
  }
  /**
   * Time and day targeting information for this ad. This field must be left
   * blank if the ad is using a targeting template. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param DayPartTargeting $dayPartTargeting
   */
  public function setDayPartTargeting(DayPartTargeting $dayPartTargeting)
  {
    $this->dayPartTargeting = $dayPartTargeting;
  }
  /**
   * @return DayPartTargeting
   */
  public function getDayPartTargeting()
  {
    return $this->dayPartTargeting;
  }
  /**
   * Default click-through event tag properties for this ad.
   *
   * @param DefaultClickThroughEventTagProperties $defaultClickThroughEventTagProperties
   */
  public function setDefaultClickThroughEventTagProperties(DefaultClickThroughEventTagProperties $defaultClickThroughEventTagProperties)
  {
    $this->defaultClickThroughEventTagProperties = $defaultClickThroughEventTagProperties;
  }
  /**
   * @return DefaultClickThroughEventTagProperties
   */
  public function getDefaultClickThroughEventTagProperties()
  {
    return $this->defaultClickThroughEventTagProperties;
  }
  /**
   * Delivery schedule information for this ad. Applicable when type is
   * AD_SERVING_STANDARD_AD or AD_SERVING_TRACKING. This field along with
   * subfields priority and impressionRatio are required on insertion when type
   * is AD_SERVING_STANDARD_AD.
   *
   * @param DeliverySchedule $deliverySchedule
   */
  public function setDeliverySchedule(DeliverySchedule $deliverySchedule)
  {
    $this->deliverySchedule = $deliverySchedule;
  }
  /**
   * @return DeliverySchedule
   */
  public function getDeliverySchedule()
  {
    return $this->deliverySchedule;
  }
  /**
   * Whether this ad is a dynamic click tracker. Applicable when type is
   * AD_SERVING_CLICK_TRACKER. This is a required field on insert, and is read-
   * only after insert.
   *
   * @param bool $dynamicClickTracker
   */
  public function setDynamicClickTracker($dynamicClickTracker)
  {
    $this->dynamicClickTracker = $dynamicClickTracker;
  }
  /**
   * @return bool
   */
  public function getDynamicClickTracker()
  {
    return $this->dynamicClickTracker;
  }
  /**
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Event tag overrides for this ad.
   *
   * @param EventTagOverride[] $eventTagOverrides
   */
  public function setEventTagOverrides($eventTagOverrides)
  {
    $this->eventTagOverrides = $eventTagOverrides;
  }
  /**
   * @return EventTagOverride[]
   */
  public function getEventTagOverrides()
  {
    return $this->eventTagOverrides;
  }
  /**
   * Geographical targeting information for this ad. This field must be left
   * blank if the ad is using a targeting template. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param GeoTargeting $geoTargeting
   */
  public function setGeoTargeting(GeoTargeting $geoTargeting)
  {
    $this->geoTargeting = $geoTargeting;
  }
  /**
   * @return GeoTargeting
   */
  public function getGeoTargeting()
  {
    return $this->geoTargeting;
  }
  /**
   * ID of this ad. This is a read-only, auto-generated field.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Dimension value for the ID of this ad. This is a read-only, auto-generated
   * field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Key-value targeting information for this ad. This field must be left blank
   * if the ad is using a targeting template. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param KeyValueTargetingExpression $keyValueTargetingExpression
   */
  public function setKeyValueTargetingExpression(KeyValueTargetingExpression $keyValueTargetingExpression)
  {
    $this->keyValueTargetingExpression = $keyValueTargetingExpression;
  }
  /**
   * @return KeyValueTargetingExpression
   */
  public function getKeyValueTargetingExpression()
  {
    return $this->keyValueTargetingExpression;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#ad".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Language targeting information for this ad. This field must be left blank
   * if the ad is using a targeting template. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param LanguageTargeting $languageTargeting
   */
  public function setLanguageTargeting(LanguageTargeting $languageTargeting)
  {
    $this->languageTargeting = $languageTargeting;
  }
  /**
   * @return LanguageTargeting
   */
  public function getLanguageTargeting()
  {
    return $this->languageTargeting;
  }
  /**
   * Information about the most recent modification of this ad. This is a read-
   * only field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Name of this ad. This is a required field and must be less than 256
   * characters long.
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
   * Placement assignments for this ad.
   *
   * @param PlacementAssignment[] $placementAssignments
   */
  public function setPlacementAssignments($placementAssignments)
  {
    $this->placementAssignments = $placementAssignments;
  }
  /**
   * @return PlacementAssignment[]
   */
  public function getPlacementAssignments()
  {
    return $this->placementAssignments;
  }
  /**
   * Remarketing list targeting expression for this ad. This field must be left
   * blank if the ad is using a targeting template. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param ListTargetingExpression $remarketingListExpression
   */
  public function setRemarketingListExpression(ListTargetingExpression $remarketingListExpression)
  {
    $this->remarketingListExpression = $remarketingListExpression;
  }
  /**
   * @return ListTargetingExpression
   */
  public function getRemarketingListExpression()
  {
    return $this->remarketingListExpression;
  }
  /**
   * Size of this ad. Applicable when type is AD_SERVING_DEFAULT_AD.
   *
   * @param Size $size
   */
  public function setSize(Size $size)
  {
    $this->size = $size;
  }
  /**
   * @return Size
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * Whether this ad is ssl compliant. This is a read-only field that is auto-
   * generated when the ad is inserted or updated.
   *
   * @param bool $sslCompliant
   */
  public function setSslCompliant($sslCompliant)
  {
    $this->sslCompliant = $sslCompliant;
  }
  /**
   * @return bool
   */
  public function getSslCompliant()
  {
    return $this->sslCompliant;
  }
  /**
   * Whether this ad requires ssl. This is a read-only field that is auto-
   * generated when the ad is inserted or updated.
   *
   * @param bool $sslRequired
   */
  public function setSslRequired($sslRequired)
  {
    $this->sslRequired = $sslRequired;
  }
  /**
   * @return bool
   */
  public function getSslRequired()
  {
    return $this->sslRequired;
  }
  /**
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Subaccount ID of this ad. This is a read-only field that can be left blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Targeting template ID, used to apply preconfigured targeting information to
   * this ad. This cannot be set while any of dayPartTargeting, geoTargeting,
   * keyValueTargetingExpression, languageTargeting, remarketingListExpression,
   * or technologyTargeting are set. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param string $targetingTemplateId
   */
  public function setTargetingTemplateId($targetingTemplateId)
  {
    $this->targetingTemplateId = $targetingTemplateId;
  }
  /**
   * @return string
   */
  public function getTargetingTemplateId()
  {
    return $this->targetingTemplateId;
  }
  /**
   * Technology platform targeting information for this ad. This field must be
   * left blank if the ad is using a targeting template. Applicable when type is
   * AD_SERVING_STANDARD_AD.
   *
   * @param TechnologyTargeting $technologyTargeting
   */
  public function setTechnologyTargeting(TechnologyTargeting $technologyTargeting)
  {
    $this->technologyTargeting = $technologyTargeting;
  }
  /**
   * @return TechnologyTargeting
   */
  public function getTechnologyTargeting()
  {
    return $this->technologyTargeting;
  }
  /**
   * Type of ad. This is a required field on insertion. Note that default ads (
   * AD_SERVING_DEFAULT_AD) cannot be created directly (see Creative resource).
   *
   * Accepted values: AD_SERVING_STANDARD_AD, AD_SERVING_DEFAULT_AD,
   * AD_SERVING_CLICK_TRACKER, AD_SERVING_TRACKING, AD_SERVING_BRAND_SAFE_AD
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
class_alias(Ad::class, 'Google_Service_Dfareporting_Ad');
