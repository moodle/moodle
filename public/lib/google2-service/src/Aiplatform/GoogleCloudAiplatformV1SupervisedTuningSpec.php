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

class GoogleCloudAiplatformV1SupervisedTuningSpec extends \Google\Model
{
  /**
   * Optional. If set to true, disable intermediate checkpoints for SFT and only
   * the last checkpoint will be exported. Otherwise, enable intermediate
   * checkpoints for SFT. Default is false.
   *
   * @var bool
   */
  public $exportLastCheckpointOnly;
  protected $hyperParametersType = GoogleCloudAiplatformV1SupervisedHyperParameters::class;
  protected $hyperParametersDataType = '';
  /**
   * Required. Training dataset used for tuning. The dataset can be specified as
   * either a Cloud Storage path to a JSONL file or as the resource name of a
   * Vertex Multimodal Dataset.
   *
   * @var string
   */
  public $trainingDatasetUri;
  /**
   * Optional. Validation dataset used for tuning. The dataset can be specified
   * as either a Cloud Storage path to a JSONL file or as the resource name of a
   * Vertex Multimodal Dataset.
   *
   * @var string
   */
  public $validationDatasetUri;

  /**
   * Optional. If set to true, disable intermediate checkpoints for SFT and only
   * the last checkpoint will be exported. Otherwise, enable intermediate
   * checkpoints for SFT. Default is false.
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
   * Optional. Hyperparameters for SFT.
   *
   * @param GoogleCloudAiplatformV1SupervisedHyperParameters $hyperParameters
   */
  public function setHyperParameters(GoogleCloudAiplatformV1SupervisedHyperParameters $hyperParameters)
  {
    $this->hyperParameters = $hyperParameters;
  }
  /**
   * @return GoogleCloudAiplatformV1SupervisedHyperParameters
   */
  public function getHyperParameters()
  {
    return $this->hyperParameters;
  }
  /**
   * Required. Training dataset used for tuning. The dataset can be specified as
   * either a Cloud Storage path to a JSONL file or as the resource name of a
   * Vertex Multimodal Dataset.
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
   * Optional. Validation dataset used for tuning. The dataset can be specified
   * as either a Cloud Storage path to a JSONL file or as the resource name of a
   * Vertex Multimodal Dataset.
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
class_alias(GoogleCloudAiplatformV1SupervisedTuningSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SupervisedTuningSpec');
