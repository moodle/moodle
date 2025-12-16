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

class TypicalFlightEmissions extends \Google\Model
{
  protected $emissionsGramsPerPaxType = EmissionsGramsPerPax::class;
  protected $emissionsGramsPerPaxDataType = '';
  protected $marketType = Market::class;
  protected $marketDataType = '';

  /**
   * Optional. Typical flight emissions per passenger for requested market. Will
   * not be present if a typical emissions could not be computed. For the list
   * of reasons why typical flight emissions could not be computed, see
   * [GitHub](https://github.com/google/travel-impact-
   * model/blob/main/projects/typical_flight_emissions.md#step-7-validate-
   * dataset).
   *
   * @param EmissionsGramsPerPax $emissionsGramsPerPax
   */
  public function setEmissionsGramsPerPax(EmissionsGramsPerPax $emissionsGramsPerPax)
  {
    $this->emissionsGramsPerPax = $emissionsGramsPerPax;
  }
  /**
   * @return EmissionsGramsPerPax
   */
  public function getEmissionsGramsPerPax()
  {
    return $this->emissionsGramsPerPax;
  }
  /**
   * Required. Matches the flight identifiers in the request. Note: all IATA
   * codes are capitalized.
   *
   * @param Market $market
   */
  public function setMarket(Market $market)
  {
    $this->market = $market;
  }
  /**
   * @return Market
   */
  public function getMarket()
  {
    return $this->market;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TypicalFlightEmissions::class, 'Google_Service_TravelImpactModel_TypicalFlightEmissions');
