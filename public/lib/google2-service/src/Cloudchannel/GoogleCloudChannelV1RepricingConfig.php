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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1RepricingConfig extends \Google\Collection
{
  /**
   * Not used.
   */
  public const REBILLING_BASIS_REBILLING_BASIS_UNSPECIFIED = 'REBILLING_BASIS_UNSPECIFIED';
  /**
   * Use the list cost, also known as the MSRP.
   */
  public const REBILLING_BASIS_COST_AT_LIST = 'COST_AT_LIST';
  /**
   * Pass through all discounts except the Reseller Program Discount. If this is
   * the default cost base and no adjustments are specified, the output cost
   * will be exactly what the customer would see if they viewed the bill in the
   * Google Cloud Console.
   */
  public const REBILLING_BASIS_DIRECT_CUSTOMER_COST = 'DIRECT_CUSTOMER_COST';
  protected $collection_key = 'conditionalOverrides';
  protected $adjustmentType = GoogleCloudChannelV1RepricingAdjustment::class;
  protected $adjustmentDataType = '';
  protected $channelPartnerGranularityType = GoogleCloudChannelV1RepricingConfigChannelPartnerGranularity::class;
  protected $channelPartnerGranularityDataType = '';
  protected $conditionalOverridesType = GoogleCloudChannelV1ConditionalOverride::class;
  protected $conditionalOverridesDataType = 'array';
  protected $effectiveInvoiceMonthType = GoogleTypeDate::class;
  protected $effectiveInvoiceMonthDataType = '';
  protected $entitlementGranularityType = GoogleCloudChannelV1RepricingConfigEntitlementGranularity::class;
  protected $entitlementGranularityDataType = '';
  /**
   * Required. The RebillingBasis to use for this bill. Specifies the relative
   * cost based on repricing costs you will apply.
   *
   * @var string
   */
  public $rebillingBasis;

  /**
   * Required. Information about the adjustment.
   *
   * @param GoogleCloudChannelV1RepricingAdjustment $adjustment
   */
  public function setAdjustment(GoogleCloudChannelV1RepricingAdjustment $adjustment)
  {
    $this->adjustment = $adjustment;
  }
  /**
   * @return GoogleCloudChannelV1RepricingAdjustment
   */
  public function getAdjustment()
  {
    return $this->adjustment;
  }
  /**
   * Applies the repricing configuration at the channel partner level. Only
   * ChannelPartnerRepricingConfig supports this value. Deprecated: This is no
   * longer supported. Use RepricingConfig.entitlement_granularity instead.
   *
   * @deprecated
   * @param GoogleCloudChannelV1RepricingConfigChannelPartnerGranularity $channelPartnerGranularity
   */
  public function setChannelPartnerGranularity(GoogleCloudChannelV1RepricingConfigChannelPartnerGranularity $channelPartnerGranularity)
  {
    $this->channelPartnerGranularity = $channelPartnerGranularity;
  }
  /**
   * @deprecated
   * @return GoogleCloudChannelV1RepricingConfigChannelPartnerGranularity
   */
  public function getChannelPartnerGranularity()
  {
    return $this->channelPartnerGranularity;
  }
  /**
   * The conditional overrides to apply for this configuration. If you list
   * multiple overrides, only the first valid override is used. If you don't
   * list any overrides, the API uses the normal adjustment and rebilling basis.
   *
   * @param GoogleCloudChannelV1ConditionalOverride[] $conditionalOverrides
   */
  public function setConditionalOverrides($conditionalOverrides)
  {
    $this->conditionalOverrides = $conditionalOverrides;
  }
  /**
   * @return GoogleCloudChannelV1ConditionalOverride[]
   */
  public function getConditionalOverrides()
  {
    return $this->conditionalOverrides;
  }
  /**
   * Required. The YearMonth when these adjustments activate. The Day field
   * needs to be "0" since we only accept YearMonth repricing boundaries.
   *
   * @param GoogleTypeDate $effectiveInvoiceMonth
   */
  public function setEffectiveInvoiceMonth(GoogleTypeDate $effectiveInvoiceMonth)
  {
    $this->effectiveInvoiceMonth = $effectiveInvoiceMonth;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getEffectiveInvoiceMonth()
  {
    return $this->effectiveInvoiceMonth;
  }
  /**
   * Required. Applies the repricing configuration at the entitlement level.
   * Note: If a ChannelPartnerRepricingConfig using
   * RepricingConfig.EntitlementGranularity becomes effective, then no existing
   * or future RepricingConfig.ChannelPartnerGranularity will apply to the
   * RepricingConfig.EntitlementGranularity.entitlement. This is the recommended
   * value for both CustomerRepricingConfig and ChannelPartnerRepricingConfig.
   *
   * @param GoogleCloudChannelV1RepricingConfigEntitlementGranularity $entitlementGranularity
   */
  public function setEntitlementGranularity(GoogleCloudChannelV1RepricingConfigEntitlementGranularity $entitlementGranularity)
  {
    $this->entitlementGranularity = $entitlementGranularity;
  }
  /**
   * @return GoogleCloudChannelV1RepricingConfigEntitlementGranularity
   */
  public function getEntitlementGranularity()
  {
    return $this->entitlementGranularity;
  }
  /**
   * Required. The RebillingBasis to use for this bill. Specifies the relative
   * cost based on repricing costs you will apply.
   *
   * Accepted values: REBILLING_BASIS_UNSPECIFIED, COST_AT_LIST,
   * DIRECT_CUSTOMER_COST
   *
   * @param self::REBILLING_BASIS_* $rebillingBasis
   */
  public function setRebillingBasis($rebillingBasis)
  {
    $this->rebillingBasis = $rebillingBasis;
  }
  /**
   * @return self::REBILLING_BASIS_*
   */
  public function getRebillingBasis()
  {
    return $this->rebillingBasis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1RepricingConfig::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1RepricingConfig');
