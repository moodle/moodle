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

class FlightCarrier extends \Google\Model
{
  protected $airlineAllianceLogoType = Image::class;
  protected $airlineAllianceLogoDataType = '';
  protected $airlineLogoType = Image::class;
  protected $airlineLogoDataType = '';
  protected $airlineNameType = LocalizedString::class;
  protected $airlineNameDataType = '';
  /**
   * Two character IATA airline code of the marketing carrier (as opposed to
   * operating carrier). Exactly one of this or `carrierIcaoCode` needs to be
   * provided for `carrier` and `operatingCarrier`. eg: "LX" for Swiss Air
   *
   * @var string
   */
  public $carrierIataCode;
  /**
   * Three character ICAO airline code of the marketing carrier (as opposed to
   * operating carrier). Exactly one of this or `carrierIataCode` needs to be
   * provided for `carrier` and `operatingCarrier`. eg: "EZY" for Easy Jet
   *
   * @var string
   */
  public $carrierIcaoCode;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#flightCarrier"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $wideAirlineLogoType = Image::class;
  protected $wideAirlineLogoDataType = '';

  /**
   * A logo for the airline alliance, displayed below the QR code that the
   * passenger scans to board.
   *
   * @param Image $airlineAllianceLogo
   */
  public function setAirlineAllianceLogo(Image $airlineAllianceLogo)
  {
    $this->airlineAllianceLogo = $airlineAllianceLogo;
  }
  /**
   * @return Image
   */
  public function getAirlineAllianceLogo()
  {
    return $this->airlineAllianceLogo;
  }
  /**
   * A logo for the airline described by carrierIataCode and
   * localizedAirlineName. This logo will be rendered at the top of the detailed
   * card view.
   *
   * @param Image $airlineLogo
   */
  public function setAirlineLogo(Image $airlineLogo)
  {
    $this->airlineLogo = $airlineLogo;
  }
  /**
   * @return Image
   */
  public function getAirlineLogo()
  {
    return $this->airlineLogo;
  }
  /**
   * A localized name of the airline specified by carrierIataCode. If unset,
   * `issuer_name` or `localized_issuer_name` from `FlightClass` will be used
   * for display purposes. eg: "Swiss Air" for "LX"
   *
   * @param LocalizedString $airlineName
   */
  public function setAirlineName(LocalizedString $airlineName)
  {
    $this->airlineName = $airlineName;
  }
  /**
   * @return LocalizedString
   */
  public function getAirlineName()
  {
    return $this->airlineName;
  }
  /**
   * Two character IATA airline code of the marketing carrier (as opposed to
   * operating carrier). Exactly one of this or `carrierIcaoCode` needs to be
   * provided for `carrier` and `operatingCarrier`. eg: "LX" for Swiss Air
   *
   * @param string $carrierIataCode
   */
  public function setCarrierIataCode($carrierIataCode)
  {
    $this->carrierIataCode = $carrierIataCode;
  }
  /**
   * @return string
   */
  public function getCarrierIataCode()
  {
    return $this->carrierIataCode;
  }
  /**
   * Three character ICAO airline code of the marketing carrier (as opposed to
   * operating carrier). Exactly one of this or `carrierIataCode` needs to be
   * provided for `carrier` and `operatingCarrier`. eg: "EZY" for Easy Jet
   *
   * @param string $carrierIcaoCode
   */
  public function setCarrierIcaoCode($carrierIcaoCode)
  {
    $this->carrierIcaoCode = $carrierIcaoCode;
  }
  /**
   * @return string
   */
  public function getCarrierIcaoCode()
  {
    return $this->carrierIcaoCode;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#flightCarrier"`.
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
   * The wide logo of the airline. When provided, this will be used in place of
   * the airline logo in the top left of the card view.
   *
   * @param Image $wideAirlineLogo
   */
  public function setWideAirlineLogo(Image $wideAirlineLogo)
  {
    $this->wideAirlineLogo = $wideAirlineLogo;
  }
  /**
   * @return Image
   */
  public function getWideAirlineLogo()
  {
    return $this->wideAirlineLogo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlightCarrier::class, 'Google_Service_Walletobjects_FlightCarrier');
