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

class Value extends \Google\Model
{
  /**
   * The name of a carrier rate referring to a carrier rate defined in the same
   * rate group. Can only be set if all other fields are not set.
   *
   * @var string
   */
  public $carrierRateName;
  protected $flatRateType = Price::class;
  protected $flatRateDataType = '';
  /**
   * If true, then the product can't ship. Must be true when set, can only be
   * set if all other fields are not set.
   *
   * @var bool
   */
  public $noShipping;
  /**
   * A percentage of the price represented as a number in decimal notation (for
   * example, `"5.4"`). Can only be set if all other fields are not set.
   *
   * @var string
   */
  public $pricePercentage;
  /**
   * The name of a subtable. Can only be set in table cells (not for single
   * values), and only if all other fields are not set.
   *
   * @var string
   */
  public $subtableName;

  /**
   * The name of a carrier rate referring to a carrier rate defined in the same
   * rate group. Can only be set if all other fields are not set.
   *
   * @param string $carrierRateName
   */
  public function setCarrierRateName($carrierRateName)
  {
    $this->carrierRateName = $carrierRateName;
  }
  /**
   * @return string
   */
  public function getCarrierRateName()
  {
    return $this->carrierRateName;
  }
  /**
   * A flat rate. Can only be set if all other fields are not set.
   *
   * @param Price $flatRate
   */
  public function setFlatRate(Price $flatRate)
  {
    $this->flatRate = $flatRate;
  }
  /**
   * @return Price
   */
  public function getFlatRate()
  {
    return $this->flatRate;
  }
  /**
   * If true, then the product can't ship. Must be true when set, can only be
   * set if all other fields are not set.
   *
   * @param bool $noShipping
   */
  public function setNoShipping($noShipping)
  {
    $this->noShipping = $noShipping;
  }
  /**
   * @return bool
   */
  public function getNoShipping()
  {
    return $this->noShipping;
  }
  /**
   * A percentage of the price represented as a number in decimal notation (for
   * example, `"5.4"`). Can only be set if all other fields are not set.
   *
   * @param string $pricePercentage
   */
  public function setPricePercentage($pricePercentage)
  {
    $this->pricePercentage = $pricePercentage;
  }
  /**
   * @return string
   */
  public function getPricePercentage()
  {
    return $this->pricePercentage;
  }
  /**
   * The name of a subtable. Can only be set in table cells (not for single
   * values), and only if all other fields are not set.
   *
   * @param string $subtableName
   */
  public function setSubtableName($subtableName)
  {
    $this->subtableName = $subtableName;
  }
  /**
   * @return string
   */
  public function getSubtableName()
  {
    return $this->subtableName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Value::class, 'Google_Service_ShoppingContent_Value');
