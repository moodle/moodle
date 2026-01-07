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

namespace Google\Service\AnalyticsData;

class NumericFilter extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const OPERATION_OPERATION_UNSPECIFIED = 'OPERATION_UNSPECIFIED';
  /**
   * Equal
   */
  public const OPERATION_EQUAL = 'EQUAL';
  /**
   * Less than
   */
  public const OPERATION_LESS_THAN = 'LESS_THAN';
  /**
   * Less than or equal
   */
  public const OPERATION_LESS_THAN_OR_EQUAL = 'LESS_THAN_OR_EQUAL';
  /**
   * Greater than
   */
  public const OPERATION_GREATER_THAN = 'GREATER_THAN';
  /**
   * Greater than or equal
   */
  public const OPERATION_GREATER_THAN_OR_EQUAL = 'GREATER_THAN_OR_EQUAL';
  /**
   * The operation type for this filter.
   *
   * @var string
   */
  public $operation;
  protected $valueType = NumericValue::class;
  protected $valueDataType = '';

  /**
   * The operation type for this filter.
   *
   * Accepted values: OPERATION_UNSPECIFIED, EQUAL, LESS_THAN,
   * LESS_THAN_OR_EQUAL, GREATER_THAN, GREATER_THAN_OR_EQUAL
   *
   * @param self::OPERATION_* $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return self::OPERATION_*
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * A numeric value or a date value.
   *
   * @param NumericValue $value
   */
  public function setValue(NumericValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return NumericValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NumericFilter::class, 'Google_Service_AnalyticsData_NumericFilter');
