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

class GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategy extends \Google\Model
{
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
   * Output only. The ID of the bidding strategy.
   *
   * @var string
   */
  public $id;
  protected $maximizeConversionValueType = GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue::class;
  protected $maximizeConversionValueDataType = '';
  protected $maximizeConversionsType = GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions::class;
  protected $maximizeConversionsDataType = '';
  /**
   * Output only. The name of the bidding strategy.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The ID of the Customer which owns the bidding strategy.
   *
   * @var string
   */
  public $ownerCustomerId;
  /**
   * Output only. descriptive_name of the Customer which owns the bidding
   * strategy.
   *
   * @var string
   */
  public $ownerDescriptiveName;
  /**
   * Output only. The resource name of the accessible bidding strategy.
   * AccessibleBiddingStrategy resource names have the form:
   * `customers/{customer_id}/accessibleBiddingStrategies/{bidding_strategy_id}`
   *
   * @var string
   */
  public $resourceName;
  protected $targetCpaType = GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetCpa::class;
  protected $targetCpaDataType = '';
  protected $targetImpressionShareType = GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetImpressionShare::class;
  protected $targetImpressionShareDataType = '';
  protected $targetRoasType = GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetRoas::class;
  protected $targetRoasDataType = '';
  protected $targetSpendType = GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend::class;
  protected $targetSpendDataType = '';
  /**
   * Output only. The type of the bidding strategy.
   *
   * @var string
   */
  public $type;

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
   * Output only. An automated bidding strategy to help get the most conversion
   * value for your campaigns while spending your budget.
   *
   * @param GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue $maximizeConversionValue
   */
  public function setMaximizeConversionValue(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue $maximizeConversionValue)
  {
    $this->maximizeConversionValue = $maximizeConversionValue;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue
   */
  public function getMaximizeConversionValue()
  {
    return $this->maximizeConversionValue;
  }
  /**
   * Output only. An automated bidding strategy to help get the most conversions
   * for your campaigns while spending your budget.
   *
   * @param GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions $maximizeConversions
   */
  public function setMaximizeConversions(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions $maximizeConversions)
  {
    $this->maximizeConversions = $maximizeConversions;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions
   */
  public function getMaximizeConversions()
  {
    return $this->maximizeConversions;
  }
  /**
   * Output only. The name of the bidding strategy.
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
   * Output only. The ID of the Customer which owns the bidding strategy.
   *
   * @param string $ownerCustomerId
   */
  public function setOwnerCustomerId($ownerCustomerId)
  {
    $this->ownerCustomerId = $ownerCustomerId;
  }
  /**
   * @return string
   */
  public function getOwnerCustomerId()
  {
    return $this->ownerCustomerId;
  }
  /**
   * Output only. descriptive_name of the Customer which owns the bidding
   * strategy.
   *
   * @param string $ownerDescriptiveName
   */
  public function setOwnerDescriptiveName($ownerDescriptiveName)
  {
    $this->ownerDescriptiveName = $ownerDescriptiveName;
  }
  /**
   * @return string
   */
  public function getOwnerDescriptiveName()
  {
    return $this->ownerDescriptiveName;
  }
  /**
   * Output only. The resource name of the accessible bidding strategy.
   * AccessibleBiddingStrategy resource names have the form:
   * `customers/{customer_id}/accessibleBiddingStrategies/{bidding_strategy_id}`
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
   * Output only. A bidding strategy that sets bids to help get as many
   * conversions as possible at the target cost-per-acquisition (CPA) you set.
   *
   * @param GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetCpa $targetCpa
   */
  public function setTargetCpa(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetCpa $targetCpa)
  {
    $this->targetCpa = $targetCpa;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetCpa
   */
  public function getTargetCpa()
  {
    return $this->targetCpa;
  }
  /**
   * Output only. A bidding strategy that automatically optimizes towards a
   * chosen percentage of impressions.
   *
   * @param GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetImpressionShare $targetImpressionShare
   */
  public function setTargetImpressionShare(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetImpressionShare $targetImpressionShare)
  {
    $this->targetImpressionShare = $targetImpressionShare;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetImpressionShare
   */
  public function getTargetImpressionShare()
  {
    return $this->targetImpressionShare;
  }
  /**
   * Output only. A bidding strategy that helps you maximize revenue while
   * averaging a specific target Return On Ad Spend (ROAS).
   *
   * @param GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetRoas $targetRoas
   */
  public function setTargetRoas(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetRoas $targetRoas)
  {
    $this->targetRoas = $targetRoas;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetRoas
   */
  public function getTargetRoas()
  {
    return $this->targetRoas;
  }
  /**
   * Output only. A bid strategy that sets your bids to help get as many clicks
   * as possible within your budget.
   *
   * @param GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend $targetSpend
   */
  public function setTargetSpend(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend $targetSpend)
  {
    $this->targetSpend = $targetSpend;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyTargetSpend
   */
  public function getTargetSpend()
  {
    return $this->targetSpend;
  }
  /**
   * Output only. The type of the bidding strategy.
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
class_alias(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategy::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategy');
