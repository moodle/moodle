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

class GoogleCloudDiscoveryengineV1betaTrainCustomModelResponse extends \Google\Collection
{
  protected $collection_key = 'errorSamples';
  protected $errorConfigType = GoogleCloudDiscoveryengineV1betaImportErrorConfig::class;
  protected $errorConfigDataType = '';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  /**
   * The metrics of the trained model.
   *
   * @var []
   */
  public $metrics;
  /**
   * Fully qualified name of the CustomTuningModel.
   *
   * @var string
   */
  public $modelName;
  /**
   * The trained model status. Possible values are: * **bad-data**: The training
   * data quality is bad. * **no-improvement**: Tuning didn't improve
   * performance. Won't deploy. * **in-progress**: Model training job creation
   * is in progress. * **training**: Model is actively training. *
   * **evaluating**: The model is evaluating trained metrics. * **indexing**:
   * The model trained metrics are indexing. * **ready**: The model is ready for
   * serving.
   *
   * @var string
   */
  public $modelStatus;

  /**
   * Echoes the destination for the complete errors in the request if set.
   *
   * @param GoogleCloudDiscoveryengineV1betaImportErrorConfig $errorConfig
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
   * A sample of errors encountered while processing the data.
   *
   * @param GoogleRpcStatus[] $errorSamples
   */
  public function setErrorSamples($errorSamples)
  {
    $this->errorSamples = $errorSamples;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrorSamples()
  {
    return $this->errorSamples;
  }
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Fully qualified name of the CustomTuningModel.
   *
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }
  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }
  /**
   * The trained model status. Possible values are: * **bad-data**: The training
   * data quality is bad. * **no-improvement**: Tuning didn't improve
   * performance. Won't deploy. * **in-progress**: Model training job creation
   * is in progress. * **training**: Model is actively training. *
   * **evaluating**: The model is evaluating trained metrics. * **indexing**:
   * The model trained metrics are indexing. * **ready**: The model is ready for
   * serving.
   *
   * @param string $modelStatus
   */
  public function setModelStatus($modelStatus)
  {
    $this->modelStatus = $modelStatus;
  }
  /**
   * @return string
   */
  public function getModelStatus()
  {
    return $this->modelStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaTrainCustomModelResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaTrainCustomModelResponse');
