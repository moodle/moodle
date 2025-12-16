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

namespace Google\Service\Datastore;

class PropertyFilter extends \Google\Model
{
  /**
   * Unspecified. This value must not be used.
   */
  public const OP_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * The given `property` is less than the given `value`. Requires: * That
   * `property` comes first in `order_by`.
   */
  public const OP_LESS_THAN = 'LESS_THAN';
  /**
   * The given `property` is less than or equal to the given `value`. Requires:
   * * That `property` comes first in `order_by`.
   */
  public const OP_LESS_THAN_OR_EQUAL = 'LESS_THAN_OR_EQUAL';
  /**
   * The given `property` is greater than the given `value`. Requires: * That
   * `property` comes first in `order_by`.
   */
  public const OP_GREATER_THAN = 'GREATER_THAN';
  /**
   * The given `property` is greater than or equal to the given `value`.
   * Requires: * That `property` comes first in `order_by`.
   */
  public const OP_GREATER_THAN_OR_EQUAL = 'GREATER_THAN_OR_EQUAL';
  /**
   * The given `property` is equal to the given `value`.
   */
  public const OP_EQUAL = 'EQUAL';
  /**
   * The given `property` is equal to at least one value in the given array.
   * Requires: * That `value` is a non-empty `ArrayValue`, subject to
   * disjunction limits. * No `NOT_IN` is in the same query.
   */
  public const OP_IN = 'IN';
  /**
   * The given `property` is not equal to the given `value`. Requires: * No
   * other `NOT_EQUAL` or `NOT_IN` is in the same query. * That `property` comes
   * first in the `order_by`.
   */
  public const OP_NOT_EQUAL = 'NOT_EQUAL';
  /**
   * Limit the result set to the given entity and its descendants. Requires: *
   * That `value` is an entity key. * All evaluated disjunctions must have the
   * same `HAS_ANCESTOR` filter.
   */
  public const OP_HAS_ANCESTOR = 'HAS_ANCESTOR';
  /**
   * The value of the `property` is not in the given array. Requires: * That
   * `value` is a non-empty `ArrayValue` with at most 10 values. * No other
   * `OR`, `IN`, `NOT_IN`, `NOT_EQUAL` is in the same query. * That `field`
   * comes first in the `order_by`.
   */
  public const OP_NOT_IN = 'NOT_IN';
  /**
   * The operator to filter by.
   *
   * @var string
   */
  public $op;
  protected $propertyType = PropertyReference::class;
  protected $propertyDataType = '';
  protected $valueType = Value::class;
  protected $valueDataType = '';

  /**
   * The operator to filter by.
   *
   * Accepted values: OPERATOR_UNSPECIFIED, LESS_THAN, LESS_THAN_OR_EQUAL,
   * GREATER_THAN, GREATER_THAN_OR_EQUAL, EQUAL, IN, NOT_EQUAL, HAS_ANCESTOR,
   * NOT_IN
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
   * The property to filter by.
   *
   * @param PropertyReference $property
   */
  public function setProperty(PropertyReference $property)
  {
    $this->property = $property;
  }
  /**
   * @return PropertyReference
   */
  public function getProperty()
  {
    return $this->property;
  }
  /**
   * The value to compare the property to.
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
class_alias(PropertyFilter::class, 'Google_Service_Datastore_PropertyFilter');
