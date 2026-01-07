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

class GoogleCloudMlV1StudyConfigParameterSpec extends \Google\Collection
{
  /**
   * By default, no scaling is applied.
   */
  public const SCALE_TYPE_SCALE_TYPE_UNSPECIFIED = 'SCALE_TYPE_UNSPECIFIED';
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
  protected $collection_key = 'childParameterSpecs';
  protected $categoricalValueSpecType = GoogleCloudMlV1StudyConfigParameterSpecCategoricalValueSpec::class;
  protected $categoricalValueSpecDataType = '';
  protected $childParameterSpecsType = GoogleCloudMlV1StudyConfigParameterSpec::class;
  protected $childParameterSpecsDataType = 'array';
  protected $discreteValueSpecType = GoogleCloudMlV1StudyConfigParameterSpecDiscreteValueSpec::class;
  protected $discreteValueSpecDataType = '';
  protected $doubleValueSpecType = GoogleCloudMlV1StudyConfigParameterSpecDoubleValueSpec::class;
  protected $doubleValueSpecDataType = '';
  protected $integerValueSpecType = GoogleCloudMlV1StudyConfigParameterSpecIntegerValueSpec::class;
  protected $integerValueSpecDataType = '';
  /**
   * Required. The parameter name must be unique amongst all ParameterSpecs.
   *
   * @var string
   */
  public $parameter;
  protected $parentCategoricalValuesType = GoogleCloudMlV1StudyConfigParameterSpecMatchingParentCategoricalValueSpec::class;
  protected $parentCategoricalValuesDataType = '';
  protected $parentDiscreteValuesType = GoogleCloudMlV1StudyConfigParameterSpecMatchingParentDiscreteValueSpec::class;
  protected $parentDiscreteValuesDataType = '';
  protected $parentIntValuesType = GoogleCloudMlV1StudyConfigParameterSpecMatchingParentIntValueSpec::class;
  protected $parentIntValuesDataType = '';
  /**
   * How the parameter should be scaled. Leave unset for categorical parameters.
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
   * The value spec for a 'CATEGORICAL' parameter.
   *
   * @param GoogleCloudMlV1StudyConfigParameterSpecCategoricalValueSpec $categoricalValueSpec
   */
  public function setCategoricalValueSpec(GoogleCloudMlV1StudyConfigParameterSpecCategoricalValueSpec $categoricalValueSpec)
  {
    $this->categoricalValueSpec = $categoricalValueSpec;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecCategoricalValueSpec
   */
  public function getCategoricalValueSpec()
  {
    return $this->categoricalValueSpec;
  }
  /**
   * A child node is active if the parameter's value matches the child node's
   * matching_parent_values. If two items in child_parameter_specs have the same
   * name, they must have disjoint matching_parent_values.
   *
   * @param GoogleCloudMlV1StudyConfigParameterSpec[] $childParameterSpecs
   */
  public function setChildParameterSpecs($childParameterSpecs)
  {
    $this->childParameterSpecs = $childParameterSpecs;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpec[]
   */
  public function getChildParameterSpecs()
  {
    return $this->childParameterSpecs;
  }
  /**
   * The value spec for a 'DISCRETE' parameter.
   *
   * @param GoogleCloudMlV1StudyConfigParameterSpecDiscreteValueSpec $discreteValueSpec
   */
  public function setDiscreteValueSpec(GoogleCloudMlV1StudyConfigParameterSpecDiscreteValueSpec $discreteValueSpec)
  {
    $this->discreteValueSpec = $discreteValueSpec;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecDiscreteValueSpec
   */
  public function getDiscreteValueSpec()
  {
    return $this->discreteValueSpec;
  }
  /**
   * The value spec for a 'DOUBLE' parameter.
   *
   * @param GoogleCloudMlV1StudyConfigParameterSpecDoubleValueSpec $doubleValueSpec
   */
  public function setDoubleValueSpec(GoogleCloudMlV1StudyConfigParameterSpecDoubleValueSpec $doubleValueSpec)
  {
    $this->doubleValueSpec = $doubleValueSpec;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecDoubleValueSpec
   */
  public function getDoubleValueSpec()
  {
    return $this->doubleValueSpec;
  }
  /**
   * The value spec for an 'INTEGER' parameter.
   *
   * @param GoogleCloudMlV1StudyConfigParameterSpecIntegerValueSpec $integerValueSpec
   */
  public function setIntegerValueSpec(GoogleCloudMlV1StudyConfigParameterSpecIntegerValueSpec $integerValueSpec)
  {
    $this->integerValueSpec = $integerValueSpec;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecIntegerValueSpec
   */
  public function getIntegerValueSpec()
  {
    return $this->integerValueSpec;
  }
  /**
   * Required. The parameter name must be unique amongst all ParameterSpecs.
   *
   * @param string $parameter
   */
  public function setParameter($parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return string
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * @param GoogleCloudMlV1StudyConfigParameterSpecMatchingParentCategoricalValueSpec $parentCategoricalValues
   */
  public function setParentCategoricalValues(GoogleCloudMlV1StudyConfigParameterSpecMatchingParentCategoricalValueSpec $parentCategoricalValues)
  {
    $this->parentCategoricalValues = $parentCategoricalValues;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecMatchingParentCategoricalValueSpec
   */
  public function getParentCategoricalValues()
  {
    return $this->parentCategoricalValues;
  }
  /**
   * @param GoogleCloudMlV1StudyConfigParameterSpecMatchingParentDiscreteValueSpec $parentDiscreteValues
   */
  public function setParentDiscreteValues(GoogleCloudMlV1StudyConfigParameterSpecMatchingParentDiscreteValueSpec $parentDiscreteValues)
  {
    $this->parentDiscreteValues = $parentDiscreteValues;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecMatchingParentDiscreteValueSpec
   */
  public function getParentDiscreteValues()
  {
    return $this->parentDiscreteValues;
  }
  /**
   * @param GoogleCloudMlV1StudyConfigParameterSpecMatchingParentIntValueSpec $parentIntValues
   */
  public function setParentIntValues(GoogleCloudMlV1StudyConfigParameterSpecMatchingParentIntValueSpec $parentIntValues)
  {
    $this->parentIntValues = $parentIntValues;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpecMatchingParentIntValueSpec
   */
  public function getParentIntValues()
  {
    return $this->parentIntValues;
  }
  /**
   * How the parameter should be scaled. Leave unset for categorical parameters.
   *
   * Accepted values: SCALE_TYPE_UNSPECIFIED, UNIT_LINEAR_SCALE, UNIT_LOG_SCALE,
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
class_alias(GoogleCloudMlV1StudyConfigParameterSpec::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1StudyConfigParameterSpec');
