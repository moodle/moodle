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

class GoogleCloudAiplatformV1AutoraterConfig extends \Google\Model
{
  /**
   * Optional. The fully qualified name of the publisher model or tuned
   * autorater endpoint to use. Publisher model format:
   * `projects/{project}/locations/{location}/publishers/models` Tuned model
   * endpoint format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @var string
   */
  public $autoraterModel;
  /**
   * Optional. Default is true. Whether to flip the candidate and baseline
   * responses. This is only applicable to the pairwise metric. If enabled, also
   * provide PairwiseMetricSpec.candidate_response_field_name and
   * PairwiseMetricSpec.baseline_response_field_name. When rendering
   * PairwiseMetricSpec.metric_prompt_template, the candidate and baseline
   * fields will be flipped for half of the samples to reduce bias.
   *
   * @var bool
   */
  public $flipEnabled;
  protected $generationConfigType = GoogleCloudAiplatformV1GenerationConfig::class;
  protected $generationConfigDataType = '';
  /**
   * Optional. Number of samples for each instance in the dataset. If not
   * specified, the default is 4. Minimum value is 1, maximum value is 32.
   *
   * @var int
   */
  public $samplingCount;

  /**
   * Optional. The fully qualified name of the publisher model or tuned
   * autorater endpoint to use. Publisher model format:
   * `projects/{project}/locations/{location}/publishers/models` Tuned model
   * endpoint format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @param string $autoraterModel
   */
  public function setAutoraterModel($autoraterModel)
  {
    $this->autoraterModel = $autoraterModel;
  }
  /**
   * @return string
   */
  public function getAutoraterModel()
  {
    return $this->autoraterModel;
  }
  /**
   * Optional. Default is true. Whether to flip the candidate and baseline
   * responses. This is only applicable to the pairwise metric. If enabled, also
   * provide PairwiseMetricSpec.candidate_response_field_name and
   * PairwiseMetricSpec.baseline_response_field_name. When rendering
   * PairwiseMetricSpec.metric_prompt_template, the candidate and baseline
   * fields will be flipped for half of the samples to reduce bias.
   *
   * @param bool $flipEnabled
   */
  public function setFlipEnabled($flipEnabled)
  {
    $this->flipEnabled = $flipEnabled;
  }
  /**
   * @return bool
   */
  public function getFlipEnabled()
  {
    return $this->flipEnabled;
  }
  /**
   * Optional. Configuration options for model generation and outputs.
   *
   * @param GoogleCloudAiplatformV1GenerationConfig $generationConfig
   */
  public function setGenerationConfig(GoogleCloudAiplatformV1GenerationConfig $generationConfig)
  {
    $this->generationConfig = $generationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfig
   */
  public function getGenerationConfig()
  {
    return $this->generationConfig;
  }
  /**
   * Optional. Number of samples for each instance in the dataset. If not
   * specified, the default is 4. Minimum value is 1, maximum value is 32.
   *
   * @param int $samplingCount
   */
  public function setSamplingCount($samplingCount)
  {
    $this->samplingCount = $samplingCount;
  }
  /**
   * @return int
   */
  public function getSamplingCount()
  {
    return $this->samplingCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AutoraterConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AutoraterConfig');
