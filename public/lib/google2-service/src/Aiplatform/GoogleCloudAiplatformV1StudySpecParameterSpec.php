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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1StudySpecParameterSpec extends \Google\Collection
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
  protected $collection_key = 'conditionalParameterSpecs';
  protected $categoricalValueSpecType = GoogleCloudAiplatformV1StudySpecParameterSpecCategoricalValueSpec::class;
  protected $categoricalValueSpecDataType = '';
  protected $conditionalParameterSpecsType = GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpec::class;
  protected $conditionalParameterSpecsDataType = 'array';
  protected $discreteValueSpecType = GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec::class;
  protected $discreteValueSpecDataType = '';
  protected $doubleValueSpecType = GoogleCloudAiplatformV1StudySpecParameterSpecDoubleValueSpec::class;
  protected $doubleValueSpecDataType = '';
  protected $integerValueSpecType = GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec::class;
  protected $integerValueSpecDataType = '';
  /**
   * Required. The ID of the parameter. Must not contain whitespaces and must be
   * unique amongst all ParameterSpecs.
   *
   * @var string
   */
  public $parameterId;
  /**
   * How the parameter should be scaled. Leave unset for `CATEGORICAL`
   * parameters.
   *
   * @var string
   */
  public $scaleType;

  /**
   * The value spec for a 'CATEGORICAL' parameter.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecCategoricalValueSpec $categoricalValueSpec
   */
  public function setCategoricalValueSpec(GoogleCloudAiplatformV1StudySpecParameterSpecCategoricalValueSpec $categoricalValueSpec)
  {
    $this->categoricalValueSpec = $categoricalValueSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecCategoricalValueSpec
   */
  public function getCategoricalValueSpec()
  {
    return $this->categoricalValueSpec;
  }
  /**
   * A conditional parameter node is active if the parameter's value matches the
   * conditional node's parent_value_condition. If two items in
   * conditional_parameter_specs have the same name, they must have disjoint
   * parent_value_condition.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpec[] $conditionalParameterSpecs
   */
  public function setConditionalParameterSpecs($conditionalParameterSpecs)
  {
    $this->conditionalParameterSpecs = $conditionalParameterSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpec[]
   */
  public function getConditionalParameterSpecs()
  {
    return $this->conditionalParameterSpecs;
  }
  /**
   * The value spec for a 'DISCRETE' parameter.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec $discreteValueSpec
   */
  public function setDiscreteValueSpec(GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec $discreteValueSpec)
  {
    $this->discreteValueSpec = $discreteValueSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec
   */
  public function getDiscreteValueSpec()
  {
    return $this->discreteValueSpec;
  }
  /**
   * The value spec for a 'DOUBLE' parameter.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecDoubleValueSpec $doubleValueSpec
   */
  public function setDoubleValueSpec(GoogleCloudAiplatformV1StudySpecParameterSpecDoubleValueSpec $doubleValueSpec)
  {
    $this->doubleValueSpec = $doubleValueSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecDoubleValueSpec
   */
  public function getDoubleValueSpec()
  {
    return $this->doubleValueSpec;
  }
  /**
   * The value spec for an 'INTEGER' parameter.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec $integerValueSpec
   */
  public function setIntegerValueSpec(GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec $integerValueSpec)
  {
    $this->integerValueSpec = $integerValueSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec
   */
  public function getIntegerValueSpec()
  {
    return $this->integerValueSpec;
  }
  /**
   * Required. The ID of the parameter. Must not contain whitespaces and must be
   * unique amongst all ParameterSpecs.
   *
   * @param string $parameterId
   */
  public function setParameterId($parameterId)
  {
    $this->parameterId = $parameterId;
  }
  /**
   * @return string
   */
  public function getParameterId()
  {
    return $this->parameterId;
  }
  /**
   * How the parameter should be scaled. Leave unset for `CATEGORICAL`
   * parameters.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecParameterSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecParameterSpec');
