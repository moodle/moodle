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

class LookupForecastRequest extends \Google\Collection
{
  /**
   * The default value. Ignored if passed as a parameter.
   */
  public const UAQI_COLOR_PALETTE_COLOR_PALETTE_UNSPECIFIED = 'COLOR_PALETTE_UNSPECIFIED';
  /**
   * Determines whether to use a red/green palette.
   */
  public const UAQI_COLOR_PALETTE_RED_GREEN = 'RED_GREEN';
  /**
   * Determines whether to use a indigo/persian palette (dark theme).
   */
  public const UAQI_COLOR_PALETTE_INDIGO_PERSIAN_DARK = 'INDIGO_PERSIAN_DARK';
  /**
   * Determines whether to use a indigo/persian palette (light theme).
   */
  public const UAQI_COLOR_PALETTE_INDIGO_PERSIAN_LIGHT = 'INDIGO_PERSIAN_LIGHT';
  protected $collection_key = 'extraComputations';
  protected $customLocalAqisType = CustomLocalAqi::class;
  protected $customLocalAqisDataType = 'array';
  /**
   * A timestamp for which to return the data for a specific point in time. The
   * timestamp is rounded to the previous exact hour. Note: this will return
   * hourly data for the requested timestamp only (i.e. a single hourly info
   * element). For example, a request sent where the date_time parameter is set
   * to 2023-01-03T11:05:49Z will be rounded down to 2023-01-03T11:00:00Z.
   *
   * @var string
   */
  public $dateTime;
  /**
   * Optional. Additional features that can be optionally enabled. Specifying
   * extra computations will result in the relevant elements and fields to be
   * returned in the response.
   *
   * @var string[]
   */
  public $extraComputations;
  /**
   * Optional. Allows the client to choose the language for the response. If
   * data cannot be provided for that language the API uses the closest match.
   * Allowed values rely on the IETF standard (default = 'en').
   *
   * @var string
   */
  public $languageCode;
  protected $locationType = LatLng::class;
  protected $locationDataType = '';
  /**
   * Optional. The maximum number of hourly info records to return per page
   * (default = 24).
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token received from a previous forecast call. It is used
   * to retrieve the subsequent page.
   *
   * @var string
   */
  public $pageToken;
  protected $periodType = Interval::class;
  protected $periodDataType = '';
  /**
   * Optional. Determines the color palette used for data provided by the
   * 'Universal Air Quality Index' (UAQI). This color palette is relevant just
   * for UAQI, other AQIs have a predetermined color palette that can't be
   * controlled.
   *
   * @var string
   */
  public $uaqiColorPalette;
  /**
   * Optional. If set to true, the Universal AQI will be included in the
   * 'indexes' field of the response (default = true).
   *
   * @var bool
   */
  public $universalAqi;

  /**
   * Optional. Expresses a 'country/region to AQI' relationship. Pairs a
   * country/region with a desired AQI so that air quality data that is required
   * for that country/region will be displayed according to the chosen AQI. This
   * parameter can be used to specify a non-default AQI for a given country, for
   * example, to get the US EPA index for Canada rather than the default index
   * for Canada.
   *
   * @param CustomLocalAqi[] $customLocalAqis
   */
  public function setCustomLocalAqis($customLocalAqis)
  {
    $this->customLocalAqis = $customLocalAqis;
  }
  /**
   * @return CustomLocalAqi[]
   */
  public function getCustomLocalAqis()
  {
    return $this->customLocalAqis;
  }
  /**
   * A timestamp for which to return the data for a specific point in time. The
   * timestamp is rounded to the previous exact hour. Note: this will return
   * hourly data for the requested timestamp only (i.e. a single hourly info
   * element). For example, a request sent where the date_time parameter is set
   * to 2023-01-03T11:05:49Z will be rounded down to 2023-01-03T11:00:00Z.
   *
   * @param string $dateTime
   */
  public function setDateTime($dateTime)
  {
    $this->dateTime = $dateTime;
  }
  /**
   * @return string
   */
  public function getDateTime()
  {
    return $this->dateTime;
  }
  /**
   * Optional. Additional features that can be optionally enabled. Specifying
   * extra computations will result in the relevant elements and fields to be
   * returned in the response.
   *
   * @param string[] $extraComputations
   */
  public function setExtraComputations($extraComputations)
  {
    $this->extraComputations = $extraComputations;
  }
  /**
   * @return string[]
   */
  public function getExtraComputations()
  {
    return $this->extraComputations;
  }
  /**
   * Optional. Allows the client to choose the language for the response. If
   * data cannot be provided for that language the API uses the closest match.
   * Allowed values rely on the IETF standard (default = 'en').
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Required. The latitude and longitude for which the API looks for air
   * quality data.
   *
   * @param LatLng $location
   */
  public function setLocation(LatLng $location)
  {
    $this->location = $location;
  }
  /**
   * @return LatLng
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Optional. The maximum number of hourly info records to return per page
   * (default = 24).
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token received from a previous forecast call. It is used
   * to retrieve the subsequent page.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Indicates the start and end period for which to get the forecast data. The
   * timestamp is rounded to the previous exact hour.
   *
   * @param Interval $period
   */
  public function setPeriod(Interval $period)
  {
    $this->period = $period;
  }
  /**
   * @return Interval
   */
  public function getPeriod()
  {
    return $this->period;
  }
  /**
   * Optional. Determines the color palette used for data provided by the
   * 'Universal Air Quality Index' (UAQI). This color palette is relevant just
   * for UAQI, other AQIs have a predetermined color palette that can't be
   * controlled.
   *
   * Accepted values: COLOR_PALETTE_UNSPECIFIED, RED_GREEN, INDIGO_PERSIAN_DARK,
   * INDIGO_PERSIAN_LIGHT
   *
   * @param self::UAQI_COLOR_PALETTE_* $uaqiColorPalette
   */
  public function setUaqiColorPalette($uaqiColorPalette)
  {
    $this->uaqiColorPalette = $uaqiColorPalette;
  }
  /**
   * @return self::UAQI_COLOR_PALETTE_*
   */
  public function getUaqiColorPalette()
  {
    return $this->uaqiColorPalette;
  }
  /**
   * Optional. If set to true, the Universal AQI will be included in the
   * 'indexes' field of the response (default = true).
   *
   * @param bool $universalAqi
   */
  public function setUniversalAqi($universalAqi)
  {
    $this->universalAqi = $universalAqi;
  }
  /**
   * @return bool
   */
  public function getUniversalAqi()
  {
    return $this->universalAqi;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LookupForecastRequest::class, 'Google_Service_AirQuality_LookupForecastRequest');
