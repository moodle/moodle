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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoCondition extends \Google\Model
{
  public const OPERATOR_UNSET = 'UNSET';
  public const OPERATOR_EQUALS = 'EQUALS';
  public const OPERATOR_CONTAINS = 'CONTAINS';
  public const OPERATOR_LESS_THAN = 'LESS_THAN';
  public const OPERATOR_GREATER_THAN = 'GREATER_THAN';
  public const OPERATOR_EXISTS = 'EXISTS';
  public const OPERATOR_DOES_NOT_EXIST = 'DOES_NOT_EXIST';
  public const OPERATOR_IS_EMPTY = 'IS_EMPTY';
  public const OPERATOR_IS_NOT_EMPTY = 'IS_NOT_EMPTY';
  /**
   * Key that's evaluated against the `value`. Please note the data type of the
   * runtime value associated with the key should match the data type of
   * `value`, else an IllegalArgumentException is thrown.
   *
   * @var string
   */
  public $eventPropertyKey;
  /**
   * Operator used to evaluate the condition. Please note that an operator with
   * an inappropriate key/value operand will result in IllegalArgumentException,
   * e.g. CONTAINS with boolean key/value pair.
   *
   * @var string
   */
  public $operator;
  protected $valueType = EnterpriseCrmEventbusProtoValueType::class;
  protected $valueDataType = '';

  /**
   * Key that's evaluated against the `value`. Please note the data type of the
   * runtime value associated with the key should match the data type of
   * `value`, else an IllegalArgumentException is thrown.
   *
   * @param string $eventPropertyKey
   */
  public function setEventPropertyKey($eventPropertyKey)
  {
    $this->eventPropertyKey = $eventPropertyKey;
  }
  /**
   * @return string
   */
  public function getEventPropertyKey()
  {
    return $this->eventPropertyKey;
  }
  /**
   * Operator used to evaluate the condition. Please note that an operator with
   * an inappropriate key/value operand will result in IllegalArgumentException,
   * e.g. CONTAINS with boolean key/value pair.
   *
   * Accepted values: UNSET, EQUALS, CONTAINS, LESS_THAN, GREATER_THAN, EXISTS,
   * DOES_NOT_EXIST, IS_EMPTY, IS_NOT_EMPTY
   *
   * @param self::OPERATOR_* $operator
   */
  public function setOperator($operator)
  {
    $this->operator = $operator;
  }
  /**
   * @return self::OPERATOR_*
   */
  public function getOperator()
  {
    return $this->operator;
  }
  /**
   * Value that's checked for the key.
   *
   * @param EnterpriseCrmEventbusProtoValueType $value
   */
  public function setValue(EnterpriseCrmEventbusProtoValueType $value)
  {
    $this->value = $value;
  }
  /**
   * @return EnterpriseCrmEventbusProtoValueType
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoCondition::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoCondition');
