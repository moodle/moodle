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

class TemplateItem extends \Google\Model
{
  public const PREDEFINED_ITEM_PREDEFINED_ITEM_UNSPECIFIED = 'PREDEFINED_ITEM_UNSPECIFIED';
  public const PREDEFINED_ITEM_FREQUENT_FLYER_PROGRAM_NAME_AND_NUMBER = 'FREQUENT_FLYER_PROGRAM_NAME_AND_NUMBER';
  /**
   * Legacy alias for `FREQUENT_FLYER_PROGRAM_NAME_AND_NUMBER`. Deprecated.
   *
   * @deprecated
   */
  public const PREDEFINED_ITEM_frequentFlyerProgramNameAndNumber = 'frequentFlyerProgramNameAndNumber';
  public const PREDEFINED_ITEM_FLIGHT_NUMBER_AND_OPERATING_FLIGHT_NUMBER = 'FLIGHT_NUMBER_AND_OPERATING_FLIGHT_NUMBER';
  /**
   * Legacy alias for `FLIGHT_NUMBER_AND_OPERATING_FLIGHT_NUMBER`. Deprecated.
   *
   * @deprecated
   */
  public const PREDEFINED_ITEM_flightNumberAndOperatingFlightNumber = 'flightNumberAndOperatingFlightNumber';
  protected $firstValueType = FieldSelector::class;
  protected $firstValueDataType = '';
  /**
   * A predefined item to display. Only one of `firstValue` or `predefinedItem`
   * may be set.
   *
   * @var string
   */
  public $predefinedItem;
  protected $secondValueType = FieldSelector::class;
  protected $secondValueDataType = '';

  /**
   * A reference to a field to display. If both `firstValue` and `secondValue`
   * are populated, they will both appear as one item with a slash between them.
   * For example, values A and B would be shown as "A / B".
   *
   * @param FieldSelector $firstValue
   */
  public function setFirstValue(FieldSelector $firstValue)
  {
    $this->firstValue = $firstValue;
  }
  /**
   * @return FieldSelector
   */
  public function getFirstValue()
  {
    return $this->firstValue;
  }
  /**
   * A predefined item to display. Only one of `firstValue` or `predefinedItem`
   * may be set.
   *
   * Accepted values: PREDEFINED_ITEM_UNSPECIFIED,
   * FREQUENT_FLYER_PROGRAM_NAME_AND_NUMBER, frequentFlyerProgramNameAndNumber,
   * FLIGHT_NUMBER_AND_OPERATING_FLIGHT_NUMBER,
   * flightNumberAndOperatingFlightNumber
   *
   * @param self::PREDEFINED_ITEM_* $predefinedItem
   */
  public function setPredefinedItem($predefinedItem)
  {
    $this->predefinedItem = $predefinedItem;
  }
  /**
   * @return self::PREDEFINED_ITEM_*
   */
  public function getPredefinedItem()
  {
    return $this->predefinedItem;
  }
  /**
   * A reference to a field to display. This may only be populated if the
   * `firstValue` field is populated.
   *
   * @param FieldSelector $secondValue
   */
  public function setSecondValue(FieldSelector $secondValue)
  {
    $this->secondValue = $secondValue;
  }
  /**
   * @return FieldSelector
   */
  public function getSecondValue()
  {
    return $this->secondValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TemplateItem::class, 'Google_Service_Walletobjects_TemplateItem');
