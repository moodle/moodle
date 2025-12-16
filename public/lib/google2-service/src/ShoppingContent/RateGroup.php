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

class RateGroup extends \Google\Collection
{
  protected $collection_key = 'subtables';
  /**
   * A list of shipping labels defining the products to which this rate group
   * applies to. This is a disjunction: only one of the labels has to match for
   * the rate group to apply. May only be empty for the last rate group of a
   * service. Required.
   *
   * @var string[]
   */
  public $applicableShippingLabels;
  protected $carrierRatesType = CarrierRate::class;
  protected $carrierRatesDataType = 'array';
  protected $mainTableType = Table::class;
  protected $mainTableDataType = '';
  /**
   * Name of the rate group. Optional. If set has to be unique within shipping
   * service.
   *
   * @var string
   */
  public $name;
  protected $singleValueType = Value::class;
  protected $singleValueDataType = '';
  protected $subtablesType = Table::class;
  protected $subtablesDataType = 'array';

  /**
   * A list of shipping labels defining the products to which this rate group
   * applies to. This is a disjunction: only one of the labels has to match for
   * the rate group to apply. May only be empty for the last rate group of a
   * service. Required.
   *
   * @param string[] $applicableShippingLabels
   */
  public function setApplicableShippingLabels($applicableShippingLabels)
  {
    $this->applicableShippingLabels = $applicableShippingLabels;
  }
  /**
   * @return string[]
   */
  public function getApplicableShippingLabels()
  {
    return $this->applicableShippingLabels;
  }
  /**
   * A list of carrier rates that can be referred to by `mainTable` or
   * `singleValue`.
   *
   * @param CarrierRate[] $carrierRates
   */
  public function setCarrierRates($carrierRates)
  {
    $this->carrierRates = $carrierRates;
  }
  /**
   * @return CarrierRate[]
   */
  public function getCarrierRates()
  {
    return $this->carrierRates;
  }
  /**
   * A table defining the rate group, when `singleValue` is not expressive
   * enough. Can only be set if `singleValue` is not set.
   *
   * @param Table $mainTable
   */
  public function setMainTable(Table $mainTable)
  {
    $this->mainTable = $mainTable;
  }
  /**
   * @return Table
   */
  public function getMainTable()
  {
    return $this->mainTable;
  }
  /**
   * Name of the rate group. Optional. If set has to be unique within shipping
   * service.
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
   * The value of the rate group (for example, flat rate $10). Can only be set
   * if `mainTable` and `subtables` are not set.
   *
   * @param Value $singleValue
   */
  public function setSingleValue(Value $singleValue)
  {
    $this->singleValue = $singleValue;
  }
  /**
   * @return Value
   */
  public function getSingleValue()
  {
    return $this->singleValue;
  }
  /**
   * A list of subtables referred to by `mainTable`. Can only be set if
   * `mainTable` is set.
   *
   * @param Table[] $subtables
   */
  public function setSubtables($subtables)
  {
    $this->subtables = $subtables;
  }
  /**
   * @return Table[]
   */
  public function getSubtables()
  {
    return $this->subtables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RateGroup::class, 'Google_Service_ShoppingContent_RateGroup');
