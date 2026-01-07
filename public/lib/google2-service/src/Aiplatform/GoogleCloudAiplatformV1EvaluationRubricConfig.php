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

class GoogleCloudAiplatformV1EvaluationRubricConfig extends \Google\Model
{
  protected $predefinedRubricGenerationSpecType = GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec::class;
  protected $predefinedRubricGenerationSpecDataType = '';
  protected $rubricGenerationSpecType = GoogleCloudAiplatformV1EvaluationRunMetricRubricGenerationSpec::class;
  protected $rubricGenerationSpecDataType = '';
  /**
   * Required. The key used to save the generated rubrics. If a generation spec
   * is provided, this key will be used for the name of the generated rubric
   * group. Otherwise, this key will be used to look up the existing rubric
   * group on the evaluation item. Note that if a rubric group key is specified
   * on both a rubric config and an evaluation metric, the key from the metric
   * will be used to select the rubrics for evaluation.
   *
   * @var string
   */
  public $rubricGroupKey;

  /**
   * Dynamically generate rubrics using a predefined spec.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec $predefinedRubricGenerationSpec
   */
  public function setPredefinedRubricGenerationSpec(GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec $predefinedRubricGenerationSpec)
  {
    $this->predefinedRubricGenerationSpec = $predefinedRubricGenerationSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec
   */
  public function getPredefinedRubricGenerationSpec()
  {
    return $this->predefinedRubricGenerationSpec;
  }
  /**
   * Dynamically generate rubrics using this specification.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunMetricRubricGenerationSpec $rubricGenerationSpec
   */
  public function setRubricGenerationSpec(GoogleCloudAiplatformV1EvaluationRunMetricRubricGenerationSpec $rubricGenerationSpec)
  {
    $this->rubricGenerationSpec = $rubricGenerationSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunMetricRubricGenerationSpec
   */
  public function getRubricGenerationSpec()
  {
    return $this->rubricGenerationSpec;
  }
  /**
   * Required. The key used to save the generated rubrics. If a generation spec
   * is provided, this key will be used for the name of the generated rubric
   * group. Otherwise, this key will be used to look up the existing rubric
   * group on the evaluation item. Note that if a rubric group key is specified
   * on both a rubric config and an evaluation metric, the key from the metric
   * will be used to select the rubrics for evaluation.
   *
   * @param string $rubricGroupKey
   */
  public function setRubricGroupKey($rubricGroupKey)
  {
    $this->rubricGroupKey = $rubricGroupKey;
  }
  /**
   * @return string
   */
  public function getRubricGroupKey()
  {
    return $this->rubricGroupKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationRubricConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRubricConfig');
