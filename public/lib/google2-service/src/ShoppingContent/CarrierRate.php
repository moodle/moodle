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

namespace Google\Service\ShoppingContent;

class CarrierRate extends \Google\Model
{
  /**
   * Carrier service, such as `"UPS"` or `"Fedex"`. The list of supported
   * carriers can be retrieved through the `getSupportedCarriers` method.
   * Required.
   *
   * @var string
   */
  public $carrierName;
  /**
   * Carrier service, such as `"ground"` or `"2 days"`. The list of supported
   * services for a carrier can be retrieved through the `getSupportedCarriers`
   * method. Required.
   *
   * @var string
   */
  public $carrierService;
  protected $flatAdjustmentType = Price::class;
  protected $flatAdjustmentDataType = '';
  /**
   * Name of the carrier rate. Must be unique per rate group. Required.
   *
   * @var string
   */
  public $name;
  /**
   * Shipping origin for this carrier rate. Required.
   *
   * @var string
   */
  public $originPostalCode;
  /**
   * Multiplicative shipping rate modifier as a number in decimal notation. Can
   * be negative. For example `"5.4"` increases the rate by 5.4%, `"-3"`
   * decreases the rate by 3%. Optional.
   *
   * @var string
   */
  public $percentageAdjustment;

  /**
   * Carrier service, such as `"UPS"` or `"Fedex"`. The list of supported
   * carriers can be retrieved through the `getSupportedCarriers` method.
   * Required.
   *
   * @param string $carrierName
   */
  public function setCarrierName($carrierName)
  {
    $this->carrierName = $carrierName;
  }
  /**
   * @return string
   */
  public function getCarrierName()
  {
    return $this->carrierName;
  }
  /**
   * Carrier service, such as `"ground"` or `"2 days"`. The list of supported
   * services for a carrier can be retrieved through the `getSupportedCarriers`
   * method. Required.
   *
   * @param string $carrierService
   */
  public function setCarrierService($carrierService)
  {
    $this->carrierService = $carrierService;
  }
  /**
   * @return string
   */
  public function getCarrierService()
  {
    return $this->carrierService;
  }
  /**
   * Additive shipping rate modifier. Can be negative. For example `{ "value":
   * "1", "currency" : "USD" }` adds $1 to the rate, `{ "value": "-3",
   * "currency" : "USD" }` removes $3 from the rate. Optional.
   *
   * @param Price $flatAdjustment
   */
  public function setFlatAdjustment(Price $flatAdjustment)
  {
    $this->flatAdjustment = $flatAdjustment;
  }
  /**
   * @return Price
   */
  public function getFlatAdjustment()
  {
    return $this->flatAdjustment;
  }
  /**
   * Name of the carrier rate. Must be unique per rate group. Required.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Shipping origin for this carrier rate. Required.
   *
   * @param string $originPostalCode
   */
  public function setOriginPostalCode($originPostalCode)
  {
    $this->originPostalCode = $originPostalCode;
  }
  /**
   * @return string
   */
  public function getOriginPostalCode()
  {
    return $this->originPostalCode;
  }
  /**
   * Multiplicative shipping rate modifier as a number in decimal notation. Can
   * be negative. For example `"5.4"` increases the rate by 5.4%, `"-3"`
   * decreases the rate by 3%. Optional.
   *
   * @param string $percentageAdjustment
   */
  public function setPercentageAdjustment($percentageAdjustment)
  {
    $this->percentageAdjustment = $percentageAdjustment;
  }
  /**
   * @return string
   */
  public function getPercentageAdjustment()
  {
    return $this->percentageAdjustment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CarrierRate::class, 'Google_Service_ShoppingContent_CarrierRate');
