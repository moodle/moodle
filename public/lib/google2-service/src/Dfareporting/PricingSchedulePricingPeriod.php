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

class PricingSchedulePricingPeriod extends \Google\Model
{
  /**
   * @var string
   */
  public $endDate;
  /**
   * Comments for this pricing period.
   *
   * @var string
   */
  public $pricingComment;
  /**
   * Rate or cost of this pricing period in nanos (i.e., multiplied by
   * 1000000000). Acceptable values are 0 to 1000000000000000000, inclusive.
   *
   * @var string
   */
  public $rateOrCostNanos;
  /**
   * @var string
   */
  public $startDate;
  /**
   * Units of this pricing period. Acceptable values are 0 to 10000000000,
   * inclusive.
   *
   * @var string
   */
  public $units;

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
   * Comments for this pricing period.
   *
   * @param string $pricingComment
   */
  public function setPricingComment($pricingComment)
  {
    $this->pricingComment = $pricingComment;
  }
  /**
   * @return string
   */
  public function getPricingComment()
  {
    return $this->pricingComment;
  }
  /**
   * Rate or cost of this pricing period in nanos (i.e., multiplied by
   * 1000000000). Acceptable values are 0 to 1000000000000000000, inclusive.
   *
   * @param string $rateOrCostNanos
   */
  public function setRateOrCostNanos($rateOrCostNanos)
  {
    $this->rateOrCostNanos = $rateOrCostNanos;
  }
  /**
   * @return string
   */
  public function getRateOrCostNanos()
  {
    return $this->rateOrCostNanos;
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
   * Units of this pricing period. Acceptable values are 0 to 10000000000,
   * inclusive.
   *
   * @param string $units
   */
  public function setUnits($units)
  {
    $this->units = $units;
  }
  /**
   * @return string
   */
  public function getUnits()
  {
    return $this->units;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PricingSchedulePricingPeriod::class, 'Google_Service_Dfareporting_PricingSchedulePricingPeriod');
