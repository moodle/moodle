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

class GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig extends \Google\Model
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
  protected $generationConfigType = GoogleCloudAiplatformV1GenerationConfig::class;
  protected $generationConfigDataType = '';
  /**
   * Optional. Number of samples for each instance in the dataset. If not
   * specified, the default is 4. Minimum value is 1, maximum value is 32.
   *
   * @var int
   */
  public $sampleCount;

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
   * @param int $sampleCount
   */
  public function setSampleCount($sampleCount)
  {
    $this->sampleCount = $sampleCount;
  }
  /**
   * @return int
   */
  public function getSampleCount()
  {
    return $this->sampleCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRunEvaluationConfigAutoraterConfig');
