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

class GoogleCloudAiplatformV1MigrateResourceResponse extends \Google\Model
{
  /**
   * Migrated Dataset's resource name.
   *
   * @var string
   */
  public $dataset;
  protected $migratableResourceType = GoogleCloudAiplatformV1MigratableResource::class;
  protected $migratableResourceDataType = '';
  /**
   * Migrated Model's resource name.
   *
   * @var string
   */
  public $model;

  /**
   * Migrated Dataset's resource name.
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Before migration, the identifier in ml.googleapis.com,
   * automl.googleapis.com or datalabeling.googleapis.com.
   *
   * @param GoogleCloudAiplatformV1MigratableResource $migratableResource
   */
  public function setMigratableResource(GoogleCloudAiplatformV1MigratableResource $migratableResource)
  {
    $this->migratableResource = $migratableResource;
  }
  /**
   * @return GoogleCloudAiplatformV1MigratableResource
   */
  public function getMigratableResource()
  {
    return $this->migratableResource;
  }
  /**
   * Migrated Model's resource name.
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
class_alias(GoogleCloudAiplatformV1MigrateResourceResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigrateResourceResponse');
