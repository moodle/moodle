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

namespace Google\Service\Firestore;

class FieldFilter extends \Google\Model
{
  /**
   * Unspecified. This value must not be used.
   */
  public const OP_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * The given `field` is less than the given `value`. Requires: * That `field`
   * come first in `order_by`.
   */
  public const OP_LESS_THAN = 'LESS_THAN';
  /**
   * The given `field` is less than or equal to the given `value`. Requires: *
   * That `field` come first in `order_by`.
   */
  public const OP_LESS_THAN_OR_EQUAL = 'LESS_THAN_OR_EQUAL';
  /**
   * The given `field` is greater than the given `value`. Requires: * That
   * `field` come first in `order_by`.
   */
  public const OP_GREATER_THAN = 'GREATER_THAN';
  /**
   * The given `field` is greater than or equal to the given `value`. Requires:
   * * That `field` come first in `order_by`.
   */
  public const OP_GREATER_THAN_OR_EQUAL = 'GREATER_THAN_OR_EQUAL';
  /**
   * The given `field` is equal to the given `value`.
   */
  public const OP_EQUAL = 'EQUAL';
  /**
   * The given `field` is not equal to the given `value`. Requires: * No other
   * `NOT_EQUAL`, `NOT_IN`, `IS_NOT_NULL`, or `IS_NOT_NAN`. * That `field` comes
   * first in the `order_by`.
   */
  public const OP_NOT_EQUAL = 'NOT_EQUAL';
  /**
   * The given `field` is an array that contains the given `value`.
   */
  public const OP_ARRAY_CONTAINS = 'ARRAY_CONTAINS';
  /**
   * The given `field` is equal to at least one value in the given array.
   * Requires: * That `value` is a non-empty `ArrayValue`, subject to
   * disjunction limits. * No `NOT_IN` filters in the same query.
   */
  public const OP_IN = 'IN';
  /**
   * The given `field` is an array that contains any of the values in the given
   * array. Requires: * That `value` is a non-empty `ArrayValue`, subject to
   * disjunction limits. * No other `ARRAY_CONTAINS_ANY` filters within the same
   * disjunction. * No `NOT_IN` filters in the same query.
   */
  public const OP_ARRAY_CONTAINS_ANY = 'ARRAY_CONTAINS_ANY';
  /**
   * The value of the `field` is not in the given array. Requires: * That
   * `value` is a non-empty `ArrayValue` with at most 10 values. * No other
   * `OR`, `IN`, `ARRAY_CONTAINS_ANY`, `NOT_IN`, `NOT_EQUAL`, `IS_NOT_NULL`, or
   * `IS_NOT_NAN`. * That `field` comes first in the `order_by`.
   */
  public const OP_NOT_IN = 'NOT_IN';
  protected $fieldType = FieldReference::class;
  protected $fieldDataType = '';
  /**
   * The operator to filter by.
   *
   * @var string
   */
  public $op;
  protected $valueType = Value::class;
  protected $valueDataType = '';

  /**
   * The field to filter by.
   *
   * @param FieldReference $field
   */
  public function setField(FieldReference $field)
  {
    $this->field = $field;
  }
  /**
   * @return FieldReference
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * The operator to filter by.
   *
   * Accepted values: OPERATOR_UNSPECIFIED, LESS_THAN, LESS_THAN_OR_EQUAL,
   * GREATER_THAN, GREATER_THAN_OR_EQUAL, EQUAL, NOT_EQUAL, ARRAY_CONTAINS, IN,
   * ARRAY_CONTAINS_ANY, NOT_IN
   *
   * @param self::OP_* $op
   */
  public function setOp($op)
  {
    $this->op = $op;
  }
  /**
   * @return self::OP_*
   */
  public function getOp()
  {
    return $this->op;
  }
  /**
   * The value to compare to.
   *
   * @param Value $value
   */
  public function setValue(Value $value)
  {
    $this->value = $value;
  }
  /**
   * @return Value
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldFilter::class, 'Google_Service_Firestore_FieldFilter');
