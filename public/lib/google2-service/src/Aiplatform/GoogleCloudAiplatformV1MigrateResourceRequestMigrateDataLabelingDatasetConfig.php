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

class GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig extends \Google\Collection
{
  protected $collection_key = 'migrateDataLabelingAnnotatedDatasetConfigs';
  /**
   * Required. Full resource name of data labeling Dataset. Format:
   * `projects/{project}/datasets/{dataset}`.
   *
   * @var string
   */
  public $dataset;
  /**
   * Optional. Display name of the Dataset in Vertex AI. System will pick a
   * display name if unspecified.
   *
   * @var string
   */
  public $datasetDisplayName;
  protected $migrateDataLabelingAnnotatedDatasetConfigsType = GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfigMigrateDataLabelingAnnotatedDatasetConfig::class;
  protected $migrateDataLabelingAnnotatedDatasetConfigsDataType = 'array';

  /**
   * Required. Full resource name of data labeling Dataset. Format:
   * `projects/{project}/datasets/{dataset}`.
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
   * Optional. Display name of the Dataset in Vertex AI. System will pick a
   * display name if unspecified.
   *
   * @param string $datasetDisplayName
   */
  public function setDatasetDisplayName($datasetDisplayName)
  {
    $this->datasetDisplayName = $datasetDisplayName;
  }
  /**
   * @return string
   */
  public function getDatasetDisplayName()
  {
    return $this->datasetDisplayName;
  }
  /**
   * Optional. Configs for migrating AnnotatedDataset in
   * datalabeling.googleapis.com to Vertex AI's SavedQuery. The specified
   * AnnotatedDatasets have to belong to the datalabeling Dataset.
   *
   * @param GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfigMigrateDataLabelingAnnotatedDatasetConfig[] $migrateDataLabelingAnnotatedDatasetConfigs
   */
  public function setMigrateDataLabelingAnnotatedDatasetConfigs($migrateDataLabelingAnnotatedDatasetConfigs)
  {
    $this->migrateDataLabelingAnnotatedDatasetConfigs = $migrateDataLabelingAnnotatedDatasetConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfigMigrateDataLabelingAnnotatedDatasetConfig[]
   */
  public function getMigrateDataLabelingAnnotatedDatasetConfigs()
  {
    return $this->migrateDataLabelingAnnotatedDatasetConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MigrateResourceRequestMigrateDataLabelingDatasetConfig');
