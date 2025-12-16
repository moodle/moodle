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

namespace Google\Service\TravelImpactModel;

class Scope3FlightEmissions extends \Google\Model
{
  /**
   * Unspecified data type.
   */
  public const SOURCE_SCOPE3_DATA_TYPE_UNSPECIFIED = 'SCOPE3_DATA_TYPE_UNSPECIFIED';
  /**
   * TIM-based emissions given origin, destination, carrier, flight number,
   * departure date, and year.
   */
  public const SOURCE_TIM_EMISSIONS = 'TIM_EMISSIONS';
  /**
   * Typical flight emissions given origin, destination, and year.
   */
  public const SOURCE_TYPICAL_FLIGHT_EMISSIONS = 'TYPICAL_FLIGHT_EMISSIONS';
  /**
   * Distance-based emissions based on the distance traveled and year.
   */
  public const SOURCE_DISTANCE_BASED_EMISSIONS = 'DISTANCE_BASED_EMISSIONS';
  protected $flightType = Scope3FlightSegment::class;
  protected $flightDataType = '';
  /**
   * Optional. The source of the emissions data.
   *
   * @var string
   */
  public $source;
  /**
   * Optional. Tank-to-wake flight emissions per passenger based on the
   * requested info.
   *
   * @var string
   */
  public $ttwEmissionsGramsPerPax;
  /**
   * Optional. Well-to-tank flight emissions per passenger based on the
   * requested info.
   *
   * @var string
   */
  public $wttEmissionsGramsPerPax;
  /**
   * Optional. Total flight emissions (sum of well-to-tank and tank-to-wake) per
   * passenger based on the requested info. This is the total emissions and
   * unless you have specific reasons for using TTW or WTT emissions, you should
   * use this number.
   *
   * @var string
   */
  public $wtwEmissionsGramsPerPax;

  /**
   * Required. Matches the flight identifiers in the request.
   *
   * @param Scope3FlightSegment $flight
   */
  public function setFlight(Scope3FlightSegment $flight)
  {
    $this->flight = $flight;
  }
  /**
   * @return Scope3FlightSegment
   */
  public function getFlight()
  {
    return $this->flight;
  }
  /**
   * Optional. The source of the emissions data.
   *
   * Accepted values: SCOPE3_DATA_TYPE_UNSPECIFIED, TIM_EMISSIONS,
   * TYPICAL_FLIGHT_EMISSIONS, DISTANCE_BASED_EMISSIONS
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Optional. Tank-to-wake flight emissions per passenger based on the
   * requested info.
   *
   * @param string $ttwEmissionsGramsPerPax
   */
  public function setTtwEmissionsGramsPerPax($ttwEmissionsGramsPerPax)
  {
    $this->ttwEmissionsGramsPerPax = $ttwEmissionsGramsPerPax;
  }
  /**
   * @return string
   */
  public function getTtwEmissionsGramsPerPax()
  {
    return $this->ttwEmissionsGramsPerPax;
  }
  /**
   * Optional. Well-to-tank flight emissions per passenger based on the
   * requested info.
   *
   * @param string $wttEmissionsGramsPerPax
   */
  public function setWttEmissionsGramsPerPax($wttEmissionsGramsPerPax)
  {
    $this->wttEmissionsGramsPerPax = $wttEmissionsGramsPerPax;
  }
  /**
   * @return string
   */
  public function getWttEmissionsGramsPerPax()
  {
    return $this->wttEmissionsGramsPerPax;
  }
  /**
   * Optional. Total flight emissions (sum of well-to-tank and tank-to-wake) per
   * passenger based on the requested info. This is the total emissions and
   * unless you have specific reasons for using TTW or WTT emissions, you should
   * use this number.
   *
   * @param string $wtwEmissionsGramsPerPax
   */
  public function setWtwEmissionsGramsPerPax($wtwEmissionsGramsPerPax)
  {
    $this->wtwEmissionsGramsPerPax = $wtwEmissionsGramsPerPax;
  }
  /**
   * @return string
   */
  public function getWtwEmissionsGramsPerPax()
  {
    return $this->wtwEmissionsGramsPerPax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scope3FlightEmissions::class, 'Google_Service_TravelImpactModel_Scope3FlightEmissions');
