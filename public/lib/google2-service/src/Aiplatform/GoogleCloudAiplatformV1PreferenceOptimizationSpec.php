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

class GoogleCloudAiplatformV1PreferenceOptimizationSpec extends \Google\Model
{
  /**
   * Optional. If set to true, disable intermediate checkpoints for Preference
   * Optimization and only the last checkpoint will be exported. Otherwise,
   * enable intermediate checkpoints for Preference Optimization. Default is
   * false.
   *
   * @var bool
   */
  public $exportLastCheckpointOnly;
  protected $hyperParametersType = GoogleCloudAiplatformV1PreferenceOptimizationHyperParameters::class;
  protected $hyperParametersDataType = '';
  /**
   * Required. Cloud Storage path to file containing training dataset for
   * preference optimization tuning. The dataset must be formatted as a JSONL
   * file.
   *
   * @var string
   */
  public $trainingDatasetUri;
  /**
   * Optional. Cloud Storage path to file containing validation dataset for
   * preference optimization tuning. The dataset must be formatted as a JSONL
   * file.
   *
   * @var string
   */
  public $validationDatasetUri;

  /**
   * Optional. If set to true, disable intermediate checkpoints for Preference
   * Optimization and only the last checkpoint will be exported. Otherwise,
   * enable intermediate checkpoints for Preference Optimization. Default is
   * false.
   *
   * @param bool $exportLastCheckpointOnly
   */
  public function setExportLastCheckpointOnly($exportLastCheckpointOnly)
  {
    $this->exportLastCheckpointOnly = $exportLastCheckpointOnly;
  }
  /**
   * @return bool
   */
  public function getExportLastCheckpointOnly()
  {
    return $this->exportLastCheckpointOnly;
  }
  /**
   * Optional. Hyperparameters for Preference Optimization.
   *
   * @param GoogleCloudAiplatformV1PreferenceOptimizationHyperParameters $hyperParameters
   */
  public function setHyperParameters(GoogleCloudAiplatformV1PreferenceOptimizationHyperParameters $hyperParameters)
  {
    $this->hyperParameters = $hyperParameters;
  }
  /**
   * @return GoogleCloudAiplatformV1PreferenceOptimizationHyperParameters
   */
  public function getHyperParameters()
  {
    return $this->hyperParameters;
  }
  /**
   * Required. Cloud Storage path to file containing training dataset for
   * preference optimization tuning. The dataset must be formatted as a JSONL
   * file.
   *
   * @param string $trainingDatasetUri
   */
  public function setTrainingDatasetUri($trainingDatasetUri)
  {
    $this->trainingDatasetUri = $trainingDatasetUri;
  }
  /**
   * @return string
   */
  public function getTrainingDatasetUri()
  {
    return $this->trainingDatasetUri;
  }
  /**
   * Optional. Cloud Storage path to file containing validation dataset for
   * preference optimization tuning. The dataset must be formatted as a JSONL
   * file.
   *
   * @param string $validationDatasetUri
   */
  public function setValidationDatasetUri($validationDatasetUri)
  {
    $this->validationDatasetUri = $validationDatasetUri;
  }
  /**
   * @return string
   */
  public function getValidationDatasetUri()
  {
    return $this->validationDatasetUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PreferenceOptimizationSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PreferenceOptimizationSpec');
