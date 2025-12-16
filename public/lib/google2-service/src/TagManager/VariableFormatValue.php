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

namespace Google\Service\TagManager;

class VariableFormatValue extends \Google\Model
{
  public const CASE_CONVERSION_TYPE_none = 'none';
  /**
   * The option to convert a variable value to lowercase.
   */
  public const CASE_CONVERSION_TYPE_lowercase = 'lowercase';
  /**
   * The option to convert a variable value to uppercase.
   */
  public const CASE_CONVERSION_TYPE_uppercase = 'uppercase';
  /**
   * The option to convert a string-type variable value to either lowercase or
   * uppercase.
   *
   * @var string
   */
  public $caseConversionType;
  protected $convertFalseToValueType = Parameter::class;
  protected $convertFalseToValueDataType = '';
  protected $convertNullToValueType = Parameter::class;
  protected $convertNullToValueDataType = '';
  protected $convertTrueToValueType = Parameter::class;
  protected $convertTrueToValueDataType = '';
  protected $convertUndefinedToValueType = Parameter::class;
  protected $convertUndefinedToValueDataType = '';

  /**
   * The option to convert a string-type variable value to either lowercase or
   * uppercase.
   *
   * Accepted values: none, lowercase, uppercase
   *
   * @param self::CASE_CONVERSION_TYPE_* $caseConversionType
   */
  public function setCaseConversionType($caseConversionType)
  {
    $this->caseConversionType = $caseConversionType;
  }
  /**
   * @return self::CASE_CONVERSION_TYPE_*
   */
  public function getCaseConversionType()
  {
    return $this->caseConversionType;
  }
  /**
   * The value to convert if a variable value is false.
   *
   * @param Parameter $convertFalseToValue
   */
  public function setConvertFalseToValue(Parameter $convertFalseToValue)
  {
    $this->convertFalseToValue = $convertFalseToValue;
  }
  /**
   * @return Parameter
   */
  public function getConvertFalseToValue()
  {
    return $this->convertFalseToValue;
  }
  /**
   * The value to convert if a variable value is null.
   *
   * @param Parameter $convertNullToValue
   */
  public function setConvertNullToValue(Parameter $convertNullToValue)
  {
    $this->convertNullToValue = $convertNullToValue;
  }
  /**
   * @return Parameter
   */
  public function getConvertNullToValue()
  {
    return $this->convertNullToValue;
  }
  /**
   * The value to convert if a variable value is true.
   *
   * @param Parameter $convertTrueToValue
   */
  public function setConvertTrueToValue(Parameter $convertTrueToValue)
  {
    $this->convertTrueToValue = $convertTrueToValue;
  }
  /**
   * @return Parameter
   */
  public function getConvertTrueToValue()
  {
    return $this->convertTrueToValue;
  }
  /**
   * The value to convert if a variable value is undefined.
   *
   * @param Parameter $convertUndefinedToValue
   */
  public function setConvertUndefinedToValue(Parameter $convertUndefinedToValue)
  {
    $this->convertUndefinedToValue = $convertUndefinedToValue;
  }
  /**
   * @return Parameter
   */
  public function getConvertUndefinedToValue()
  {
    return $this->convertUndefinedToValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VariableFormatValue::class, 'Google_Service_TagManager_VariableFormatValue');
