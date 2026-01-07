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

class IntegerPropertyOptions extends \Google\Model
{
  /**
   * There is no ranking order for the property. Results are not adjusted by
   * this property's value.
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
  protected $integerFacetingOptionsType = IntegerFacetingOptions::class;
  protected $integerFacetingOptionsDataType = '';
  /**
   * The maximum value of the property. The minimum and maximum values for the
   * property are used to rank results according to the ordered ranking.
   * Indexing requests with values greater than the maximum are accepted and
   * ranked with the same weight as items indexed with the maximum value.
   *
   * @var string
   */
  public $maximumValue;
  /**
   * The minimum value of the property. The minimum and maximum values for the
   * property are used to rank results according to the ordered ranking.
   * Indexing requests with values less than the minimum are accepted and ranked
   * with the same weight as items indexed with the minimum value.
   *
   * @var string
   */
  public $minimumValue;
  protected $operatorOptionsType = IntegerOperatorOptions::class;
  protected $operatorOptionsDataType = '';
  /**
   * Used to specify the ordered ranking for the integer. Can only be used if
   * isRepeatable is false.
   *
   * @var string
   */
  public $orderedRanking;

  /**
   * If set, describes integer faceting options for the given integer property.
   * The corresponding integer property should be marked isFacetable.
   *
   * @param IntegerFacetingOptions $integerFacetingOptions
   */
  public function setIntegerFacetingOptions(IntegerFacetingOptions $integerFacetingOptions)
  {
    $this->integerFacetingOptions = $integerFacetingOptions;
  }
  /**
   * @return IntegerFacetingOptions
   */
  public function getIntegerFacetingOptions()
  {
    return $this->integerFacetingOptions;
  }
  /**
   * The maximum value of the property. The minimum and maximum values for the
   * property are used to rank results according to the ordered ranking.
   * Indexing requests with values greater than the maximum are accepted and
   * ranked with the same weight as items indexed with the maximum value.
   *
   * @param string $maximumValue
   */
  public function setMaximumValue($maximumValue)
  {
    $this->maximumValue = $maximumValue;
  }
  /**
   * @return string
   */
  public function getMaximumValue()
  {
    return $this->maximumValue;
  }
  /**
   * The minimum value of the property. The minimum and maximum values for the
   * property are used to rank results according to the ordered ranking.
   * Indexing requests with values less than the minimum are accepted and ranked
   * with the same weight as items indexed with the minimum value.
   *
   * @param string $minimumValue
   */
  public function setMinimumValue($minimumValue)
  {
    $this->minimumValue = $minimumValue;
  }
  /**
   * @return string
   */
  public function getMinimumValue()
  {
    return $this->minimumValue;
  }
  /**
   * If set, describes how the integer should be used as a search operator.
   *
   * @param IntegerOperatorOptions $operatorOptions
   */
  public function setOperatorOptions(IntegerOperatorOptions $operatorOptions)
  {
    $this->operatorOptions = $operatorOptions;
  }
  /**
   * @return IntegerOperatorOptions
   */
  public function getOperatorOptions()
  {
    return $this->operatorOptions;
  }
  /**
   * Used to specify the ordered ranking for the integer. Can only be used if
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntegerPropertyOptions::class, 'Google_Service_CloudSearch_IntegerPropertyOptions');
