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

class CustomLocalAqi extends \Google\Model
{
  /**
   * The AQI to associate the country/region with. Value should be a [valid
   * index](/maps/documentation/air-quality/laqis) code.
   *
   * @var string
   */
  public $aqi;
  /**
   * The country/region requiring the custom AQI. Value should be provided using
   * [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
   * code.
   *
   * @var string
   */
  public $regionCode;

  /**
   * The AQI to associate the country/region with. Value should be a [valid
   * index](/maps/documentation/air-quality/laqis) code.
   *
   * @param string $aqi
   */
  public function setAqi($aqi)
  {
    $this->aqi = $aqi;
  }
  /**
   * @return string
   */
  public function getAqi()
  {
    return $this->aqi;
  }
  /**
   * The country/region requiring the custom AQI. Value should be provided using
   * [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
   * code.
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
class_alias(CustomLocalAqi::class, 'Google_Service_AirQuality_CustomLocalAqi');
