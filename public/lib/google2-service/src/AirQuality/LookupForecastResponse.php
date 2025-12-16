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

namespace Google\Service\AirQuality;

class LookupForecastResponse extends \Google\Collection
{
  protected $collection_key = 'hourlyForecasts';
  protected $hourlyForecastsType = HourlyForecast::class;
  protected $hourlyForecastsDataType = 'array';
  /**
   * Optional. The token to retrieve the next page.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Optional. The ISO_3166-1 alpha-2 code of the country/region corresponding
   * to the location provided in the request. This field might be omitted from
   * the response if the location provided in the request resides in a disputed
   * territory.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Optional. Contains the air quality information for each hour in the
   * requested range. For example, if the request is for 48 hours of forecast
   * there will be 48 elements of hourly forecasts.
   *
   * @param HourlyForecast[] $hourlyForecasts
   */
  public function setHourlyForecasts($hourlyForecasts)
  {
    $this->hourlyForecasts = $hourlyForecasts;
  }
  /**
   * @return HourlyForecast[]
   */
  public function getHourlyForecasts()
  {
    return $this->hourlyForecasts;
  }
  /**
   * Optional. The token to retrieve the next page.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Optional. The ISO_3166-1 alpha-2 code of the country/region corresponding
   * to the location provided in the request. This field might be omitted from
   * the response if the location provided in the request resides in a disputed
   * territory.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LookupForecastResponse::class, 'Google_Service_AirQuality_LookupForecastResponse');
