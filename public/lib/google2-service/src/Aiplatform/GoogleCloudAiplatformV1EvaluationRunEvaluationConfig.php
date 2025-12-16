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

class GoogleCloudAiplatformV1EvaluationRunEvaluationConfig extends \Google\Collection
{
  protected $collection_key = 'rubricConfigs';
  protected $autoraterConfigType = GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig::class;
  protected $autoraterConfigDataType = '';
  protected $metricsType = GoogleCloudAiplatformV1EvaluationRunMetric::class;
  protected $metricsDataType = 'array';
  protected $outputConfigType = GoogleCloudAiplatformV1EvaluationRunEvaluationConfigOutputConfig::class;
  protected $outputConfigDataType = '';
  protected $promptTemplateType = GoogleCloudAiplatformV1EvaluationRunEvaluationConfigPromptTemplate::class;
  protected $promptTemplateDataType = '';
  protected $rubricConfigsType = GoogleCloudAiplatformV1EvaluationRubricConfig::class;
  protected $rubricConfigsDataType = 'array';

  /**
   * Optional. The autorater config for the evaluation run.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig $autoraterConfig
   */
  public function setAutoraterConfig(GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig $autoraterConfig)
  {
    $this->autoraterConfig = $autoraterConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig
   */
  public function getAutoraterConfig()
  {
    return $this->autoraterConfig;
  }
  /**
   * Required. The metrics to be calculated in the evaluation run.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Optional. The output config for the evaluation run.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunEvaluationConfigOutputConfig $outputConfig
   */
  public function setOutputConfig(GoogleCloudAiplatformV1EvaluationRunEvaluationConfigOutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunEvaluationConfigOutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * The prompt template used for inference. The values for variables in the
   * prompt template are defined in
   * EvaluationItem.EvaluationPrompt.PromptTemplateData.values.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunEvaluationConfigPromptTemplate $promptTemplate
   */
  public function setPromptTemplate(GoogleCloudAiplatformV1EvaluationRunEvaluationConfigPromptTemplate $promptTemplate)
  {
    $this->promptTemplate = $promptTemplate;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunEvaluationConfigPromptTemplate
   */
  public function getPromptTemplate()
  {
    return $this->promptTemplate;
  }
  /**
   * Optional. The rubric configs for the evaluation run. They are used to
   * generate rubrics which can be used by rubric-based metrics. Multiple rubric
   * configs can be specified for rubric generation but only one rubric config
   * can be used for a rubric-based metric. If more than one rubric config is
   * provided, the evaluation metric must specify a rubric group key. Note that
   * if a generation spec is specified on both a rubric config and an evaluation
   * metric, the rubrics generated for the metric will be used for evaluation.
   *
   * @param GoogleCloudAiplatformV1EvaluationRubricConfig[] $rubricConfigs
   */
  public function setRubricConfigs($rubricConfigs)
  {
    $this->rubricConfigs = $rubricConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRubricConfig[]
   */
  public function getRubricConfigs()
  {
    return $this->rubricConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationRunEvaluationConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRunEvaluationConfig');
