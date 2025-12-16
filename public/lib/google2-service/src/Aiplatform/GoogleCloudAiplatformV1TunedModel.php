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

class GoogleCloudAiplatformV1TunedModel extends \Google\Collection
{
  protected $collection_key = 'checkpoints';
  protected $checkpointsType = GoogleCloudAiplatformV1TunedModelCheckpoint::class;
  protected $checkpointsDataType = 'array';
  /**
   * Output only. A resource name of an Endpoint. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`.
   *
   * @var string
   */
  public $endpoint;
  /**
   * Output only. The resource name of the TunedModel. Format:
   * `projects/{project}/locations/{location}/models/{model}@{version_id}` When
   * tuning from a base model, the version ID will be 1. For continuous tuning,
   * if the provided tuned_model_display_name is set and different from parent
   * model's display name, the tuned model will have a new parent model with
   * version 1. Otherwise the version id will be incremented by 1 from the last
   * version ID in the parent model. E.g.,
   * `projects/{project}/locations/{location}/models/{model}@{last_version_id +
   * 1}`
   *
   * @var string
   */
  public $model;

  /**
   * Output only. The checkpoints associated with this TunedModel. This field is
   * only populated for tuning jobs that enable intermediate checkpoints.
   *
   * @param GoogleCloudAiplatformV1TunedModelCheckpoint[] $checkpoints
   */
  public function setCheckpoints($checkpoints)
  {
    $this->checkpoints = $checkpoints;
  }
  /**
   * @return GoogleCloudAiplatformV1TunedModelCheckpoint[]
   */
  public function getCheckpoints()
  {
    return $this->checkpoints;
  }
  /**
   * Output only. A resource name of an Endpoint. Format:
   * `projects/{project}/locations/{location}/endpoints/{endpoint}`.
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
   * Output only. The resource name of the TunedModel. Format:
   * `projects/{project}/locations/{location}/models/{model}@{version_id}` When
   * tuning from a base model, the version ID will be 1. For continuous tuning,
   * if the provided tuned_model_display_name is set and different from parent
   * model's display name, the tuned model will have a new parent model with
   * version 1. Otherwise the version id will be incremented by 1 from the last
   * version ID in the parent model. E.g.,
   * `projects/{project}/locations/{location}/models/{model}@{last_version_id +
   * 1}`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TunedModel::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TunedModel');
