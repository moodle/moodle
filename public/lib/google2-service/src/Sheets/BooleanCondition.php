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

namespace Google\Service\Sheets;

class BooleanCondition extends \Google\Collection
{
  /**
   * The default value, do not use.
   */
  public const TYPE_CONDITION_TYPE_UNSPECIFIED = 'CONDITION_TYPE_UNSPECIFIED';
  /**
   * The cell's value must be greater than the condition's value. Supported by
   * data validation, conditional formatting and filters. Requires a single
   * ConditionValue.
   */
  public const TYPE_NUMBER_GREATER = 'NUMBER_GREATER';
  /**
   * The cell's value must be greater than or equal to the condition's value.
   * Supported by data validation, conditional formatting and filters. Requires
   * a single ConditionValue.
   */
  public const TYPE_NUMBER_GREATER_THAN_EQ = 'NUMBER_GREATER_THAN_EQ';
  /**
   * The cell's value must be less than the condition's value. Supported by data
   * validation, conditional formatting and filters. Requires a single
   * ConditionValue.
   */
  public const TYPE_NUMBER_LESS = 'NUMBER_LESS';
  /**
   * The cell's value must be less than or equal to the condition's value.
   * Supported by data validation, conditional formatting and filters. Requires
   * a single ConditionValue.
   */
  public const TYPE_NUMBER_LESS_THAN_EQ = 'NUMBER_LESS_THAN_EQ';
  /**
   * The cell's value must be equal to the condition's value. Supported by data
   * validation, conditional formatting and filters. Requires a single
   * ConditionValue for data validation, conditional formatting, and filters on
   * non-data source objects and at least one ConditionValue for filters on data
   * source objects.
   */
  public const TYPE_NUMBER_EQ = 'NUMBER_EQ';
  /**
   * The cell's value must be not equal to the condition's value. Supported by
   * data validation, conditional formatting and filters. Requires a single
   * ConditionValue for data validation, conditional formatting, and filters on
   * non-data source objects and at least one ConditionValue for filters on data
   * source objects.
   */
  public const TYPE_NUMBER_NOT_EQ = 'NUMBER_NOT_EQ';
  /**
   * The cell's value must be between the two condition values. Supported by
   * data validation, conditional formatting and filters. Requires exactly two
   * ConditionValues.
   */
  public const TYPE_NUMBER_BETWEEN = 'NUMBER_BETWEEN';
  /**
   * The cell's value must not be between the two condition values. Supported by
   * data validation, conditional formatting and filters. Requires exactly two
   * ConditionValues.
   */
  public const TYPE_NUMBER_NOT_BETWEEN = 'NUMBER_NOT_BETWEEN';
  /**
   * The cell's value must contain the condition's value. Supported by data
   * validation, conditional formatting and filters. Requires a single
   * ConditionValue.
   */
  public const TYPE_TEXT_CONTAINS = 'TEXT_CONTAINS';
  /**
   * The cell's value must not contain the condition's value. Supported by data
   * validation, conditional formatting and filters. Requires a single
   * ConditionValue.
   */
  public const TYPE_TEXT_NOT_CONTAINS = 'TEXT_NOT_CONTAINS';
  /**
   * The cell's value must start with the condition's value. Supported by
   * conditional formatting and filters. Requires a single ConditionValue.
   */
  public const TYPE_TEXT_STARTS_WITH = 'TEXT_STARTS_WITH';
  /**
   * The cell's value must end with the condition's value. Supported by
   * conditional formatting and filters. Requires a single ConditionValue.
   */
  public const TYPE_TEXT_ENDS_WITH = 'TEXT_ENDS_WITH';
  /**
   * The cell's value must be exactly the condition's value. Supported by data
   * validation, conditional formatting and filters. Requires a single
   * ConditionValue for data validation, conditional formatting, and filters on
   * non-data source objects and at least one ConditionValue for filters on data
   * source objects.
   */
  public const TYPE_TEXT_EQ = 'TEXT_EQ';
  /**
   * The cell's value must be a valid email address. Supported by data
   * validation. Requires no ConditionValues.
   */
  public const TYPE_TEXT_IS_EMAIL = 'TEXT_IS_EMAIL';
  /**
   * The cell's value must be a valid URL. Supported by data validation.
   * Requires no ConditionValues.
   */
  public const TYPE_TEXT_IS_URL = 'TEXT_IS_URL';
  /**
   * The cell's value must be the same date as the condition's value. Supported
   * by data validation, conditional formatting and filters. Requires a single
   * ConditionValue for data validation, conditional formatting, and filters on
   * non-data source objects and at least one ConditionValue for filters on data
   * source objects.
   */
  public const TYPE_DATE_EQ = 'DATE_EQ';
  /**
   * The cell's value must be before the date of the condition's value.
   * Supported by data validation, conditional formatting and filters. Requires
   * a single ConditionValue that may be a relative date.
   */
  public const TYPE_DATE_BEFORE = 'DATE_BEFORE';
  /**
   * The cell's value must be after the date of the condition's value. Supported
   * by data validation, conditional formatting and filters. Requires a single
   * ConditionValue that may be a relative date.
   */
  public const TYPE_DATE_AFTER = 'DATE_AFTER';
  /**
   * The cell's value must be on or before the date of the condition's value.
   * Supported by data validation. Requires a single ConditionValue that may be
   * a relative date.
   */
  public const TYPE_DATE_ON_OR_BEFORE = 'DATE_ON_OR_BEFORE';
  /**
   * The cell's value must be on or after the date of the condition's value.
   * Supported by data validation. Requires a single ConditionValue that may be
   * a relative date.
   */
  public const TYPE_DATE_ON_OR_AFTER = 'DATE_ON_OR_AFTER';
  /**
   * The cell's value must be between the dates of the two condition values.
   * Supported by data validation. Requires exactly two ConditionValues.
   */
  public const TYPE_DATE_BETWEEN = 'DATE_BETWEEN';
  /**
   * The cell's value must be outside the dates of the two condition values.
   * Supported by data validation. Requires exactly two ConditionValues.
   */
  public const TYPE_DATE_NOT_BETWEEN = 'DATE_NOT_BETWEEN';
  /**
   * The cell's value must be a date. Supported by data validation. Requires no
   * ConditionValues.
   */
  public const TYPE_DATE_IS_VALID = 'DATE_IS_VALID';
  /**
   * The cell's value must be listed in the grid in condition value's range.
   * Supported by data validation. Requires a single ConditionValue, and the
   * value must be a valid range in A1 notation.
   */
  public const TYPE_ONE_OF_RANGE = 'ONE_OF_RANGE';
  /**
   * The cell's value must be in the list of condition values. Supported by data
   * validation. Supports any number of condition values, one per item in the
   * list. Formulas are not supported in the values.
   */
  public const TYPE_ONE_OF_LIST = 'ONE_OF_LIST';
  /**
   * The cell's value must be empty. Supported by conditional formatting and
   * filters. Requires no ConditionValues.
   */
  public const TYPE_BLANK = 'BLANK';
  /**
   * The cell's value must not be empty. Supported by conditional formatting and
   * filters. Requires no ConditionValues.
   */
  public const TYPE_NOT_BLANK = 'NOT_BLANK';
  /**
   * The condition's formula must evaluate to true. Supported by data
   * validation, conditional formatting and filters. Not supported by data
   * source sheet filters. Requires a single ConditionValue.
   */
  public const TYPE_CUSTOM_FORMULA = 'CUSTOM_FORMULA';
  /**
   * The cell's value must be TRUE/FALSE or in the list of condition values.
   * Supported by data validation. Renders as a cell checkbox. Supports zero,
   * one or two ConditionValues. No values indicates the cell must be TRUE or
   * FALSE, where TRUE renders as checked and FALSE renders as unchecked. One
   * value indicates the cell will render as checked when it contains that value
   * and unchecked when it is blank. Two values indicate that the cell will
   * render as checked when it contains the first value and unchecked when it
   * contains the second value. For example, ["Yes","No"] indicates that the
   * cell will render a checked box when it has the value "Yes" and an unchecked
   * box when it has the value "No".
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * The cell's value must be exactly not the condition's value. Supported by
   * filters on data source objects. Requires at least one ConditionValue.
   */
  public const TYPE_TEXT_NOT_EQ = 'TEXT_NOT_EQ';
  /**
   * The cell's value must be exactly not the condition's value. Supported by
   * filters on data source objects. Requires at least one ConditionValue.
   */
  public const TYPE_DATE_NOT_EQ = 'DATE_NOT_EQ';
  /**
   * The cell's value must follow the pattern specified. Requires a single
   * ConditionValue.
   */
  public const TYPE_FILTER_EXPRESSION = 'FILTER_EXPRESSION';
  protected $collection_key = 'values';
  /**
   * The type of condition.
   *
   * @var string
   */
  public $type;
  protected $valuesType = ConditionValue::class;
  protected $valuesDataType = 'array';

  /**
   * The type of condition.
   *
   * Accepted values: CONDITION_TYPE_UNSPECIFIED, NUMBER_GREATER,
   * NUMBER_GREATER_THAN_EQ, NUMBER_LESS, NUMBER_LESS_THAN_EQ, NUMBER_EQ,
   * NUMBER_NOT_EQ, NUMBER_BETWEEN, NUMBER_NOT_BETWEEN, TEXT_CONTAINS,
   * TEXT_NOT_CONTAINS, TEXT_STARTS_WITH, TEXT_ENDS_WITH, TEXT_EQ,
   * TEXT_IS_EMAIL, TEXT_IS_URL, DATE_EQ, DATE_BEFORE, DATE_AFTER,
   * DATE_ON_OR_BEFORE, DATE_ON_OR_AFTER, DATE_BETWEEN, DATE_NOT_BETWEEN,
   * DATE_IS_VALID, ONE_OF_RANGE, ONE_OF_LIST, BLANK, NOT_BLANK, CUSTOM_FORMULA,
   * BOOLEAN, TEXT_NOT_EQ, DATE_NOT_EQ, FILTER_EXPRESSION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The values of the condition. The number of supported values depends on the
   * condition type. Some support zero values, others one or two values, and
   * ConditionType.ONE_OF_LIST supports an arbitrary number of values.
   *
   * @param ConditionValue[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return ConditionValue[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BooleanCondition::class, 'Google_Service_Sheets_BooleanCondition');
