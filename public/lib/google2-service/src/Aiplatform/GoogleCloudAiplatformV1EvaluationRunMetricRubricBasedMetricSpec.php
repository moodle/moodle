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

class GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpec extends \Google\Model
{
  protected $inlineRubricsType = GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpecRepeatedRubrics::class;
  protected $inlineRubricsDataType = '';
  protected $judgeAutoraterConfigType = GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig::class;
  protected $judgeAutoraterConfigDataType = '';
  /**
   * Optional. Template for the prompt used by the judge model to evaluate
   * against rubrics.
   *
   * @var string
   */
  public $metricPromptTemplate;
  protected $rubricGenerationSpecType = GoogleCloudAiplatformV1EvaluationRunMetricRubricGenerationSpec::class;
  protected $rubricGenerationSpecDataType = '';
  /**
   * Use a pre-defined group of rubrics associated with the input content. This
   * refers to a key in the `rubric_groups` map of `RubricEnhancedContents`.
   *
   * @var string
   */
  public $rubricGroupKey;

  /**
   * Use rubrics provided directly in the spec.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpecRepeatedRubrics $inlineRubrics
   */
  public function setInlineRubrics(GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpecRepeatedRubrics $inlineRubrics)
  {
    $this->inlineRubrics = $inlineRubrics;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpecRepeatedRubrics
   */
  public function getInlineRubrics()
  {
    return $this->inlineRubrics;
  }
  /**
   * Optional. Optional configuration for the judge LLM (Autorater). The
   * definition of AutoraterConfig needs to be provided.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig $judgeAutoraterConfig
   */
  public function setJudgeAutoraterConfig(GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig $judgeAutoraterConfig)
  {
    $this->judgeAutoraterConfig = $judgeAutoraterConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig
   */
  public function getJudgeAutoraterConfig()
  {
    return $this->judgeAutoraterConfig;
  }
  /**
   * Optional. Template for the prompt used by the judge model to evaluate
   * against rubrics.
   *
   * @param string $metricPromptTemplate
   */
  public function setMetricPromptTemplate($metricPromptTemplate)
  {
    $this->metricPromptTemplate = $metricPromptTemplate;
  }
  /**
   * @return string
   */
  public function getMetricPromptTemplate()
  {
    return $this->metricPromptTemplate;
  }
  /**
   * Dynamically generate rubrics for evaluation using this specification.
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
   * Use a pre-defined group of rubrics associated with the input content. This
   * refers to a key in the `rubric_groups` map of `RubricEnhancedContents`.
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
class_alias(GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRunMetricRubricBasedMetricSpec');
