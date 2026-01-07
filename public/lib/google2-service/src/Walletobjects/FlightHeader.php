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

namespace Google\Service\Walletobjects;

class FlightHeader extends \Google\Model
{
  protected $carrierType = FlightCarrier::class;
  protected $carrierDataType = '';
  /**
   * The flight number without IATA carrier code. This field should contain only
   * digits. This is a required property of `flightHeader`. eg: "123"
   *
   * @var string
   */
  public $flightNumber;
  /**
   * Override value to use for flight number. The default value used for display
   * purposes is carrier + flight_number. If a different value needs to be shown
   * to passengers, use this field to override the default behavior. eg: "XX1234
   * / YY576"
   *
   * @var string
   */
  public $flightNumberDisplayOverride;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#flightHeader"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $operatingCarrierType = FlightCarrier::class;
  protected $operatingCarrierDataType = '';
  /**
   * The flight number used by the operating carrier without IATA carrier code.
   * This field should contain only digits. eg: "234"
   *
   * @var string
   */
  public $operatingFlightNumber;

  /**
   * Information about airline carrier. This is a required property of
   * `flightHeader`.
   *
   * @param FlightCarrier $carrier
   */
  public function setCarrier(FlightCarrier $carrier)
  {
    $this->carrier = $carrier;
  }
  /**
   * @return FlightCarrier
   */
  public function getCarrier()
  {
    return $this->carrier;
  }
  /**
   * The flight number without IATA carrier code. This field should contain only
   * digits. This is a required property of `flightHeader`. eg: "123"
   *
   * @param string $flightNumber
   */
  public function setFlightNumber($flightNumber)
  {
    $this->flightNumber = $flightNumber;
  }
  /**
   * @return string
   */
  public function getFlightNumber()
  {
    return $this->flightNumber;
  }
  /**
   * Override value to use for flight number. The default value used for display
   * purposes is carrier + flight_number. If a different value needs to be shown
   * to passengers, use this field to override the default behavior. eg: "XX1234
   * / YY576"
   *
   * @param string $flightNumberDisplayOverride
   */
  public function setFlightNumberDisplayOverride($flightNumberDisplayOverride)
  {
    $this->flightNumberDisplayOverride = $flightNumberDisplayOverride;
  }
  /**
   * @return string
   */
  public function getFlightNumberDisplayOverride()
  {
    return $this->flightNumberDisplayOverride;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#flightHeader"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Information about operating airline carrier.
   *
   * @param FlightCarrier $operatingCarrier
   */
  public function setOperatingCarrier(FlightCarrier $operatingCarrier)
  {
    $this->operatingCarrier = $operatingCarrier;
  }
  /**
   * @return FlightCarrier
   */
  public function getOperatingCarrier()
  {
    return $this->operatingCarrier;
  }
  /**
   * The flight number used by the operating carrier without IATA carrier code.
   * This field should contain only digits. eg: "234"
   *
   * @param string $operatingFlightNumber
   */
  public function setOperatingFlightNumber($operatingFlightNumber)
  {
    $this->operatingFlightNumber = $operatingFlightNumber;
  }
  /**
   * @return string
   */
  public function getOperatingFlightNumber()
  {
    return $this->operatingFlightNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlightHeader::class, 'Google_Service_Walletobjects_FlightHeader');
