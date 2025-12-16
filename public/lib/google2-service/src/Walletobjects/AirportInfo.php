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

class AirportInfo extends \Google\Model
{
  /**
   * Three character IATA airport code. This is a required field for `origin`
   * and `destination`. Eg: "SFO"
   *
   * @var string
   */
  public $airportIataCode;
  protected $airportNameOverrideType = LocalizedString::class;
  protected $airportNameOverrideDataType = '';
  /**
   * A name of the gate. Eg: "B59" or "59"
   *
   * @var string
   */
  public $gate;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#airportInfo"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  /**
   * Terminal name. Eg: "INTL" or "I"
   *
   * @var string
   */
  public $terminal;

  /**
   * Three character IATA airport code. This is a required field for `origin`
   * and `destination`. Eg: "SFO"
   *
   * @param string $airportIataCode
   */
  public function setAirportIataCode($airportIataCode)
  {
    $this->airportIataCode = $airportIataCode;
  }
  /**
   * @return string
   */
  public function getAirportIataCode()
  {
    return $this->airportIataCode;
  }
  /**
   * Optional field that overrides the airport city name defined by IATA. By
   * default, Google takes the `airportIataCode` provided and maps it to the
   * official airport city name defined by IATA. Official IATA airport city
   * names can be found at IATA airport city names website. For example, for the
   * airport IATA code "LTN", IATA website tells us that the corresponding
   * airport city is "London". If this field is not populated, Google would
   * display "London". However, populating this field with a custom name (eg:
   * "London Luton") would override it.
   *
   * @param LocalizedString $airportNameOverride
   */
  public function setAirportNameOverride(LocalizedString $airportNameOverride)
  {
    $this->airportNameOverride = $airportNameOverride;
  }
  /**
   * @return LocalizedString
   */
  public function getAirportNameOverride()
  {
    return $this->airportNameOverride;
  }
  /**
   * A name of the gate. Eg: "B59" or "59"
   *
   * @param string $gate
   */
  public function setGate($gate)
  {
    $this->gate = $gate;
  }
  /**
   * @return string
   */
  public function getGate()
  {
    return $this->gate;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#airportInfo"`.
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
   * Terminal name. Eg: "INTL" or "I"
   *
   * @param string $terminal
   */
  public function setTerminal($terminal)
  {
    $this->terminal = $terminal;
  }
  /**
   * @return string
   */
  public function getTerminal()
  {
    return $this->terminal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AirportInfo::class, 'Google_Service_Walletobjects_AirportInfo');
