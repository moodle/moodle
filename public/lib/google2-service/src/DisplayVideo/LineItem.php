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

class LineItem extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const CONTAINS_EU_POLITICAL_ADS_EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN = 'EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN';
  /**
   * Contains EU political advertising.
   */
  public const CONTAINS_EU_POLITICAL_ADS_CONTAINS_EU_POLITICAL_ADVERTISING = 'CONTAINS_EU_POLITICAL_ADVERTISING';
  /**
   * Does not contain EU political advertising.
   */
  public const CONTAINS_EU_POLITICAL_ADS_DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING = 'DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING';
  /**
   * Default value when status is not specified or is unknown in this version.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  /**
   * Type value is not specified or is unknown in this version. Line items of
   * this type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_UNSPECIFIED = 'LINE_ITEM_TYPE_UNSPECIFIED';
  /**
   * Image, HTML5, native, or rich media ads.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_DEFAULT = 'LINE_ITEM_TYPE_DISPLAY_DEFAULT';
  /**
   * Display ads that drive installs of an app.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL = 'LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL';
  /**
   * Video ads sold on a CPM basis for a variety of environments.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_DEFAULT = 'LINE_ITEM_TYPE_VIDEO_DEFAULT';
  /**
   * Video ads that drive installs of an app.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL = 'LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL';
  /**
   * Display ads served on mobile app inventory. Line items of this type and
   * their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INVENTORY = 'LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INVENTORY';
  /**
   * Video ads served on mobile app inventory. Line items of this type and their
   * targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INVENTORY = 'LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INVENTORY';
  /**
   * RTB Audio ads sold for a variety of environments.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_AUDIO_DEFAULT = 'LINE_ITEM_TYPE_AUDIO_DEFAULT';
  /**
   * Over-the-top ads present in OTT insertion orders. This type is only
   * applicable to line items with an insertion order of insertion_order_type
   * `OVER_THE_TOP`.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_OVER_THE_TOP = 'LINE_ITEM_TYPE_VIDEO_OVER_THE_TOP';
  /**
   * YouTube video ads that promote conversions. Line items of this type and
   * their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_ACTION = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_ACTION';
  /**
   * YouTube video ads (up to 15 seconds) that cannot be skipped. Line items of
   * this type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE';
  /**
   * YouTube video ads that show a story in a particular sequence using a mix of
   * formats. Line items of this type and their targeting cannot be created or
   * updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIDEO_SEQUENCE = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIDEO_SEQUENCE';
  /**
   * YouTube audio ads. Line items of this type and their targeting cannot be
   * created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_AUDIO = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_AUDIO';
  /**
   * YouTube video ads that optimize reaching more unique users at lower cost.
   * May include bumper ads, skippable in-stream ads, or a mix of types. Line
   * items of this type and their targeting cannot be created or updated using
   * the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH';
  /**
   * Default YouTube video ads. Line items of this type and their targeting
   * cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE';
  /**
   * Connected TV youTube video ads (up to 15 seconds) that cannot be skipped.
   * Line items of this type and their targeting cannot be created or updated
   * using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE_OVER_THE_TOP = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE_OVER_THE_TOP';
  /**
   * Connected TV youTube video ads that optimize reaching more unique users at
   * lower cost. May include bumper ads, skippable in-stream ads, or a mix of
   * types. Line items of this type and their targeting cannot be created or
   * updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH_OVER_THE_TOP = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH_OVER_THE_TOP';
  /**
   * Connected TV default YouTube video ads. Only include in-stream ad-format.
   * Line items of this type and their targeting cannot be created or updated
   * using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE_OVER_THE_TOP = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE_OVER_THE_TOP';
  /**
   * The goal of this line item type is to show the YouTube ads target number of
   * times to the same person in a certain period of time. Line items of this
   * type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_TARGET_FREQUENCY = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_TARGET_FREQUENCY';
  /**
   * YouTube video ads that aim to get more views with a variety of ad formats.
   * Line items of this type and their targeting cannot be created or updated
   * using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIEW = 'LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIEW';
  /**
   * Display ads served on digital-out-of-home inventory. Line items of this
   * type and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_DISPLAY_OUT_OF_HOME = 'LINE_ITEM_TYPE_DISPLAY_OUT_OF_HOME';
  /**
   * Video ads served on digital-out-of-home inventory. Line items of this type
   * and their targeting cannot be created or updated using the API.
   */
  public const LINE_ITEM_TYPE_LINE_ITEM_TYPE_VIDEO_OUT_OF_HOME = 'LINE_ITEM_TYPE_VIDEO_OUT_OF_HOME';
  /**
   * Reservation type value is not specified or is unknown in this version.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_UNSPECIFIED = 'RESERVATION_TYPE_UNSPECIFIED';
  /**
   * Not created through a guaranteed inventory source.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_NOT_GUARANTEED = 'RESERVATION_TYPE_NOT_GUARANTEED';
  /**
   * Created through a programmatic guaranteed inventory source.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_PROGRAMMATIC_GUARANTEED = 'RESERVATION_TYPE_PROGRAMMATIC_GUARANTEED';
  /**
   * Created through a tag guaranteed inventory source.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_TAG_GUARANTEED = 'RESERVATION_TYPE_TAG_GUARANTEED';
  /**
   * Created through a Petra inventory source. Only applicable to YouTube and
   * Partners line items.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_PETRA_VIRAL = 'RESERVATION_TYPE_PETRA_VIRAL';
  /**
   * Created with an instant quote. Only applicable to YouTube and partners line
   * items.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_INSTANT_RESERVE = 'RESERVATION_TYPE_INSTANT_RESERVE';
  protected $collection_key = 'warningMessages';
  /**
   * Output only. The unique ID of the advertiser the line item belongs to.
   *
   * @var string
   */
  public $advertiserId;
  protected $bidStrategyType = BiddingStrategy::class;
  protected $bidStrategyDataType = '';
  protected $budgetType = LineItemBudget::class;
  protected $budgetDataType = '';
  /**
   * Output only. The unique ID of the campaign that the line item belongs to.
   *
   * @var string
   */
  public $campaignId;
  /**
   * Whether this line item will serve European Union political ads. If
   * contains_eu_political_ads has been set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` in the parent advertiser, then
   * this field will be assigned `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if
   * not otherwise specified. This field can then be updated using the UI, API,
   * or Structured Data Files. This field must be assigned when creating a new
   * line item. Otherwise, **the `advertisers.lineItems.create` request will
   * fail**.
   *
   * @var string
   */
  public $containsEuPoliticalAds;
  protected $conversionCountingType = ConversionCountingConfig::class;
  protected $conversionCountingDataType = '';
  /**
   * The IDs of the creatives associated with the line item.
   *
   * @var string[]
   */
  public $creativeIds;
  /**
   * Required. The display name of the line item. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Controls whether or not the line item can spend its budget and
   * bid on inventory. * For CreateLineItem method, only `ENTITY_STATUS_DRAFT`
   * is allowed. To activate a line item, use UpdateLineItem method and update
   * the status to `ENTITY_STATUS_ACTIVE` after creation. * A line item cannot
   * be changed back to `ENTITY_STATUS_DRAFT` status from any other status. * If
   * the line item's parent insertion order is not active, the line item can't
   * spend its budget even if its own status is `ENTITY_STATUS_ACTIVE`.
   *
   * @var string
   */
  public $entityStatus;
  /**
   * Whether to exclude new exchanges from automatically being targeted by the
   * line item. This field is false by default.
   *
   * @var bool
   */
  public $excludeNewExchanges;
  protected $flightType = LineItemFlight::class;
  protected $flightDataType = '';
  protected $frequencyCapType = FrequencyCap::class;
  protected $frequencyCapDataType = '';
  /**
   * Required. Immutable. The unique ID of the insertion order that the line
   * item belongs to.
   *
   * @var string
   */
  public $insertionOrderId;
  protected $integrationDetailsType = IntegrationDetails::class;
  protected $integrationDetailsDataType = '';
  /**
   * Output only. The unique ID of the line item. Assigned by the system.
   *
   * @var string
   */
  public $lineItemId;
  /**
   * Required. Immutable. The type of the line item.
   *
   * @var string
   */
  public $lineItemType;
  protected $mobileAppType = MobileApp::class;
  protected $mobileAppDataType = '';
  /**
   * Output only. The resource name of the line item.
   *
   * @var string
   */
  public $name;
  protected $pacingType = Pacing::class;
  protected $pacingDataType = '';
  protected $partnerCostsType = PartnerCost::class;
  protected $partnerCostsDataType = 'array';
  protected $partnerRevenueModelType = PartnerRevenueModel::class;
  protected $partnerRevenueModelDataType = '';
  /**
   * Output only. The reservation type of the line item.
   *
   * @var string
   */
  public $reservationType;
  protected $targetingExpansionType = TargetingExpansionConfig::class;
  protected $targetingExpansionDataType = '';
  /**
   * Output only. The timestamp when the line item was last updated. Assigned by
   * the system.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The warning messages generated by the line item. These
   * warnings do not block saving the line item, but some may block the line
   * item from running.
   *
   * @var string[]
   */
  public $warningMessages;
  protected $youtubeAndPartnersSettingsType = YoutubeAndPartnersSettings::class;
  protected $youtubeAndPartnersSettingsDataType = '';

  /**
   * Output only. The unique ID of the advertiser the line item belongs to.
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
   * Required. The bidding strategy of the line item.
   *
   * @param BiddingStrategy $bidStrategy
   */
  public function setBidStrategy(BiddingStrategy $bidStrategy)
  {
    $this->bidStrategy = $bidStrategy;
  }
  /**
   * @return BiddingStrategy
   */
  public function getBidStrategy()
  {
    return $this->bidStrategy;
  }
  /**
   * Required. The budget allocation setting of the line item.
   *
   * @param LineItemBudget $budget
   */
  public function setBudget(LineItemBudget $budget)
  {
    $this->budget = $budget;
  }
  /**
   * @return LineItemBudget
   */
  public function getBudget()
  {
    return $this->budget;
  }
  /**
   * Output only. The unique ID of the campaign that the line item belongs to.
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
   * Whether this line item will serve European Union political ads. If
   * contains_eu_political_ads has been set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` in the parent advertiser, then
   * this field will be assigned `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if
   * not otherwise specified. This field can then be updated using the UI, API,
   * or Structured Data Files. This field must be assigned when creating a new
   * line item. Otherwise, **the `advertisers.lineItems.create` request will
   * fail**.
   *
   * Accepted values: EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN,
   * CONTAINS_EU_POLITICAL_ADVERTISING,
   * DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING
   *
   * @param self::CONTAINS_EU_POLITICAL_ADS_* $containsEuPoliticalAds
   */
  public function setContainsEuPoliticalAds($containsEuPoliticalAds)
  {
    $this->containsEuPoliticalAds = $containsEuPoliticalAds;
  }
  /**
   * @return self::CONTAINS_EU_POLITICAL_ADS_*
   */
  public function getContainsEuPoliticalAds()
  {
    return $this->containsEuPoliticalAds;
  }
  /**
   * The conversion tracking setting of the line item.
   *
   * @param ConversionCountingConfig $conversionCounting
   */
  public function setConversionCounting(ConversionCountingConfig $conversionCounting)
  {
    $this->conversionCounting = $conversionCounting;
  }
  /**
   * @return ConversionCountingConfig
   */
  public function getConversionCounting()
  {
    return $this->conversionCounting;
  }
  /**
   * The IDs of the creatives associated with the line item.
   *
   * @param string[] $creativeIds
   */
  public function setCreativeIds($creativeIds)
  {
    $this->creativeIds = $creativeIds;
  }
  /**
   * @return string[]
   */
  public function getCreativeIds()
  {
    return $this->creativeIds;
  }
  /**
   * Required. The display name of the line item. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
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
   * Required. Controls whether or not the line item can spend its budget and
   * bid on inventory. * For CreateLineItem method, only `ENTITY_STATUS_DRAFT`
   * is allowed. To activate a line item, use UpdateLineItem method and update
   * the status to `ENTITY_STATUS_ACTIVE` after creation. * A line item cannot
   * be changed back to `ENTITY_STATUS_DRAFT` status from any other status. * If
   * the line item's parent insertion order is not active, the line item can't
   * spend its budget even if its own status is `ENTITY_STATUS_ACTIVE`.
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::ENTITY_STATUS_* $entityStatus
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return self::ENTITY_STATUS_*
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
  /**
   * Whether to exclude new exchanges from automatically being targeted by the
   * line item. This field is false by default.
   *
   * @param bool $excludeNewExchanges
   */
  public function setExcludeNewExchanges($excludeNewExchanges)
  {
    $this->excludeNewExchanges = $excludeNewExchanges;
  }
  /**
   * @return bool
   */
  public function getExcludeNewExchanges()
  {
    return $this->excludeNewExchanges;
  }
  /**
   * Required. The start and end time of the line item's flight.
   *
   * @param LineItemFlight $flight
   */
  public function setFlight(LineItemFlight $flight)
  {
    $this->flight = $flight;
  }
  /**
   * @return LineItemFlight
   */
  public function getFlight()
  {
    return $this->flight;
  }
  /**
   * Required. The impression frequency cap settings of the line item. The
   * max_impressions field in this settings object must be used if assigning a
   * limited cap.
   *
   * @param FrequencyCap $frequencyCap
   */
  public function setFrequencyCap(FrequencyCap $frequencyCap)
  {
    $this->frequencyCap = $frequencyCap;
  }
  /**
   * @return FrequencyCap
   */
  public function getFrequencyCap()
  {
    return $this->frequencyCap;
  }
  /**
   * Required. Immutable. The unique ID of the insertion order that the line
   * item belongs to.
   *
   * @param string $insertionOrderId
   */
  public function setInsertionOrderId($insertionOrderId)
  {
    $this->insertionOrderId = $insertionOrderId;
  }
  /**
   * @return string
   */
  public function getInsertionOrderId()
  {
    return $this->insertionOrderId;
  }
  /**
   * Integration details of the line item.
   *
   * @param IntegrationDetails $integrationDetails
   */
  public function setIntegrationDetails(IntegrationDetails $integrationDetails)
  {
    $this->integrationDetails = $integrationDetails;
  }
  /**
   * @return IntegrationDetails
   */
  public function getIntegrationDetails()
  {
    return $this->integrationDetails;
  }
  /**
   * Output only. The unique ID of the line item. Assigned by the system.
   *
   * @param string $lineItemId
   */
  public function setLineItemId($lineItemId)
  {
    $this->lineItemId = $lineItemId;
  }
  /**
   * @return string
   */
  public function getLineItemId()
  {
    return $this->lineItemId;
  }
  /**
   * Required. Immutable. The type of the line item.
   *
   * Accepted values: LINE_ITEM_TYPE_UNSPECIFIED,
   * LINE_ITEM_TYPE_DISPLAY_DEFAULT, LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL,
   * LINE_ITEM_TYPE_VIDEO_DEFAULT, LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL,
   * LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INVENTORY,
   * LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INVENTORY, LINE_ITEM_TYPE_AUDIO_DEFAULT,
   * LINE_ITEM_TYPE_VIDEO_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_ACTION,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIDEO_SEQUENCE,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_AUDIO,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_NON_SKIPPABLE_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_REACH_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_SIMPLE_OVER_THE_TOP,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_TARGET_FREQUENCY,
   * LINE_ITEM_TYPE_YOUTUBE_AND_PARTNERS_VIEW,
   * LINE_ITEM_TYPE_DISPLAY_OUT_OF_HOME, LINE_ITEM_TYPE_VIDEO_OUT_OF_HOME
   *
   * @param self::LINE_ITEM_TYPE_* $lineItemType
   */
  public function setLineItemType($lineItemType)
  {
    $this->lineItemType = $lineItemType;
  }
  /**
   * @return self::LINE_ITEM_TYPE_*
   */
  public function getLineItemType()
  {
    return $this->lineItemType;
  }
  /**
   * The mobile app promoted by the line item. This is applicable only when
   * line_item_type is either `LINE_ITEM_TYPE_DISPLAY_MOBILE_APP_INSTALL` or
   * `LINE_ITEM_TYPE_VIDEO_MOBILE_APP_INSTALL`.
   *
   * @param MobileApp $mobileApp
   */
  public function setMobileApp(MobileApp $mobileApp)
  {
    $this->mobileApp = $mobileApp;
  }
  /**
   * @return MobileApp
   */
  public function getMobileApp()
  {
    return $this->mobileApp;
  }
  /**
   * Output only. The resource name of the line item.
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
   * Required. The budget spending speed setting of the line item.
   *
   * @param Pacing $pacing
   */
  public function setPacing(Pacing $pacing)
  {
    $this->pacing = $pacing;
  }
  /**
   * @return Pacing
   */
  public function getPacing()
  {
    return $this->pacing;
  }
  /**
   * The partner costs associated with the line item. If absent or empty in
   * CreateLineItem method, the newly created line item will inherit partner
   * costs from its parent insertion order.
   *
   * @param PartnerCost[] $partnerCosts
   */
  public function setPartnerCosts($partnerCosts)
  {
    $this->partnerCosts = $partnerCosts;
  }
  /**
   * @return PartnerCost[]
   */
  public function getPartnerCosts()
  {
    return $this->partnerCosts;
  }
  /**
   * Required. The partner revenue model setting of the line item.
   *
   * @param PartnerRevenueModel $partnerRevenueModel
   */
  public function setPartnerRevenueModel(PartnerRevenueModel $partnerRevenueModel)
  {
    $this->partnerRevenueModel = $partnerRevenueModel;
  }
  /**
   * @return PartnerRevenueModel
   */
  public function getPartnerRevenueModel()
  {
    return $this->partnerRevenueModel;
  }
  /**
   * Output only. The reservation type of the line item.
   *
   * Accepted values: RESERVATION_TYPE_UNSPECIFIED,
   * RESERVATION_TYPE_NOT_GUARANTEED, RESERVATION_TYPE_PROGRAMMATIC_GUARANTEED,
   * RESERVATION_TYPE_TAG_GUARANTEED, RESERVATION_TYPE_PETRA_VIRAL,
   * RESERVATION_TYPE_INSTANT_RESERVE
   *
   * @param self::RESERVATION_TYPE_* $reservationType
   */
  public function setReservationType($reservationType)
  {
    $this->reservationType = $reservationType;
  }
  /**
   * @return self::RESERVATION_TYPE_*
   */
  public function getReservationType()
  {
    return $this->reservationType;
  }
  /**
   * The [optimized
   * targeting](//support.google.com/displayvideo/answer/12060859) settings of
   * the line item. This config is only applicable for display, video, or audio
   * line items that use automated bidding and positively target eligible
   * audience lists.
   *
   * @param TargetingExpansionConfig $targetingExpansion
   */
  public function setTargetingExpansion(TargetingExpansionConfig $targetingExpansion)
  {
    $this->targetingExpansion = $targetingExpansion;
  }
  /**
   * @return TargetingExpansionConfig
   */
  public function getTargetingExpansion()
  {
    return $this->targetingExpansion;
  }
  /**
   * Output only. The timestamp when the line item was last updated. Assigned by
   * the system.
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
  /**
   * Output only. The warning messages generated by the line item. These
   * warnings do not block saving the line item, but some may block the line
   * item from running.
   *
   * @param string[] $warningMessages
   */
  public function setWarningMessages($warningMessages)
  {
    $this->warningMessages = $warningMessages;
  }
  /**
   * @return string[]
   */
  public function getWarningMessages()
  {
    return $this->warningMessages;
  }
  /**
   * Output only. Settings specific to YouTube and Partners line items.
   *
   * @param YoutubeAndPartnersSettings $youtubeAndPartnersSettings
   */
  public function setYoutubeAndPartnersSettings(YoutubeAndPartnersSettings $youtubeAndPartnersSettings)
  {
    $this->youtubeAndPartnersSettings = $youtubeAndPartnersSettings;
  }
  /**
   * @return YoutubeAndPartnersSettings
   */
  public function getYoutubeAndPartnersSettings()
  {
    return $this->youtubeAndPartnersSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LineItem::class, 'Google_Service_DisplayVideo_LineItem');
