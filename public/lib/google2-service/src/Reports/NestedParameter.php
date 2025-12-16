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

namespace Google\Service\Reports;

class NestedParameter extends \Google\Collection
{
  protected $collection_key = 'multiValue';
  /**
   * Boolean value of the parameter.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Integer value of the parameter.
   *
   * @var string
   */
  public $intValue;
  /**
   * Multiple boolean values of the parameter.
   *
   * @var bool[]
   */
  public $multiBoolValue;
  /**
   * Multiple integer values of the parameter.
   *
   * @var string[]
   */
  public $multiIntValue;
  /**
   * Multiple string values of the parameter.
   *
   * @var string[]
   */
  public $multiValue;
  /**
   * The name of the parameter.
   *
   * @var string
   */
  public $name;
  /**
   * String value of the parameter.
   *
   * @var string
   */
  public $value;

  /**
   * Boolean value of the parameter.
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
   * Integer value of the parameter.
   *
   * @param string $intValue
   */
  public function setIntValue($intValue)
  {
    $this->intValue = $intValue;
  }
  /**
   * @return string
   */
  public function getIntValue()
  {
    return $this->intValue;
  }
  /**
   * Multiple boolean values of the parameter.
   *
   * @param bool[] $multiBoolValue
   */
  public function setMultiBoolValue($multiBoolValue)
  {
    $this->multiBoolValue = $multiBoolValue;
  }
  /**
   * @return bool[]
   */
  public function getMultiBoolValue()
  {
    return $this->multiBoolValue;
  }
  /**
   * Multiple integer values of the parameter.
   *
   * @param string[] $multiIntValue
   */
  public function setMultiIntValue($multiIntValue)
  {
    $this->multiIntValue = $multiIntValue;
  }
  /**
   * @return string[]
   */
  public function getMultiIntValue()
  {
    return $this->multiIntValue;
  }
  /**
   * Multiple string values of the parameter.
   *
   * @param string[] $multiValue
   */
  public function setMultiValue($multiValue)
  {
    $this->multiValue = $multiValue;
  }
  /**
   * @return string[]
   */
  public function getMultiValue()
  {
    return $this->multiValue;
  }
  /**
   * The name of the parameter.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * String value of the parameter.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NestedParameter::class, 'Google_Service_Reports_NestedParameter');
