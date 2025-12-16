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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1ParameterDefinition extends \Google\Collection
{
  /**
   * Not used.
   */
  public const PARAMETER_TYPE_PARAMETER_TYPE_UNSPECIFIED = 'PARAMETER_TYPE_UNSPECIFIED';
  /**
   * Int64 type.
   */
  public const PARAMETER_TYPE_INT64 = 'INT64';
  /**
   * String type.
   */
  public const PARAMETER_TYPE_STRING = 'STRING';
  /**
   * Double type.
   */
  public const PARAMETER_TYPE_DOUBLE = 'DOUBLE';
  /**
   * Boolean type.
   */
  public const PARAMETER_TYPE_BOOLEAN = 'BOOLEAN';
  protected $collection_key = 'allowedValues';
  protected $allowedValuesType = GoogleCloudChannelV1Value::class;
  protected $allowedValuesDataType = 'array';
  protected $maxValueType = GoogleCloudChannelV1Value::class;
  protected $maxValueDataType = '';
  protected $minValueType = GoogleCloudChannelV1Value::class;
  protected $minValueDataType = '';
  /**
   * Name of the parameter.
   *
   * @var string
   */
  public $name;
  /**
   * If set to true, parameter is optional to purchase this Offer.
   *
   * @var bool
   */
  public $optional;
  /**
   * Data type of the parameter. Minimal value, Maximum value and allowed values
   * will use specified data type here.
   *
   * @var string
   */
  public $parameterType;

  /**
   * If not empty, parameter values must be drawn from this list. For example,
   * [us-west1, us-west2, ...] Applicable to STRING parameter type.
   *
   * @param GoogleCloudChannelV1Value[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return GoogleCloudChannelV1Value[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
  /**
   * Maximum value of the parameter, if applicable. Inclusive. For example,
   * maximum seats when purchasing Google Workspace Business Standard.
   * Applicable to INT64 and DOUBLE parameter types.
   *
   * @param GoogleCloudChannelV1Value $maxValue
   */
  public function setMaxValue(GoogleCloudChannelV1Value $maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return GoogleCloudChannelV1Value
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Minimal value of the parameter, if applicable. Inclusive. For example,
   * minimal commitment when purchasing Anthos is 0.01. Applicable to INT64 and
   * DOUBLE parameter types.
   *
   * @param GoogleCloudChannelV1Value $minValue
   */
  public function setMinValue(GoogleCloudChannelV1Value $minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return GoogleCloudChannelV1Value
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Name of the parameter.
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
   * If set to true, parameter is optional to purchase this Offer.
   *
   * @param bool $optional
   */
  public function setOptional($optional)
  {
    $this->optional = $optional;
  }
  /**
   * @return bool
   */
  public function getOptional()
  {
    return $this->optional;
  }
  /**
   * Data type of the parameter. Minimal value, Maximum value and allowed values
   * will use specified data type here.
   *
   * Accepted values: PARAMETER_TYPE_UNSPECIFIED, INT64, STRING, DOUBLE, BOOLEAN
   *
   * @param self::PARAMETER_TYPE_* $parameterType
   */
  public function setParameterType($parameterType)
  {
    $this->parameterType = $parameterType;
  }
  /**
   * @return self::PARAMETER_TYPE_*
   */
  public function getParameterType()
  {
    return $this->parameterType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ParameterDefinition::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ParameterDefinition');
