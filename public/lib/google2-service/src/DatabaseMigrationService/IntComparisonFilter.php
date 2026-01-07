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

namespace Google\Service\DatabaseMigrationService;

class IntComparisonFilter extends \Google\Model
{
  /**
   * Value comparison unspecified.
   */
  public const VALUE_COMPARISON_VALUE_COMPARISON_UNSPECIFIED = 'VALUE_COMPARISON_UNSPECIFIED';
  /**
   * Value is smaller than the Compare value.
   */
  public const VALUE_COMPARISON_VALUE_COMPARISON_IF_VALUE_SMALLER_THAN = 'VALUE_COMPARISON_IF_VALUE_SMALLER_THAN';
  /**
   * Value is smaller or equal than the Compare value.
   */
  public const VALUE_COMPARISON_VALUE_COMPARISON_IF_VALUE_SMALLER_EQUAL_THAN = 'VALUE_COMPARISON_IF_VALUE_SMALLER_EQUAL_THAN';
  /**
   * Value is larger than the Compare value.
   */
  public const VALUE_COMPARISON_VALUE_COMPARISON_IF_VALUE_LARGER_THAN = 'VALUE_COMPARISON_IF_VALUE_LARGER_THAN';
  /**
   * Value is larger or equal than the Compare value.
   */
  public const VALUE_COMPARISON_VALUE_COMPARISON_IF_VALUE_LARGER_EQUAL_THAN = 'VALUE_COMPARISON_IF_VALUE_LARGER_EQUAL_THAN';
  /**
   * Required. Integer compare value to be used
   *
   * @var string
   */
  public $value;
  /**
   * Required. Relation between source value and compare value
   *
   * @var string
   */
  public $valueComparison;

  /**
   * Required. Integer compare value to be used
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Required. Relation between source value and compare value
   *
   * Accepted values: VALUE_COMPARISON_UNSPECIFIED,
   * VALUE_COMPARISON_IF_VALUE_SMALLER_THAN,
   * VALUE_COMPARISON_IF_VALUE_SMALLER_EQUAL_THAN,
   * VALUE_COMPARISON_IF_VALUE_LARGER_THAN,
   * VALUE_COMPARISON_IF_VALUE_LARGER_EQUAL_THAN
   *
   * @param self::VALUE_COMPARISON_* $valueComparison
   */
  public function setValueComparison($valueComparison)
  {
    $this->valueComparison = $valueComparison;
  }
  /**
   * @return self::VALUE_COMPARISON_*
   */
  public function getValueComparison()
  {
    return $this->valueComparison;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntComparisonFilter::class, 'Google_Service_DatabaseMigrationService_IntComparisonFilter');
