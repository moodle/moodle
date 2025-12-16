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

class GoogleCloudAiplatformV1MigrateResourceRequest extends \Google\Model
{
  protected $migrateAutomlDatasetConfigType = GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlDatasetConfig::class;
  protected $migrateAutomlDatasetConfigDataType = '';
  protected $migrateAutomlModelConfigType = GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlModelConfig::class;
  protected $migrateAutomlModelConfigDataType = '';
  protected $migrateDataLabelingDatasetConfigType = GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig::class;
  protected $migrateDataLabelingDatasetConfigDataType = '';
  protected $migrateMlEngineModelVersionConfigType = GoogleCloudAiplatformV1MigrateResourceRequestMigrateMlEngineModelVersionConfig::class;
  protected $migrateMlEngineModelVersionConfigDataType = '';

  /**
   * Config for migrating Dataset in automl.googleapis.com to Vertex AI's
   * Dataset.
   *
   * @param GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlDatasetConfig $migrateAutomlDatasetConfig
   */
  public function setMigrateAutomlDatasetConfig(GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlDatasetConfig $migrateAutomlDatasetConfig)
  {
    $this->migrateAutomlDatasetConfig = $migrateAutomlDatasetConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlDatasetConfig
   */
  public function getMigrateAutomlDatasetConfig()
  {
    return $this->migrateAutomlDatasetConfig;
  }
  /**
   * Config for migrating Model in automl.googleapis.com to Vertex AI's Model.
   *
   * @param GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlModelConfig $migrateAutomlModelConfig
   */
  public function setMigrateAutomlModelConfig(GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlModelConfig $migrateAutomlModelConfig)
  {
    $this->migrateAutomlModelConfig = $migrateAutomlModelConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1MigrateResourceRequestMigrateAutomlModelConfig
   */
  public function getMigrateAutomlModelConfig()
  {
    return $this->migrateAutomlModelConfig;
  }
  /**
   * Config for migrating Dataset in datalabeling.googleapis.com to Vertex AI's
   * Dataset.
   *
   * @param GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig $migrateDataLabelingDatasetConfig
   */
  public function setMigrateDataLabelingDatasetConfig(GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig $migrateDataLabelingDatasetConfig)
  {
    $this->migrateDataLabelingDatasetConfig = $migrateDataLabelingDatasetConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig
   */
  public function getMigrateDataLabelingDatasetConfig()
  {
    return $this->migrateDataLabelingDatasetConfig;
  }
  /**
   * Config for migrating Version in ml.googleapis.com to Vertex AI's Model.
   *
   * @param GoogleCloudAiplatformV1MigrateResourceRequestMigrateMlEngineModelVersionConfig $migrateMlEngineModelVersionConfig
   */
  public function setMigrateMlEngineModelVersionConfig(GoogleCloudAiplatformV1MigrateResourceRequestMigrateMlEngineModelVersionConfig $migrateMlEngineModelVersionConfig)
  {
    $this->migrateMlEngineModelVersionConfig = $migrateMlEngineModelVersionConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1MigrateResourceRequestMigrateMlEngineModelVersionConfig
   */
  public function getMigrateMlEngineModelVersionConfig()
  {
    return $this->migrateMlEngineModelVersionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MigrateResourceRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigrateResourceRequest');
