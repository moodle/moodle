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

class PricingSchedule extends \Google\Collection
{
  public const CAP_COST_OPTION_CAP_COST_NONE = 'CAP_COST_NONE';
  public const CAP_COST_OPTION_CAP_COST_MONTHLY = 'CAP_COST_MONTHLY';
  public const CAP_COST_OPTION_CAP_COST_CUMULATIVE = 'CAP_COST_CUMULATIVE';
  public const PRICING_TYPE_PRICING_TYPE_CPM = 'PRICING_TYPE_CPM';
  public const PRICING_TYPE_PRICING_TYPE_CPC = 'PRICING_TYPE_CPC';
  public const PRICING_TYPE_PRICING_TYPE_CPA = 'PRICING_TYPE_CPA';
  public const PRICING_TYPE_PRICING_TYPE_FLAT_RATE_IMPRESSIONS = 'PRICING_TYPE_FLAT_RATE_IMPRESSIONS';
  public const PRICING_TYPE_PRICING_TYPE_FLAT_RATE_CLICKS = 'PRICING_TYPE_FLAT_RATE_CLICKS';
  public const PRICING_TYPE_PRICING_TYPE_CPM_ACTIVEVIEW = 'PRICING_TYPE_CPM_ACTIVEVIEW';
  protected $collection_key = 'pricingPeriods';
  /**
   * Placement cap cost option.
   *
   * @var string
   */
  public $capCostOption;
  /**
   * @var string
   */
  public $endDate;
  /**
   * Whether this placement is flighted. If true, pricing periods will be
   * computed automatically.
   *
   * @var bool
   */
  public $flighted;
  /**
   * Floodlight activity ID associated with this placement. This field should be
   * set when placement pricing type is set to PRICING_TYPE_CPA.
   *
   * @var string
   */
  public $floodlightActivityId;
  protected $pricingPeriodsType = PricingSchedulePricingPeriod::class;
  protected $pricingPeriodsDataType = 'array';
  /**
   * Placement pricing type. This field is required on insertion.
   *
   * @var string
   */
  public $pricingType;
  /**
   * @var string
   */
  public $startDate;
  /**
   * @var string
   */
  public $testingStartDate;

  /**
   * Placement cap cost option.
   *
   * Accepted values: CAP_COST_NONE, CAP_COST_MONTHLY, CAP_COST_CUMULATIVE
   *
   * @param self::CAP_COST_OPTION_* $capCostOption
   */
  public function setCapCostOption($capCostOption)
  {
    $this->capCostOption = $capCostOption;
  }
  /**
   * @return self::CAP_COST_OPTION_*
   */
  public function getCapCostOption()
  {
    return $this->capCostOption;
  }
  /**
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
   * Whether this placement is flighted. If true, pricing periods will be
   * computed automatically.
   *
   * @param bool $flighted
   */
  public function setFlighted($flighted)
  {
    $this->flighted = $flighted;
  }
  /**
   * @return bool
   */
  public function getFlighted()
  {
    return $this->flighted;
  }
  /**
   * Floodlight activity ID associated with this placement. This field should be
   * set when placement pricing type is set to PRICING_TYPE_CPA.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
  /**
   * Pricing periods for this placement.
   *
   * @param PricingSchedulePricingPeriod[] $pricingPeriods
   */
  public function setPricingPeriods($pricingPeriods)
  {
    $this->pricingPeriods = $pricingPeriods;
  }
  /**
   * @return PricingSchedulePricingPeriod[]
   */
  public function getPricingPeriods()
  {
    return $this->pricingPeriods;
  }
  /**
   * Placement pricing type. This field is required on insertion.
   *
   * Accepted values: PRICING_TYPE_CPM, PRICING_TYPE_CPC, PRICING_TYPE_CPA,
   * PRICING_TYPE_FLAT_RATE_IMPRESSIONS, PRICING_TYPE_FLAT_RATE_CLICKS,
   * PRICING_TYPE_CPM_ACTIVEVIEW
   *
   * @param self::PRICING_TYPE_* $pricingType
   */
  public function setPricingType($pricingType)
  {
    $this->pricingType = $pricingType;
  }
  /**
   * @return self::PRICING_TYPE_*
   */
  public function getPricingType()
  {
    return $this->pricingType;
  }
  /**
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
   * @param string $testingStartDate
   */
  public function setTestingStartDate($testingStartDate)
  {
    $this->testingStartDate = $testingStartDate;
  }
  /**
   * @return string
   */
  public function getTestingStartDate()
  {
    return $this->testingStartDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PricingSchedule::class, 'Google_Service_Dfareporting_PricingSchedule');
