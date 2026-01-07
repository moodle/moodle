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

namespace Google\Service\ShoppingContent;

class BestSellers extends \Google\Model
{
  /**
   * Relative demand is unknown.
   */
  public const PREVIOUS_RELATIVE_DEMAND_RELATIVE_DEMAND_UNSPECIFIED = 'RELATIVE_DEMAND_UNSPECIFIED';
  /**
   * Demand is 0-5% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const PREVIOUS_RELATIVE_DEMAND_VERY_LOW = 'VERY_LOW';
  /**
   * Demand is 6-10% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const PREVIOUS_RELATIVE_DEMAND_LOW = 'LOW';
  /**
   * Demand is 11-20% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const PREVIOUS_RELATIVE_DEMAND_MEDIUM = 'MEDIUM';
  /**
   * Demand is 21-50% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const PREVIOUS_RELATIVE_DEMAND_HIGH = 'HIGH';
  /**
   * Demand is 51-100% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const PREVIOUS_RELATIVE_DEMAND_VERY_HIGH = 'VERY_HIGH';
  /**
   * Relative demand is unknown.
   */
  public const RELATIVE_DEMAND_RELATIVE_DEMAND_UNSPECIFIED = 'RELATIVE_DEMAND_UNSPECIFIED';
  /**
   * Demand is 0-5% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const RELATIVE_DEMAND_VERY_LOW = 'VERY_LOW';
  /**
   * Demand is 6-10% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const RELATIVE_DEMAND_LOW = 'LOW';
  /**
   * Demand is 11-20% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const RELATIVE_DEMAND_MEDIUM = 'MEDIUM';
  /**
   * Demand is 21-50% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const RELATIVE_DEMAND_HIGH = 'HIGH';
  /**
   * Demand is 51-100% of the demand of the highest ranked product clusters or
   * brands.
   */
  public const RELATIVE_DEMAND_VERY_HIGH = 'VERY_HIGH';
  /**
   * Relative demand change is unknown.
   */
  public const RELATIVE_DEMAND_CHANGE_RELATIVE_DEMAND_CHANGE_TYPE_UNSPECIFIED = 'RELATIVE_DEMAND_CHANGE_TYPE_UNSPECIFIED';
  /**
   * Relative demand is lower than previous time period.
   */
  public const RELATIVE_DEMAND_CHANGE_SINKER = 'SINKER';
  /**
   * Relative demand is equal to previous time period.
   */
  public const RELATIVE_DEMAND_CHANGE_FLAT = 'FLAT';
  /**
   * Relative demand is higher than the previous time period.
   */
  public const RELATIVE_DEMAND_CHANGE_RISER = 'RISER';
  /**
   * Report granularity is unknown.
   */
  public const REPORT_GRANULARITY_REPORT_GRANULARITY_UNSPECIFIED = 'REPORT_GRANULARITY_UNSPECIFIED';
  /**
   * Ranking is done over a week timeframe.
   */
  public const REPORT_GRANULARITY_WEEKLY = 'WEEKLY';
  /**
   * Ranking is done over a month timeframe.
   */
  public const REPORT_GRANULARITY_MONTHLY = 'MONTHLY';
  /**
   * Google product category ID to calculate the ranking for, represented in
   * [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436). If a
   * `WHERE` condition on `best_sellers.category_id` is not specified in the
   * query, rankings for all top-level categories are returned.
   *
   * @var string
   */
  public $categoryId;
  /**
   * Country where the ranking is calculated. A `WHERE` condition on
   * `best_sellers.country_code` is required in the query.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Popularity rank in the previous week or month.
   *
   * @var string
   */
  public $previousRank;
  /**
   * Estimated demand in relation to the item with the highest popularity rank
   * in the same category and country in the previous week or month.
   *
   * @var string
   */
  public $previousRelativeDemand;
  /**
   * Popularity on Shopping ads and free listings, in the selected category and
   * country, based on the estimated number of units sold.
   *
   * @var string
   */
  public $rank;
  /**
   * Estimated demand in relation to the item with the highest popularity rank
   * in the same category and country.
   *
   * @var string
   */
  public $relativeDemand;
  /**
   * Change in the estimated demand. Whether it rose, sank or remained flat.
   *
   * @var string
   */
  public $relativeDemandChange;
  protected $reportDateType = Date::class;
  protected $reportDateDataType = '';
  /**
   * Granularity of the report. The ranking can be done over a week or a month
   * timeframe. A `WHERE` condition on `best_sellers.report_granularity` is
   * required in the query.
   *
   * @var string
   */
  public $reportGranularity;

  /**
   * Google product category ID to calculate the ranking for, represented in
   * [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436). If a
   * `WHERE` condition on `best_sellers.category_id` is not specified in the
   * query, rankings for all top-level categories are returned.
   *
   * @param string $categoryId
   */
  public function setCategoryId($categoryId)
  {
    $this->categoryId = $categoryId;
  }
  /**
   * @return string
   */
  public function getCategoryId()
  {
    return $this->categoryId;
  }
  /**
   * Country where the ranking is calculated. A `WHERE` condition on
   * `best_sellers.country_code` is required in the query.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Popularity rank in the previous week or month.
   *
   * @param string $previousRank
   */
  public function setPreviousRank($previousRank)
  {
    $this->previousRank = $previousRank;
  }
  /**
   * @return string
   */
  public function getPreviousRank()
  {
    return $this->previousRank;
  }
  /**
   * Estimated demand in relation to the item with the highest popularity rank
   * in the same category and country in the previous week or month.
   *
   * Accepted values: RELATIVE_DEMAND_UNSPECIFIED, VERY_LOW, LOW, MEDIUM, HIGH,
   * VERY_HIGH
   *
   * @param self::PREVIOUS_RELATIVE_DEMAND_* $previousRelativeDemand
   */
  public function setPreviousRelativeDemand($previousRelativeDemand)
  {
    $this->previousRelativeDemand = $previousRelativeDemand;
  }
  /**
   * @return self::PREVIOUS_RELATIVE_DEMAND_*
   */
  public function getPreviousRelativeDemand()
  {
    return $this->previousRelativeDemand;
  }
  /**
   * Popularity on Shopping ads and free listings, in the selected category and
   * country, based on the estimated number of units sold.
   *
   * @param string $rank
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return string
   */
  public function getRank()
  {
    return $this->rank;
  }
  /**
   * Estimated demand in relation to the item with the highest popularity rank
   * in the same category and country.
   *
   * Accepted values: RELATIVE_DEMAND_UNSPECIFIED, VERY_LOW, LOW, MEDIUM, HIGH,
   * VERY_HIGH
   *
   * @param self::RELATIVE_DEMAND_* $relativeDemand
   */
  public function setRelativeDemand($relativeDemand)
  {
    $this->relativeDemand = $relativeDemand;
  }
  /**
   * @return self::RELATIVE_DEMAND_*
   */
  public function getRelativeDemand()
  {
    return $this->relativeDemand;
  }
  /**
   * Change in the estimated demand. Whether it rose, sank or remained flat.
   *
   * Accepted values: RELATIVE_DEMAND_CHANGE_TYPE_UNSPECIFIED, SINKER, FLAT,
   * RISER
   *
   * @param self::RELATIVE_DEMAND_CHANGE_* $relativeDemandChange
   */
  public function setRelativeDemandChange($relativeDemandChange)
  {
    $this->relativeDemandChange = $relativeDemandChange;
  }
  /**
   * @return self::RELATIVE_DEMAND_CHANGE_*
   */
  public function getRelativeDemandChange()
  {
    return $this->relativeDemandChange;
  }
  /**
   * Report date. The value of this field can only be one of the following: *
   * The first day of the week (Monday) for weekly reports. * The first day of
   * the month for monthly reports. If a `WHERE` condition on
   * `best_sellers.report_date` is not specified in the query, the latest
   * available weekly or monthly report is returned.
   *
   * @param Date $reportDate
   */
  public function setReportDate(Date $reportDate)
  {
    $this->reportDate = $reportDate;
  }
  /**
   * @return Date
   */
  public function getReportDate()
  {
    return $this->reportDate;
  }
  /**
   * Granularity of the report. The ranking can be done over a week or a month
   * timeframe. A `WHERE` condition on `best_sellers.report_granularity` is
   * required in the query.
   *
   * Accepted values: REPORT_GRANULARITY_UNSPECIFIED, WEEKLY, MONTHLY
   *
   * @param self::REPORT_GRANULARITY_* $reportGranularity
   */
  public function setReportGranularity($reportGranularity)
  {
    $this->reportGranularity = $reportGranularity;
  }
  /**
   * @return self::REPORT_GRANULARITY_*
   */
  public function getReportGranularity()
  {
    return $this->reportGranularity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BestSellers::class, 'Google_Service_ShoppingContent_BestSellers');
