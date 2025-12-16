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

class Placement extends \Google\Collection
{
  public const ACTIVE_STATUS_PLACEMENT_STATUS_UNKNOWN = 'PLACEMENT_STATUS_UNKNOWN';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_ACTIVE = 'PLACEMENT_STATUS_ACTIVE';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_INACTIVE = 'PLACEMENT_STATUS_INACTIVE';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_ARCHIVED = 'PLACEMENT_STATUS_ARCHIVED';
  public const ACTIVE_STATUS_PLACEMENT_STATUS_PERMANENTLY_ARCHIVED = 'PLACEMENT_STATUS_PERMANENTLY_ARCHIVED';
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
  public const PAYMENT_SOURCE_PLACEMENT_AGENCY_PAID = 'PLACEMENT_AGENCY_PAID';
  public const PAYMENT_SOURCE_PLACEMENT_PUBLISHER_PAID = 'PLACEMENT_PUBLISHER_PAID';
  /**
   * Placement is not yet reviewed by publisher.
   */
  public const STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * Placement Ad Serving fee is accepted by publisher
   */
  public const STATUS_PAYMENT_ACCEPTED = 'PAYMENT_ACCEPTED';
  /**
   * Placement Ad Serving fee is rejected by publisher
   */
  public const STATUS_PAYMENT_REJECTED = 'PAYMENT_REJECTED';
  /**
   * Advertisers has accepted rejection of placement ad serving fee. This will
   * suppress future notification on DDMM UI
   */
  public const STATUS_ACKNOWLEDGE_REJECTION = 'ACKNOWLEDGE_REJECTION';
  /**
   * Advertisers has accepted acceptance of placement ad serving fee. This will
   * suppress future notification on DDMM UI
   */
  public const STATUS_ACKNOWLEDGE_ACCEPTANCE = 'ACKNOWLEDGE_ACCEPTANCE';
  /**
   * Advertisers is still working on placement not yet ready for Publisher
   * review; default status for pub-paid placements
   */
  public const STATUS_DRAFT = 'DRAFT';
  /**
   * DEFAULT means Google chooses which adapter, if any, to serve.
   */
  public const VPAID_ADAPTER_CHOICE_DEFAULT = 'DEFAULT';
  public const VPAID_ADAPTER_CHOICE_FLASH = 'FLASH';
  public const VPAID_ADAPTER_CHOICE_HTML5 = 'HTML5';
  public const VPAID_ADAPTER_CHOICE_BOTH = 'BOTH';
  protected $collection_key = 'tagFormats';
  /**
   * Account ID of this placement. This field can be left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether this placement is active, inactive, archived or permanently
   * archived.
   *
   * @var string
   */
  public $activeStatus;
  /**
   * Whether this placement opts out of ad blocking. When true, ad blocking is
   * disabled for this placement. When false, the campaign and site settings
   * take effect.
   *
   * @var bool
   */
  public $adBlockingOptOut;
  /**
   * Optional. Ad serving platform ID to identify the ad serving platform used
   * by the placement. Measurement partners can use this field to add ad-server
   * specific macros. Possible values are: * `1`, Adelphic * `2`, Adform * `3`,
   * Adobe * `4`, Amobee * `5`, Basis (Centro) * `6`, Beeswax * `7`, Amazon *
   * `8`, DV360 (DBM) * `9`, Innovid * `10`, MediaMath * `11`, Roku OneView DSP
   * * `12`, TabMo Hawk * `13`, The Trade Desk * `14`, Xandr Invest DSP * `15`,
   * Yahoo DSP * `16`, Zeta Global * `17`, Scaleout * `18`, Bidtellect * `19`,
   * Unicorn * `20`, Teads * `21`, Quantcast * `22`, Cognitiv * `23`, AdTheorent
   * * `24`, DeepIntent * `25`, Pulsepoint
   *
   * @var string
   */
  public $adServingPlatformId;
  protected $additionalSizesType = Size::class;
  protected $additionalSizesDataType = 'array';
  /**
   * Advertiser ID of this placement. This field can be left blank.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Optional. Whether the placement is enabled for YouTube integration.
   *
   * @var bool
   */
  public $allowOnYoutube;
  /**
   * Campaign ID of this placement. This field is a required field on insertion.
   *
   * @var string
   */
  public $campaignId;
  protected $campaignIdDimensionValueType = DimensionValue::class;
  protected $campaignIdDimensionValueDataType = '';
  /**
   * Comments for this placement.
   *
   * @var string
   */
  public $comment;
  /**
   * Placement compatibility. DISPLAY and DISPLAY_INTERSTITIAL refer to
   * rendering on desktop, on mobile devices or in mobile apps for regular or
   * interstitial ads respectively. APP and APP_INTERSTITIAL are no longer
   * allowed for new placement insertions. Instead, use DISPLAY or
   * DISPLAY_INTERSTITIAL. IN_STREAM_VIDEO refers to rendering in in-stream
   * video ads developed with the VAST standard. This field is required on
   * insertion.
   *
   * @var string
   */
  public $compatibility;
  /**
   * ID of the content category assigned to this placement.
   *
   * @var string
   */
  public $contentCategoryId;
  protected $conversionDomainOverrideType = PlacementConversionDomainOverride::class;
  protected $conversionDomainOverrideDataType = '';
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * Directory site ID of this placement. On insert, you must set either this
   * field or the siteId field to specify the site associated with this
   * placement. This is a required field that is read-only after insertion.
   *
   * @var string
   */
  public $directorySiteId;
  protected $directorySiteIdDimensionValueType = DimensionValue::class;
  protected $directorySiteIdDimensionValueDataType = '';
  /**
   * External ID for this placement.
   *
   * @var string
   */
  public $externalId;
  /**
   * ID of this placement. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Key name of this placement. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $keyName;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#placement".
   *
   * @var string
   */
  public $kind;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  protected $lookbackConfigurationType = LookbackConfiguration::class;
  protected $lookbackConfigurationDataType = '';
  /**
   * Name of this placement.This is a required field and must be less than or
   * equal to 512 characters long.
   *
   * @var string
   */
  public $name;
  protected $partnerWrappingDataType = MeasurementPartnerWrappingData::class;
  protected $partnerWrappingDataDataType = '';
  /**
   * Whether payment was approved for this placement. This is a read-only field
   * relevant only to publisher-paid placements.
   *
   * @var bool
   */
  public $paymentApproved;
  /**
   * Payment source for this placement. This is a required field that is read-
   * only after insertion.
   *
   * @var string
   */
  public $paymentSource;
  /**
   * ID of this placement's group, if applicable.
   *
   * @var string
   */
  public $placementGroupId;
  protected $placementGroupIdDimensionValueType = DimensionValue::class;
  protected $placementGroupIdDimensionValueDataType = '';
  /**
   * ID of the placement strategy assigned to this placement.
   *
   * @var string
   */
  public $placementStrategyId;
  protected $pricingScheduleType = PricingSchedule::class;
  protected $pricingScheduleDataType = '';
  /**
   * Whether this placement is the primary placement of a roadblock (placement
   * group). You cannot change this field from true to false. Setting this field
   * to true will automatically set the primary field on the original primary
   * placement of the roadblock to false, and it will automatically set the
   * roadblock's primaryPlacementId field to the ID of this placement.
   *
   * @var bool
   */
  public $primary;
  protected $publisherUpdateInfoType = LastModifiedInfo::class;
  protected $publisherUpdateInfoDataType = '';
  /**
   * Site ID associated with this placement. On insert, you must set either this
   * field or the directorySiteId field to specify the site associated with this
   * placement. This is a required field that is read-only after insertion.
   *
   * @var string
   */
  public $siteId;
  protected $siteIdDimensionValueType = DimensionValue::class;
  protected $siteIdDimensionValueDataType = '';
  /**
   * Optional. Whether the ads in the placement are served by another platform
   * and CM is only used for tracking or they are served by CM. A false value
   * indicates the ad is served by CM.
   *
   * @var bool
   */
  public $siteServed;
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  /**
   * Whether creatives assigned to this placement must be SSL-compliant.
   *
   * @var bool
   */
  public $sslRequired;
  /**
   * Third-party placement status.
   *
   * @var string
   */
  public $status;
  /**
   * Subaccount ID of this placement. This field can be left blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Tag formats to generate for this placement. This field is required on
   * insertion. Acceptable values are: - "PLACEMENT_TAG_STANDARD" -
   * "PLACEMENT_TAG_IFRAME_JAVASCRIPT" - "PLACEMENT_TAG_IFRAME_ILAYER" -
   * "PLACEMENT_TAG_INTERNAL_REDIRECT" - "PLACEMENT_TAG_JAVASCRIPT" -
   * "PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT" -
   * "PLACEMENT_TAG_INTERSTITIAL_INTERNAL_REDIRECT" -
   * "PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT" - "PLACEMENT_TAG_CLICK_COMMANDS" -
   * "PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH" -
   * "PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_3" -
   * "PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_4" - "PLACEMENT_TAG_TRACKING" -
   * "PLACEMENT_TAG_TRACKING_IFRAME" - "PLACEMENT_TAG_TRACKING_JAVASCRIPT"
   *
   * @var string[]
   */
  public $tagFormats;
  protected $tagSettingType = TagSetting::class;
  protected $tagSettingDataType = '';
  /**
   * Whether Verification and ActiveView are disabled for in-stream video
   * creatives for this placement. The same setting videoActiveViewOptOut exists
   * on the site level -- the opt out occurs if either of these settings are
   * true. These settings are distinct from
   * DirectorySites.settings.activeViewOptOut or
   * Sites.siteSettings.activeViewOptOut which only apply to display ads.
   * However, Accounts.activeViewOptOut opts out both video traffic, as well as
   * display ads, from Verification and ActiveView.
   *
   * @var bool
   */
  public $videoActiveViewOptOut;
  protected $videoSettingsType = VideoSettings::class;
  protected $videoSettingsDataType = '';
  /**
   * VPAID adapter setting for this placement. Controls which VPAID format the
   * measurement adapter will use for in-stream video creatives assigned to this
   * placement. *Note:* Flash is no longer supported. This field now defaults to
   * HTML5 when the following values are provided: FLASH, BOTH.
   *
   * @var string
   */
  public $vpaidAdapterChoice;
  /**
   * Whether this placement opts out of tag wrapping.
   *
   * @var bool
   */
  public $wrappingOptOut;
  protected $youtubeSettingsType = YoutubeSettings::class;
  protected $youtubeSettingsDataType = '';

  /**
   * Account ID of this placement. This field can be left blank.
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
   * Whether this placement is active, inactive, archived or permanently
   * archived.
   *
   * Accepted values: PLACEMENT_STATUS_UNKNOWN, PLACEMENT_STATUS_ACTIVE,
   * PLACEMENT_STATUS_INACTIVE, PLACEMENT_STATUS_ARCHIVED,
   * PLACEMENT_STATUS_PERMANENTLY_ARCHIVED
   *
   * @param self::ACTIVE_STATUS_* $activeStatus
   */
  public function setActiveStatus($activeStatus)
  {
    $this->activeStatus = $activeStatus;
  }
  /**
   * @return self::ACTIVE_STATUS_*
   */
  public function getActiveStatus()
  {
    return $this->activeStatus;
  }
  /**
   * Whether this placement opts out of ad blocking. When true, ad blocking is
   * disabled for this placement. When false, the campaign and site settings
   * take effect.
   *
   * @param bool $adBlockingOptOut
   */
  public function setAdBlockingOptOut($adBlockingOptOut)
  {
    $this->adBlockingOptOut = $adBlockingOptOut;
  }
  /**
   * @return bool
   */
  public function getAdBlockingOptOut()
  {
    return $this->adBlockingOptOut;
  }
  /**
   * Optional. Ad serving platform ID to identify the ad serving platform used
   * by the placement. Measurement partners can use this field to add ad-server
   * specific macros. Possible values are: * `1`, Adelphic * `2`, Adform * `3`,
   * Adobe * `4`, Amobee * `5`, Basis (Centro) * `6`, Beeswax * `7`, Amazon *
   * `8`, DV360 (DBM) * `9`, Innovid * `10`, MediaMath * `11`, Roku OneView DSP
   * * `12`, TabMo Hawk * `13`, The Trade Desk * `14`, Xandr Invest DSP * `15`,
   * Yahoo DSP * `16`, Zeta Global * `17`, Scaleout * `18`, Bidtellect * `19`,
   * Unicorn * `20`, Teads * `21`, Quantcast * `22`, Cognitiv * `23`, AdTheorent
   * * `24`, DeepIntent * `25`, Pulsepoint
   *
   * @param string $adServingPlatformId
   */
  public function setAdServingPlatformId($adServingPlatformId)
  {
    $this->adServingPlatformId = $adServingPlatformId;
  }
  /**
   * @return string
   */
  public function getAdServingPlatformId()
  {
    return $this->adServingPlatformId;
  }
  /**
   * Additional sizes associated with this placement. When inserting or updating
   * a placement, only the size ID field is used.
   *
   * @param Size[] $additionalSizes
   */
  public function setAdditionalSizes($additionalSizes)
  {
    $this->additionalSizes = $additionalSizes;
  }
  /**
   * @return Size[]
   */
  public function getAdditionalSizes()
  {
    return $this->additionalSizes;
  }
  /**
   * Advertiser ID of this placement. This field can be left blank.
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
   * Optional. Whether the placement is enabled for YouTube integration.
   *
   * @param bool $allowOnYoutube
   */
  public function setAllowOnYoutube($allowOnYoutube)
  {
    $this->allowOnYoutube = $allowOnYoutube;
  }
  /**
   * @return bool
   */
  public function getAllowOnYoutube()
  {
    return $this->allowOnYoutube;
  }
  /**
   * Campaign ID of this placement. This field is a required field on insertion.
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
   * Comments for this placement.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * Placement compatibility. DISPLAY and DISPLAY_INTERSTITIAL refer to
   * rendering on desktop, on mobile devices or in mobile apps for regular or
   * interstitial ads respectively. APP and APP_INTERSTITIAL are no longer
   * allowed for new placement insertions. Instead, use DISPLAY or
   * DISPLAY_INTERSTITIAL. IN_STREAM_VIDEO refers to rendering in in-stream
   * video ads developed with the VAST standard. This field is required on
   * insertion.
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
   * ID of the content category assigned to this placement.
   *
   * @param string $contentCategoryId
   */
  public function setContentCategoryId($contentCategoryId)
  {
    $this->contentCategoryId = $contentCategoryId;
  }
  /**
   * @return string
   */
  public function getContentCategoryId()
  {
    return $this->contentCategoryId;
  }
  /**
   * Optional. Conversion domain overrides for a placement.
   *
   * @param PlacementConversionDomainOverride $conversionDomainOverride
   */
  public function setConversionDomainOverride(PlacementConversionDomainOverride $conversionDomainOverride)
  {
    $this->conversionDomainOverride = $conversionDomainOverride;
  }
  /**
   * @return PlacementConversionDomainOverride
   */
  public function getConversionDomainOverride()
  {
    return $this->conversionDomainOverride;
  }
  /**
   * Information about the creation of this placement. This is a read-only
   * field.
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
   * Directory site ID of this placement. On insert, you must set either this
   * field or the siteId field to specify the site associated with this
   * placement. This is a required field that is read-only after insertion.
   *
   * @param string $directorySiteId
   */
  public function setDirectorySiteId($directorySiteId)
  {
    $this->directorySiteId = $directorySiteId;
  }
  /**
   * @return string
   */
  public function getDirectorySiteId()
  {
    return $this->directorySiteId;
  }
  /**
   * Dimension value for the ID of the directory site. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $directorySiteIdDimensionValue
   */
  public function setDirectorySiteIdDimensionValue(DimensionValue $directorySiteIdDimensionValue)
  {
    $this->directorySiteIdDimensionValue = $directorySiteIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getDirectorySiteIdDimensionValue()
  {
    return $this->directorySiteIdDimensionValue;
  }
  /**
   * External ID for this placement.
   *
   * @param string $externalId
   */
  public function setExternalId($externalId)
  {
    $this->externalId = $externalId;
  }
  /**
   * @return string
   */
  public function getExternalId()
  {
    return $this->externalId;
  }
  /**
   * ID of this placement. This is a read-only, auto-generated field.
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
   * Dimension value for the ID of this placement. This is a read-only, auto-
   * generated field.
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
   * Key name of this placement. This is a read-only, auto-generated field.
   *
   * @param string $keyName
   */
  public function setKeyName($keyName)
  {
    $this->keyName = $keyName;
  }
  /**
   * @return string
   */
  public function getKeyName()
  {
    return $this->keyName;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#placement".
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
   * Information about the most recent modification of this placement. This is a
   * read-only field.
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
   * Lookback window settings for this placement.
   *
   * @param LookbackConfiguration $lookbackConfiguration
   */
  public function setLookbackConfiguration(LookbackConfiguration $lookbackConfiguration)
  {
    $this->lookbackConfiguration = $lookbackConfiguration;
  }
  /**
   * @return LookbackConfiguration
   */
  public function getLookbackConfiguration()
  {
    return $this->lookbackConfiguration;
  }
  /**
   * Name of this placement.This is a required field and must be less than or
   * equal to 512 characters long.
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
   * Measurement partner provided settings for a wrapped placement.
   *
   * @param MeasurementPartnerWrappingData $partnerWrappingData
   */
  public function setPartnerWrappingData(MeasurementPartnerWrappingData $partnerWrappingData)
  {
    $this->partnerWrappingData = $partnerWrappingData;
  }
  /**
   * @return MeasurementPartnerWrappingData
   */
  public function getPartnerWrappingData()
  {
    return $this->partnerWrappingData;
  }
  /**
   * Whether payment was approved for this placement. This is a read-only field
   * relevant only to publisher-paid placements.
   *
   * @param bool $paymentApproved
   */
  public function setPaymentApproved($paymentApproved)
  {
    $this->paymentApproved = $paymentApproved;
  }
  /**
   * @return bool
   */
  public function getPaymentApproved()
  {
    return $this->paymentApproved;
  }
  /**
   * Payment source for this placement. This is a required field that is read-
   * only after insertion.
   *
   * Accepted values: PLACEMENT_AGENCY_PAID, PLACEMENT_PUBLISHER_PAID
   *
   * @param self::PAYMENT_SOURCE_* $paymentSource
   */
  public function setPaymentSource($paymentSource)
  {
    $this->paymentSource = $paymentSource;
  }
  /**
   * @return self::PAYMENT_SOURCE_*
   */
  public function getPaymentSource()
  {
    return $this->paymentSource;
  }
  /**
   * ID of this placement's group, if applicable.
   *
   * @param string $placementGroupId
   */
  public function setPlacementGroupId($placementGroupId)
  {
    $this->placementGroupId = $placementGroupId;
  }
  /**
   * @return string
   */
  public function getPlacementGroupId()
  {
    return $this->placementGroupId;
  }
  /**
   * Dimension value for the ID of the placement group. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $placementGroupIdDimensionValue
   */
  public function setPlacementGroupIdDimensionValue(DimensionValue $placementGroupIdDimensionValue)
  {
    $this->placementGroupIdDimensionValue = $placementGroupIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getPlacementGroupIdDimensionValue()
  {
    return $this->placementGroupIdDimensionValue;
  }
  /**
   * ID of the placement strategy assigned to this placement.
   *
   * @param string $placementStrategyId
   */
  public function setPlacementStrategyId($placementStrategyId)
  {
    $this->placementStrategyId = $placementStrategyId;
  }
  /**
   * @return string
   */
  public function getPlacementStrategyId()
  {
    return $this->placementStrategyId;
  }
  /**
   * Pricing schedule of this placement. This field is required on insertion,
   * specifically subfields startDate, endDate and pricingType.
   *
   * @param PricingSchedule $pricingSchedule
   */
  public function setPricingSchedule(PricingSchedule $pricingSchedule)
  {
    $this->pricingSchedule = $pricingSchedule;
  }
  /**
   * @return PricingSchedule
   */
  public function getPricingSchedule()
  {
    return $this->pricingSchedule;
  }
  /**
   * Whether this placement is the primary placement of a roadblock (placement
   * group). You cannot change this field from true to false. Setting this field
   * to true will automatically set the primary field on the original primary
   * placement of the roadblock to false, and it will automatically set the
   * roadblock's primaryPlacementId field to the ID of this placement.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Information about the last publisher update. This is a read-only field.
   *
   * @param LastModifiedInfo $publisherUpdateInfo
   */
  public function setPublisherUpdateInfo(LastModifiedInfo $publisherUpdateInfo)
  {
    $this->publisherUpdateInfo = $publisherUpdateInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getPublisherUpdateInfo()
  {
    return $this->publisherUpdateInfo;
  }
  /**
   * Site ID associated with this placement. On insert, you must set either this
   * field or the directorySiteId field to specify the site associated with this
   * placement. This is a required field that is read-only after insertion.
   *
   * @param string $siteId
   */
  public function setSiteId($siteId)
  {
    $this->siteId = $siteId;
  }
  /**
   * @return string
   */
  public function getSiteId()
  {
    return $this->siteId;
  }
  /**
   * Dimension value for the ID of the site. This is a read-only, auto-generated
   * field.
   *
   * @param DimensionValue $siteIdDimensionValue
   */
  public function setSiteIdDimensionValue(DimensionValue $siteIdDimensionValue)
  {
    $this->siteIdDimensionValue = $siteIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getSiteIdDimensionValue()
  {
    return $this->siteIdDimensionValue;
  }
  /**
   * Optional. Whether the ads in the placement are served by another platform
   * and CM is only used for tracking or they are served by CM. A false value
   * indicates the ad is served by CM.
   *
   * @param bool $siteServed
   */
  public function setSiteServed($siteServed)
  {
    $this->siteServed = $siteServed;
  }
  /**
   * @return bool
   */
  public function getSiteServed()
  {
    return $this->siteServed;
  }
  /**
   * Size associated with this placement. When inserting or updating a
   * placement, only the size ID field is used. This field is required on
   * insertion.
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
   * Whether creatives assigned to this placement must be SSL-compliant.
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
   * Third-party placement status.
   *
   * Accepted values: PENDING_REVIEW, PAYMENT_ACCEPTED, PAYMENT_REJECTED,
   * ACKNOWLEDGE_REJECTION, ACKNOWLEDGE_ACCEPTANCE, DRAFT
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
   * Subaccount ID of this placement. This field can be left blank.
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
   * Tag formats to generate for this placement. This field is required on
   * insertion. Acceptable values are: - "PLACEMENT_TAG_STANDARD" -
   * "PLACEMENT_TAG_IFRAME_JAVASCRIPT" - "PLACEMENT_TAG_IFRAME_ILAYER" -
   * "PLACEMENT_TAG_INTERNAL_REDIRECT" - "PLACEMENT_TAG_JAVASCRIPT" -
   * "PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT" -
   * "PLACEMENT_TAG_INTERSTITIAL_INTERNAL_REDIRECT" -
   * "PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT" - "PLACEMENT_TAG_CLICK_COMMANDS" -
   * "PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH" -
   * "PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_3" -
   * "PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_4" - "PLACEMENT_TAG_TRACKING" -
   * "PLACEMENT_TAG_TRACKING_IFRAME" - "PLACEMENT_TAG_TRACKING_JAVASCRIPT"
   *
   * @param string[] $tagFormats
   */
  public function setTagFormats($tagFormats)
  {
    $this->tagFormats = $tagFormats;
  }
  /**
   * @return string[]
   */
  public function getTagFormats()
  {
    return $this->tagFormats;
  }
  /**
   * Tag settings for this placement.
   *
   * @param TagSetting $tagSetting
   */
  public function setTagSetting(TagSetting $tagSetting)
  {
    $this->tagSetting = $tagSetting;
  }
  /**
   * @return TagSetting
   */
  public function getTagSetting()
  {
    return $this->tagSetting;
  }
  /**
   * Whether Verification and ActiveView are disabled for in-stream video
   * creatives for this placement. The same setting videoActiveViewOptOut exists
   * on the site level -- the opt out occurs if either of these settings are
   * true. These settings are distinct from
   * DirectorySites.settings.activeViewOptOut or
   * Sites.siteSettings.activeViewOptOut which only apply to display ads.
   * However, Accounts.activeViewOptOut opts out both video traffic, as well as
   * display ads, from Verification and ActiveView.
   *
   * @param bool $videoActiveViewOptOut
   */
  public function setVideoActiveViewOptOut($videoActiveViewOptOut)
  {
    $this->videoActiveViewOptOut = $videoActiveViewOptOut;
  }
  /**
   * @return bool
   */
  public function getVideoActiveViewOptOut()
  {
    return $this->videoActiveViewOptOut;
  }
  /**
   * A collection of settings which affect video creatives served through this
   * placement. Applicable to placements with IN_STREAM_VIDEO compatibility.
   *
   * @param VideoSettings $videoSettings
   */
  public function setVideoSettings(VideoSettings $videoSettings)
  {
    $this->videoSettings = $videoSettings;
  }
  /**
   * @return VideoSettings
   */
  public function getVideoSettings()
  {
    return $this->videoSettings;
  }
  /**
   * VPAID adapter setting for this placement. Controls which VPAID format the
   * measurement adapter will use for in-stream video creatives assigned to this
   * placement. *Note:* Flash is no longer supported. This field now defaults to
   * HTML5 when the following values are provided: FLASH, BOTH.
   *
   * Accepted values: DEFAULT, FLASH, HTML5, BOTH
   *
   * @param self::VPAID_ADAPTER_CHOICE_* $vpaidAdapterChoice
   */
  public function setVpaidAdapterChoice($vpaidAdapterChoice)
  {
    $this->vpaidAdapterChoice = $vpaidAdapterChoice;
  }
  /**
   * @return self::VPAID_ADAPTER_CHOICE_*
   */
  public function getVpaidAdapterChoice()
  {
    return $this->vpaidAdapterChoice;
  }
  /**
   * Whether this placement opts out of tag wrapping.
   *
   * @param bool $wrappingOptOut
   */
  public function setWrappingOptOut($wrappingOptOut)
  {
    $this->wrappingOptOut = $wrappingOptOut;
  }
  /**
   * @return bool
   */
  public function getWrappingOptOut()
  {
    return $this->wrappingOptOut;
  }
  /**
   * Optional. YouTube settings for the placement. The placement must be enabled
   * for YouTube to use this field.
   *
   * @param YoutubeSettings $youtubeSettings
   */
  public function setYoutubeSettings(YoutubeSettings $youtubeSettings)
  {
    $this->youtubeSettings = $youtubeSettings;
  }
  /**
   * @return YoutubeSettings
   */
  public function getYoutubeSettings()
  {
    return $this->youtubeSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Placement::class, 'Google_Service_Dfareporting_Placement');
