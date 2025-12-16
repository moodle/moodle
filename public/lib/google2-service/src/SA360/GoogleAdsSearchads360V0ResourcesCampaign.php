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

class GoogleAdsSearchads360V0ResourcesCampaign extends \Google\Collection
{
  /**
   * No value has been specified.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Ad serving is optimized based on CTR for the campaign.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_OPTIMIZE = 'OPTIMIZE';
  /**
   * Ad serving is optimized based on CTR * Conversion for the campaign. If the
   * campaign is not in the conversion optimizer bidding strategy, it will
   * default to OPTIMIZED.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_CONVERSION_OPTIMIZE = 'CONVERSION_OPTIMIZE';
  /**
   * Ads are rotated evenly for 90 days, then optimized for clicks.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_ROTATE = 'ROTATE';
  /**
   * Show lower performing ads more evenly with higher performing ads, and do
   * not optimize.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_ROTATE_INDEFINITELY = 'ROTATE_INDEFINITELY';
  /**
   * Ad serving optimization status is not available.
   */
  public const AD_SERVING_OPTIMIZATION_STATUS_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Not specified.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used as a return value only. Represents value unknown in this version.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Mobile app campaigns for Search.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_SEARCH_MOBILE_APP = 'SEARCH_MOBILE_APP';
  /**
   * Mobile app campaigns for Display.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_DISPLAY_MOBILE_APP = 'DISPLAY_MOBILE_APP';
  /**
   * AdWords express campaigns for search.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_SEARCH_EXPRESS = 'SEARCH_EXPRESS';
  /**
   * AdWords Express campaigns for display.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_DISPLAY_EXPRESS = 'DISPLAY_EXPRESS';
  /**
   * Smart Shopping campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_SHOPPING_SMART_ADS = 'SHOPPING_SMART_ADS';
  /**
   * Gmail Ad campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_DISPLAY_GMAIL_AD = 'DISPLAY_GMAIL_AD';
  /**
   * Smart display campaigns. New campaigns of this sub type cannot be created.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_DISPLAY_SMART_CAMPAIGN = 'DISPLAY_SMART_CAMPAIGN';
  /**
   * Video Outstream campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_VIDEO_OUTSTREAM = 'VIDEO_OUTSTREAM';
  /**
   * Video TrueView for Action campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_VIDEO_ACTION = 'VIDEO_ACTION';
  /**
   * Video campaigns with non-skippable video ads.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_VIDEO_NON_SKIPPABLE = 'VIDEO_NON_SKIPPABLE';
  /**
   * App Campaign that lets you easily promote your Android or iOS app across
   * Google's top properties including Search, Play, YouTube, and the Google
   * Display Network.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_APP_CAMPAIGN = 'APP_CAMPAIGN';
  /**
   * App Campaign for engagement, focused on driving re-engagement with the app
   * across several of Google's top properties including Search, YouTube, and
   * the Google Display Network.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_APP_CAMPAIGN_FOR_ENGAGEMENT = 'APP_CAMPAIGN_FOR_ENGAGEMENT';
  /**
   * Campaigns specialized for local advertising.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_LOCAL_CAMPAIGN = 'LOCAL_CAMPAIGN';
  /**
   * Shopping Comparison Listing campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_SHOPPING_COMPARISON_LISTING_ADS = 'SHOPPING_COMPARISON_LISTING_ADS';
  /**
   * Standard Smart campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_SMART_CAMPAIGN = 'SMART_CAMPAIGN';
  /**
   * Video campaigns with sequence video ads.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_VIDEO_SEQUENCE = 'VIDEO_SEQUENCE';
  /**
   * App Campaign for pre registration, specialized for advertising mobile app
   * pre-registration, that targets multiple advertising channels across Google
   * Play, YouTube and Display Network.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_APP_CAMPAIGN_FOR_PRE_REGISTRATION = 'APP_CAMPAIGN_FOR_PRE_REGISTRATION';
  /**
   * Video reach campaign with Target Frequency bidding strategy.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_VIDEO_REACH_TARGET_FREQUENCY = 'VIDEO_REACH_TARGET_FREQUENCY';
  /**
   * Travel Activities campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_TRAVEL_ACTIVITIES = 'TRAVEL_ACTIVITIES';
  /**
   * Facebook tracking only social campaigns.
   */
  public const ADVERTISING_CHANNEL_SUB_TYPE_SOCIAL_FACEBOOK_TRACKING_ONLY = 'SOCIAL_FACEBOOK_TRACKING_ONLY';
  /**
   * Not specified.
   */
  public const ADVERTISING_CHANNEL_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ADVERTISING_CHANNEL_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Search Network. Includes display bundled, and Search+ campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_SEARCH = 'SEARCH';
  /**
   * Google Display Network only.
   */
  public const ADVERTISING_CHANNEL_TYPE_DISPLAY = 'DISPLAY';
  /**
   * Shopping campaigns serve on the shopping property and on google.com search
   * results.
   */
  public const ADVERTISING_CHANNEL_TYPE_SHOPPING = 'SHOPPING';
  /**
   * Hotel Ads campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_HOTEL = 'HOTEL';
  /**
   * Video campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_VIDEO = 'VIDEO';
  /**
   * App Campaigns, and App Campaigns for Engagement, that run across multiple
   * channels.
   */
  public const ADVERTISING_CHANNEL_TYPE_MULTI_CHANNEL = 'MULTI_CHANNEL';
  /**
   * Local ads campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_LOCAL = 'LOCAL';
  /**
   * Smart campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_SMART = 'SMART';
  /**
   * Performance Max campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_PERFORMANCE_MAX = 'PERFORMANCE_MAX';
  /**
   * Local services campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_LOCAL_SERVICES = 'LOCAL_SERVICES';
  /**
   * Discovery campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_DISCOVERY = 'DISCOVERY';
  /**
   * Travel campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_TRAVEL = 'TRAVEL';
  /**
   * Social campaigns.
   */
  public const ADVERTISING_CHANNEL_TYPE_SOCIAL = 'SOCIAL';
  /**
   * Signals that an unexpected error occurred, for example, no bidding strategy
   * type was found, or no status information was found.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The bid strategy is active, and AdWords cannot find any specific issues
   * with the strategy.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_ENABLED = 'ENABLED';
  /**
   * The bid strategy is learning because it has been recently created or
   * recently reactivated.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LEARNING_NEW = 'LEARNING_NEW';
  /**
   * The bid strategy is learning because of a recent setting change.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LEARNING_SETTING_CHANGE = 'LEARNING_SETTING_CHANGE';
  /**
   * The bid strategy is learning because of a recent budget change.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LEARNING_BUDGET_CHANGE = 'LEARNING_BUDGET_CHANGE';
  /**
   * The bid strategy is learning because of recent change in number of
   * campaigns, ad groups or keywords attached to it.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LEARNING_COMPOSITION_CHANGE = 'LEARNING_COMPOSITION_CHANGE';
  /**
   * The bid strategy depends on conversion reporting and the customer recently
   * modified conversion types that were relevant to the bid strategy.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LEARNING_CONVERSION_TYPE_CHANGE = 'LEARNING_CONVERSION_TYPE_CHANGE';
  /**
   * The bid strategy depends on conversion reporting and the customer recently
   * changed their conversion settings.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LEARNING_CONVERSION_SETTING_CHANGE = 'LEARNING_CONVERSION_SETTING_CHANGE';
  /**
   * The bid strategy is limited by its bid ceiling.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_CPC_BID_CEILING = 'LIMITED_BY_CPC_BID_CEILING';
  /**
   * The bid strategy is limited by its bid floor.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_CPC_BID_FLOOR = 'LIMITED_BY_CPC_BID_FLOOR';
  /**
   * The bid strategy is limited because there was not enough conversion traffic
   * over the past weeks.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_DATA = 'LIMITED_BY_DATA';
  /**
   * A significant fraction of keywords in this bid strategy are limited by
   * budget.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_BUDGET = 'LIMITED_BY_BUDGET';
  /**
   * The bid strategy cannot reach its target spend because its spend has been
   * de-prioritized.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_LOW_PRIORITY_SPEND = 'LIMITED_BY_LOW_PRIORITY_SPEND';
  /**
   * A significant fraction of keywords in this bid strategy have a low Quality
   * Score.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_LOW_QUALITY = 'LIMITED_BY_LOW_QUALITY';
  /**
   * The bid strategy cannot fully spend its budget because of narrow targeting.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_LIMITED_BY_INVENTORY = 'LIMITED_BY_INVENTORY';
  /**
   * Missing conversion tracking (no pings present) and/or remarketing lists for
   * SSC.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MISCONFIGURED_ZERO_ELIGIBILITY = 'MISCONFIGURED_ZERO_ELIGIBILITY';
  /**
   * The bid strategy depends on conversion reporting and the customer is
   * lacking conversion types that might be reported against this strategy.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MISCONFIGURED_CONVERSION_TYPES = 'MISCONFIGURED_CONVERSION_TYPES';
  /**
   * The bid strategy depends on conversion reporting and the customer's
   * conversion settings are misconfigured.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MISCONFIGURED_CONVERSION_SETTINGS = 'MISCONFIGURED_CONVERSION_SETTINGS';
  /**
   * There are campaigns outside the bid strategy that share budgets with
   * campaigns included in the strategy.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MISCONFIGURED_SHARED_BUDGET = 'MISCONFIGURED_SHARED_BUDGET';
  /**
   * The campaign has an invalid strategy type and is not serving.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MISCONFIGURED_STRATEGY_TYPE = 'MISCONFIGURED_STRATEGY_TYPE';
  /**
   * The bid strategy is not active. Either there are no active campaigns, ad
   * groups or keywords attached to the bid strategy. Or there are no active
   * budgets connected to the bid strategy.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_PAUSED = 'PAUSED';
  /**
   * This bid strategy currently does not support status reporting.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * There were multiple LEARNING_* system statuses for this bid strategy during
   * the time in question.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MULTIPLE_LEARNING = 'MULTIPLE_LEARNING';
  /**
   * There were multiple LIMITED_* system statuses for this bid strategy during
   * the time in question.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MULTIPLE_LIMITED = 'MULTIPLE_LIMITED';
  /**
   * There were multiple MISCONFIGURED_* system statuses for this bid strategy
   * during the time in question.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MULTIPLE_MISCONFIGURED = 'MULTIPLE_MISCONFIGURED';
  /**
   * There were multiple system statuses for this bid strategy during the time
   * in question.
   */
  public const BIDDING_STRATEGY_SYSTEM_STATUS_MULTIPLE = 'MULTIPLE';
  /**
   * Not specified.
   */
  public const BIDDING_STRATEGY_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const BIDDING_STRATEGY_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Commission is an automatic bidding strategy in which the advertiser pays a
   * certain portion of the conversion value.
   */
  public const BIDDING_STRATEGY_TYPE_COMMISSION = 'COMMISSION';
  /**
   * Enhanced CPC is a bidding strategy that raises bids for clicks that seem
   * more likely to lead to a conversion and lowers them for clicks where they
   * seem less likely.
   */
  public const BIDDING_STRATEGY_TYPE_ENHANCED_CPC = 'ENHANCED_CPC';
  /**
   * Used for return value only. Indicates that a campaign does not have a
   * bidding strategy. This prevents the campaign from serving. For example, a
   * campaign may be attached to a manager bidding strategy and the serving
   * account is subsequently unlinked from the manager account. In this case the
   * campaign will automatically be detached from the now inaccessible manager
   * bidding strategy and transition to the INVALID bidding strategy type.
   */
  public const BIDDING_STRATEGY_TYPE_INVALID = 'INVALID';
  /**
   * Manual bidding strategy that allows advertiser to set the bid per
   * advertiser-specified action.
   */
  public const BIDDING_STRATEGY_TYPE_MANUAL_CPA = 'MANUAL_CPA';
  /**
   * Manual click based bidding where user pays per click.
   */
  public const BIDDING_STRATEGY_TYPE_MANUAL_CPC = 'MANUAL_CPC';
  /**
   * Manual impression based bidding where user pays per thousand impressions.
   */
  public const BIDDING_STRATEGY_TYPE_MANUAL_CPM = 'MANUAL_CPM';
  /**
   * A bidding strategy that pays a configurable amount per video view.
   */
  public const BIDDING_STRATEGY_TYPE_MANUAL_CPV = 'MANUAL_CPV';
  /**
   * A bidding strategy that automatically maximizes number of conversions given
   * a daily budget.
   */
  public const BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSIONS = 'MAXIMIZE_CONVERSIONS';
  /**
   * An automated bidding strategy that automatically sets bids to maximize
   * revenue while spending your budget.
   */
  public const BIDDING_STRATEGY_TYPE_MAXIMIZE_CONVERSION_VALUE = 'MAXIMIZE_CONVERSION_VALUE';
  /**
   * Page-One Promoted bidding scheme, which sets max cpc bids to target
   * impressions on page one or page one promoted slots on google.com. This enum
   * value is deprecated.
   */
  public const BIDDING_STRATEGY_TYPE_PAGE_ONE_PROMOTED = 'PAGE_ONE_PROMOTED';
  /**
   * Percent Cpc is bidding strategy where bids are a fraction of the advertised
   * price for some good or service.
   */
  public const BIDDING_STRATEGY_TYPE_PERCENT_CPC = 'PERCENT_CPC';
  /**
   * Target CPA is an automated bid strategy that sets bids to help get as many
   * conversions as possible at the target cost-per-acquisition (CPA) you set.
   */
  public const BIDDING_STRATEGY_TYPE_TARGET_CPA = 'TARGET_CPA';
  /**
   * Target CPM is an automated bid strategy that sets bids to help get as many
   * impressions as possible at the target cost per one thousand impressions
   * (CPM) you set.
   */
  public const BIDDING_STRATEGY_TYPE_TARGET_CPM = 'TARGET_CPM';
  /**
   * An automated bidding strategy that sets bids so that a certain percentage
   * of search ads are shown at the top of the first page (or other targeted
   * location).
   */
  public const BIDDING_STRATEGY_TYPE_TARGET_IMPRESSION_SHARE = 'TARGET_IMPRESSION_SHARE';
  /**
   * Target Outrank Share is an automated bidding strategy that sets bids based
   * on the target fraction of auctions where the advertiser should outrank a
   * specific competitor. This enum value is deprecated.
   */
  public const BIDDING_STRATEGY_TYPE_TARGET_OUTRANK_SHARE = 'TARGET_OUTRANK_SHARE';
  /**
   * Target ROAS is an automated bidding strategy that helps you maximize
   * revenue while averaging a specific target Return On Average Spend (ROAS).
   */
  public const BIDDING_STRATEGY_TYPE_TARGET_ROAS = 'TARGET_ROAS';
  /**
   * Target Spend is an automated bid strategy that sets your bids to help get
   * as many clicks as possible within your budget.
   */
  public const BIDDING_STRATEGY_TYPE_TARGET_SPEND = 'TARGET_SPEND';
  /**
   * No value has been specified.
   */
  public const SERVING_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const SERVING_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Serving.
   */
  public const SERVING_STATUS_SERVING = 'SERVING';
  /**
   * None.
   */
  public const SERVING_STATUS_NONE = 'NONE';
  /**
   * Ended.
   */
  public const SERVING_STATUS_ENDED = 'ENDED';
  /**
   * Pending.
   */
  public const SERVING_STATUS_PENDING = 'PENDING';
  /**
   * Suspended.
   */
  public const SERVING_STATUS_SUSPENDED = 'SUSPENDED';
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Campaign is active and can show ads.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * Campaign has been paused by the user.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * Campaign has been removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  protected $collection_key = 'urlCustomParameters';
  /**
   * Output only. Resource name of AccessibleBiddingStrategy, a read-only view
   * of the unrestricted attributes of the attached portfolio bidding strategy
   * identified by 'bidding_strategy'. Empty, if the campaign does not use a
   * portfolio strategy. Unrestricted strategy attributes are available to all
   * customers with whom the strategy is shared and are read from the
   * AccessibleBiddingStrategy resource. In contrast, restricted attributes are
   * only available to the owner customer of the strategy and their managers.
   * Restricted attributes can only be read from the BiddingStrategy resource.
   *
   * @var string
   */
  public $accessibleBiddingStrategy;
  /**
   * The ad serving optimization status of the campaign.
   *
   * @var string
   */
  public $adServingOptimizationStatus;
  /**
   * Immutable. Optional refinement to `advertising_channel_type`. Must be a
   * valid sub-type of the parent channel type. Can be set only when creating
   * campaigns. After campaign is created, the field can not be changed.
   *
   * @var string
   */
  public $advertisingChannelSubType;
  /**
   * Immutable. The primary serving target for ads within the campaign. The
   * targeting options can be refined in `network_settings`. This field is
   * required and should not be empty when creating new campaigns. Can be set
   * only when creating campaigns. After the campaign is created, the field can
   * not be changed.
   *
   * @var string
   */
  public $advertisingChannelType;
  /**
   * The resource name of the portfolio bidding strategy used by the campaign.
   *
   * @var string
   */
  public $biddingStrategy;
  /**
   * Output only. The system status of the campaign's bidding strategy.
   *
   * @var string
   */
  public $biddingStrategySystemStatus;
  /**
   * Output only. The type of bidding strategy. A bidding strategy can be
   * created by setting either the bidding scheme to create a standard bidding
   * strategy or the `bidding_strategy` field to create a portfolio bidding
   * strategy. This field is read-only.
   *
   * @var string
   */
  public $biddingStrategyType;
  /**
   * The resource name of the campaign budget of the campaign.
   *
   * @var string
   */
  public $campaignBudget;
  /**
   * Output only. The timestamp when this campaign was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   * create_time will be deprecated in v1. Use creation_time instead.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The timestamp when this campaign was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @var string
   */
  public $creationTime;
  protected $dynamicSearchAdsSettingType = GoogleAdsSearchads360V0ResourcesCampaignDynamicSearchAdsSetting::class;
  protected $dynamicSearchAdsSettingDataType = '';
  /**
   * Output only. The resource names of effective labels attached to this
   * campaign. An effective label is a label inherited or directly assigned to
   * this campaign.
   *
   * @var string[]
   */
  public $effectiveLabels;
  /**
   * The last day of the campaign in serving customer's timezone in YYYY-MM-DD
   * format. On create, defaults to 2037-12-30, which means the campaign will
   * run indefinitely. To set an existing campaign to run indefinitely, set this
   * field to 2037-12-30.
   *
   * @var string
   */
  public $endDate;
  /**
   * Output only. ID of the campaign in the external engine account. This field
   * is for non-Google Ads account only, for example, Yahoo Japan, Microsoft,
   * Baidu etc. For Google Ads entity, use "campaign.id" instead.
   *
   * @var string
   */
  public $engineId;
  /**
   * The asset field types that should be excluded from this campaign. Asset
   * links with these field types will not be inherited by this campaign from
   * the upper level.
   *
   * @var string[]
   */
  public $excludedParentAssetFieldTypes;
  /**
   * Output only. Types of feeds that are attached directly to this campaign.
   *
   * @var string[]
   */
  public $feedTypes;
  /**
   * Suffix used to append query parameters to landing pages that are served
   * with parallel tracking.
   *
   * @var string
   */
  public $finalUrlSuffix;
  protected $frequencyCapsType = GoogleAdsSearchads360V0CommonFrequencyCapEntry::class;
  protected $frequencyCapsDataType = 'array';
  protected $geoTargetTypeSettingType = GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting::class;
  protected $geoTargetTypeSettingDataType = '';
  /**
   * Output only. The ID of the campaign.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The resource names of labels attached to this campaign.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The datetime when this campaign was last modified. The
   * datetime is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss"
   * format.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $manualCpaType = GoogleAdsSearchads360V0CommonManualCpa::class;
  protected $manualCpaDataType = '';
  protected $manualCpcType = GoogleAdsSearchads360V0CommonManualCpc::class;
  protected $manualCpcDataType = '';
  protected $manualCpmType = GoogleAdsSearchads360V0CommonManualCpm::class;
  protected $manualCpmDataType = '';
  protected $maximizeConversionValueType = GoogleAdsSearchads360V0CommonMaximizeConversionValue::class;
  protected $maximizeConversionValueDataType = '';
  protected $maximizeConversionsType = GoogleAdsSearchads360V0CommonMaximizeConversions::class;
  protected $maximizeConversionsDataType = '';
  /**
   * The name of the campaign. This field is required and should not be empty
   * when creating new campaigns. It must not contain any null (code point 0x0),
   * NL line feed (code point 0xA) or carriage return (code point 0xD)
   * characters.
   *
   * @var string
   */
  public $name;
  protected $networkSettingsType = GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings::class;
  protected $networkSettingsDataType = '';
  protected $optimizationGoalSettingType = GoogleAdsSearchads360V0ResourcesCampaignOptimizationGoalSetting::class;
  protected $optimizationGoalSettingDataType = '';
  protected $percentCpcType = GoogleAdsSearchads360V0CommonPercentCpc::class;
  protected $percentCpcDataType = '';
  protected $realTimeBiddingSettingType = GoogleAdsSearchads360V0CommonRealTimeBiddingSetting::class;
  protected $realTimeBiddingSettingDataType = '';
  /**
   * Immutable. The resource name of the campaign. Campaign resource names have
   * the form: `customers/{customer_id}/campaigns/{campaign_id}`
   *
   * @var string
   */
  public $resourceName;
  protected $selectiveOptimizationType = GoogleAdsSearchads360V0ResourcesCampaignSelectiveOptimization::class;
  protected $selectiveOptimizationDataType = '';
  /**
   * Output only. The ad serving status of the campaign.
   *
   * @var string
   */
  public $servingStatus;
  protected $shoppingSettingType = GoogleAdsSearchads360V0ResourcesCampaignShoppingSetting::class;
  protected $shoppingSettingDataType = '';
  /**
   * The date when campaign started in serving customer's timezone in YYYY-MM-DD
   * format.
   *
   * @var string
   */
  public $startDate;
  /**
   * The status of the campaign. When a new campaign is added, the status
   * defaults to ENABLED.
   *
   * @var string
   */
  public $status;
  protected $targetCpaType = GoogleAdsSearchads360V0CommonTargetCpa::class;
  protected $targetCpaDataType = '';
  protected $targetCpmType = GoogleAdsSearchads360V0CommonTargetCpm::class;
  protected $targetCpmDataType = '';
  protected $targetImpressionShareType = GoogleAdsSearchads360V0CommonTargetImpressionShare::class;
  protected $targetImpressionShareDataType = '';
  protected $targetRoasType = GoogleAdsSearchads360V0CommonTargetRoas::class;
  protected $targetRoasDataType = '';
  protected $targetSpendType = GoogleAdsSearchads360V0CommonTargetSpend::class;
  protected $targetSpendDataType = '';
  protected $trackingSettingType = GoogleAdsSearchads360V0ResourcesCampaignTrackingSetting::class;
  protected $trackingSettingDataType = '';
  /**
   * The URL template for constructing a tracking URL.
   *
   * @var string
   */
  public $trackingUrlTemplate;
  protected $urlCustomParametersType = GoogleAdsSearchads360V0CommonCustomParameter::class;
  protected $urlCustomParametersDataType = 'array';
  /**
   * Represents opting out of URL expansion to more targeted URLs. If opted out
   * (true), only the final URLs in the asset group or URLs specified in the
   * advertiser's Google Merchant Center or business data feeds are targeted. If
   * opted in (false), the entire domain will be targeted. This field can only
   * be set for Performance Max campaigns, where the default value is false.
   *
   * @var bool
   */
  public $urlExpansionOptOut;

  /**
   * Output only. Resource name of AccessibleBiddingStrategy, a read-only view
   * of the unrestricted attributes of the attached portfolio bidding strategy
   * identified by 'bidding_strategy'. Empty, if the campaign does not use a
   * portfolio strategy. Unrestricted strategy attributes are available to all
   * customers with whom the strategy is shared and are read from the
   * AccessibleBiddingStrategy resource. In contrast, restricted attributes are
   * only available to the owner customer of the strategy and their managers.
   * Restricted attributes can only be read from the BiddingStrategy resource.
   *
   * @param string $accessibleBiddingStrategy
   */
  public function setAccessibleBiddingStrategy($accessibleBiddingStrategy)
  {
    $this->accessibleBiddingStrategy = $accessibleBiddingStrategy;
  }
  /**
   * @return string
   */
  public function getAccessibleBiddingStrategy()
  {
    return $this->accessibleBiddingStrategy;
  }
  /**
   * The ad serving optimization status of the campaign.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, OPTIMIZE, CONVERSION_OPTIMIZE,
   * ROTATE, ROTATE_INDEFINITELY, UNAVAILABLE
   *
   * @param self::AD_SERVING_OPTIMIZATION_STATUS_* $adServingOptimizationStatus
   */
  public function setAdServingOptimizationStatus($adServingOptimizationStatus)
  {
    $this->adServingOptimizationStatus = $adServingOptimizationStatus;
  }
  /**
   * @return self::AD_SERVING_OPTIMIZATION_STATUS_*
   */
  public function getAdServingOptimizationStatus()
  {
    return $this->adServingOptimizationStatus;
  }
  /**
   * Immutable. Optional refinement to `advertising_channel_type`. Must be a
   * valid sub-type of the parent channel type. Can be set only when creating
   * campaigns. After campaign is created, the field can not be changed.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SEARCH_MOBILE_APP,
   * DISPLAY_MOBILE_APP, SEARCH_EXPRESS, DISPLAY_EXPRESS, SHOPPING_SMART_ADS,
   * DISPLAY_GMAIL_AD, DISPLAY_SMART_CAMPAIGN, VIDEO_OUTSTREAM, VIDEO_ACTION,
   * VIDEO_NON_SKIPPABLE, APP_CAMPAIGN, APP_CAMPAIGN_FOR_ENGAGEMENT,
   * LOCAL_CAMPAIGN, SHOPPING_COMPARISON_LISTING_ADS, SMART_CAMPAIGN,
   * VIDEO_SEQUENCE, APP_CAMPAIGN_FOR_PRE_REGISTRATION,
   * VIDEO_REACH_TARGET_FREQUENCY, TRAVEL_ACTIVITIES,
   * SOCIAL_FACEBOOK_TRACKING_ONLY
   *
   * @param self::ADVERTISING_CHANNEL_SUB_TYPE_* $advertisingChannelSubType
   */
  public function setAdvertisingChannelSubType($advertisingChannelSubType)
  {
    $this->advertisingChannelSubType = $advertisingChannelSubType;
  }
  /**
   * @return self::ADVERTISING_CHANNEL_SUB_TYPE_*
   */
  public function getAdvertisingChannelSubType()
  {
    return $this->advertisingChannelSubType;
  }
  /**
   * Immutable. The primary serving target for ads within the campaign. The
   * targeting options can be refined in `network_settings`. This field is
   * required and should not be empty when creating new campaigns. Can be set
   * only when creating campaigns. After the campaign is created, the field can
   * not be changed.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SEARCH, DISPLAY, SHOPPING, HOTEL,
   * VIDEO, MULTI_CHANNEL, LOCAL, SMART, PERFORMANCE_MAX, LOCAL_SERVICES,
   * DISCOVERY, TRAVEL, SOCIAL
   *
   * @param self::ADVERTISING_CHANNEL_TYPE_* $advertisingChannelType
   */
  public function setAdvertisingChannelType($advertisingChannelType)
  {
    $this->advertisingChannelType = $advertisingChannelType;
  }
  /**
   * @return self::ADVERTISING_CHANNEL_TYPE_*
   */
  public function getAdvertisingChannelType()
  {
    return $this->advertisingChannelType;
  }
  /**
   * The resource name of the portfolio bidding strategy used by the campaign.
   *
   * @param string $biddingStrategy
   */
  public function setBiddingStrategy($biddingStrategy)
  {
    $this->biddingStrategy = $biddingStrategy;
  }
  /**
   * @return string
   */
  public function getBiddingStrategy()
  {
    return $this->biddingStrategy;
  }
  /**
   * Output only. The system status of the campaign's bidding strategy.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, LEARNING_NEW,
   * LEARNING_SETTING_CHANGE, LEARNING_BUDGET_CHANGE,
   * LEARNING_COMPOSITION_CHANGE, LEARNING_CONVERSION_TYPE_CHANGE,
   * LEARNING_CONVERSION_SETTING_CHANGE, LIMITED_BY_CPC_BID_CEILING,
   * LIMITED_BY_CPC_BID_FLOOR, LIMITED_BY_DATA, LIMITED_BY_BUDGET,
   * LIMITED_BY_LOW_PRIORITY_SPEND, LIMITED_BY_LOW_QUALITY,
   * LIMITED_BY_INVENTORY, MISCONFIGURED_ZERO_ELIGIBILITY,
   * MISCONFIGURED_CONVERSION_TYPES, MISCONFIGURED_CONVERSION_SETTINGS,
   * MISCONFIGURED_SHARED_BUDGET, MISCONFIGURED_STRATEGY_TYPE, PAUSED,
   * UNAVAILABLE, MULTIPLE_LEARNING, MULTIPLE_LIMITED, MULTIPLE_MISCONFIGURED,
   * MULTIPLE
   *
   * @param self::BIDDING_STRATEGY_SYSTEM_STATUS_* $biddingStrategySystemStatus
   */
  public function setBiddingStrategySystemStatus($biddingStrategySystemStatus)
  {
    $this->biddingStrategySystemStatus = $biddingStrategySystemStatus;
  }
  /**
   * @return self::BIDDING_STRATEGY_SYSTEM_STATUS_*
   */
  public function getBiddingStrategySystemStatus()
  {
    return $this->biddingStrategySystemStatus;
  }
  /**
   * Output only. The type of bidding strategy. A bidding strategy can be
   * created by setting either the bidding scheme to create a standard bidding
   * strategy or the `bidding_strategy` field to create a portfolio bidding
   * strategy. This field is read-only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, COMMISSION, ENHANCED_CPC, INVALID,
   * MANUAL_CPA, MANUAL_CPC, MANUAL_CPM, MANUAL_CPV, MAXIMIZE_CONVERSIONS,
   * MAXIMIZE_CONVERSION_VALUE, PAGE_ONE_PROMOTED, PERCENT_CPC, TARGET_CPA,
   * TARGET_CPM, TARGET_IMPRESSION_SHARE, TARGET_OUTRANK_SHARE, TARGET_ROAS,
   * TARGET_SPEND
   *
   * @param self::BIDDING_STRATEGY_TYPE_* $biddingStrategyType
   */
  public function setBiddingStrategyType($biddingStrategyType)
  {
    $this->biddingStrategyType = $biddingStrategyType;
  }
  /**
   * @return self::BIDDING_STRATEGY_TYPE_*
   */
  public function getBiddingStrategyType()
  {
    return $this->biddingStrategyType;
  }
  /**
   * The resource name of the campaign budget of the campaign.
   *
   * @param string $campaignBudget
   */
  public function setCampaignBudget($campaignBudget)
  {
    $this->campaignBudget = $campaignBudget;
  }
  /**
   * @return string
   */
  public function getCampaignBudget()
  {
    return $this->campaignBudget;
  }
  /**
   * Output only. The timestamp when this campaign was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   * create_time will be deprecated in v1. Use creation_time instead.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The timestamp when this campaign was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
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
   * The setting for controlling Dynamic Search Ads (DSA).
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignDynamicSearchAdsSetting $dynamicSearchAdsSetting
   */
  public function setDynamicSearchAdsSetting(GoogleAdsSearchads360V0ResourcesCampaignDynamicSearchAdsSetting $dynamicSearchAdsSetting)
  {
    $this->dynamicSearchAdsSetting = $dynamicSearchAdsSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignDynamicSearchAdsSetting
   */
  public function getDynamicSearchAdsSetting()
  {
    return $this->dynamicSearchAdsSetting;
  }
  /**
   * Output only. The resource names of effective labels attached to this
   * campaign. An effective label is a label inherited or directly assigned to
   * this campaign.
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
   * The last day of the campaign in serving customer's timezone in YYYY-MM-DD
   * format. On create, defaults to 2037-12-30, which means the campaign will
   * run indefinitely. To set an existing campaign to run indefinitely, set this
   * field to 2037-12-30.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Output only. ID of the campaign in the external engine account. This field
   * is for non-Google Ads account only, for example, Yahoo Japan, Microsoft,
   * Baidu etc. For Google Ads entity, use "campaign.id" instead.
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
   * The asset field types that should be excluded from this campaign. Asset
   * links with these field types will not be inherited by this campaign from
   * the upper level.
   *
   * @param string[] $excludedParentAssetFieldTypes
   */
  public function setExcludedParentAssetFieldTypes($excludedParentAssetFieldTypes)
  {
    $this->excludedParentAssetFieldTypes = $excludedParentAssetFieldTypes;
  }
  /**
   * @return string[]
   */
  public function getExcludedParentAssetFieldTypes()
  {
    return $this->excludedParentAssetFieldTypes;
  }
  /**
   * Output only. Types of feeds that are attached directly to this campaign.
   *
   * @param string[] $feedTypes
   */
  public function setFeedTypes($feedTypes)
  {
    $this->feedTypes = $feedTypes;
  }
  /**
   * @return string[]
   */
  public function getFeedTypes()
  {
    return $this->feedTypes;
  }
  /**
   * Suffix used to append query parameters to landing pages that are served
   * with parallel tracking.
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
   * A list that limits how often each user will see this campaign's ads.
   *
   * @param GoogleAdsSearchads360V0CommonFrequencyCapEntry[] $frequencyCaps
   */
  public function setFrequencyCaps($frequencyCaps)
  {
    $this->frequencyCaps = $frequencyCaps;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonFrequencyCapEntry[]
   */
  public function getFrequencyCaps()
  {
    return $this->frequencyCaps;
  }
  /**
   * The setting for ads geotargeting.
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting $geoTargetTypeSetting
   */
  public function setGeoTargetTypeSetting(GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting $geoTargetTypeSetting)
  {
    $this->geoTargetTypeSetting = $geoTargetTypeSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting
   */
  public function getGeoTargetTypeSetting()
  {
    return $this->geoTargetTypeSetting;
  }
  /**
   * Output only. The ID of the campaign.
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
   * Output only. The resource names of labels attached to this campaign.
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
   * Output only. The datetime when this campaign was last modified. The
   * datetime is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss"
   * format.
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
   * Standard Manual CPA bidding strategy. Manual bidding strategy that allows
   * advertiser to set the bid per advertiser-specified action. Supported only
   * for Local Services campaigns.
   *
   * @param GoogleAdsSearchads360V0CommonManualCpa $manualCpa
   */
  public function setManualCpa(GoogleAdsSearchads360V0CommonManualCpa $manualCpa)
  {
    $this->manualCpa = $manualCpa;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonManualCpa
   */
  public function getManualCpa()
  {
    return $this->manualCpa;
  }
  /**
   * Standard Manual CPC bidding strategy. Manual click-based bidding where user
   * pays per click.
   *
   * @param GoogleAdsSearchads360V0CommonManualCpc $manualCpc
   */
  public function setManualCpc(GoogleAdsSearchads360V0CommonManualCpc $manualCpc)
  {
    $this->manualCpc = $manualCpc;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonManualCpc
   */
  public function getManualCpc()
  {
    return $this->manualCpc;
  }
  /**
   * Standard Manual CPM bidding strategy. Manual impression-based bidding where
   * user pays per thousand impressions.
   *
   * @param GoogleAdsSearchads360V0CommonManualCpm $manualCpm
   */
  public function setManualCpm(GoogleAdsSearchads360V0CommonManualCpm $manualCpm)
  {
    $this->manualCpm = $manualCpm;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonManualCpm
   */
  public function getManualCpm()
  {
    return $this->manualCpm;
  }
  /**
   * Standard Maximize Conversion Value bidding strategy that automatically sets
   * bids to maximize revenue while spending your budget.
   *
   * @param GoogleAdsSearchads360V0CommonMaximizeConversionValue $maximizeConversionValue
   */
  public function setMaximizeConversionValue(GoogleAdsSearchads360V0CommonMaximizeConversionValue $maximizeConversionValue)
  {
    $this->maximizeConversionValue = $maximizeConversionValue;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonMaximizeConversionValue
   */
  public function getMaximizeConversionValue()
  {
    return $this->maximizeConversionValue;
  }
  /**
   * Standard Maximize Conversions bidding strategy that automatically maximizes
   * number of conversions while spending your budget.
   *
   * @param GoogleAdsSearchads360V0CommonMaximizeConversions $maximizeConversions
   */
  public function setMaximizeConversions(GoogleAdsSearchads360V0CommonMaximizeConversions $maximizeConversions)
  {
    $this->maximizeConversions = $maximizeConversions;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonMaximizeConversions
   */
  public function getMaximizeConversions()
  {
    return $this->maximizeConversions;
  }
  /**
   * The name of the campaign. This field is required and should not be empty
   * when creating new campaigns. It must not contain any null (code point 0x0),
   * NL line feed (code point 0xA) or carriage return (code point 0xD)
   * characters.
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
   * The network settings for the campaign.
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings $networkSettings
   */
  public function setNetworkSettings(GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings $networkSettings)
  {
    $this->networkSettings = $networkSettings;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignNetworkSettings
   */
  public function getNetworkSettings()
  {
    return $this->networkSettings;
  }
  /**
   * Optimization goal setting for this campaign, which includes a set of
   * optimization goal types.
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignOptimizationGoalSetting $optimizationGoalSetting
   */
  public function setOptimizationGoalSetting(GoogleAdsSearchads360V0ResourcesCampaignOptimizationGoalSetting $optimizationGoalSetting)
  {
    $this->optimizationGoalSetting = $optimizationGoalSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignOptimizationGoalSetting
   */
  public function getOptimizationGoalSetting()
  {
    return $this->optimizationGoalSetting;
  }
  /**
   * Standard Percent Cpc bidding strategy where bids are a fraction of the
   * advertised price for some good or service.
   *
   * @param GoogleAdsSearchads360V0CommonPercentCpc $percentCpc
   */
  public function setPercentCpc(GoogleAdsSearchads360V0CommonPercentCpc $percentCpc)
  {
    $this->percentCpc = $percentCpc;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonPercentCpc
   */
  public function getPercentCpc()
  {
    return $this->percentCpc;
  }
  /**
   * Settings for Real-Time Bidding, a feature only available for campaigns
   * targeting the Ad Exchange network.
   *
   * @param GoogleAdsSearchads360V0CommonRealTimeBiddingSetting $realTimeBiddingSetting
   */
  public function setRealTimeBiddingSetting(GoogleAdsSearchads360V0CommonRealTimeBiddingSetting $realTimeBiddingSetting)
  {
    $this->realTimeBiddingSetting = $realTimeBiddingSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonRealTimeBiddingSetting
   */
  public function getRealTimeBiddingSetting()
  {
    return $this->realTimeBiddingSetting;
  }
  /**
   * Immutable. The resource name of the campaign. Campaign resource names have
   * the form: `customers/{customer_id}/campaigns/{campaign_id}`
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
   * Selective optimization setting for this campaign, which includes a set of
   * conversion actions to optimize this campaign towards. This feature only
   * applies to app campaigns that use MULTI_CHANNEL as AdvertisingChannelType
   * and APP_CAMPAIGN or APP_CAMPAIGN_FOR_ENGAGEMENT as
   * AdvertisingChannelSubType.
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignSelectiveOptimization $selectiveOptimization
   */
  public function setSelectiveOptimization(GoogleAdsSearchads360V0ResourcesCampaignSelectiveOptimization $selectiveOptimization)
  {
    $this->selectiveOptimization = $selectiveOptimization;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignSelectiveOptimization
   */
  public function getSelectiveOptimization()
  {
    return $this->selectiveOptimization;
  }
  /**
   * Output only. The ad serving status of the campaign.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SERVING, NONE, ENDED, PENDING,
   * SUSPENDED
   *
   * @param self::SERVING_STATUS_* $servingStatus
   */
  public function setServingStatus($servingStatus)
  {
    $this->servingStatus = $servingStatus;
  }
  /**
   * @return self::SERVING_STATUS_*
   */
  public function getServingStatus()
  {
    return $this->servingStatus;
  }
  /**
   * The setting for controlling Shopping campaigns.
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignShoppingSetting $shoppingSetting
   */
  public function setShoppingSetting(GoogleAdsSearchads360V0ResourcesCampaignShoppingSetting $shoppingSetting)
  {
    $this->shoppingSetting = $shoppingSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignShoppingSetting
   */
  public function getShoppingSetting()
  {
    return $this->shoppingSetting;
  }
  /**
   * The date when campaign started in serving customer's timezone in YYYY-MM-DD
   * format.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * The status of the campaign. When a new campaign is added, the status
   * defaults to ENABLED.
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
   * Standard Target CPA bidding strategy that automatically sets bids to help
   * get as many conversions as possible at the target cost-per-acquisition
   * (CPA) you set.
   *
   * @param GoogleAdsSearchads360V0CommonTargetCpa $targetCpa
   */
  public function setTargetCpa(GoogleAdsSearchads360V0CommonTargetCpa $targetCpa)
  {
    $this->targetCpa = $targetCpa;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetCpa
   */
  public function getTargetCpa()
  {
    return $this->targetCpa;
  }
  /**
   * A bidding strategy that automatically optimizes cost per thousand
   * impressions.
   *
   * @param GoogleAdsSearchads360V0CommonTargetCpm $targetCpm
   */
  public function setTargetCpm(GoogleAdsSearchads360V0CommonTargetCpm $targetCpm)
  {
    $this->targetCpm = $targetCpm;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetCpm
   */
  public function getTargetCpm()
  {
    return $this->targetCpm;
  }
  /**
   * Target Impression Share bidding strategy. An automated bidding strategy
   * that sets bids to achieve a chosen percentage of impressions.
   *
   * @param GoogleAdsSearchads360V0CommonTargetImpressionShare $targetImpressionShare
   */
  public function setTargetImpressionShare(GoogleAdsSearchads360V0CommonTargetImpressionShare $targetImpressionShare)
  {
    $this->targetImpressionShare = $targetImpressionShare;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetImpressionShare
   */
  public function getTargetImpressionShare()
  {
    return $this->targetImpressionShare;
  }
  /**
   * Standard Target ROAS bidding strategy that automatically maximizes revenue
   * while averaging a specific target return on ad spend (ROAS).
   *
   * @param GoogleAdsSearchads360V0CommonTargetRoas $targetRoas
   */
  public function setTargetRoas(GoogleAdsSearchads360V0CommonTargetRoas $targetRoas)
  {
    $this->targetRoas = $targetRoas;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetRoas
   */
  public function getTargetRoas()
  {
    return $this->targetRoas;
  }
  /**
   * Standard Target Spend bidding strategy that automatically sets your bids to
   * help get as many clicks as possible within your budget.
   *
   * @param GoogleAdsSearchads360V0CommonTargetSpend $targetSpend
   */
  public function setTargetSpend(GoogleAdsSearchads360V0CommonTargetSpend $targetSpend)
  {
    $this->targetSpend = $targetSpend;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetSpend
   */
  public function getTargetSpend()
  {
    return $this->targetSpend;
  }
  /**
   * Output only. Campaign-level settings for tracking information.
   *
   * @param GoogleAdsSearchads360V0ResourcesCampaignTrackingSetting $trackingSetting
   */
  public function setTrackingSetting(GoogleAdsSearchads360V0ResourcesCampaignTrackingSetting $trackingSetting)
  {
    $this->trackingSetting = $trackingSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesCampaignTrackingSetting
   */
  public function getTrackingSetting()
  {
    return $this->trackingSetting;
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
   * Represents opting out of URL expansion to more targeted URLs. If opted out
   * (true), only the final URLs in the asset group or URLs specified in the
   * advertiser's Google Merchant Center or business data feeds are targeted. If
   * opted in (false), the entire domain will be targeted. This field can only
   * be set for Performance Max campaigns, where the default value is false.
   *
   * @param bool $urlExpansionOptOut
   */
  public function setUrlExpansionOptOut($urlExpansionOptOut)
  {
    $this->urlExpansionOptOut = $urlExpansionOptOut;
  }
  /**
   * @return bool
   */
  public function getUrlExpansionOptOut()
  {
    return $this->urlExpansionOptOut;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCampaign::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCampaign');
