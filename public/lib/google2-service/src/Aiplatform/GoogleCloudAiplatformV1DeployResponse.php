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

class GoogleCloudAiplatformV1DeployResponse extends \Google\Model
{
  /**
   * Output only. The name of the Endpoint created. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @var string
   */
  public $endpoint;
  /**
   * Output only. The name of the Model created. Format:
   * `projects/{project}/locations/{location}/models/{model}`
   *
   * @var string
   */
  public $model;
  /**
   * Output only. The name of the PublisherModel resource. Format:
   * `publishers/{publisher}/models/{publisher_model}@{version_id}`, or
   * `publishers/hf-{hugging-face-author}/models/{hugging-face-model-name}@001`
   *
   * @var string
   */
  public $publisherModel;

  /**
   * Output only. The name of the Endpoint created. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Output only. The name of the Model created. Format:
   * `projects/{project}/locations/{location}/models/{model}`
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Output only. The name of the PublisherModel resource. Format:
   * `publishers/{publisher}/models/{publisher_model}@{version_id}`, or
   * `publishers/hf-{hugging-face-author}/models/{hugging-face-model-name}@001`
   *
   * @param string $publisherModel
   */
  public function setPublisherModel($publisherModel)
  {
    $this->publisherModel = $publisherModel;
  }
  /**
   * @return string
   */
  public function getPublisherModel()
  {
    return $this->publisherModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployResponse');
