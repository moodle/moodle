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

class GoogleCloudAiplatformV1RagEmbeddingModelConfig extends \Google\Model
{
  protected $vertexPredictionEndpointType = GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint::class;
  protected $vertexPredictionEndpointDataType = '';

  /**
   * The Vertex AI Prediction Endpoint that either refers to a publisher model
   * or an endpoint that is hosting a 1P fine-tuned text embedding model.
   * Endpoints hosting non-1P fine-tuned text embedding models are currently not
   * supported. This is used for dense vector search.
   *
   * @param GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint $vertexPredictionEndpoint
   */
  public function setVertexPredictionEndpoint(GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint $vertexPredictionEndpoint)
  {
    $this->vertexPredictionEndpoint = $vertexPredictionEndpoint;
  }
  /**
   * @return GoogleCloudAiplatformV1RagEmbeddingModelConfigVertexPredictionEndpoint
   */
  public function getVertexPredictionEndpoint()
  {
    return $this->vertexPredictionEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagEmbeddingModelConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagEmbeddingModelConfig');
