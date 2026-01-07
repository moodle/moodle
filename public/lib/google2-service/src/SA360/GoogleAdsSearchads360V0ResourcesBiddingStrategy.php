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

class GoogleAdsSearchads360V0ResourcesBiddingStrategy extends \Google\Model
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
   * The bidding strategy is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The bidding strategy is removed.
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
   * Commission is an automatic bidding strategy in which the advertiser pays a
   * certain portion of the conversion value.
   */
  public const TYPE_COMMISSION = 'COMMISSION';
  /**
   * Enhanced CPC is a bidding strategy that raises bids for clicks that seem
   * more likely to lead to a conversion and lowers them for clicks where they
   * seem less likely.
   */
  public const TYPE_ENHANCED_CPC = 'ENHANCED_CPC';
  /**
   * Used for return value only. Indicates that a campaign does not have a
   * bidding strategy. This prevents the campaign from serving. For example, a
   * campaign may be attached to a manager bidding strategy and the serving
   * account is subsequently unlinked from the manager account. In this case the
   * campaign will automatically be detached from the now inaccessible manager
   * bidding strategy and transition to the INVALID bidding strategy type.
   */
  public const TYPE_INVALID = 'INVALID';
  /**
   * Manual bidding strategy that allows advertiser to set the bid per
   * advertiser-specified action.
   */
  public const TYPE_MANUAL_CPA = 'MANUAL_CPA';
  /**
   * Manual click based bidding where user pays per click.
   */
  public const TYPE_MANUAL_CPC = 'MANUAL_CPC';
  /**
   * Manual impression based bidding where user pays per thousand impressions.
   */
  public const TYPE_MANUAL_CPM = 'MANUAL_CPM';
  /**
   * A bidding strategy that pays a configurable amount per video view.
   */
  public const TYPE_MANUAL_CPV = 'MANUAL_CPV';
  /**
   * A bidding strategy that automatically maximizes number of conversions given
   * a daily budget.
   */
  public const TYPE_MAXIMIZE_CONVERSIONS = 'MAXIMIZE_CONVERSIONS';
  /**
   * An automated bidding strategy that automatically sets bids to maximize
   * revenue while spending your budget.
   */
  public const TYPE_MAXIMIZE_CONVERSION_VALUE = 'MAXIMIZE_CONVERSION_VALUE';
  /**
   * Page-One Promoted bidding scheme, which sets max cpc bids to target
   * impressions on page one or page one promoted slots on google.com. This enum
   * value is deprecated.
   */
  public const TYPE_PAGE_ONE_PROMOTED = 'PAGE_ONE_PROMOTED';
  /**
   * Percent Cpc is bidding strategy where bids are a fraction of the advertised
   * price for some good or service.
   */
  public const TYPE_PERCENT_CPC = 'PERCENT_CPC';
  /**
   * Target CPA is an automated bid strategy that sets bids to help get as many
   * conversions as possible at the target cost-per-acquisition (CPA) you set.
   */
  public const TYPE_TARGET_CPA = 'TARGET_CPA';
  /**
   * Target CPM is an automated bid strategy that sets bids to help get as many
   * impressions as possible at the target cost per one thousand impressions
   * (CPM) you set.
   */
  public const TYPE_TARGET_CPM = 'TARGET_CPM';
  /**
   * An automated bidding strategy that sets bids so that a certain percentage
   * of search ads are shown at the top of the first page (or other targeted
   * location).
   */
  public const TYPE_TARGET_IMPRESSION_SHARE = 'TARGET_IMPRESSION_SHARE';
  /**
   * Target Outrank Share is an automated bidding strategy that sets bids based
   * on the target fraction of auctions where the advertiser should outrank a
   * specific competitor. This enum value is deprecated.
   */
  public const TYPE_TARGET_OUTRANK_SHARE = 'TARGET_OUTRANK_SHARE';
  /**
   * Target ROAS is an automated bidding strategy that helps you maximize
   * revenue while averaging a specific target Return On Average Spend (ROAS).
   */
  public const TYPE_TARGET_ROAS = 'TARGET_ROAS';
  /**
   * Target Spend is an automated bid strategy that sets your bids to help get
   * as many clicks as possible within your budget.
   */
  public const TYPE_TARGET_SPEND = 'TARGET_SPEND';
  /**
   * Output only. The number of campaigns attached to this bidding strategy.
   * This field is read-only.
   *
   * @var string
   */
  public $campaignCount;
  /**
   * Immutable. The currency used by the bidding strategy (ISO 4217 three-letter
   * code). For bidding strategies in manager customers, this currency can be
   * set on creation and defaults to the manager customer's currency. For
   * serving customers, this field cannot be set; all strategies in a serving
   * customer implicitly use the serving customer's currency. In all cases the
   * effective_currency_code field returns the currency used by the strategy.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Output only. The currency used by the bidding strategy (ISO 4217 three-
   * letter code). For bidding strategies in manager customers, this is the
   * currency set by the advertiser when creating the strategy. For serving
   * customers, this is the customer's currency_code. Bidding strategy metrics
   * are reported in this currency. This field is read-only.
   *
   * @var string
   */
  public $effectiveCurrencyCode;
  protected $enhancedCpcType = GoogleAdsSearchads360V0CommonEnhancedCpc::class;
  protected $enhancedCpcDataType = '';
  /**
   * Output only. The ID of the bidding strategy.
   *
   * @var string
   */
  public $id;
  protected $maximizeConversionValueType = GoogleAdsSearchads360V0CommonMaximizeConversionValue::class;
  protected $maximizeConversionValueDataType = '';
  protected $maximizeConversionsType = GoogleAdsSearchads360V0CommonMaximizeConversions::class;
  protected $maximizeConversionsDataType = '';
  /**
   * The name of the bidding strategy. All bidding strategies within an account
   * must be named distinctly. The length of this string should be between 1 and
   * 255, inclusive, in UTF-8 bytes, (trimmed).
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of non-removed campaigns attached to this bidding
   * strategy. This field is read-only.
   *
   * @var string
   */
  public $nonRemovedCampaignCount;
  /**
   * Immutable. The resource name of the bidding strategy. Bidding strategy
   * resource names have the form:
   * `customers/{customer_id}/biddingStrategies/{bidding_strategy_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The status of the bidding strategy. This field is read-only.
   *
   * @var string
   */
  public $status;
  protected $targetCpaType = GoogleAdsSearchads360V0CommonTargetCpa::class;
  protected $targetCpaDataType = '';
  protected $targetImpressionShareType = GoogleAdsSearchads360V0CommonTargetImpressionShare::class;
  protected $targetImpressionShareDataType = '';
  protected $targetOutrankShareType = GoogleAdsSearchads360V0CommonTargetOutrankShare::class;
  protected $targetOutrankShareDataType = '';
  protected $targetRoasType = GoogleAdsSearchads360V0CommonTargetRoas::class;
  protected $targetRoasDataType = '';
  protected $targetSpendType = GoogleAdsSearchads360V0CommonTargetSpend::class;
  protected $targetSpendDataType = '';
  /**
   * Output only. The type of the bidding strategy. Create a bidding strategy by
   * setting the bidding scheme. This field is read-only.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The number of campaigns attached to this bidding strategy.
   * This field is read-only.
   *
   * @param string $campaignCount
   */
  public function setCampaignCount($campaignCount)
  {
    $this->campaignCount = $campaignCount;
  }
  /**
   * @return string
   */
  public function getCampaignCount()
  {
    return $this->campaignCount;
  }
  /**
   * Immutable. The currency used by the bidding strategy (ISO 4217 three-letter
   * code). For bidding strategies in manager customers, this currency can be
   * set on creation and defaults to the manager customer's currency. For
   * serving customers, this field cannot be set; all strategies in a serving
   * customer implicitly use the serving customer's currency. In all cases the
   * effective_currency_code field returns the currency used by the strategy.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Output only. The currency used by the bidding strategy (ISO 4217 three-
   * letter code). For bidding strategies in manager customers, this is the
   * currency set by the advertiser when creating the strategy. For serving
   * customers, this is the customer's currency_code. Bidding strategy metrics
   * are reported in this currency. This field is read-only.
   *
   * @param string $effectiveCurrencyCode
   */
  public function setEffectiveCurrencyCode($effectiveCurrencyCode)
  {
    $this->effectiveCurrencyCode = $effectiveCurrencyCode;
  }
  /**
   * @return string
   */
  public function getEffectiveCurrencyCode()
  {
    return $this->effectiveCurrencyCode;
  }
  /**
   * A bidding strategy that raises bids for clicks that seem more likely to
   * lead to a conversion and lowers them for clicks where they seem less
   * likely.
   *
   * @param GoogleAdsSearchads360V0CommonEnhancedCpc $enhancedCpc
   */
  public function setEnhancedCpc(GoogleAdsSearchads360V0CommonEnhancedCpc $enhancedCpc)
  {
    $this->enhancedCpc = $enhancedCpc;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonEnhancedCpc
   */
  public function getEnhancedCpc()
  {
    return $this->enhancedCpc;
  }
  /**
   * Output only. The ID of the bidding strategy.
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
   * An automated bidding strategy to help get the most conversion value for
   * your campaigns while spending your budget.
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
   * An automated bidding strategy to help get the most conversions for your
   * campaigns while spending your budget.
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
   * The name of the bidding strategy. All bidding strategies within an account
   * must be named distinctly. The length of this string should be between 1 and
   * 255, inclusive, in UTF-8 bytes, (trimmed).
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
   * Output only. The number of non-removed campaigns attached to this bidding
   * strategy. This field is read-only.
   *
   * @param string $nonRemovedCampaignCount
   */
  public function setNonRemovedCampaignCount($nonRemovedCampaignCount)
  {
    $this->nonRemovedCampaignCount = $nonRemovedCampaignCount;
  }
  /**
   * @return string
   */
  public function getNonRemovedCampaignCount()
  {
    return $this->nonRemovedCampaignCount;
  }
  /**
   * Immutable. The resource name of the bidding strategy. Bidding strategy
   * resource names have the form:
   * `customers/{customer_id}/biddingStrategies/{bidding_strategy_id}`
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
   * Output only. The status of the bidding strategy. This field is read-only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED
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
   * A bidding strategy that sets bids to help get as many conversions as
   * possible at the target cost-per-acquisition (CPA) you set.
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
   * A bidding strategy that automatically optimizes towards a chosen percentage
   * of impressions.
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
   * A bidding strategy that sets bids based on the target fraction of auctions
   * where the advertiser should outrank a specific competitor. This field is
   * deprecated. Creating a new bidding strategy with this field or attaching
   * bidding strategies with this field to a campaign will fail. Mutates to
   * strategies that already have this scheme populated are allowed.
   *
   * @param GoogleAdsSearchads360V0CommonTargetOutrankShare $targetOutrankShare
   */
  public function setTargetOutrankShare(GoogleAdsSearchads360V0CommonTargetOutrankShare $targetOutrankShare)
  {
    $this->targetOutrankShare = $targetOutrankShare;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetOutrankShare
   */
  public function getTargetOutrankShare()
  {
    return $this->targetOutrankShare;
  }
  /**
   * A bidding strategy that helps you maximize revenue while averaging a
   * specific target Return On Ad Spend (ROAS).
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
   * A bid strategy that sets your bids to help get as many clicks as possible
   * within your budget.
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
   * Output only. The type of the bidding strategy. Create a bidding strategy by
   * setting the bidding scheme. This field is read-only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, COMMISSION, ENHANCED_CPC, INVALID,
   * MANUAL_CPA, MANUAL_CPC, MANUAL_CPM, MANUAL_CPV, MAXIMIZE_CONVERSIONS,
   * MAXIMIZE_CONVERSION_VALUE, PAGE_ONE_PROMOTED, PERCENT_CPC, TARGET_CPA,
   * TARGET_CPM, TARGET_IMPRESSION_SHARE, TARGET_OUTRANK_SHARE, TARGET_ROAS,
   * TARGET_SPEND
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
class_alias(GoogleAdsSearchads360V0ResourcesBiddingStrategy::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesBiddingStrategy');
