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

class GoogleCloudAiplatformV1PreTunedModel extends \Google\Model
{
  /**
   * Output only. The name of the base model this PreTunedModel was tuned from.
   *
   * @var string
   */
  public $baseModel;
  /**
   * Optional. The source checkpoint id. If not specified, the default
   * checkpoint will be used.
   *
   * @var string
   */
  public $checkpointId;
  /**
   * The resource name of the Model. E.g., a model resource name with a
   * specified version id or alias:
   * `projects/{project}/locations/{location}/models/{model}@{version_id}`
   * `projects/{project}/locations/{location}/models/{model}@{alias}` Or, omit
   * the version id to use the default version:
   * `projects/{project}/locations/{location}/models/{model}`
   *
   * @var string
   */
  public $tunedModelName;

  /**
   * Output only. The name of the base model this PreTunedModel was tuned from.
   *
   * @param string $baseModel
   */
  public function setBaseModel($baseModel)
  {
    $this->baseModel = $baseModel;
  }
  /**
   * @return string
   */
  public function getBaseModel()
  {
    return $this->baseModel;
  }
  /**
   * Optional. The source checkpoint id. If not specified, the default
   * checkpoint will be used.
   *
   * @param string $checkpointId
   */
  public function setCheckpointId($checkpointId)
  {
    $this->checkpointId = $checkpointId;
  }
  /**
   * @return string
   */
  public function getCheckpointId()
  {
    return $this->checkpointId;
  }
  /**
   * The resource name of the Model. E.g., a model resource name with a
   * specified version id or alias:
   * `projects/{project}/locations/{location}/models/{model}@{version_id}`
   * `projects/{project}/locations/{location}/models/{model}@{alias}` Or, omit
   * the version id to use the default version:
   * `projects/{project}/locations/{location}/models/{model}`
   *
   * @param string $tunedModelName
   */
  public function setTunedModelName($tunedModelName)
  {
    $this->tunedModelName = $tunedModelName;
  }
  /**
   * @return string
   */
  public function getTunedModelName()
  {
    return $this->tunedModelName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PreTunedModel::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PreTunedModel');
