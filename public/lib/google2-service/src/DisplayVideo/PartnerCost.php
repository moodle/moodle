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

class PartnerCost extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_UNSPECIFIED = 'PARTNER_COST_TYPE_UNSPECIFIED';
  /**
   * The cost is charged for using Scope3 (previously known as Adloox). Billed
   * by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_ADLOOX = 'PARTNER_COST_TYPE_ADLOOX';
  /**
   * The cost is charged for using Scope3 (previously known as Adloox) Pre-Bid.
   * Billed through DV360.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_ADLOOX_PREBID = 'PARTNER_COST_TYPE_ADLOOX_PREBID';
  /**
   * The cost is charged for using AdSafe. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_ADSAFE = 'PARTNER_COST_TYPE_ADSAFE';
  /**
   * The cost is charged for using AdExpose. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_ADXPOSE = 'PARTNER_COST_TYPE_ADXPOSE';
  /**
   * The cost is charged for using Aggregate Knowledge. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_AGGREGATE_KNOWLEDGE = 'PARTNER_COST_TYPE_AGGREGATE_KNOWLEDGE';
  /**
   * The cost is charged for using an Agency Trading Desk. Billed by the
   * partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_AGENCY_TRADING_DESK = 'PARTNER_COST_TYPE_AGENCY_TRADING_DESK';
  /**
   * The cost is charged for using DV360. Billed through DV360.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_DV360_FEE = 'PARTNER_COST_TYPE_DV360_FEE';
  /**
   * The cost is charged for using comScore vCE. Billed through DV360.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_COMSCORE_VCE = 'PARTNER_COST_TYPE_COMSCORE_VCE';
  /**
   * The cost is charged for using a Data Management Platform. Billed by the
   * partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_DATA_MANAGEMENT_PLATFORM = 'PARTNER_COST_TYPE_DATA_MANAGEMENT_PLATFORM';
  /**
   * The default cost type. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_DEFAULT = 'PARTNER_COST_TYPE_DEFAULT';
  /**
   * The cost is charged for using DoubleVerify. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_DOUBLE_VERIFY = 'PARTNER_COST_TYPE_DOUBLE_VERIFY';
  /**
   * The cost is charged for using DoubleVerify Pre-Bid. Billed through DV360.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_DOUBLE_VERIFY_PREBID = 'PARTNER_COST_TYPE_DOUBLE_VERIFY_PREBID';
  /**
   * The cost is charged for using Evidon. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_EVIDON = 'PARTNER_COST_TYPE_EVIDON';
  /**
   * The cost is charged for using Integral Ad Science Video. Billed by the
   * partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_VIDEO = 'PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_VIDEO';
  /**
   * The cost is charged for using Integral Ad Science Pre-Bid. Billed through
   * DV360.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_PREBID = 'PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_PREBID';
  /**
   * The cost is charged for using media cost data. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_MEDIA_COST_DATA = 'PARTNER_COST_TYPE_MEDIA_COST_DATA';
  /**
   * The cost is charged for using MOAT Video. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_MOAT_VIDEO = 'PARTNER_COST_TYPE_MOAT_VIDEO';
  /**
   * The cost is charged for using Nielsen Digital Ad Ratings. Billed through
   * DV360.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_NIELSEN_DAR = 'PARTNER_COST_TYPE_NIELSEN_DAR';
  /**
   * The cost is charged for using ShopLocal. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_SHOP_LOCAL = 'PARTNER_COST_TYPE_SHOP_LOCAL';
  /**
   * The cost is charged for using Teracent. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_TERACENT = 'PARTNER_COST_TYPE_TERACENT';
  /**
   * The cost is charged for using a third-party ad server. Billed by the
   * partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_THIRD_PARTY_AD_SERVER = 'PARTNER_COST_TYPE_THIRD_PARTY_AD_SERVER';
  /**
   * The cost is charged for using TrustMetrics. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_TRUST_METRICS = 'PARTNER_COST_TYPE_TRUST_METRICS';
  /**
   * The cost is charged for using Vizu. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_VIZU = 'PARTNER_COST_TYPE_VIZU';
  /**
   * The cost is charged as custom fee 1. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_CUSTOM_FEE_1 = 'PARTNER_COST_TYPE_CUSTOM_FEE_1';
  /**
   * The cost is charged as custom fee 2. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_CUSTOM_FEE_2 = 'PARTNER_COST_TYPE_CUSTOM_FEE_2';
  /**
   * The cost is charged as custom fee 3. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_CUSTOM_FEE_3 = 'PARTNER_COST_TYPE_CUSTOM_FEE_3';
  /**
   * The cost is charged as custom fee 4. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_CUSTOM_FEE_4 = 'PARTNER_COST_TYPE_CUSTOM_FEE_4';
  /**
   * The cost is charged as custom fee 5. Billed by the partner.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_CUSTOM_FEE_5 = 'PARTNER_COST_TYPE_CUSTOM_FEE_5';
  /**
   * The cost is charged for using Scibids. Billed through DV360. This type is
   * currently only available to certain customers. Other customers attempting
   * to use this type will receive an error.
   */
  public const COST_TYPE_PARTNER_COST_TYPE_SCIBIDS_FEE = 'PARTNER_COST_TYPE_SCIBIDS_FEE';
  /**
   * Value is not specified or is unknown in this version.
   */
  public const FEE_TYPE_PARTNER_COST_FEE_TYPE_UNSPECIFIED = 'PARTNER_COST_FEE_TYPE_UNSPECIFIED';
  /**
   * The partner cost is a fixed CPM fee. Not applicable when the partner cost
   * cost_type is one of: * `PARTNER_COST_TYPE_MEDIA_COST_DATA` *
   * `PARTNER_COST_TYPE_DV360_FEE`.
   */
  public const FEE_TYPE_PARTNER_COST_FEE_TYPE_CPM_FEE = 'PARTNER_COST_FEE_TYPE_CPM_FEE';
  /**
   * The partner cost is a percentage surcharge based on the media cost. Not
   * applicable when the partner cost_type is one of: *
   * `PARTNER_COST_TYPE_SHOP_LOCAL` * `PARTNER_COST_TYPE_TRUST_METRICS` *
   * `PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_VIDEO` *
   * `PARTNER_COST_TYPE_MOAT_VIDEO`.
   */
  public const FEE_TYPE_PARTNER_COST_FEE_TYPE_MEDIA_FEE = 'PARTNER_COST_FEE_TYPE_MEDIA_FEE';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const INVOICE_TYPE_PARTNER_COST_INVOICE_TYPE_UNSPECIFIED = 'PARTNER_COST_INVOICE_TYPE_UNSPECIFIED';
  /**
   * Partner cost is billed through DV360.
   */
  public const INVOICE_TYPE_PARTNER_COST_INVOICE_TYPE_DV360 = 'PARTNER_COST_INVOICE_TYPE_DV360';
  /**
   * Partner cost is billed by the partner.
   */
  public const INVOICE_TYPE_PARTNER_COST_INVOICE_TYPE_PARTNER = 'PARTNER_COST_INVOICE_TYPE_PARTNER';
  /**
   * Required. The type of the partner cost.
   *
   * @var string
   */
  public $costType;
  /**
   * The CPM fee amount in micros of advertiser's currency. Applicable when the
   * fee_type is `PARTNER_FEE_TYPE_CPM_FEE`. Must be greater than or equal to 0.
   * For example, for 1.5 standard unit of the advertiser's currency, set this
   * field to 1500000.
   *
   * @var string
   */
  public $feeAmount;
  /**
   * The media fee percentage in millis (1/1000 of a percent). Applicable when
   * the fee_type is `PARTNER_FEE_TYPE_MEDIA_FEE`. Must be greater than or equal
   * to 0. For example: 100 represents 0.1%.
   *
   * @var string
   */
  public $feePercentageMillis;
  /**
   * Required. The fee type for this partner cost.
   *
   * @var string
   */
  public $feeType;
  /**
   * The invoice type for this partner cost. * Required when cost_type is one
   * of: - `PARTNER_COST_TYPE_ADLOOX` - `PARTNER_COST_TYPE_DOUBLE_VERIFY` -
   * `PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE`. * Output only for other types.
   *
   * @var string
   */
  public $invoiceType;

  /**
   * Required. The type of the partner cost.
   *
   * Accepted values: PARTNER_COST_TYPE_UNSPECIFIED, PARTNER_COST_TYPE_ADLOOX,
   * PARTNER_COST_TYPE_ADLOOX_PREBID, PARTNER_COST_TYPE_ADSAFE,
   * PARTNER_COST_TYPE_ADXPOSE, PARTNER_COST_TYPE_AGGREGATE_KNOWLEDGE,
   * PARTNER_COST_TYPE_AGENCY_TRADING_DESK, PARTNER_COST_TYPE_DV360_FEE,
   * PARTNER_COST_TYPE_COMSCORE_VCE, PARTNER_COST_TYPE_DATA_MANAGEMENT_PLATFORM,
   * PARTNER_COST_TYPE_DEFAULT, PARTNER_COST_TYPE_DOUBLE_VERIFY,
   * PARTNER_COST_TYPE_DOUBLE_VERIFY_PREBID, PARTNER_COST_TYPE_EVIDON,
   * PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_VIDEO,
   * PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE_PREBID,
   * PARTNER_COST_TYPE_MEDIA_COST_DATA, PARTNER_COST_TYPE_MOAT_VIDEO,
   * PARTNER_COST_TYPE_NIELSEN_DAR, PARTNER_COST_TYPE_SHOP_LOCAL,
   * PARTNER_COST_TYPE_TERACENT, PARTNER_COST_TYPE_THIRD_PARTY_AD_SERVER,
   * PARTNER_COST_TYPE_TRUST_METRICS, PARTNER_COST_TYPE_VIZU,
   * PARTNER_COST_TYPE_CUSTOM_FEE_1, PARTNER_COST_TYPE_CUSTOM_FEE_2,
   * PARTNER_COST_TYPE_CUSTOM_FEE_3, PARTNER_COST_TYPE_CUSTOM_FEE_4,
   * PARTNER_COST_TYPE_CUSTOM_FEE_5, PARTNER_COST_TYPE_SCIBIDS_FEE
   *
   * @param self::COST_TYPE_* $costType
   */
  public function setCostType($costType)
  {
    $this->costType = $costType;
  }
  /**
   * @return self::COST_TYPE_*
   */
  public function getCostType()
  {
    return $this->costType;
  }
  /**
   * The CPM fee amount in micros of advertiser's currency. Applicable when the
   * fee_type is `PARTNER_FEE_TYPE_CPM_FEE`. Must be greater than or equal to 0.
   * For example, for 1.5 standard unit of the advertiser's currency, set this
   * field to 1500000.
   *
   * @param string $feeAmount
   */
  public function setFeeAmount($feeAmount)
  {
    $this->feeAmount = $feeAmount;
  }
  /**
   * @return string
   */
  public function getFeeAmount()
  {
    return $this->feeAmount;
  }
  /**
   * The media fee percentage in millis (1/1000 of a percent). Applicable when
   * the fee_type is `PARTNER_FEE_TYPE_MEDIA_FEE`. Must be greater than or equal
   * to 0. For example: 100 represents 0.1%.
   *
   * @param string $feePercentageMillis
   */
  public function setFeePercentageMillis($feePercentageMillis)
  {
    $this->feePercentageMillis = $feePercentageMillis;
  }
  /**
   * @return string
   */
  public function getFeePercentageMillis()
  {
    return $this->feePercentageMillis;
  }
  /**
   * Required. The fee type for this partner cost.
   *
   * Accepted values: PARTNER_COST_FEE_TYPE_UNSPECIFIED,
   * PARTNER_COST_FEE_TYPE_CPM_FEE, PARTNER_COST_FEE_TYPE_MEDIA_FEE
   *
   * @param self::FEE_TYPE_* $feeType
   */
  public function setFeeType($feeType)
  {
    $this->feeType = $feeType;
  }
  /**
   * @return self::FEE_TYPE_*
   */
  public function getFeeType()
  {
    return $this->feeType;
  }
  /**
   * The invoice type for this partner cost. * Required when cost_type is one
   * of: - `PARTNER_COST_TYPE_ADLOOX` - `PARTNER_COST_TYPE_DOUBLE_VERIFY` -
   * `PARTNER_COST_TYPE_INTEGRAL_AD_SCIENCE`. * Output only for other types.
   *
   * Accepted values: PARTNER_COST_INVOICE_TYPE_UNSPECIFIED,
   * PARTNER_COST_INVOICE_TYPE_DV360, PARTNER_COST_INVOICE_TYPE_PARTNER
   *
   * @param self::INVOICE_TYPE_* $invoiceType
   */
  public function setInvoiceType($invoiceType)
  {
    $this->invoiceType = $invoiceType;
  }
  /**
   * @return self::INVOICE_TYPE_*
   */
  public function getInvoiceType()
  {
    return $this->invoiceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnerCost::class, 'Google_Service_DisplayVideo_PartnerCost');
