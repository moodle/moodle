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

class GoogleCloudDiscoveryengineV1betaTrainCustomModelRequest extends \Google\Model
{
  protected $errorConfigType = GoogleCloudDiscoveryengineV1betaImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $gcsTrainingInputType = GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput::class;
  protected $gcsTrainingInputDataType = '';
  /**
   * @var string
   */
  public $modelType;

  /**
   * @param GoogleCloudDiscoveryengineV1betaImportErrorConfig
   */
  public function setErrorConfig(GoogleCloudDiscoveryengineV1betaImportErrorConfig $errorConfig)
  {
    $this->errorConfig = $errorConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaImportErrorConfig
   */
  public function getErrorConfig()
  {
    return $this->errorConfig;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput
   */
  public function setGcsTrainingInput(GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput $gcsTrainingInput)
  {
    $this->gcsTrainingInput = $gcsTrainingInput;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaTrainCustomModelRequestGcsTrainingInput
   */
  public function getGcsTrainingInput()
  {
    return $this->gcsTrainingInput;
  }
  /**
   * @param string
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
class_alias(GoogleCloudDiscoveryengineV1betaTrainCustomModelRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaTrainCustomModelRequest');
