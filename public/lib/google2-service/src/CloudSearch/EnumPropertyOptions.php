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

namespace Google\Service\CloudSearch;

class EnumPropertyOptions extends \Google\Collection
{
  /**
   * There is no ranking order for the property. Results aren't adjusted by this
   * property's value.
   */
  public const ORDERED_RANKING_NO_ORDER = 'NO_ORDER';
  /**
   * This property is ranked in ascending order. Lower values indicate lower
   * ranking.
   */
  public const ORDERED_RANKING_ASCENDING = 'ASCENDING';
  /**
   * This property is ranked in descending order. Lower values indicate higher
   * ranking.
   */
  public const ORDERED_RANKING_DESCENDING = 'DESCENDING';
  protected $collection_key = 'possibleValues';
  protected $operatorOptionsType = EnumOperatorOptions::class;
  protected $operatorOptionsDataType = '';
  /**
   * Used to specify the ordered ranking for the enumeration that determines how
   * the integer values provided in the possible EnumValuePairs are used to rank
   * results. If specified, integer values must be provided for all possible
   * EnumValuePair values given for this property. Can only be used if
   * isRepeatable is false.
   *
   * @var string
   */
  public $orderedRanking;
  protected $possibleValuesType = EnumValuePair::class;
  protected $possibleValuesDataType = 'array';

  /**
   * If set, describes how the enum should be used as a search operator.
   *
   * @param EnumOperatorOptions $operatorOptions
   */
  public function setOperatorOptions(EnumOperatorOptions $operatorOptions)
  {
    $this->operatorOptions = $operatorOptions;
  }
  /**
   * @return EnumOperatorOptions
   */
  public function getOperatorOptions()
  {
    return $this->operatorOptions;
  }
  /**
   * Used to specify the ordered ranking for the enumeration that determines how
   * the integer values provided in the possible EnumValuePairs are used to rank
   * results. If specified, integer values must be provided for all possible
   * EnumValuePair values given for this property. Can only be used if
   * isRepeatable is false.
   *
   * Accepted values: NO_ORDER, ASCENDING, DESCENDING
   *
   * @param self::ORDERED_RANKING_* $orderedRanking
   */
  public function setOrderedRanking($orderedRanking)
  {
    $this->orderedRanking = $orderedRanking;
  }
  /**
   * @return self::ORDERED_RANKING_*
   */
  public function getOrderedRanking()
  {
    return $this->orderedRanking;
  }
  /**
   * The list of possible values for the enumeration property. All
   * EnumValuePairs must provide a string value. If you specify an integer value
   * for one EnumValuePair, then all possible EnumValuePairs must provide an
   * integer value. Both the string value and integer value must be unique over
   * all possible values. Once set, possible values cannot be removed or
   * modified. If you supply an ordered ranking and think you might insert
   * additional enum values in the future, leave gaps in the initial integer
   * values to allow adding a value in between previously registered values. The
   * maximum number of elements is 100.
   *
   * @param EnumValuePair[] $possibleValues
   */
  public function setPossibleValues($possibleValues)
  {
    $this->possibleValues = $possibleValues;
  }
  /**
   * @return EnumValuePair[]
   */
  public function getPossibleValues()
  {
    return $this->possibleValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnumPropertyOptions::class, 'Google_Service_CloudSearch_EnumPropertyOptions');
