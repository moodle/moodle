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

class GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpec extends \Google\Model
{
  protected $parameterSpecType = GoogleCloudAiplatformV1StudySpecParameterSpec::class;
  protected $parameterSpecDataType = '';
  protected $parentCategoricalValuesType = GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecCategoricalValueCondition::class;
  protected $parentCategoricalValuesDataType = '';
  protected $parentDiscreteValuesType = GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecDiscreteValueCondition::class;
  protected $parentDiscreteValuesDataType = '';
  protected $parentIntValuesType = GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecIntValueCondition::class;
  protected $parentIntValuesDataType = '';

  /**
   * Required. The spec for a conditional parameter.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpec $parameterSpec
   */
  public function setParameterSpec(GoogleCloudAiplatformV1StudySpecParameterSpec $parameterSpec)
  {
    $this->parameterSpec = $parameterSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpec
   */
  public function getParameterSpec()
  {
    return $this->parameterSpec;
  }
  /**
   * The spec for matching values from a parent parameter of `CATEGORICAL` type.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecCategoricalValueCondition $parentCategoricalValues
   */
  public function setParentCategoricalValues(GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecCategoricalValueCondition $parentCategoricalValues)
  {
    $this->parentCategoricalValues = $parentCategoricalValues;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecCategoricalValueCondition
   */
  public function getParentCategoricalValues()
  {
    return $this->parentCategoricalValues;
  }
  /**
   * The spec for matching values from a parent parameter of `DISCRETE` type.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecDiscreteValueCondition $parentDiscreteValues
   */
  public function setParentDiscreteValues(GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecDiscreteValueCondition $parentDiscreteValues)
  {
    $this->parentDiscreteValues = $parentDiscreteValues;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecDiscreteValueCondition
   */
  public function getParentDiscreteValues()
  {
    return $this->parentDiscreteValues;
  }
  /**
   * The spec for matching values from a parent parameter of `INTEGER` type.
   *
   * @param GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecIntValueCondition $parentIntValues
   */
  public function setParentIntValues(GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecIntValueCondition $parentIntValues)
  {
    $this->parentIntValues = $parentIntValues;
  }
  /**
   * @return GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpecIntValueCondition
   */
  public function getParentIntValues()
  {
    return $this->parentIntValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecParameterSpecConditionalParameterSpec');
