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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1RatePlan extends \Google\Collection
{
  /**
   * Billing period not specified.
   */
  public const BILLING_PERIOD_BILLING_PERIOD_UNSPECIFIED = 'BILLING_PERIOD_UNSPECIFIED';
  /**
   * Weekly billing period. **Note**: Not supported by Apigee at this time.
   */
  public const BILLING_PERIOD_WEEKLY = 'WEEKLY';
  /**
   * Monthly billing period.
   */
  public const BILLING_PERIOD_MONTHLY = 'MONTHLY';
  /**
   * Pricing model not specified. This is the default.
   */
  public const CONSUMPTION_PRICING_TYPE_CONSUMPTION_PRICING_TYPE_UNSPECIFIED = 'CONSUMPTION_PRICING_TYPE_UNSPECIFIED';
  /**
   * Fixed rate charged for each API call.
   */
  public const CONSUMPTION_PRICING_TYPE_FIXED_PER_UNIT = 'FIXED_PER_UNIT';
  /**
   * Variable rate charged for each API call based on price tiers. Example: *
   * 1-100 calls cost $2 per call * 101-200 calls cost $1.50 per call * 201-300
   * calls cost $1 per call * Total price for 50 calls: 50 x $2 = $100 * Total
   * price for 150 calls: 100 x $2 + 50 x $1.5 = $275 * Total price for 250
   * calls: 100 x $2 + 100 x $1.5 + 50 x $1 = $400.
   */
  public const CONSUMPTION_PRICING_TYPE_BANDED = 'BANDED';
  /**
   * **Note**: Not supported by Apigee at this time.
   */
  public const CONSUMPTION_PRICING_TYPE_TIERED = 'TIERED';
  /**
   * **Note**: Not supported by Apigee at this time.
   */
  public const CONSUMPTION_PRICING_TYPE_STAIRSTEP = 'STAIRSTEP';
  /**
   * Billing account type not specified.
   */
  public const PAYMENT_FUNDING_MODEL_PAYMENT_FUNDING_MODEL_UNSPECIFIED = 'PAYMENT_FUNDING_MODEL_UNSPECIFIED';
  /**
   * Prepaid billing account type. Developer pays in advance for the use of your
   * API products. Funds are deducted from their prepaid account balance.
   * **Note**: Not supported by Apigee at this time.
   */
  public const PAYMENT_FUNDING_MODEL_PREPAID = 'PREPAID';
  /**
   * Postpaid billing account type. Developer is billed through an invoice after
   * using your API products.
   */
  public const PAYMENT_FUNDING_MODEL_POSTPAID = 'POSTPAID';
  /**
   * Revenue share type is not specified.
   */
  public const REVENUE_SHARE_TYPE_REVENUE_SHARE_TYPE_UNSPECIFIED = 'REVENUE_SHARE_TYPE_UNSPECIFIED';
  /**
   * Fixed percentage of the total revenue will be shared. The percentage to be
   * shared can be configured by the API provider.
   */
  public const REVENUE_SHARE_TYPE_FIXED = 'FIXED';
  /**
   * Amount of revenue shared depends on the number of API calls. The API call
   * volume ranges and the revenue share percentage for each volume can be
   * configured by the API provider. **Note**: Not supported by Apigee at this
   * time.
   */
  public const REVENUE_SHARE_TYPE_VOLUME_BANDED = 'VOLUME_BANDED';
  /**
   * State of the rate plan is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Rate plan is in draft mode and only visible to API providers.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * Rate plan is published and will become visible to developers for the
   * configured duration (between `startTime` and `endTime`).
   */
  public const STATE_PUBLISHED = 'PUBLISHED';
  protected $collection_key = 'revenueShareRates';
  /**
   * Name of the API product that the rate plan is associated with.
   *
   * @var string
   */
  public $apiproduct;
  /**
   * Frequency at which the customer will be billed.
   *
   * @var string
   */
  public $billingPeriod;
  protected $consumptionPricingRatesType = GoogleCloudApigeeV1RateRange::class;
  protected $consumptionPricingRatesDataType = 'array';
  /**
   * Pricing model used for consumption-based charges.
   *
   * @var string
   */
  public $consumptionPricingType;
  /**
   * Output only. Time that the rate plan was created in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Currency to be used for billing. Consists of a three-letter code as defined
   * by the [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) standard.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Description of the rate plan.
   *
   * @var string
   */
  public $description;
  /**
   * Display name of the rate plan.
   *
   * @var string
   */
  public $displayName;
  /**
   * Time when the rate plan will expire in milliseconds since epoch. Set to 0
   * or `null` to indicate that the rate plan should never expire.
   *
   * @var string
   */
  public $endTime;
  /**
   * Frequency at which the fixed fee is charged.
   *
   * @var int
   */
  public $fixedFeeFrequency;
  protected $fixedRecurringFeeType = GoogleTypeMoney::class;
  protected $fixedRecurringFeeDataType = '';
  /**
   * Output only. Time the rate plan was last modified in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Output only. Name of the rate plan.
   *
   * @var string
   */
  public $name;
  /**
   * DEPRECATED: This field is no longer supported and will eventually be
   * removed when Apigee Hybrid 1.5/1.6 is no longer supported. Instead, use the
   * `billingType` field inside `DeveloperMonetizationConfig` resource. Flag
   * that specifies the billing account type, prepaid or postpaid.
   *
   * @deprecated
   * @var string
   */
  public $paymentFundingModel;
  protected $revenueShareRatesType = GoogleCloudApigeeV1RevenueShareRange::class;
  protected $revenueShareRatesDataType = 'array';
  /**
   * Method used to calculate the revenue that is shared with developers.
   *
   * @var string
   */
  public $revenueShareType;
  protected $setupFeeType = GoogleTypeMoney::class;
  protected $setupFeeDataType = '';
  /**
   * Time when the rate plan becomes active in milliseconds since epoch.
   *
   * @var string
   */
  public $startTime;
  /**
   * Current state of the rate plan (draft or published).
   *
   * @var string
   */
  public $state;

  /**
   * Name of the API product that the rate plan is associated with.
   *
   * @param string $apiproduct
   */
  public function setApiproduct($apiproduct)
  {
    $this->apiproduct = $apiproduct;
  }
  /**
   * @return string
   */
  public function getApiproduct()
  {
    return $this->apiproduct;
  }
  /**
   * Frequency at which the customer will be billed.
   *
   * Accepted values: BILLING_PERIOD_UNSPECIFIED, WEEKLY, MONTHLY
   *
   * @param self::BILLING_PERIOD_* $billingPeriod
   */
  public function setBillingPeriod($billingPeriod)
  {
    $this->billingPeriod = $billingPeriod;
  }
  /**
   * @return self::BILLING_PERIOD_*
   */
  public function getBillingPeriod()
  {
    return $this->billingPeriod;
  }
  /**
   * API call volume ranges and the fees charged when the total number of API
   * calls is within a given range. The method used to calculate the final fee
   * depends on the selected pricing model. For example, if the pricing model is
   * `BANDED` and the ranges are defined as follows: ``` { "start": 1, "end":
   * 100, "fee": 2 }, { "start": 101, "end": 200, "fee": 1.50 }, { "start": 201,
   * "end": 0, "fee": 1 }, } ``` Then the following fees would be charged based
   * on the total number of API calls (assuming the currency selected is `USD`):
   * * 50 calls cost 50 x $2 = $100 * 150 calls cost 100 x $2 + 50 x $1.5 = $275
   * * 250 calls cost 100 x $2 + 100 x $1.5 + 50 x $1 = $400 * 500 calls cost
   * 100 x $2 + 100 x $1.5 + 300 x $1 = $650
   *
   * @param GoogleCloudApigeeV1RateRange[] $consumptionPricingRates
   */
  public function setConsumptionPricingRates($consumptionPricingRates)
  {
    $this->consumptionPricingRates = $consumptionPricingRates;
  }
  /**
   * @return GoogleCloudApigeeV1RateRange[]
   */
  public function getConsumptionPricingRates()
  {
    return $this->consumptionPricingRates;
  }
  /**
   * Pricing model used for consumption-based charges.
   *
   * Accepted values: CONSUMPTION_PRICING_TYPE_UNSPECIFIED, FIXED_PER_UNIT,
   * BANDED, TIERED, STAIRSTEP
   *
   * @param self::CONSUMPTION_PRICING_TYPE_* $consumptionPricingType
   */
  public function setConsumptionPricingType($consumptionPricingType)
  {
    $this->consumptionPricingType = $consumptionPricingType;
  }
  /**
   * @return self::CONSUMPTION_PRICING_TYPE_*
   */
  public function getConsumptionPricingType()
  {
    return $this->consumptionPricingType;
  }
  /**
   * Output only. Time that the rate plan was created in milliseconds since
   * epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Currency to be used for billing. Consists of a three-letter code as defined
   * by the [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) standard.
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
   * Description of the rate plan.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Display name of the rate plan.
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
   * Time when the rate plan will expire in milliseconds since epoch. Set to 0
   * or `null` to indicate that the rate plan should never expire.
   *
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
   * Frequency at which the fixed fee is charged.
   *
   * @param int $fixedFeeFrequency
   */
  public function setFixedFeeFrequency($fixedFeeFrequency)
  {
    $this->fixedFeeFrequency = $fixedFeeFrequency;
  }
  /**
   * @return int
   */
  public function getFixedFeeFrequency()
  {
    return $this->fixedFeeFrequency;
  }
  /**
   * Fixed amount that is charged at a defined interval and billed in advance of
   * use of the API product. The fee will be prorated for the first billing
   * period.
   *
   * @param GoogleTypeMoney $fixedRecurringFee
   */
  public function setFixedRecurringFee(GoogleTypeMoney $fixedRecurringFee)
  {
    $this->fixedRecurringFee = $fixedRecurringFee;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getFixedRecurringFee()
  {
    return $this->fixedRecurringFee;
  }
  /**
   * Output only. Time the rate plan was last modified in milliseconds since
   * epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Output only. Name of the rate plan.
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
   * DEPRECATED: This field is no longer supported and will eventually be
   * removed when Apigee Hybrid 1.5/1.6 is no longer supported. Instead, use the
   * `billingType` field inside `DeveloperMonetizationConfig` resource. Flag
   * that specifies the billing account type, prepaid or postpaid.
   *
   * Accepted values: PAYMENT_FUNDING_MODEL_UNSPECIFIED, PREPAID, POSTPAID
   *
   * @deprecated
   * @param self::PAYMENT_FUNDING_MODEL_* $paymentFundingModel
   */
  public function setPaymentFundingModel($paymentFundingModel)
  {
    $this->paymentFundingModel = $paymentFundingModel;
  }
  /**
   * @deprecated
   * @return self::PAYMENT_FUNDING_MODEL_*
   */
  public function getPaymentFundingModel()
  {
    return $this->paymentFundingModel;
  }
  /**
   * Details of the revenue sharing model.
   *
   * @param GoogleCloudApigeeV1RevenueShareRange[] $revenueShareRates
   */
  public function setRevenueShareRates($revenueShareRates)
  {
    $this->revenueShareRates = $revenueShareRates;
  }
  /**
   * @return GoogleCloudApigeeV1RevenueShareRange[]
   */
  public function getRevenueShareRates()
  {
    return $this->revenueShareRates;
  }
  /**
   * Method used to calculate the revenue that is shared with developers.
   *
   * Accepted values: REVENUE_SHARE_TYPE_UNSPECIFIED, FIXED, VOLUME_BANDED
   *
   * @param self::REVENUE_SHARE_TYPE_* $revenueShareType
   */
  public function setRevenueShareType($revenueShareType)
  {
    $this->revenueShareType = $revenueShareType;
  }
  /**
   * @return self::REVENUE_SHARE_TYPE_*
   */
  public function getRevenueShareType()
  {
    return $this->revenueShareType;
  }
  /**
   * Initial, one-time fee paid when purchasing the API product.
   *
   * @param GoogleTypeMoney $setupFee
   */
  public function setSetupFee(GoogleTypeMoney $setupFee)
  {
    $this->setupFee = $setupFee;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getSetupFee()
  {
    return $this->setupFee;
  }
  /**
   * Time when the rate plan becomes active in milliseconds since epoch.
   *
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
   * Current state of the rate plan (draft or published).
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, PUBLISHED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RatePlan::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RatePlan');
