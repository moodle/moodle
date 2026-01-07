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

class GoogleCloudAiplatformV1Metric extends \Google\Collection
{
  protected $collection_key = 'aggregationMetrics';
  /**
   * Optional. The aggregation metrics to use.
   *
   * @var string[]
   */
  public $aggregationMetrics;
  protected $bleuSpecType = GoogleCloudAiplatformV1BleuSpec::class;
  protected $bleuSpecDataType = '';
  protected $customCodeExecutionSpecType = GoogleCloudAiplatformV1CustomCodeExecutionSpec::class;
  protected $customCodeExecutionSpecDataType = '';
  protected $exactMatchSpecType = GoogleCloudAiplatformV1ExactMatchSpec::class;
  protected $exactMatchSpecDataType = '';
  protected $llmBasedMetricSpecType = GoogleCloudAiplatformV1LLMBasedMetricSpec::class;
  protected $llmBasedMetricSpecDataType = '';
  protected $pairwiseMetricSpecType = GoogleCloudAiplatformV1PairwiseMetricSpec::class;
  protected $pairwiseMetricSpecDataType = '';
  protected $pointwiseMetricSpecType = GoogleCloudAiplatformV1PointwiseMetricSpec::class;
  protected $pointwiseMetricSpecDataType = '';
  protected $predefinedMetricSpecType = GoogleCloudAiplatformV1PredefinedMetricSpec::class;
  protected $predefinedMetricSpecDataType = '';
  protected $rougeSpecType = GoogleCloudAiplatformV1RougeSpec::class;
  protected $rougeSpecDataType = '';

  /**
   * Optional. The aggregation metrics to use.
   *
   * @param string[] $aggregationMetrics
   */
  public function setAggregationMetrics($aggregationMetrics)
  {
    $this->aggregationMetrics = $aggregationMetrics;
  }
  /**
   * @return string[]
   */
  public function getAggregationMetrics()
  {
    return $this->aggregationMetrics;
  }
  /**
   * Spec for bleu metric.
   *
   * @param GoogleCloudAiplatformV1BleuSpec $bleuSpec
   */
  public function setBleuSpec(GoogleCloudAiplatformV1BleuSpec $bleuSpec)
  {
    $this->bleuSpec = $bleuSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1BleuSpec
   */
  public function getBleuSpec()
  {
    return $this->bleuSpec;
  }
  /**
   * Spec for Custom Code Execution metric.
   *
   * @param GoogleCloudAiplatformV1CustomCodeExecutionSpec $customCodeExecutionSpec
   */
  public function setCustomCodeExecutionSpec(GoogleCloudAiplatformV1CustomCodeExecutionSpec $customCodeExecutionSpec)
  {
    $this->customCodeExecutionSpec = $customCodeExecutionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1CustomCodeExecutionSpec
   */
  public function getCustomCodeExecutionSpec()
  {
    return $this->customCodeExecutionSpec;
  }
  /**
   * Spec for exact match metric.
   *
   * @param GoogleCloudAiplatformV1ExactMatchSpec $exactMatchSpec
   */
  public function setExactMatchSpec(GoogleCloudAiplatformV1ExactMatchSpec $exactMatchSpec)
  {
    $this->exactMatchSpec = $exactMatchSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ExactMatchSpec
   */
  public function getExactMatchSpec()
  {
    return $this->exactMatchSpec;
  }
  /**
   * Spec for an LLM based metric.
   *
   * @param GoogleCloudAiplatformV1LLMBasedMetricSpec $llmBasedMetricSpec
   */
  public function setLlmBasedMetricSpec(GoogleCloudAiplatformV1LLMBasedMetricSpec $llmBasedMetricSpec)
  {
    $this->llmBasedMetricSpec = $llmBasedMetricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1LLMBasedMetricSpec
   */
  public function getLlmBasedMetricSpec()
  {
    return $this->llmBasedMetricSpec;
  }
  /**
   * Spec for pairwise metric.
   *
   * @param GoogleCloudAiplatformV1PairwiseMetricSpec $pairwiseMetricSpec
   */
  public function setPairwiseMetricSpec(GoogleCloudAiplatformV1PairwiseMetricSpec $pairwiseMetricSpec)
  {
    $this->pairwiseMetricSpec = $pairwiseMetricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseMetricSpec
   */
  public function getPairwiseMetricSpec()
  {
    return $this->pairwiseMetricSpec;
  }
  /**
   * Spec for pointwise metric.
   *
   * @param GoogleCloudAiplatformV1PointwiseMetricSpec $pointwiseMetricSpec
   */
  public function setPointwiseMetricSpec(GoogleCloudAiplatformV1PointwiseMetricSpec $pointwiseMetricSpec)
  {
    $this->pointwiseMetricSpec = $pointwiseMetricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PointwiseMetricSpec
   */
  public function getPointwiseMetricSpec()
  {
    return $this->pointwiseMetricSpec;
  }
  /**
   * The spec for a pre-defined metric.
   *
   * @param GoogleCloudAiplatformV1PredefinedMetricSpec $predefinedMetricSpec
   */
  public function setPredefinedMetricSpec(GoogleCloudAiplatformV1PredefinedMetricSpec $predefinedMetricSpec)
  {
    $this->predefinedMetricSpec = $predefinedMetricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PredefinedMetricSpec
   */
  public function getPredefinedMetricSpec()
  {
    return $this->predefinedMetricSpec;
  }
  /**
   * Spec for rouge metric.
   *
   * @param GoogleCloudAiplatformV1RougeSpec $rougeSpec
   */
  public function setRougeSpec(GoogleCloudAiplatformV1RougeSpec $rougeSpec)
  {
    $this->rougeSpec = $rougeSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1RougeSpec
   */
  public function getRougeSpec()
  {
    return $this->rougeSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Metric::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Metric');
