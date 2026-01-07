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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Condition extends \Google\Model
{
  /**
   * Unused
   */
  public const OPERATOR_RELATIONAL_OPERATOR_UNSPECIFIED = 'RELATIONAL_OPERATOR_UNSPECIFIED';
  /**
   * Equal. Attempts to match even with incompatible types.
   */
  public const OPERATOR_EQUAL_TO = 'EQUAL_TO';
  /**
   * Not equal to. Attempts to match even with incompatible types.
   */
  public const OPERATOR_NOT_EQUAL_TO = 'NOT_EQUAL_TO';
  /**
   * Greater than.
   */
  public const OPERATOR_GREATER_THAN = 'GREATER_THAN';
  /**
   * Less than.
   */
  public const OPERATOR_LESS_THAN = 'LESS_THAN';
  /**
   * Greater than or equals.
   */
  public const OPERATOR_GREATER_THAN_OR_EQUALS = 'GREATER_THAN_OR_EQUALS';
  /**
   * Less than or equals.
   */
  public const OPERATOR_LESS_THAN_OR_EQUALS = 'LESS_THAN_OR_EQUALS';
  /**
   * Exists
   */
  public const OPERATOR_EXISTS = 'EXISTS';
  protected $fieldType = GooglePrivacyDlpV2FieldId::class;
  protected $fieldDataType = '';
  /**
   * Required. Operator used to compare the field or infoType to the value.
   *
   * @var string
   */
  public $operator;
  protected $valueType = GooglePrivacyDlpV2Value::class;
  protected $valueDataType = '';

  /**
   * Required. Field within the record this condition is evaluated against.
   *
   * @param GooglePrivacyDlpV2FieldId $field
   */
  public function setField(GooglePrivacyDlpV2FieldId $field)
  {
    $this->field = $field;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * Required. Operator used to compare the field or infoType to the value.
   *
   * Accepted values: RELATIONAL_OPERATOR_UNSPECIFIED, EQUAL_TO, NOT_EQUAL_TO,
   * GREATER_THAN, LESS_THAN, GREATER_THAN_OR_EQUALS, LESS_THAN_OR_EQUALS,
   * EXISTS
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
   * Value to compare against. [Mandatory, except for `EXISTS` tests.]
   *
   * @param GooglePrivacyDlpV2Value $value
   */
  public function setValue(GooglePrivacyDlpV2Value $value)
  {
    $this->value = $value;
  }
  /**
   * @return GooglePrivacyDlpV2Value
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Condition::class, 'Google_Service_DLP_GooglePrivacyDlpV2Condition');
