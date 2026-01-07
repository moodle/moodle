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

class Scope3FlightSegment extends \Google\Model
{
  /**
   * Unspecified cabin class.
   */
  public const CABIN_CLASS_CABIN_CLASS_UNSPECIFIED = 'CABIN_CLASS_UNSPECIFIED';
  /**
   * Economy class.
   */
  public const CABIN_CLASS_ECONOMY = 'ECONOMY';
  /**
   * Premium economy class.
   */
  public const CABIN_CLASS_PREMIUM_ECONOMY = 'PREMIUM_ECONOMY';
  /**
   * Business class.
   */
  public const CABIN_CLASS_BUSINESS = 'BUSINESS';
  /**
   * First class.
   */
  public const CABIN_CLASS_FIRST = 'FIRST';
  /**
   * Required. The cabin class of the flight.
   *
   * @var string
   */
  public $cabinClass;
  /**
   * Optional. 2-character [IATA carrier
   * code](https://www.iata.org/en/publications/directories/code-search/), e.g.
   * `KE`. This is required if specific flight matching is desired. Otherwise,
   * this is unused for typical flight and distance-based emissions models. This
   * could be both operating and marketing carrier code (i.e. codeshare is
   * covered).
   *
   * @var string
   */
  public $carrierCode;
  protected $departureDateType = Date::class;
  protected $departureDateDataType = '';
  /**
   * Optional. 3-character [IATA airport
   * code](https://www.iata.org/en/publications/directories/code-search/) for
   * flight destination, e.g. `ICN`. This is used to match specific flight if
   * provided alongside origin, carrier, and flight number. If there is no
   * match, we will first try to match the flight to a typical flight between
   * the provided origin and destination airports. Otherwise, we will use the
   * distance-based emissions model if the flight distance is provided.
   *
   * @var string
   */
  public $destination;
  /**
   * Optional. Distance in kilometers, e.g. `2423`, from [1, 2.5e16) km. This is
   * used to match a flight to distance-based emissions when origin and
   * destination are not provided or there are no matching typical flights.
   *
   * @var string
   */
  public $distanceKm;
  /**
   * Optional. Up to 4-digit [flight
   * number](https://en.wikipedia.org/wiki/Flight_number), e.g. `71`, from [1,
   * 9999]. This is first used to match a specific flight if a flight number is
   * specified alongside origin, destination, and carrier. If a flight number is
   * not specified, we will first try to match the flight to a typical flight
   * between the provided origin and destination airports. If that fails and/or
   * origin & destination are not provided, we will use the distance-based
   * emissions model based on the flight distance provided.
   *
   * @var int
   */
  public $flightNumber;
  /**
   * Optional. 3-character [IATA airport
   * code](https://www.iata.org/en/publications/directories/code-search/) for
   * flight origin, e.g. `YVR`. This is used to match specific flight if
   * provided alongside destination, carrier, and flight number. If there is no
   * match, we will first try to match the flight to a typical flight between
   * the provided origin and destination airports. Otherwise, we will use the
   * distance-based emissions model if the flight distance is provided.
   *
   * @var string
   */
  public $origin;

  /**
   * Required. The cabin class of the flight.
   *
   * Accepted values: CABIN_CLASS_UNSPECIFIED, ECONOMY, PREMIUM_ECONOMY,
   * BUSINESS, FIRST
   *
   * @param self::CABIN_CLASS_* $cabinClass
   */
  public function setCabinClass($cabinClass)
  {
    $this->cabinClass = $cabinClass;
  }
  /**
   * @return self::CABIN_CLASS_*
   */
  public function getCabinClass()
  {
    return $this->cabinClass;
  }
  /**
   * Optional. 2-character [IATA carrier
   * code](https://www.iata.org/en/publications/directories/code-search/), e.g.
   * `KE`. This is required if specific flight matching is desired. Otherwise,
   * this is unused for typical flight and distance-based emissions models. This
   * could be both operating and marketing carrier code (i.e. codeshare is
   * covered).
   *
   * @param string $carrierCode
   */
  public function setCarrierCode($carrierCode)
  {
    $this->carrierCode = $carrierCode;
  }
  /**
   * @return string
   */
  public function getCarrierCode()
  {
    return $this->carrierCode;
  }
  /**
   * Required. Date of the flight in the time zone of the origin airport. Only
   * year is required for typical flight and distance-based emissions models
   * (month and day values are ignored and therefore, can be either omitted, set
   * to 0, or set to a valid date for those cases). Correspondingly, if a
   * specific date is not provided for TIM emissions, we will fallback to
   * typical flight (or distance-based) emissions.
   *
   * @param Date $departureDate
   */
  public function setDepartureDate(Date $departureDate)
  {
    $this->departureDate = $departureDate;
  }
  /**
   * @return Date
   */
  public function getDepartureDate()
  {
    return $this->departureDate;
  }
  /**
   * Optional. 3-character [IATA airport
   * code](https://www.iata.org/en/publications/directories/code-search/) for
   * flight destination, e.g. `ICN`. This is used to match specific flight if
   * provided alongside origin, carrier, and flight number. If there is no
   * match, we will first try to match the flight to a typical flight between
   * the provided origin and destination airports. Otherwise, we will use the
   * distance-based emissions model if the flight distance is provided.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Optional. Distance in kilometers, e.g. `2423`, from [1, 2.5e16) km. This is
   * used to match a flight to distance-based emissions when origin and
   * destination are not provided or there are no matching typical flights.
   *
   * @param string $distanceKm
   */
  public function setDistanceKm($distanceKm)
  {
    $this->distanceKm = $distanceKm;
  }
  /**
   * @return string
   */
  public function getDistanceKm()
  {
    return $this->distanceKm;
  }
  /**
   * Optional. Up to 4-digit [flight
   * number](https://en.wikipedia.org/wiki/Flight_number), e.g. `71`, from [1,
   * 9999]. This is first used to match a specific flight if a flight number is
   * specified alongside origin, destination, and carrier. If a flight number is
   * not specified, we will first try to match the flight to a typical flight
   * between the provided origin and destination airports. If that fails and/or
   * origin & destination are not provided, we will use the distance-based
   * emissions model based on the flight distance provided.
   *
   * @param int $flightNumber
   */
  public function setFlightNumber($flightNumber)
  {
    $this->flightNumber = $flightNumber;
  }
  /**
   * @return int
   */
  public function getFlightNumber()
  {
    return $this->flightNumber;
  }
  /**
   * Optional. 3-character [IATA airport
   * code](https://www.iata.org/en/publications/directories/code-search/) for
   * flight origin, e.g. `YVR`. This is used to match specific flight if
   * provided alongside destination, carrier, and flight number. If there is no
   * match, we will first try to match the flight to a typical flight between
   * the provided origin and destination airports. Otherwise, we will use the
   * distance-based emissions model if the flight distance is provided.
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Scope3FlightSegment::class, 'Google_Service_TravelImpactModel_Scope3FlightSegment');
