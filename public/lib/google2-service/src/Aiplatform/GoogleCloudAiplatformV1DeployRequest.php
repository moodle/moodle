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

class GoogleCloudAiplatformV1DeployRequest extends \Google\Model
{
  protected $deployConfigType = GoogleCloudAiplatformV1DeployRequestDeployConfig::class;
  protected $deployConfigDataType = '';
  protected $endpointConfigType = GoogleCloudAiplatformV1DeployRequestEndpointConfig::class;
  protected $endpointConfigDataType = '';
  /**
   * The Hugging Face model to deploy. Format: Hugging Face model ID like
   * `google/gemma-2-2b-it`.
   *
   * @var string
   */
  public $huggingFaceModelId;
  protected $modelConfigType = GoogleCloudAiplatformV1DeployRequestModelConfig::class;
  protected $modelConfigDataType = '';
  /**
   * The Model Garden model to deploy. Format:
   * `publishers/{publisher}/models/{publisher_model}@{version_id}`, or
   * `publishers/hf-{hugging-face-author}/models/{hugging-face-model-name}@001`.
   *
   * @var string
   */
  public $publisherModelName;

  /**
   * Optional. The deploy config to use for the deployment. If not specified,
   * the default deploy config will be used.
   *
   * @param GoogleCloudAiplatformV1DeployRequestDeployConfig $deployConfig
   */
  public function setDeployConfig(GoogleCloudAiplatformV1DeployRequestDeployConfig $deployConfig)
  {
    $this->deployConfig = $deployConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployRequestDeployConfig
   */
  public function getDeployConfig()
  {
    return $this->deployConfig;
  }
  /**
   * Optional. The endpoint config to use for the deployment. If not specified,
   * the default endpoint config will be used.
   *
   * @param GoogleCloudAiplatformV1DeployRequestEndpointConfig $endpointConfig
   */
  public function setEndpointConfig(GoogleCloudAiplatformV1DeployRequestEndpointConfig $endpointConfig)
  {
    $this->endpointConfig = $endpointConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployRequestEndpointConfig
   */
  public function getEndpointConfig()
  {
    return $this->endpointConfig;
  }
  /**
   * The Hugging Face model to deploy. Format: Hugging Face model ID like
   * `google/gemma-2-2b-it`.
   *
   * @param string $huggingFaceModelId
   */
  public function setHuggingFaceModelId($huggingFaceModelId)
  {
    $this->huggingFaceModelId = $huggingFaceModelId;
  }
  /**
   * @return string
   */
  public function getHuggingFaceModelId()
  {
    return $this->huggingFaceModelId;
  }
  /**
   * Optional. The model config to use for the deployment. If not specified, the
   * default model config will be used.
   *
   * @param GoogleCloudAiplatformV1DeployRequestModelConfig $modelConfig
   */
  public function setModelConfig(GoogleCloudAiplatformV1DeployRequestModelConfig $modelConfig)
  {
    $this->modelConfig = $modelConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployRequestModelConfig
   */
  public function getModelConfig()
  {
    return $this->modelConfig;
  }
  /**
   * The Model Garden model to deploy. Format:
   * `publishers/{publisher}/models/{publisher_model}@{version_id}`, or
   * `publishers/hf-{hugging-face-author}/models/{hugging-face-model-name}@001`.
   *
   * @param string $publisherModelName
   */
  public function setPublisherModelName($publisherModelName)
  {
    $this->publisherModelName = $publisherModelName;
  }
  /**
   * @return string
   */
  public function getPublisherModelName()
  {
    return $this->publisherModelName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployRequest');
