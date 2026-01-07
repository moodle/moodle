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

namespace Google\Service\ShoppingContent;

class CloudExportAdditionalProperties extends \Google\Collection
{
  protected $collection_key = 'textValue';
  /**
   * Boolean value of the given property. For example for a TV product, "True"
   * or "False" if the screen is UHD.
   *
   * @var bool
   */
  public $boolValue;
  /**
   * Float values of the given property. For example for a TV product 1.2345.
   * Maximum number of specified values for this field is 400. Values are stored
   * in an arbitrary but consistent order.
   *
   * @var float[]
   */
  public $floatValue;
  /**
   * Integer values of the given property. For example, 1080 for a screen
   * resolution of a TV product. Maximum number of specified values for this
   * field is 400. Values are stored in an arbitrary but consistent order.
   *
   * @var string[]
   */
  public $intValue;
  /**
   * Maximum float value of the given property. For example for a TV product
   * 100.00.
   *
   * @var float
   */
  public $maxValue;
  /**
   * Minimum float value of the given property. For example for a TV product
   * 1.00.
   *
   * @var float
   */
  public $minValue;
  /**
   * Name of the given property. For example, "Screen-Resolution" for a TV
   * product. Maximum string size is 256 characters.
   *
   * @var string
   */
  public $propertyName;
  /**
   * Text value of the given property. For example, "8K(UHD)" could be a text
   * value for a TV product. Maximum number of specified values for this field
   * is 400. Values are stored in an arbitrary but consistent order. Maximum
   * string size is 256 characters.
   *
   * @var string[]
   */
  public $textValue;
  /**
   * Unit of the given property. For example, "Pixels" for a TV product. Maximum
   * string size is 256 bytes.
   *
   * @var string
   */
  public $unitCode;

  /**
   * Boolean value of the given property. For example for a TV product, "True"
   * or "False" if the screen is UHD.
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
   * Float values of the given property. For example for a TV product 1.2345.
   * Maximum number of specified values for this field is 400. Values are stored
   * in an arbitrary but consistent order.
   *
   * @param float[] $floatValue
   */
  public function setFloatValue($floatValue)
  {
    $this->floatValue = $floatValue;
  }
  /**
   * @return float[]
   */
  public function getFloatValue()
  {
    return $this->floatValue;
  }
  /**
   * Integer values of the given property. For example, 1080 for a screen
   * resolution of a TV product. Maximum number of specified values for this
   * field is 400. Values are stored in an arbitrary but consistent order.
   *
   * @param string[] $intValue
   */
  public function setIntValue($intValue)
  {
    $this->intValue = $intValue;
  }
  /**
   * @return string[]
   */
  public function getIntValue()
  {
    return $this->intValue;
  }
  /**
   * Maximum float value of the given property. For example for a TV product
   * 100.00.
   *
   * @param float $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return float
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Minimum float value of the given property. For example for a TV product
   * 1.00.
   *
   * @param float $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return float
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Name of the given property. For example, "Screen-Resolution" for a TV
   * product. Maximum string size is 256 characters.
   *
   * @param string $propertyName
   */
  public function setPropertyName($propertyName)
  {
    $this->propertyName = $propertyName;
  }
  /**
   * @return string
   */
  public function getPropertyName()
  {
    return $this->propertyName;
  }
  /**
   * Text value of the given property. For example, "8K(UHD)" could be a text
   * value for a TV product. Maximum number of specified values for this field
   * is 400. Values are stored in an arbitrary but consistent order. Maximum
   * string size is 256 characters.
   *
   * @param string[] $textValue
   */
  public function setTextValue($textValue)
  {
    $this->textValue = $textValue;
  }
  /**
   * @return string[]
   */
  public function getTextValue()
  {
    return $this->textValue;
  }
  /**
   * Unit of the given property. For example, "Pixels" for a TV product. Maximum
   * string size is 256 bytes.
   *
   * @param string $unitCode
   */
  public function setUnitCode($unitCode)
  {
    $this->unitCode = $unitCode;
  }
  /**
   * @return string
   */
  public function getUnitCode()
  {
    return $this->unitCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudExportAdditionalProperties::class, 'Google_Service_ShoppingContent_CloudExportAdditionalProperties');
