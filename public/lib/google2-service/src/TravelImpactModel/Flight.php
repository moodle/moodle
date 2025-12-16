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

class Flight extends \Google\Model
{
  protected $departureDateType = Date::class;
  protected $departureDateDataType = '';
  /**
   * Required. IATA airport code for flight destination, e.g. "JFK".
   *
   * @var string
   */
  public $destination;
  /**
   * Required. Flight number, e.g. 324.
   *
   * @var int
   */
  public $flightNumber;
  /**
   * Required. IATA carrier code, e.g. "AA".
   *
   * @var string
   */
  public $operatingCarrierCode;
  /**
   * Required. IATA airport code for flight origin, e.g. "LHR".
   *
   * @var string
   */
  public $origin;

  /**
   * Required. Date of the flight in the time zone of the origin airport. Must
   * be a date in the present or future.
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
   * Required. IATA airport code for flight destination, e.g. "JFK".
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
   * Required. Flight number, e.g. 324.
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
   * Required. IATA carrier code, e.g. "AA".
   *
   * @param string $operatingCarrierCode
   */
  public function setOperatingCarrierCode($operatingCarrierCode)
  {
    $this->operatingCarrierCode = $operatingCarrierCode;
  }
  /**
   * @return string
   */
  public function getOperatingCarrierCode()
  {
    return $this->operatingCarrierCode;
  }
  /**
   * Required. IATA airport code for flight origin, e.g. "LHR".
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
class_alias(Flight::class, 'Google_Service_TravelImpactModel_Flight');
