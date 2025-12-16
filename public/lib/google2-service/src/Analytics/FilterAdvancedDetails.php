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

namespace Google\Service\Analytics;

class FilterAdvancedDetails extends \Google\Model
{
  /**
   * Indicates if the filter expressions are case sensitive.
   *
   * @var bool
   */
  public $caseSensitive;
  /**
   * Expression to extract from field A.
   *
   * @var string
   */
  public $extractA;
  /**
   * Expression to extract from field B.
   *
   * @var string
   */
  public $extractB;
  /**
   * Field A.
   *
   * @var string
   */
  public $fieldA;
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @var int
   */
  public $fieldAIndex;
  /**
   * Indicates if field A is required to match.
   *
   * @var bool
   */
  public $fieldARequired;
  /**
   * Field B.
   *
   * @var string
   */
  public $fieldB;
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @var int
   */
  public $fieldBIndex;
  /**
   * Indicates if field B is required to match.
   *
   * @var bool
   */
  public $fieldBRequired;
  /**
   * Expression used to construct the output value.
   *
   * @var string
   */
  public $outputConstructor;
  /**
   * Output field.
   *
   * @var string
   */
  public $outputToField;
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @var int
   */
  public $outputToFieldIndex;
  /**
   * Indicates if the existing value of the output field, if any, should be
   * overridden by the output expression.
   *
   * @var bool
   */
  public $overrideOutputField;

  /**
   * Indicates if the filter expressions are case sensitive.
   *
   * @param bool $caseSensitive
   */
  public function setCaseSensitive($caseSensitive)
  {
    $this->caseSensitive = $caseSensitive;
  }
  /**
   * @return bool
   */
  public function getCaseSensitive()
  {
    return $this->caseSensitive;
  }
  /**
   * Expression to extract from field A.
   *
   * @param string $extractA
   */
  public function setExtractA($extractA)
  {
    $this->extractA = $extractA;
  }
  /**
   * @return string
   */
  public function getExtractA()
  {
    return $this->extractA;
  }
  /**
   * Expression to extract from field B.
   *
   * @param string $extractB
   */
  public function setExtractB($extractB)
  {
    $this->extractB = $extractB;
  }
  /**
   * @return string
   */
  public function getExtractB()
  {
    return $this->extractB;
  }
  /**
   * Field A.
   *
   * @param string $fieldA
   */
  public function setFieldA($fieldA)
  {
    $this->fieldA = $fieldA;
  }
  /**
   * @return string
   */
  public function getFieldA()
  {
    return $this->fieldA;
  }
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @param int $fieldAIndex
   */
  public function setFieldAIndex($fieldAIndex)
  {
    $this->fieldAIndex = $fieldAIndex;
  }
  /**
   * @return int
   */
  public function getFieldAIndex()
  {
    return $this->fieldAIndex;
  }
  /**
   * Indicates if field A is required to match.
   *
   * @param bool $fieldARequired
   */
  public function setFieldARequired($fieldARequired)
  {
    $this->fieldARequired = $fieldARequired;
  }
  /**
   * @return bool
   */
  public function getFieldARequired()
  {
    return $this->fieldARequired;
  }
  /**
   * Field B.
   *
   * @param string $fieldB
   */
  public function setFieldB($fieldB)
  {
    $this->fieldB = $fieldB;
  }
  /**
   * @return string
   */
  public function getFieldB()
  {
    return $this->fieldB;
  }
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @param int $fieldBIndex
   */
  public function setFieldBIndex($fieldBIndex)
  {
    $this->fieldBIndex = $fieldBIndex;
  }
  /**
   * @return int
   */
  public function getFieldBIndex()
  {
    return $this->fieldBIndex;
  }
  /**
   * Indicates if field B is required to match.
   *
   * @param bool $fieldBRequired
   */
  public function setFieldBRequired($fieldBRequired)
  {
    $this->fieldBRequired = $fieldBRequired;
  }
  /**
   * @return bool
   */
  public function getFieldBRequired()
  {
    return $this->fieldBRequired;
  }
  /**
   * Expression used to construct the output value.
   *
   * @param string $outputConstructor
   */
  public function setOutputConstructor($outputConstructor)
  {
    $this->outputConstructor = $outputConstructor;
  }
  /**
   * @return string
   */
  public function getOutputConstructor()
  {
    return $this->outputConstructor;
  }
  /**
   * Output field.
   *
   * @param string $outputToField
   */
  public function setOutputToField($outputToField)
  {
    $this->outputToField = $outputToField;
  }
  /**
   * @return string
   */
  public function getOutputToField()
  {
    return $this->outputToField;
  }
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @param int $outputToFieldIndex
   */
  public function setOutputToFieldIndex($outputToFieldIndex)
  {
    $this->outputToFieldIndex = $outputToFieldIndex;
  }
  /**
   * @return int
   */
  public function getOutputToFieldIndex()
  {
    return $this->outputToFieldIndex;
  }
  /**
   * Indicates if the existing value of the output field, if any, should be
   * overridden by the output expression.
   *
   * @param bool $overrideOutputField
   */
  public function setOverrideOutputField($overrideOutputField)
  {
    $this->overrideOutputField = $overrideOutputField;
  }
  /**
   * @return bool
   */
  public function getOverrideOutputField()
  {
    return $this->overrideOutputField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterAdvancedDetails::class, 'Google_Service_Analytics_FilterAdvancedDetails');
