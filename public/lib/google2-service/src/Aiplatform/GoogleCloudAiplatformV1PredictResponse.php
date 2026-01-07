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

class GoogleCloudAiplatformV1PredictResponse extends \Google\Collection
{
  protected $collection_key = 'predictions';
  /**
   * ID of the Endpoint's DeployedModel that served this prediction.
   *
   * @var string
   */
  public $deployedModelId;
  /**
   * Output only. Request-level metadata returned by the model. The metadata
   * type will be dependent upon the model implementation.
   *
   * @var array
   */
  public $metadata;
  /**
   * Output only. The resource name of the Model which is deployed as the
   * DeployedModel that this prediction hits.
   *
   * @var string
   */
  public $model;
  /**
   * Output only. The display name of the Model which is deployed as the
   * DeployedModel that this prediction hits.
   *
   * @var string
   */
  public $modelDisplayName;
  /**
   * Output only. The version ID of the Model which is deployed as the
   * DeployedModel that this prediction hits.
   *
   * @var string
   */
  public $modelVersionId;
  /**
   * The predictions that are the output of the predictions call. The schema of
   * any single prediction may be specified via Endpoint's DeployedModels'
   * Model's PredictSchemata's prediction_schema_uri.
   *
   * @var array[]
   */
  public $predictions;

  /**
   * ID of the Endpoint's DeployedModel that served this prediction.
   *
   * @param string $deployedModelId
   */
  public function setDeployedModelId($deployedModelId)
  {
    $this->deployedModelId = $deployedModelId;
  }
  /**
   * @return string
   */
  public function getDeployedModelId()
  {
    return $this->deployedModelId;
  }
  /**
   * Output only. Request-level metadata returned by the model. The metadata
   * type will be dependent upon the model implementation.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. The resource name of the Model which is deployed as the
   * DeployedModel that this prediction hits.
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
   * Output only. The display name of the Model which is deployed as the
   * DeployedModel that this prediction hits.
   *
   * @param string $modelDisplayName
   */
  public function setModelDisplayName($modelDisplayName)
  {
    $this->modelDisplayName = $modelDisplayName;
  }
  /**
   * @return string
   */
  public function getModelDisplayName()
  {
    return $this->modelDisplayName;
  }
  /**
   * Output only. The version ID of the Model which is deployed as the
   * DeployedModel that this prediction hits.
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
  /**
   * The predictions that are the output of the predictions call. The schema of
   * any single prediction may be specified via Endpoint's DeployedModels'
   * Model's PredictSchemata's prediction_schema_uri.
   *
   * @param array[] $predictions
   */
  public function setPredictions($predictions)
  {
    $this->predictions = $predictions;
  }
  /**
   * @return array[]
   */
  public function getPredictions()
  {
    return $this->predictions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PredictResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PredictResponse');
