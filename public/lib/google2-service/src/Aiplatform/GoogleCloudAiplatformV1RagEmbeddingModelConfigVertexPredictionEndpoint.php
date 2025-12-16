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

class GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint extends \Google\Model
{
  /**
   * Required. The endpoint resource name. Format: `projects/{project}/locations
   * /{location}/publishers/{publisher}/models/{model}` or
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`
   *
   * @var string
   */
  public $endpoint;
  /**
   * Output only. The resource name of the model that is deployed on the
   * endpoint. Present only when the endpoint is not a publisher model. Pattern:
   * `projects/{project}/locations/{location}/models/{model}`
   *
   * @var string
   */
  public $model;
  /**
   * Output only. Version ID of the model that is deployed on the endpoint.
   * Present only when the endpoint is not a publisher model.
   *
   * @var string
   */
  public $modelVersionId;

  /**
   * Required. The endpoint resource name. Format: `projects/{project}/locations
   * /{location}/publishers/{publisher}/models/{model}` or
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
   * Output only. The resource name of the model that is deployed on the
   * endpoint. Present only when the endpoint is not a publisher model. Pattern:
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
   * Output only. Version ID of the model that is deployed on the endpoint.
   * Present only when the endpoint is not a publisher model.
   *
   * @param string $modelVersionId
   */
  public function setModelVersionId($modelVersionId)
  {
    $this->modelVersionId = $modelVersionId;
  }
  /**
   * @return string
   */
  public function getModelVersionId()
  {
    return $this->modelVersionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint');
