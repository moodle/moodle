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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1ParameterSpec extends \Google\Collection
{
  /**
   * By default, no scaling is applied.
   */
  public const SCALE_TYPE_NONE = 'NONE';
  /**
   * Scales the feasible space to (0, 1) linearly.
   */
  public const SCALE_TYPE_UNIT_LINEAR_SCALE = 'UNIT_LINEAR_SCALE';
  /**
   * Scales the feasible space logarithmically to (0, 1). The entire feasible
   * space must be strictly positive.
   */
  public const SCALE_TYPE_UNIT_LOG_SCALE = 'UNIT_LOG_SCALE';
  /**
   * Scales the feasible space "reverse" logarithmically to (0, 1). The result
   * is that values close to the top of the feasible space are spread out more
   * than points near the bottom. The entire feasible space must be strictly
   * positive.
   */
  public const SCALE_TYPE_UNIT_REVERSE_LOG_SCALE = 'UNIT_REVERSE_LOG_SCALE';
  /**
   * You must specify a valid type. Using this unspecified type will result in
   * an error.
   */
  public const TYPE_PARAMETER_TYPE_UNSPECIFIED = 'PARAMETER_TYPE_UNSPECIFIED';
  /**
   * Type for real-valued parameters.
   */
  public const TYPE_DOUBLE = 'DOUBLE';
  /**
   * Type for integral parameters.
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * The parameter is categorical, with a value chosen from the categories
   * field.
   */
  public const TYPE_CATEGORICAL = 'CATEGORICAL';
  /**
   * The parameter is real valued, with a fixed set of feasible points. If
   * `type==DISCRETE`, feasible_points must be provided, and {`min_value`,
   * `max_value`} will be ignored.
   */
  public const TYPE_DISCRETE = 'DISCRETE';
  protected $collection_key = 'discreteValues';
  /**
   * Required if type is `CATEGORICAL`. The list of possible categories.
   *
   * @var string[]
   */
  public $categoricalValues;
  /**
   * Required if type is `DISCRETE`. A list of feasible points. The list should
   * be in strictly increasing order. For instance, this parameter might have
   * possible settings of 1.5, 2.5, and 4.0. This list should not contain more
   * than 1,000 values.
   *
   * @var []
   */
  public $discreteValues;
  /**
   * Required if type is `DOUBLE` or `INTEGER`. This field should be unset if
   * type is `CATEGORICAL`. This value should be integers if type is `INTEGER`.
   *
   * @var 
   */
  public $maxValue;
  /**
   * Required if type is `DOUBLE` or `INTEGER`. This field should be unset if
   * type is `CATEGORICAL`. This value should be integers if type is INTEGER.
   *
   * @var 
   */
  public $minValue;
  /**
   * Required. The parameter name must be unique amongst all ParameterConfigs in
   * a HyperparameterSpec message. E.g., "learning_rate".
   *
   * @var string
   */
  public $parameterName;
  /**
   * Optional. How the parameter should be scaled to the hypercube. Leave unset
   * for categorical parameters. Some kind of scaling is strongly recommended
   * for real or integral parameters (e.g., `UNIT_LINEAR_SCALE`).
   *
   * @var string
   */
  public $scaleType;
  /**
   * Required. The type of the parameter.
   *
   * @var string
   */
  public $type;

  /**
   * Required if type is `CATEGORICAL`. The list of possible categories.
   *
   * @param string[] $categoricalValues
   */
  public function setCategoricalValues($categoricalValues)
  {
    $this->categoricalValues = $categoricalValues;
  }
  /**
   * @return string[]
   */
  public function getCategoricalValues()
  {
    return $this->categoricalValues;
  }
  public function setDiscreteValues($discreteValues)
  {
    $this->discreteValues = $discreteValues;
  }
  public function getDiscreteValues()
  {
    return $this->discreteValues;
  }
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Required. The parameter name must be unique amongst all ParameterConfigs in
   * a HyperparameterSpec message. E.g., "learning_rate".
   *
   * @param string $parameterName
   */
  public function setParameterName($parameterName)
  {
    $this->parameterName = $parameterName;
  }
  /**
   * @return string
   */
  public function getParameterName()
  {
    return $this->parameterName;
  }
  /**
   * Optional. How the parameter should be scaled to the hypercube. Leave unset
   * for categorical parameters. Some kind of scaling is strongly recommended
   * for real or integral parameters (e.g., `UNIT_LINEAR_SCALE`).
   *
   * Accepted values: NONE, UNIT_LINEAR_SCALE, UNIT_LOG_SCALE,
   * UNIT_REVERSE_LOG_SCALE
   *
   * @param self::SCALE_TYPE_* $scaleType
   */
  public function setScaleType($scaleType)
  {
    $this->scaleType = $scaleType;
  }
  /**
   * @return self::SCALE_TYPE_*
   */
  public function getScaleType()
  {
    return $this->scaleType;
  }
  /**
   * Required. The type of the parameter.
   *
   * Accepted values: PARAMETER_TYPE_UNSPECIFIED, DOUBLE, INTEGER, CATEGORICAL,
   * DISCRETE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1ParameterSpec::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1ParameterSpec');
