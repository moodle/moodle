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

namespace Google\Service\BusinessProfilePerformance\Resource;

use Google\Service\BusinessProfilePerformance\FetchMultiDailyMetricsTimeSeriesResponse;
use Google\Service\BusinessProfilePerformance\GetDailyMetricsTimeSeriesResponse;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $businessprofileperformanceService = new Google\Service\BusinessProfilePerformance(...);
 *   $locations = $businessprofileperformanceService->locations;
 *  </code>
 */
class Locations extends \Google\Service\Resource
{
  /**
   * (locations.fetchMultiDailyMetricsTimeSeries)
   *
   * @param string $location Required. The location for which the time series
   * should be fetched. Format: locations/{location_id} where location_id is an
   * unobfuscated listing id.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dailyMetrics Required. The metrics to retrieve time series
   * for.
   * @opt_param int dailyRange.endDate.day Day of a month. Must be from 1 to 31
   * and valid for the year and month, or 0 to specify a year by itself or a year
   * and month where the day isn't significant.
   * @opt_param int dailyRange.endDate.month Month of a year. Must be from 1 to
   * 12, or 0 to specify a year without a month and day.
   * @opt_param int dailyRange.endDate.year Year of the date. Must be from 1 to
   * 9999, or 0 to specify a date without a year.
   * @opt_param int dailyRange.startDate.day Day of a month. Must be from 1 to 31
   * and valid for the year and month, or 0 to specify a year by itself or a year
   * and month where the day isn't significant.
   * @opt_param int dailyRange.startDate.month Month of a year. Must be from 1 to
   * 12, or 0 to specify a year without a month and day.
   * @opt_param int dailyRange.startDate.year Year of the date. Must be from 1 to
   * 9999, or 0 to specify a date without a year.
   * @return FetchMultiDailyMetricsTimeSeriesResponse
   * @throws \Google\Service\Exception
   */
  public function fetchMultiDailyMetricsTimeSeries($location, $optParams = [])
  {
    $params = ['location' => $location];
    $params = array_merge($params, $optParams);
    return $this->call('fetchMultiDailyMetricsTimeSeries', [$params], FetchMultiDailyMetricsTimeSeriesResponse::class);
  }
  /**
   * (locations.getDailyMetricsTimeSeries)
   *
   * @param string $name Required. The location for which the time series should
   * be fetched. Format: locations/{location_id} where location_id is an
   * unobfuscated listing id.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dailyMetric Required. The metric to retrieve time series.
   * @opt_param int dailyRange.endDate.day Day of a month. Must be from 1 to 31
   * and valid for the year and month, or 0 to specify a year by itself or a year
   * and month where the day isn't significant.
   * @opt_param int dailyRange.endDate.month Month of a year. Must be from 1 to
   * 12, or 0 to specify a year without a month and day.
   * @opt_param int dailyRange.endDate.year Year of the date. Must be from 1 to
   * 9999, or 0 to specify a date without a year.
   * @opt_param int dailyRange.startDate.day Day of a month. Must be from 1 to 31
   * and valid for the year and month, or 0 to specify a year by itself or a year
   * and month where the day isn't significant.
   * @opt_param int dailyRange.startDate.month Month of a year. Must be from 1 to
   * 12, or 0 to specify a year without a month and day.
   * @opt_param int dailyRange.startDate.year Year of the date. Must be from 1 to
   * 9999, or 0 to specify a date without a year.
   * @opt_param string dailySubEntityType.dayOfWeek Represents the day of the
   * week. Eg: MONDAY. Currently supported DailyMetrics = NONE.
   * @opt_param int dailySubEntityType.timeOfDay.hours Hours of a day in 24 hour
   * format. Must be greater than or equal to 0 and typically must be less than or
   * equal to 23. An API may choose to allow the value "24:00:00" for scenarios
   * like business closing time.
   * @opt_param int dailySubEntityType.timeOfDay.minutes Minutes of an hour. Must
   * be greater than or equal to 0 and less than or equal to 59.
   * @opt_param int dailySubEntityType.timeOfDay.nanos Fractions of seconds, in
   * nanoseconds. Must be greater than or equal to 0 and less than or equal to
   * 999,999,999.
   * @opt_param int dailySubEntityType.timeOfDay.seconds Seconds of a minute. Must
   * be greater than or equal to 0 and typically must be less than or equal to 59.
   * An API may allow the value 60 if it allows leap-seconds.
   * @return GetDailyMetricsTimeSeriesResponse
   * @throws \Google\Service\Exception
   */
  public function getDailyMetricsTimeSeries($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDailyMetricsTimeSeries', [$params], GetDailyMetricsTimeSeriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Locations::class, 'Google_Service_BusinessProfilePerformance_Resource_Locations');
