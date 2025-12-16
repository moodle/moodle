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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1TrainCustomModelRequest extends \Google\Model
{
  protected $errorConfigType = GoogleCloudDiscoveryengineV1ImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $gcsTrainingInputType = GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput::class;
  protected $gcsTrainingInputDataType = '';
  /**
   * If not provided, a UUID will be generated.
   *
   * @var string
   */
  public $modelId;
  /**
   * Model to be trained. Supported values are: * **search-tuning**: Fine tuning
   * the search system based on data provided.
   *
   * @var string
   */
  public $modelType;

  /**
   * The desired location of errors incurred during the data ingestion and
   * training.
   *
   * @param GoogleCloudDiscoveryengineV1ImportErrorConfig $errorConfig
   */
  public function setErrorConfig(GoogleCloudDiscoveryengineV1ImportErrorConfig $errorConfig)
  {
    $this->errorConfig = $errorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ImportErrorConfig
   */
  public function getErrorConfig()
  {
    return $this->errorConfig;
  }
  /**
   * Cloud Storage training input.
   *
   * @param GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput $gcsTrainingInput
   */
  public function setGcsTrainingInput(GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput $gcsTrainingInput)
  {
    $this->gcsTrainingInput = $gcsTrainingInput;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1TrainCustomModelRequestGcsTrainingInput
   */
  public function getGcsTrainingInput()
  {
    return $this->gcsTrainingInput;
  }
  /**
   * If not provided, a UUID will be generated.
   *
   * @param string $modelId
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return string
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * Model to be trained. Supported values are: * **search-tuning**: Fine tuning
   * the search system based on data provided.
   *
   * @param string $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return string
   */
  public function getModelType()
  {
    return $this->modelType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1TrainCustomModelRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1TrainCustomModelRequest');
