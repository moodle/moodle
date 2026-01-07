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

namespace Google\Service\Dfareporting;

class FieldFilter extends \Google\Model
{
  /**
   * The left hand side of the expression is unknown. This value is unused.
   */
  public const MATCH_TYPE_LHS_MATCH_TYPE_UNKNOWN = 'LHS_MATCH_TYPE_UNKNOWN';
  /**
   * The left hand side of the expression is equals or unrestricted. It is the
   * default value.
   */
  public const MATCH_TYPE_EQUALS_OR_UNRESTRICTED = 'EQUALS_OR_UNRESTRICTED';
  /**
   * The left hand side of the expression is equals.
   */
  public const MATCH_TYPE_EQUALS = 'EQUALS';
  /**
   * The left hand side of the expression is unrestricted. Unrestricted is used
   * to target fields with no restrictions. For example, country targeting
   * fields hold a list of countries. If the list is empty, we consider the
   * element value to have no restrictions.
   */
  public const MATCH_TYPE_UNRESTRICTED = 'UNRESTRICTED';
  /**
   * Left hand side of the expression is not equals. Not equals specifies which
   * fields should not be targeted.
   */
  public const MATCH_TYPE_NOT_EQUALS = 'NOT_EQUALS';
  /**
   * The right hand side of the expression is unknown. This value is unused.
   */
  public const VALUE_TYPE_RHS_VALUE_TYPE_UNKNOWN = 'RHS_VALUE_TYPE_UNKNOWN';
  /**
   * The right hand side of the expression is a string.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * The right hand side of the expression is a request value.
   */
  public const VALUE_TYPE_REQUEST = 'REQUEST';
  /**
   * The right hand side of the expression is a boolean.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * The right hand side of the expression is a dependent field value.
   */
  public const VALUE_TYPE_DEPENDENT = 'DEPENDENT';
  /**
   * Optional. The boolean values, only applicable when rhs_value_type is BOOL.
   *
   * @var bool
   */
  public $boolValue;
  protected $dependentFieldValueType = DependentFieldValue::class;
  protected $dependentFieldValueDataType = '';
  /**
   * Optional. The field ID on the left hand side of the expression.
   *
   * @var int
   */
  public $fieldId;
  /**
   * Optional. Left hand side of the expression match type.
   *
   * @var string
   */
  public $matchType;
  protected $requestValueType = RequestValue::class;
  protected $requestValueDataType = '';
  /**
   * Optional. The string value, only applicable when rhs_value_type is STRING.
   *
   * @var string
   */
  public $stringValue;
  /**
   * Optional. Right hand side of the expression.
   *
   * @var string
   */
  public $valueType;

  /**
   * Optional. The boolean values, only applicable when rhs_value_type is BOOL.
   *
   * @param bool $boolValue
   */
  public function setBoolValue($boolValue)
  {
    $this->boolValue = $boolValue;
  }
  /**
   * @return bool
   */
  public function getBoolValue()
  {
    return $this->boolValue;
  }
  /**
   * Optional. The dependent values, only applicable when rhs_value_type is
   * DEPENDENT.
   *
   * @param DependentFieldValue $dependentFieldValue
   */
  public function setDependentFieldValue(DependentFieldValue $dependentFieldValue)
  {
    $this->dependentFieldValue = $dependentFieldValue;
  }
  /**
   * @return DependentFieldValue
   */
  public function getDependentFieldValue()
  {
    return $this->dependentFieldValue;
  }
  /**
   * Optional. The field ID on the left hand side of the expression.
   *
   * @param int $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return int
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * Optional. Left hand side of the expression match type.
   *
   * Accepted values: LHS_MATCH_TYPE_UNKNOWN, EQUALS_OR_UNRESTRICTED, EQUALS,
   * UNRESTRICTED, NOT_EQUALS
   *
   * @param self::MATCH_TYPE_* $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return self::MATCH_TYPE_*
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * Optional. The request value, only applicable when rhs_value_type is
   * REQUEST.
   *
   * @param RequestValue $requestValue
   */
  public function setRequestValue(RequestValue $requestValue)
  {
    $this->requestValue = $requestValue;
  }
  /**
   * @return RequestValue
   */
  public function getRequestValue()
  {
    return $this->requestValue;
  }
  /**
   * Optional. The string value, only applicable when rhs_value_type is STRING.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
  /**
   * Optional. Right hand side of the expression.
   *
   * Accepted values: RHS_VALUE_TYPE_UNKNOWN, STRING, REQUEST, BOOL, DEPENDENT
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldFilter::class, 'Google_Service_Dfareporting_FieldFilter');
