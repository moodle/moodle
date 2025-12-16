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

namespace Google\Service\BusinessProfilePerformance;

class DailyMetricTimeSeries extends \Google\Model
{
  /**
   * Represents the default unknown value.
   */
  public const DAILY_METRIC_DAILY_METRIC_UNKNOWN = 'DAILY_METRIC_UNKNOWN';
  /**
   * Business impressions on Google Maps on Desktop devices. Multiple
   * impressions by a unique user within a single day are counted as a single
   * impression.
   */
  public const DAILY_METRIC_BUSINESS_IMPRESSIONS_DESKTOP_MAPS = 'BUSINESS_IMPRESSIONS_DESKTOP_MAPS';
  /**
   * Business impressions on Google Search on Desktop devices. Multiple
   * impressions by a unique user within a single day are counted as a single
   * impression.
   */
  public const DAILY_METRIC_BUSINESS_IMPRESSIONS_DESKTOP_SEARCH = 'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH';
  /**
   * Business impressions on Google Maps on Mobile devices. Multiple impressions
   * by a unique user within a single day are counted as a single impression.
   */
  public const DAILY_METRIC_BUSINESS_IMPRESSIONS_MOBILE_MAPS = 'BUSINESS_IMPRESSIONS_MOBILE_MAPS';
  /**
   * Business impressions on Google Search on Mobile devices. Multiple
   * impressions by a unique user within a single day are counted as a single
   * impression.
   */
  public const DAILY_METRIC_BUSINESS_IMPRESSIONS_MOBILE_SEARCH = 'BUSINESS_IMPRESSIONS_MOBILE_SEARCH';
  /**
   * The number of message conversations received on the business profile.
   */
  public const DAILY_METRIC_BUSINESS_CONVERSATIONS = 'BUSINESS_CONVERSATIONS';
  /**
   * The number of times a direction request was requested to the business
   * location.
   */
  public const DAILY_METRIC_BUSINESS_DIRECTION_REQUESTS = 'BUSINESS_DIRECTION_REQUESTS';
  /**
   * The number of times the business profile call button was clicked.
   */
  public const DAILY_METRIC_CALL_CLICKS = 'CALL_CLICKS';
  /**
   * The number of times the business profile website was clicked.
   */
  public const DAILY_METRIC_WEBSITE_CLICKS = 'WEBSITE_CLICKS';
  /**
   * The number of bookings made from the business profile via Reserve with
   * Google.
   */
  public const DAILY_METRIC_BUSINESS_BOOKINGS = 'BUSINESS_BOOKINGS';
  /**
   * The number of food orders received from the business profile.
   *
   * @deprecated
   */
  public const DAILY_METRIC_BUSINESS_FOOD_ORDERS = 'BUSINESS_FOOD_ORDERS';
  /**
   * The number of clicks to view or interact with the menu content on the
   * business profile. Multiple clicks by a unique user within a single day are
   * counted as 1.
   */
  public const DAILY_METRIC_BUSINESS_FOOD_MENU_CLICKS = 'BUSINESS_FOOD_MENU_CLICKS';
  /**
   * The DailyMetric that the TimeSeries represents.
   *
   * @var string
   */
  public $dailyMetric;
  protected $dailySubEntityTypeType = DailySubEntityType::class;
  protected $dailySubEntityTypeDataType = '';
  protected $timeSeriesType = TimeSeries::class;
  protected $timeSeriesDataType = '';

  /**
   * The DailyMetric that the TimeSeries represents.
   *
   * Accepted values: DAILY_METRIC_UNKNOWN, BUSINESS_IMPRESSIONS_DESKTOP_MAPS,
   * BUSINESS_IMPRESSIONS_DESKTOP_SEARCH, BUSINESS_IMPRESSIONS_MOBILE_MAPS,
   * BUSINESS_IMPRESSIONS_MOBILE_SEARCH, BUSINESS_CONVERSATIONS,
   * BUSINESS_DIRECTION_REQUESTS, CALL_CLICKS, WEBSITE_CLICKS,
   * BUSINESS_BOOKINGS, BUSINESS_FOOD_ORDERS, BUSINESS_FOOD_MENU_CLICKS
   *
   * @param self::DAILY_METRIC_* $dailyMetric
   */
  public function setDailyMetric($dailyMetric)
  {
    $this->dailyMetric = $dailyMetric;
  }
  /**
   * @return self::DAILY_METRIC_*
   */
  public function getDailyMetric()
  {
    return $this->dailyMetric;
  }
  /**
   * The DailySubEntityType that the TimeSeries represents. Will not be present
   * when breakdown does not exist.
   *
   * @param DailySubEntityType $dailySubEntityType
   */
  public function setDailySubEntityType(DailySubEntityType $dailySubEntityType)
  {
    $this->dailySubEntityType = $dailySubEntityType;
  }
  /**
   * @return DailySubEntityType
   */
  public function getDailySubEntityType()
  {
    return $this->dailySubEntityType;
  }
  /**
   * List of datapoints where each datapoint is a date-value pair.
   *
   * @param TimeSeries $timeSeries
   */
  public function setTimeSeries(TimeSeries $timeSeries)
  {
    $this->timeSeries = $timeSeries;
  }
  /**
   * @return TimeSeries
   */
  public function getTimeSeries()
  {
    return $this->timeSeries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DailyMetricTimeSeries::class, 'Google_Service_BusinessProfilePerformance_DailyMetricTimeSeries');
