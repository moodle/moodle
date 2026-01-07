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

class GoogleCloudAiplatformV1MigratableResourceAutomlModel extends \Google\Model
{
  /**
   * Full resource name of automl Model. Format:
   * `projects/{project}/locations/{location}/models/{model}`.
   *
   * @var string
   */
  public $model;
  /**
   * The Model's display name in automl.googleapis.com.
   *
   * @var string
   */
  public $modelDisplayName;

  /**
   * Full resource name of automl Model. Format:
   * `projects/{project}/locations/{location}/models/{model}`.
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
   * The Model's display name in automl.googleapis.com.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MigratableResourceAutomlModel::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigratableResourceAutomlModel');
