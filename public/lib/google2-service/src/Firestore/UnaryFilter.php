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

class UnaryFilter extends \Google\Model
{
  /**
   * Unspecified. This value must not be used.
   */
  public const OP_OPERATOR_UNSPECIFIED = 'OPERATOR_UNSPECIFIED';
  /**
   * The given `field` is equal to `NaN`.
   */
  public const OP_IS_NAN = 'IS_NAN';
  /**
   * The given `field` is equal to `NULL`.
   */
  public const OP_IS_NULL = 'IS_NULL';
  /**
   * The given `field` is not equal to `NaN`. Requires: * No other `NOT_EQUAL`,
   * `NOT_IN`, `IS_NOT_NULL`, or `IS_NOT_NAN`. * That `field` comes first in the
   * `order_by`.
   */
  public const OP_IS_NOT_NAN = 'IS_NOT_NAN';
  /**
   * The given `field` is not equal to `NULL`. Requires: * A single `NOT_EQUAL`,
   * `NOT_IN`, `IS_NOT_NULL`, or `IS_NOT_NAN`. * That `field` comes first in the
   * `order_by`.
   */
  public const OP_IS_NOT_NULL = 'IS_NOT_NULL';
  protected $fieldType = FieldReference::class;
  protected $fieldDataType = '';
  /**
   * The unary operator to apply.
   *
   * @var string
   */
  public $op;

  /**
   * The field to which to apply the operator.
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
   * The unary operator to apply.
   *
   * Accepted values: OPERATOR_UNSPECIFIED, IS_NAN, IS_NULL, IS_NOT_NAN,
   * IS_NOT_NULL
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnaryFilter::class, 'Google_Service_Firestore_UnaryFilter');
