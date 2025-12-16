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

namespace Google\Service\Cloudbilling;

class PricingExpression extends \Google\Collection
{
  protected $collection_key = 'tieredRates';
  /**
   * The base unit for the SKU which is the unit used in usage exports. Example:
   * "By"
   *
   * @var string
   */
  public $baseUnit;
  /**
   * Conversion factor for converting from price per usage_unit to price per
   * base_unit, and start_usage_amount to start_usage_amount in base_unit.
   * unit_price / base_unit_conversion_factor = price per base_unit.
   * start_usage_amount * base_unit_conversion_factor = start_usage_amount in
   * base_unit.
   *
   * @var 
   */
  public $baseUnitConversionFactor;
  /**
   * The base unit in human readable form. Example: "byte".
   *
   * @var string
   */
  public $baseUnitDescription;
  /**
   * The recommended quantity of units for displaying pricing info. When
   * displaying pricing info it is recommended to display: (unit_price *
   * display_quantity) per display_quantity usage_unit. This field does not
   * affect the pricing formula and is for display purposes only. Example: If
   * the unit_price is "0.0001 USD", the usage_unit is "GB" and the
   * display_quantity is "1000" then the recommended way of displaying the
   * pricing info is "0.10 USD per 1000 GB"
   *
   * @var 
   */
  public $displayQuantity;
  protected $tieredRatesType = TierRate::class;
  protected $tieredRatesDataType = 'array';
  /**
   * The short hand for unit of usage this pricing is specified in. Example:
   * usage_unit of "GiBy" means that usage is specified in "Gibi Byte".
   *
   * @var string
   */
  public $usageUnit;
  /**
   * The unit of usage in human readable form. Example: "gibi byte".
   *
   * @var string
   */
  public $usageUnitDescription;

  /**
   * The base unit for the SKU which is the unit used in usage exports. Example:
   * "By"
   *
   * @param string $baseUnit
   */
  public function setBaseUnit($baseUnit)
  {
    $this->baseUnit = $baseUnit;
  }
  /**
   * @return string
   */
  public function getBaseUnit()
  {
    return $this->baseUnit;
  }
  public function setBaseUnitConversionFactor($baseUnitConversionFactor)
  {
    $this->baseUnitConversionFactor = $baseUnitConversionFactor;
  }
  public function getBaseUnitConversionFactor()
  {
    return $this->baseUnitConversionFactor;
  }
  /**
   * The base unit in human readable form. Example: "byte".
   *
   * @param string $baseUnitDescription
   */
  public function setBaseUnitDescription($baseUnitDescription)
  {
    $this->baseUnitDescription = $baseUnitDescription;
  }
  /**
   * @return string
   */
  public function getBaseUnitDescription()
  {
    return $this->baseUnitDescription;
  }
  public function setDisplayQuantity($displayQuantity)
  {
    $this->displayQuantity = $displayQuantity;
  }
  public function getDisplayQuantity()
  {
    return $this->displayQuantity;
  }
  /**
   * The list of tiered rates for this pricing. The total cost is computed by
   * applying each of the tiered rates on usage. This repeated list is sorted by
   * ascending order of start_usage_amount.
   *
   * @param TierRate[] $tieredRates
   */
  public function setTieredRates($tieredRates)
  {
    $this->tieredRates = $tieredRates;
  }
  /**
   * @return TierRate[]
   */
  public function getTieredRates()
  {
    return $this->tieredRates;
  }
  /**
   * The short hand for unit of usage this pricing is specified in. Example:
   * usage_unit of "GiBy" means that usage is specified in "Gibi Byte".
   *
   * @param string $usageUnit
   */
  public function setUsageUnit($usageUnit)
  {
    $this->usageUnit = $usageUnit;
  }
  /**
   * @return string
   */
  public function getUsageUnit()
  {
    return $this->usageUnit;
  }
  /**
   * The unit of usage in human readable form. Example: "gibi byte".
   *
   * @param string $usageUnitDescription
   */
  public function setUsageUnitDescription($usageUnitDescription)
  {
    $this->usageUnitDescription = $usageUnitDescription;
  }
  /**
   * @return string
   */
  public function getUsageUnitDescription()
  {
    return $this->usageUnitDescription;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PricingExpression::class, 'Google_Service_Cloudbilling_PricingExpression');
